<?php
class Img extends Create_Responsive_image
{
    public $markup;
    public $attributes;

    function __construct( $id, $settings )
    {
        parent::__construct( $id, $settings );
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
        $images = $this->images;
        usort($images, array($this, 'order_images_by_media_query'));
        $images = $this->find_smallest_image_and_place_it_last_in_array( $images );

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
    protected function order_images_by_media_query( $a, $b )
    {
        if ( isset($a['media_query']['value']) && isset($b['media_query']['value']) ) {
            if ( (int) $a['media_query']['value'] == (int) $b['media_query']['value'] ) {
                return 0;
            }
            return ( (int) $a['media_query']['value'] < (int) $b['media_query']['value'] ) ? -1 : 1;
        }
    }
    
    protected function find_smallest_image_and_place_it_last_in_array( $images )
    {
        $smallest_image = array_filter($images, array($this, 'find_smallest_image'));
        unset($images[key($smallest_image)]);
        $images = array_reverse($images);
        $images[] = $smallest_image[key($smallest_image)];
        return $images;
    }

    protected function find_smallest_image( $image )
    {
        if ( empty($image['media_query']) ) {
            return $image;
        }
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