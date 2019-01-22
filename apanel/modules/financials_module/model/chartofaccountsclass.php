<?php
class chartofaccountsclass extends wc_model
{
	public function __construct() 
	{
		parent::__construct();
		$this->log = new log();
	}

	public function insertData($data)
	{
		$account_code 	           = $data['segment5'];
		$account_name 		       = $data['accountname'];
		$data["generalledgercode"] = '0';
		$data["subsidiaryledgercode"] = '0';
		$data["description"]       = "";
		$data["taxacctflg"]        = "N";

		// Check for duplicate entries before inserting
		$retain = $this->db->setTable('chartaccount')
				->setValues($data)
				->setWhere("segment5 = '$account_code' AND accountname = '$account_name'")
				->getProperties();

		$result = $this->db->setProperties($retain)
						->setLimit(1)
						->runSelect()
						->getRow();	
		if ($result) {
			return false;
		} else {
			$this->db->setProperties($retain);
			
			$this->log->saveActivity("Inserted Account Code [$account_code], 
											   Account Name [$account_name]");

			//var_dump($this->db->buildInsert());
			return $this->db->runInsert();
		}
				
	}

	public function editData($data, $id)
	{
		
		$cond = "id = '$id'";

		$result = $this->db->setTable('chartaccount')
				->setValues($data)
				->setWhere($cond)
				->runUpdate();

		if($result)
		{
			$this->log->saveActivity("Update Account Code [$id]");
			return $result;
		}
				
	}

	public function retrieveData($data = array() ,$search = "", $sort = "", $addCond = "") 
	{	
		$add_query = "";
		
		
		
		$add_query = (!empty($search)) ? " (chart.segment1 LIKE '%$search%' 
										  OR chart.segment2 LIKE '%$search%'
										  OR chart.segment3 LIKE '%$search%'
										  OR chart.segment4 LIKE '%$search%'
										  OR chart.segment5 LIKE '%$search%'
										  OR chart.accountname LIKE '%$search%'
										  OR chart.accountclasscode LIKE '%$search%') " : "";
		$add_query .= (!empty($addCond)) ? ((!empty($search))? "AND ".$addCond: $addCond) : "";

		$sort 	   = (!empty($sort)) ? $sort : "accountclasscode";

		$result = $this->db->setTable('chartaccount chart')
					->setFields($data)
					->setWhere($add_query)
					->setOrderBy($sort)
					->runPagination();

		return $result;
	}

	public function check_duplicate($current)
	{
		$result =  $this->db->setTable('chartaccount')
						->setFields('COUNT(segment5) count')
						->setWhere(" segment5 = '$current'")
						->runSelect()
						->getResult();
						// echo $this->db->getQuery();
		return $result;
	}

	public function importCOA($data)
	{
		$result = $this->db->setTable('chartaccount')
				->setValuesFromPost($data)
				->runInsert();

		return $result;
	}
	
	public function fileExport($data)
	{
		$addCond 		 = (isset($data['addCond']) && !empty($data['addCond']))? 
								htmlentities($data['addCond']) : "";
		$addCond 		= 	stripslashes($addCond);
		$search          = (isset($data['search']))? htmlentities($data['search']) : "";
		$fetch_sort      = (isset($data['sort']))? htmlentities($data['sort']) : "";
		$fields 		 =  array("segment5", "accountname","accountclasscode","fspresentation", 
		"accounttype", "parentaccountcode", "accountnature","stat");

		$addCondition = "";
		$addCondition = (!empty($search)) ? " (chart.segment1 LIKE '%$search%' 
											OR chart.segment2 LIKE '%$search%'
											OR chart.segment3 LIKE '%$search%'
											OR chart.segment4 LIKE '%$search%'
											OR chart.segment5 LIKE '%$search%'
											OR chart.accountname LIKE '%$search%'
											OR chart.accountclasscode LIKE '%$search%') " : "";

		$addCondition .= (!empty($addCond)) ? ((!empty($search))? "AND ".$addCond: $addCond) : "";
		$sort 	       = (!empty($fetch_sort)) ? $fetch_sort : "accountclasscode";
	
		$result = $this->db->setTable('chartaccount chart')
							->setFields($fields)
							->setWhere($addCondition)
							->setOrderBy($sort)
							->runSelect()
							->getResult();

		return $result;
	}
	

	public function deleteData($id)
	{
		$cond   = "";
		$pieces = explode(',', $id["id"]);
		$errmsg = array();

		for($i = 0; $i < count($pieces); $i++)
		{
			$account_code     = $pieces[$i];

			$cond = "id = '$account_code'";

			$result = $this->db->setTable('chartaccount')
				->setWhere($cond)
				->runDelete();

			// var_dump($this->db->buildDelete());
			
			if(!$result)
				$errmsg[] = "<p class = 'no-margin'>Deleting Account Code: $account_code</p>";
			else
				$this->log->saveActivity("Delete Account Code [$account_code]");
		}

		return $errmsg;
	}

	public function saveImport($value) {
		// var_dump($value);
		$result = $this->db->setTable('chartaccount')
		 					->setValues($value)
		 					->runInsert();
		//var_dump($this->db->getQuery());
		if ($result) {
		 	$this->log->saveActivity("Import Accounts");
		 }

		return $result;
	}

	public function searchData($cond)
	{

		$list = $this->db->setTable('chartaccount')
					->setFields('companycode,segment1, segment2, segment3, segment4, segment5, accountname, accountclasscode')
					->setWhere($cond)
					->runPagination();
					//->setPreview()
					//->runSelect()
					//->getResult();
		
		return $list;
	}

	public function getValue($table, $cols = array(), $cond, $orderby = "", $addon = true,$limit = false)
	{
		 $this->db->setTable($table)
					->setFields($cols)
					->setWhere($cond)
					->setOrderBy($orderby);
					if($limit){
						$this->db->setLimit('1');
					}
		$result =   $this->db->runSelect($addon)
					->getResult();
					//->buildSelect();

		return $result;
	}

	public function updateStat($data,$code)
	{
		$condition 			   = " id = '$code' ";

		$result 			   = $this->db->setTable('chartaccount')
											->setValues($data)
											->setWhere($condition)
											->setLimit(1)
											->runUpdate();

		return $result;
	}
}