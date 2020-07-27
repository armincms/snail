<?php   
$router->group(['prefix' => 'schemas'], function($router) {  
	$router->get('{schema}', 'SchemaIndexController@handle'); 
	$router->get('{schema}/version/{version}', 'SchemaVersionController@handle');   
}); 

// base resource
$router->group(['prefix' => '{version}'], function($router) { 
	$router->get('{resource}', 'ResourceIndexController@handle'); 
	$router->get('{resource}/{resourceId}', 'ResourceShowController@handle');   
});   