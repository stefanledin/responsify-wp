<?php
class Element extends Create_Responsive_image
{
	public $markup;
	public $attributes;

	public function __construct($id, $settings)
	{
		parent::__construct($id, $settings);
		$this->set_attributes();
		if ( has_filter( 'rwp_edit_attributes' ) ) {
            $this->settings['attributes'] = apply_filters( 'rwp_edit_attributes', $this->settings['attributes'] );
        }
		$markup = $this->create_markup();
        if ( get_option( 'rwp_debug_mode', 'off' ) == 'on' ) {
            $markup = $this->prepend_debug_information( $markup );
        }
        $this->markup = $markup;
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
			'source' => array(),
			'img' => array()
		);
		
		// The Picture element wants to have the largest image first.
		$this->images = array_reverse($this->images);
		$markup = '<picture '.$picture_attributes.'>';
			$markup .= '<!--[if IE 9]><video style="display: none;"><![endif]-->';
			for ($i=0; $i < count($this->images)-1; $i++) { 
				$media_attribute = $this->media_attribute( $this->images[$i] );
				$srcset_attribute = $this->srcset_attribute( $this->images[$i] );
				$markup .= '<source '.$source_attributes.' srcset="'.$srcset_attribute.'" media="'.$media_attribute.'">';
				$this->attributes['source'][] = array(
					'media' => $media_attribute,
					'srcset' => $srcset_attribute
				);
			}
			$markup .= '<!--[if IE 9]></video><![endif]-->';
			$img_srcset_attribute = $this->srcset_attribute($this->images[count($this->images)-1]);
			$markup .= '<img srcset="'.$img_srcset_attribute.'" '.$img_attributes.'>';
			$this->attributes['img']['srcset'] = $img_srcset_attribute;
		$markup .= '</picture>';
		return $markup;
	}

	protected function srcset_attribute( $image )
	{
        $attribute[] = $image['src'];
        if ( isset($image['highres']) ) {
            foreach ($image['highres'] as $density => $highres) {
                $attribute[] = $highres['src'].' '.$density;
            }
        }
        return implode(', ', $attribute);
	}

	protected function media_attribute( $image )
	{
		if ( gettype($image['media_query']) == 'array' ) {
			return '('.$image['media_query']['property'] . ': ' . $image['media_query']['value'].')';
		} 
		return '('.$image['media_query'].')';
	}

}