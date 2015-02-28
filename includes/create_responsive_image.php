<?php
abstract class Create_Responsive_image
{
	protected $image_sizes;
	protected $id;
	protected $images;
	protected $settings;

	public function __construct( $id, $settings )
	{
		$this->id = $id;
        $this->settings = $settings;

        // 1. Get sizes
        $this->image_sizes = $this->get_image_sizes();
        if ( isset($this->settings['retina']) ) {
            if ( !is_bool($this->settings['retina']) ) {
                if ( isset($this->settings['sizes']) ) {
                    $this->add_retina_sizes();
                }
                $this->set_retina_sizes();
            }
            if ( !$this->settings['retina'] ) {
                $this->remove_retina_sizes();
            }
        }

        // 2. Get images 
        $this->images = $this->get_images( $this->image_sizes );

        // 3. Order the images by width
        $this->images = $this->order_images( $this->images );

        if ( isset($this->settings['retina']) && $this->settings['retina'] ) {
            $this->group_highres();
        }
        $this->images = array_values($this->images);

        // 4. Set the media queries
        $user_media_queries = ( isset($settings['media_queries']) ) ? $settings['media_queries'] : null;
        $media_queries = new Media_Queries( $this->images, $user_media_queries );
        $this->images = $media_queries->set();
	}

    /**
     * Finds images in the selected sizes.
     *
     * @param $sizes
     * @return array
     */
    public function get_images( $sizes )
	{
		$images = array();
		$image_srcs = array();

        $notBiggerThan = (isset($this->settings['notBiggerThan'])) ? $this->settings['notBiggerThan'] : null;

        $image_meta_data = wp_get_attachment_metadata( $this->id );
        $image_meta_data['sizes']['full'] = array(
            'width' => $image_meta_data['width'],
            'height' => $image_meta_data['height']
        );

        foreach ( $sizes as $size ) {
            $image = $this->get_image($size);
            #if ( in_array($image[0], $image_srcs) ) continue;
            if ( isset($image_meta_data['sizes'][$size]) ) {
                array_push($images, array(
                    'src' => $image[0],
                    'size' => $size,
                    'width' => $image_meta_data['sizes'][$size]['width'],
                    'height' => $image_meta_data['sizes'][$size]['height']
                ));
                array_push($image_srcs, $image[0]);
                if ( isset($notBiggerThan) && ($image[0] == $notBiggerThan) ) break;
            } 
        }
		return $images;
	}
    
    protected function group_highres() {
        $retina_image_indexes = array();
        for ($i=0; $i < count($this->images); $i++) { 
            if ( strpos($this->images[$i]['size'], '@') ) {
                $retina_image_indexes[] = $i;
                continue;
            }
            $possible_retina_image_name = $this->images[$i]['size'] . '@';
            foreach ($this->images as $image) {
                if ( substr($image['size'], 0, strlen($possible_retina_image_name)) == $possible_retina_image_name ) {
                    $density = substr($image['size'], (strpos($image['size'], '@')+1));
                    $this->images[$i]['highres'][$density] = $image;
                }
            }
        }
        for ($i=0; $i < count($retina_image_indexes); $i++) { 
            unset($this->images[$retina_image_indexes[$i]]);
        }
    }

    /**
     * Finds a single image in the selected size
     *
     * @param $size
     * @return array
     */
    protected function get_image( $size )
	{
		return wp_get_attachment_image_src( $this->id, $size );
	}

    /**
     * Orders the array of images based on width.
     *
     * @param $images
     * @return array
     */
    protected function order_images( $images )
	{
		usort($images, function($img1, $img2) {
			return $img1['width'] < $img2['width'] ? -1 : 1;
		});
        return $images;
	}

    /**
     * Finds and returns all available image sizes.
     *
     * @return array
     */
    protected function get_image_sizes()
	{
        if ( isset($this->settings['sizes'])  ) return $this->settings['sizes'];

        $selected_sizes = get_option( 'selected_sizes' );
        $image_sizes = ( $selected_sizes ) ? array_keys($selected_sizes) : get_intermediate_image_sizes() ;
        if ( !in_array('full', $image_sizes) ) {
            array_push($image_sizes, 'full');
        }
        return $image_sizes;
	}

    protected function set_retina_sizes()
    {
        $densities = ( is_array($this->settings['retina']) ) 
            ? $this->settings['retina']
            : array( $this->settings['retina'] );
        $image_sizes = array();
        foreach ( $densities as $density ) {
            foreach ( $this->image_sizes as $image_size ) {
                if ( (!strpos($image_size, '@')) || (strpos($image_size, $density)) ) {
                    if ( !in_array($image_size, $image_sizes) ) {
                        array_push($image_sizes, $image_size);
                    }
                }
            }                
        }
        $this->image_sizes = $image_sizes;
    }

    protected function add_retina_sizes()
    {
        $densities = ( is_array($this->settings['retina']) )
            ? $this->settings['retina']
            : array( $this->settings['retina'] );

        foreach ($densities as $density) {
            foreach ( $this->image_sizes as $image_size ) {
                if ( !strpos($image_size, '@') ) {
                    array_push($this->image_sizes, $image_size.'@'.$density);
                }
            }
        }
    }

    protected function remove_retina_sizes()
    {
        $count = count($this->image_sizes);
        for ($i=0; $i < $count; $i++) { 
            if ( strpos($this->image_sizes[$i], '@') ) {
                unset($this->image_sizes[$i]);
            }
        }
    }

    /**
     * Gets a meta value.
     *
     * @param $meta
     * @return array
     */
    protected function get_image_meta( $meta )
    {
        return get_post_meta($this->id, '_wp_attachment_image_' . $meta, true);
    }

    /**
     * Makes a string with all attributes.
     *
     * @param $attribute_array
     * @return string
     */
    protected function create_attributes( $attribute_array )
    {
        $attributes = '';
        foreach ($attribute_array as $attribute => $value) {
            $attributes .= $attribute . '="' . $value . '" ';
        }
        // Removes the extra space after the last attribute
        return substr($attributes, 0, -1);
    }


}