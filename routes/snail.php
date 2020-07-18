<?php  
// base resource
$router->group(['prefix' => '{version}'], function($router) {
	$router->get('{resource}', 'ResourceIndexController@handle'); 
	$router->get('{resource}/{resourceId}', 'ResourceShowController@handle');   
});  