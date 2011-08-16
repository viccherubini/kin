<?php namespace kin\app;
declare(encoding='UTF-8');

class helper {

	private $settings = null;

	public function __construct() {
	
	}
	
	
	
	public function attach_settings(settings $settings) {
		$this->settings = $settings;
		return($this);
	}
	
	
	
	public function css($css_file, $media='screen', $local_file=true) {
		if ($local_file) {
			$css_file = $this->append_extension($css_file, '.css');
			//$root_url = $this->get_root_url();
			//$css_file = $root_url.$this->css_path.$css_file;
		}

		$link_tag = sprintf('<link type="text/css" rel="stylesheet" href="%s" media="%s">%s', $css_file, $media, PHP_EOL);
		return $link_tag;
	}
	
	
	
	
	
	
	
	private function append_extension($file, $ext) {
		if (0 == preg_match('/\\'.$ext.'$/i', $file)) {
			$file .= $ext;
		}
		return $file;
	}


}