<?php
class asset_master extends wc_model {

		public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function saveAssetMaster($data) {
		$data['commissioning_date'] = date("Y-m-d",strtotime($data['commissioning_date']));
		$data['retirement_date']    = date("Y-m-d",strtotime($data['retirement_date']));
		$data['depreciation_month'] = date("Y-m-d",strtotime($data['depreciation_month']));
		
		$result =  $this->db->setTable('asset_master')
							->setValues($data)
							->runInsert();
		
		if ($result) {
			$insert_id = $this->db->getInsertId();
			$this->log->saveActivity("Create Asset [$insert_id]");
		}

		return $result;
	}

	// public function deletesched($asset_number) {
	// 	$result = $this->db->setTable('depreciation_schedule')
	// 						->setWhere("asset_id = '$asset_number'")
	// 						->runDelete();
	// }

	public function saveAssetMasterSchedule($shh,$asset_number,$useful_life,$balance_value,$salvage_value,$final,$depreciation,$depreciation_amount) {
		$shh['asset_id'] = $asset_number;
		$shh['depreciation_date'] = $final;
		$shh['accumulated_dep'] = $depreciation;
		$shh['depreciation_amount'] = $depreciation_amount;

		$result =  $this->db->setTable('depreciation_schedule')
							->setValues($shh)
							->runInsert();
		if ($result) {
			$insert_id = $this->db->getInsertId();
			$this->log->saveActivity("Create Asset Schedule [$insert_id]");
		}

		return $result;
	}

	public function updateAssetMaster($data, $id, $asset_number, $old_location, $old_department, $old_person) {
		$data['commissioning_date'] = date("Y-m-d",strtotime($data['commissioning_date']));
		$data['retirement_date']    = date("Y-m-d",strtotime($data['retirement_date']));
		$data['depreciation_month'] = date("Y-m-d",strtotime($data['depreciation_month']));
		$result =  $this->db->setTable('asset_master')
							->setValues($data)
							->setWhere("id = '$id'")
							->setLimit(1)
							->runUpdate();

		if($result){
			if($old_location != $data['asset_location'] || $old_department != $data['department'] || $old_person != $data['accountable_person']){
				$this->db->setTable('asset_transfer')
				->setValues(array('asset_number' => $data['asset_number'], 'asset_location' => $data['asset_location'], 'department' => $data['department'], 'accountable_person' => $data['accountable_person']))
				->runInsert();
			}
		} 
		// $result = $this->db->setTable('depreciation_schedule')
		// 					->setWhere("asset_id = '$asset_number'")
		// 					->runDelete();
		
		if ($result) {
			$this->log->saveActivity("Update Asset [$id]");		
		}

		return $result;
	}

	public function updateAssetMasterSchedule($shh,$asset_number,$useful_life,$balance_value,$salvage_value,$final,$depreciation,$depreciation_amount) {
		$shh['asset_id'] = $asset_number;
		$shh['depreciation_date'] = $final;
		$shh['accumulated_dep'] = $depreciation;
		$shh['depreciation_amount'] = $depreciation_amount;

		// $result =  $this->db->setTable('depreciation_schedule')
		// 					->setValues($shh)
		// 					->setWhere("asset_id = '$asset_number'")
		// 					->setLimit(1)
		// 					->runUpdate();
							
		// if ($result) {
		// 	$insert_id = $this->db->getInsertId();
		// 	$this->log->saveActivity("Create Asset Master Schedule [$insert_id]");
		// }

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
			$this->log->saveActivity("Delete Asset [$id]");
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

	public function getDepartment() {
		$result = $this->db->setTable('cost_center')
							->setFields("id ind, name val, stat stat")
							->setWhere(1)
							->runSelect()
							->getResult();

		return $result;
	}

	public function retrieveItems($itemcode)
	{
		$result = $this->db->setTable('items i')
						->setFields("itemcode ind, CONCAT(itemcode, ' - ', itemname) val, i.stat stat")
						->leftJoin('itemtype it ON it.id = i.typeid')
						->setWhere("it.label = 'Fixed Asset' AND (i.itemcode = '$itemcode' OR NOT EXISTS (SELECT *  FROM asset_master am WHERE am.itemcode = i.itemcode))")
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

	public function getSchedule($asset_number) {
		$fields 	= array('depreciation_date','depreciation_amount','accumulated_dep','gl_asset','gl_accdep','gl_depexpense','CONCAT(coa.segment5, " - ", coa.accountname) asset','CONCAT(asd.segment5, " - ", asd.accountname) accdep','CONCAT(dsa.segment5, " - ", dsa.accountname) depexpense');
		$result = $this->db->setTable('depreciation_schedule ds')
						->setFields($fields)
						->leftJoin('chartaccount coa ON coa.id = ds.gl_asset')
						->leftJoin('chartaccount asd ON asd.id = ds.gl_accdep')
						->leftJoin('chartaccount dsa ON dsa.id = ds.gl_depexpense')
						->setWhere("asset_id = '$asset_number'")
						->setOrderBy('depreciation_date')
						->runSelect()
						->getResult();
		return $result;
	}

	public function getAssetMasterListPagination($search = '', $sort) {
		$fields = array('a.id id',
		'itemcode',
		'asset_class',
		'asset_name',
		'asset_number',
		'sub_number',
		'serial_number',
		'a.description description',
		'asset_location',
		'department',
		'name',
		'accountable_person',
		'commissioning_date',
		'retirement_date',
		'useful_life',
		'depreciation_month',
		'depreciation_amount',
		'capitalized_cost',
		'purchase_value',
		'balance_value',
		'salvage_value',
		'frequency_of_dep',
		'number_of_dep',
		'gl_asset',
		'gl_accdep',
		'gl_depexpense',
		'a.stat stat');
		$sort		= ($sort) ? $sort : 'asset_name';
		$condition = '';
		if ($search) {
			$condition = $this->generateSearch($search, array('asset_name', 'asset_number','name','a.description','accountable_person'));
		}
			$result = $this->db->setTable("asset_master a")
								->setFields($fields)
								->leftJoin("cost_center c ON c.id = a.department")
								->setWhere($condition)
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
			return $this->db->setTable('asset_master')
							->setFields('COUNT(asset_number) count')
							->setWhere(" asset_number = '$current'")
							->runSelect()
							->getResult();
		}

	private function getAssetMasterListQuery($fields, $search = '', $sort) {
		$fields = array(
			'am.id',
			'itemcode',
			'assetclass',
			'asset_name',
			'asset_number',
			'sub_number',
			'serial_number',
			'am.description',
			'asset_location',
			'department',
			'accountable_person',
			'commissioning_date',
			'retirement_date',
			'am.useful_life',
			'depreciation_month',
			'depreciation_amount',
			'capitalized_cost',
			'purchase_value',
			'balance_value',
			'am.salvage_value',
			'c.accountname asset',
			'o.accountname accdep',
			'a.accountname depexp',
			'c.segment5 a_asset',
			'o.segment5 a_accdep',
			'a.segment5 a_depexp',
			'am.stat'
		);
		$sort		= ($sort) ? $sort : 'asset_number';
		$condition = '';
		if ($search) {
			$condition = $this->generateSearch($search, array('id', 'asset_number'));
		}
		$query = $this->db->setTable('asset_master am')
							->setFields($fields)
							->leftJoin("asset_class ac ON ac.id = am.asset_class")
							->leftJoin("chartaccount c ON c.id = am.gl_asset")
							->leftJoin("chartaccount o ON o.id = am.gl_accdep")
							->leftJoin("chartaccount a ON a.id = am.gl_depexpense")
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

	public function tagRetired($id,$data)
	{
		$condition 	= " id = '$id' ";

		$result 	= $this->db->setTable('asset_master')
							->setValues($data)
							->setWhere($condition)
							->setLimit(1)
							->runUpdate();

		return $result;
	}

	public function untagRetired($id,$data)
	{
		$condition 	= " id = '$id' ";

		$result 	= $this->db->setTable('asset_master')
							->setValues($data)
							->setWhere($condition)
							->setLimit(1)
							->runUpdate();

		return $result;
	}
}