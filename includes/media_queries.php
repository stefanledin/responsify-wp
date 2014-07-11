<?php
class Media_Queries
{
	protected $images;
	protected $user_settings;
	
	function __construct( $images )
	{
		$this->images = $images;
	}

	public function set()
	{
		$previous_image_width = null;
		for ( $i=0; $i < count($this->images ); $i++) { 
			if ( $i > 0 ) {
				$this->images[$i]['media_query'] = 'min-width: ' . $this->images[$i-1]['width'] . 'px';
				$previous_image_width = $this->images[$i]['width'];
			}
		}
		return $this->images;
	}

}