<?php
class depreciation_run extends wc_model {

	public function getAssetMasterList($fields,$sort) {
		$fields = array(
			'am.id',
			'am.itemcode',
			'am.asset_class',
			'am.asset_name',
			'am.asset_number',
			'am.sub_number',
			'am.serial_number',
			'am.description',
			'am.asset_location',
			'am.department',
			'am.accountable_person',
			'am.commissioning_date',
			'am.retirement_date',
			'am.useful_life',
			'am.depreciation_month',
			'd.depreciation_amount',
			'am.capitalized_cost',
			'purchase_value',
			'balance_value',
			'am.salvage_value',
			'frequency_of_dep',
			'number_of_dep',
			'am.gl_asset',
			'am.gl_accdep',
			'am.gl_depexpense',
			'am.stat',
			'assetclass',
			'accumulated_dep',
			'depreciation_date',
			'CONCAT(c.segment5," - ",c.accountname) asset',
			'CONCAT(o.segment5," - ",o.accountname) accdep',
			'CONCAT(a.segment5," - ",a.accountname) depexp',
			'cc.name'
		);
		$orderby = '';
		$condition = "";

		if($sort){
			$orderby = $sort;
		}
	
		$date = $this->date->dateDbFormat();
		
		$condition = "MONTH(depreciation_date) = MONTH('$date') AND YEAR(depreciation_date) = YEAR('$date')";

		$result = $this->db->setTable('asset_master am') 
							->setFields($fields)
							->leftJoin('depreciation_schedule d ON asset_id = am.asset_number')
							->leftJoin("cost_center cc ON cc.id = am.department")
							->leftJoin('asset_class ac ON ac.id = am.asset_class')
							->leftJoin('chartaccount c ON c.id = am.gl_asset')
							->leftJoin('chartaccount o ON o.id = am.gl_accdep')
							->leftJoin('chartaccount a ON a.id = am.gl_depexpense')
							->setWhere($condition)
							->setOrderBy($orderby)
							->runSelect()
							->getResult();

		return $result;
	
	}

	public function getAsset2($fields, $search, $sort ,$checked) {
		$fields = array(
			'am.id',
			'am.itemcode',
			'am.asset_class',
			'am.asset_name',
			'am.asset_number',
			'am.sub_number',
			'am.serial_number',
			'am.description',
			'am.asset_location',
			'am.department',
			'am.accountable_person',
			'am.commissioning_date',
			'am.retirement_date',
			'am.useful_life',
			'am.depreciation_month',
			'd.depreciation_amount',
			'am.capitalized_cost',
			'purchase_value',
			'balance_value',
			'am.salvage_value',
			'frequency_of_dep',
			'number_of_dep',
			'am.gl_asset',
			'am.gl_accdep',
			'am.gl_depexpense',
			'am.stat',
			'assetclass',
			'accumulated_dep',
			'depreciation_date',
			'CONCAT(c.segment5," - ",c.accountname) asset',
			'CONCAT(o.segment5," - ",o.accountname) accdep',
			'CONCAT(a.segment5," - ",a.accountname) depexp',
			'cc.name'
		);
		$sort = ($sort) ? $sort : 'asset_number asc';
		$condition = '';
		if ($search) {
			$condition = $this->generateSearch($search, array('asset_number','asset_name'));
		}
		if($checked){
			$condition .=  " am.id IN ($checked) ";
		}

		$date = $this->date->dateDbFormat();
		
		$condition .= "AND MONTH(depreciation_date) = MONTH('$date') AND YEAR(depreciation_date) = YEAR('$date')";

		$result = $this->db->setTable('asset_master am') 
							->setFields($fields)
							->leftJoin('depreciation_schedule d ON asset_id = am.asset_number')
							->leftJoin("cost_center cc ON cc.id = am.department")
							->leftJoin('asset_class ac ON ac.id = am.asset_class')
							->leftJoin('chartaccount c ON c.id = am.gl_asset')
							->leftJoin('chartaccount o ON o.id = am.gl_accdep')
							->leftJoin('chartaccount a ON a.id = am.gl_depexpense')
							->setWhere($condition)
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
							->setValues(array('asset_id' => $assetnumber,'itemcode' => $itemcode,'depreciation_date' => $final, 'depreciation_amount' => $depreciation_amount, 'accumulated_dep' => $depreciation, 'gl_asset' => $gl_asset, 'gl_accdep' => $gl_accdep, 'gl_depexpense' => $gl_depexp))
							->runInsert();
		
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
			$condition = ' AND ' .$this->generateSearch($search, array('a.asset_number','assetclass','c.name'));
		}
		$result = $this->db->setTable("asset_master a")
						->setFields($fields)
						->leftJoin("cost_center c ON c.id = a.department")
						->leftJoin('asset_class ac ON ac.id = a.asset_class')
						->setWhere("a.stat = 'active' $condition")
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
}