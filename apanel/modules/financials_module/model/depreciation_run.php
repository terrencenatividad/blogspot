<?php
class depreciation_run extends wc_model {

	public function getAsset() {
		$fields = array(
		'assetclass',
		'ac.id'
	);
		
	$condition = '';
	$result = $this->db->setTable('depreciation_schedule d')
						->setFields($fields)
						->leftJoin('asset_master am ON am.asset_number = d.asset_id')
						->leftJoin("cost_center c ON c.id = am.department")
						->leftJoin('asset_class ac ON ac.id = am.asset_class')
						->setWhere("depreciation_date <= DATE(NOW())")
						->setGroupBy('assetclass')
						->runSelect()
						->getResult();
						
		return $result;
	}

	public function getAssetDetails($id) {
		$result = $this->db->setTable('asset_master a') 
						->setFields('a.department, a.asset_number, a.department, a.useful_life, a.capitalized_cost, a.balance_value, a.salvage_value,c.name')
						->leftJoin('depreciation_schedule d ON asset_id = a.asset_number')
						->leftJoin("cost_center c ON c.id = a.department")
						->setWhere("asset_class = '$id' AND depreciation_date <= DATE(NOW())")
						->setGroupBy('asset_number')
						->runSelect()
						->getResult();

		return $result;
	}

	public function getDepreciationDetails($id) {
		$fields = array(
			'coa.accountname asset',
			'asd.accountname accdep',
			'dsa.accountname depexp',
			'coa.segment5 a_segment5',
			'asd.segment5 b_segment5',
			'dsa.segment5 c_segment5',
			'd.depreciation_date',
			'd.depreciation_amount',
			'd.accumulated_dep'
		);
		$result = $this->db->setTable('depreciation_schedule d') 
						->setFields($fields)
						->leftJoin('chartaccount coa ON coa.id = d.gl_asset')
						->leftJoin('chartaccount asd ON asd.id = d.gl_accdep')
						->leftJoin('chartaccount dsa ON dsa.id = d.gl_depexpense')
						->setWhere("d.asset_id = '$id' AND d.depreciation_date <= DATE(NOW())")
						->runSelect()
						->getResult();

		return $result;
	}

	public function getAsset123() {
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
						->runSelect()
						->getResult();

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
						->runSelect()
						->getResult();
						
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
						
		return $result;
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

	public function getAssetClass($checked){
		$result = $this->db->setTable("asset_master am")
						->setFields('asset_class,assetclass')
						->leftJoin('asset_class ac ON ac.id = am.asset_class')
						->setWhere("am.id IN ($checked)")
						->setGroupBy('asset_class')
						->runSelect()
						->getResult();
		return $result;
	}

	public function getAssetDetails2($id) {
		$result = $this->db->setTable('asset_master a') 
						->setFields('a.department, a.asset_number, a.department, a.useful_life, a.capitalized_cost, a.balance_value, a.salvage_value,c.name')
						->leftJoin('depreciation_schedule d ON asset_id = a.asset_number')
						->leftJoin("cost_center c ON c.id = a.department")
						->setWhere("asset_class = '$id'")
						->setGroupBy('asset_number')
						->runSelect()
						->getResult();

		return $result;
	}
}