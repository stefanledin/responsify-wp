<?php
class Native_Img extends Img
{
	protected function create_markup()
    {
        $img_attributes = $this->create_attributes($this->settings['attributes']);

        $markup = '<img ';
            $markup .= 'src="'.$this->images[0]['src'].'"';
            if ( count($this->images) > 1 ) : 
                $markup .= ' srcset="'.$this->srcset_attribute().'" ';
            endif;
            $markup .= $img_attributes;
        $markup .= '>';
        return $markup;
    }
}