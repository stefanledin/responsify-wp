<?php
class Retina
{
    protected $settings;
    protected $image_sizes;
    protected $all_image_sizes;

    function __construct( $settings )
    {
        $this->settings = $settings;
        $this->all_image_sizes = get_intermediate_image_sizes();
    }

    public function set_sizes( $sizes )
    {
        $this->image_sizes = $sizes;
        
        if ( $this->settings['retina'] == true ) {
            $this->add_all_retina_sizes();
        }
        
        if ( !is_bool($this->settings['retina']) ) {
            
            if ( isset($this->settings['sizes']) ) {
                $this->add_retina_sizes();
            }
            $this->set_retina_sizes();
        } 
        
        if ( $this->settings['retina'] == false ) {
            $this->remove_retina_sizes();
        }

        return $this->image_sizes;
    }

    protected function is_retina_size( $size )
    {
        return strpos($size, '@');
    }

    protected function set_retina_sizes()
    {
        $densities = ( is_array($this->settings['retina']) ) 
            ? $this->settings['retina']
            : array( $this->settings['retina'] );
        $image_sizes = array();
        foreach ( $densities as $density ) {
            foreach ( $this->image_sizes as $image_size ) {
                if ( (!$this->is_retina_size($image_size)) || (strpos($image_size, $density)) ) {
                    if ( !in_array($image_size, $image_sizes) ) {
                        array_push($image_sizes, $image_size);
                    }
                }
            }                
        }
        $this->image_sizes = $image_sizes;
    }

    protected function add_all_retina_sizes()
    {
        foreach ($this->all_image_sizes as $image_size) {
            if ( $this->is_retina_size( $image_size ) && !in_array($image_size, $this->image_sizes) ) {
                array_push($this->image_sizes, $image_size);
            }
        }
    }

    protected function add_retina_sizes()
    {
        $densities = ( is_array($this->settings['retina']) )
            ? $this->settings['retina']
            : array( $this->settings['retina'] );
        foreach ($densities as $density) {
            foreach ( $this->image_sizes as $image_size ) {
                if ( !$this->is_retina_size($image_size) ) {
                    array_push($this->image_sizes, $image_size.'@'.$density);
                }
            }
        }
    }

    protected function remove_retina_sizes()
    {
        $count = count($this->image_sizes);
        for ($i=0; $i < $count; $i++) { 
            if ( $this->is_retina_size($this->image_sizes[$i]) ) {
                unset($this->image_sizes[$i]);
            }
        }
    }

}