<?php
class Picturefill
{
	protected $imageSizes = array();
	protected $id;
	protected $images;

	public function __construct($id, $settings)
	{
		$this->id = $id;
		// 1. Hämta alla förinställda bildstorlekar
		$this->getImageSizes();
		// 2. Hämta bilderna i antingen de valda storlekarna eller alla förinställda.
		$sizes = (isset($settings['sizes'])) ? $settings['sizes'] : $this->imageSizes;
		$this->images = $this->getImages($sizes);
		// 3. Sortera bilderna i storleksordning
		$this->orderImages();
	}
	public function getImages($sizes)
	{
		$images = array();
		foreach ($sizes as $size) {
			array_push($images, $this->getImage($size));
		}
		return $images;
	}

	protected function getImage($size)
	{
		return wp_get_attachment_image_src($this->id, $size);
	}

	protected function orderImages()
	{
		usort($this->images, function($img1, $img2) {
			return $img1[1] < $img2[1] ? -1 : 1;
		});
	}
	
	protected function getImageSizes()
	{
		$this->imageSizes = get_intermediate_image_sizes();
		array_push($this->imageSizes, 'full');
	}
}