<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\HttpHeaderAuth\Http\Controllers'], function()
{
    Route::get('/', 'HttpHeaderAuthController@index');
});
