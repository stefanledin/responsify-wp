<?php
class Element extends Create_Responsive_image
{
	public $markup;
	public $attributes;

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

		$this->attributes = array(
			'source' => array(
				
			),
			'img' => array()
		);
		
		// The Picture element wants to have the largest image first.
		$this->images = array_reverse($this->images);

		$markup = '<picture '.$picture_attributes.'>';
			$markup .= '<!--[if IE 9]><video style="display: none;"><![endif]-->';
			for ($i=0; $i < count($this->images)-1; $i++) { 
				$srcset_attribute = 'srcset="'.$this->images[$i]['src'].'"';
				$media_attribute = $this->media_attribute( $this->images[$i] );
				$markup .= '<source '.$source_attributes.' '.$srcset_attribute.' '.$media_attribute.'>';
				
				$this->attributes['source'][] = array(
					'srcset' => substr($srcset_attribute, 8, -1),
					'media' => substr($media_attribute, 7, -1)
				);
			}
			$markup .= '<source '.$source_attributes.' srcset="'.$this->images[count($this->images)-1]['src'].'">';
			$this->attributes['source'][] = array( 'srcset' => $this->images[count($this->images)-1]['src'] );
			$markup .= '<!--[if IE 9]></video><![endif]-->';
			$markup .= '<img srcset="'.$this->images[0]['src'].'" '.$img_attributes.'>';
			$this->attributes['img'] = array( 'srcset' => $this->images[0]['src'] );
		$markup .= '</picture>';
		return $markup;
	}

	protected function media_attribute( $image )
	{
		if ( gettype($image['media_query']) == 'array' ) {
			return 'media="('.$image['media_query']['property'] . ': ' . $image['media_query']['value'].')"';
		} 
		return 'media="('.$image['media_query'].')"';
	}

}