<?php

use Dcat\Admin\Admin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('custom-users','UserController');
    $router->resource('verification-mail-log','VerificationMailLogController');
    $router->resource('products','ProductController');
    $router->resource('product-categories','ProductCategoryController');
    $router->resource('product-types','ProductItemTypeController');
    $router->resource('product-option-inventories', 'ProductOptionInventoryController');
});
