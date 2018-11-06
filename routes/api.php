<?php

Route::group([

    'middleware' => 'api'

], function ($router) {

    Route::get('dashboard', 'DashboardController@get');

    Route::get('programacao', 'ProgramacaoController@get');
    Route::get('programacao-list', 'ProgramacaoController@getListProgramacao');
    Route::get('programacao/{id}', 'ProgramacaoController@getList');
    Route::post('programacao', 'ProgramacaoController@save');
    Route::post('programacao-edit', 'ProgramacaoController@edit');
    Route::delete('programacao/{id}', 'ProgramacaoController@remove');

    Route::get('status', 'StatusController@get');
    Route::get('status/{id}', 'StatusController@getPerguntas');
    Route::post('status', 'StatusController@save');
    Route::put('status', 'StatusController@edit');
    Route::delete('status/{id}', 'StatusController@remove');

    Route::get('uso', 'UsoController@get');
    Route::get('uso/{id}', 'UsoController@getPalestrante');
    Route::post('uso', 'UsoController@save');
    Route::put('uso', 'UsoController@edit');
    Route::delete('uso/{id}', 'UsoController@remove');

    Route::get('users', 'UsuarioController@get');

    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('sendPasswordResetLink', 'ResetPasswordController@sendEmail');
    Route::post('resetPassword', 'ChangePasswordController@process');

});