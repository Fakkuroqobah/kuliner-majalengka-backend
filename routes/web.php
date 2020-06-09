<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

/* ADMIN -------------------------------------------------------------- */
$router->post('/admin/login', 'Admin\AdminController@login');
/* -------------------------------------------------------------------- */

$router->post('/register', 'UserController@register');
$router->post('/login', 'UserController@login');

// restaurant
$router->get('/restaurant', 'RestaurantController@all');
$router->get('/restaurant/random', 'RestaurantController@random');
$router->get('/restaurant/popular', 'RestaurantController@popular');
$router->get('/restaurant/detail/{restaurant}', 'RestaurantController@show');

// menu
$router->get('/menu', 'MenuController@all');
$router->get('/menu/random', 'MenuController@random');
$router->get('/{restaurant}/menu/', 'MenuController@index');
$router->get('/{restaurant}/menu/{menu}', 'MenuController@show');

// category
$router->get('/category', 'CategoryController@all');
$router->get('/category/random', 'CategoryController@random');

// Gallery
$router->get('/gallery', 'GalleryController@all');
$router->get('/{restaurant}/gallery/', 'GalleryController@index');

// rating
$router->get('/rating/{restaurant}', 'RatingController@show');

// like
$router->get('/like/{restaurant}', 'LikeController@show');

$router->group(['middleware' => 'auth:api'], function() use($router) {
    
    /* ADMIN -------------------------------------------------------------- */
    $router->get('/admin/user/all', 'Admin\AdminUserController@all');
    $router->get('/admin/user/total', 'Admin\AdminUserController@total');
    $router->get('/admin/user/details/{id}', 'Admin\AdminUserController@details');

    $router->get('/admin/restaurant/all', 'Admin\AdminRestaurantController@all');
    $router->get('/admin/restaurant/total', 'Admin\AdminRestaurantController@total');
    $router->post('/admin/restaurant/active/{id}', 'Admin\AdminRestaurantController@active');
    /* -------------------------------------------------------------------- */

    // User
    $router->post('/logout', 'UserController@logout');
    $router->post('/refresh', 'UserController@refresh');
    $router->get('/user/details', 'UserController@details');
    $router->post('/user/update', 'UserController@update');

    // Restaurant
    $router->get('/restaurant/owner', 'RestaurantController@owner');
    $router->get('/restaurant/owner/all', 'RestaurantController@ownerAll');
    $router->get('/restaurant/categories/{restaurant}', 'RestaurantController@category');
    $router->get('/restaurant/galleries/{restaurant}', 'RestaurantController@gallery');
    $router->get('/restaurant/menus/{restaurant}', 'RestaurantController@menu');
    $router->get('/restaurant/likes/{restaurant}', 'RestaurantController@like');
    $router->get('/restaurant/ratings', 'RestaurantController@rating');
    $router->post('/restaurant/create', 'RestaurantController@create');
    $router->post('/restaurant/owner/search', 'RestaurantController@search');
    $router->post('/restaurant/update/{id}', 'RestaurantController@update');
    $router->delete('/restaurant/delete/{id}', 'RestaurantController@delete');

    // Category
    $router->get('/category/owner', 'CategoryController@owner');
    $router->post('/category/create', 'CategoryController@create');
    $router->post('/category/update/{id}', 'CategoryController@update');
    $router->delete('/category/delete/{id}', 'CategoryController@delete');
    
    // menu
    $router->get('/menu/owner', 'MenuController@owner');
    $router->post('/menu/create', 'MenuController@create');
    $router->post('/menu/owner/search', 'MenuController@search');
    $router->post('/menu/update/{id}', 'MenuController@update');
    $router->delete('/menu/delete/{id}', 'MenuController@delete');

    // Gallery
    $router->get('/gallery/owner', 'GalleryController@owner');
    $router->get('/gallery/{id}', 'GalleryController@show');
    $router->post('/gallery/owner/search', 'GalleryController@search');
    $router->post('/gallery/create', 'GalleryController@create');
    $router->post('/gallery/update/{id}', 'GalleryController@update');
    $router->delete('/gallery/delete/{id}', 'GalleryController@delete');

    // rating
    $router->get('/all/rating', 'RatingController@all');
    $router->get('/owner/rating', 'RatingController@owner');
    $router->post('/rating/create/{id}', 'RatingController@create');
    $router->post('/rating/update/{id}', 'RatingController@update');
    $router->delete('/rating/delete/{id}', 'RatingController@delete');

    // like
    $router->get('/owner/like', 'LikeController@owner');
    $router->post('/like/{id}', 'LikeController@create');
    $router->delete('/unlike/{id}', 'LikeController@delete');

});