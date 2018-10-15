<?php
/*
 * Common helper file, which is autoloaded in application
 * Author: Jaiprakash Dadoliya
 * Author Email: @gmail.com  
 */

/**
* Format print_r
* @param $array
* @return print_r($array);
*/
function p($array) {
    if(isset($array)) {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    } else {
        echo 'Missing Array';
    }
}

/**
* Decode rest api request
* @param json
* @return array of json
*/
function decode_request() {
    $reqArr = json_decode(file_get_contents('php://input'), TRUE);
    if(!empty($reqArr)) {
        return $reqArr;
    } else {
        echo json_encode(array('error' => 'Empty request'));
        exit();
    }
}

/**
* check for required value in rest api
* @param Array $chk_params, $converted_array
* @return missed params
*/
function check_required_value($chk_params, $converted_array) {
    foreach ($chk_params as $param) {
        if (array_key_exists($param, $converted_array) && ($converted_array[$param] != '')) {
            $check_error = 0;
        } else {
            $check_error = array('check_error' => 1, 'param' => $param);
            break;
        }
    }
    return $check_error;
}

/**
* Crypto rand encryption
*/
function crypto_rand_secure($min, $max) {
    $range = $max - $min;
    if ($range < 1) return $min;
    $log = ceil(log($range, 2));
    $bytes = (int) ($log / 8) + 1;
    $bits = (int) $log + 1;
    $filter = (int) (1 << $bits) - 1;
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter;
    } while ($rnd >= $range);
    return $min + $rnd;
}

/**
* Generate encrypted token
* @param token length
* @return token
*/
function getToken($length) {
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet) - 1;
    for($i=0; $i < $length; $i++) {
        $token .= $codeAlphabet[crypto_rand_secure(0, $max)];
    }
    return $token;
}

/**
* Convert DB int
* @param $value
* @return $newValue
*/
function convertDBInt($value) {
    $newValue = $value*100;
    return $newValue;
}

/**
* Revert DB int
* @param $value
* @return $newValue
*/
function revertDBInt($value) {
    $newValue = $value/100;
    return round($newValue);
}

/**
* Get percentage of funded pledged
* @param $pledged, $funded
* @return $percentage
*/
function getFundedPledged($pledged, $funded) {
    $fun = $funded * 100;
    $percentage = round($fun / $pledged);
    return $percentage;
}

/**
* Get currency symbol by currency code
* @param $currency
* @return currency symbol 
*/
function getCurrencySymbol($currency) {
    $currencySymbol = ''; 
    $currencyData = currency()->find($currency);
    if(!empty($currencyData->symbol_left)) {
        $currencySymbol = $currencyData->symbol_left;
    } elseif(!empty($currencyData->symbol_right)) {
        $currencySymbol = $currencyData->symbol_right;
    }
    return $currencySymbol;
}

/**
 * Transaction Status
 * 0 = Pending
 * 1 = Completed
 * 2 = Cancelled
 * @return Array
 */
function getTransactionStatus() {
    return array(0, 1, 2);
}

/**
 * Get donation amount
 * @param $realAmount, $donatepercentage
 * @return $donationAmount
 */
function getDonationAmount($realAmount, $donatepercentage) {
    $mAmnt = $realAmount*$donatepercentage;
    $donationAmount = $mAmnt/100;
    return $donationAmount/100;
}

function week_between_two_dates($date1, $date2, $method)
{
    $first = DateTime::createFromFormat('m/d/Y', $date1);
    $second = DateTime::createFromFormat('m/d/Y', $date2);
    if($method == 'Fortnight') {
        if($date1 > $date2) return week_between_two_dates($date2, $date1, 'Fortnight');
        return floor($first->diff($second)->days/14);
    } else {
        if($date1 > $date2) return week_between_two_dates($date2, $date1, '');
        return floor($first->diff($second)->days/7);
    }
   
}
