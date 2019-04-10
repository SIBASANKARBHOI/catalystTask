<?php

Route::group(['module' => 'User', 'middleware' => ['web'], 'namespace' => 'App\Modules\User\Controllers'], function() {

    Route::resource('User', 'UserController');
    Route::get('/user-signup', 'UserController@userSignup');
    Route::post('/user-signup', 'UserController@userSignup');

    Route::get('/user-signin', 'UserController@userSignin');
    Route::post('/user-signin', 'UserController@userSignin');

    Route::get('/user-dashboard', 'UserController@userDashboard');
    Route::get('/edit-user/{id}', 'UserController@editUserProfile');
    Route::post('/edit-user/{id}', 'UserController@editUserProfile');
    Route::get('/logout', 'UserController@logout');

});
