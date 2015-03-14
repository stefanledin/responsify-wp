<?php
class Native_Element extends element
{
	protected function create_markup()
	{
		$picture_attributes = $this->create_attributes($this->settings['attributes']['picture']);
		$source_attributes = $this->create_attributes($this->settings['attributes']['source']);
		$img_attributes = $this->create_attributes($this->settings['attributes']['img']);
		
		// The Picture element wants to have the largest image first.
		$this->images = array_reverse($this->images);

		$markup = '<picture '.$picture_attributes.'>';
			for ($i=0; $i < count($this->images)-1; $i++) { 
				$media_attribute = $this->media_attribute( $this->images[$i] );
				$markup .= '<source '.$source_attributes.' srcset="'.$this->images[$i]['src'].'" media="'.$media_attribute.'">';
			}
			$markup .= '<img src="'.$this->images[count($this->images)-1]['src'].'" '.$img_attributes.'>';
		$markup .= '</picture>';
		return $markup;
	}
}