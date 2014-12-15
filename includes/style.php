<?php
class Style extends Create_Responsive_image
{
	public $style;

	public function __construct($id, $settings)
	{
		parent::__construct($id, $settings);
		$this->style = $this->build_css($settings['selector']);
	}

	protected function build_css($selector)
	{
		$css = '<style>';
			$css .= $selector . ' {';
				$css .= 'background-image: url("'.$this->images[0]['src'].'");';
			$css .= '}';
			for ($i=1; $i < count($this->images); $i++) { 
				$media_query = $this->media_query($this->images[$i]);
				$css .= $media_query.' {';
					$css .= $selector . '{';
						$css .= 'background-image: url("'.$this->images[$i]['src'].'");';
					$css .= '}';
				$css .= '}';
			}
		$css .= '</style>';
		return $css;
	}

	protected function media_query( $image )
	{
		if ( gettype($image['media_query']) == 'array' ) {
			return '@media screen and ('.$image['media_query']['property'].': '.$image['media_query']['value'].')';	 
		}
		return '@media screen and ('.$image['media_query'].')';	
	}
}