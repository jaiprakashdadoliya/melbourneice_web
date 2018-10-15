<?php

/**
* Application globally common methods are define here
* Author: Jaiprakash Dadoliya
* Author Email: @gmail.com
*/ 

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\UserHelper;

class CommonModel extends Model
{
	/**
	* Add Record
	* @param $table, $data
	* @return last_insert_id
	*/
	public function addRecord($table, $data) {
		return DB::table($table)->insertGetId($data);
	}

	/**
	* Update Record
	* @param $table, $data, $condition
	*/
	public function updateRecords($table, $data, $condition) {
		DB::table($table)
			->where($condition)
            ->update($data);
	}

	/**
	* Delete Record
	* @param $table, $condition
	*/
	public function deleteRecords($table, $condition) {
		DB::table($table)
			->where($condition)
            ->delete();
	}
	
	/**
	* Get single record
	* @param $table, $condition
	* @return $data
	*/
	public function getSingleRecord($table, $condition) {
		$data = DB::table($table)->where($condition)->first();
		return $data;
	}

	/**
	* Get all records
	* @param $table
	* @return $data
	*/
	public function getAllRecords($table) {
		$data = DB::table($table)->get();
		return $data;
	}

	/**
	* Get all records by select field
	* @param $table, $fields
	* @return $data
	*/
	public function getAllRecordsByFields($table, $fields) {
		$data = DB::table($table)
				->select($fields)
				->get();
		return $data;
	}

	/**
	* Get records count
	* @param $table
	* @return $data
	*/
	public function getRecordCount($table) {
		$data = DB::table($table)
				->count();
		return $data;
	}

	/**
	* Get records count by condition
	* @param $table, $condition
	* @return $data
	*/
	public function getRecordCountByCondition($table, $condition) {
		$data = DB::table($table)
				->where($condition)
				->count();
		return $data;
	}

	/**
	* Get all records by condition
	* @param $table, $condition
	* @return $data
	*/
	public function getAllRecordsBycondition($table, $condition) {
		$data = DB::table($table)->where($condition)->get();
		return $data;
	}

	/**
	* Get all records by select field and condition
	* @param $table, $fields
	* @return $data
	*/
	public function getAllConditionalsRecordsByFields($table, $condition, $fields) {
		$data = DB::table($table)
				->select($fields)
				->where($condition)
				->get();
		return $data;
	}

	/**
	* Get paginate records
	* @param $table, $limit, $offset
	* @return $data
	*/
	public function getPaginateRecords($table, $limit, $offset = 0) {
		$data = DB::table($table)
				->offset($limit)
                ->limit($offset)
				->get();
		return $data;
	}

	/**
	* Get paginate records count
	* @param $table, $limit, $offset
	* @return $data
	*/
	public function getPaginateRecordsCount($table) {
		$data = DB::table($table)->count();
		return $data;
	}

	/**
	* Get paginate records by condition
	* @param $table, $condition, $limit, $offset
	* @return $data
	*/
	public function getPaginateRecordsByCondition($table, $condition, $limit, $offset = 0) {
		$data = DB::table($table)
				->where($condition)
				->offset($limit)
                ->limit($offset)
				->get();
		return $data;
	}

	/**
	* Get paginate records count by condition
	* @param $table, $condition, $limit, $offset
	* @return $data
	*/
	public function getPaginateRecordsCountByCondition($table, $condition) {
		$data = DB::table($table)
				->where($condition)
				->count();
		return $data;
	}

	/**
	* Get sorted paginate records
	* @param $table, $limit, $offset, $field, $sort
	* @return $data
	*/
	public function getSortedPaginateRecords($table, $limit, $offset = 0, $field, $sort) {
		$data = DB::table($table)
				->orderBy($field, $sort)
				->offset($limit)
                ->limit($offset)
				->get();
		return $data;
	}

	/**
	* Get sorted paginate records by condition
	* @param $table, $condition, $limit, $offset, $field, $sort
	* @return $data
	*/
	public function getSortedPaginateRecordsByCondition($table, $condition, $limit, $offset = 0, $field, $sort) {
		$data = DB::table($table)
				->where($condition)
				->orderBy($field, $sort)
				->offset($offset)
                ->limit($limit)
				->get();
		return $data;
	}

	/**
	* Get uniqueId
	* @param table name and coloum name
	* @return unique id
	*/
	public function getUniqueId($table, $primeryIdKey){
		$sessionData = UserHelper::user_session_data('user_id');
      	$uid = $sessionData['user_id'];
      	$primeryIdVal = time().rand(0, 9).$uid;
          // $idExists = $CI->adminModel->getDetails($table,array($primeryIdKey=>$primeryIdVal), $primeryIdKey);
      	$data = DB::table($table)->where(array($primeryIdKey=>$primeryIdVal))->get();
      	if(!empty($data)){
           $this->getUniqueId($table, $primeryIdKey);
      	}else{
           return $primeryIdVal;
      	}
    }

    /**
	* Custom query to check existing membership records
    */
    /*public function checkRecordIsExist()
    {
    	$year = date('Y');
    	$getResult = DB::table(MEMBERSHIP)->where('membershipType', '=', 3)->orWhere('profileId', '=', 17)->whereYear('startDate', '=', $year)->get();
    	return $getResult;
    }*/
}