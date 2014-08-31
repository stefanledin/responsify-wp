<?php
class Element extends Picturefill
{
	public $markup;

	public function __construct($id, $settings)
	{
		parent::__construct($id, $settings);
		$this->setAttributes();
		$element = get_option( 'selected_element', 'span' );
		$this->markup = $this->createMarkup( $element );
	}

	public function setAttributes()
	{
		if ( get_option( 'selected_element' ) == 'picture' ) {
			$default_attributes = array(
				'picture' => array(),
				'source' => array(),
				'img' => array()
			);
		} else {
			$default_attributes = array(
				'picture_span' => array(
					'data-alt' => ($this->settings['attributes']['img']['alt']) ? $this->settings['attributes']['img']['alt'] : $this->getImageMeta('alt')
				),
				'src_span' => array()
			);
		}

		if ( isset($this->settings['attributes']) ) {
			$this->settings['attributes'] = array_replace_recursive($default_attributes, $this->settings['attributes']);
		} else {
			$this->settings['attributes'] = $default_attributes;
		}
	}

	protected function createMarkup( $element )
	{
		switch ( $element ) {
			case 'picture':
				return $this->picture();
				break;
			default:
				return $this->span();
				break;
		}
	}

	protected function span()
	{
		$picture_span_attributes = $this->createAttributes($this->settings['attributes']['picture_span']);
		$src_span_attributes = $this->createAttributes($this->settings['attributes']['src_span']);
		$markup = '<span data-picture '.$picture_span_attributes.'>';
			$markup .= '<span data-src="'.$this->images[0]['src'].'" '.$src_span_attributes.'></span>';
			for ($i=1; $i < count($this->images); $i++) { 
				$markup .= '<span data-src="'.$this->images[$i]['src'].'" data-media="('.$this->images[$i]['media_query'].')" '.$src_span_attributes.'></span>';
			}
			$markup .= '<noscript>';
				$markup .= '<img src="'.$this->images[0]['src'].'" alt="'.$this->getImageMeta('alt').'">';
			$markup .= '</noscript>';
		$markup .= '</span>';
		return $markup;
	}

	protected function picture()
	{
		$picture_attributes = $this->createAttributes($this->settings['attributes']['picture']);
		$source_attributes = $this->createAttributes($this->settings['attributes']['source']);
		$img_attributes = $this->createAttributes($this->settings['attributes']['img']);

		// The Picture element wants to have the largest image first.
		$this->images = array_reverse($this->images);

		$markup = '<picture '.$picture_attributes.'>';
			$markup .= '<!--[if IE 9]><video style="display: none;"><![endif]-->';
			for ($i=0; $i < count($this->images)-1; $i++) { 
				$markup .= '<source '.$source_attributes.' srcset="'.$this->images[$i]['src'].'" media="(min-width: '.$this->images[$i+1]['width'].'px)">';
			}
			$markup .= '<source '.$source_attributes.' srcset="'.$this->images[count($this->images)-1]['src'].'">';
			$markup .= '<!--[if IE 9]></video><![endif]-->';
			$markup .= '<img srcset="'.$this->images[0]['src'].'" '.$img_attributes.'>';
		$markup .= '</picture>';

		return $markup;
	}

	protected function getImageMeta( $meta )
	{
		return get_post_meta( $this->id, '_wp_attachment_image_'.$meta, true );
	}

	protected function createAttributes( $attr )
	{
		$attributes = '';
		foreach ( $attr as $attribute => $value ) {
			$attributes .= $attribute . '="'.$value.'" ';
		}
		// Removes the extra space after the last attribute
		return substr($attributes, 0, -1);
	}
}