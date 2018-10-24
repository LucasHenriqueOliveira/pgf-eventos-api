<?php

Route::group([

    'middleware' => 'api'

], function ($router) {

    Route::post('crawler', 'CrawlerController@process');
    Route::get('marcas', 'MarcasController@getMarcas');
    Route::get('marcas/{id}', 'MarcasController@getModelos');
    Route::post('manual', 'ManualController@save');
    Route::get('manual', 'ManualController@get');
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('sendPasswordResetLink', 'ResetPasswordController@sendEmail');
    Route::post('resetPassword', 'ChangePasswordController@process');

});