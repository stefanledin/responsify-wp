<?php
class Element extends Picturefill
{
	public $markup;

	public function __construct($id, $settings)
	{
		parent::__construct($id, $settings);
		$this->markup = $this->buildMarkup();
	}

	protected function buildMarkup()
	{
		$alt = $this->getImageAttributes();
		$markup = '<span data-picture data-alt="'.$alt.'">';
			$markup .= '<span data-src="'.$this->images[0][0].'"></span>';
			for ($i=1; $i < count($this->images); $i++) { 
				$markup .= '<span data-src="'.$this->images[$i][0].'" data-media="(min-width: '.$this->images[$i-1][1].'px)"></span>';
			}
			$markup .= '<noscript>';
				$markup .= '<img src="'.$this->images[0][0].'" alt="'.$alt.'">';
			$markup .= '</noscript>';
		$markup .= '</span>';
		return $markup;
	}

	protected function getImageAttributes()
	{
		return get_post_meta($this->id, '_wp_attachment_image_alt', true);
	}
}