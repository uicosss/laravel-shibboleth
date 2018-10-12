<?php

Route::group(['middleware' => 'web'], function () {
    Route::name('shibboleth-login')->get('/shibboleth-login', 'StudentSystemServices\Shibboleth\Controllers\ShibbolethController@login');
    Route::name('shibboleth-authenticate')->get('/shibboleth-authenticate', 'StudentSystemServices\Shibboleth\Controllers\ShibbolethController@idpAuthenticate');
    Route::name('shibboleth-logout')->get('/shibboleth-logout', 'StudentSystemServices\Shibboleth\Controllers\ShibbolethController@destroy');
});
