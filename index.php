<?php namespace metrics;
declare(encoding='UTF-8');

require_once('jolt.php');

$routes = array(
	 array('GET', '/', 'index_controller.php', 'metrics\index_controller', 'action_index')
	,array('POST', '/', 'index_controller.php', 'metrics\index_controller', 'action_index_post')
	,array('GET', '/read/user/%n', 'index_controller.php', 'metrics\index_controller', 'action_read_user')
	,array('GET', '/read/user/%n/and/delete/user/%n', 'index_controller.php', 'metrics\index_controller', 'action_read_user_and_delete_user')
	,array('GET', '/search/%s', 'search_controller.php', 'metrics\search_controller', 'action_search')
);

$route_404 = array('GET', '/', 'error_controller.php', 'metrics\error_controller', 'action_error_404');

$paths = array(
	'application' => __DIR__.'/application',
	'asset' => 'public/'
);

$settings = array(
	'allow_ssl' => true,
	'force_ssl' => false
);

$jolt = new \jolt\jolt;
$jolt->set_paths($paths)
	->set_settings($settings)
	->set_routes($routes, $route_404);

$jolt->execute();

print_r($jolt);