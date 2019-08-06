<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/register', 'UserController@register');
$router->post('/login', 'UserController@login');

$router->group(['middleware' => 'auth:api'], function() use($router) {
    
    // User
    $router->get('/user/details', 'UserController@details');
    $router->post('/user/update', 'UserController@update');

    // Restaurant
    $router->get('/restaurant', 'RestaurantController@all');
    $router->get('/restaurant/owner', 'RestaurantController@owner');
    $router->get('/restaurant/{restaurant}', 'RestaurantController@show');
    $router->post('/restaurant/create', 'RestaurantController@create');
    $router->post('/restaurant/update/{id}', 'RestaurantController@update');
    $router->delete('/restaurant/delete/{id}', 'RestaurantController@delete');

    // menu
    $router->get('/menu', 'MenuController@all');
    $router->get('/menu/owner', 'MenuController@owner');
    $router->get('/{restaurant}/menu/', 'MenuController@index');
    $router->get('/{restaurant}/menu/{menu}', 'MenuController@show');
    $router->post('/menu/create', 'MenuController@create');
    $router->post('/menu/update/{id}', 'MenuController@update');
    $router->delete('/menu/delete/{id}', 'MenuController@delete');

    // Category
    $router->get('/category', 'CategoryController@all');
    $router->get('/category/owner', 'CategoryController@owner');
    $router->post('/category/create', 'CategoryController@create');
    $router->post('/category/update/{id}', 'CategoryController@update');
    $router->delete('/category/delete/{id}', 'CategoryController@delete');

    // Gallery
    $router->get('/gallery', 'GalleryController@all');
    $router->get('/gallery/owner', 'GalleryController@owner');
    $router->get('/{restaurant}/gallery/', 'GalleryController@index');
    $router->post('/gallery/create', 'GalleryController@create');
    $router->post('/gallery/update/{id}', 'GalleryController@update');
    $router->delete('/gallery/delete/{id}', 'GalleryController@delete');

    // rating
    $router->get('/rating/{restaurant}', 'RatingController@show');
    $router->get('/owner/rating', 'RatingController@owner');
    $router->post('/rating/create/{id}', 'RatingController@create');
    $router->post('/rating/update/{id}', 'RatingController@update');
    $router->delete('/rating/delete/{id}', 'RatingController@delete');

    // like
    $router->get('/like/{restaurant}', 'LikeController@show');
    $router->get('/owner/like', 'LikeController@owner');
    $router->post('/like/{id}', 'LikeController@create');
    $router->delete('/unlike/{id}', 'LikeController@delete');

});