<?php
class ap_aging extends wc_model {

	public function getSupplierList() {
		$result = $this->db->setTable('partners p')
							->setFields("DISTINCT partnercode ind, CONCAT(partnercode,' - ',partnername) val")
							->leftJoin("accountspayable ap ON p.partnercode = ap.vendor")
							->setWhere("p.partnercode != '' AND p.partnertype = 'supplier' AND p.stat = 'active' AND ap.stat = 'posted'")
							->setGroupBy("val")
							->setOrderBy("val")
							->runSelect()
							->getResult();

		return $result;
	}

	public function getArAgingQuery($datefilter, $supplier) {
		$condition = ($supplier && $supplier != 'none') ? " AND vendor = '$supplier'" : '';

		$payment_query = $this->db->setTable('pv_application pva')
									->setFields('(SUM(IFNULL(pva.amount,0))+SUM(IFNULL(pva.discount,0))) payments, pva.apvoucherno, pva.companycode')
									->leftJoin('paymentvoucher pv ON pv.voucherno = pva.voucherno AND pv.companycode = pva.companycode')
									->setWhere("pv.stat IN('open','posted') AND pv.transactiondate <= '$datefilter'")
									->setGroupBy('pva.apvoucherno')
									->buildSelect();

		$query = $this->db->setTable('accountspayable ap')
							->setFields("p.partnername supplier, ap.voucherno, ap.transactiondate, ap.terms, ap.amount, ap.duedate, IF (ap.duedate < DATE_SUB('$datefilter', INTERVAL 60 DAY), ap.amount - IFNULL(pva.payments, 0), 0) oversixty,
							IF (ap.duedate < DATE_SUB('$datefilter', INTERVAL 30 DAY) AND ap.duedate >= DATE_SUB('$datefilter', INTERVAL 60 DAY), ap.amount - IFNULL(pva.payments, 0), 0) sixty,
							IF (ap.duedate < '$datefilter' AND ap.duedate >= DATE_SUB('$datefilter', INTERVAL 30 DAY), ap.amount - IFNULL(pva.payments, 0), 0) thirty,
							IF (ap.duedate >= '$datefilter', ap.amount - IFNULL(pva.payments, 0), 0) current, (ap.amount - IFNULL(pva.payments, 0)) balance, ap.companycode")
							->leftJoin("($payment_query) pva ON pva.apvoucherno = ap.voucherno AND pva.companycode = ap.companycode")
							->leftJoin('partners p ON p.partnercode = ap.vendor AND p.companycode = ap.companycode')
							->setWhere("ap.stat = 'posted' AND ap.transactiondate <= '$datefilter'" . $condition)
							->setHaving('balance > 0');

		return $query;
	}

	public function getArAging($datefilter, $supplier) {
		$result = $this->getArAgingQuery($datefilter, $supplier)
						->runPagination();

		return $result;
	}

	public function getArAgingExport($datefilter, $supplier) {
		$result = $this->getArAgingQuery($datefilter, $supplier)
						->runSelect()
						->getResult();

		return $result;
	}

	public function getArAgingTotal($datefilter, $supplier) {
		$aging_query = $this->getArAgingQuery($datefilter, $supplier)
							->buildSelect();

		$result = $this->db->setTable("($aging_query) aq")
							->setFields("supplier, voucherno, transactiondate, terms, amount, duedate, SUM(oversixty) oversixty_total, SUM(sixty) sixty_total, SUM(thirty) thirty_total, SUM(current) current_total, SUM(balance) balance_total")
							->runSelect()
							->getRow();

		return $result;
	}

}