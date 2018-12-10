<?php

Route::group([

    'middleware' => 'api'

], function ($router) {

    Route::get('dashboard', 'DashboardController@get');

    Route::get('evento/{id}', 'ProgramacaoController@getEvento');
    Route::get('programacao', 'ProgramacaoController@get');
    Route::get('programacao-list', 'ProgramacaoController@getListProgramacao');
    Route::get('programacao/{id}', 'ProgramacaoController@getList');
    Route::get('votacao/{id}', 'ProgramacaoController@getVotacao');
    Route::get('votacao-detalhada/{id}/{id_user}', 'ProgramacaoController@getVotacaoDetalhada');
    Route::post('voto', 'ProgramacaoController@saveVoto');
    Route::post('programacao', 'ProgramacaoController@save');
    Route::post('programacao-edit', 'ProgramacaoController@edit');
    Route::post('inscricao', 'ProgramacaoController@setInscricao');
    Route::post('programacao-finalizar', 'ProgramacaoController@finalizar');
    Route::post('programacao-desabilitar', 'ProgramacaoController@desabilitar');
    Route::post('inscricao-cancelar', 'ProgramacaoController@removeInscricao');
    Route::delete('programacao/{id}', 'ProgramacaoController@remove');

    Route::get('status', 'StatusController@get');
    Route::get('status/{id}', 'StatusController@getPerguntas');
    Route::post('status', 'StatusController@save');
    Route::post('resposta', 'StatusController@save');
    Route::post('pergunta', 'StatusController@savePergunta');
    Route::put('status', 'StatusController@edit');
    Route::delete('status/{id}', 'StatusController@remove');

    Route::get('uso', 'UsoController@get');
    Route::get('uso/{id}', 'UsoController@getPalestrante');
    Route::post('uso', 'UsoController@save');
    Route::put('uso', 'UsoController@edit');
    Route::delete('uso/{id}', 'UsoController@remove');

    Route::get('users/validar/{token}', 'UsuarioController@validarUser');
    Route::get('users/response-password-reset/{token}', 'UsuarioController@passwordReset');
    Route::get('users', 'UsuarioController@get');
    Route::get('user/{id}', 'UsuarioController@getUser');
    Route::post('users/change-password', 'UsuarioController@passwordChange');

    Route::get('email-certificado', 'AuthController@emailCertificado');
    Route::post('certificado', 'AuthController@certificado');

    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
    Route::post('logout', 'AuthController@logout');
    Route::post('reset', 'AuthController@reset');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('sendPasswordResetLink', 'ResetPasswordController@sendEmail');
    Route::post('resetPassword', 'ChangePasswordController@process');

});