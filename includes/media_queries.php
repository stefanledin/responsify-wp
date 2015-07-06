<?php
class Media_Queries
{
	protected $images;
	protected $user_settings;
	
	function __construct( $images, $user_settings = null )
	{
		$this->images = $images;
		$this->user_settings = ($user_settings) ? $user_settings : null;
	}

	public function set()
	{
		if ( $this->user_settings ) {
			return $this->set_custom();
		}
		return $this->set_default();
	}

	protected function set_default()
	{
		for ( $i=0; $i < count($this->images ); $i++) { 
			if ( $i > 0 ) {
				$this->images[$i]['media_query'] = array(
					'property' => 'min-width',
					'value' => $this->images[$i-1]['width'] . 'px'
				);
			}
		}
		return $this->images;
	}

	protected function set_custom()
	{
		// This method feels a bit code smelly,
		// but it gets the work done.
		for ( $i=0; $i < count($this->images); $i++ ) { 
			if ( $i > 0 ) {
				foreach ($this->user_settings as $size => $mq) {
					if ( $this->images[$i]['size'] == $size ) {
						$this->images[$i]['media_query'] = $mq;
					}
				}
			}
		}
		return $this->images;
	}

}