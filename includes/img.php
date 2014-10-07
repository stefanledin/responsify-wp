<?php
class Img extends Create_Responsive_image
{
    public $markup;

    function __construct( $id, $settings )
    {
        parent::__construct( $id, $settings );
        $this->set_attributes();
        $this->markup = $this->create_markup();
    }

    protected function set_attributes()
    {
        $default_attributes = array(
            'sizes' => '100vw'
        );

        if ( isset($this->settings['attributes']['img']) ) {
            $this->settings['attributes'] = array_replace_recursive($default_attributes, $this->settings['attributes']['img']);
        } elseif ( isset($this->settings['attributes']) ) {
            $this->settings['attributes'] = array_replace_recursive($default_attributes, $this->settings['attributes']);
        } else {
            $this->settings['attributes'] = $default_attributes;
        }
    }

    protected function create_markup()
    {
        $img_attributes = $this->create_attributes($this->settings['attributes']);

        $markup = '<img ';
            $markup .= 'srcset="';
            for ($i=0; $i < count($this->images); $i++) {
                $markup .= $this->images[$i]['src'].' '.$this->images[$i]['width'].'w, ';
            }
            // Removes the last comma
            $markup = substr($markup, 0, -2);

            $markup .= '" ';
            $markup .= $img_attributes;
        $markup .= '>';
        return $markup;
    }
} 