<?php
class depreciation_run extends wc_model {

	public function getAsset() {
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
		'a.useful_life',
		'depreciation_month',
		'depreciation_amount',
		'capitalized_cost',
		'purchase_value',
		'balance_value',
		'a.salvage_value',
		'frequency_of_dep',
		'number_of_dep',
		'a.gl_asset gl_asset',
		'a.gl_accdep gl_accdep',
		'a.gl_depexpense gl_depexp',
		'a.stat stat',
		'coa.accountname asset',
		'asd.accountname accdep',
		'dsa.accountname depexp',
		'coa.segment5 a_segment5',
		'asd.segment5 b_segment5',
		'dsa.segment5 c_segment5',
		'ac.assetclass'
	);
		
		$result = $this->db->setTable("asset_master a")
						->setFields($fields)
						->leftJoin("cost_center c ON c.id = a.department")
						->leftJoin('asset_class ac ON ac.id = a.asset_class')
						->leftJoin('chartaccount coa ON coa.id = a.gl_asset')
						->leftJoin('chartaccount asd ON asd.id = a.gl_accdep')
						->leftJoin('chartaccount dsa ON dsa.id = a.gl_depexpense')
						->setWhere("a.stat = 'active'")
						// ->setOrderBy($sort)
						// ->setGroupBy('a.id')
						->runPagination();

						// echo $this->db->getQuery();
						
		return $result;
	}
	public function deleteSched(){
		$this->db->setTable('depreciation_schedule')
		->setWhere('companycode IS NOT NULL')
		->runDelete();
	}
	
	public function saveAssetMasterSchedule($assetnumber, $itemcode, $final,$depreciation,$depreciation_amount, $gl_asset, $gl_accdep, $gl_depexp) {	
		$result =  $this->db->setTable('depreciation_schedule')
							->setValues(array('asset_id' => $assetnumber,'itemcode' => $itemcode,'depreciation_date' => $final, 'depreciation_amount' => $depreciation, 'accumulated_dep' => $depreciation_amount, 'gl_asset' => $gl_asset, 'gl_accdep' => $gl_accdep, 'gl_depexpense' => $gl_depexp))
							->runInsert();
		
		
		// if ($result) {
		// 	$insert_id = $this->db->getInsertId();
		// 	$this->log->saveActivity("Run Depreciation [$insert_id]");
		// }

		return $result;
	}

	public function getAsset2($fields, $search, $sort ,$checked) {
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
		'a.useful_life',
		'depreciation_month',
		'depreciation_amount',
		'capitalized_cost',
		'purchase_value',
		'balance_value',
		'a.salvage_value',
		'frequency_of_dep',
		'number_of_dep',
		'a.gl_asset',
		'a.gl_accdep',
		'a.gl_depexpense',
		'a.stat stat',
		'coa.accountname asset',
		'asd.accountname accdep',
		'dsa.accountname depexp',
		'coa.segment5 a_segment5',
		'asd.segment5 b_segment5',
		'dsa.segment5 c_segment5',
		'ac.assetclass'
	);
		$sort = ($sort) ? $sort : 'asset_number asc';
		$condition = '';
		if ($search) {
			$condition = $this->generateSearch($search, array('asset_number','asset_name'));
		}
		if($checked){
			$condition .=  " a.id IN ($checked) ";
		}
		$result = $this->db->setTable("asset_master a")
						->setFields($fields)
						->leftJoin("cost_center c ON c.id = a.department")
						->leftJoin('asset_class ac ON ac.id = a.asset_class')
						->leftJoin('chartaccount coa ON coa.id = a.gl_asset')
						->leftJoin('chartaccount asd ON asd.id = a.gl_accdep')
						->leftJoin('chartaccount dsa ON dsa.id = a.gl_depexpense')
						->setWhere($condition)
						// ->setOrderBy($sort)
						// ->setGroupBy('asset_class')
						->runPagination();

						// echo $this->db->getQuery();
						
		return $result;
	}

	// public function getAssetClass($asset_class) {
	// 	$fields = array('a.id id',
	// 	'itemcode',
	// 	'asset_class',
	// 	'asset_name',
	// 	'asset_number',
	// 	'sub_number',
	// 	'serial_number',
	// 	'a.description description',
	// 	'asset_location',
	// 	'department',
	// 	'name',
	// 	'accountable_person',
	// 	'commissioning_date',
	// 	'retirement_date',
	// 	'a.useful_life',
	// 	'depreciation_month',
	// 	'depreciation_amount',
	// 	'capitalized_cost',
	// 	'purchase_value',
	// 	'balance_value',
	// 	'a.salvage_value',
	// 	'frequency_of_dep',
	// 	'number_of_dep',
	// 	'a.gl_asset',
	// 	'a.gl_accdep',
	// 	'a.gl_depexpense',
	// 	'a.stat stat',
	// 	'coa.accountname asset',
	// 	'asd.accountname accdep',
	// 	'dsa.accountname depexp',
	// 	'coa.segment5 a_segment5',
	// 	'asd.segment5 b_segment5',
	// 	'dsa.segment5 c_segment5',
	// 	'ac.assetclass'
	// );

	// 	$result = $this->db->setTable("asset_master a")
	// 					->setFields($fields)
	// 					->leftJoin("cost_center c ON c.id = a.department")
	// 					->leftJoin('asset_class ac ON ac.id = a.asset_class')
	// 					->leftJoin('chartaccount coa ON coa.id = a.gl_asset')
	// 					->leftJoin('chartaccount asd ON asd.id = a.gl_accdep')
	// 					->leftJoin('chartaccount dsa ON dsa.id = a.gl_depexpense')
	// 					->setWhere("a.asset_class = '$asset_class'")
	// 					->runSelect()
	// 					->getResult();

	// 					// echo $this->db->getQuery();
						
	// 	return $result;
	// }

	public function getAssetClass($asset_class) {
		$fields = array('am.id id',
		'am.itemcode',
		'asset_class',
		'asset_name',
		'asset_number',
		'sub_number',
		'serial_number',
		'am.description description',
		'asset_location',
		'department',
		'name',
		'accountable_person',
		'commissioning_date',
		'retirement_date',
		'am.useful_life',
		'depreciation_month',
		'd.depreciation_amount',
		'capitalized_cost',
		'purchase_value',
		'balance_value',
		'am.salvage_value',
		'frequency_of_dep',
		'number_of_dep',
		'am.gl_asset gl_asset',
		'am.gl_accdep gl_accdep',
		'am.gl_depexpense gl_depexp',
		'am.stat stat',
		'coa.accountname asset',
		'asd.accountname accdep',
		'dsa.accountname depexp',
		'coa.segment5 a_segment5',
		'asd.segment5 b_segment5',
		'dsa.segment5 c_segment5',
		'ac.assetclass'
	);
	$condition = '';
	$result = $this->db->setTable('depreciation_schedule d')
						->setFields($fields)
						->leftJoin('asset_master am ON am.asset_number = d.asset_id')
						->leftJoin('asset_class ac ON ac.id = am.asset_class')
						->leftJoin('cost_center cc ON cc.id = am.department')
						->leftJoin('chartaccount coa ON coa.id = am.gl_asset')
						->leftJoin('chartaccount asd ON asd.id = am.gl_accdep')
						->leftJoin('chartaccount dsa ON dsa.id = am.gl_depexpense')
						->setWhere($condition)
						->setGroupBy('asset_class')
						->runSelect()
						->getResult();
						// echo $this->db->getQuery();
						
		return $result;
	}

	public function getAssetList($fields, $search, $sort) {
		$fields = array('a.id id',
		'asset_number',
		'name',
		'assetclass'
	);
		$sort = ($sort) ? $sort : 'asset_number asc';
		$condition = '';
		if ($search) {
			$condition = $this->generateSearch($search, array('asset_number','asset_name'));
		}
		$result = $this->db->setTable("asset_master a")
						->setFields($fields)
						->leftJoin("cost_center c ON c.id = a.department")
						->leftJoin('asset_class ac ON ac.id = a.asset_class')
						->setWhere("a.stat = 'active'")
						->runPagination();

						// echo $this->db->getQuery();
						
		return $result;
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

	public function insertDepreciation($fields, $checked) {
		$orderby = '';
		$condition = 'd.depreciation_date <= DATE(NOW()) ';


		$result = $this->db->setTable('depreciation_schedule d')
							->setFields($fields)
							->leftJoin('asset_master am ON am.asset_number = d.asset_id')
							->leftJoin('asset_class ac ON ac.id = am.asset_class')
							->leftJoin('cost_center cc ON cc.id = am.department')
							->setWhere($condition)
							->setOrderBy($orderby)
							->runPagination();
							// echo $this->db->getQuery();
		return $result;
	}


	public function getDepreciation($fields) {
		$fields = array('am.id id',
		'am.itemcode',
		'asset_class',
		'asset_name',
		'asset_number',
		'sub_number',
		'serial_number',
		'am.description description',
		'asset_location',
		'department',
		'name',
		'accountable_person',
		'commissioning_date',
		'retirement_date',
		'am.useful_life',
		'depreciation_month',
		'd.depreciation_amount',
		'capitalized_cost',
		'purchase_value',
		'balance_value',
		'am.salvage_value',
		'frequency_of_dep',
		'number_of_dep',
		'am.gl_asset gl_asset',
		'am.gl_accdep gl_accdep',
		'am.gl_depexpense gl_depexp',
		'am.stat stat',
		'coa.accountname asset',
		'asd.accountname accdep',
		'dsa.accountname depexp',
		'coa.segment5 a_segment5',
		'asd.segment5 b_segment5',
		'dsa.segment5 c_segment5',
		'ac.assetclass'
	);
		$orderby = '';
		$condition = 'd.depreciation_date <= DATE(NOW()) ';
		$result = $this->db->setTable('depreciation_schedule d')
							->setFields($fields)
							->leftJoin('asset_master am ON am.asset_number = d.asset_id')
							->leftJoin('asset_class ac ON ac.id = am.asset_class')
							->leftJoin('cost_center cc ON cc.id = am.department')
							->leftJoin('chartaccount coa ON coa.id = am.gl_asset')
							->leftJoin('chartaccount asd ON asd.id = am.gl_accdep')
							->leftJoin('chartaccount dsa ON dsa.id = am.gl_depexpense')
							->setWhere($condition)
							->setOrderBy($orderby)
							->runPagination();
							// echo $this->db->getQuery();
		return $result;
	}

}