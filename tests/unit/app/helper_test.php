<?php namespace kintest\app;
declare(encoding='UTF-8');

use \kin\app\helper as helper,
	\kintest\testcase as testcase;

require_once(__DIR__.'/../../testcase.php');
require_once(__DIR__.'/../../../kin/lib/app/helper.php');

class helper_test extends testcase {
	
	/**
	 * @expectedException \PHPUnit_Framework_Error
	 */
	public function test_attach_settings__requires_settings_object() {
		$helper = new helper;
		$helper->attach_settings(null);
	}
	
	
	
	public function test_css__appends_extension_only_if_local_file() {
		$settings = $this->getMock('\kin\app\settings');
		$settings->force_ssl = false;
		$settings->css_path = 'assets/css/';
		$settings->url = 'http://localhost/';
		
		$helper = new helper;
		$helper->attach_settings($settings);
		
		$local_css_file = 'reset';
		$local_css_file_with_extension = $settings->url.$settings->css_path.$local_css_file.'.css';
		
		$external_css_file = 'http://leftnode.com/path/to/css/reset';
		
		$this->assertTag(array(
			'tag' => 'link',
			'attributes' => array(
				'href' => $local_css_file_with_extension
			)
		), $helper->css($local_css_file));
		
		$this->assertTag(array(
			'tag' => 'link',
			'attributes' => array(
				'href' => $external_css_file
			)
		), $helper->css($external_css_file, 'screen', false));
	}
	
	public function test_css__appends_root_url_if_local_file() {
		$settings = $this->getMock('\kin\app\settings');
		$settings->force_ssl = false;
		$settings->css_path = 'assets/css/';
		$settings->url = 'http://leftnode.com/';
		
		$helper = new helper;
		$helper->attach_settings($settings);
		
		$local_css_file = 'reset';
		$local_css_file_full_url = $settings->url.$settings->css_path.$local_css_file.'.css';
		
		$this->assertTag(array(
			'tag' => 'link',
			'attributes' => array(
				'href' => $local_css_file_full_url
			)
		), $helper->css($local_css_file));
	}
	
	public function test_css__forces_ssl_url_if_local_file() {
		$settings = $this->getMock('\kin\app\settings');
		$settings->force_ssl = true;
		$settings->css_path = 'assets/css/';
		$settings->url = 'http://leftnode.com/';
		$settings->secure_url = 'https://leftnode.com/';
		
		$helper = new helper;
		$helper->attach_settings($settings);
		
		$local_css_file = 'reset';
		$local_css_file_full_url = $settings->secure_url.$settings->css_path.$local_css_file.'.css';
		
		$this->assertTag(array(
			'tag' => 'link',
			'attributes' => array(
				'href' => $local_css_file_full_url
			)
		), $helper->css($local_css_file));
	}
	
	
	
	public function test_js_appends_extension_only_if_local_file() {
		$settings = $this->getMock('\kin\app\settings');
		$settings->force_ssl = false;
		$settings->js_path = 'assets/js/';
		$settings->url = 'http://localhost/';
		
		$helper = new helper;
		$helper->attach_settings($settings);
		
		$local_js_file = 'jquery-1.6.2';
		$local_js_file_with_extension = $settings->url.$settings->js_path.$local_js_file.'.js';
		
		$external_js_file = 'http://leftnode.com/assets/js/jquery-1.6.2';
		
		$this->assertTag(array(
			'tag' => 'script',
			'attributes' => array(
				'src' => $local_js_file_with_extension
			)
		), $helper->js($local_js_file));
		
		$this->assertTag(array(
			'tag' => 'script',
			'attributes' => array(
				'src' => $external_js_file
			)
		), $helper->js($external_js_file, false));
	}
	
	public function test_js__appends_root_url_if_local_file() {
		$settings = $this->getMock('\kin\app\settings');
		$settings->force_ssl = false;
		$settings->js_path = 'assets/js/';
		$settings->url = 'http://leftnode.com/';
		
		$helper = new helper;
		$helper->attach_settings($settings);
		
		$local_js_file = 'jquery-1.6.2';
		$local_js_file_full_url = $settings->url.$settings->js_path.$local_js_file.'.js';
		
		$this->assertTag(array(
			'tag' => 'script',
			'attributes' => array(
				'src' => $local_js_file_full_url
			)
		), $helper->js($local_js_file));
	}
	
	public function test_js__forces_ssl_url_if_local_file() {
		$settings = $this->getMock('\kin\app\settings');
		$settings->force_ssl = true;
		$settings->js_path = 'assets/js/';
		$settings->url = 'http://leftnode.com/';
		$settings->secure_url = 'https://leftnode.com/';
		
		$helper = new helper;
		$helper->attach_settings($settings);
		
		$local_js_file = 'jquery-1.6.2';
		$local_js_file_full_url = $settings->secure_url.$settings->js_path.$local_js_file.'.js';
		
		$this->assertTag(array(
			'tag' => 'script',
			'attributes' => array(
				'src' => $local_js_file_full_url
			)
		), $helper->js($local_js_file));
	}
	
	
	
	public function test_img__appends_root_url_if_local_file() {
		$settings = $this->getMock('\kin\app\settings');
		$settings->force_ssl = false;
		$settings->images_path = 'assets/images/';
		$settings->url = 'http://leftnode.com/';
		
		$helper = new helper;
		$helper->attach_settings($settings);
		
		$local_image_file = 'logo.png';
		$local_image_file_full_url = $settings->url.$settings->images_path.$local_image_file;
		
		$this->assertTag(array(
			'tag' => 'img',
			'attributes' => array(
				'src' => $local_image_file_full_url
			)
		), $helper->img($local_image_file));
	}
	
	public function test_img__forces_ssl_url_if_local_file() {
		$settings = $this->getMock('\kin\app\settings');
		$settings->force_ssl = true;
		$settings->images_path = 'assets/images/';
		$settings->url = 'http://leftnode.com/';
		$settings->secure_url = 'https://leftnode.com/';
		
		$helper = new helper;
		$helper->attach_settings($settings);
		
		$local_image_file = 'logo.png';
		$local_image_file_full_url = $settings->secure_url.$settings->images_path.$local_image_file;
		
		$this->assertTag(array(
			'tag' => 'img',
			'attributes' => array(
				'src' => $local_image_file_full_url
			)
		), $helper->img($local_image_file));
	}
	
	public function test_img__adds_alt_text() {
		$settings = $this->getMock('\kin\app\settings');
		$settings->force_ssl = false;
		$settings->images_path = 'assets/images/';
		$settings->url = 'http://leftnode.com/';
		
		$helper = new helper;
		$helper->attach_settings($settings);
		
		$local_image_file = 'logo.png';
		$alt_text = "Welcome to Kin";
		
		$this->assertTag(array(
			'tag' => 'img',
			'attributes' => array(
				'alt' => $alt_text,
				'title' => $alt_text
			)
		), $helper->img($local_image_file, $alt_text));
	}
	
	public function test_img__adds_additional_attributes() {
		$settings = $this->getMock('\kin\app\settings');
		$settings->force_ssl = false;
		$settings->images_path = 'assets/images/';
		$settings->url = 'http://leftnode.com/';
		
		$helper = new helper;
		$helper->attach_settings($settings);
		
		$local_image_file = 'logo.png';
		$styles = 'border: 1px solid black;';
		$style_attribute = 'style="'.$styles.'"';
		
		$this->assertTag(array(
			'tag' => 'img',
			'attributes' => array(
				'style' => $styles
			)
		), $helper->img($local_image_file, '', $style_attribute));
	}
	


	public function test_url__creates_url_from_parameters() {
		$settings = $this->getMock('\kin\app\settings');
		$settings->force_ssl = false;
		$settings->url = 'http://leftnode.com/';
		$settings->rewrite = false;
		
		$helper = new helper;
		$helper->attach_settings($settings);
		
		$url = $settings->url.'index.php/path/to/some/resource';
		
		$this->assertEquals($url, $helper->url('path', 'to', 'some', 'resource'));
	}
	
	public function test_url__appends_http_parameters() {
		$settings = $this->getMock('\kin\app\settings');
		$settings->force_ssl = false;
		$settings->url = 'http://leftnode.com/';
		$settings->rewrite = false;
		
		$helper = new helper;
		$helper->attach_settings($settings);
		
		$url = $settings->url.'index.php/path/to/some/resource?id=10&fname=Vic&lname=Cherubini';
		
		$this->assertEquals($url, $helper->url('path', 'to', 'some', 'resource', array(
			'id' => 10,
			'fname' => 'Vic',
			'lname' => 'Cherubini'
		)));
	}
	
	public function test_url__force_ssl_makes_url_secure() {
		$settings = $this->getMock('\kin\app\settings');
		$settings->force_ssl = true;
		$settings->url = 'http://leftnode.com/';
		$settings->secure_url = 'https://leftnode.com/';
		$settings->rewrite = false;
		
		$helper = new helper;
		$helper->attach_settings($settings);
		
		$url = $settings->secure_url.'index.php/path/to/some/resource';
		
		$this->assertEquals($url, $helper->url('path', 'to', 'some', 'resource'));
	}
	
	public function test_url__allows_rewrites() {
		$settings = $this->getMock('\kin\app\settings');
		$settings->force_ssl = false;
		$settings->url = 'http://leftnode.com/';
		$settings->rewrite = true;
		
		$helper = new helper;
		$helper->attach_settings($settings);
		
		$url = $settings->url.'path/to/some/resource';
		
		$this->assertEquals($url, $helper->url('path', 'to', 'some', 'resource'));
	}
	
}