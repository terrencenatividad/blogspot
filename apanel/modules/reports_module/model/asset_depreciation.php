<?php
class asset_depreciation extends wc_model {

	public function getAsset() {
		$result = $this->db->setTable('asset_master')
							->setFields("asset_number ind, asset_number val, stat stat")
							->setWhere(1)
							->runSelect()
							->getResult();

		return $result;
	}

	public function getAssetClass() {
		$result = $this->db->setTable('asset_class')
							->setFields("id ind, assetclass val, stat stat")
							->setWhere(1)
							->runSelect()
							->getResult();

		return $result;
	}

	public function getAssetDepartment() {
		$result = $this->db->setTable('cost_center')
							->setFields("id ind, name val, stat stat")
							->setWhere(1)
							->runSelect()
							->getResult();

		return $result;
	}

	public function getDepreciationcsv($fields, $sort, $asset_number, $datefilter, $assetclass, $department) {
		$orderby = '';
		$condition = 'd.depreciation_date <= DATE(NOW()) ';
		$datefilter = $this->date->dateDbFormat($datefilter);

		if ($datefilter) {
			$condition .= " AND depreciation_date <= '$datefilter'";
		 }

		if($sort){
			$orderby = $sort;
		}else{
			$orderby = 'd.depreciation_date DESC';
		}
		
		if($asset_number != 'none' && $asset_number != ''){
			$condition .= " AND d.asset_id = '$asset_number'";
		}

		if($assetclass != 'none' && $assetclass != ''){
			$condition .= " AND am.asset_class = '$assetclass'";
		}

		if($department != 'none' && $department != ''){
			$condition .= " AND am.department = '$department'";
		}

		$result = $this->db->setTable('depreciation_schedule d')
							->setFields($fields)
							->leftJoin('asset_master am ON am.asset_number = d.asset_id')
							->leftJoin('asset_class ac ON ac.id = am.asset_class')
							->leftJoin('cost_center cc ON cc.id = am.department')
							->setWhere($condition)
							->setOrderBy($orderby)
							->runSelect()
							->getResult();

		return $result;
	}

	public function getDepreciation($fields, $sort, $asset_number, $datefilter, $assetclass, $department) {
		$orderby = '';
		$condition = 'd.depreciation_date <= DATE(NOW()) ';
		$datefilter = $this->date->dateDbFormat($datefilter);

		if ($datefilter) {
			$condition .= " AND depreciation_date <= '$datefilter'";
		 }

		if($sort){
			$orderby = $sort;
		}else{
			$orderby = 'd.depreciation_date DESC';
		}
		
		if($asset_number != 'none' && $asset_number != ''){
			$condition .= " AND d.asset_id = '$asset_number'";
		}

		if($assetclass != 'none' && $assetclass != ''){
			$condition .= " AND am.asset_class = '$assetclass'";
		}

		if($department != 'none' && $department != ''){
			$condition .= " AND am.department = '$department'";
		}

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

}