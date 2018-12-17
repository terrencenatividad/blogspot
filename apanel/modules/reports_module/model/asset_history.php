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

	public function getAssetHistory($fields,$sort,$asset,$datefilter) {
		$orderby = '';
		$condition = '';
		$datefilter = $this->date->dateDbFormat($datefilter);

		if ($datefilter) {
			$condition .= " transactiondate <= '$datefilter'";
		 }
		 
		if($sort){
			$orderby = $sort;
		}else{
			$orderby = 'at.transactiondate DESC';
		}
		
		if($asset != 'none' && $asset != ''){
			$condition .= " AND asset_number = '$asset'";
		}

		$result = $this->db->setTable('asset_transaction at')
							->setFields($fields)
							->leftJoin('asset_class ac ON ac.id = at.asset_class')
							->setWhere($condition)
							->setOrderBy($orderby)
							->runPagination();
		return $result;
	}

	public function getAssetHistorycsv($fields,$sort,$asset,$datefilter) {
		$orderby = '';
		$condition = '';
		$datefilter = $this->date->dateDbFormat($datefilter);

		if ($datefilter) {
			$condition .= " transactiondate <= '$datefilter'";
		 }
		 
		if($sort){
			$orderby = $sort;
		}else{
			$orderby = 'at.transactiondate DESC';
		}
		
		if($asset != 'none' && $asset != ''){
			$condition .= " AND asset_number = '$asset'";
		}

		$result = $this->db->setTable('asset_transaction at')
							->setFields($fields)
							->leftJoin('asset_class ac ON ac.id = at.asset_class')
							->setWhere($condition)
							->setOrderBy($orderby)
							->runSelect()
							->getResult();
		return $result;
	}

}