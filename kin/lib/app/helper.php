<?php namespace kin\app;
declare(encoding='UTF-8');

class helper {

	private $root_url = '';
	
	private $settings = null;

	public function __construct() {
	
	}
	
	
	
	public function attach_settings(settings $settings) {
		$this->settings = $settings;
		$this->compile_root_url();
		return($this);
	}
	
	
	
	public function css($css_file, $media='screen', $local_file=true) {
		if ($local_file) {
			$css_file = $this->root_url.$this->settings->css_path.$this->append_extension($css_file, '.css');
		}
		return(sprintf('<link type="text/css" rel="stylesheet" href="%s" media="%s">%s', $css_file, $media, PHP_EOL));
	}
	
	public function js($js_file, $local_file=true) {
		if ($local_file) {
			$js_file = $this->root_url.$this->settings->js_path.$this->append_extension($js_file, '.js');
		}
		return(sprintf('<script src="%s" type="text/javascript"></script>%s', $js_file, PHP_EOL));
	}
	
	public function img($img_src, $alt_text=null, $tag_attributes=null, $local_file=true) {
		if ($local_file) {
			$img_src = $this->root_url.$this->settings->images_path.$img_src;
		}
		return(sprintf('<img src="%s" alt="%s" title="%s" %s>%s', $img_src, $alt_text, $alt_text, $tag_attributes, PHP_EOL));
	}
	
	public function url() {
		$argc = func_num_args();
		$argv = func_get_args();

		$http_parameters = '';
		if ($argc > 1 && is_array($argv[$argc-1])) {
			$http_parameters = http_build_query(array_pop($argv));
		}
		
		$root_url = $this->root_url;
		if (!$this->settings->rewrite) {
			$root_url .= 'index.php/';
		}
		
		$url = $root_url.implode('/', $argv);
		if (!empty($http_parameters)) {
			$url .= '?'.$http_parameters;
		}
		return($url);
	}
	
	
	
	private function append_extension($file, $ext) {
		if (0 == preg_match('/\\'.$ext.'$/i', $file)) {
			$file .= $ext;
		}
		return($file);
	}

	private function compile_root_url() {
		$this->root_url = $this->settings->url;
		if ($this->is_secure() || $this->settings->force_ssl) {
			$this->root_url = $this->settings->secure_url;
		}
		return(true);
	}

	private function is_secure() {
		return('on' === strtolower(filter_input(INPUT_SERVER, 'HTTPS')));
	}

}