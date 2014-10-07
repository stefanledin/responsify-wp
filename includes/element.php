<?php
class Element extends Create_Responsive_image
{
	public $markup;

	public function __construct($id, $settings)
	{
		parent::__construct($id, $settings);
		$this->set_attributes();
		$this->markup = $this->create_markup();
	}

	protected function set_attributes()
	{

        $default_attributes = array(
            'picture' => array(),
            'source' => array(),
            'img' => array()
        );


		if ( isset($this->settings['attributes']) ) {
            $this->settings['attributes'] = array_replace_recursive($default_attributes, $this->settings['attributes']);
        } else {
			$this->settings['attributes'] = $default_attributes;
		}
	}

	protected function create_markup()
	{
		$picture_attributes = $this->create_attributes($this->settings['attributes']['picture']);
		$source_attributes = $this->create_attributes($this->settings['attributes']['source']);
		$img_attributes = $this->create_attributes($this->settings['attributes']['img']);
		
		// The Picture element wants to have the largest image first.
		$this->images = array_reverse($this->images);

		$markup = '<picture '.$picture_attributes.'>';
			$markup .= '<!--[if IE 9]><video style="display: none;"><![endif]-->';
			for ($i=0; $i < count($this->images)-1; $i++) { 
				$markup .= '<source '.$source_attributes.' srcset="'.$this->images[$i]['src'].'" media="('.$this->images[$i]['media_query'].')">';
			}
			$markup .= '<source '.$source_attributes.' srcset="'.$this->images[count($this->images)-1]['src'].'">';
			$markup .= '<!--[if IE 9]></video><![endif]-->';
			$markup .= '<img srcset="'.$this->images[0]['src'].'" '.$img_attributes.'>';
		$markup .= '</picture>';
		return $markup;
	}

}