<?php
class atccode_class extends wc_model
{
	public function __construct() 
	{
		parent::__construct();
		$this->log = new log();
	}

	public function insertData($data)
	{
		$atc_code = $data['atc_code'];
		$tax_rate = $data['tax_rate'];
		$data['tax_rate'] = $data['tax_rate'] / 100;

		$retain = $this->db->setTable('atccode')
				->setValues($data)
				->setWhere("atc_code = '$atc_code' ")
				->getProperties();

		$result = $this->db->setProperties($retain)
						->setLimit(1)
						->runSelect()
						->getRow();	
		if ($result) {
			return false;
		} else {
			$this->db->setProperties($retain);
			
			$this->log->saveActivity("Inserted Tax Code [$atc_code], 
											   Tax Rate [$tax_rate]") ;
			return $this->db->runInsert();
		}
				
	}

	public function editData($data, $id)
	{
		$sid = $id["atcId"];
		
		$cond = "atcId = '$sid'";

		$result = $this->db->setTable('atccode')
				->setValues($data)
				->setWhere($cond)
				->runUpdate();

		if($result)
		{
			$this->log->saveActivity("Update ATC Code [$sid]");
			return $result;
		}
				
	}

	public function retrieveData($data = array() ,$search = "", $sort = "", $addCond = "") 
	{	
		$add_query = "";
		$add_query = (!empty($search)) ?  " (atc_code LIKE '%$search%' 
											OR tax_rate LIKE '%$search%'
											OR wtaxcode LIKE '%$search%'
											OR short_desc LIKE '%$search%') " : "";
		$add_query .= (!empty($addCond)) ? ((!empty($search))? "AND ".$addCond: $addCond) : "";

		$sort 	   = (!empty($sort)) ? $sort : "atcId desc";
		$leftJoin  = "chartaccount c ON a.tax_account = c.id";

		$result = $this->db->setTable('atccode a')
					->setFields($data)
					->leftJoin($leftJoin)
					->setWhere($add_query)
					->setOrderBy($sort)
					->runPagination();

		return $result;
	}
	
	public function fileExport($data)
	{
		$addCond 		 = (isset($data['addCond']) && !empty($data['addCond']))? 
								htmlentities($data['addCond']) : "";
		$addCond 		= 	stripslashes($addCond);
		$search          = (isset($data['search']))? htmlentities($data['search']) : "";
		$fetch_sort      = (isset($data['sort']))? htmlentities($data['sort']) : "";
		$fields 		 =  array("atc_code", "tax_rate","wtaxcode","short_desc","accountname");

		$addCondition = "";
		$addCondition = (!empty($search)) ? " (atc_code LIKE '%$search%' 
											OR tax_rate LIKE '%$search%'
											OR wtaxcode LIKE '%$search%'
											OR short_desc LIKE '%$search%') " : "";

		$addCondition .= (!empty($addCond)) ? ((!empty($search))? "AND ".$addCond: $addCond) : "";
		$sort 	       = (!empty($fetch_sort)) ? $fetch_sort : "atcId desc";
		$left  			= "chartaccount c ON c.id = a.tax_account";
	
		$result = $this->db->setTable('atccode a')
							->setFields($fields)
							->setWhere($addCondition)
							->leftJoin($left)
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
			$id     = $pieces[$i];

			$cond = "atcId = '$id'";

			$result = $this->db->setTable('atccode')
				->setWhere($cond)
				->runDelete();

			// var_dump($this->db->buildDelete());
			
			if(!$result)
				$errmsg[] = "<p class = 'no-margin'>Deleting ATC Code: $id</p>";
			else
				$this->log->saveActivity("Delete ATC Code [$id]");
		}

		return $errmsg;
	}

	public function saveImport($value) {
		$result = $this->db->setTable('atccode')
		 					->setValues($value)
							 ->runInsert();
							 echo $this->db->getQuery();
		if ($result) {
		 	$this->log->saveActivity("Import ATC Codes");
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
}