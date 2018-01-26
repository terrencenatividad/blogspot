<?php
class taxcodes extends wc_model
{
	public function __construct() 
	{
		parent::__construct();
		$this->log = new log();
	}
	
	public function retrieveTaxType() 
	{
		$result = $this->db->setTable('wc_option')
					->setFields('code ind, value val')
					->setWhere("type = 'tax_type'")
					->runSelect(false)
					->getResult();
		
		return $result;
	}

	public function retrieveAccountList()
	{
		$result = $this->db->setTable('chartaccount')
					->setFields('id ind, companycode, accountname val')
					->setWhere("segment5 != '' AND accounttype != 'P' ORDER BY accountname")
					->runSelect()
					->getResult();
		
		return $result;
	}

	public function retrieveData($search, $sort)
	{
		$add_query = (!empty($search)) ? "AND (fstaxcode LIKE '%$search%' OR longname LIKE '%$search%' OR code LIKE '%$search%' OR taxrate LIKE '%$search%')" : "";
		$sort = (!empty($sort)) ? $sort : "fstaxcode";

		$setFields = "tax.fstaxcode as fstaxcode,tax.longname as longname, code.value as code, tax.taxrate as taxrate, tax.companycode as companycode";
		$cond 	   = "(tax.fstaxcode != '' AND stat = 'active') $add_query";

		$result = $this->db->setTable("fintaxcode as tax")
					->setFields($setFields)
					->leftJoin("wc_option as code ON code.code = tax.taxtype AND code.type = 'tax_type'")
					->setWhere($cond)
					->setOrderBy($sort)
					->runPagination();
					// ->runSelect()
					// ->getResult();

		return $result;
	}
	
	public function retrieveEditData($fstaxcode)
	{
		$setFields = "fstaxcode, shortname, longname, taxrate, taxtype, salesAccount, purchaseAccount, companycode";
		$cond = "fstaxcode = '$fstaxcode'";
		
		return $this->db->setTable('fintaxcode')
					->setFields($setFields)
					->setWhere($cond)
					->setLimit('1')
					->runSelect()
					->getRow();
	}

	public function getValue($table, $cols, $cond, $bool = "")
	{
		$result = $this->db->setTable($table)
					->setFields($cols)
					->setWhere($cond)
					->setLimit('1')
					->runSelect($bool)
					->getRow();

		return $result;
	}

	public function insertData($data)
	{
		$data_insert["fstaxcode"]       = $data["fstaxcode"];
		$data_insert["shortname"]       = $data["shortname"];
		$data_insert["longname"]        = $data["longname"];
		$data_insert["taxrate"]         = $data["taxrate"];
		$data_insert["taxtype"] 		= $data["tax_type"];
		$data_insert["salesAccount"]    = $data["sales_account"];
		$data_insert["purchaseAccount"] = $data["purchase_account"];
		$data_insert["stat"] 			= "active";
		
		$fstaxcode                      = $data["fstaxcode"];
		$tax_name 						= $data["shortname"];

		// Check for duplicate entries before inserting
		$retain = $this->db->setTable('fintaxcode')
				->setValues($data_insert)
				->setWhere("fstaxcode = '$fstaxcode'")
				->getProperties();

		$result = $this->db->setProperties($retain)
						->setLimit(1)
						->runSelect()
						->getRow();	
	

		if ($result) {
			return false;
		} else {
			$this->db->setProperties($retain);
			
			$this->log->saveActivity("Inserted Tax Code [$fstaxcode], Tax Name [$tax_name]");

			// var_dump($this->db->buildInsert());
			
			return $this->db->runInsert();
		}
	}

	public function fileInsert($docData)
	{
		$tempArr 					= array();
		$errmsg 					= array();
		$taxList					= array();

		for($i = 0; $i < count($docData); $i++)
		{	
			$data_insert["fstaxcode"]       = $docData[$i]["fstaxcode"];
			$data_insert["shortname"]       = $docData[$i]["shortname"];
			$data_insert["longname"]        = $docData[$i]["longname"];
			$data_insert["taxrate"]         = $docData[$i]["taxrate"];
			$data_insert["taxtype"]         = $docData[$i]["taxtype"];
			$data_insert["salesAccount"]    = $docData[$i]["salesAccount"];
			$data_insert["purchaseAccount"] = $docData[$i]["purchaseAccount"];
			$data_insert["stat"]            = "active";

			$taxList[]						= $docData[$i]["fstaxcode"];
			$tempArr[] 						= $data_insert;
		}

		$retain = $this->db->setTable('fintaxcode')
						->setValues($tempArr)
						->getProperties();

		$this->db->setProperties($retain);
		// var_dump($this->db->buildInsert());
		$insert_result = $this->db->runInsert();

		if(!$insert_result)
		{
			// $errmsg[]	= "Selected file was not uploaded successfully.";

			if(!empty($taxList))
			{
				$taxlist["id"]	= implode(",",$taxList);
				$this->deleteData($taxlist);
			}
			return false;
		}
		else
			return $insert_result;
	}

	public function fileExport()
	{
		$setFields = "tax.fstaxcode as fstaxcode, tax.shortname as shortname, tax.longname as longname, code.value as code, tax.taxrate as taxrate, chart_s.accountname as sales_account, chart_p.accountname as purchase_account";
		$cond 	   = "(tax.fstaxcode != '' AND stat = 'active') AND (chart_s.accounttype != 'P' AND chart_p.accounttype != 'P')";

		$result = $this->db->setTable('fintaxcode as tax')
								->setFields($setFields)
								->leftJoin("wc_option as code ON code.code = tax.taxtype AND code.type = 'tax_type'")
								->leftJoin("chartaccount as chart_s ON chart_s.id = tax.salesAccount")
								->leftJoin("chartaccount as chart_p ON chart_p.id = tax.purchaseAccount")
								->setWhere($cond)
								->setOrderBy("fstaxcode")
								->runSelect()
								->getResult();
								// ->buildSelect();
		
		return $result;
	}
	

	public function editData($data, $fstaxcode)
	{
		$data_insert["fstaxcode"]       = $data["fstaxcode"];
		$data_insert["shortname"]       = $data["shortname"];
		$data_insert["longname"]        = $data["longname"];
		$data_insert["taxrate"]         = $data["taxrate"];
		$data_insert["taxtype"] 		= $data["tax_type"];
		$data_insert["salesAccount"]    = $data["sales_account"];
		$data_insert["purchaseAccount"] = $data["purchase_account"];
		
		$cond = "fstaxcode = '$fstaxcode'";

		$result = $this->db->setTable('fintaxcode')
				->setValues($data_insert)
				->setWhere($cond)
				->runUpdate();
		
		if($result)
		{
			$this->log->saveActivity("Update Tax Code [$fstaxcode]");
			return $result;
		}
	}

	public function deleteData($id)
	{
		$cond   = "";
		$pieces = explode(',', $id["id"]);
		$errmsg = array();

		for($i = 0; $i < count($pieces); $i++)
		{
			$fstaxcode   = $pieces[$i];
			
			$cond = "fstaxcode = '$fstaxcode'";

			$result = $this->db->setTable('fintaxcode')
				->setWhere($cond)
				->runDelete();
			
			if(!$result)
				$errmsg[] = "<p class = 'no-margin'>Deleting Tax Code: $fstaxcode</p>";
			else
				$this->log->saveActivity("Delete Tax Code [$fstaxcode]");
		}

		return $errmsg;
	}

}