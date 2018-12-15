<?php
class asset_depreciation extends wc_model {

	public function getDepreciationcsv($fields) {
		$result = $this->db->setTable('depreciation_schedule d')
							->setFields($fields)
							->leftJoin('asset_master am ON am.asset_number = d.asset_id')
							->leftJoin('asset_class ac ON ac.id = am.asset_class')			
							->setWhere("d.depreciation_date <= DATE(NOW())")
							->setOrderBy('d.depreciation_date DESC')
							->runSelect()
							->getResult();

		return $result;
	}

	public function getDepreciation($fields) {
		$result = $this->db->setTable('depreciation_schedule d')
							->setFields($fields)
							->leftJoin('asset_master am ON am.asset_number = d.asset_id')
							->leftJoin('asset_class ac ON ac.id = am.asset_class')			
							->setWhere("d.depreciation_date <= DATE(NOW())")
							->setOrderBy('d.depreciation_date DESC')
							->runPagination();
		return $result;
	}

}