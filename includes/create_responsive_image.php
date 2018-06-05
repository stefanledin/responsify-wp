<?php
abstract class Create_Responsive_image
{
    protected $image_sizes = array();
    protected $id;
    protected $images;
    protected $settings;
    protected $log;
    protected $logger;
    
    public function __construct( $id, $settings )
    {
        $this->logger = new Responsify_WP_Logger;

        $this->id = $id;
        $this->logger->add('Attachment ID', $this->id);
        
        $this->settings = $settings;

        // 1. Get sizes
        $this->image_sizes = $this->get_all_avaliable_image_sizes();
        // Retina
        if ( isset($this->settings['retina']) ) {
            $retina = new Retina( $this->settings );
            $this->image_sizes = $retina->set_sizes( $this->image_sizes );
        }
        $this->logger->add('Image sizes', $this->image_sizes);

        // 2. Get images 
        $this->images = $this->get_images( $this->image_sizes );
        // 3. Order the images by width
        $this->images = $this->order_images( $this->images );
        // 4. Adds retina versions to the same array as the 'original' image.
        if ( isset($this->settings['retina']) && $this->settings['retina'] ) {
            $this->group_highres();
        }
        // 5. Remove images that is larger than the one inserted into the editor.
        if ( isset($this->settings['notBiggerThan']) ) {
            $this->images = $this->remove_images_larger_than_inserted( $this->images, $this->settings['notBiggerThan'] );
            $this->logger->add( 'Largest size that should be used', $this->settings['notBiggerThan'] );
        }
        $this->images = $this->remove_images_in_sizes_not_selected( $this->images, $this->get_image_sizes_selected_by_user());
        
        $this->images = array_values($this->images);
        // 5. Set the media queries
        $user_media_queries = ( isset($settings['media_queries']) ) ? $settings['media_queries'] : null;
        $media_queries = new Media_Queries( $this->images, $user_media_queries );
        $this->images = $media_queries->set();
        $this->logger->log_media_queries( $this->images );
        
        $this->log = $this->logger->get();
    }
    
    /**
     * Finds images in all avaliable sizes
     *
     * @param $sizes
     * @return array
     */
    public function get_images( $sizes )
    {
        $images = array();
        $image_srcs = array();
        $image_meta_data = wp_get_attachment_metadata( $this->id );
          
        $image_meta_data['sizes']['full'] = array(
            'width' => $image_meta_data['width'],
            // According to a thread in the support forum, 'height' isn't always avaliable on full size images.
            'height' => ( isset( $image_meta_data['height'] ) ) ? $image_meta_data['height'] : ''
        );
        
        $this->logger->add( 'Image width', ( isset($image_meta_data['width']) ) ? $image_meta_data['width'] : 'No width avaliable' );
        $this->logger->add( 'Image height', ( isset($image_meta_data['height']) ) ? $image_meta_data['height'] : 'No height avaliable' );

        foreach ( $sizes as $size ) {
            $image = $this->get_image( $size );
            if ( isset($image_meta_data['sizes'][$size]) ) {
                array_push($images, array(
                    'src' => $image[0],
                    'size' => $size,
                    'width' => $image_meta_data['sizes'][$size]['width'],
                    'height' => $image_meta_data['sizes'][$size]['height']
                ));
                array_push($image_srcs, $image[0]);
                $this->logger->addArray( 'Image sizes found', $size );
                $this->logger->addArray( 'Images found', "\n- $size: $image[0]" );
            } 
        }
        return $images;
    }
    
    /**
     * Adds retina versions to the same array as
     * the regular image. Thus they are grouped together.
     */
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
     * Removes images that is larger than the one inserted into the editor.
     * For example, if medium is inserted, ignore large and full.
     * 
     * @param  array $images 
     * @return array
     */
    protected function remove_images_larger_than_inserted( $images, $largest_image_url )
    {
        $valid_images = array();
        foreach ( $images as $image ) {
            $valid_images[] = $image;
            if ( $image['src'] == $largest_image_url ) break;
        }
        return $valid_images;
    }

    /**
     * Removes images in sizes that has not been selected by the user.
     * If medium has been deselected, it will be removed. (Unless it's the sizes
     * that has been inserted through the editor, that's why this method exists.)
     *
     * @param array $images
     * @param array $selected_sizes
     * @return array
     */
    protected function remove_images_in_sizes_not_selected( $images, $selected_sizes )
    {
        $self = $this;
        $valid_images = array_map( function( $image ) use ($selected_sizes) {
            if ( isset( $self->settings['notBiggerThan'] ) ) {
                if ( $image['src'] == $self->settings['notBiggerThan'] ) {
                    return $image;
                }
            }
            if ( in_array( $image['size'], $selected_sizes ) ) {
                return $image;
            } else {
                return null;
            }
        }, $images);
        $valid_images = array_filter( $valid_images );
        return $valid_images;
    }

    /**
     * Finds and returns image sizes that has been selected by the user.
     *
     * @return array
     */
    protected function get_image_sizes_selected_by_user()
    {
        if ( isset($this->settings['sizes'])  ) return $this->settings['sizes'];
        $selected_sizes = get_option( 'selected_sizes' );
        $image_sizes = ( $selected_sizes ) ? array_keys($selected_sizes) : get_intermediate_image_sizes() ;
        if ( !in_array('full', $image_sizes) ) {
            array_push($image_sizes, 'full');
        }
        return $image_sizes;
    }

    /**
     * Finds and returns all available image sizes.
     *
     * @return array
     */
    protected function get_all_avaliable_image_sizes()
    {
        $image_sizes = get_intermediate_image_sizes();
        if ( !in_array('full', $image_sizes) ) {
            array_push($image_sizes, 'full');
        }
        return $image_sizes;
    }
    
    /**
     * Gets a meta value.
     *
     * @param $meta
     * @return array
     */
    protected function get_image_meta( $meta )
    {
        return get_post_meta( $this->id, '_wp_attachment_image_' . $meta, true );
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

    /**
     * Add an HTML comment with all the
     * debug information from the Logger.
     * @param  string $markup 
     * @return string
     */
    protected function prepend_debug_information( $markup )
    {
        $debug_information = "<!--\n### RWP Debug ###\n";
        foreach ( $this->log as $key => $value ) {
            if ( is_array($value) ) {
                $value = implode(", ", $value);
            }
            $debug_information .= "$key: $value\n";
        }
        $debug_information .= '-->';
        $markup = $debug_information . $markup;
        return $markup;
    }
}
