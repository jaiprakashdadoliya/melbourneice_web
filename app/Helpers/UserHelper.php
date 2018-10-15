<?php // Code within app\Helpers\UserHelper.php

/*
 * Common helper file, which is autoloaded in application
 * Author: Jaiprakash Dadoliya
 * Author Email: @gmail.com  
 */

namespace App\Helpers;

use Session;
use DB;
use Config;
use PayPal\Api\Agreement;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use App\Models\CommonModel;
/*use Request;
use Schema;
use Cache;
use DB;
use Route;
use Validator;*/

class UserHelper
{   
    public static function myId() {
		return Session::get('admin_id');
    }

    public static function myName() {
    	return Session::get('admin_name');
    }

    public static function getProfilePic() {
        $id = Session::get('admin_id');
        $data = DB::table(ADMIN_USER)->where('id', $id)->first(); 
        if ( is_null($data) ) {
            return false;
        } else {
            return $data->photo;
        }
        
    }

    public static function user_session_data()
    {
    	return $data = Session::get('user_session_data');
    }

    public static function get_profile_name()
    {
        $sessionData = Session::get('user_session_data');
        $userData = DB::table(USER)->where('id', $sessionData['user_id'])->first();
        if(!empty($userData)){
            return $userData->userName;
        } else {
            return "User";            
        }
    }

    public static function get_profile_email()
    {
        $sessionData = Session::get('user_session_data');
        $userData = DB::table(USER)->where('id', $sessionData['user_id'])->first();
        return $userData->email;
    }

    public static function get_member_id()
    {
        $sessionData = Session::get('user_session_data');
        $userData = DB::table(USER)->where('id', $sessionData['user_id'])->first();
        return $userData->email;
    }

    public static function get_email($profileId)
    {
        $userData = DB::table(PROFILE)->where('id', $profileId)->first();
        $email = $userData->profileEmail;
        return $email;
    }

    public static function get_name($profileId)
    {
        $userData = DB::table(PROFILE)->where('id', $profileId)->first();
        $firstName = $userData->firstName;
        $lastName = $userData->lastName;
        if(!empty($firstName) && !empty($lastName)){
            return $fullName = ucfirst($firstName.' '.$lastName);
        } else {
            return "N/A";
        }
    }


    public static function checkout_session_data()
    {
        return $data = Session::get('checkout_session_data');
    }

    public static function check_bay_and_seat()
    {
        return $data = Session::get('check_bay_and_seat');   
    }

    public static function getBillingAgreement($createdAgreementId)
    {
        // private $_api_context;

        // setup PayPal api context
        $paypal_conf = Config::get('paypal');
        $api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
        $api_context->setConfig($paypal_conf['settings']);
        try {
            $agreement = Agreement::get($createdAgreementId, $api_context);
        } catch (Exception $ex) {
            exit(1);
        }
        return $agreement->state;
        // Cancelled, Active
    }

    public static function getSeatNumber($bayNo, $rowNo, $seatNumber)
    {       
        $commonModel = new CommonModel();
        //  get seats from seatAllocation table
        $seatAllocations = $commonModel->getAllRecordsBycondition(SEAT_ALLOCATION, array('Bay' => $bayNo, 'Row' => $rowNo, 'reserved' => 1, 'deleted' => 1)); 

        $noSeats = 0;
        $bookedSeat = array();
        if(!empty($seatAllocations)){
            foreach ($seatAllocations as $seatAllocation) {
                $bookedSeat[] = $seatAllocation->seatNumber;
            }
        } 
        else {
            if(!empty($seatNumber)){
                $seatAllocations = $commonModel->getAllRecordsBycondition(SEAT_ALLOCATION, array('Bay' => $bayNo, 'Row' => $rowNo, 'reserved' => 0, 'deleted' => 0)); 
                if(!empty($seatAllocations)){
                    foreach ($seatAllocations as $seatAllocation) {
                        $bookedSeat[] = $seatAllocation->seatNumber;
                    }
                    $noSeats = 1;
                }
            }
        }

        if($noSeats==0){
            $bookedVar = "- Booked";
            $bookedSel = "selected style='font-weight:bold'";
        } else {
            $bookedVar = "";
            $bookedSel = "";
        }
        // p($bookedSeat); die;

        //  get seats from seats table
        $seats = DB::table(SEATS)->select('seatNumber')->where(array('bayNumber' => $bayNo, 'rowNumber' => $rowNo))->get();
        $seatsArray = array();
        foreach ($seats as $seat) {
            $seatsArray[] = $seat->seatNumber;
        }

        $startCount = $seatsArray[0]; // get first value
        $seatCount = count($seatsArray); // get total count

        $selectValue = '<label for="bay1231_seats" id="select_seats_label">Select Seat <span class="red">*</span></label>';
        $selectValue .=  '<select name="seatNumber" id="seatNumber" class="form-control">';
        $selectValue .= '<option selected disabled>Select Seat</option>';
                foreach ($seatsArray as $value) {
                    if(in_array($value, $bookedSeat)){
                        if($value == $seatNumber){
        $selectValue .= '<option value='.$value.' '.$bookedSel.'>'.$value.' '.$bookedVar.'</option>';
                        } else {
        $selectValue .= '<option value='.$value.' disabled style="font-weight:bold">'.$value.' - Booked</option>';
                        }
                    } else {
        $selectValue .= '<option value='.$value.'>'.$value.'</option>';
                    }
                }
                
        $selectValue .=  '</select>';

        return $selectValue;
    }

    public static function getRowNumber($bayNo, $seletedRow)
    {
        $rows = DB::table(ROW)->select('rowNumber')->where(array('bayNumber' => $bayNo))->distinct('bayNumber')->get();

        $rowNumbers = array(); 
        foreach ($rows as $row) {
            $rowNumbers[] = $row->rowNumber;
        }

        $selectValue  = '<label for="select_row" id="select_row_label"> Select Row <span class="red">*</span></label>';
        $selectValue .= '<select name="select_row" id="select_row" class="form-control">';
        $selectValue .= '<option selected disabled>Select Row</option>';
        
        foreach ($rowNumbers as $value) {
             if($value == $seletedRow){
                $select = 'selected="selected"';
             } else {
                $select = '';
             }
             $selectValue .= '<option '.$select.' value="'.$value.'">'.$value.'</option>';
         } 
        $selectValue .= '</select>';    
        return $selectValue;
    }

    /**
     * Generate a "random" alpha-numeric string.
     *
     * Should not be considered sufficient for cryptography, etc.
     *
     * @param  int  $length
     * @return string
     */
    public static function quickRandom($length = 9)
    {
        $chars =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.
            '0123456789`-=~!@#$%^&*()_+,./<>?;:[]{}\|';

        $str = '';
        $max = strlen($chars) - 1;

        for ($i=0; $i < $length; $i++)
            $str .= $chars[mt_rand(0, $max)];

        return $str;
    }

    public static function generateMemberNo($prefix)
    {
        $firstNumber = $prefix.'5001';
        $checkRecord = DB::table(PROFILE)->select('memberNumber')->where(array('memberNumber' => $firstNumber))->get();

        // p($checkRecord); die;
        if(empty($checkRecord)){
            return $firstNumber;
        } else {
            // $getLatestId = DB::table(PROFILE)->select('memberNumber')->where(array('paymentStatus' => 1))->orderBy('id', 'desc')->get();
            $getLatestId = DB::table(PROFILE)->select(DB::raw("MAX(CAST(SUBSTRING(memberNumber, 4, length(memberNumber)-3) AS UNSIGNED)) AS final"))->get();
            $getLastDigit = $getLatestId[0]->final;
            $newindex = (int)$getLastDigit + 1;
            return $MIM = $prefix.$newindex;            
        }
    }

    public static function getMembershipTypes($membershipType)
    {
        $getType = DB::table(MEMBER_TYPES)->select('memberShipTypeName')->where(array('id' => $membershipType))->get();
        if(!empty($getType)){
            return $getType[0]->memberShipTypeName;            
        } else {
            return 'N/A';
        }
    }

    public static function checkIsProfileCompleted($user_id)
    {
        $result = DB::table(PROFILE)->whereNotNull('firstName')
                ->whereNotNull('lastName')
                ->whereNotNull('contactNumber')
                ->whereNotNull('addressLine1')
                ->whereNotNull('suburb')
                ->whereNotNull('state')
                ->whereNotNull('postCode')
                ->whereNotNull('country')
                ->where('profession', '!=', '')
                ->whereNotNull('dateofBirth')
                ->where('dateofBirth', '!=', '0000-00-00')
                ->where('userId', $user_id)->get();
        return $result;
    }

    public static function changeDateFormat($getDate)
    {
        if($getDate == "0000-00-00"){
            return "00-00-0000";
        } else {
            $dateB = strtotime($getDate);
            return $birthDate = date('Y-m-d', $dateB); 
        }
        
    }

    public static function convertDateFormat($getDate)
    {
        if($getDate == "0000-00-00" || $getDate == null){
            return "00-00-0000";
        } else {
            $dateB = strtotime($getDate);
            return $birthDate = date('d-m-Y', $dateB);
        }
    }
}