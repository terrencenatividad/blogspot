<?php
class dashboard_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->year					= date('Y');
		$this->current_month_query	= $this->getMonthly();
		$this->previous_month_query	= $this->getMonthly(1);
	}

	private function getMonthly($y = 0) {
		$months	= array();
		$year	= $this->year - $y;
		for ($x = 1; $x <= 12; $x++) {
			$months[] = "SELECT $x month, '$year' year, '" . COMPANYCODE . "' companycode";
		}

		return implode(' UNION ALL ', $months);
	}

	public function getInvoices() {
		$result = $this->db->setTable('salesinvoice')
							->setFields('COUNT(*) quantity')
							->setWhere("stat NOT IN ('cancelled', 'temporary') AND fiscalyear = '{$this->year}'")
							->runSelect()
							->getRow();
		
		$count = ($result) ? $result->quantity : 0;

		return $count;
	}

	public function getPurchases() {
		$result = $this->db->setTable('purchasereceipt')
							->setFields('COUNT(*) quantity')
							->setWhere("stat NOT IN ('Cancelled', 'temporary') AND fiscalyear = '{$this->year}'")
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
							->setWhere("stat = 'posted' AND transtype = 'JV' AND fiscalyear = '{$this->year}'")
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
								->leftJoin("balance_table pr ON pr.period = m.month AND pr.fiscalyear = m.year AND pr.accountcode = n.id ")
								->setFields("IFNULL(SUM(pr.credit)-SUM(pr.debit), 0) revenue, CONCAT(m.year, '-', m.month) month")
								->setGroupBy('m.month')
								->setOrderBy('m.month')
								->runSelect()
								->getResult();

		$current2 	=	$this->db->setTable("({$this->current_month_query}) m")
								->leftJoin("($coa_cost) n ON 1 = 1")
								->leftJoin("balance_table pr ON pr.period = m.month AND pr.fiscalyear = m.year AND pr.accountcode = n.id ")
								->setFields("IFNULL(SUM(pr.credit)-SUM(pr.debit), 0) expense")
								->setGroupBy('m.month')
								->setOrderBy('m.month')
								->runSelect()
								->getResult();

		$previous 	=	$this->db->setTable("({$this->previous_month_query}) m")
								->leftJoin("($coa_rev) n ON 1 = 1")
								->leftJoin("balance_table pr ON pr.period = m.month AND pr.fiscalyear = m.year AND pr.accountcode = n.id ")
								->setFields("IFNULL(SUM(pr.credit)-SUM(pr.debit), 0) revenue, CONCAT(m.year, '-', m.month) month")
								->setGroupBy('m.month')
								->setOrderBy('m.month')
								->runSelect()
								->getResult();

		$previous2 	=	$this->db->setTable("({$this->previous_month_query}) m")
								->leftJoin("($coa_cost) n ON 1 = 1")
								->leftJoin("balance_table pr ON pr.period = m.month AND pr.fiscalyear = m.year AND pr.accountcode = n.id ")
								->setFields("IFNULL(SUM(pr.credit)-SUM(pr.debit), 0) expense, CONCAT(m.year, '-', m.month) month")
								->setGroupBy('m.month')
								->setOrderBy('m.month')
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
									->setFields('SUM(rva.amount) payments, rva.arvoucherno, rva.companycode')
									->leftJoin('receiptvoucher rv ON rv.voucherno = rva.voucherno AND rv.companycode = rva.companycode')
									->setWhere("rv.stat = 'posted' AND rv.transactiondate <= CURDATE()")
									->setGroupBy('rva.arvoucherno')
									->buildSelect();

		$aging_query = $this->db->setTable('accountsreceivable ar')
								->setFields("p.partnername customer, ar.voucherno, ar.transactiondate, ar.terms, ar.amount, ar.duedate, IF (ar.duedate < DATE_SUB(CURDATE(), INTERVAL 60 DAY), ar.amount - IFNULL(rva.payments, 0), 0) oversixty,
								IF (ar.duedate < DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND ar.duedate > DATE_SUB(CURDATE(), INTERVAL 60 DAY), ar.amount - IFNULL(rva.payments, 0), 0) sixty,
								IF (ar.duedate < CURDATE() AND ar.duedate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY), ar.amount - IFNULL(rva.payments, 0), 0) thirty,
								IF (ar.duedate = CURDATE(), ar.amount - IFNULL(rva.payments, 0), 0) today, (ar.amount - IFNULL(rva.payments, 0)) balance, ar.companycode")
								->leftJoin("($payment_query) rva ON rva.arvoucherno = ar.voucherno AND rva.companycode = ar.companycode")
								->leftJoin('partners p ON p.partnercode = ar.customer AND p.companycode = ar.companycode')
								->setWhere("ar.stat = 'posted' AND ar.transactiondate <= CURDATE()")
								->setHaving('balance > 0')
								->buildSelect();

		$result = $this->db->setTable("($aging_query) aq")
							->setFields("customer, voucherno, transactiondate, terms, amount, duedate, SUM(oversixty) oversixty_total, SUM(sixty) sixty_total, SUM(thirty) thirty_total, SUM(today) today_total, SUM(balance) balance_total")
							->runSelect()
							->getRow();

		return $result;
	}

	private function getPayableAging() {
		$payment_query = $this->db->setTable('pv_application pva')
									->setFields('SUM(pva.amount) payments, pva.apvoucherno, pva.companycode')
									->leftJoin('paymentvoucher pv ON pv.voucherno = pva.voucherno AND pv.companycode = pva.companycode')
									->setWhere("pv.stat = 'posted' AND pv.transactiondate <= CURDATE()")
									->setGroupBy('pva.apvoucherno')
									->buildSelect();

		$aging_query = $this->db->setTable('accountspayable ap')
								->setFields("p.partnername supplier, ap.voucherno, ap.transactiondate, ap.terms, ap.amount, ap.duedate, IF (ap.duedate < DATE_SUB(CURDATE(), INTERVAL 60 DAY), ap.amount - IFNULL(pva.payments, 0), 0) oversixty,
								IF (ap.duedate < DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND ap.duedate > DATE_SUB(CURDATE(), INTERVAL 60 DAY), ap.amount - IFNULL(pva.payments, 0), 0) sixty,
								IF (ap.duedate < CURDATE() AND ap.duedate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY), ap.amount - IFNULL(pva.payments, 0), 0) thirty,
								IF (ap.duedate = CURDATE(), ap.amount - IFNULL(pva.payments, 0), 0) today, (ap.amount - IFNULL(pva.payments, 0)) balance, ap.companycode")
								->leftJoin("($payment_query) pva ON pva.apvoucherno = ap.voucherno AND pva.companycode = ap.companycode")
								->leftJoin('partners p ON p.partnercode = ap.vendor AND p.companycode = ap.companycode')
								->setWhere("ap.stat = 'posted' AND ap.transactiondate <= CURDATE()")
								->setHaving('balance > 0')
								->buildSelect();


		$result = $this->db->setTable("($aging_query) aq")
							->setFields("supplier, voucherno, transactiondate, terms, amount, duedate, SUM(oversixty) oversixty_total, SUM(sixty) sixty_total, SUM(thirty) thirty_total, SUM(today) today_total, SUM(balance) balance_total")
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
								->leftJoin("salesinvoice si ON si.period = m.month AND si.companycode = m.companycode AND si.fiscalyear = m.year AND si.stat NOT IN ('temporary', 'cancelled')")
								->setFields("IFNULL(SUM(amount), 0) value, CONCAT(m.year, '-', m.month) month")
								->setGroupBy('m.month')
								->runSelect()
								->getResult();

		$purchases	= $this->db->setTable("({$this->current_month_query}) m")
								->leftJoin("purchasereceipt pr ON pr.period = m.month AND pr.companycode = m.companycode AND pr.fiscalyear = m.year AND pr.stat NOT IN ('temporary', 'Cancelled')")
								->setFields("IFNULL(SUM(netamount), 0) value, CONCAT(m.year, '-', m.month) month")
								->setGroupBy('m.month')
								->runSelect()
								->getResult();

		$aging = array(
			'sales'		=> $sales,
			'purchases'	=> $purchases
		);
		return $aging;
	}

}