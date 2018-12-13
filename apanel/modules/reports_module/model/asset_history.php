<?php
class asset_history extends wc_model {

	public function getSupplierList() {
		$result = $this->db->setTable('partners p')
							->setFields("DISTINCT p.partnercode ind, p.partnername val")
							->leftJoin("accountspayable ap ON p.partnercode = ap.vendor")
							->setWhere("p.partnercode != '' AND p.partnertype = 'supplier' AND p.stat = 'active' AND ap.stat = 'posted'")
							->setGroupBy("val")
							->setOrderBy("val")
							->runSelect()
							->getResult();

		return $result;
	}

	public function getAssetHistory($fields) {

		$result = $this->db->setTable('asset_transaction at')
							->setFields($fields)
							->leftJoin('asset_class ac ON ac.id = at.asset_class')
							->setWhere(1)
							->setOrderBy('at.transactiondate DESC')
							->runPagination();
		return $result;
	}

}