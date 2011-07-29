<?php namespace metrics;
declare(encoding='UTF-8');

require_once('jolt.php');

$routes = array(
	 array('GET', '/', 'index_controller.php', 'metrics\index_controller', 'action_index')
	,array('GET', '/read/user/%n', 'index_controller.php', 'metrics\index_controller', 'action_read_user')
	,array('GET', '/read/user/%n/and/delete/user/%n', 'index_controller.php', 'metrics\index_controller', 'action_read_user_and_delete_user')
	,array('GET', '/search/%s', 'search_controller.php', 'metrics\search_controller', 'action_search')
);

$jolt = new \jolt\jolt;
$jolt->set_application_path(__DIR__.'/application/')
	->set_routes($routes)
	->set_route_404(array('GET', '/', 'error_controller.php', 'metrics\error_controller', 'action_error_404'));

echo $jolt->execute();
//var_dump($jolt->get_execution_time());
