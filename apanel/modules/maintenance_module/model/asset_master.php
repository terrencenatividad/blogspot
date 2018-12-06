<?php
class asset_master extends wc_model {

		public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function saveAssetMaster($data) {
		$data['commissioning_date'] = $this->date->dateDbFormat();
		$data['retirement_date']    = $this->date->dateDbFormat();
		$data['depreciation_month'] = $this->date->dateDbFormat();
		$result =  $this->db->setTable('asset_master')
							->setValues($data)
							->runInsert();
		
		if ($result) {
			$insert_id = $this->db->getInsertId();
			$this->log->saveActivity("Create Asset Master [$insert_id]");
		}

		return $result;
	}

	public function updateAssetMaster($data, $id) {
		$data['commissioning_date'] = $this->date->dateDbFormat();
		$data['retirement_date']    = $this->date->dateDbFormat();
		$data['depreciation_month'] = $this->date->dateDbFormat();
		$result =  $this->db->setTable('asset_master')
							->setValues($data)
							->setWhere("id = '$id'")
							->setLimit(1)
							->runUpdate();
		
		if ($result) {
			$this->log->saveActivity("Update Asset Master [$id]");
		}

		return $result;
	}

	public function deleteAssetMaster($data) {
		$error_id = array();
		foreach ($data as $id) {
			$result =  $this->db->setTable('asset_master')
								->setWhere("id = '$id'")
								->setLimit(1)
								->runDelete();
		
			if ($result) {
				$this->log->saveActivity("Delete Asset Master [$id]");
			} else {
				if ($this->db->getError() == 'locked') {
					$error_id[] = $id;
				}
			}
		}

		return $error_id;
	}
	
	public function getItems()
	{
		$result = $this->db->setTable('items i')
						->setFields("itemcode ind, CONCAT(itemcode, ' - ', itemname) val, i.stat stat")
						->leftJoin('itemtype it ON it.id = i.typeid')
						->setWhere("it.label = 'Fixed Asset' AND NOT EXISTS (SELECT *  FROM asset_master am WHERE am.itemcode = i.itemcode)")
						->runSelect()
						->getResult();
						
		return $result;
	}

	public function getAssetClass()
	{
		$result = $this->db->setTable('asset_class')
						->setFields("id ind, CONCAT(code, ' - ', assetclass) val, stat stat")
						->runSelect()
						->getResult();
					
		return $result;
	}

	public function getCOA()
	{
		$result = $this->db->setTable('chartaccount')
						->setFields("id ind, CONCAT(segment5, ' - ', accountname) val, stat stat")
						->runSelect()
						->getResult();
						
		return $result;
	}

	public function getAssetMasterById($fields, $id) {
		return $this->db->setTable('asset_master')
						->setFields($fields)
						->setWhere("id = '$id'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}

	public function retrieveItemCode($itemcode) {
		$fields 	= array('itemdesc','barcode','itemname');
		return $this->db->setTable('items')
						->setFields($fields)
						->setWhere("itemcode = '$itemcode'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}

	public function retrievePO($itemcode) {
		$fields 	= array('unitprice','source_no', 'transactiondate');
		return $this->db->setTable('purchasereceipt_details prd')
						->setFields($fields)
						->leftJoin('purchasereceipt pr ON pr.voucherno = prd.voucherno')
						->setWhere("itemcode = '$itemcode'")
						->runSelect()
						->getResult();
	}

	public function retrieveAssetClass($id) {
		$fields 	= array('gl_asset','gl_accdep','gl_depexpense','salvage_value','useful_life');
		return $this->db->setTable('asset_class')
						->setFields($fields)
						->setWhere("id = '$id'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}

	public function retrieveGLAsset($gl_asset) {
		$fields 	= array('segment5','accountname');
		return $this->db->setTable('chartaccount')
						->setFields($fields)
						->setWhere("id = '$gl_asset'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}
	public function retrieveGLAccdep($gl_accdep) {
		$fields 	= array('segment5','accountname');
		return $this->db->setTable('chartaccount')
						->setFields($fields)
						->setWhere("id = '$gl_accdep'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}
	public function retrieveGLDepexpense($gl_depexpense) {
		$fields 	= array('segment5','accountname');
		return $this->db->setTable('chartaccount')
						->setFields($fields)
						->setWhere("id = '$gl_depexpense'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}


	public function getAssetMasterListPagination($fields, $search = '', $sort) {

			$result = $this->db->setTable("asset_master")
								->setFields($fields)
								->setWhere(1)
								->setOrderBy($sort)
								->runPagination();
	
			return $result;
		
	}

	public function getAssetMasterList($fields, $search = '', $sort) {
		$result = $this->getAssetMasterListQuery($fields, $search, $sort)
						->runSelect()
						->getResult();

		return $result;
	}

	public function checkExistingAssetMaster($data) {
		$asset_numbers = "'" . implode("', '", $data) . "'";

		$result = $this->db->setTable('asset_master')
							->setFields('asset_number')
							->setWhere("asset_number IN ($asset_numbers)")
							->runSelect()
							->getResult();
		
		return $result;
	}

	public function check_duplicate($current)
		{
			return $this->db->setTable('asset_number')
							->setFields('COUNT(asset_number) count')
							->setWhere(" asset_number = '$current'")
							->runSelect()
							->getResult();
		}

	private function getAssetMasterListQuery($fields, $search = '', $sort) {
		$sort		= ($sort) ? $sort : 'asset_number';
		$condition = '';
		if ($search) {
			$condition = $this->generateSearch($search, array('id', 'asset_number'));
		}
		$query = $this->db->setTable('asset_master')
							->setFields($fields)
							->setOrderBy($sort)
							->setWhere($condition);

		return $query;
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

	public function updateStat($data,$id)
	{
		$condition 			   = " id = '$id' ";

		$result 			   = $this->db->setTable('asset_master')
											->setValues($data)
											->setWhere($condition)
											->setLimit(1)
											->runUpdate();

		return $result;
	}
}