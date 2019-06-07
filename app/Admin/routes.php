<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {
	$router->get('/listChange', 'HomeController@listChange');
    $router->get('/', 'HomeController@index')->name('admin.home');
	
	//$router->post('auth/login', 'AuthController@login');	//登入api
	$router->post('member/import', 'MemberController@import');
	$router->resource('operation', LogsController::class);
	$router->resource('member', MemberController::class);
});
