<?php
class Picturefill
{
	protected $imageSizes = array();
	protected $id;
	protected $images;
	protected $settings;

	public function __construct( $id, $settings )
	{
		$this->id = $id;
		$this->settings = $settings;
		// 1. Hämta alla förinställda bildstorlekar
		$this->getImageSizes();
		// 2. Hämta bilderna i antingen de valda storlekarna eller alla förinställda.
		$sizes = (isset($settings['sizes'])) ? $settings['sizes'] : $this->imageSizes;
		$this->images = $this->getImages($sizes, $settings['notBiggerThan']);
		// 3. Sortera bilderna i storleksordning
		$this->orderImages();
		// 4. Räkna ut vilka media queries bilderna ska ha
		$user_media_queries = (isset($settings['media_queries'])) ? $settings['media_queries'] : null;
		$media_queries = new Media_Queries( $this->images, $user_media_queries );
		$this->images = $media_queries->set();
	}
	public function getImages( $sizes, $notBiggerThan = null )
	{
		$images = array();
		$image_srcs = array();

		foreach ( $sizes as $size ) {
			$image = $this->getImage($size);
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

	protected function getImage( $size )
	{
		return wp_get_attachment_image_src( $this->id, $size );
	}

	protected function orderImages()
	{
		usort($this->images, function($img1, $img2) {
			return $img1['width'] < $img2['width'] ? -1 : 1;
		});
	}
	
	protected function getImageSizes()
	{
		$selected_sizes = get_option( 'selected_sizes' );
		$this->imageSizes = ( $selected_sizes ) ? array_keys($selected_sizes) : get_intermediate_image_sizes() ;
		if ( !in_array('full', $this->imageSizes) ) {
			array_push($this->imageSizes, 'full');
		}
	}
}