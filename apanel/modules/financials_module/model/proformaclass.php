<?php
class proformaclass extends wc_model
{
	public function __construct() 
	{
		parent::__construct();
		$this->log = new log();
	}
	
	public function insertData($data)
	{

		$proformacode		= (isset($data['proformacode']) && (!empty($data['proformacode']))) ? 
								htmlentities(addslashes(trim($data['proformacode']))) : "";
		$proformadesc		= (isset($data['proformadesc']) && (!empty($data['proformadesc']))) ? 
								htmlentities(addslashes(trim($data['proformadesc']))) : "";
		$transactiontype	= (isset($data['transactiontype']) && (!empty($data['transactiontype']))) ? 
								htmlentities(addslashes(trim($data['transactiontype']))) : "";
		$accountcodeid      = (isset($data['accountcodeid']) && (!empty($data['accountcodeid'])))? 
								$data['accountcodeid'] : "";
		$costcentercode     = (isset($data['costcentercode']) && (!empty($data['costcentercode'])))? 
								$data['costcentercode'] : "-";
		
		$mainfield  = array();
		$mainfield['proformacode'] = $proformacode;
		$mainfield['proformadesc'] = $proformadesc;
		$mainfield['transactiontype'] = $transactiontype;
		$mainfield['stat'] = 'active';
		$mainfield['costcentercode'] = $costcentercode;
		$mainfield['subsidiaryledger']  = "-";
		$mainfield['moduleid'] = 0;
		// Check for duplicate entries before inserting
		$retain = $this->db->setTable('proforma')
					->setValues($mainfield)
					->setWhere("proformacode = '$proformacode' 
								AND proformadesc = '$proformadesc'
								AND transactiontype = '$transactiontype' ")
					->getProperties();
		
		$result = $this->db->setProperties($retain)
							->setLimit(1)
							->runSelect()
							->getRow();	
		if ($result) {
			return false;
		} else {
			$this->db->setProperties($retain);
			
			$this->log->saveActivity("Inserted Proforma Code [$proformacode], 
											Proforma Desc [$proformadesc]");

			$isInserted = $this->db->runInsert();
			 if($isInserted){
			 	return $this->insertProformaDetails($data);
			 }
		}

	}
	
	private function insertProformaDetails($data) 
	{
		
		foreach ($data['accountcodeid'] as $accountid) {
			
			//get from coa
			$coaArr = $this->getValue("chartaccount",
					array("segment1","segment2","segment3","segment4","segment5","accountname"),
					"id= '$accountid'");
				if(!empty($coaArr)){
					$detailFields["proformacode"] = $data['proformacode'];
					$detailFields["segment1"] = (!empty($coaArr[0]->segment1))? $coaArr[0]->segment1 : 0;
					$detailFields["segment2"] = (!empty($coaArr[0]->segment2))? $coaArr[0]->segment2 : 0; 
					$detailFields["segment3"] = (!empty($coaArr[0]->segment3))? $coaArr[0]->segment3 : 0;
					$detailFields["segment4"] = (!empty($coaArr[0]->segment4))? $coaArr[0]->segment4 : 0;
					$detailFields["segment5"] = (!empty($coaArr[0]->segment5))? $coaArr[0]->segment5 : 0;
					$detailFields["accountname"] = (!empty($coaArr[0]->accountname))? $coaArr[0]->accountname : "";
					$detailFields["accountcodeid"] = $accountid;
					$detailFields["stat"] = "active";
					$result = $this->db->setTable('proforma_details')
										->setValues($detailFields)
										->runInsert();
				}
		}
							
		return true;
	}

	public function editData($data)
	{
		$proformacode = $data['proformacode'];
		$cond = "proformacode = '$proformacode'";

		$mainfield  = array();
		$mainfield['proformadesc'] = $data['proformadesc'];
		$mainfield['transactiontype'] = $data['transactiontype'];
		$result = $this->db->setTable('proforma')
				->setValues($mainfield)
				->setWhere($cond)
				->runUpdate();

		if($result)
		{
			$this->log->saveActivity("Update Proforma Code [$proformacode]");
			 return $this->updateProformaDetails($data);
		}
				
	}

	public function updateProformaDetails($data) 
	{	
		$this->db->setTable('proforma_details')
				 ->setWhere("proformacode = '".$data['proformacode']."'")
			     ->runDelete();

		foreach ($data['accountcodeid'] as $accountid) {
			
			//get from coa
			$coaArr = $this->getValue("chartaccount",
					array("segment1","segment2","segment3","segment4","segment5","accountname"),
					"id= '$accountid'");
				if(!empty($coaArr)){
					$detailFields["proformacode"] = $data['proformacode'];
					$detailFields["segment1"] = (!empty($coaArr[0]->segment1))? $coaArr[0]->segment1 : 0;
					$detailFields["segment2"] = (!empty($coaArr[0]->segment2))? $coaArr[0]->segment2 : 0; 
					$detailFields["segment3"] = (!empty($coaArr[0]->segment3))? $coaArr[0]->segment3 : 0;
					$detailFields["segment4"] = (!empty($coaArr[0]->segment4))? $coaArr[0]->segment4 : 0;
					$detailFields["segment5"] = (!empty($coaArr[0]->segment5))? $coaArr[0]->segment5 : 0;
					$detailFields["accountname"] = (!empty($coaArr[0]->accountname))? $coaArr[0]->accountname : "";
					$detailFields["accountcodeid"] = $accountid;
					$detailFields["stat"] = "active";
					$result = $this->db->setTable('proforma_details')
										->setValues($detailFields)
										->runInsert();
				}
		}
							
		return true;
	}

	public function retrieveList($fields,$condition)
	{

			$condition    = stripslashes($condition);

			$result = $this->db->setTable("proforma")
						->setFields($fields)
						->setWhere($condition)
						->runPagination();
			return $result;

	}

	public function retrieveData($data = array() ,$search = "", $sort = "", $addCond = "") 
	{	
		$add_query = "";
		$add_query = (!empty($search)) ? " (p.proformacode LIKE '%$search%' 
										  OR p.proformadesc LIKE '%$search%'
										  OR p.transactiontype LIKE '%$search%') " : "";
		$add_query .= (!empty($addCond)) ? ((!empty($search))? "AND ".$addCond: $addCond) : "";
		$sort 	   = (!empty($sort)) ? $sort : "p.proformacode";

		$result = $this->db->setTable('proforma p')
					->setFields($data)
					->setWhere($add_query)
					->setOrderBy($sort)
					->runPagination();

		return $result;
	}

	public function retrieveProformaDetails($data,$condition){
		return $this->db->setTable('proforma_details')
						->setFields($data)
						->setWhere($condition)
						->runSelect()
						->getResult();
	}

	public function deleteData($id)
	{
		$cond   = "";
		$pieces = explode(',', $id["id"]);
		$errmsg = array();

		for($i = 0; $i < count($pieces); $i++)
		{
			$proformacode     = $pieces[$i];

			$cond = " proformacode = '$proformacode'";

			$result = $this->db->setTable('proforma')
				->setWhere($cond)
				->runDelete();
			
			if(!$result)
				$errmsg[] = "<p class = 'no-margin'>Deleting Proforma Code: $proformacode</p>";
			else
				$this->log->saveActivity("Delete Proforma Code [$proformacode]");

		}

		return $errmsg;
	}

	public function check_duplicate($current)
	{
		return $this->db->setTable('proforma')
						->setFields('COUNT(proformacode) count')
						->setWhere(" proformacode = '$current'")
						->runSelect()
						->getResult();
	}

	public function importProforma($data)
	{
		$result = $this->db->setTable('proforma')
				->setValuesFromPost($data)
				->runInsert();

		return $result;
	}

	public function fileExport($data)
	{
		$search          = (isset($data['search']))? htmlentities($data['search']) : "";
		$fetch_sort      = (isset($data['sort']))? htmlentities($data['sort']) : "";
		$addCondition = "";
		$addCondition = (!empty($search)) ? " (p.proformacode LIKE '%$search%' 
										  OR p.proformadesc LIKE '%$search%'
										  OR p.transactiontype LIKE '%$search%') " : "";

		$addCondition .= (!empty($addCond)) ? ((!empty($search))? "AND ".$addCond: $addCond) : "";
		$sort 	       = (!empty($fetch_sort)) ? $fetch_sort : "p.proformadesc";
		
		$result = $this->db->setTable('proforma p')
							->leftJoin('proforma_details dtl ON p.proformacode = dtl.proformacode
										AND p.companycode = dtl.companycode')
							->setFields(array("p.proformacode", "p.proformadesc","p.transactiontype",
									"dtl.accountname", "dtl.accountcodeid"))
							->setWhere($addCondition)
							->setOrderBy($sort)
							->runSelect()
							->getResult();
		return $result;
	}

	public function saveImport($data) {

		foreach ($data as $arr) {
			foreach($arr as $subArr){
				
				$mainfield = array();
				$proformacode = $subArr["proformacode"];
				$proformadesc = $subArr["proformadesc"];
				$transactiontype =  $subArr["transactiontype"];
				$accountname =  $subArr["accountname"];
				$accountcodeid =  $subArr["accountcodeid"];

				$mainfield["proformacode"] = $proformacode;
				$mainfield["proformadesc"] = $proformadesc;
				$mainfield["transactiontype"] = $transactiontype;
				$mainfield["stat"] = "active";
				// Check for duplicate entries before inserting
				$retain = $this->db->setTable('proforma')
							->setValues($mainfield)
							->setWhere("proformacode = '$proformacode' 
										AND proformadesc = '$proformadesc'
										AND transactiontype = '$transactiontype' ")
							->getProperties();
				
				$result = $this->db->setProperties($retain)
									->setLimit(1)
									->runSelect()
									->getRow();	
				if (!$result) {
					$this->db->setProperties($retain);
			
					$this->log->saveActivity("Imported Proforma Code [$proformacode], 
													Proforma Desc [$proformadesc]");

					//var_dump($this->db->buildInsert());
					$this->db->runInsert();
				}
				
				//insert Proforma Details
						
				//get from coa
				$coaArr = $this->getValue("chartaccount",
						array("segment1","segment2","segment3","segment4","segment5",
						"accountname"),
						"id= '$accountcodeid'");

				if(!empty($coaArr)){
					$detailFields["proformacode"] = $proformacode;
					$detailFields["segment1"] = (!empty($coaArr[0]->segment1))? $coaArr[0]->segment1 : 0;
					$detailFields["segment2"] = (!empty($coaArr[0]->segment2))? $coaArr[0]->segment2 : 0; 
					$detailFields["segment3"] = (!empty($coaArr[0]->segment3))? $coaArr[0]->segment3 : 0;
					$detailFields["segment4"] = (!empty($coaArr[0]->segment4))? $coaArr[0]->segment4 : 0;
					$detailFields["segment5"] = (!empty($coaArr[0]->segment5))? $coaArr[0]->segment5 : 0;
					$detailFields["accountname"] = (!empty($coaArr[0]->accountname))? 
													$coaArr[0]->accountname : "";
					$detailFields["accountcodeid"] = $accountcodeid;
					$detailFields["stat"] = "active";

					// Check for duplicate entries before inserting (details)
					$retainDetails = $this->db->setTable('proforma_details')
								->setValues($detailFields)
								->setWhere("proformacode = '$proformacode' 
											AND accountcodeid = '$accountcodeid'")
								->getProperties();

					$resultDetails = $this->db->setProperties($retainDetails)
									->setLimit(1)
									->runSelect()
									->getRow();	
					
					if (!$resultDetails) {
						$result = $this->db->setTable('proforma_details')
									->setValues($detailFields)
									->runInsert();
					}	
					
				}
							
				
			}
		}

		return true;
	
	}

	public function getValue($table, $cols = array(), $cond, $orderby = "", $addon = true)
	{
		$result = $this->db->setTable($table)
					->setFields($cols)
					->setWhere($cond)
					->setOrderBy($orderby)
					// ->setLimit('1')
					->runSelect($addon)
					->getResult();
					//->buildSelect();

		return $result;
	}

	public function updateStat($data,$code)
	{
		$condition 			   = " proformacode = '$code' ";

		$result 			   = $this->db->setTable('proforma')
											->setValues($data)
											->setWhere($condition)
											->setLimit(1)
											->runUpdate();

		return $result;
	}
}