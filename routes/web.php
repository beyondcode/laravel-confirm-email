<?php

Route::name('auth.resend_confirmation')->get('/register/resend_confirmation', 'Auth\RegisterController@resendConfirmation');
Route::name('auth.confirm')->get('/register/confirm/{id}', 'Auth\RegisterController@confirm')->middleware('signed');
