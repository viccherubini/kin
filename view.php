<?php namespace jolt;
declare(encoding='UTF-8');

class view {

	private $css_path = null;
	private $image_path = null;
	private $js_path = null;
	private $view_path = null;
	
	private $secure_url = null;
	private $url = null;
	private $use_rewrite = false;
	private $view_type = null;
	
	private $payload = null;
	private $rendering = null;

	private $javascripts = array();

	public function __construct() {
		
	}

	public function __destruct() {

	}
	
	public function attach_payload(payload $payload) {
		$this->payload = $payload;
		return $this;
	}

	public function render($view) {
		$view = "{$view}.{$this->view_type}".jolt::ext;

		// Find the view file
		$view_path = $this->view_path.$view;
		if (!is_file($view_path)) {
			throw new \Exception("The view was not found in the path specified: {$view_path}.");
		}

		$payload = $this->payload;
		ob_start();
			require($view_path);
		$this->rendering = ob_get_clean();

		return $this;
	}

	public function safe($v) {
		return htmlentities($v, ENT_COMPAT, 'UTF-8');
	}

	public function css($css_file, $media='screen', $local_file=true) {
		if ($local_file) {
			$css_file = $this->get_root_url().$this->css_path.$this->append_extension($css_file, '.css');
		}

		$link_tag = sprintf('<link type="text/css" rel="stylesheet" href="%s" media="%s">%s', $css_file, $media, PHP_EOL);
		return $link_tag;
	}

	public function href($url, $text, $tag_attributes=null, $local_url=true, $secure=false) {
		if ($local_url) {
			$url = $this->url($url, $secure);
		}

		$text = $this->safe($text);
		$href = sprintf('<a href="%s" %s>%s</a>%s', $url, $tag_attributes, $text, PHP_EOL);
		return $href;
	}

	public function img($img_src, $alt_text=null, $tag_attributes=null, $local_file=true) {
		if ($local_file) {
			$img_src = $this->get_root_url().$this->image_path.$img_src;
		}

		$img_tag = sprintf('<img src="%s" alt="%s" title="%s" %s>%s', $img_src, $alt_text, $alt_text, $tag_attributes, PHP_EOL);
		return $img_tag;
	}

	public function js($javascript_file, $local_file=true) {
		$javascript_file = $this->append_extension($javascript_file, '.js');

		if ($local_file) {
			$javascript_file = $this->get_root_url().$this->js_path.$javascript_file;
		}

		$script_tag = sprintf('<script src="%s" type="text/javascript"></script>%s', $javascript_file, PHP_EOL);
		return $script_tag;
	}
	
	public function register_javascript($javascript_file) {
		$this->javascripts[] = $this->js($javascript_file);
		return $this;
	}
	
	public function include_javascript() {
		return(implode(PHP_EOL, $this->javascripts));
	}

	public function url() {
		$argc = func_num_args();
		$argv = func_get_args();

		$http_parameters = null;
		$url_prefix = $this->url;
		if ($argc > 0 && is_bool($argv[$argc-1])) {
			$argc--;
			$secure = array_pop($argv);
			if ($secure) {
				$url_prefix = $this->secure_url;
			}

			if (is_array($argv[$argc-1])) {
				$argc--;
				$http_parameters = array_pop($argv);
				$http_parameters = http_build_query($http_parameters);
			}
		}

		$p = $this->make_url_parameters($argc, $argv);
		$url = $url_prefix.$p;
		if (!empty($http_parameters)) {
			$url .= '?'.$http_parameters;
		}

		return $url;
	}

	public function set_css_path($css_path) {
		$this->css_path = $this->append_directory_separator($css_path);
		return $this;
	}

	public function set_image_path($image_path) {
		$this->image_path = $this->append_directory_separator($image_path);
		return $this;
	}

	public function set_js_path($js_path) {
		$this->js_path = $this->append_directory_separator($js_path);
		return $this;
	}

	public function set_secure_url($secure_url) {
		$this->secure_url = $this->append_url_slash(trim($secure_url));
		return $this;
	}

	public function set_url($url) {
		$this->url = $this->append_url_slash(trim($url));
		return $this;
	}

	public function set_use_rewrite($use_rewrite) {
		if (!is_bool($use_rewrite)) {
			$use_rewrite = false;
		}
		$this->use_rewrite = $use_rewrite;
		return $this;
	}

	public function set_view_path($view_path) {
		$this->view_path = $this->append_directory_separator($view_path);
		return $this;
	}
	
	public function set_view_type($view_type) {
		$this->view_type = $view_type;
		return $this;
	}

	public function get_blocks() {
		return $this->blocks;
	}

	public function get_block($block_name) {
		if (array_key_exists($block_name, $this->blocks)) {
			return $this->blocks[$block_name];
		}
		return null;
	}

	public function get_rendering() {
		return $this->rendering;
	}




	private function append_extension($file, $ext) {
		if (0 == preg_match('/\\'.$ext.'$/i', $file)) {
			$file .= $ext;
		}
		return $file;
	}

	private function append_directory_separator($path) {
		return(rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
	}

	private function append_url_slash($url) {
		return(rtrim($url, '/').'/');
	}

	private function get_root_url() {
		if ($this->is_secure()) {
			return $this->secure_url;
		}
		return $this->url;
	}

	private function is_secure() {
		// Unfortunately only way to detect if a page is secure or
		// not is to use the $_SERVER superglobal
		$is_secure = false;
		if (isset($_SERVER)) {
			if (array_key_exists('HTTPS', $_SERVER)) {
				$is_secure = ('on' === strtolower($_SERVER['HTTPS']));
			}
		}
		return $is_secure;
	}

	private function make_url_parameters($argc, $argv) {
		if (0 == $argc) {
			return null;
		}

		$route = null;
		$root = '';
		if ('/' != $argv[0]) {
			$root = $argv[0];
		}

		if ($argc > 1) {
			$route = '/'.implode('/', array_slice($argv, 1));
		}

		$parameters = $root.$route;
		if (!$this->use_rewrite) {
			$parameters = "index.php/{$parameters}";
		}
		return $parameters;
	}

}