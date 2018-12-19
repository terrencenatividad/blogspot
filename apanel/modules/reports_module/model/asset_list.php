<?php
class asset_list extends wc_model {

	public function getAssetMasterListcsv($fields, $sort, $asset, $datefilter, $assetclass, $department) {
		$orderby = '';
		$condition = '';
		if($sort){
			$orderby = $sort;
		}else{
			$orderby = 'am.id ASC';
		}
		$datefilter = $this->date->dateDbFormat($datefilter);

		if ($datefilter) {
			$condition .= " am.entereddate <= '$datefilter 11:59:59'";
		 }

		if($asset != 'none' && $asset != ''){
			$condition .= " AND am.asset_number = '$asset'";
		}

		if($assetclass != 'none' && $assetclass != ''){
			$condition .= " AND am.asset_class = '$assetclass'";
		}

		if($department != 'none' && $department != ''){
			$condition .= " AND am.department = '$department'";
		}
		$result = $this->db->setTable("asset_master am")
							->setFields($fields)
							->leftJoin("asset_class ac ON ac.id = am.asset_class")
							->leftJoin("chartaccount c ON c.id = am.gl_asset")
							->leftJoin("chartaccount o ON o.id = am.gl_accdep")
							->leftJoin("chartaccount a ON a.id = am.gl_depexpense")
							->leftJoin("cost_center cc ON cc.id = am.department")
							->setWhere($condition)
							->setOrderBy($orderby)
							->runSelect()
							->getResult();

		return $result;
	
	}

	public function getAssetMasterList($fields, $sort, $asset, $datefilter, $assetclass, $department) {
		$orderby = '';
		$condition = "";

		if($sort){
			$orderby = $sort;
		}else{
			$orderby = 'am.id ASC';
		}
		// $datefilter = $this->date->dateDbFormat($datefilter);

		// if ($datefilter) {
		// 	$condition .= " AND am.entereddate <= '$datefilter 11:59:59'";
		//  }
		
		if($asset != 'none' && $asset != ''){
			$condition .= " AND am.asset_number = '$asset'";
		}

		if($assetclass != 'none' && $assetclass != ''){
			$condition .= " AND am.asset_class = '$assetclass'";
		}

		if($department != 'none' && $department != ''){
			$condition .= " AND am.department = '$department'";
		}
		$result = $this->db->setTable("asset_master am")
							->setFields($fields)
							->leftJoin("asset_class ac ON ac.id = am.asset_class")
							->leftJoin("chartaccount c ON c.id = am.gl_asset")
							->leftJoin("chartaccount o ON o.id = am.gl_accdep")
							->leftJoin("chartaccount a ON a.id = am.gl_depexpense")
							->leftJoin("cost_center cc ON cc.id = am.department")
							->setWhere("am.stat = 'active' $condition")
							->setOrderBy($orderby)
							->runPagination();
// echo $this->db->getQuery();
		return $result;
	
	}
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


}