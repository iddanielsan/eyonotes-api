<?php

$route->before('/', 'App\Middlewares\ClientAuth@init');

$route->get('/', function(){
    echo "teste";
});