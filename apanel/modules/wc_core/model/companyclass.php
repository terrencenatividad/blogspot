<?php
class companyclass extends wc_model
{
	/**
	* Update data
	*/
	public function updateData($type,$data,$condition = '')
	{
		/**
		 * Get Session Values
         */
		$session               = new session();
		$companycode           = $session->get('companycode');
		$companycode           = (!empty($companycode)) ? $companycode : 'CID';

		$datetime              = date("Y-m-d H:i:s"); 
		$data["companycode"]   = $companycode;

		$data["enteredby"]     = "admin";
		$data["entereddate"]   = $datetime;
		$data["updateby"]      = "admin";
		$data["updatedate"]    = $datetime;
		$data["updateprogram"] = "Company-New";

		if($type == 'add'){
			$result = $this->db->setTable('company')
				->setValues($data)
				->runInsert();
		}else if($type == 'update'){
			$result = $this->db->setTable('company')
				->setValues($data)
				->setWhere($condition)
				->runUpdate();
		}
		
		return $result;
	}

	public function retrieveData($data,$condition) 
	{
		$result = $this->db->setTable('company')
					->setFields(array_keys($data))
					->setWhere($condition)
					->runSelect()
					->getRow();

		return $result;
	}

}