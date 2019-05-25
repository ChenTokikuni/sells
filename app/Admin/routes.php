<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
	
	//$router->post('auth/login', 'AuthController@login');	//登入api
	
	$router->resource('operation', LogsController::class);
	$router->resource('member', MemberController::class);
});
