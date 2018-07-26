<?php
class ar_aging extends wc_model {

	public function getCustomerList() {
		$result = $this->db->setTable('partners p')
							->setFields("DISTINCT p.partnercode ind, p.partnername val")
							->leftJoin("accountsreceivable ar ON p.partnercode = ar.customer")
							->setWhere("p.partnercode != '' AND p.partnertype = 'customer' AND p.stat = 'active' AND ar.stat = 'posted'")
							->setGroupBy("val")
							->setOrderBy("val")
							->runSelect()
							->getResult();

		return $result;
	}

	public function getArAgingQuery($datefilter, $customer) {
		$condition = ($customer && $customer != 'none') ? " AND customer = '$customer'" : '';

		$payment_query = $this->db->setTable('rv_application rva')
									->setFields('SUM(rva.amount) payments, rva.arvoucherno, rva.companycode')
									->leftJoin('receiptvoucher rv ON rv.voucherno = rva.voucherno AND rv.companycode = rva.companycode')
									->setWhere("rv.stat = 'posted' AND rv.transactiondate <= '$datefilter'")
									->setGroupBy('rva.arvoucherno')
									->buildSelect();

		$query = $this->db->setTable('accountsreceivable ar')
							->setFields("p.partnername customer, ar.voucherno, ar.transactiondate, ar.terms, ar.amount, ar.duedate, IF (ar.duedate < DATE_SUB('$datefilter', INTERVAL 60 DAY), ar.amount - IFNULL(rva.payments, 0), 0) oversixty,
							IF (ar.duedate < DATE_SUB('$datefilter', INTERVAL 30 DAY) AND ar.duedate > DATE_SUB('$datefilter', INTERVAL 60 DAY), ar.amount - IFNULL(rva.payments, 0), 0) sixty,
							IF (ar.duedate < '$datefilter' AND ar.duedate >= DATE_SUB('$datefilter', INTERVAL 30 DAY), ar.amount - IFNULL(rva.payments, 0), 0) thirty,
							IF (ar.duedate >= '$datefilter', ar.amount - IFNULL(rva.payments, 0), 0) current, (ar.amount - IFNULL(rva.payments, 0)) balance, ar.companycode")
							->leftJoin("($payment_query) rva ON rva.arvoucherno = ar.voucherno AND rva.companycode = ar.companycode")
							->leftJoin('partners p ON p.partnercode = ar.customer AND p.companycode = ar.companycode')
							->setWhere("ar.stat = 'posted' AND ar.transactiondate <= '$datefilter'" . $condition)
							->setHaving('balance > 0');

		return $query;
	}

	public function getArAging($datefilter, $customer) {
		$result = $this->getArAgingQuery($datefilter, $customer)
						->runPagination();

		return $result;
	}

	public function getArAgingExport($datefilter, $customer) {
		$result = $this->getArAgingQuery($datefilter, $customer)
						->runSelect()
						->getResult();

		return $result;
	}

	public function getArAgingTotal($datefilter, $customer) {
		$aging_query = $this->getArAgingQuery($datefilter, $customer)
							->buildSelect();

		$result = $this->db->setTable("($aging_query) aq")
							->setFields("customer, voucherno, transactiondate, terms, amount, duedate, SUM(oversixty) oversixty_total, SUM(sixty) sixty_total, SUM(thirty) thirty_total, SUM(current) current_total, SUM(balance) balance_total")
							->runSelect()
							->getRow();

		return $result;
	}

}