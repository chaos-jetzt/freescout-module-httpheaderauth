<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\LouketoAuth\Http\Controllers'], function()
{
    Route::get('/', 'LouketoAuthController@index');
});
