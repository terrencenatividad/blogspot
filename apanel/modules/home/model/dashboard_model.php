<?php
class dashboard_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->year					= date('Y');
		$this->period				= 1;
		$this->getPeriodStart();
		$this->current_month_query	= $this->getMonthly();
		$this->previous_month_query	= $this->getMonthly(1);
	}

	public function getYear() {
		return $this->year;
	}

	private function getPeriodStart() {
		$result = $this->db->setTable('company')
							->setFields(array('taxyear', "MONTH(STR_TO_DATE(periodstart,'%b')) periodstart"))
							->setLimit(1)
							->runSelect()
							->getRow();

		if ($result->taxyear == 'fiscal') {
			$this->period = $result->periodstart;
			if ($this->period > date('n')) {
				$this->year = date('Y') - 1;
			}
		}
	}

	private function getMonthly($y = 0) {
		$months	= array();
		$year	= $this->year - $y;
		$period	= $this->period;
		for ($x = 1; $x <= 12; $x++) {
			if ($period > 12) {
				$period = 1;
				$year++;
			}
			$months[] = "SELECT $period month, '$year' year, '" . COMPANYCODE . "' companycode";
			$period++;
		}

		return implode(' UNION ALL ', $months);
	}

	public function getInvoices() {
		$result = $this->db->setTable('salesinvoice')
							->setFields('COUNT(*) quantity')
							->setWhere("stat NOT IN ('cancelled', 'temporary')")
							->runSelect()
							->getRow();
		
		$count = ($result) ? $result->quantity : 0;

		return $count;
	}

	public function getPurchases() {
		$result = $this->db->setTable('purchasereceipt')
							->setFields('COUNT(*) quantity')
							->setWhere("stat NOT IN ('Cancelled', 'temporary')")
							->runSelect()
							->getRow();
		
		$count = ($result) ? $result->quantity : 0;

		return $count;
	}

	public function getItems() {
		$result = $this->db->setTable('items')
							->setFields('COUNT(*) quantity')
							->runSelect()
							->getRow();
		
		$count = ($result) ? $result->quantity : 0;

		return $count;
	}

	public function getJournalVouchers() {
		$result = $this->db->setTable('journalvoucher')
							->setFields('COUNT(*) quantity')
							->setWhere("stat = 'posted' AND transtype = 'JV'")
							->runSelect()
							->getRow();
		
		$count = ($result) ? $result->quantity : 0;

		return $count;
	}

	public function getRevenuesAndExpenses() {
		$coa_cost 	= 	$this->db->setTable("chartaccount")
								->setFields("id")
								->setWhere("accountclasscode IN ('COST','COSTSA')")
								->buildSelect();

		$coa_rev 	= 	$this->db->setTable("chartaccount")
								->setFields("id")
								->setWhere("accountclasscode IN ('REV','REVENU')")
								->buildSelect();

		$current 	=	$this->db->setTable("({$this->current_month_query}) m")
								->leftJoin("($coa_rev) n ON 1 = 1")
								->leftJoin("balance_table pr ON MONTH(pr.transactiondate) = m.month AND YEAR(pr.transactiondate) = m.year AND pr.accountcode = n.id ")
								->setFields("IFNULL(SUM(pr.credit)-SUM(pr.debit), 0) revenue, CONCAT(m.year, '-', m.month) month")
								->setGroupBy('m.month')
								->setOrderBy('m.year, m.month')
								->runSelect()
								->getResult();
								// echo $this->db->getQuery();

		$current2 	=	$this->db->setTable("({$this->current_month_query}) m")
								->leftJoin("($coa_cost) n ON 1 = 1")
								->leftJoin("balance_table pr ON MONTH(pr.transactiondate) = m.month AND YEAR(pr.transactiondate) = m.year AND pr.accountcode = n.id ")
								->setFields("IFNULL(SUM(pr.credit)-SUM(pr.debit), 0) expense, CONCAT(m.year, '-', m.month) month")
								->setGroupBy('m.month')
								->setOrderBy('m.year, m.month')
								->runSelect()
								->getResult();
								// echo $this->db->getQuery();

		$previous 	=	$this->db->setTable("({$this->previous_month_query}) m")
								->leftJoin("($coa_rev) n ON 1 = 1")
								->leftJoin("balance_table pr ON MONTH(pr.transactiondate) = m.month AND YEAR(pr.transactiondate) = m.year AND pr.accountcode = n.id ")
								->setFields("IFNULL(SUM(pr.credit)-SUM(pr.debit), 0) revenue, CONCAT(m.year, '-', m.month) month")
								->setGroupBy('m.month')
								->setOrderBy('m.year, m.month')
								->runSelect()
								->getResult();

		$previous2 	=	$this->db->setTable("({$this->previous_month_query}) m")
								->leftJoin("($coa_cost) n ON 1 = 1")
								->leftJoin("balance_table pr ON MONTH(pr.transactiondate) = m.month AND YEAR(pr.transactiondate) = m.year AND pr.accountcode = n.id ")
								->setFields("IFNULL(SUM(pr.credit)-SUM(pr.debit), 0) expense, CONCAT(m.year, '-', m.month) month")
								->setGroupBy('m.month')
								->setOrderBy('m.year, m.month')
								->runSelect()
								->getResult();

		foreach ($current as $key => $row) {
			$current[$key]->expense = $current2[$key]->expense;
		}

		foreach ($previous as $key => $row) {
			$previous[$key]->expense = $previous2[$key]->expense;
		}

		$rae = array(
			'current'	=> $current,
			'previous'	=> $previous
		);
		return $rae;
	}

	public function getAging() {
		$ar_aging = $this->getReceivableAging();
		$ap_aging = $this->getPayableAging();

		$aging = array(
			'ar' => $this->getVoucherApplication($ar_aging),
			'ap' => $this->getVoucherApplication($ap_aging)
		);
		return $aging;
	}
	
	private function getReceivableAging() {
		$payment_query = $this->db->setTable('rv_application rva')
									->setFields('(SUM(IFNULL(rva.amount,0)) + SUM(IFNULL(rva.discount,0)) + SUM(IFNULL(rva.credits_used,0)) + SUM(IFNULL(rva.overpayment,0))) payments, rva.arvoucherno, rva.companycode')
									->leftJoin('receiptvoucher rv ON rv.voucherno = rva.voucherno AND rv.companycode = rva.companycode')
									->setWhere("rv.stat IN('open','posted') AND rv.transactiondate <= CURDATE()")
									->setGroupBy('rva.arvoucherno')
									->buildSelect();

		$aging_query = $this->db->setTable('accountsreceivable ar')
								->setFields("p.partnername customer, ar.voucherno, ar.transactiondate, ar.terms, ar.amount, ar.duedate, IF (ar.duedate < DATE_SUB(CURDATE(), INTERVAL 60 DAY), ar.amount - IFNULL(rva.payments, 0), 0) oversixty,
								IF (ar.duedate < DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND ar.duedate >= DATE_SUB(CURDATE(), INTERVAL 60 DAY), ar.amount - IFNULL(rva.payments, 0), 0) sixty,
								IF (ar.duedate < CURDATE() AND ar.duedate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY), ar.amount - IFNULL(rva.payments, 0), 0) thirty,
								IF (ar.duedate >= CURDATE(), ar.amount - IFNULL(rva.payments, 0), 0) today, (ar.amount - IFNULL(rva.payments, 0)) balance, ar.companycode")
								->leftJoin("($payment_query) rva ON rva.arvoucherno = ar.voucherno AND rva.companycode = ar.companycode")
								->leftJoin('partners p ON p.partnercode = ar.customer AND p.companycode = ar.companycode AND p.partnertype="customer"')
								->setWhere("ar.stat = 'posted' AND ar.transactiondate <= CURDATE()")
								->setHaving('balance > 0')
								->buildSelect();

		$result = $this->db->setTable("($aging_query) aq")
							->setFields("SUM(oversixty) oversixty_total, SUM(sixty) sixty_total, SUM(thirty) thirty_total, SUM(today) today_total, SUM(balance) balance_total")
							->runSelect()
							->getRow();
		return $result;
	}

	private function getPayableAging() {
		$payment_query = $this->db->setTable('pv_application pva')
									->setFields('(SUM(IFNULL(pva.amount,0))+SUM(IFNULL(pva.discount,0))) payments, pva.apvoucherno, pva.companycode')
									->leftJoin('paymentvoucher pv ON pv.voucherno = pva.voucherno AND pv.companycode = pva.companycode')
									->setWhere("pv.stat IN('open','posted') AND pv.transactiondate <= CURDATE()")
									->setGroupBy('pva.apvoucherno')
									->buildSelect();

		$aging_query = $this->db->setTable('accountspayable ap')
								->setFields("p.partnername supplier, ap.voucherno, ap.transactiondate, ap.terms, ap.amount, ap.duedate, IF (ap.duedate < DATE_SUB(CURDATE(), INTERVAL 60 DAY), ap.amount - IFNULL(pva.payments, 0), 0) oversixty,
								IF (ap.duedate < DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND ap.duedate >= DATE_SUB(CURDATE(), INTERVAL 60 DAY), ap.amount - IFNULL(pva.payments, 0), 0) sixty,
								IF (ap.duedate < CURDATE() AND ap.duedate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY), ap.amount - IFNULL(pva.payments, 0), 0) thirty,
								IF (ap.duedate >= CURDATE(), ap.amount - IFNULL(pva.payments, 0), 0) today, (ap.amount - IFNULL(pva.payments, 0)) balance, ap.companycode")
								->leftJoin("($payment_query) pva ON pva.apvoucherno = ap.voucherno AND pva.companycode = ap.companycode")
								->leftJoin('partners p ON p.partnercode = ap.vendor AND p.companycode = ap.companycode AND p.partnertype="supplier"')
								->setWhere("ap.stat = 'posted' AND ap.transactiondate <= CURDATE()")
								->setHaving('balance > 0')
								->buildSelect();


		$result = $this->db->setTable("($aging_query) aq")
							->setFields("SUM(oversixty) oversixty_total, SUM(sixty) sixty_total, SUM(thirty) thirty_total, SUM(today) today_total, SUM(balance) balance_total")
							->runSelect()
							->getRow();

		return $result;
	}

	private function getVoucherApplication($row) {
		if ($row) {
			$data = array();
			$data[] = array('label' => 'Today', 'value' => $row->today_total);
			$data[] = array('label' => '1 to 30 days', 'value' => $row->thirty_total);
			$data[] = array('label' => '31 to 60 days', 'value' => $row->sixty_total);
			$data[] = array('label' => '60 days over', 'value' => $row->oversixty_total);
			return $data;
		} else {
			return array(
				array('label' => 'No Aging', 'value' => '0')
			);
		}
	}

	public function getSalesAndPurchases() {
		$sales		= $this->db->setTable("({$this->current_month_query}) m")
								->leftJoin("salesinvoice si ON MONTH(si.transactiondate) = m.month AND si.companycode = m.companycode AND YEAR(si.transactiondate) = m.year AND si.stat NOT IN ('temporary', 'cancelled')")
								->setFields("IFNULL(SUM(amount), 0) value, CONCAT(m.year, '-', m.month) month")
								->setGroupBy('m.month')
								->setOrderBy('m.year, m.month')
								->runSelect()
								->getResult();

		$purchases	= $this->db->setTable("({$this->current_month_query}) m")
								->leftJoin("purchasereceipt pr ON MONTH(pr.transactiondate) = m.month AND pr.companycode = m.companycode AND YEAR(pr.transactiondate) = m.year AND pr.stat NOT IN ('temporary', 'Cancelled')")
								->setFields("IFNULL(SUM(netamount), 0) value, CONCAT(m.year, '-', m.month) month")
								->setGroupBy('m.month')
								->setOrderBy('m.year, m.month')
								->runSelect()
								->getResult();

		$aging = array(
			'sales'		=> $sales,
			'purchases'	=> $purchases
		);
		return $aging;
	}

	public function getHeavySalesAndPurchases() {
		$sales		= $this->db->setTable("({$this->current_month_query}) m")
								->setFields("IFNULL(SUM(si.amount), 0) value, CONCAT(m.year, '-', m.month) month")
								->leftJoin("salesinvoice si ON MONTH(si.transactiondate) = m.month AND si.companycode = m.companycode AND YEAR(si.transactiondate) = m.year AND si.stat NOT IN ('temporary', 'cancelled')")
								->leftJoin("salesinvoice_details sid ON sid.voucherno = si.voucherno AND sid.companycode = m.companycode")
								->leftJoin("items itm ON itm.itemcode = sid.itemcode AND itm.companycode = m.companycode")
								->leftJoin("itemclass itc ON itc.id = itm.classid AND itc.companycode = m.companycode AND itc.revenue_account = '473'")
								->setGroupBy('m.month')
								->setOrderBy('m.year, m.month')
								->runSelect()
								->getResult();

		$purchases	= $this->db->setTable("({$this->current_month_query}) m")
								->leftJoin("purchasereceipt pr ON MONTH(pr.transactiondate) = m.month AND pr.companycode = m.companycode AND YEAR(pr.transactiondate) = m.year AND pr.stat NOT IN ('temporary', 'Cancelled')")
								->setFields("IFNULL(SUM(netamount), 0) value, CONCAT(m.year, '-', m.month) month")
								->setGroupBy('m.month')
								->setOrderBy('m.year, m.month')
								->runSelect()
								->getResult();

		$aging = array(
			'heavy_sales'		=> $sales,
			'heavy_purchases'	=> $purchases
		);
		return $aging;
	}

	public function getSales() {
		$equipment_account 	= '40101';
		$parts_account 		= '40111';
		$service_account 	= '40121';

		$sales_equipment		= $this->db->setTable("({$this->current_month_query}) m")
								->setFields("CONCAT(m.year, '-', m.month) month, IFNULL(SUM(bal.credit) - SUM(bal.debit), 0) equipment, '0' parts, '0' service")
								->leftJoin("balance_table bal ON MONTH(bal.transactiondate) = m.month AND bal.companycode = m.companycode AND YEAR(bal.transactiondate) = m.year AND (select coa.segment5 from chartaccount coa where coa.id = bal.accountcode and coa.companycode = m.companycode) = '$equipment_account' ")
								->setGroupBy('m.month')
								->setOrderBy('m.year, m.month')
								->runSelect()
								->getResult();
		$sales_parts			= $this->db->setTable("({$this->current_month_query}) m")
								->setFields("CONCAT(m.year, '-', m.month) month, '0' equipment, IFNULL(SUM(bal.credit) - SUM(bal.debit), 0) parts, '0' service")
								->leftJoin("balance_table bal ON MONTH(bal.transactiondate) = m.month AND bal.companycode = m.companycode AND YEAR(bal.transactiondate) = m.year AND (select coa.segment5 from chartaccount coa where coa.id = bal.accountcode and coa.companycode = m.companycode) = '$parts_account'")
								->setGroupBy('m.month')
								->setOrderBy('m.year, m.month')
								->runSelect()
								->getResult();
		$sales_service			= $this->db->setTable("({$this->current_month_query}) m")
								->setFields("CONCAT(m.year, '-', m.month) month, '0' equipment, '0' parts, IFNULL(SUM(bal.credit) - SUM(bal.debit), 0) service")
								->leftJoin("balance_table bal ON MONTH(bal.transactiondate) = m.month AND bal.companycode = m.companycode AND YEAR(bal.transactiondate) = m.year AND (select coa.segment5 from chartaccount coa where coa.id = bal.accountcode and coa.companycode = m.companycode) = '$service_account'")
								->setGroupBy('m.month')
								->setOrderBy('m.year, m.month')
								->runSelect()
								->getResult();
		$sales 	= array();
		foreach ($sales_equipment as $key => $value) {
			$sales[$key]['month'] 		= $value->month;
			$sales[$key]['equipment'] 	= $value->equipment;
			$sales[$key]['parts'] 		= $value->parts;
			$sales[$key]['service'] 	= $value->service;
		}
		foreach ($sales_parts as $key => $value) {
			$sales[$key]['equipment'] 	+= $value->equipment;
			$sales[$key]['parts'] 		+= $value->parts;
			$sales[$key]['service'] 	+= $value->service;
		}
		foreach ($sales_service as $key => $value) {
			$sales[$key]['equipment'] 	+= $value->equipment;
			$sales[$key]['parts'] 		+= $value->parts;
			$sales[$key]['service'] 	+= $value->service;
		}			
		$result = array(
			'sales'		=> $sales
		);
		return $result;
	}

	public function getPurchase() {
		$equipment_class 	= 'EQUIPMENT';
		$parts_class 		= 'SPARE PARTS';

		$sales_equipment		= $this->db->setTable("({$this->current_month_query}) m")
								->setFields("CONCAT(m.year, '-', m.month) month, IFNULL(SUM(bal.netamount), 0) equipment, '0' parts")
								->leftJoin("balance_table_purchase bal ON MONTH(bal.transactiondate) = m.month AND bal.companycode = m.companycode AND YEAR(bal.transactiondate) = m.year AND bal.itemclass = '$equipment_class' ")
								->setGroupBy('m.month')
								->setOrderBy('m.year, m.month')
								->runSelect()
								->getResult();
		$sales_parts			= $this->db->setTable("({$this->current_month_query}) m")
								->setFields("CONCAT(m.year, '-', m.month) month, '0' equipment, IFNULL(SUM(bal.netamount), 0) parts")
								->leftJoin("balance_table_purchase bal ON MONTH(bal.transactiondate) = m.month AND bal.companycode = m.companycode AND YEAR(bal.transactiondate) = m.year AND bal.itemclass = '$parts_class' ")
								->setGroupBy('m.month')
								->setOrderBy('m.year, m.month')
								->runSelect()
								->getResult();
		$purchase 	= array();
		foreach ($sales_equipment as $key => $value) {
			$purchase[$key]['month'] 		= $value->month;
			$purchase[$key]['equipment'] 	= $value->equipment;
			$purchase[$key]['parts'] 		= $value->parts;
		}
		foreach ($sales_parts as $key => $value) {
			$purchase[$key]['equipment'] 	+= $value->equipment;
			$purchase[$key]['parts'] 		+= $value->parts;
		}		
		$result = array(
			'purchase'		=> $purchase
		);
		return $result;
	}
}