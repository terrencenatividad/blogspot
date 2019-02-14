<?php
class asset_history extends wc_model {

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

	public function getAssetHistory($fields, $sort, $asset, $datefilter, $assetclass, $department) {
		$orderby = '';
		$condition = '';

		$datefilterArr		= explode(' - ',$datefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
		

		if ($datefilter) {
			$condition .= " ass.transactiondate AND transactiondate BETWEEN '$datefilterFrom 11:59:59' AND '$datefilterTo 11:59:59'";
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
							// echo $this->db->getQuery();
		return $result;
	}

	public function getAssetHistorycsv($fields,$sort,$asset,$datefilter, $assetclass, $department) {
		$orderby = '';
		$condition = '';
		
		$datefilterArr		= explode(' - ',$datefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
		

		if ($datefilter) {
			$condition .= " ass.transactiondate AND transactiondate BETWEEN '$datefilterFrom 11:59:59' AND '$datefilterTo 11:59:59'";
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