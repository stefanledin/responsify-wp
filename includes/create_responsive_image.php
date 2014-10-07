<?php
class Create_Responsive_image
{
	protected $imageSizes;
	protected $id;
	protected $images;
	protected $settings;

	public function __construct( $id, $settings )
	{
		$this->id = $id;
		$this->settings = $settings;

		// 1. Hämta bildstorlekar
		$this->imageSizes = $this->get_image_sizes();

		// 2. Hämta bilderna i antingen de valda storlekarna eller alla förinställda.
        $sizes = (isset($settings['sizes'])) ? $settings['sizes'] : $this->imageSizes;
		$this->images = $this->get_images($sizes, $settings['notBiggerThan']);

		// 3. Sortera bilderna i storleksordning
		$this->images = $this->order_images($this->images);

		// 4. Räkna ut vilka media queries bilderna ska ha
		$user_media_queries = (isset($settings['media_queries'])) ? $settings['media_queries'] : null;
		$media_queries = new Media_Queries( $this->images, $user_media_queries );
		$this->images = $media_queries->set();
	}

    /**
     * Finds images in the selected sizes.
     *
     * @param $sizes
     * @param null $notBiggerThan
     * @return array
     */
    public function get_images( $sizes, $notBiggerThan = null )
	{
		$images = array();
		$image_srcs = array();

		foreach ( $sizes as $size ) {
			$image = $this->get_image($size);
			if ( !in_array($image[0], $image_srcs) ) {
				array_push($images, array(
					'src' => $image[0],
					'size' => $size,
					'width' => $image[1],
					'height' => $image[2]
				));
				array_push($image_srcs, $image[0]);
			}
			if (isset($notBiggerThan) && ($image[0] == $notBiggerThan)) break;
		}
		return $images;
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
		$selected_sizes = get_option( 'selected_sizes' );
		$imageSizes = ( $selected_sizes ) ? array_keys($selected_sizes) : get_intermediate_image_sizes() ;
		if ( !in_array('full', $imageSizes) ) {
			array_push($imageSizes, 'full');
		}
        return $imageSizes;
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