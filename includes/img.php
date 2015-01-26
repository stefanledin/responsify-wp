<?php
class Img extends Create_Responsive_image
{
    public $markup;
    public $attributes;

    function __construct( $id, $settings )
    {
        parent::__construct( $id, $settings );
        $this->set_attributes();
        $this->markup = $this->create_markup();
    }

    protected function set_attributes()
    {
        $default_attributes = array(
            'sizes' => $this->sizes_attribute()
        );

        if ( isset($this->settings['attributes']['img']) ) {
            $this->settings['attributes'] = array_replace_recursive($default_attributes, $this->settings['attributes']['img']);
        } elseif ( isset($this->settings['attributes']) ) {
            $this->settings['attributes'] = array_replace_recursive($default_attributes, $this->settings['attributes']);
        } else {
            $this->settings['attributes'] = $default_attributes;
        }
    }

    protected function sizes_attribute()
    {
        $images = array_reverse($this->images);
        $attribute = array();
        for ($i=0; $i < count($images); $i++) { 
            if ( isset($images[$i]['media_query']) ) {
                $mq = $images[$i]['media_query'];
                $attribute[] = '('.$mq['property'].': '.$mq['value'].') '.$images[$i]['width'].'px';
            } else {
                $attribute[] = $images[$i]['width'].'px';
            }
        }
        $this->attributes['sizes'] = implode(', ', $attribute);
        return $this->attributes['sizes'];
    }

    protected function srcset_attribute()
    {
        $attribute = array();
        for ($i=0; $i < count($this->images); $i++) {
            $attribute[] = $this->images[$i]['src'].' '.$this->images[$i]['width'].'w';
            if ( isset($this->images[$i]['highres']) ) {
                foreach ($this->images[$i]['highres'] as $density => $highres) {
                    $attribute[] = $highres['src'].' '.$highres['width'].'w';
                }
            }
        }
        $this->attributes['srcset'] = implode(', ', $attribute);
        return $this->attributes['srcset'];
    }

    protected function create_markup()
    {
        $img_attributes = $this->create_attributes($this->settings['attributes']);
        $markup = '<img ';
            if ( count($this->images) == 1 ) : 
                $markup .= 'src="'.$this->images[0]['src'].'"';
            else:
                $markup .= 'srcset="'.$this->srcset_attribute().'" ';
            endif;
            $markup .= $img_attributes;
        $markup .= '>';
        return $markup;
    }
} 