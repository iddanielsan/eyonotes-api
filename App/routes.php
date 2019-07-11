<?php

$route->before('/', 'App\Middlewares\ClientAuth@init');

$route->group('/user', function(){
	$this->post('/register', 'App\Controllers\User\Register@init');
});