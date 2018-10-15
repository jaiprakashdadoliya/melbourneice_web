<?php

namespace App\Http\Controllers;

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

ini_set('max_execution_time', 180); // 3 minutes

use Illuminate\Http\Request;

use App\Http\Requests;
use Config;
use Session;
use Redirect;
Use Mail;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;

use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;

use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
// use PayPal\Api\Plan;
use PayPal\Common\PayPalModel;

use PayPal\Api\Agreement;

use PayPal\Api\Payer;
// use PayPal\Api\Plan;
use PayPal\Api\ShippingAddress;

use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
// use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;

use Input, DB;
use App\Helpers\UserHelper;
use App\Models\CommonModel;
use App\User;

class PaypalController extends Controller
{
    private $_api_context;

    /**
    * Construct method
    * define paypal config
    */
	public function __construct()
 	{
		// setup PayPal api context
	 	$paypal_conf = Config::get('paypal');
	 	$this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
	 	$this->_api_context->setConfig($paypal_conf['settings']);
	 	$this->commonModel = new CommonModel();
 	}

 	/**
 	* payment process method
 	* @param Request paypal, stripe, payway
 	* redirect to payment method
 	*/
 	public function payment_process(Request $request)
 	{
 		if(UserHelper::user_session_data('is_user_logged_in')){ 
 			$getData = UserHelper::checkout_session_data();
 			// p($getData); die;
 			if(!empty($getData)){
				Session::forget('user_comment');
 				if(!empty($request->comment)) {
 					$comment = $request->comment;
	 				Session::put('user_comment', $comment); 
 				} else {
 					Session::put('user_comment', "null"); 
 				}
	 			$payment = $request->payment;
	 			$setCycle = $request->setCycle;  // weekly or monthly
	 			$setFrequency = $request->setFrequency; // 1 to 6

	 			Session::forget('setFrequency');
	 			Session::forget('setCycle');
	 			Session::put('setFrequency', $setFrequency);
	 			Session::put('setCycle', $setCycle);
	 			
 			} else {
	 			return redirect('home')->with('message', trans('messages.server_error'));
	 		}		
	 		
	 		if($payment == "paypal"){
	 			return redirect('/payment');
	 		}
	 	} else {
	 		return redirect('/')->with('message', trans('messages.alert_session_expired'));
	 	}
 	}

 	/**
 	* postpaypal method
 	* payment process and redirect to postPayment method
 	*/
 	public function postPayment()
 	{
 		if(UserHelper::user_session_data('is_user_logged_in')){
 			$getDatas = UserHelper::checkout_session_data(); 
	 		$sessionData = UserHelper::user_session_data();

	 		$getFrequency =Session::get('setFrequency'); 
 			$getCycle = Session::get('setCycle'); // weekly or monthly 

 			if($getFrequency == 'full' || ($getFrequency == 1 && $getCycle == 'Month'))
 			{
 				// pay-pal process start from here
				$payer = new Payer();
			 	$payer->setPaymentMethod('paypal');
		 		if(!empty($getDatas)){
		 			$result = array();
	 				$count=0;
	 				$totalAmout = 0;
			 		foreach ($getDatas as $getData) {
			 			// (1 = RS - Adult, 2 = RS - Concession, 4 = GA - Concession, 3 = GA - Adult)
			 			$setName = UserHelper::getMembershipTypes($getData['membershipType']);

				 		$paidFees[] = $getData['paidFees'];
			 			$membershipFees[] = $getData['membershipFees'];	
			 			$totalPaidFess =  $getData['paidFees'] + $getData['membershipFees'];	
						
					  	$obj = new Item();
					  	$obj->setName($setName) // item name
						        ->setCurrency('AUD')
						        ->setQuantity(1)
						        ->setPrice($totalPaidFess); 
					  	$result[] = $obj;

						$totalAmout++;
					 	$count++;
			 			
		 			}	

		 			$totalPaidFess = array_sum($paidFees) + array_sum($membershipFees); // total paid fees per user
		 		
			 		Session::forget('totalAmount');
			 		Session::put('totalAmount', $totalPaidFess);

			 		// add item to list
				 	$item_list = new ItemList();
				 	$item_list->setItems($result);

					$amount = new Amount();
				 	$amount->setCurrency('AUD')
					 		->setTotal($totalPaidFess);

					$transaction = new Transaction();
				 	$transaction->setAmount($amount)
				 		->setItemList($item_list)
				 		->setDescription('Melbourne Ice Community');

					$redirect_urls = new RedirectUrls();
				 	$redirect_urls->setReturnUrl(route('payment.status')) // Specify return URL
				 					->setCancelUrl(route('payment.status'));

					$payment = new Payment();
				 	$payment->setIntent('Sale')
							 ->setPayer($payer)
							 ->setRedirectUrls($redirect_urls)
							 ->setTransactions(array($transaction));

					try {
					 	$payment->create($this->_api_context);
				 	} catch (\PayPal\Exception\PPConnectionException $ex) {
				 		if (\Config::get('app.debug')) {
					 		echo "Exception: " . $ex->getMessage() . PHP_EOL;
					 		$err_data = json_decode($ex->getData(), true);		 		
					 		exit;
					 	} else {
					 		die('Some error occur, sorry for inconvenient');
				 		}
				 	}

					foreach($payment->getLinks() as $link) {
				 		if($link->getRel() == 'approval_url') {
				 			$redirect_url = $link->getHref();
				 			break;
				 		}
			 		}

					// add payment ID to session
				 	Session::put('paypal_payment_id', $payment->getId());

					if(isset($redirect_url)) {
				 		// redirect to paypal
						return redirect($redirect_url);
					}

		 		} else {
		 			return redirect('home')->with('message', trans('messages.server_error'));
		 		}
 			} else {
 				// pay-pal process start from here
		 		if(!empty($getDatas)){
		 			$result = array();
	 				$count=0;
	 				$totalAmout = 0;
	 				$paidFees = array();
	 				$membershipFees = array();
			 		foreach ($getDatas as $getData) {	
			 			$paidFees[] = $getData['paidFees'];
			 			$membershipFees[] = $getData['membershipFees'];		
						$totalAmout++;
					 	$count++;
			 			
		 			}	
		 		} else {
		 			return redirect('home')->with('message', trans('messages.server_error'));
		 		}	
		 		
		 		$totalPaidFess = array_sum($paidFees) + array_sum($membershipFees); // total paid fees per user
		 		
		 		Session::forget('totalAmount');
		 		Session::put('totalAmount', $totalPaidFess);

 				// manage subscription for monthly and weekly payment
 						
	   			if($getCycle == "Week"){
	   				$start_date = date("m/d/Y");	   				
	   				$end_date = date("m/d/Y", strtotime("+".$getFrequency." month"));	   				
					$setFinalCycle =  week_between_two_dates(($start_date), ($end_date), '');				
					// $setFinalCycle = 3;
					$finalFrequency = "Week";
					$getFrequency = 1;

					Session::forget('finalCycleCal');
	   				Session::set('finalCycleCal', $setFinalCycle);

					Session::forget('setFinalFrequency');
	   				Session::set('setFinalFrequency', 'Weekly');
					// $days =  week_between_two_dates(($start_date), ($end_date), 'days');
	   			} else if ($getCycle == "Month") {
	   				$setFinalCycle = $getFrequency;
	   				// $setFinalCycle = 3;
	   				$getFrequency = 1;
	   				$finalFrequency = "Month";

	   				Session::forget('finalCycleCal');
	   				Session::set('finalCycleCal', $setFinalCycle);

	   				Session::forget('setFinalFrequency');
	   				Session::set('setFinalFrequency', 'Monthly');

	   			} else if($getCycle == "Fortnight"){
	   				$start_date = date("m/d/Y");
	   				$end_date = date("m/d/Y", strtotime("+".$getFrequency." month"));
	   				$setFinalCycle =  week_between_two_dates(($start_date), ($end_date), 'Fortnight');
	   				// $setFinalCycle = 3;
	   				$getFrequency = 2;
	   				$finalFrequency = "Week";

	   				Session::forget('finalCycleCal');
	   				Session::set('finalCycleCal', $setFinalCycle);

	   				Session::forget('setFinalFrequency');
	   				Session::set('setFinalFrequency', 'Fortnightly');
	   			}
	   			
	 			// calculate amount 
	 			$Emi = $totalPaidFess/$setFinalCycle;
	 			$monthlyEmi = round($Emi, 2);
	 			$setupFess = $monthlyEmi; 
	 			$totalPay = $totalPaidFess - $monthlyEmi;
	 			Session::forget('totalPay');
		 		Session::put('totalPay', $totalPay);
		 		Session::forget('setupFess');
		 		Session::put('setupFess', $setupFess);
				// ### Create Plan

				// Start Create a new instance of Plan object
				$plan = new Plan();
				// Fill up the basic information that is required for the plan
				$plan->setName('Melbourne Ice Community Portal')
				    ->setDescription('Melbourne Ice Community Membership Plan')
				    ->setType('fixed');  // FIXED, INFINITE
				// # Payment definitions for this billing plan.
				$paymentDefinition = new PaymentDefinition();
				
				// You should be able to see the acceptable values in the comments.
				$paymentDefinition->setName('Regular Payments')
				    ->setType('REGULAR')
				    ->setFrequency($finalFrequency)
				    ->setFrequencyInterval($getFrequency)
				    ->setCycles($setFinalCycle -1)
				    ->setAmount(new Currency(array('value' => $monthlyEmi, 'currency' => 'AUD')));

				$merchantPreferences = new MerchantPreferences();			
				// This will keep your plan compatible with both the possible scenarios on how it is being used in agreement.
				$merchantPreferences->setReturnUrl(url('executeAgreement/1'))
				    ->setCancelUrl(url('/executeAgreement/2'))
				    ->setAutoBillAmount("yes")
				    ->setInitialFailAmountAction("CONTINUE")
				    ->setMaxFailAttempts("0")
				    ->setSetupFee(new Currency(array('value' => $setupFess, 'currency' => 'AUD')));
				$plan->setPaymentDefinitions(array($paymentDefinition));
				$plan->setMerchantPreferences($merchantPreferences);
					
				try {
				    $output = $plan->create($this->_api_context);
				} catch (Exception $ex) {
				    // p("Created Plan", "Plan", null, $ex);
				    return redirect('home')->with('message', 'Something wrong while creating the plan.');
				    exit(1);
				}
			 	// p("Created Plan", "Plan", $output->getId(), $output); die;
				$planId = $output->getId();
				// return $output;	
				// End Create Plan

				// Start Update plan
			 	try {
				    $patch = new Patch();
				    $value = new PayPalModel('{
					       "state":"ACTIVE"
					     }');
				    $patch->setOp('replace')
				        ->setPath('/')
				        ->setValue($value);
				    $patchRequest = new PatchRequest();
				    $patchRequest->addPatch($patch);

				    $plan->update($patchRequest, $this->_api_context);
				    $plan = Plan::get($planId, $this->_api_context);
				} catch (Exception $ex) {
				    // p("Updated the Plan to Active State", "Plan", null, $patchRequest, $ex);
				    return redirect('home')->with('message', 'Something wrong while updating the plan to active state.');
				    exit(1);
				}
				// p("Updated the Plan to Active State", "Plan", $plan->getId(), $patchRequest, $plan);
				// return $plan;
				$updatedPlanId = $plan->getId();
				// End Update plan

				// Start Create Billing Agreement With PayPal
	   			$time = time() + 30 * (1 * 3600);
	   			$startTime = date('Y-m-d\\TH:i:s\\Z', $time);
				
	   			if($getCycle == "Week"){
	   				$startTime = date("Y-m-d\\TH:i:s\\Z", strtotime("+1".$getCycle));
	   			} else if ($getCycle == "Month") {
	   				$startTime = date("Y-m-d\\TH:i:s\\Z", strtotime("+1".$getCycle));
	   			} else if ($getCycle == "Fortnight") {
	   				$startTime = date("Y-m-d\\TH:i:s\\Z", strtotime("+2 Week"));
	   			} else {
	   				// set to NA
	   			}    	

				$agreement = new Agreement();
				$agreement->setName('Melbourne Ice Community Portal Agreement')
				    ->setDescription('Melbourne Ice Community Portal Agreement')
				    ->setStartDate($startTime);  // '2019-06-17T9:45:04Z'
				// Add Plan ID
				// Please note that the plan Id should be only set in this case.
				
				$plan = new Plan();
				$plan->setId($updatedPlanId);
				$agreement->setPlan($plan);
				// Add Payer
				$payer = new Payer();
				$payer->setPaymentMethod('paypal');
				$agreement->setPayer($payer);

				// $request = clone $agreement;
				// ### Create Agreement
				try {
				    // Please note that as the agreement has not yet activated, we wont be receiving the ID just yet.
				    $agreement = $agreement->create($this->_api_context);
				    // the buyer to. Retrieve the url from the $agreement->getApprovalLink()
				    // method
				    $approvalUrl = $agreement->getApprovalLink();
				    // return redirect($approvalUrl);
				} catch (Exception $ex) {
				    // p("Created Billing Agreement.", "Agreement", null, $ex);
				    return redirect('home')->with('message', 'Something wrong while creating billing agreement.');
				    exit(1);
				}
				// return $agreement;
				return redirect($approvalUrl);
				// End Create Billing Agreement With PayPal
 			}

 		} else {
 			return redirect('/')->with('message', trans('messages.alert_session_expired'));
 		}		
	}

	/**
	* Exceute billing agreemnet
	* @param Id
	* get agreement id and store data into database
	*/
	public function executeAgreement($id)
	{	
		if(UserHelper::user_session_data('is_user_logged_in')){ 
			$sessionData = UserHelper::user_session_data('user_id');  
			$getSessionParams = UserHelper::checkout_session_data();

			// if id = 1 => success, else id = 2 => failure
			if (isset($id) && $id == '1') {
			    $token = $_GET['token'];
			    if(empty($token)){
			 		return redirect()->route('home')->with('info', 'Payment failed');
			 	}

			    $agreement = new \PayPal\Api\Agreement();
			    try {
			        // ## Execute Agreement
			        // Execute the agreement by passing in the token
			        $agreement->execute($token, $this->_api_context);
			    } catch (Exception $ex) {		        
			        // p("Executed an Agreement", "Agreement", $agreement->getId(), $_GET['token'], $ex);
			        return redirect('home')->with('message', 'Something wrong while exceute an agreement.');
			        exit(1);
			    }
			    
			    // ## Get Agreement
			    // Make a get call to retrieve the executed agreement details
			    try {
			        $agreement = \PayPal\Api\Agreement::get($agreement->getId(), $this->_api_context);
			    } catch (Exception $ex) {
			        // p("Get Agreement", "Agreement", null, null, $ex);
			        return redirect('home')->with('message', 'Something wrong while get an agreement.');
			        exit(1);
			    }
			    // return $agreement;
			    $agreementId = $agreement->getId();  // agreement id
			    $agreementState = $agreement->state; // agreement state
			    $payer = $agreement->payer; 
			    $payerStatus = $payer->status; // get payer status

			    $paymentStartDate = $agreement->start_date; // get agreement start date
			    $agreement_details = $agreement->agreement_details; // get payer status
			    // $last_payment_amount = Session::get('setupFess');
			    // $last_payment_amount = $agreement_details->last_payment_amount;
			    // $lastPayValue = $last_payment_amount->value;
			    // $lastPayValue = $last_payment_amount;
			    // $amountDue = Session::get('totalPay');	
			    $cycles_remaining = $agreement_details->cycles_remaining;
			    $total_cycles_remaining = (int)$cycles_remaining + 1;
			    // $last_payment_date = $agreement_details->last_payment_date;
			    $next_billing_date = $agreement_details->next_billing_date;
			    $final_payment_date = $agreement_details->final_payment_date;
			    $checkFrequency =Session::get('setCycle');
			    $getFinalFrequency = Session::get('setFinalFrequency');
			    if($checkFrequency == "Fortnight"){
			    	$getFrequency = Session::get('setFinalFrequency');
			    } else {
			    	$getFrequency = Session::get('setFinalFrequency');
			    }

			    $lastPaymentDate = date('d-M-Y');
			    $nextBillingDate = date('d-M-Y', strtotime($next_billing_date));
			    $finalPaymentDate = date('d-M-Y', strtotime($final_payment_date));		    	
			    $owner_html = '';
			    $counter = 1;
			    $all_user_name = '';
			    $final_total_amount = '';
			    $getfinalCycleCal = Session::get('finalCycleCal');
				foreach ($getSessionParams as $getSessionParam) {
					// fees calculation as per user memberships
					$totalFessPerUser = $getSessionParam['membershipFees'] + $getSessionParam['paidFees'];
					$getFirstEmis = $totalFessPerUser/$getfinalCycleCal; // 300/3
					$lastPayValue = round($getFirstEmis, 2); // 1st payment
					$amountDues = $totalFessPerUser - $getFirstEmis; // reamaing amount
			    	$amountDue = round($amountDues, 2);

					// (1 = RS - Adult, 2 = RS - Concession, 4 = GA - Concession, 3 = GA - Adult)
					$memType = UserHelper::getMembershipTypes($getSessionParam['membershipType']);
			 		$dateofBirth = UserHelper::changeDateFormat($getSessionParam['dateofBirth']);
					if($getSessionParam['select_membership'] == "New"){
	                    $userName = ucfirst($getSessionParam['firstName'].' '.$getSessionParam['lastName']);
	                    // $rand_string = UserHelper::quickRandom(9);
	                    // create user
        				// $insertedId = $this->commonModel->addRecord(USER, array('userName' => $userName, 'email' => $getSessionParam['profileEmail'], 'password' => bcrypt($rand_string), 'emailConfirmed' => 1));
	                    $memberNumber = UserHelper::generateMemberNo($getSessionParam['prefix']);
						// get profile data from session
						$profileData = array(
		                    'memberNumber'  => $memberNumber,
		                    'firstName' =>  $getSessionParam['firstName'],
		                    'lastName'  =>  $getSessionParam['lastName'],
		                    'contactNumber' => $getSessionParam['contactNumber'],
		                    'dateofBirth'   =>  $dateofBirth,
		                    'state' =>  $getSessionParam['state'],
		                    'suburb' => $getSessionParam['suburb'],
		                    'country' => $getSessionParam['country'],
		                    'postCode'  =>  $getSessionParam['postCode'],
		                    'profession' => $getSessionParam['profession'],
		                    'yearFirstJoined' => $getSessionParam['yearFirstJoined'],
		                    'addressLine1'  =>  $getSessionParam['addressLine1'],
		                    'addressLine2'  =>  $getSessionParam['addressLine2'],
		                    'membershipBuyerId' => $getSessionParam['membershipBuyerId'],
		                    'profileEmail' => $getSessionParam['profileEmail'],
		                    'idAttachment' => $getSessionParam['idAttachment'],
		                    'paymentStatus' => 1
		                );
		                $insertId = $this->commonModel->addRecord(PROFILE, $profileData);

		                // Email verification token
			        	/*$token = getToken(50);
			        	User::where('id', $insertedId)->update(array('securityStamp'=>$token));
			        	$email = $getSessionParam['profileEmail'];
			        	$link = WEB_URL.EMAIL_VERIFICATION_FRONT_SLUG.$insertedId.'/'.base64_encode($rand_string).'/'.$token;
			        	$html = sprintf(EMAIL_VERIFICATION_MESSAGE_WITH_PASSWORD, $userName, $memberNumber, $rand_string, $link);
			        	// send email
			            try{
			                Mail::send(array(), array(), function ($message) use ($email, $html) {
			                $message->to($email)
			                    ->subject(EMAIL_VERIFICATION_SUBJECT)
			                    ->from(FROM_EMAIL)
			                    ->setBody($html, 'text/html');
			                });
			            } 
			            catch(\Exception $e){
			                return redirect('register')->with('message', trans('messages.unable_to_send_email'));
			            }*/
		                $getUserRecord = DB::table(PROFILE)->select('userId')->where(array('id' => $insertId))->get();
		                if(!empty($getUserRecord)){
			                if($getUserRecord[0]->userId == $sessionData['user_id']) {
			                	DB::table(USER)->where('id', $getUserRecord[0]->userId)->update(array('userName' => $userName));
			                }
			            }			            		            

		                if($getSessionParam['idAttachment']){
		                    // image upload email 
		                    $email = ADMIN_EMAIL;
		                    $html = sprintf(IMAGE_UPLOAD_MESSAGE, $userName);                   
		                    try{
		                        Mail::send(array(), array(), function ($message) use ($email, $html) {
		                        $message->to($email)
		                            ->subject(IMAGE_UPLOAD_SUBJECT)
		                            ->from(FROM_EMAIL)
		                            ->setBody($html, 'text/html');
		                        });
		                    } 
		                    catch(\Exception $e){
		                        return redirect('home')->with('message', trans('messages.unable_to_send_email'));
		                    }
		                }

		                // p($getSessionParam); die;	                
		                if($getSessionParam['membershipType']==1 || $getSessionParam['membershipType']==2 || $getSessionParam['membershipType']==7){
		                	// send Email  
		                	$Bay =  $getSessionParam['Bay'];
		                	$Row = $getSessionParam['Row'];
			            	if($Bay == 1){
			            		$BayType = "Bay 1";
			            	} else if ($Bay == 2) {
			            		$BayType = "Bay 2";
			            	} else if ($Bay == 3) {
			            		$BayType = "Bay 3";
			            	} else if ($Bay == 4) {
			            		$BayType = "Bay 4";
			            	} else if ($Bay == 5) {
			            		$BayType = "Bay 5";
			            	} else {
			            		$BayType = "NA";
			            	}

			            	if($Row){
			            		$RowType = $getSessionParam['Row'];
			            	} else {
			            		$RowType = "NA";
			            	}

			            	$user_name = UserHelper::get_name($insertId);
		                	$email = UserHelper::get_email($insertId);
		                    $seatNumber = $getSessionParam['seatNumber'];

		                    // $single_user_name = $memType.' - '.$user_name.'<br>';
		                    $single_user_name = $memType.' - '.$user_name.'<br><p style="margin: 0px;"><b>'.$BayType.'</b>, Row <b>'.$RowType.'</b>, Seat No. <b>'.$seatNumber.'</b></p>';

					        $html = sprintf(MEMBERSHIP_MSG_RS, $user_name, $single_user_name, $amountDue, $total_cycles_remaining, $cycles_remaining, $getFrequency, $lastPayValue, $lastPaymentDate, $nextBillingDate, $finalPaymentDate);
					        			        			        
					        try{
					            Mail::send(array(), array(), function ($message) use ($email, $html) {
					            $message->to($email)
					                ->subject(MEMBERSHIP_SUBJECT_RS)
					                ->from(FROM_EMAIL)
					                ->bcc(ADMIN_EMAIL)	
					                ->setBody($html, 'text/html');
					            });

					            //Sending email to owner
					            $get_profile_name = UserHelper::get_profile_name();
					            // $all_user_name .= $counter.'. '.$memType.' - '.$user_name.'<br>';
					            $all_user_name .= $counter.'. '.$memType.' - '.$user_name.'<br><p style="margin: 0px; padding-left: 15px;"><b>'.$BayType.'</b>, Row <b>'.$RowType.'</b>, Seat No. <b>'.$seatNumber.'</b></p><br>';

					        	// $owner_html .= sprintf(MEMBERSHIP_MSG_RS_OWNER, $get_profile_name, $user_name, $memberNumber, $memType, $BayType, $RowType, $seatNumber, $amountDue, $total_cycles_remaining, $cycles_remaining, $getFrequency, $lastPayValue, $lastPaymentDate, $nextBillingDate, $finalPaymentDate);	

					            /*$email = UserHelper::get_profile_email();
					            Mail::send(array(), array(), function ($message) use ($email, $html) {
					            $message->to($email)
					                ->subject(MEMBERSHIP_SUBJECT_RS_OWNER)
					                ->from(FROM_EMAIL)
					                ->setBody($html, 'text/html');
					            });*/
					           // return redirect('home')->with('success', trans('messages.contact_us_mail'));
					        } 
					        catch(\Exception $e){
					            return redirect('home')->with('message', trans('messages.unable_to_send_email'));
					        }	

		                } else {
		                	$user_name = UserHelper::get_name($insertId);
		                	$email = UserHelper::get_email($insertId);

		                	$single_user_name = $memType.' - '.$user_name.'<br>';
					        $html = sprintf(MEMBERSHIP_MSG_GA, $user_name, $single_user_name, $amountDue, $total_cycles_remaining, $cycles_remaining, $getFrequency, $lastPayValue, $lastPaymentDate, $nextBillingDate, $finalPaymentDate);
	        
					        try{
					            Mail::send(array(), array(), function ($message) use ($email, $html) {
					            $message->to($email)
					                ->subject(MEMBERSHIP_SUBJECT_GA)
					                ->from(FROM_EMAIL)
					                ->bcc(ADMIN_EMAIL)	
					                ->setBody($html, 'text/html');
					            });

					             //Sending email to owner
					            $email = UserHelper::get_profile_email();
					            $get_profile_name = UserHelper::get_profile_name();
					            $all_user_name .= $counter.'. '.$memType.' - '.$user_name.'<br>';
					        	// $owner_html .= sprintf(MEMBERSHIP_MSG_GA_OWNER, $get_profile_name, $user_name, $memType, $amountDue, $total_cycles_remaining, $cycles_remaining, $getFrequency, $lastPayValue, $lastPaymentDate, $nextBillingDate, $finalPaymentDate);

					            /*Mail::send(array(), array(), function ($message) use ($email, $html) {
					            $message->to($email)
					                ->subject(MEMBERSHIP_SUBJECT_GA_OWNER)
					                ->from(FROM_EMAIL)
					                ->setBody($html, 'text/html');
					            });*/

					           // return redirect('home')->with('success', trans('messages.contact_us_mail'));
					        } 
					        catch(\Exception $e){
					        	// forget session key
		            			// Session::forget('checkout_session_data');
					            return redirect('home')->with('message', trans('messages.unable_to_send_email'));
					        }
		                }  


		                // (1 = RS - Adult, 2 = RS - Concession, 4 = GA - Concession, 3 = GA - Adult)
				        $user_comment = Session::get('user_comment');
				        if($user_comment == "null"){
				        	$userComment = "";
				        } else {
				        	$userComment = $user_comment;
				        }	                	
	                	// add new membership
	                	$membershipData = array(
				                            'membershipType' => $getSessionParam['membershipType'],
				                            'startDate' => $getSessionParam['startDate'],
				                            'endDate' => $getSessionParam['endDate'],
				                            'membershipYear' => $getSessionParam['membershipYear'],
				                           	'profileId' => $insertId,
				                           	'agreementId' => $agreementId,
				                            'privacyAcknowledged' => $getSessionParam['privacyAcknowledged'],
				                            'membershipStatus' => 'Active',
				                            'comment' => $userComment
				                        );	

		            	$memberShipId = $this->commonModel->addRecord(MEMBERSHIP, $membershipData);

		                if($getSessionParam['membershipType']==1 || $getSessionParam['membershipType']==2 || $getSessionParam['membershipType']==7){
		                    //  seat allocation data
		                    $seatAlocationData = array(
		                                    'Bay'   =>  $getSessionParam['Bay'],
		                                    'Row'   =>  $getSessionParam['Row'],
		                                    'seatNumber' => $getSessionParam['seatNumber'],
		                                    'memberShipId' => $memberShipId,
		                                    'reserved' => 1,
		                                    'createdOn' =>  $getSessionParam['createdOn'],
		                                    'changedOn' =>  $getSessionParam['changedOn'],
		                                    'createdBy' => $getSessionParam['createdBy'],
		                                    'deleted' => $getSessionParam['deleted']
		                                );
		                    // check for inactive seats
	            			$checkForInactiveSeats = DB::table(SEAT_ALLOCATION)->where(array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'reserved' => 0, 'deleted' => 0))->count();
	            			if($checkForInactiveSeats){
	            				// update record
	            				$this->commonModel->updateRecords(SEAT_ALLOCATION, array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'memberShipId' =>$memberShipId, 'reserved' => 1, 'createdOn' =>  $getSessionParam['createdOn'], 'changedOn' =>  $getSessionParam['changedOn'],'createdBy' => $getSessionParam['createdBy'], 'deleted' => $getSessionParam['deleted']), array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'reserved' => 0, 'deleted' => 0));
	            			} else {
			                	// Seat Allocation
			                    $seatAllocationId = $this->commonModel->addRecord(SEAT_ALLOCATION, $seatAlocationData);
			                }
		                } 

		                if(empty($getSessionParam['Bay']) && empty($getSessionParam['Row']) && empty($getSessionParam['seatNumber'])){
		                	$BayNo = "";
		                	$RowNo = "";
		                	$seatNo = "";
		                } else {
		                	$BayNo = $getSessionParam['Bay'];
		                	$RowNo = $getSessionParam['Row'];
		                	$seatNo = $getSessionParam['seatNumber'];
		                }
		                // insert record into membershipHistory table
                        DB::table(HISTORY)->insertGetId(array('profileId' => $insertId, 'membershipId' => $memberShipId, 'membershipType' => $getSessionParam['membershipType'], 'bay' => $BayNo, 'row' => $RowNo, 'seatNumber' => $seatNo, 'membershipBuyerId' => $getSessionParam['membershipBuyerId'], 'yearFirstJoined' => $getSessionParam['yearFirstJoined'], 'createdDate' => date('Y-m-d H:i:s')));

		                // $this->commonModel->updateRecords(PROFILE, array('paymentStatus' => 1), array('id' => $insertId));

					} else {
						// Renew memebsership logic
						// update profileEmail
						$userName = ucfirst($getSessionParam['firstName'].' '.$getSessionParam['lastName']);
						$dateofBirth = UserHelper::changeDateFormat($getSessionParam['dateofBirth']);
						if(empty($getSessionParam['idAttachment'])){
							$this->commonModel->updateRecords(PROFILE, array('firstName' =>  $getSessionParam['firstName'], 'lastName'  =>  $getSessionParam['lastName'], 'profileEmail' => $getSessionParam['profileEmail'], 'dateofBirth' => $dateofBirth, 'contactNumber' => $getSessionParam['contactNumber'], 'state' =>  $getSessionParam['state'], 'suburb' => $getSessionParam['suburb'], 'country' => $getSessionParam['country'], 'postCode'  =>  $getSessionParam['postCode'], 'addressLine1'  =>  $getSessionParam['addressLine1'], 'addressLine2'  =>  $getSessionParam['addressLine2'], 'membershipBuyerId' => $getSessionParam['membershipBuyerId'], 'yearFirstJoined' => $getSessionParam['yearFirstJoined'], 'profession' => $getSessionParam['profession']), array('id' => $getSessionParam['profileId']));
						} else {
							$this->commonModel->updateRecords(PROFILE, array('firstName' =>  $getSessionParam['firstName'], 'lastName'  =>  $getSessionParam['lastName'], 'profileEmail' => $getSessionParam['profileEmail'], 'dateofBirth' => $dateofBirth, 'contactNumber' => $getSessionParam['contactNumber'], 'state' =>  $getSessionParam['state'], 'suburb' => $getSessionParam['suburb'], 'country' => $getSessionParam['country'], 'postCode'  =>  $getSessionParam['postCode'], 'addressLine1'  =>  $getSessionParam['addressLine1'], 'addressLine2'  =>  $getSessionParam['addressLine2'], 'idAttachment' => $getSessionParam['idAttachment'], 'membershipBuyerId' => $getSessionParam['membershipBuyerId'], 'yearFirstJoined' => $getSessionParam['yearFirstJoined'], 'profession' => $getSessionParam['profession']), array('id' => $getSessionParam['profileId']));
	                        // image upload email 
			                if(!empty($getSessionParam['idAttachment'])){
		                        $email = ADMIN_EMAIL;
		                        $html = sprintf(IMAGE_UPLOAD_MESSAGE, $userName);                   
		                        try{
		                            Mail::send(array(), array(), function ($message) use ($email, $html) {
		                            $message->to($email)
		                                ->subject(IMAGE_UPLOAD_SUBJECT)
		                                ->from(FROM_EMAIL)
		                                ->bcc(ADMIN_EMAIL)
		                                ->setBody($html, 'text/html');
		                            });
		                        } 
		                        catch(\Exception $e){
		                            return redirect('home')->with('message', trans('messages.unable_to_send_email'));
		                        }
		                    }		                    
						}

						$getUserRecord = DB::table(PROFILE)->select('userId', 'memberNumber')->where(array('id' => $getSessionParam['profileId']))->get();

						if(!empty($getUserRecord)){
							$getuserId = $getUserRecord[0]->userId;
							$isExistMIM = $getUserRecord[0]->memberNumber;
							Session::forget('getuserId', $getuserId);
							Session::put('getuserId', $getuserId);
		                	if($getUserRecord[0]->userId == $sessionData['user_id']) {
		                		if(empty($isExistMIM)){
		                			// $getFirstThree = substr($getUserRecord[0]->memberNumber,0, 3);
			                		$memberNumber = UserHelper::generateMemberNo('MIM');
		                			DB::table(PROFILE)->where('id', $getSessionParam['profileId'])->update(array('memberNumber' => $memberNumber, 'paymentStatus' => 1));
		                		}
		                		DB::table(USER)->where('id', $getUserRecord[0]->userId)->update(array('userName' => $userName));
		                	}
		                }

						// (1 = RS - Adult, 2 = RS - Concession, 4 = GA - Concession, 3 = GA - Adult)
						$user_comment = Session::get('user_comment');
				        if($user_comment == "null"){
				        	$userComment = "";
				        } else {
				        	$userComment = $user_comment;
				        }

	                    $checkRecordIsExist = DB::table(MEMBERSHIP)->where(array('profileId' => $getSessionParam['profileId']))->get();
	                    // $checkRecordIsExist = DB::table(MEMBERSHIP)->where(array('profileId' => $getSessionParam['profileId']))->whereYear('membershipYear', '=', $getSessionParam['membershipYear'])->get();

						if(!empty($checkRecordIsExist)){
							
						 	$this->commonModel->updateRecords(MEMBERSHIP, array('membershipType' => $getSessionParam['membershipType'], 'membershipYear' => $getSessionParam['membershipYear'],'comment' => $userComment, 'agreementId' => $agreementId, 'membershipStatus' => 'Active','startDate' => $getSessionParam['startDate'], 'endDate' => $getSessionParam['endDate']), array('profileId' => $getSessionParam['profileId']));

						 	if($getSessionParam['membershipType']==1 || $getSessionParam['membershipType']==2 || $getSessionParam['membershipType']==7){

						 		$checkMemberIsExist = DB::table(SEAT_ALLOCATION)->where(array('memberShipId' => $getSessionParam['memberShipId']))->count();

						 		if($checkMemberIsExist){
						 			// Seat Allocation update
			                		$this->commonModel->updateRecords(SEAT_ALLOCATION, array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'reserved' => 1, 'createdOn' =>  $getSessionParam['createdOn'], 'changedOn' =>  $getSessionParam['changedOn'],'createdBy' => $getSessionParam['createdBy'], 'deleted' => $getSessionParam['deleted']), array('memberShipId' => $getSessionParam['memberShipId']));
						 		} else {
						 			$this->commonModel->addRecord(SEAT_ALLOCATION, array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'memberShipId' => $getSessionParam['memberShipId'], 'reserved' => 1, 'createdOn' =>  $getSessionParam['createdOn'], 'changedOn' =>  $getSessionParam['changedOn'],'createdBy' => $getSessionParam['createdBy'], 'deleted' => $getSessionParam['deleted']));
						 		}			                	
			                }

						} else {
							// add new membership
	                		$membershipData = array(
				                            'membershipType' => $getSessionParam['membershipType'],
				                            'startDate' => $getSessionParam['startDate'],
				                            'endDate' => $getSessionParam['endDate'],
				                            'membershipYear' => $getSessionParam['membershipYear'],
				                           	'profileId' => $getSessionParam['profileId'],
				                           	'agreementId' => $agreementId,
				                            'privacyAcknowledged' => $getSessionParam['privacyAcknowledged'],
				                            'membershipStatus' => 'Active',
				                            'comment' => $userComment
				                        );	

		            		$memberShipId = $this->commonModel->addRecord(MEMBERSHIP, $membershipData);

		            		if($getSessionParam['membershipType']==1 || $getSessionParam['membershipType']==2 || $getSessionParam['membershipType']==7){
		            			// check for inactive seats
		            			$checkForInactiveSeats = DB::table(SEAT_ALLOCATION)->where(array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'reserved' => 0, 'deleted' => 0))->count();

		            			if($checkForInactiveSeats){
		            				// update record
		            				$this->commonModel->updateRecords(SEAT_ALLOCATION, array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'memberShipId' =>$memberShipId, 'reserved' => 1, 'createdOn' =>  $getSessionParam['createdOn'], 'changedOn' =>  $getSessionParam['changedOn'],'createdBy' => $getSessionParam['createdBy'], 'deleted' => $getSessionParam['deleted']), array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'reserved' => 0, 'deleted' => 0));
		            			} else {
				                	// Seat Allocation add
				                	$this->commonModel->addRecord(SEAT_ALLOCATION, array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'memberShipId' => $memberShipId, 'reserved' => 1, 'createdOn' =>  $getSessionParam['createdOn'], 'changedOn' =>  $getSessionParam['changedOn'],'createdBy' => $getSessionParam['createdBy'], 'deleted' => $getSessionParam['deleted']));
				                }
			                }

						}


	                	if($getSessionParam['membershipType']==1 || $getSessionParam['membershipType']==2 || $getSessionParam['membershipType']==7){
		                	// send Email    
		                	$Bay =  $getSessionParam['Bay'];
		                	$Row =  $getSessionParam['Row'];
			            	if($Bay == 1){
			            		$BayType = "Bay 1";
			            	} else if ($Bay == 2) {
			            		$BayType = "Bay 2";
			            	} else if ($Bay == 3) {
			            		$BayType = "Bay 3";
			            	} else if ($Bay == 4) {
			            		$BayType = "Bay 4";
			            	} else if ($Bay == 5) {
			            		$BayType = "Bay 5";
			            	} else {
			            		$BayType = "NA";
			            	}  

			            	if($Row){
			            		$RowType = $getSessionParam['Row'];
			            	} else {
			            		$RowType = "NA";
			            	}

		                	$user_name = UserHelper::get_name($getSessionParam['profileId']);
		                    $seatNumber = $getSessionParam['seatNumber'];
		                    // $memberNumber = $getSessionParam['memberNumber'];
	                		$email = UserHelper::get_email($getSessionParam['profileId']);

	                		// $single_user_name = $memType.' - '.$user_name.'<br>';
	                		$single_user_name = $memType.' - '.$user_name.'<br><p style="margin: 0px;"><b>'.$BayType.'</b>, Row <b>'.$RowType.'</b>, Seat No. <b>'.$seatNumber.'</b></p>';

					        $html = sprintf(MEMBERSHIP_MSG_EXIST, $user_name, $single_user_name, $amountDue, $total_cycles_remaining, $cycles_remaining, $getFrequency, $lastPayValue, $lastPaymentDate, $nextBillingDate, $finalPaymentDate);
				        	
					        // dd($html);		        
					        try{
					            Mail::send(array(), array(), function ($message) use ($email, $html) {
					            $message->to($email)
					                ->subject(MEMBERSHIP_SUBJECT_EXIST)
					                ->from(FROM_EMAIL)
					                ->bcc(ADMIN_EMAIL)
					                ->setBody($html, 'text/html');
					            });

					            $get_profile_name = UserHelper::get_profile_name();
					            // $all_user_name .= $counter.'. '.$memType.' - '.$user_name.'<br>';
					            $all_user_name .= $memType.' - '.$user_name.'<br><p style="margin: 0px; padding-left: 15px;"><b>'.$BayType.'</b>, Row <b>'.$RowType.'</b>, Seat No. <b>'.$seatNumber.'</b></p><br>';

					        	// $html = sprintf(MEMBERSHIP_MSG_EXIST_OWNER, $get_profile_name, $user_name, $memType, $BayType, $RowType, $seatNumber, $amountDue, $total_cycles_remaining, $cycles_remaining, $getFrequency, $lastPayValue, $lastPaymentDate, $nextBillingDate, $finalPaymentDate);

					            //Notify to owner 
					            if($getuserId != $sessionData['user_id']) {
					            	/*Mail::send(array(), array(), function ($message) use ($email, $html) {
						            	$message->to($email)
						                ->subject(MEMBERSHIP_SUBJECT_EXIST_OWNER)
						                ->from(FROM_EMAIL)
						                ->setBody($html, 'text/html');
						            });*/
					            }
					           // return redirect('home')->with('success', trans('messages.contact_us_mail'));
					        }
					        catch(\Exception $e){
					        	// forget session key
		            			// Session::forget('checkout_session_data');
					            return redirect('home')->with('message', trans('messages.unable_to_send_email'));
					        }
					    } else {
					    	$user_name = UserHelper::get_name($getSessionParam['profileId']);
	                		$email = UserHelper::get_email($getSessionParam['profileId']);

	                		$single_user_name = $memType.' - '.$user_name.'<br>';
					        $html = sprintf(MEMBERSHIP_MSG_GA, $user_name, $single_user_name,  $amountDue, $total_cycles_remaining, $cycles_remaining, $getFrequency, $lastPayValue, $lastPaymentDate, $nextBillingDate, $finalPaymentDate);	

					        try{
					            Mail::send(array(), array(), function ($message) use ($email, $html) {
					            $message->to($email)
					                ->subject(MEMBERSHIP_SUBJECT_GA)
					                ->from(FROM_EMAIL)
					                ->bcc(ADMIN_EMAIL)
					                ->setBody($html, 'text/html');
					            });

					            //Sending email to owner
					            $get_profile_name = UserHelper::get_profile_name();
					            $all_user_name .= $counter.'. '.$memType.' - '.$user_name.'<br>';
					        	// $html = sprintf(MEMBERSHIP_MSG_GA_OWNER, $get_profile_name, $user_name, $memType,  $amountDue, $total_cycles_remaining, $cycles_remaining, $getFrequency, $lastPayValue, $lastPaymentDate, $nextBillingDate, $finalPaymentDate);	

					            $email = UserHelper::get_profile_email();
					            if($getuserId != $sessionData['user_id']) {
						            // Mail::send(array(), array(), function ($message) use ($email, $html) {
						            // $message->to($email)
						            //     ->subject(MEMBERSHIP_SUBJECT_GA_OWNER)
						            //     ->from(FROM_EMAIL)
						            //     ->setBody($html, 'text/html');
						            // });
						        }

					           // return redirect('home')->with('success', trans('messages.contact_us_mail'));
					        } 
					        catch(\Exception $e){
					        	// forget session key
		            			// Session::forget('checkout_session_data');
					            return redirect('home')->with('message', trans('messages.unable_to_send_email'));
					        }
					    }

					    // update profile paymentStatus to 1(payment Done)
					    // $this->commonModel->updateRecords(PROFILE, array('paymentStatus' => 1), array('id' => $getSessionParam['profileId']));

					}

	                // insert into transaction tables	                
	                $transData = array('transactionToken' => $agreementId, 'paymentUserId' =>  $sessionData['user_id'], 'paymentProvider' => 'Paypal', 'amount' => $totalFessPerUser);
	                // dd($transData);
	                $insertData = $this->commonModel->addRecord(TRANS_HISTORY, $transData);	                
	                $counter++;
			 	} // end for-each
			 	// forget session key
			 	$installmentFreq = $getFinalFrequency;
			 	$totalInstalments = $total_cycles_remaining;
			 	$installmentRemainig = $cycles_remaining;
			 	$installmentAmount = Session::get('setupFess');
			 	$paymentDeduction = $installmentAmount;
			 	$get_profile_name = UserHelper::get_profile_name();
			 	$totalAmount = Session::get('totalAmount');
			 	$email = UserHelper::get_profile_email();

			 	$html = sprintf(FINAL_OWNER_MESSAGE, $get_profile_name, $all_user_name, $totalAmount, $installmentAmount, $totalInstalments, $installmentRemainig, $installmentFreq, $paymentDeduction, $lastPaymentDate, $nextBillingDate, $finalPaymentDate);

			 	if($getSessionParam['select_membership'] == "New"){
			 		Mail::send(array(), array(), function ($message) use ($email, $html) {
		            $message->to($email)
		                ->subject(FINAL_OWNER_SUBJECT)
		                ->from(FROM_EMAIL)
		                ->bcc(ADMIN_EMAIL)
		                ->setBody($html, 'text/html');
		            });
			 	} else {
			 		$getUserID = Session::get('getuserId');
		            if($getUserID != $sessionData['user_id']) {
			            Mail::send(array(), array(), function ($message) use ($email, $html) {
			            $message->to($email)
			                ->subject(FINAL_OWNER_SUBJECT)
			                ->from(FROM_EMAIL)
			                ->bcc(ADMIN_EMAIL)
			                ->setBody($html, 'text/html');
			            });
			        }
			 	}
			 	
			 	// p($html); die;
	        	Session::forget('checkout_session_data');
				return redirect('home')->with('success', trans('messages.payment_done'));
			    
			} else {
			    // p("User Cancelled the Approval", null);
			    return redirect('home')->with('message', 'User canceled the approval.');
			}			
		} else {
			return redirect('/')->with('message', trans('messages.alert_session_expired'));
		}
	}	

	/**
	* get Request from paypal payment status
	* @param paypal callback parameters
	* save data into tables and redirect back to home page
	*/
	public function getPaymentStatus(Request $request)
 	{
 		if(UserHelper::user_session_data('is_user_logged_in')){ 
		 	// Get the payment ID before session clear
		 	$payment_id = Session::get('paypal_payment_id');

			// clear the session payment ID
		 	Session::forget('paypal_payment_id');

			if(empty($request->input('PayerID')) || empty($request->input('token'))){
		 		return redirect()->route('home')->with('info', 'Payment failed');
		 	}

			$payment = Payment::get($payment_id, $this->_api_context);
			
			// PaymentExecution object includes information necessary
		 	// to execute a PayPal account payment.
		 	// The payer_id is added to the request query parameters
		 	// when the user is redirected from paypal back to your site
		 	$execution = new PaymentExecution();
		 	$execution->setPayerId($request->input('PayerID'));

			//Execute the payment
		 	$result = $payment->execute($execution, $this->_api_context);
			if ($result->getState() == 'approved') { // payment made

				$transactionToken = $result->getId();
				$sessionData = UserHelper::user_session_data('user_id');  
				$getSessionParams = UserHelper::checkout_session_data();

				$counter = 1;
			    $all_user_name = '';
			    $final_total_amount = '';
				foreach ($getSessionParams as $getSessionParam) {
					// fees calculation as per user memberships
					$totalFessPerUser = $getSessionParam['membershipFees'] + $getSessionParam['paidFees'];
					$lastPaymentDate = date('d-M-Y');
					// (1 = RS - Adult, 2 = RS - Concession, 4 = GA - Concession, 3 = GA - Adult)
			 		$memType = UserHelper::getMembershipTypes($getSessionParam['membershipType']);

					if($getSessionParam['select_membership'] == "New"){

		                $userName = ucfirst($getSessionParam['firstName'].' '.$getSessionParam['lastName']);
						// $rand_string = UserHelper::quickRandom(9);
	                    // create user
        				// $insertedId = $this->commonModel->addRecord(USER, array('userName' => $userName, 'email' => $getSessionParam['profileEmail'], 'password' => bcrypt($rand_string), 'emailConfirmed' => 1));
        				$memberNumber = UserHelper::generateMemberNo($getSessionParam['prefix']);

        				$dateofBirth = UserHelper::changeDateFormat($getSessionParam['dateofBirth']);
						// get profile data from session
						$profileData = array(
		                    'memberNumber'  => $memberNumber,
		                    'firstName' =>  $getSessionParam['firstName'],
		                    'lastName'  =>  $getSessionParam['lastName'],
		                    'dateofBirth'   =>  $dateofBirth,
		                    'contactNumber' => $getSessionParam['contactNumber'],
		                    'state' =>  $getSessionParam['state'],
		                    'suburb' => $getSessionParam['suburb'],
		                    'country' => $getSessionParam['country'],
		                    'postCode'  =>  $getSessionParam['postCode'],
		                    'yearFirstJoined' => $getSessionParam['yearFirstJoined'],
		                    'profession' => $getSessionParam['profession'],
		                    'addressLine1'  =>  $getSessionParam['addressLine1'],
		                    'addressLine2'  =>  $getSessionParam['addressLine2'],
		                    'membershipBuyerId' => $getSessionParam['membershipBuyerId'],
		                    'profileEmail' => $getSessionParam['profileEmail'],
		                    'idAttachment' => $getSessionParam['idAttachment'],
		                    'paymentStatus' => 1
		                );
		                $insertId = $this->commonModel->addRecord(PROFILE, $profileData);

		                // Email verification token
			        	/*$token = getToken(50);
			        	User::where('id', $insertedId)->update(array('securityStamp'=>$token));
			        	$email = $getSessionParam['profileEmail'];
			        	$link = WEB_URL.EMAIL_VERIFICATION_FRONT_SLUG.$insertedId.'/'.base64_encode($rand_string).'/'.$token;
			        	$html = sprintf(EMAIL_VERIFICATION_MESSAGE_WITH_PASSWORD, $userName, $memberNumber, $rand_string, $link);
			        	// send email
			            try{
			                Mail::send(array(), array(), function ($message) use ($email, $html) {
			                $message->to($email)
			                    ->subject(EMAIL_VERIFICATION_SUBJECT)
			                    ->from(FROM_EMAIL)
			                    ->setBody($html, 'text/html');
			                });
			            } 
			            catch(\Exception $e){
			                return redirect('register')->with('message', trans('messages.unable_to_send_email'));
			            }*/
			            
		                $getUserRecord = DB::table(PROFILE)->select('userId')->where(array('id' => $insertId))->get();
		                if(!empty($getUserRecord)){
			                if($getUserRecord[0]->userId == $sessionData['user_id']) {
			                	DB::table(USER)->where('id', $getUserRecord[0]->userId)->update(array('userName' => $userName));
			                }
			            }
                        // image upload email 
		                if(!empty($getSessionParam['idAttachment'])){
	                        $email = ADMIN_EMAIL;
	                        $html = sprintf(IMAGE_UPLOAD_MESSAGE, $userName);                   
	                        try{
	                            Mail::send(array(), array(), function ($message) use ($email, $html) {
	                            $message->to($email)
	                                ->subject(IMAGE_UPLOAD_SUBJECT)
	                                ->from(FROM_EMAIL)
	                                ->bcc(ADMIN_EMAIL)
	                                ->setBody($html, 'text/html');
	                            });
	                        } 
	                        catch(\Exception $e){
	                            return redirect('home')->with('message', trans('messages.unable_to_send_email'));
	                        }
	                    }

		                // p($getSessionParam); die;	                
		                if($getSessionParam['membershipType']==1 || $getSessionParam['membershipType']==2 || $getSessionParam['membershipType']==7){
		                	// send Email  
		                	$Bay =  $getSessionParam['Bay'];
		                	$Row = $getSessionParam['Row'];
			            	if($Bay == 1){
			            		$BayType = "Bay 1";
			            	} else if ($Bay == 2) {
			            		$BayType = "Bay 2";
			            	} else if ($Bay == 3) {
			            		$BayType = "Bay 3";
			            	} else if ($Bay == 4) {
			            		$BayType = "Bay 4";
			            	} else if ($Bay == 5) {
			            		$BayType = "Bay 5";
			            	} else {
			            		$BayType = "NA";
			            	}

			            	if($Row){
                                $RowType = $getSessionParam['Row'];
                            } else {
                                $RowType = "NA";
                            }

			            	$user_name = UserHelper::get_name($insertId);
		                	$email = UserHelper::get_email($insertId);
		                	// $memberNumber = $getSessionParam['memberNumber'];
		                    $seatNumber = $getSessionParam['seatNumber'];

	                    	// $single_user_name = $memType.' - '.$user_name.'<br>';
	                    	$single_user_name = $memType.' - '.$user_name.'<br><p style="margin: 0px;"><b>'.$BayType.'</b>, Row <b>'.$RowType.'</b>, Seat No. <b>'.$seatNumber.'</b></p>';

					        $html = sprintf(MEMBERSHIP_MSG_RS_FULL, $user_name, $single_user_name, $totalFessPerUser, $lastPaymentDate);
					        
					        try{
					            Mail::send(array(), array(), function ($message) use ($email, $html) {
					            $message->to($email)
					                ->subject(MEMBERSHIP_SUBJECT_RS)
					                ->from(FROM_EMAIL)
					                ->bcc(ADMIN_EMAIL)
					                ->setBody($html, 'text/html');
					            });

					            //Sending email to owner
					            $get_profile_name = UserHelper::get_profile_name();
                            	// $html =  sprintf(MEMBERSHIP_MSG_RS_FULL_OWNER, $get_profile_name, $user_name, $memberNumber, $memType, $BayType, $RowType, $seatNumber, $totalFessPerUser, $lastPaymentDate);
                            	// $all_user_name .= $counter.'. '.$memType.' - '.$user_name.'<br>';
                            	$all_user_name .= $counter.'. '.$memType.' - '.$user_name.'<br><p style="margin: 0px; padding-left: 15px;"><b>'.$BayType.'</b>, Row <b>'.$RowType.'</b>, Seat No. <b>'.$seatNumber.'</b></p><br>';
                                /*$email = UserHelper::get_profile_email();
                                 Mail::send(array(), array(), function ($message) use ($email, $html) {
                                $message->to($email)
                                    ->subject(MEMBERSHIP_SUBJECT_RS)
                                    ->from(FROM_EMAIL)
                                    ->setBody($html, 'text/html');
                                });*/

					           // return redirect('home')->with('success', trans('messages.contact_us_mail'));
					        } 
					        catch(\Exception $e){
					        	// forget session key
		            			// Session::forget('checkout_session_data');
					            return redirect('home')->with('message', trans('messages.unable_to_send_email'));
					        }
		                } else {
		                	$user_name = UserHelper::get_name($insertId);
		                	$email = UserHelper::get_email($insertId);
		                	// $memberNumber = $getSessionParam['memberNumber'];

		                	$single_user_name = $memType.' - '.$user_name.'<br>';
					        $html = sprintf(MEMBERSHIP_MSG_GA_FULL, $user_name, $single_user_name, $totalFessPerUser, $lastPaymentDate);
					        		        
					        try{
					            Mail::send(array(), array(), function ($message) use ($email, $html) {
					            $message->to($email)
					                ->subject(MEMBERSHIP_SUBJECT_GA)
					                ->from(FROM_EMAIL)
					                ->bcc(ADMIN_EMAIL)
					                ->setBody($html, 'text/html');
					            });

					            //Sending email to owner
                                $email = UserHelper::get_profile_email();
                                $get_profile_name = UserHelper::get_profile_name();
                                $all_user_name .= $counter.'. '.$memType.' - '.$user_name.'<br>';
                            	/*$html =  sprintf(MEMBERSHIP_MSG_GA_FULL_OWNER, $get_profile_name, $user_name, $memType, $totalFessPerUser, $lastPaymentDate);	
                                 Mail::send(array(), array(), function ($message) use ($email, $html) {
                                $message->to($email)
                                    ->subject(MEMBERSHIP_SUBJECT_GA)
                                    ->from(FROM_EMAIL)
                                    ->setBody($html, 'text/html');
                                });*/
					            // return redirect('home')->with('success', trans('messages.contact_us_mail'));
					        } 
					        catch(\Exception $e){
					        	// forget session key
		            			// Session::forget('checkout_session_data');
					            return redirect('home')->with('message', trans('messages.unable_to_send_email'));
					        }
		                }            	

			         	// (1 = RS - Adult, 2 = RS - Concession, 4 = GA - Concession, 3 = GA - Adult)
				        $user_comment = Session::get('user_comment');
				        if($user_comment == "null"){
				        	$userComment = "";
				        } else {
				        	$userComment = $user_comment;
				        }
	                	$membershipData = array(
				                            'membershipType' => $getSessionParam['membershipType'],
				                            'startDate' => $getSessionParam['startDate'],
				                            'endDate' => $getSessionParam['endDate'],
				                            'membershipYear' => $getSessionParam['membershipYear'],
				                           	'profileId' => $insertId,
				                            'privacyAcknowledged' => $getSessionParam['privacyAcknowledged'],
				                            'membershipStatus' => 'Active',
				                            'comment' => $userComment
				                        );
	                	// add new membership
		            	$memberShipId = $this->commonModel->addRecord(MEMBERSHIP, $membershipData);

		                if($getSessionParam['membershipType']==1 || $getSessionParam['membershipType']==2 || $getSessionParam['membershipType']==7){
		                    //  seat allocation data
		                    $seatAlocationData = array(
		                                    'Bay'   =>  $getSessionParam['Bay'],
		                                    'Row'   =>  $getSessionParam['Row'],
		                                    'seatNumber' => $getSessionParam['seatNumber'],
		                                    'memberShipId' => $memberShipId,
		                                    'reserved' => 1,
		                                    'createdOn' =>  $getSessionParam['createdOn'],
		                                    'changedOn' =>  $getSessionParam['changedOn'],
		                                    'createdBy' => $getSessionParam['createdBy'],
		                                    'deleted' => $getSessionParam['deleted']
		                                );

		                    // check for inactive seats
	            			$checkForInactiveSeats = DB::table(SEAT_ALLOCATION)->where(array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'reserved' => 0, 'deleted' => 0))->count();
	            			if($checkForInactiveSeats){
	            				// update record
	            				$this->commonModel->updateRecords(SEAT_ALLOCATION, array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'memberShipId' =>$memberShipId, 'reserved' => 1, 'createdOn' =>  $getSessionParam['createdOn'], 'changedOn' =>  $getSessionParam['changedOn'],'createdBy' => $getSessionParam['createdBy'], 'deleted' => $getSessionParam['deleted']), array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'reserved' => 0, 'deleted' => 0));
	            			} else {
			                	// Seat Allocation
			                    $seatAllocationId = $this->commonModel->addRecord(SEAT_ALLOCATION, $seatAlocationData);
			                }
		                }

		                if(empty($getSessionParam['Bay']) && empty($getSessionParam['Row']) && empty($getSessionParam['seatNumber'])){
		                	$BayNo = "";
		                	$RowNo = "";
		                	$seatNo = "";
		                } else {
		                	$BayNo = $getSessionParam['Bay'];
		                	$RowNo = $getSessionParam['Row'];
		                	$seatNo = $getSessionParam['seatNumber'];
		                }
		                // insert record into membershipHistory table
                        DB::table(HISTORY)->insertGetId(array('profileId' => $insertId, 'membershipId' => $memberShipId, 'membershipType' => $getSessionParam['membershipType'], 'bay' => $BayNo, 'row' => $RowNo, 'seatNumber' => $seatNo, 'membershipBuyerId' => $getSessionParam['membershipBuyerId'], 'yearFirstJoined' => $getSessionParam['yearFirstJoined'], 'createdDate' => date('Y-m-d H:i:s')));

		                // $this->commonModel->updateRecords(PROFILE, array('paymentStatus' => 1), array('id' => $insertId));

					} else {
						// existing memebsership logic
						// update profileEmail
						$userName = ucfirst($getSessionParam['firstName'].' '.$getSessionParam['lastName']);
						$dateofBirth = UserHelper::changeDateFormat($getSessionParam['dateofBirth']);
						if(empty($getSessionParam['idAttachment'])){
							$this->commonModel->updateRecords(PROFILE, array('firstName' =>  $getSessionParam['firstName'], 'lastName'  =>  $getSessionParam['lastName'], 'profileEmail' => $getSessionParam['profileEmail'], 'dateofBirth' => $dateofBirth, 'contactNumber' => $getSessionParam['contactNumber'], 'state' =>  $getSessionParam['state'], 'suburb' => $getSessionParam['suburb'], 'country' => $getSessionParam['country'], 'postCode'  =>  $getSessionParam['postCode'], 'addressLine1'  =>  $getSessionParam['addressLine1'], 'addressLine2'  =>  $getSessionParam['addressLine2'], 'membershipBuyerId' => $getSessionParam['membershipBuyerId'], 'yearFirstJoined' => $getSessionParam['yearFirstJoined'], 'profession' => $getSessionParam['profession']), array('id' => $getSessionParam['profileId']));		

						} else {
							$this->commonModel->updateRecords(PROFILE, array('firstName' =>  $getSessionParam['firstName'], 'lastName'  =>  $getSessionParam['lastName'], 'profileEmail' => $getSessionParam['profileEmail'], 'dateofBirth' => $dateofBirth, 'contactNumber' => $getSessionParam['contactNumber'], 'state' =>  $getSessionParam['state'], 'suburb' => $getSessionParam['suburb'], 'country' => $getSessionParam['country'], 'postCode'  =>  $getSessionParam['postCode'], 'addressLine1'  =>  $getSessionParam['addressLine1'], 'addressLine2'  =>  $getSessionParam['addressLine2'], 'idAttachment' => $getSessionParam['idAttachment'], 'membershipBuyerId' => $getSessionParam['membershipBuyerId'], 'yearFirstJoined' => $getSessionParam['yearFirstJoined'], 'profession' => $getSessionParam['profession']), array('id' => $getSessionParam['profileId']));

	                        // image upload email 
			                if(!empty($getSessionParam['idAttachment'])){
		                        $email = ADMIN_EMAIL;
		                        $html = sprintf(IMAGE_UPLOAD_MESSAGE, $userName);                   
		                        try{
		                            Mail::send(array(), array(), function ($message) use ($email, $html) {
		                            $message->to($email)
		                                ->subject(IMAGE_UPLOAD_SUBJECT)
		                                ->from(FROM_EMAIL)
		                                ->bcc(ADMIN_EMAIL)
		                                ->setBody($html, 'text/html');
		                            });
		                        } 
		                        catch(\Exception $e){
		                            return redirect('home')->with('message', trans('messages.unable_to_send_email'));
		                        }
		                    }		                    
						}

						$getUserRecord = DB::table(PROFILE)->select('userId', 'memberNumber')->where(array('id' => $getSessionParam['profileId']))->get();
		                if(!empty($getUserRecord)){
		                	$getuserId = $getUserRecord[0]->userId;
		                	$isExistMIM = $getUserRecord[0]->memberNumber;
		                	Session::forget('getuserId', $getuserId);
							Session::put('getuserId', $getuserId);
			                if($getUserRecord[0]->userId == $sessionData['user_id']) {
			                	if(empty($isExistMIM)){
			                		// $getFirstThree = substr($getUserRecord[0]->memberNumber,0, 3);
			                		$memberNumber = UserHelper::generateMemberNo('MIM');
		                			DB::table(PROFILE)->where('id', $getSessionParam['profileId'])->update(array('memberNumber' => $memberNumber, 'paymentStatus' => 1));
			                	}
			                	DB::table(USER)->where('id', $getUserRecord[0]->userId)->update(array('userName' => $userName));
			                }
			            }
						
                        // (1 = RS - Adult, 2 = RS - Concession, 4 = GA - Concession, 3 = GA - Adult)
						$user_comment = Session::get('user_comment');
				        if($user_comment == "null"){
				        	$userComment = "";
				        } else {
				        	$userComment = $user_comment;
				        }

	                    $checkRecordIsExist = DB::table(MEMBERSHIP)->where(array('profileId' => $getSessionParam['profileId']))->get();
	                    // $checkRecordIsExist = DB::table(MEMBERSHIP)->where(array('profileId' => $getSessionParam['profileId']))->whereYear('membershipYear', '=', $getSessionParam['membershipYear'])->get();
						if(!empty($checkRecordIsExist)){
							
						 	$this->commonModel->updateRecords(MEMBERSHIP, array('membershipType' => $getSessionParam['membershipType'], 'membershipYear' => $getSessionParam['membershipYear'],'comment' => $userComment, 'startDate' => $getSessionParam['startDate'], 'endDate' => $getSessionParam['endDate'], 'membershipStatus' => 'Active'), array('profileId' => $getSessionParam['profileId']));	 	

						 	if($getSessionParam['membershipType']==1 || $getSessionParam['membershipType']==2 || $getSessionParam['membershipType']==7){

						 		$checkMemberIsExist = DB::table(SEAT_ALLOCATION)->where(array('memberShipId' => $getSessionParam['memberShipId']))->count();

						 		if($checkMemberIsExist){
		                			// Seat Allocation update
			                		$this->commonModel->updateRecords(SEAT_ALLOCATION, array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'reserved' => 1, 'createdOn' =>  $getSessionParam['createdOn'], 'changedOn' =>  $getSessionParam['changedOn'],'createdBy' => $getSessionParam['createdBy'], 'deleted' => $getSessionParam['deleted']), array('memberShipId' => $getSessionParam['memberShipId']));
						 		} else {
						 			$this->commonModel->addRecord(SEAT_ALLOCATION, array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'memberShipId' => $getSessionParam['memberShipId'], 'reserved' => 1, 'createdOn' =>  $getSessionParam['createdOn'], 'changedOn' =>  $getSessionParam['changedOn'],'createdBy' => $getSessionParam['createdBy'], 'deleted' => $getSessionParam['deleted']));
						 		}
			                }                

						} else {
							// add new membership
	                		$membershipData = array(
				                            'membershipType' => $getSessionParam['membershipType'],
				                            'startDate' => $getSessionParam['startDate'],
				                            'endDate' => $getSessionParam['endDate'],
				                            'membershipYear' => $getSessionParam['membershipYear'],
				                           	'profileId' => $getSessionParam['profileId'],
				                            'privacyAcknowledged' => $getSessionParam['privacyAcknowledged'],
				                            'membershipStatus' => 'Active',
				                            'comment' => $userComment
				                        );	

		            		$memberShipId = $this->commonModel->addRecord(MEMBERSHIP, $membershipData);

		            		if($getSessionParam['membershipType']==1 || $getSessionParam['membershipType']==2 || $getSessionParam['membershipType']==7){

		            			// check for inactive seats
		            			$checkForInactiveSeats = DB::table(SEAT_ALLOCATION)->where(array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'reserved' => 0, 'deleted' => 0))->count();

		            			if($checkForInactiveSeats){
		            				// update record
		            				$this->commonModel->updateRecords(SEAT_ALLOCATION, array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'memberShipId' =>$memberShipId, 'reserved' => 1, 'createdOn' =>  $getSessionParam['createdOn'], 'changedOn' =>  $getSessionParam['changedOn'],'createdBy' => $getSessionParam['createdBy'], 'deleted' => $getSessionParam['deleted']), array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'reserved' => 0, 'deleted' => 0));
		            			} else {
				                	// Seat Allocation addRecord
				                	$this->commonModel->addRecord(SEAT_ALLOCATION, array('Bay' => $getSessionParam['Bay'], 'Row' => $getSessionParam['Row'], 'seatNumber' => $getSessionParam['seatNumber'], 'memberShipId' => $memberShipId, 'reserved' => 1, 'createdOn' =>  $getSessionParam['createdOn'], 'changedOn' =>  $getSessionParam['changedOn'],'createdBy' => $getSessionParam['createdBy'], 'deleted' => $getSessionParam['deleted']));
			                	}			                	
			                }

						}

	                	if($getSessionParam['membershipType']==1 || $getSessionParam['membershipType']==2 || $getSessionParam['membershipType']==7){
		                	// send Email    
		                	$Bay =  $getSessionParam['Bay'];
		                	$Row =  $getSessionParam['Row'];
			            	if($Bay == 1){
			            		$BayType = "Bay 1";
			            	} else if ($Bay == 2) {
			            		$BayType = "Bay 2";
			            	} else if ($Bay == 3) {
			            		$BayType = "Bay 3";
			            	} else if ($Bay == 4) {
			            		$BayType = "Bay 4";
			            	} else if ($Bay == 5) {
			            		$BayType = "Bay 5";
			            	} else {
			            		$BayType = "NA";
			            	}  

			            	if($Row){
                                $RowType = $getSessionParam['Row'];
                            } else {
                                $RowType = "NA";
                            }

		                	$user_name = UserHelper::get_name($getSessionParam['profileId']);
                            $seatNumber = $getSessionParam['seatNumber'];
                            // $memberNumber = $getSessionParam['memberNumber'];
                            $email = UserHelper::get_email($getSessionParam['profileId']);

                            // $single_user_name = $memType.' - '.$user_name.'<br>';
                            $single_user_name = $memType.' - '.$user_name.'<br><p style="margin: 0px;"><b>'.$BayType.'</b>, Row <b>'.$RowType.'</b>, Seat No. <b>'.$seatNumber.'</b></p>';

					        $html = sprintf(MEMBERSHIP_MSG_EXIST_FULL, $user_name, $single_user_name, $totalFessPerUser, $lastPaymentDate);

					        try{
					            Mail::send(array(), array(), function ($message) use ($email, $html) {
					            $message->to($email)
					                ->subject(MEMBERSHIP_SUBJECT_EXIST)
					                ->from(FROM_EMAIL)
					                ->bcc(ADMIN_EMAIL)
					                ->setBody($html, 'text/html');
					            });

					            //Notify to owner 
				            	$get_profile_name = UserHelper::get_profile_name();
				            	$email = UserHelper::get_profile_email();
				            	// $all_user_name .= $counter.'. '.$memType.' - '.$user_name.'<br>';
				            	$all_user_name .= $memType.' - '.$user_name.'<br><p style="margin: 0px; padding-left: 15px;"><b>'.$BayType.'</b>, Row <b>'.$RowType.'</b>, Seat No. <b>'.$seatNumber.'</b></p><br>';
					       		/*$html =  sprintf(MEMBERSHIP_MSG_EXIST_FULL_OWNER, $get_profile_name, $user_name, $memType, $BayType, $RowType, $seatNumber, $totalFessPerUser, $lastPaymentDate);
                                if($getuserId != $sessionData['user_id']) {
                                    Mail::send(array(), array(), function ($message) use ($email, $html) {
                                        $message->to($email)
                                        ->subject(MEMBERSHIP_SUBJECT_EXIST)
                                        ->from(FROM_EMAIL)
                                        ->setBody($html, 'text/html');
                                    });
                                }*/
					           // return redirect('home')->with('success', trans('messages.contact_us_mail'));
					        }
					        catch(\Exception $e){
					        	// forget session key
		            			// Session::forget('checkout_session_data');
					            return redirect('home')->with('message', trans('messages.unable_to_send_email'));
					        }
					    } else {
					    	$user_name = UserHelper::get_name($getSessionParam['profileId']);
					    	$email = UserHelper::get_email($getSessionParam['profileId']);

					    	$single_user_name = $memType.' - '.$user_name.'<br>';
					        $html = sprintf(MEMBERSHIP_MSG_GA_FULL, $user_name, $single_user_name, $totalFessPerUser, $lastPaymentDate);	
					        
					        try{
					            Mail::send(array(), array(), function ($message) use ($email, $html) {
					            $message->to($email)
					                ->subject(MEMBERSHIP_SUBJECT_GA)
					                ->from(FROM_EMAIL)
					                ->bcc(ADMIN_EMAIL)
					                ->setBody($html, 'text/html');
					            });

					            //Sending email to owner
					            $get_profile_name = UserHelper::get_profile_name();

					            $all_user_name .= $counter.'. '.$memType.' - '.$user_name.'<br>';
					        	/*$html = sprintf(MEMBERSHIP_MSG_GA_FULL_OWNER, $get_profile_name, $user_name, $memType, $totalFessPerUser, $lastPaymentDate);  
                                $email = UserHelper::get_profile_email();
                                if($getuserId != $sessionData['user_id']) {
	                                Mail::send(array(), array(), function ($message) use ($email, $html) {
	                                $message->to($email)
	                                    ->subject(MEMBERSHIP_SUBJECT_GA)
	                                    ->from(FROM_EMAIL)
	                                    ->setBody($html, 'text/html');
	                                });
	                            }*/
					           // return redirect('home')->with('success', trans('messages.contact_us_mail'));
					        } 
					        catch(\Exception $e){
					        	// forget session key
		            			// Session::forget('checkout_session_data');
					            return redirect('home')->with('message', trans('messages.unable_to_send_email'));
					        }
					    }
					    // update profile paymentStatus to 1(payment Done)
					    // $this->commonModel->updateRecords(PROFILE, array('paymentStatus' => 1), array('id' => $getSessionParam['profileId']));

					}			
					
	                // insert into transaction tables
	                // $totalAmount = Session::get('totalAmount');
	                $transData = array('transactionToken' => $transactionToken, 'paymentUserId' =>  $sessionData['user_id'], 'paymentProvider' => 'Paypal', 'amount' => $totalFessPerUser);
	                // dd($transData);
	                $insertData = $this->commonModel->addRecord(TRANS_HISTORY, $transData);	                
                	$counter++;
			 	} 
			 	// end for-each

			 	// $installmentFreq = $getFrequency;
			 	// $totalInstalments = $total_cycles_remaining;
			 	// $installmentRemainig = $cycles_remaining;
			 	// $installmentAmount = Session::get('setupFess');
			 	// $paymentDeduction = $installmentAmount;
			 	$get_profile_name = UserHelper::get_profile_name();
			 	$totalAmount = Session::get('totalAmount');
			 	$email = UserHelper::get_profile_email();

			 	$html = sprintf(FINAL_FULL_OWNER_MESSAGE, $get_profile_name, $all_user_name, $totalAmount, $lastPaymentDate);

			 	if($getSessionParam['select_membership'] == "New"){
			 		Mail::send(array(), array(), function ($message) use ($email, $html) {
		            $message->to($email)
		                ->subject(FINAL_OWNER_SUBJECT)
		                ->from(FROM_EMAIL)
		                ->bcc(ADMIN_EMAIL)
		                ->setBody($html, 'text/html');
		            });
			 	} else {
			 		$getUserID = Session::get('getuserId');
		            if($getUserID != $sessionData['user_id']) {
			            Mail::send(array(), array(), function ($message) use ($email, $html) {
			            $message->to($email)
			                ->subject(FINAL_OWNER_SUBJECT)
			                ->from(FROM_EMAIL)
			                ->bcc(ADMIN_EMAIL)
			                ->setBody($html, 'text/html');
			            });
			        }
			 	}

			 	// forget session key
	        	Session::forget('checkout_session_data');
				return redirect('home')->with('success', trans('messages.payment_done'));
		 	} 
		 	// return Redirect::route('home')
		 		// ->with('info', 'Payment failed');
		 	// }
	 	} else {
	 		return redirect('/')->with('message', trans('messages.alert_session_expired'));
	 	}		
	}

	/**
	* Get billing agreement
	* @param created agreement Id
	* result Deactivate all the cancelled subscription
	*/
	public function getBillingAgreement()
	{
		$getAllMembershipsRecords = DB::table(MEMBERSHIP)->select('id', 'agreementId')->get();
		if(!empty($getAllMembershipsRecords)){
			$agreement = array();
			foreach ($getAllMembershipsRecords as $getAllMembershipsRecord) {
				$createdAgreementId = $getAllMembershipsRecord->agreementId;
				$membershipId = $getAllMembershipsRecord->id;
				try {
					// get billing agreement
				    $getAgreement = Agreement::get($createdAgreementId, $this->_api_context);
				    $agreementState = $getAgreement->state;

				    if($agreementState == 'Cancelled'){
				    	DB::table(MEMBERSHIP)->where('agreementId', $createdAgreementId)->update(['membershipStatus' => 'Inactive']);

				    	DB::table(SEAT_ALLOCATION)->where('memberShipId', $membershipId)->update(['reserved' =>0, 'deleted' => 0]);
				    }
				} catch (Exception $ex) {
				    // p("Retrieved an Agreement", "Agreement", $agreement->getId(), $createdAgreementId, $ex);
				    exit(1);
				}

			}
		} else {
			return redirect('/')->with('message', 'Record not exist. Cron Record!!');
		}
	}

	/**
	* check end date for membership
	* result deactivate all membership having endDate less than march 01-2018 and released its holding seats
	*/
	public function checkMembershipEndDate()
	{
		$getAllMembershipsRecords = DB::table(MEMBERSHIP)->select('id','membershipStatus')->whereDate('endDate', '=', date('2018-03-02'))->get();
		if(!empty($getAllMembershipsRecords)){
			foreach ($getAllMembershipsRecords as $value) {
				DB::table(MEMBERSHIP)->where('id', $value->id)->update(['membershipStatus' => 'Inactive']);
				DB::table(SEAT_ALLOCATION)->where('memberShipId', $value->id)->update(['reserved' =>0, 'deleted' => 0]);
			}
			dd('Records updated successfully');
		}
	}

	/**
	* deactive 2017 members
	*/
	public function deActivate2017Members()
	{
		// echo date('Y-m-d H:i:s a'); die;
        if(UserHelper::user_session_data('is_user_logged_in')){
    		$get2017Members = DB::table(MEMBERSHIP)->select('id', 'membershipYear', 'profileId', 'membershipStatus')->where('membershipYear', 2017)->get();
    		if(!empty($get2017Members)){
    			$membershipId = array();
    			$profileId = array();
    			foreach ($get2017Members as $get2017Member) {
    				$membershipId[] = $get2017Member->id;
    				$profileId[] = $get2017Member->profileId;
    			}

    			if(!empty($membershipId) && !empty($profileId)){
    				// update Membership status to Inactive in membership table
    				DB::table(MEMBERSHIP)->whereIn('id', $membershipId)->update(['membershipStatus' => 'Inactive']);
    				// update reserved = 0 and deleted = 0 in seat Allocation table
    				DB::table(SEAT_ALLOCATION)->whereIn('memberShipId', $membershipId)->update(['reserved' => 0, 'deleted' => 0]);
    				return redirect('/home')->with('success', 'Deactivated all 2017 members successfully.');
    			}    			
    			// p($membershipId);
    		}
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        } 
	}

}
