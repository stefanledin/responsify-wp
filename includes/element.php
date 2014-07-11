<?php
class Element extends Picturefill
{
	public $markup;

	public function __construct($id, $settings)
	{
		parent::__construct($id, $settings);
		$this->setAttributes();
		$this->markup = $this->createMarkup();
	}

	protected function setAttributes()
	{
		$defaultAttributes = array(
			'picture_span' => array(
				'data-alt' => $this->getImageMeta('alt')
			),
			'src_span' => array()
		);
		if (isset($this->settings['attributes'])) {
			$this->settings['attributes'] = array_replace_recursive($defaultAttributes, $this->settings['attributes']);
		} else {
			$this->settings['attributes'] = $defaultAttributes;
		}
	}

	protected function createMarkup()
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