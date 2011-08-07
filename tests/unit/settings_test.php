<?php namespace kintest;
declare(encoding='UTF-8');

use \kin\settings as settings;

require_once(__DIR__.'/testcase.php');
require_once(__DIR__.'/../../kin/settings.php');

class settings_test extends testcase {

	public function test___set__can_only_change_predefined_settings() {
		$property = uniqid('kin_');
		
		$settings = new settings;
		$settings->$property = 'value';
		
		$this->assertFalse(property_exists($settings, $property));
	}

	public function test___set__can_not_change_default_setting_type() {
		$settings = new settings;
		
		$allow_ssl = $settings->allow_ssl;
		$settings->allow_ssl = 'yes';
		
		$this->assertInternalType('bool', $settings->allow_ssl);
		$this->assertEquals($allow_ssl, $settings->allow_ssl);
	}
	
	public function test___set__always_appends_ending_slash_for_paths() {
		$app_path = __DIR__.'/';
		$settings = new settings;
		
		$settings->app_path = __DIR__;
		$this->assertEquals($app_path, $settings->app_path);
	}
	
	
	
	/**
	 * @expectedException \kin\exception\unrecoverable
	 */
	public function test_compile__requires_app_path() {
		$settings = new settings;
		
		$settings->compile();
	}
	
	public function test_compile__compiles_app_paths_if_empty() {
		$settings = new settings;
		$settings->app_path = __DIR__;
		
		$settings->compile();
		$this->assertNotEmpty($settings->controllers_path);
		$this->assertNotEmpty($settings->views_path);
		$this->assertNotEmpty($settings->validators_path);
	}
	
	public function test_compile__compiles_assets_paths_if_empty() {
		$settings = new settings;
		$settings->app_path = __DIR__;
		
		$settings->compile();
		$this->assertNotEmpty($settings->css_path);
		$this->assertNotEmpty($settings->js_path);
		$this->assertNotEmpty($settings->images_path);
	}

	public function test_compile__compiles_server_name_if_empty() {
		$settings = new settings;
		$settings->app_path = __DIR__;
		
		$settings->compile();
		$this->assertNotEmpty($settings->server_name);
		$this->assertEquals('localhost', $settings->server_name);
	}

	public function test_compile__compiles_url_if_empty() {
		$settings = new settings;
		$settings->app_path = __DIR__;
		
		$settings->compile();
		$this->assertNotEmpty($settings->url);
		$this->assertEquals('http://localhost/', $settings->url);
	}
	
	public function test_compile__compiles_secure_url_if_empty() {
		$settings = new settings;
		$settings->app_path = __DIR__;
		
		$settings->compile();
		$this->assertNotEmpty($settings->secure_url);
		$this->assertEquals('https://localhost/', $settings->secure_url);
	}
	
	public function test_compile__compiles_empty_url_as_secure_if_forced() {
		$settings = new settings;
		$settings->app_path = __DIR__;
		$settings->force_ssl = true;
		
		$settings->compile();
		$this->assertNotEmpty($settings->url);
		$this->assertEquals('https://localhost/', $settings->url);
	}
	
	
	
	public function test_add_custom__custom_variable_can_not_exist_in_settings() {
		$settings = new settings;
		$settings->add_custom('url', 'http://leftnode.com');
		
		$this->assertEmpty($settings->get_custom());
	}
	
	public function test_add_custom__pushes_custom_variable_to_settings() {
		$custom_key = 'custom_path';
		$custom_value = '/path/to/some/variable/';
		
		$settings = new settings;
		$settings->add_custom($custom_key, $custom_value);
		
		$this->assertEquals($custom_value, $settings->get_custom_by_key($custom_key));
	}

}