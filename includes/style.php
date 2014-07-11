<?php
class Style extends Picturefill
{
	public $style;

	public function __construct($id, $settings)
	{
		parent::__construct($id, $settings);
		$this->style = $this->buildCss($settings['selector']);
	}

	protected function buildCss($selector)
	{
		$css = '<style>';
			$css .= $selector . ' {';
				$css .= 'background-image: url("'.$this->images[0]['src'].'");';
			$css .= '}';
			for ($i=1; $i < count($this->images); $i++) { 
				$css .= '@media screen and ('.$this->images[$i]['media_query'].') {';
					$css .= $selector . '{';
						$css .= 'background-image: url("'.$this->images[$i]['src'].'");';
					$css .= '}';
				$css .= '}';
			}
		$css .= '</style>';
		return $css;
	}
}