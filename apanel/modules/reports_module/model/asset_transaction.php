<?php
class asset_transaction extends wc_model {

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

	public function getAssetTransaction($fields, $sort, $asset, $datefilter, $assetclass, $department) {
		$orderby = '';
		$condition = '';
		$datefilter = $this->date->dateDbFormat($datefilter);

		if ($datefilter) {
			$condition .= " ass.transactiondate <= '$datefilter'";
		 }
		 
		if($sort){
			$orderby = $sort;
		}else{
			$orderby = 'ass.transactiondate DESC';
		}
		
		if($asset != 'none' && $asset != ''){
			$condition .= " AND ass.asset_number = '$asset'";
		}

		if($assetclass != 'none' && $assetclass != ''){
			$condition .= " AND am.asset_class = '$assetclass'";
		}

		if($department != 'none' && $department != ''){
			$condition .= " AND am.department = '$department'";
		}

		$result = $this->db->setTable('asset_transaction ass')
							->setFields($fields)
							->leftJoin('asset_master am ON am.asset_number = ass.asset_number')
							->leftJoin('asset_class ac ON ac.id = ass.asset_class')
							->leftJoin('cost_center cc ON cc.id = am.department')
							->setWhere($condition)
							->setOrderBy($orderby)
							->runPagination();
		return $result;
	}

	public function getAssetTransactioncsv($fields, $sort, $asset, $datefilter, $assetclass, $department) {
		$orderby = '';
		$condition = '';
		$datefilter = $this->date->dateDbFormat($datefilter);

		if ($datefilter) {
			$condition .= " ass.transactiondate <= '$datefilter'";
		 }
		 
		if($sort){
			$orderby = $sort;
		}else{
			$orderby = 'ass.transactiondate DESC';
		}
		
		if($asset != 'none' && $asset != ''){
			$condition .= " AND ass.asset_number = '$asset'";
		}

		if($assetclass != 'none' && $assetclass != ''){
			$condition .= " AND am.asset_class = '$assetclass'";
		}

		if($department != 'none' && $department != ''){
			$condition .= " AND am.department = '$department'";
		}

		$result = $this->db->setTable('asset_transaction ass')
							->setFields($fields)
							->leftJoin('asset_master am ON am.asset_number = ass.asset_number')
							->leftJoin('asset_class ac ON ac.id = ass.asset_class')
							->leftJoin('cost_center cc ON cc.id = am.department')
							->setWhere($condition)
							->setOrderBy($orderby)
							->runSelect()
							->getResult();

		return $result;
	}

}