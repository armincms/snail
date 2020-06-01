<?php  
// base resource
$router->group(['prefix' => 'resources'], function($router) {
	$router->get('{resource}', 'ResourceIndexController@handle'); 
	$router->get('{resource}/{resourceId}', 'ResourceShowController@handle');   
}); 