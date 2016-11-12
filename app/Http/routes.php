<?php

use DB;

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});

Route::post('oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});

Route::get('oauth/validate_token/', ['middleware' => 'oauth', function() {
	$tokens = DB::table('oauth_access_tokens')->get();
	foreach ($tokens as $token) {
		$datetime1 = new DateTime($token->created_at);
		$datetime2 = new DateTime();
		$interval = $datetime1->diff($datetime2);
		$days = intval($interval->format('%a'));
		if ($days > 1) {
			DB::table('oauth_access_tokens')->where('id', $token->id)->delete();
		}
	}

	$sessions = DB::table('oauth_sessions')->get();
	foreach ($sessions as $session) {
		$datetime1 = new DateTime($session->created_at);
		$datetime2 = new DateTime();
		$interval = $datetime1->diff($datetime2);
		$days = intval($interval->format('%a'));
		if ($days > 1) {
			DB::table('oauth_sessions')->where('id', $session->id)->delete();
		}
	}

    return Response::json("ACK");
}]);