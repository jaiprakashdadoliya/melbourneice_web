<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
use Illuminate\Http\Request;
/*Route::get('/', function () {
    return view('welcome');
});*/
// Route::group(['middleware'=>'web'], function(){

	Route::get('/', ['uses' => 'UserController@index', 'as' => '/']);
	Route::post('login', ['uses'=>'UserController@login', 'as'=>'login']);
	Route::get('/register', ['uses' => 'UserController@getRegister', 'as' => 'getRegister']);
	Route::post('/register', ['uses' => 'UserController@postRegister', 'as' => 'postRegister']);
	Route::get('/forget-password', ['uses' => 'UserController@forgetPassword', 'as' => 'forget-password']);
	Route::post('/forget-password', ['uses'=>'UserController@resetpassword', 'as'=>'resetpassword']);
	Route::get('change-password/{id}/{token}', ['uses'=>'UserController@changePassword', 'as'=>'changePassword']);
	Route::get('/verify-email/{id}/{password}/{token}', ['uses'=>'UserController@verifyEmail', 'as'=>'verifyEmail']);
	Route::post('/save-password', ['uses'=>'UserController@savePassword', 'as'=>'savePassword']);
	Route::get('logout', ['uses'=>'UserController@logout', 'as'=>'logout']);
	// Home Page Routes
	Route::get('/home', ['uses' => 'HomeController@index', 'as' => 'home']);
	Route::get('/profile', ['uses' => 'HomeController@getProfile', 'as' => 'profile']);
	Route::post('/profile', ['uses' => 'HomeController@postProfile', 'as' => 'profile']);
	Route::get('/prod&serv', ['uses' => 'HomeController@getProductAndServices', 'as' => 'prod&serv']);
	Route::get('/membership', ['uses' => 'HomeController@getMembership', 'as' => 'membership']);
	Route::post('/membership', ['uses' => 'HomeController@postMembership', 'as' => 'membership']);
	Route::get('/newmembership', ['uses' => 'HomeController@getNewMembership', 'as' => 'newmembership']);
	Route::get('/contact', ['uses' => 'HomeController@contact', 'as' => 'contact']);
	Route::post('/contact', ['uses' => 'HomeController@postContact', 'as' => 'contact']);
	Route::get('/setting', ['uses' => 'HomeController@setting', 'as' => 'setting']);
	Route::post('/setting', ['uses' => 'HomeController@postSetting', 'as' => 'setting']);
	Route::get('/cartdetails', ['uses' => 'HomeController@addtocart', 'as' => 'cartdetails']);
	Route::get('/removeCardItem/{id}', ['uses' => 'HomeController@removeCardItem', 'as' => 'removeCardItem']);

	Route::get('/fees', ['uses' => 'HomeController@fees', 'as' => 'fees']);
	Route::post('/updateFees', ['uses' => 'HomeController@postFess', 'as' => 'updateFees']);	

	Route::post('/registerMembership', ['uses' => 'HomeController@registerMembership', 'as' => 'registerMembership']);
	Route::post('/getSeatNumber', ['uses' => 'HomeController@getSeatNumber', 'as' => 'getSeatNumber']);
	Route::post('/getUserProfileRecord', ['uses' => 'HomeController@getUserProfileRecord', 'as' => 'getUserProfileRecord']);
	Route::post('/updateMembershipCategory', ['uses' => 'HomeController@updateMembershipCategory', 'as' => 'updateMembershipCategory']);
	Route::post('/uploadImage', ['uses' => 'HomeController@uploadImage', 'as' => 'uploadImage']);
	Route::get('/checkout', ['uses' => 'HomeController@checkout', 'as' => 'checkout']);

	Route::get('membserships', ['uses' => 'HomeController@membershipStatus', 'as' => 'membershipStatus']);
	Route::post('deLinkUserMembership', ['uses' => 'HomeController@deLinkUserMembership', 'as' => 'deLinkUserMembership']);
	// Payment routes
	Route::post('/payment_process', ['uses' => 'PaypalController@payment_process', 'as' => 'payment_process']);	

	Route::get('/payment', array('as' => 'payment','uses' => 'PaypalController@postPayment'));

	// this is after make the payment, PayPal redirect back to your site
 	Route::get('/payment/status', array('as' => 'payment.status','uses' => 'PaypalController@getPaymentStatus'));

 	Route::get('/executeAgreement/{id}', ['uses'=>'PaypalController@executeAgreement', 'as'=>'executeAgreement']);
 	Route::get('/getBillingAgreement', ['uses' =>'PaypalController@getBillingAgreement', 'as'=>'getBillingAgreement']);
 	Route::get('/checkMembershipEndDate', ['uses' =>'PaypalController@checkMembershipEndDate', 'as'=>'checkMembershipEndDate']); 
 	Route::get('/deActivate2017Members', ['uses' =>'PaypalController@deActivate2017Members', 'as'=>'deActivate2017Members']);

 	// stripe route
 	Route::post('stripe', 'PaypalController@postStripePayment');

 	// Facebook and google plus signin route
	Route::get ( '/redirect/{service}', 'UserController@social_redirect');
	Route::get ( '/callback/{service}', 'UserController@social_callback');

	// Custom error page details 
	Route::get('pagenotfound', ['uses' => 'UserController@pagenotfound', 'as' => 'pagenotfound']);

	Route::post('/getRowNumber', ['uses' => 'HomeController@getRowNumber', 'as' => 'getRowNumber']);
	// routes for excel import
	Route::get('importExport', 'HomeController@importExport');
	Route::post('importExcel', 'HomeController@importExcel');

	Route::get('/privacy', ['uses' => 'HomeController@privacy', 'as' => 'privacy']);
	Route::get('/termsAndcondition', ['uses' => 'HomeController@termsAndcondition', 'as' => 'termsAndcondition']);
	Route::get('/privacyAndpolicy', ['uses' => 'HomeController@privacyAndpolicy', 'as' => 'privacyAndpolicy']);

	Route::post('/editCartProfile', ['uses' => 'HomeController@editCartProfile', 'as' => 'editCartProfile']);
	Route::post('/saveCartProfile', ['uses' => 'HomeController@saveCartProfile', 'as' => 'saveCartProfile']);
	
	
	Route::get('/clear-cache', function() {
	  $exitCode = Artisan::call('cache:clear'); 
	  $exitCode = Artisan::call('config:clear'); 
	  $exitCode = Artisan::call('config:cache'); 
		// return what you want
	});	
// });


