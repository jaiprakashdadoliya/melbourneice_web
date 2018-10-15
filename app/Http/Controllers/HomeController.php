<?php
namespace App\Http\Controllers;
/*ini_set('memory_limit','2048M');
ini_set('max_execution_time', 180); // 3 minutes
use Excel;*/

use Input, DB, Session, URL, File;
use Validator;
use Auth, Password, Mail, JWTAuth;
use Illuminate\Http\Request;
use App\Models\CommonModel;
use App\Http\Controllers\Controller;
use App\User;
use App\Helpers\UserHelper;
use Tymon\JWTAuth\Exceptions\JWTException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DateTime;


class HomeController extends Controller
{
    public function __construct()
    {   
        // date_default_timezone_set('Australia/Melbourne');
    	$this->commonModel = new CommonModel();
    }

	/**
    * Home page
    * @param null
    * redirect to home page
    */
    public function index()
    {
        // echo date('Y-m-d H:i:s a'); die;
        if(UserHelper::user_session_data('is_user_logged_in')){ 
            $sessionData = UserHelper::user_session_data('user_id');
			//$result = $request->session()->get('checkout_session_data');
            $data['profileDetail'] = UserHelper::checkIsProfileCompleted($sessionData['user_id']);
            return view('home.home', $data);
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }    	
    }

    /**
    * getProfile method
    * @param null
    * redirect to profile page
    */
    public function getProfile()
    {
        if(UserHelper::user_session_data('is_user_logged_in')){   
            $sessionData = UserHelper::user_session_data('user_id');  
            $data['profileDetail'] = $this->commonModel->getAllRecordsBycondition(PROFILE, array('userId' => $sessionData['user_id']));  
            
            /* section for memberships status */
            $data['membserships_title'] = 'Memberships';                    

            // get user profile id
            // $mem_purchase_id = $this->commonModel->getAllConditionalsRecordsByFields(PROFILE, array('membershipBuyerId' => $sessionData['user_id'], 'paymentStatus' => 1), array('id'));
            $mem_purchase_id = DB::table(PROFILE)->select('id')->where(array('membershipBuyerId' => $sessionData['user_id'], 'paymentStatus' => 1))->orWhere(array('userId' => $sessionData['user_id']))->orderBy('firstName', 'asc')->get();

            if(!empty($mem_purchase_id)){
                $profieId = array();
                foreach ($mem_purchase_id as $ids) {
                    $profileId[] = $ids->id;
                }
                // get data from membership
                // $data['memebershipData'] = DB::table(MEMBERSHIP)->whereIn('profileId', $profileId)->orderBy('profileId', 'desc')->get();                
                $data['memebershipData'] = DB::table(MEMBERSHIP)->leftJoin('seatAllocations', 'memberShips.id', '=', 'seatAllocations.memberShipId')->leftJoin('profiles', 'memberShips.profileId', '=', 'profiles.id')->whereIn('profileId', $profileId)->orderBy('profileId', 'desc')->get();
            }

            return view('home.profile', $data);
            // return view('home.membership_status', $data); 

        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }  
    }

    public function allMembershipRecord(Request $request)
    {
        if(UserHelper::user_session_data('is_user_logged_in')){   
            $sessionData = UserHelper::user_session_data('user_id'); 

            // get user profile id
            // $mem_purchase_id = $this->commonModel->getAllConditionalsRecordsByFields(PROFILE, array('membershipBuyerId' => $sessionData['user_id'], 'paymentStatus' => 1), array('id'));

            $mem_purchase_id = DB::table(PROFILE)->select('id')->where(array('membershipBuyerId' => $sessionData['user_id'], 'paymentStatus' => 1))->orWhere(array('userId' => $sessionData['user_id']))->orderBy('firstName', 'asc')->get();


            if(!empty($mem_purchase_id)){
                $profieId = array();
                foreach ($mem_purchase_id as $ids) {
                    $profileId[] = $ids->id;
                }
                // get data from membership
                // $data['memebershipData'] = DB::table(MEMBERSHIP)->whereIn('profileId', $profileId)->orderBy('profileId', 'desc')->get();
                /*$data['memebershipData'] = DB::table('memberShips')->leftJoin('seatAllocations', 'memberShips.id', '=', 'seatAllocations.memberShipId')->leftJoin('profiles', 'memberShips.profileId', '=', 'profiles.id')->whereIn('profileId', $profileId)->orderBy('profileId', 'desc')->get();

                p($data['memebershipData']); die;*/

                $columns = array( 
                            0 => 'memberShips.id', 
                            1 => 'Name',
                            2 => 'Member Number',
                            3 => 'Membership Type',
                            4 => 'Concession Type',
                            5 => 'Seat',
                            6 => 'Membership Status',
                            7 => 'Created Date'
                        );

                $totalData = DB::table(MEMBERSHIP)->whereIn('profileId', $profileId)->count();

                $totalFiltered = $totalData; 

                $limit = $request->input('length');
                $start = $request->input('start');
                $order = $columns[$request->input('order.0.column')];
                $dir = $request->input('order.0.dir');

                if(empty($request->input('search.value')))
                {            
                    $posts =  DB::table('memberShips')->leftJoin('seatAllocations', 'memberShips.id', '=', 'seatAllocations.memberShipId')->leftJoin('profiles', 'memberShips.profileId', '=', 'profiles.id')->whereIn('profileId', $profileId)->orderBy('profileId', 'desc')->offset($start)->limit($limit)->orderBy($order,$dir)->get();
                }
                else 
                {
                    $search = $request->input('search.value');
                    $posts = DB::table('memberShips')->leftJoin('seatAllocations', 'memberShips.id', '=', 'seatAllocations.memberShipId')->leftJoin('profiles', 'memberShips.profileId', '=', 'profiles.id')->where('profiles.firstName','LIKE',"%{$search}%")->orWhere('profiles.lastName','LIKE',"%{$search}%")->orWhere('profiles.memberNumber','LIKE',"%{$search}%")->whereIn('profileId', $profileId)->orderBy('profileId', 'desc')->offset($start)->limit($limit)->get();

                    $totalFiltered = DB::table('memberShips')->leftJoin('seatAllocations', 'memberShips.id', '=', 'seatAllocations.memberShipId')->leftJoin('profiles', 'memberShips.profileId', '=', 'profiles.id')->where('profiles.firstName','LIKE',"%{$search}%")->orWhere('profiles.lastName','LIKE',"%{$search}%")->orWhere('profiles.memberNumber','LIKE',"%{$search}%")->whereIn('profileId', $profileId)->count();
                }


                $data = array();
                if(!empty($posts))
                {
                    $j = 1;
                    foreach ($posts as $memberships)
                    {
                        if($memberships->membershipType == 1){
                          $membershipType = 'SEATED - Adult';
                        } else if($memberships->membershipType == 2){
                          $membershipType = 'SEATED - Concession';
                        } else if($memberships->membershipType == 3){
                          $membershipType = 'GA - Adult';
                        } else if($memberships->membershipType == 4){
                          $membershipType = 'GA - Concession';
                        } else if($memberships->membershipType == 5){
                          $membershipType = 'DISTANT SUPPORTER';
                        } else {}

                        $membershipStatus = $memberships->membershipStatus;
                        $membershipYear = $memberships->membershipYear;

                        if($memberships->Bay == 1){
                          $BayType = 'Bay 1';
                        } else if($memberships->Bay == 2){
                          $BayType = 'Bay 2';
                        } else if($memberships->Bay == 3){
                          $BayType = 'Bay 3';
                        } else if($memberships->Bay == 4){
                          $BayType = 'Bay 4';
                        } else if($memberships->Bay == 5){
                          $BayType = 'Bay 5';
                        } else {
                          $BayType = '-';
                        }

                        if($memberships->Row){
                          $RowNo = $memberships->Row;
                        } else {
                          $RowNo = '-';
                        }

                        if($memberships->seatNumber != 0){
                          $seatNumber = $memberships->seatNumber;
                        } else {
                          $seatNumber = '-';
                        }


                        if(!empty($memberships->memberNumber)){
                          $memberNumber = $memberships->memberNumber;
                        } else {
                          $memberNumber = 'N/A';
                        }

                        if(!empty($memberships->firstName) && !empty($memberships->lastName)){
                          $fullName = ucfirst($memberships->firstName.' '.$memberships->lastName);
                        } else {
                          $fullName = 'N/A';
                        }

                        $old_timestamp = strtotime($memberships->startDate);
                        $startDate = date('d-M-Y', $old_timestamp); 

                        // assign values to data fields
                        $nestedData['id'] = $j;
                        $nestedData['Name'] = $fullName;
                        $nestedData['Member Number'] = $memberNumber;
                        $nestedData['Membership Type'] = $membershipType;
                        if($membershipType == "RS - Adult" || $membershipType == "RS - Concession"){
                            $nestedData['Seat'] = $BayType.', '.$RowNo.', '.$seatNumber;
                        } else if($membershipType = 'DISTANT SUPPORTER'){
                            $nestedData['Seat'] = '-';
                        } else {
                            $nestedData['Seat'] = 'GA';
                        }                       
                        $nestedData['Membership Status'] = $membershipStatus;
                        $nestedData['Created Date'] = $startDate;                        
                        $data[] = $nestedData;

                        $j++;
                    }
                }
                  
                $json_data = array(
                                "draw"            => intval($request->input('draw')),  
                                "recordsTotal"    => intval($totalData),  
                                "recordsFiltered" => intval($totalFiltered), 
                                "data"            => $data   
                            );
                    
                echo json_encode($json_data);
            }


        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        } 
    }

    /**
    * postProfile method
    * @param request param
    * save data to profile
    */
    public function postProfile(Request $request)
    {
        if(UserHelper::user_session_data('is_user_logged_in')){   
            $sessionData = UserHelper::user_session_data('user_id');

            // check validations
            $Validator = Validator::make($request->all(),[
                'firstName' => 'required',
                'lastName' => 'required',
                'contactNumber' => 'required|numeric',
                'profession' => 'required',
                'addressLine1' => 'required',
                'suburb'    =>  'required',
                'state' =>  'required',
                'country'   =>  'required',
                'postCode' => 'required|numeric'
            ]);

            if ($Validator->fails()) {
                return redirect('profile')
                            ->withErrors($Validator)
                            ->withInput();
            }

            $dateofBirth = UserHelper::changeDateFormat($request['dateofBirth']);

            $userName = ucfirst($request['firstName'].' '.$request['lastName']);
            // update user
            User::where('id', $sessionData['user_id'])->update(array('userName' => $userName));

            $this->commonModel->updateRecords(PROFILE, array('firstName' => $request['firstName'], 'lastName' => $request['lastName'], 'dateofBirth' => $dateofBirth, 'contactNumber' => $request['contactNumber'], 'addressLine1' => $request['addressLine1'], 'addressLine2' => $request['addressLine2'], 'suburb' => $request['suburb'], 'state' => $request['state'], 'country' => $request['country'], 'postCode' => $request['postCode'], 'profession' => $request['profession']), array('userId' => $sessionData['user_id']));
            
            return redirect('profile')->with('success', trans('messages.profie_updated_success'));
        } else {
            return redirect('/')->with('success', trans('messages.alert_session_expired'));
        }  
    }

    /**
    * getProductAndServices method
    * @param null
    * redirect to product&service
    */
    public function getProductAndServices()
    {
        if(UserHelper::user_session_data('is_user_logged_in')){ 
    	    return view('home.product_services');           
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }
    }

    /**
    * postMembership method
    * @param null
    */
    public function postMembership(Request $request)
    {
        
        if(UserHelper::user_session_data('is_user_logged_in')){  
            $sessionData = UserHelper::user_session_data('is_user_logged_in');          
            $getParam = $request->all();
            if($getParam['select_service'] == 1){
                // get single Record from profile
                $getData = $this->commonModel->getSingleRecord(PROFILE,array('userId' => $sessionData['user_id'], 'paymentStatus' => 1));
                if(!empty($getData)){
                    // match record into membership table 
                    $getCount = $this->commonModel->getRecordCountByCondition(MEMBERSHIP, array('profileId' => $getData->id));
                    if($getCount != 0){
                        $data['getMembershipStatus'] = $this->commonModel->getSingleRecord(MEMBERSHIP, array('profileId' => $getData->id));
                    }                
                }

                //get all membership types with price
                $data['memebershipTypes'] = DB::table('memberShipTypes')->leftJoin('memberShipTypePrices', 'memberShipTypes.id', '=', 'memberShipTypePrices.memberShipTypeId')->orderBy('memberShipTypes.membershipOrder', 'asc')->get();

                $mem_purchase_id = DB::table(PROFILE)->select('id', 'memberNumber')->where(array('membershipBuyerId' => $sessionData['user_id'], 'paymentStatus' => 1))->orderBy('firstName', 'asc')->get();

                 // $mem_purchase_id = DB::table(PROFILE)->select('id', 'memberNumber')->where(array('membershipBuyerId' => $sessionData['user_id'], 'paymentStatus' => 1))->orWhere(array('userId' => $sessionData['user_id']))->orderBy('firstName', 'asc')->get();

                if(!empty($mem_purchase_id)){
                    $i=0;
                    $profieId = array();
                    $memberNumber = array();
                    foreach ($mem_purchase_id as $ids) {
                        $profileId[$i]['profile_id'] = $ids->id;
                        $profileId[$i]['member_no'] = $ids->memberNumber;
                        $i++;
                    }
                    $data['profileIds'] = $profileId;

                }

                // get  bay from master table 
                $data['bays'] = DB::table(BAY)->select('bayNumber')->distinct('bayNumber')->get();
                $data['year'] = '';

                return view('home.membership', $data);       
            } else {
                return redirect('prod&serv')->with('message', trans('messages.select_membership_only'));
            }
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }        
    }

    /**
    * getMembership method
    * @param null
    */
    public function getMembership()
    {
        if(UserHelper::user_session_data('is_user_logged_in')){ 
            $sessionData = UserHelper::user_session_data('is_user_logged_in');

            
            // get single Record from profile
            $getData = $this->commonModel->getSingleRecord(PROFILE,array('userId' => $sessionData['user_id'], 'paymentStatus' => 1));
            if(!empty($getData)){
                // match record into membership table 
                $getCount = $this->commonModel->getRecordCountByCondition(MEMBERSHIP, array('profileId' => $getData->id));
                if($getCount != 0){
                    $data['getMembershipStatus'] = $this->commonModel->getSingleRecord(MEMBERSHIP, array('profileId' => $getData->id));
                }                
            }

            //get all membership types with price
            $data['memebershipTypes'] = DB::table('memberShipTypes')->leftJoin('memberShipTypePrices', 'memberShipTypes.id', '=', 'memberShipTypePrices.memberShipTypeId')->orderBy('memberShipTypes.membershipOrder', 'asc')->get();

            // get exiting membership to show user profile name 
            // $mem_purchase_id = $this->commonModel->getAllConditionalsRecordsByFields(PROFILE, array('membershipBuyerId' => $sessionData['user_id'], 'paymentStatus' => 1), array('id', 'memberNumber'));
            $mem_purchase_id = DB::table(PROFILE)->select('id', 'memberNumber')->where(array('membershipBuyerId' => $sessionData['user_id'], 'paymentStatus' => 1))->orderBy('firstName', 'asc')->get();

            // p($mem_purchase_id); die;
            if(!empty($mem_purchase_id)){
                $i=0;
                $profieId = array();
                $memberNumber = array();
                foreach ($mem_purchase_id as $ids) {
                    $profileId[$i]['profile_id'] = $ids->id;
                    $profileId[$i]['member_no'] = $ids->memberNumber;
                    $i++;
                }
                $data['profileIds'] = $profileId;
            }

            // get  bay from master table 
            $data['bays'] = DB::table(BAY)->select('bayNumber')->distinct('bayNumber')->get();
            $data['year'] = '';
			
            return view('home.membership', $data);          
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }
        
    }   

    public function getNewMembership()
    {
        if(UserHelper::user_session_data('is_user_logged_in')){ 
            $sessionData = UserHelper::user_session_data('is_user_logged_in');

            // get single Record from profile
            $getData = $this->commonModel->getSingleRecord(PROFILE,array('userId' => $sessionData['user_id'], 'paymentStatus' => 1));
            if(!empty($getData)){
                // match record into membership table 
                $getCount = $this->commonModel->getRecordCountByCondition(MEMBERSHIP, array('profileId' => $getData->id));
                if($getCount != 0){
                    $data['getMembershipStatus'] = $this->commonModel->getSingleRecord(MEMBERSHIP, array('profileId' => $getData->id));
                }                
            }

            //get all membership types with price
            $data['memebershipTypes'] = DB::table('memberShipTypes')->leftJoin('memberShipTypePrices', 'memberShipTypes.id', '=', 'memberShipTypePrices.memberShipTypeId')->orderBy('memberShipTypes.membershipOrder', 'asc')->get();

            // get exiting membership to show user profile name 
            $mem_purchase_id = DB::table(PROFILE)->select('id', 'memberNumber')->where(array('membershipBuyerId' => $sessionData['user_id'], 'paymentStatus' => 1))->orderBy('firstName', 'asc')->get();

            if(!empty($mem_purchase_id)){
                $i=0;
                $profieId = array();
                $memberNumber = array();
                foreach ($mem_purchase_id as $ids) {
                    $profileId[$i]['profile_id'] = $ids->id;
                    $profileId[$i]['member_no'] = $ids->memberNumber;
                    $i++;
                }
                $data['profileIds'] = $profileId;
            }

            // get  bay from master table 
            $data['bays'] = DB::table(BAY)->select('bayNumber')->distinct('bayNumber')->get();
            $year = date('Y');
            $data['year'] = $year;

            return view('home.membership', $data);          
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }
    } 

    /**
    * contact method
    * @param null
    */
    public function contact()
    {
        if(UserHelper::user_session_data('is_user_logged_in')){
            $sessionData = UserHelper::user_session_data('user_id');
            $data['contactDetails'] = DB::table(PROFILE)->select(DB::raw('CONCAT(firstName," ",lastName) AS full_name'), 'contactNumber', 'profileEmail')->where(array('userId' => $sessionData['user_id']))->get(); 
            // p($data);
            return view('home.contact', $data);         
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }    	   
    }

    /**
    * submitContact Method
    * @param Request parameters
    * @return Send email to site-admin
    */
    public function postContact(Request $request)
    {
        if(UserHelper::user_session_data('is_user_logged_in')){  
            // check validations
            /*$Validator = Validator::make($request->all(),[
                'name' => 'required',
                'contact_number' => 'required|numeric',
                'email' => 'required',
                'message' => 'required'
            ]);

            if ($Validator->fails()) {
                return redirect('contact')
                            ->withErrors($Validator)
                            ->withInput();
            }*/
            $userName = $request['name'];
            $email = $request['email'];
            $contact_number = $request['contact_number'];
            $message_details = $request['message'];

            $html = sprintf(CONTACT_MESSAGE, $userName, $email, $contact_number, $message_details);
            $admin_email = ADMIN_EMAIL;
            // send email
            try{
                Mail::send(array(), array(), function ($message) use ($admin_email, $html) {
                $message->to($admin_email)
                    ->subject(CONTACT_SBUJECT)
                    ->from(FROM_EMAIL)
                    ->setBody($html, 'text/html');
                });

               return redirect('contact')->with('success', trans('messages.contact_us_mail'));
            } 
            catch(\Exception $e){
                return redirect('contact')->with('message', trans('messages.unable_to_send_email'));
            }
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }

    }

    /**
    * fees method
    * @param null
    */
    public function fees()
    {
        if(UserHelper::user_session_data('is_user_logged_in')){   
            $sessionData = UserHelper::user_session_data('user_id');
            $data['user_details'] = DB::table(USER)->select('paidFees', 'membershipFees')->where("id", $sessionData['user_id'])->first();
            return view('home.fees', $data);        
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }       
    }

    /**
    * updateFees method    
    * @param null
    */
    public function postFess(Request $request)
    {
        if(UserHelper::user_session_data('is_user_logged_in')){   
            $sessionData = UserHelper::user_session_data('user_id');

            // check validations
            $Validator = Validator::make($request->all(),[
                'serviceFees' => 'required|not_in:0|numeric|min:1',
                'membershipFees' => 'required|not_in:0|numeric|min:1'
            ]);

            if ($Validator->fails()) {
                return redirect('fees')
                            ->withErrors($Validator)
                            ->withInput();
            }

            // update user
            User::where('id', $sessionData['user_id'])->update(array('paidFees' => $request['serviceFees'], 'membershipFees' => $request['membershipFees']));

            return redirect()->route('fees')->with('feesSuccess', trans('messages.fees_updated_succes'));
        
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }    
    }

    /**
    * setting method
    * @param null
    */
    public function setting()
    {
        $sessionData = UserHelper::user_session_data(); 
        if(UserHelper::user_session_data('is_user_logged_in')){   
            // $sessionData = UserHelper::user_session_data('user_id');
            // $data['user_details'] = DB::table(USER)->select('paidFees', 'membershipFees')->where("id", $sessionData['user_id'])->first();
            return view('home.setting');        
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }    	
    }

    /**
    * postSetting method
    * @param request
    */
    public function postSetting(Request $request)
    {
        if(UserHelper::user_session_data('is_user_logged_in')){  
            $sessionData = UserHelper::user_session_data('user_id'); 

            // check validations
            $Validator = Validator::make($request->all(),[
                'password' => 'required',
                'new_passsword' => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%@]).*$/',
                'confirm_password' =>  'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%@]).*$/|same:new_passsword'
            ]);

            if ($Validator->fails()) {
                return redirect('setting')
                            ->withErrors($Validator)
                            ->withInput();
            }

            $users = DB::table(USER)->where("id",$sessionData['user_id'])->first(); 

            if(!empty($users->password)){
                // check password
                if (Hash::check($request['password'], $users->password)) {
                    // update user
                    User::where('id', $sessionData['user_id'])->update(array('password' => bcrypt($request['confirm_password'])));
                    return redirect()->route('setting')->with('success', trans('messages.pwd_changed_success'));
                } else {
                    return redirect()->route('setting')->with('message', trans('messages.old_password_not_match'));  
                }
            } 
            /*else {
                // update password for Fb and goggle plus user
                User::where('id', $sessionData['user_id'])->update(array('password' => bcrypt($request['confirm_password'])));
                return redirect()->route('setting')->with('success', trans('messages.pwd_changed_success'));
            }*/

        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }   
    }
    

    /**
    * get booked seat according to row and bay type
    * @param Ajax request param
    */
    public function getSeatNumber(Request $request)
    {
        if(UserHelper::user_session_data('is_user_logged_in')){ 
            $sessionData = UserHelper::user_session_data('is_user_logged_in');
            $getParam = request()->all();
            $bayNo = $getParam['bayNo'];
            $rowNo = $getParam['rowNo'];
            
            $result = UserHelper::getSeatNumber($bayNo, $rowNo, $seatNumber='');
            return response()->json(['message'=>'seat numbers.', 'code' => SUCCESS, 'result' => $result]);
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }
    }

    /**
    * get row no according to bay no
    * @param bayNo
    */
    public function getRowNumber(Request $request)
    {
        if(UserHelper::user_session_data('is_user_logged_in')){ 
            $getParam = request()->all();
            $bayNo = $getParam['bayNo'];

            $result = UserHelper::getRowNumber($bayNo, $Row='');            
            return response()->json(['message'=>'row numbers.', 'code' => SUCCESS, 'result' => $result]);

        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));   
        }
    }

    /**
    * register Membership & add new membership method
    * @param Ajax Request param
    * Save the data into session and redirect to checkout page
    */
    public function registerMembership(Request $request)
    {
        if(UserHelper::user_session_data('is_user_logged_in')){   
            $sessionData = UserHelper::user_session_data('user_id');
            $checkoutSessionDatas = UserHelper::checkout_session_data();  
            // get Ajax request 
            $getParam = request()->all();
            $profileUrl = MAIN_URL.'profile';
            $isProfileCompleted = UserHelper::checkIsProfileCompleted($sessionData['user_id']);
            if(empty($isProfileCompleted)){
                return response()->json(['message'=>'Please first complete your <a href="'.$profileUrl.'" target="_blank" class="completeYourProfile">profile</a>.', 'code' => GENERAL_ERROR]);
            }      
            // define bay types(1 = bayA_seats, 2 = bayB_seats, 3 = bayC_seats, 4 = bayD_seats, 5 = bayE_seats)
            if(!empty($getParam['seatNumber'])){
                $baySeats = $getParam['seatNumber'];
            }

            //get all membership types with price
            $memebershipFeesData = DB::table('memberShipTypes')->select('memberShipTypePrice')->leftJoin('memberShipTypePrices', 'memberShipTypes.id', '=', 'memberShipTypePrices.memberShipTypeId')->where('memberShipTypes.id', '=', $getParam['membership_type'])->orderBy('memberShipTypes.id', 'asc')->get();

            if(!empty($memebershipFeesData)){
                $paidFees = PAID_FEES; // define constant
                $membershipFees = $memebershipFeesData[0]->memberShipTypePrice;
            } else {
                return response()->json(['message'=>'Please add your MI Fees details from the Settings.', 'code' => GENERAL_ERROR]);
            }

            //  check for attachedId file
            if ($getParam['membership_type'] == 2 || $getParam['membership_type'] == 4) {
                if(empty($getParam['idAttachment'])){
                    return response()->json(['message'=>'Please upload ID attachment file.', 'code' => GENERAL_ERROR]);
                }
            }

            // check seats no and bay inside cart session
            if($getParam['membership_type']==1 || $getParam['membership_type']==2 || $getParam['membership_type']==7){
                $isExistIncart = "";
                if(!empty($checkoutSessionDatas)){

                    $request->session()->forget('check_bay_and_seat');

                    $set_session_for_bay = $request->session()->put('check_bay_and_seat', array('Bay' => $getParam['select_bay'], 'select_row' => $getParam['select_row'], 'seatNumber' => $baySeats));

                    $check_bay_and_seat = UserHelper::check_bay_and_seat();

                    foreach ($checkoutSessionDatas as $checkSessionData) {
                        if($checkSessionData['membershipType'] == 1 || $checkSessionData['membershipType'] == 2 || $checkSessionData['membershipType'] == 7 ){
                            if($checkSessionData['Bay'] == $check_bay_and_seat['Bay'] && $checkSessionData['seatNumber'] == $check_bay_and_seat['seatNumber'] || ($checkSessionData['seatNumber'] == $check_bay_and_seat['seatNumber'] && $checkSessionData['Row'] == $check_bay_and_seat['select_row'])){
                                $isExistIncart = 1;

                                if($isExistIncart == 1){
                                    $isExistIncart = 1;
                                    break;
                                }
                            } else {
                                $isExistIncart = 0;
                            }
                        }
                    }
                }
                $request->session()->forget('check_bay_and_seat');
                if($isExistIncart == 1){
                    return response()->json(['message'=>'Seat details already exist in the cart.', 'code' => GENERAL_ERROR]);
                }
            }

            // check for duplicate email id for all cases
            if(!empty($checkoutSessionDatas)){
                $getAllEmail = array();
                foreach ($checkoutSessionDatas as $checkSessionData) {
                    if($checkSessionData['profileEmail'] == $getParam['profileEmail']){
                        $getAllEmail[] = $checkSessionData['profileEmail'];                        
                    }
                }
            }
                
            $startDate = date('Y-m-d');
            // $endDate = date('Y-m-d');
            if(!empty($getParam['upcoming_year'])){
                $time = time() + 30 * (24 * 3600);
                $membershipYear = date('Y', $time);
                $endDate = date('Y-03-01', mktime(0, 0, 0, date('m'), 1, date('Y') + 1));
            } else {
                $membershipYear = date('Y');
                $endDate = date('Y-03-01', mktime(0, 0, 0, date('m'), 1, date('Y') + 1));
            }

            $checkEmailExist = $this->commonModel->getSingleRecord(PROFILE, array('userId' => $sessionData['user_id'], 'profileEmail' =>  $getParam['profileEmail'], 'paymentStatus' => 0));

            if(!empty($checkEmailExist))
            { 
                if(empty($getAllEmail)){
                    if($checkEmailExist->memberNumber){
                        $getParam['mim_number'] = $checkEmailExist->memberNumber; // replace value 
                    } else {
                        $getParam['mim_number'] = ''; // replace value
                    }
                    $getParam['select_membership'] = "Renew"; // replace value                
                    $getParam['profileIds'] = $checkEmailExist->id; // replace value
                }
            }

            if($getParam['upcoming_year'] && !empty($getParam['profileIds'])){

                if(!empty($getParam['profileIds'])){
                    $userDetails = DB::table(PROFILE)->select('id','memberNumber')->where(array('id' => $getParam['profileIds']))->get();
                } else {
                    $userDetails = DB::table(PROFILE)->select('id','memberNumber')->where(array('userId' => $sessionData['user_id'], 'profileEmail' => $getParam['profileEmail']))->get();                    
                }
                
                if(!empty($userDetails)){
                    $year = $getParam['upcoming_year'];
                    $checkRecordIsExist = DB::table(MEMBERSHIP)->where(array('profileId' => $userDetails[0]->id))->whereYear('membershipYear', '=', $year)->count();
                    if(!empty($checkRecordIsExist)){
                        return response()->json(['message'=>'You already have taken membership in '.$year.'.', 'code' => GENERAL_ERROR]);
                    } 
                    
                    if($getParam['select_membership'] == 'Renew'){
                        if(empty($getAllEmail)){
                            // $getParam['mim_number'] = $userDetails[0]->memberNumber; // replace value
                            if($userDetails[0]->memberNumber){
                                $getParam['mim_number'] = $userDetails[0]->memberNumber; // replace value 
                            } else {
                                $getParam['mim_number'] = ''; // replace value
                            }                
                            $getParam['select_membership'] = "Renew"; // replace value
                            $getParam['profileIds'] = $userDetails[0]->id; // replace value
                        }
                    }     
                }
            }

            $prefix = "MIM";
            // echo $memberNumber = UserHelper::generateMemberNo($prefix); die;
            $dateofBirth = UserHelper::changeDateFormat($getParam['dateofBirth']);
            if($getParam['select_membership'] == "New"){
                // check for email
                // $getemailCount = $this->commonModel->getRecordCountByCondition(PROFILE, array('profileEmail' =>  $getParam['profileEmail']));                    
                // if(empty($getemailCount)){
                    // if($getParam['membershipCategory'] == "MIM"){
                        // $prefix = "MIM";
                    // } else {
                        // $prefix = "IBC";
                    // }

                   /* if(!empty($getParam['special_condition'])){
                        $consessionType = 1;
                    } else {
                        $consessionType = 0;
                    }*/
                    if(!empty($getParam['privacyAcknowledged'])){
                        $privacyAcknowledged = 1;
                    } else {
                        $privacyAcknowledged = 0;
                    }

                    
                    // check membership type if GA then not allocate seat
                    if($getParam['membership_type']==1 || $getParam['membership_type']==2 || $getParam['membership_type']==7){
                        
                        // check bay, row and seats is already exist or not
                        /*$checkExist = DB::table(SEAT_ALLOCATION)->where(array('Bay' => $getParam['select_bay'], 'Row' => $getParam['select_row'], 'seatNumber' => $baySeats, 'reserved' => 1))->count();
                        if(!empty($checkExist)){
                            return response()->json(['message'=>'Seat details already booked.', 'code' => GENERAL_ERROR]);
                        }*/
                        // put data into session
                        $putSessionData = array(
                            'select_membership'  =>  $getParam['select_membership'],
                            // 'memberNumber'  => $memberNumber,
                            'prefix' => $prefix,
                            'firstName' =>  $getParam['firstName'],
                            'lastName'  =>  $getParam['lastName'],
                            'contactNumber' => $getParam['contactNumber'],
                            'dateofBirth'   =>  $dateofBirth,
                            'state' =>  $getParam['state'],
                            'suburb' => $getParam['suburb'],
                            'country' => $getParam['country'],
                            'postCode'  =>  $getParam['postCode'],
                            'profession' => $getParam['profession'],
                            'addressLine1'  =>  $getParam['addressLine1'],
                            'addressLine2'  =>  $getParam['addressLine2'],
                            'yearFirstJoined' => $getParam['yearFirstJoined'],
                            'membershipBuyerId' => $sessionData['user_id'],
                            'profileEmail' => $getParam['profileEmail'],
                            'idAttachment' => $getParam['idAttachment'],
                            'membershipType' => $getParam['membership_type'],
                            'startDate' => $startDate,
                            'endDate' => $endDate,
                            'membershipYear' => $membershipYear,
                            'privacyAcknowledged' => $privacyAcknowledged,
                            'Bay'   =>  $getParam['select_bay'],
                            'Row'   =>  $getParam['select_row'],
                            'seatNumber' => $baySeats,
                            'createdOn' =>  date('Y-m-d H:i:s'),
                            'changedOn' =>  date('Y-m-d H:i:s'),
                            'createdBy' => $sessionData['user_id'],
                            'deleted' => 1,
                            'paidFees' => $paidFees,
                            'membershipFees' => $membershipFees
                        );

                        $checkSessionData = UserHelper::checkout_session_data();
                        if(!empty($checkSessionData)){
                            if(count($checkSessionData) > 0){
                                foreach ($checkSessionData as $value) {
                                    $request->session()->push('checkout_session_data', $putSessionData);
                                    return response()->json(['message'=>'You have created the membership successfully.', 'code' => SUCCESS]);
                                }
                            }                            
                        } else {
                            $request->session()->put('checkout_session_data.0', $putSessionData);
                        }                            
                    } else  {
                        // put data into session
                        $putSessionData = array(
                                            'select_membership'  =>  $getParam['select_membership'],
                                            // 'memberNumber'  => $memberNumber,
                                            'prefix' => $prefix,
                                            'firstName' =>  $getParam['firstName'],
                                            'lastName'  =>  $getParam['lastName'],
                                            'contactNumber' => $getParam['contactNumber'],
                                            'dateofBirth'   =>  $dateofBirth,
                                            'state' =>  $getParam['state'],
                                            'suburb' => $getParam['suburb'],
                                            'country' => $getParam['country'],
                                            'postCode'  =>  $getParam['postCode'],
                                            'profession' => $getParam['profession'],
                                            'addressLine1'  =>  $getParam['addressLine1'],
                                            'addressLine2'  =>  $getParam['addressLine2'],
                                            'yearFirstJoined' => $getParam['yearFirstJoined'],
                                            'membershipBuyerId' => $sessionData['user_id'],
                                            'profileEmail' => $getParam['profileEmail'],
                                            'idAttachment' => $getParam['idAttachment'],
                                            'membershipType' => $getParam['membership_type'],
                                            'startDate' => $startDate,
                                            'endDate' => $endDate,
                                            'membershipYear' => $membershipYear,
                                            'privacyAcknowledged' => $privacyAcknowledged,
                                            'paidFees' => $paidFees,
                                            'membershipFees' => $membershipFees
                                        );

                        $checkSessionData = UserHelper::checkout_session_data();
                        if(!empty($checkSessionData)){
                            if(count($checkSessionData) > 0){
                                foreach ($checkSessionData as $value) {
                                    $request->session()->push('checkout_session_data', $putSessionData);
                                    return response()->json(['message'=>'You have created the membership successfully.', 'code' => SUCCESS]);
                                }
                            }                            
                        } else {
                            $request->session()->put('checkout_session_data.0', $putSessionData);
                        }
                    }
                    return response()->json(['message'=>'You have created the membership successfully.', 'code' => SUCCESS]); 
                /*} else {
                    return response()->json(['message'=>'Email already exists.', 'code' => GENERAL_ERROR]);
                }*/
                               
            } else {
				
				$year = '2018';
				$checkRecordIsExist = DB::table(MEMBERSHIP)->where(array('profileId' => $getParam['profileIds'],'membershipYear' => $year))->count();
				if(!empty($checkRecordIsExist)){
					return response()->json(['message'=>'You already have taken membership in '.$year.'.', 'code' => GENERAL_ERROR]);
				} 
                // get request parameters
                $mim_number = $getParam['mim_number'];
                $profileId = $getParam['profileIds'];
                if(empty($mim_number)){
                    $mim_number = null;
                }
                // check for membership number
                $getRecord = DB::table(PROFILE)->select('id')->where(array('id' => $profileId, 'memberNumber' => $mim_number))->get();
                if(!empty($getRecord)){
                } else {
                    return response()->json(['message'=>'You entered invalid MIM/IBC number.', 'code' => GENERAL_ERROR]);
                }

                /*if(!empty($getParam['special_condition'])){
                    $consessionType = 1;
                } else {
                    $consessionType = 0;
                }*/

                if(!empty($getParam['privacyAcknowledged'])){
                    $privacyAcknowledged = 1;
                }else {
                    $privacyAcknowledged = 0;
                }

                               
                // check membership type if GA then not allocate seat
                if($getParam['membership_type']==1 || $getParam['membership_type']==2 || $getParam['membership_type']==7){
                                            
                    $putSessionData = array(
                        'select_membership'  =>  $getParam['select_membership'],
                        'memberNumber'    =>  $getParam['mim_number'],
                        'prefix' => $prefix,
                        'firstName' =>  $getParam['firstName'],
                        'lastName'  =>  $getParam['lastName'],
                        'contactNumber' => $getParam['contactNumber'],
                        'dateofBirth'   =>  $dateofBirth,
                        'state' =>  $getParam['state'],
                        'suburb' => $getParam['suburb'],
                        'country' => $getParam['country'],
                        'postCode'  =>  $getParam['postCode'],
                        'profession' => $getParam['profession'],
                        'addressLine1'  =>  $getParam['addressLine1'],
                        'addressLine2'  =>  $getParam['addressLine2'],
                        'yearFirstJoined' => $getParam['yearFirstJoined'],
                        'membershipBuyerId' => $sessionData['user_id'],
                        'profileEmail' => $getParam['profileEmail'],
                        'idAttachment' => $getParam['idAttachment'],
                        'membershipType' => $getParam['membership_type'],
                        'startDate' => $startDate,
                        'endDate' => $endDate,
                        'membershipYear' => $membershipYear,
                        'profileId' => $profileId,
                        'privacyAcknowledged' => $privacyAcknowledged,
                        'Bay'   =>  $getParam['select_bay'],
                        'Row'   =>  $getParam['select_row'],
                        'seatNumber' => $baySeats,
                        'createdOn' =>  date('Y-m-d H:i:s'),
                        'changedOn' =>  date('Y-m-d H:i:s'),
                        'createdBy' => $sessionData['user_id'],
                        'deleted' => 1,
                        'paidFees' => $paidFees,
                        'membershipFees' => $membershipFees,
                        'memberShipId' => $getParam['memberShipId'],
                    );

                    // forget session key
                    // $request->session()->forget('checkout_session_data');
                    // put data into session
                    $checkSessionData = UserHelper::checkout_session_data();
                    if(!empty($checkSessionData)){
                        if(count($checkSessionData) > 0){
                            foreach ($checkSessionData as $value) {
                                $request->session()->push('checkout_session_data', $putSessionData);
                                return response()->json(['message'=>'You have created the membership successfully.', 'code' => SUCCESS]);
                            }
                        }                            
                    } else {
                        $request->session()->put('checkout_session_data.0', $putSessionData);
                    }

                } else {
                    // forget session key
                    // $request->session()->forget('checkout_session_data');
                    // put data into session
                    $putSessionData = array(
                        'select_membership'  =>  $getParam['select_membership'],
                        'memberNumber'    =>  $getParam['mim_number'],
                        'prefix' => $prefix,
                        'firstName' =>  $getParam['firstName'],
                        'lastName'  =>  $getParam['lastName'],
                        'contactNumber' => $getParam['contactNumber'],
                        'dateofBirth'   =>  $dateofBirth,
                        'state' =>  $getParam['state'],
                        'suburb' => $getParam['suburb'],
                        'country' => $getParam['country'],
                        'postCode'  =>  $getParam['postCode'],
                        'profession' => $getParam['profession'],
                        'addressLine1'  =>  $getParam['addressLine1'],
                        'addressLine2'  =>  $getParam['addressLine2'],
                        'yearFirstJoined' => $getParam['yearFirstJoined'],
                        'membershipBuyerId' => $sessionData['user_id'],
                        'profileEmail' => $getParam['profileEmail'],
                        'idAttachment' => $getParam['idAttachment'],
                        'membershipType' => $getParam['membership_type'],
                        'startDate' => $startDate,
                        'endDate' => $endDate,
                        'membershipYear' => $membershipYear,
                        'profileId' => $profileId,
                        'privacyAcknowledged' => $privacyAcknowledged,
                        'paidFees' => $paidFees,
                        'membershipFees' => $membershipFees,
                        'memberShipId' => $getParam['memberShipId'],
                    );

                    $checkSessionData = UserHelper::checkout_session_data();
                    if(!empty($checkSessionData)){
                        if(count($checkSessionData) > 0){
                            foreach ($checkSessionData as $value) {
                                $request->session()->push('checkout_session_data', $putSessionData);
                                return response()->json(['message'=>'You have created the membership successfully.', 'code' => SUCCESS]);
                            }
                        }                            
                    } else {
                        $request->session()->put('checkout_session_data.0', $putSessionData);
                    }
                }
                // print_r($_SESSION);die;
                return response()->json(['message'=>'You have created the membership successfully.', 'code' => SUCCESS]);
            }                 
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }  
    }

    /**
    * method to get user profile record
    * @param Request
    *
    */
    public function getUserProfileRecord(Request $request)
    {
        if(UserHelper::user_session_data('is_user_logged_in')){          
            // get Ajax request 
            $getParam = request()->all();
            $getUserDetails = $this->commonModel->getSingleRecord(PROFILE, array('id' => $getParam['profile_id']));
            $profileId = $getUserDetails->id;

            // DB::enableQueryLog();
            $memebershipData = DB::table(MEMBERSHIP)->select('memberShips.id', 'membershipType', 'membershipStatus', 'Bay', 'Row', 'seatNumber', 'membershipYear')->leftJoin('seatAllocations', 'memberShips.id', '=', 'seatAllocations.memberShipId')->where('profileId', $profileId)->get();
            // dd(DB::getQueryLog());
            // die;
            $membershipValue = $memebershipData[0]->membershipType;
            $membershipStatus = $memebershipData[0]->membershipStatus;

            if($membershipValue == 1 || $membershipValue == 2 || $membershipValue == 7){
                $showMembershipType = 'show';
            } else {
                $showMembershipType = 'hide';
            }         
            
            if($memebershipData[0]->Bay){
                $Bay = $memebershipData[0]->Bay;                              
            } else {
                $Bay = 'null';
            }

            if($memebershipData[0]->Row){
                $rowNo = $memebershipData[0]->Row; 
                $Row = UserHelper::getRowNumber($Bay, $rowNo);                             
            } else {
                $Row = 'null';
            }

            if($memebershipData[0]->seatNumber){
                $seatNumber = $memebershipData[0]->seatNumber;                
                $result = UserHelper::getSeatNumber($Bay, $rowNo, $seatNumber);
            } else {
                $result = 'null';
            }

            if($memebershipData[0]->membershipYear){
                $membershipYear = $memebershipData[0]->membershipYear;
            } else {
                $membershipYear = "null";
            }

            if($memebershipData[0]->id){
                $memberShipsId = $memebershipData[0]->id;
            }


            if(!empty($getUserDetails)){ 
                if($getUserDetails->dateofBirth == "0000-00-00" || $getUserDetails->dateofBirth == null){
                    $dateofBirth = '';
                } else {
                    $dateofBirth = UserHelper::convertDateFormat($getUserDetails->dateofBirth);
                }
                   
                return response()->json(['code' => SUCCESS, 'user_data' => $getUserDetails, 'showMembershipType' => $showMembershipType, 'membershipValue' => $membershipValue, 'membershipStatus' => $membershipStatus, 'Bay' => $Bay, 'Row' => $Row, 'seatNumber' => $result, 'memberShipsId' => $memberShipsId, 'dateofBirth' => $dateofBirth, 'membershipYear' => $membershipYear]);
            } else {
                return response()->json(['code' => ERROR, 'error' => ERROR_MSG]);
            }
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }  
    }

    /**
    * update Membership Category
    * @param profileId and category type
    * @return updated membership number
    */
    public function updateMembershipCategory(Request $request)
    {
        if(UserHelper::user_session_data('is_user_logged_in')){
            $getParam = request()->all();
            $getMembership = DB::table(PROFILE)->select('memberNumber')->where(['id' => $getParam['profileId']])->get();
            if(!empty($getMembership[0]->memberNumber)){
                $memberNumber = $getMembership[0]->memberNumber;
                $newMemNumber = substr_replace($memberNumber, $getParam['category'] ,0, 3); // replace first 3 digits

                DB::table(PROFILE)->where('id', $getParam['profileId'])->update(['memberNumber' => $newMemNumber]);
                return response()->json(['code' => SUCCESS, 'user_data' => $newMemNumber]);
            } else {
                return response()->json(['code' => ERROR, 'message' => ERROR_MSG]);
            }           

        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }
    }

    /**
    * Image upload
    * @param ajax request
    * @return image name
    */
    public function uploadImage(Request $request)
    {
        if(UserHelper::user_session_data('is_user_logged_in')){  
            $sessionData = UserHelper::user_session_data();
            if($request->ajax()){
                $url = URL::to('/');          
                $upload_file = request()->all();
                $image = $request->file('upload_file'); 
                $name =  $image->getClientOriginalName();
                // image request process
                if($request->hasFile('upload_file')){
                    $name = time().'_'.$image->getClientOriginalName();
                    // $destinationPath =$url.'/'.WEB_UPLOAD_PATH;
                    $destinationPath = public_path('uploads/web_images/');
                    $image->move($destinationPath, $name);

                    return response()->json(['code'=>SUCCESS, 'attached_image'=>$name, 'message' => 'Image uploaded successfully.']);      
                }
            }
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        } 
    }

    public function editCartProfile(Request $request)
    {   
        if($request->ajax()){
            $getParam = request()->all();
            // p($getParam);
            $result = $request->session()->get('checkout_session_data.'.$getParam['sessionKey']);
			if(!empty($result)){
                $dateofBirth = UserHelper::convertDateFormat($result['dateofBirth']);
				return response()->json(['code'=>SUCCESS,  'message' => $result, 'details' => ($result), 'dob' => $dateofBirth]);
			} else {
				return response()->json(['code'=>ERROR,  'message' => "No data found."]);
			}
        }   
    }
	
	public function saveCartProfile(Request $request)
	{
		 if($request->ajax()){
            $getParam = request()->all();
			//$result = $request->session()->get('checkout_session_data');
			//p($result);die;
			$result = $request->session()->get('checkout_session_data.'.$getParam['sessionKey']);
            // p($result); die;
			$putSessionData = array(
                            'firstName' =>  $getParam['firstName'],
                            'lastName'  =>  $getParam['lastName'],
                            'contactNumber' => $getParam['contactNumber'],
                            'dateofBirth'   =>  $getParam['dateofBirth'],
                            'state' =>  $getParam['state'],
                            'suburb' => $getParam['suburb'],
                            'country' => $getParam['country'],
                            'postCode'  =>  $getParam['postCode'],
                            'profession' => $getParam['profession'],
                            'addressLine1'  =>  $getParam['addressLine1'],
                            'addressLine2'  =>  $getParam['addressLine2'],
							'profileEmail'  =>  $getParam['profileEmail']
                        );
			 $putSessionData['select_membership'] = $result['select_membership'];
			 $putSessionData['prefix'] = $result['prefix'];
			 $putSessionData['yearFirstJoined'] = $result['yearFirstJoined'];
			 $putSessionData['membershipBuyerId'] = $result['membershipBuyerId'];
			 $putSessionData['idAttachment'] = $result['idAttachment'];
			 $putSessionData['membershipType'] = $result['membershipType'];
			 $putSessionData['startDate'] = $result['startDate'];
			 $putSessionData['endDate'] = $result['endDate'];
			 $putSessionData['membershipYear'] = $result['membershipYear'];
			 $putSessionData['privacyAcknowledged'] = $result['privacyAcknowledged'];
			 if(isset($result['Bay']) && isset($result['Row']) && isset($result['seatNumber'])){
				$putSessionData['Bay'] = $result['Bay'];
				$putSessionData['Row'] = $result['Row'];
			 	$putSessionData['seatNumber'] = $result['seatNumber'];
			 }
			 if(isset($result['profileId'])){
				$putSessionData['profileId'] = $result['profileId'];
			 }
			 if(isset($result['memberShipId'])){
				$putSessionData['memberShipId'] = $result['memberShipId'];
			 }
			  if(isset($result['createdOn'])){
				$putSessionData['createdOn'] = $result['createdOn'];
			 }
			 if(isset($result['changedOn'])){
				$putSessionData['changedOn'] = $result['changedOn'];
			 }
			 if(isset($result['createdBy'])){
				$putSessionData['createdBy'] = $result['createdBy'];
			 }
			 if(isset($result['deleted'])){
				$putSessionData['deleted'] = $result['deleted'];
			 }
			  if(isset($result['createdBy'])){
				$putSessionData['createdBy'] = $result['createdBy'];
			 }
			 if(isset($result['deleted'])){
				$putSessionData['deleted'] = $result['deleted'];
			 }
			 //$putSessionData['createdOn'] = $result['createdOn'];
			 //$putSessionData['changedOn'] = $result['changedOn'];
			 //$putSessionData['createdBy'] = $result['createdBy'];
			 //$putSessionData['deleted'] = $result['deleted'];
			 $putSessionData['paidFees'] = $result['paidFees'];
			 $putSessionData['membershipFees'] = $result['membershipFees'];
			 $array = Session::pull('checkout_session_data',[]);
             unset($array[$getParam['sessionKey']]);
			 $array = array_values($array);
			 $cnt = count($array);
			 $array[$cnt] = $putSessionData;
			 Session::put('checkout_session_data',$array);
			 //p($_SESSION['checkout_session_data']);die;
            // p($array);die;
			 //$request->session()->push('checkout_session_data', $putSessionData);
			return response()->json(['code'=>SUCCESS]);

        } 
	}

    /**
    * Checkout method
    * redirect to checkout page
    */
    public function checkout()
    {     
        // p(UserHelper::checkout_session_data()); die;
        if(UserHelper::user_session_data('is_user_logged_in')){             
            $data = array();
            $data['title'] = 'Checkout';

            $sessionData = UserHelper::user_session_data('user_id');
            /*$data['user_details'] = DB::table(USER)->select('paidFees', 'membershipFees')->where("id", $sessionData['user_id'])->first();*/

            return view('home.checkout', $data);       
        } else {
            return redirect('home')->with('message', trans('messages.alert_session_expired'));
        }        
    }

    public function addtocart()
    {        
        if(UserHelper::user_session_data('is_user_logged_in')){             
            $data = array();
            $data['title'] = 'Cart Details';            
            $data['checkoutSessionData'] = UserHelper::checkout_session_data();

            $sessionData = UserHelper::user_session_data('user_id');
           /* $data['user_details'] = DB::table(USER)->select('paidFees', 'membershipFees')->where("id", $sessionData['user_id'])->first();*/

            return view('home.addtocart', $data);       
        } else {
            return redirect('home')->with('message', trans('messages.alert_session_expired'));
        } 
    }

    public function removeCardItem($key)
    {        
        if(UserHelper::user_session_data('is_user_logged_in')){                  
            // unset session key 
            $array = Session::pull('checkout_session_data',[]);
            unset($array[$key]);
            Session::put('checkout_session_data',$array);
            return redirect('cartdetails')->with('message', trans('messages.remove_item_from_cart'));
        } else {
            return redirect('home')->with('message', trans('messages.alert_session_expired'));
        } 
    }
    

    /**
    * Get Membership status
    * @param null
    * @return get total active membership of user
    */ 
    public function membershipStatus()
    {
        if(UserHelper::user_session_data('is_user_logged_in')){ 
            $data = array();
            $data['title'] = 'Memberships';
            $sessionData = UserHelper::user_session_data();                      

            // get user profile id
            $mem_purchase_id = $this->commonModel->getAllConditionalsRecordsByFields(PROFILE, array('membershipBuyerId' => $sessionData['user_id']), array('id'));

            if(!empty($mem_purchase_id)){
                $profieId = array();
                foreach ($mem_purchase_id as $ids) {
                    $profileId[] = $ids->id;
                }
                // get data from membership
                $data['memebershipData'] = DB::table(MEMBERSHIP)->whereIn('profileId', $profileId)->get();
            }

            return view('home.membership_status', $data);            
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }         
    }

    /**
    * Delink memberships
    * @param member profile id
    * @return return its status
    */
    public function deLinkUserMembership(Request $request)
    {
        $getParam = request()->all();
        $deLinkMemberProfileId = $getParam['deLinkMemberProfileId'];
        $deLinkComment = $getParam['deLinkComment'];

        if($deLinkMemberProfileId){

            $getProfileDetails = DB::table(PROFILE)->select('firstName', 'lastName', 'profileEmail')->where(array('id' => $deLinkMemberProfileId))->get();

            $membershiData = DB::table(MEMBERSHIP)->select('id')->where(array('profileId' => $deLinkMemberProfileId))->get();
            if($membershiData){
                DB::table(MEMBERSHIP)->where('id', $membershiData[0]->id)->update(['membershipStatus' => 'Inactive']);
                DB::table(SEAT_ALLOCATION)->where('memberShipId', $membershiData[0]->id)->update(['reserved' =>0, 'deleted' => 0]);                
            }

            $userName = ucfirst($getProfileDetails[0]->firstName.' '.$getProfileDetails[0]->lastName);
            $email = $getProfileDetails[0]->profileEmail;
            
            if($deLinkComment){
                $html = sprintf(DE_LINK_MESSAGE_COMMNET, $userName, $deLinkComment);
                try{
                    Mail::send(array(), array(), function ($message) use ($email, $html) {
                    $message->to($email)
                        ->subject(DE_LINK_SUBJECT)
                        ->from(FROM_EMAIL)
                        ->setBody($html, 'text/html');
                    });
                }
                catch(\Exception $e){
                    return redirect('home')->with('message', trans('messages.unable_to_send_email'));
                } 
            } else {
               $html = sprintf(DE_LINK_MESSAGE, $userName);
                try{
                    Mail::send(array(), array(), function ($message) use ($email, $html) {
                    $message->to($email)
                        ->subject(DE_LINK_SUBJECT)
                        ->from(FROM_EMAIL)
                        ->setBody($html, 'text/html');
                    });
                }
                catch(\Exception $e){
                    return redirect('home')->with('message', trans('messages.unable_to_send_email'));
                } 
            }           
            
            return response()->json(array('code' => SUCCESS, 'message' => 'Membership de-link successfully.'));
        } else {
            return response()->json(array('code' => ERROR, 'message' => ERROR_MSG));
        }        
    }

    public function privacy()
    {
        if(UserHelper::user_session_data('is_user_logged_in')){ 
            return view('home.privacy');            
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }  
    }

    public function termsAndcondition()
    {
        if(UserHelper::user_session_data('is_user_logged_in')){ 
            return response()->download('public/Terms&Condition.pdf');    
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        }        
    }

    public function privacyAndpolicy()
    {
        if(UserHelper::user_session_data('is_user_logged_in')){ 
            return response()->download('public/PrivacyPolicy.pdf');    
        } else {
            return redirect('/')->with('message', trans('messages.alert_session_expired'));
        } 
    }


    public function importExport()
    {
        return view('web.importExport');
    }
    
    public function importExcel()
    {
        $tommorow = new Carbon;
        if(Input::hasFile('import_file')){
            $path = Input::file('import_file')->getRealPath();
            $data = Excel::load($path, function($reader) {
            })->get();
            // $i=1;
            if(!empty($data) && $data->count()){
                foreach ($data as $key => $value) {
                    // p($value); die;
                    if($value['dob']){  // check is date is valid or not
                        if(DateTime::createFromFormat('Y-m-d H:i:s', $value['dob']) !== FALSE && $value['dob'] !='1970-01-01') {
                            $dateofBirth = $value['dob'];
                        }
                    } else {
                        $dateofBirth = '';
                    }

                    $memberNumber = $value['mim_number'];                                
                    // $membershipCategory = substr($memberNumber,0, 3);
                    $bay = $value['bay'];
                    $row = $value['row'];
                    $seat = $value['seat'];
                    $yearFirstJoined = $value['year_first_joined']; 
                    if($value['mim_cat'] == 'Master')
                    {                               
                        $userName = ucfirst($value['first_name'].' '.$value['last_name']);
                        $rand_string = UserHelper::quickRandom(9);
                        // create user
                        $insertedUserId = $this->commonModel->addRecord(USER, array('userName' => $userName, 'email' => $value['email'], 'password' => bcrypt($rand_string), 'emailConfirmed' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')));
                        
                        // create profile
                        $profileData = array(
                                            'memberNumber'  => $value['mim_number'],
                                            'firstName' =>  $value['first_name'],
                                            'lastName'  =>  $value['last_name'],
                                            'contactNumber' => $value['phone'],
                                            'dateofBirth'   =>  $dateofBirth,
                                            'addressLine1'  =>  $value['address_1'],
                                            'addressLine2'  =>  $value['address_2'],
                                            'suburb' => $value['suburb'],
                                            'state' =>  $value['state'],
                                            'country' => $value['country'],
                                            'postCode'  =>  $value['postcode'],
                                            'yearFirstJoined' => $yearFirstJoined,
                                            'userId' => $insertedUserId,
                                            'membershipBuyerId' => $insertedUserId,
                                            'profileEmail' => $value['email'],
                                            'paymentStatus' => 1
                                        );
                        $profileId = $this->commonModel->addRecord(PROFILE, $profileData);
                    } 
                    else {
                        $parent_mim = $value['parent_mim'];
                        if(!empty($parent_mim)){
                            $getUserDetail = DB::table(PROFILE)->select('userId')->where(['memberNumber' => $parent_mim])->get();
                            // $getUserId = $getUserDetail[0]->userId;
                            $insertedUserId = $getUserDetail[0]->userId;

                            // create profile
                            $profileData = array(
                                                'memberNumber'  => $value['mim_number'],
                                                'firstName' =>  $value['first_name'],
                                                'lastName'  =>  $value['last_name'],
                                                'contactNumber' => $value['phone'],
                                                'dateofBirth'   =>  $dateofBirth,
                                                'addressLine1'  =>  $value['address_1'],
                                                'addressLine2'  =>  $value['address_2'],
                                                'suburb' => $value['suburb'],
                                                'state' =>  $value['state'],
                                                'country' => $value['country'],
                                                'postCode'  =>  $value['postcode'],
                                                'yearFirstJoined' => $yearFirstJoined,
                                                'membershipBuyerId' => $insertedUserId,
                                                'profileEmail' => $value['email'],
                                                'paymentStatus' => 1
                                            );
                            $profileId = $this->commonModel->addRecord(PROFILE, $profileData);
                        }
                    }


                    // check if bay, row and seats is not empty
                    if(!empty($bay) && !empty($row) && !empty($seat)){
                        $membershipData = array(
                                            'membershipType' => 1,
                                            'membershipYear' => $yearFirstJoined,          
                                            'startDate' => date('Y-m-d H:i:s'),
                                            'endDate' => "2018-03-01 00:00:00",
                                            'profileId' => $profileId,
                                            'privacyAcknowledged' => 1,
                                            'membershipStatus' => 'Active'
                                        );
                        // add membership
                        $membershipId = $this->commonModel->addRecord(MEMBERSHIP, $membershipData);

                        //  seat allocation data
                        $seatAlocationData = array(
                                                'Bay'   =>  $bay,
                                                'Row'   =>  $row,
                                                'seatNumber' => $seat,
                                                'memberShipId' => $membershipId,
                                                'reserved' => 1,
                                                'createdBy' => $insertedUserId,
                                                'createdOn' =>  date('Y-m-d H:i:s'),
                                                'changedOn' =>  "2018-01-10 00:00:00",
                                                'deleted' => 1
                                            );
                        // add Seat Allocation
                        $seatAllocationId = $this->commonModel->addRecord(SEAT_ALLOCATION, $seatAlocationData);
                       
                        // insert record into membershipHistory table
                        DB::table(HISTORY)->insertGetId(array('profileId' => $profileId, 'membershipId' => $membershipId, 'membershipType' => 1, 'bay' => $bay, 'row' => $row, 'seatNumber' => $seat, 'membershipBuyerId' => $insertedUserId,  'yearFirstJoined' => $yearFirstJoined, 'createdDate' => date('Y-m-d H:i:s')));

                    } else {
                        // membershipType for GA
                        $membershipData = array(
                                            'membershipType' => 3,
                                            'membershipYear' => $yearFirstJoined,
                                            'startDate' => date('Y-m-d H:i:s'),
                                            'endDate' => "2018-03-01 00:00:00",
                                            'profileId' => $profileId,
                                            'privacyAcknowledged' => 1,
                                            'membershipStatus' => 'Active'
                                        );
                        // add membership
                        $membershipId = $this->commonModel->addRecord(MEMBERSHIP, $membershipData);

                        // insert record into membershipHistory table
                        DB::table(HISTORY)->insertGetId(array('profileId' => $profileId, 'membershipId' => $membershipId, 'membershipType' => 3, 'membershipBuyerId' => $insertedUserId, 'yearFirstJoined' => $yearFirstJoined, 'createdDate' => date('Y-m-d H:i:s')));
                    }
                    
                }
                dd('Insert Record successfully.');
            }
        }
    }

    /*foreach ($data as $key => $value) {
        if($i==989){
            dd('Insert Record successfully.');
        }
        $i++;
        $bayId = DB::table(BAY)->insertGetId(array('bayNumber' => $value->bay));
        $rowId = DB::table(ROW)->insertGetId(array('bayNumber' => $value->bay, 'rowNumber' => $value->row));

        if(!empty($value->rs)){
            $reserved = $value->rs;
        } else {
            $reserved = 0;
        }
        $seatId = DB::table(SEATS)->insertGetId(array('bayNumber' => $value->bay, 'rowNumber' => $value->row, 'seatNumber' => $value->number));

        if($value->first_name == "IBC" || $value->first_name == "JP" || $value->first_name == "Camera"){

            // insert into user table 
            // insert into profile table
            $profileId = DB::table(PROFILE)->insertGetId(array('memberNumber' => $value->mim_num, 'firstName' => $value->first_name, 'lastName' => $value->last_name, 'yearFirstJoined' => date('Y')));

            // insert into membership table
            $membershipId = DB::table(MEMBERSHIP)->insertGetId(array('membershipType' => 1, 'membershipYear' => date('Y') 'startDate' => date('Y-m-d'), 'endDate' => '2019-03-01', 'profileId' => $profileId, 'privacyAcknowledged' => 1, 'membershipStatus' => 'Active'));

            // insert into seatAllocations table
            $seatAllocations = DB::table(SEAT_ALLOCATION)->insertGetId(array('Bay' => $value->bay, 'Row' => $value->row, 'seatNumber' => $value->number, 'memberShipId' => $membershipId, 'reserved' => $reserved, 'createdOn' => date('Y-m-d'), 'changedOn' => date('Y-m-d'), 'deleted' => 1));

            // insert record into membershipHistory table

            DB::table(HISTORY)->insertGetId(array('profileId' => $profileId, 'membershipId' => $membershipId, 'membershipType' => 1, 'bay' => $value->bay, 'row' => $value->row, 'seatNumber' => $value->number, 'yearFirstJoined' => date('Y'), 'createdDate' => date('Y-m-d H:i:s')));
        }
    }*/
}
