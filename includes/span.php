<?php
class Span extends Create_Responsive_image
{
    public $markup;

    function __construct( $id, $settings )
    {
        parent::__construct( $id, $settings );
        $this->set_attributes();
        $markup = $this->create_markup();
        if ( get_option( 'rwp_debug_mode', 'off' ) == 'on' ) {
            $markup = $this->prepend_debug_information( $markup );
        }
        $this->markup = $markup;
    }

    protected function set_attributes()
    {
        $default_attributes = array(
            'picture_span' => array(
                'data-alt' => (isset($this->settings['attributes']['img']['alt'])) ? $this->settings['attributes']['img']['alt'] : $this->get_image_meta('alt')
            ),
            'src_span' => array()
        );

        if ( isset($this->settings['attributes']) ) {
            $this->settings['attributes'] = array_replace_recursive($default_attributes, $this->settings['attributes']);
        } else {
            $this->settings['attributes'] = $default_attributes;
        }
    }

    protected function create_markup()
    {
        $picture_span_attributes = $this->create_attributes($this->settings['attributes']['picture_span']);
        $src_span_attributes = $this->create_attributes($this->settings['attributes']['src_span']);

        $markup = '<span data-picture '.$picture_span_attributes.'>';
            $markup .= '<span data-src="'.$this->images[0]['src'].'" '.$src_span_attributes.'></span>';
            for ($i=1; $i < count($this->images); $i++) {
                $media_attribute = $this->media_attribute( $this->images[$i] );
                $markup .= '<span data-src="'.$this->images[$i]['src'].'" '.$media_attribute.' '.$src_span_attributes.'></span>';
            }
            $markup .= '<noscript>';
                $markup .= '<img src="'.$this->images[0]['src'].'" alt="'.$this->get_image_meta('alt').'">';
            $markup .= '</noscript>';
        $markup .= '</span>';
        return $markup;
    }

    protected function media_attribute( $image )
    {
        if ( gettype($image['media_query']) == 'array' ) {
            return 'data-media="('.$image['media_query']['property'] . ': ' . $image['media_query']['value'].')"';
        } 
        return 'data-media="('.$image['media_query'].')"';
    }

} 