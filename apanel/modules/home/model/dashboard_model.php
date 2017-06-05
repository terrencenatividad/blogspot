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
			$months[] = "SELECT '$x' month, '$year' year, '" . COMPANYCODE . "' companycode";
		}

		return implode(' UNION ALL ', $months);
	}

	public function getInvoices() {
		$result = $this->db->setTable('salesinvoice')
							->setFields('COUNT(companycode) quantity')
							->setWhere("stat NOT IN ('cancelled', 'temporary') AND fiscalyear = '{$this->year}'")
							->runSelect()
							->getRow();
		
		$count = ($result) ? $result->quantity : 0;

		return $count;
	}

	public function getPurchases() {
		$result = $this->db->setTable('purchasereceipt')
							->setFields('COUNT(companycode) quantity')
							->setWhere("stat NOT IN ('cancelled', 'temporary') AND fiscalyear = '{$this->year}'")
							->runSelect()
							->getRow();
		
		$count = ($result) ? $result->quantity : 0;

		return $count;
	}

	public function getBillings() {
		$result = $this->db->setTable('salesinvoice')
							->setFields('COUNT(companycode) quantity')
							->setWhere("stat NOT IN ('cancelled', 'temporary') AND fiscalyear = '{$this->year}'")
							->runSelect()
							->getRow();
		
		$count = ($result) ? $result->quantity : 0;

		return $count;
	}

	public function getJournalVouchers() {
		$result = $this->db->setTable('journalvoucher')
							->setFields('COUNT(companycode) quantity')
							->setWhere("stat = 'posted' AND transtype = 'JV' AND fiscalyear = '{$this->year}'")
							->runSelect()
							->getRow();
		
		$count = ($result) ? $result->quantity : 0;

		return $count;
	}

	public function getRevenuesAndExpenses() {
		$current	= $this->db->setTable("({$this->current_month_query}) m")
								->leftJoin("salesinvoice si ON si.period = m.month AND si.companycode = m.companycode AND si.fiscalyear = m.year AND si.stat NOT IN ('temporary', 'cancelled')")
								->leftJoin("purchaseorder po ON po.period = m.month AND po.companycode = m.companycode AND po.fiscalyear = m.year AND po.stat NOT IN ('temporary', 'cancelled')")
								->setFields("IFNULL(SUM(si.amount), 0) revenue, IFNULL(SUM(po.amount), 0) expense, CONCAT(m.year, '-', m.month) month")
								->setGroupBy('m.month')
								->runSelect()
								->getResult();

		$previous	= $this->db->setTable("({$this->previous_month_query}) m")
								->leftJoin("salesinvoice si ON si.period = m.month AND si.companycode = m.companycode AND si.fiscalyear = m.year AND si.stat NOT IN ('temporary', 'cancelled')")
								->leftJoin("purchaseorder po ON po.period = m.month AND po.companycode = m.companycode AND po.fiscalyear = m.year AND po.stat NOT IN ('temporary', 'cancelled')")
								->setFields("IFNULL(SUM(si.amount), 0) revenue, IFNULL(SUM(po.amount), 0) expense, CONCAT(m.year, '-', m.month) month")
								->setGroupBy('m.month')
								->runSelect()
								->getResult();

		$rae = array(
			'current'	=> $current,
			'previous'	=> $previous
		);
		return $rae;
	}

	public function getAging() {
		$datefilter = $this->date->dateDbFormat() . ' 23:59:59';
		$ap	= $this->db->setTable('accountspayable ap')
						->innerJoin('pv_application pva ON pva.apvoucherno = ap.voucherno AND pva.companycode = ap.companycode')
						->setFields("
							pva.convertedamount value,
							CASE
								WHEN DATEDIFF('$datefilter', ap.duedate) > 60 THEN '1 to 30 days'
								WHEN DATEDIFF('$datefilter', ap.duedate) > 30 THEN '31 to 60 days'
								WHEN DATEDIFF('$datefilter', ap.duedate) > 1 THEN '60 days over'
							END label
						")
						->setWhere("ap.stat = 'posted' AND pva.stat = 'posted' AND ap.duedate < '$datefilter'")
						->buildSelect();


		$ar	= $this->db->setTable('accountsreceivable ar')
						->innerJoin('rv_application rva ON rva.arvoucherno = ar.voucherno AND rva.companycode = ar.companycode')
						->setFields("
							rva.convertedamount value,
							CASE
								WHEN DATEDIFF('$datefilter', ar.duedate) > 60 THEN '1 to 30 days'
								WHEN DATEDIFF('$datefilter', ar.duedate) > 30 THEN '31 to 60 days'
								WHEN DATEDIFF('$datefilter', ar.duedate) > 1 THEN '60 days over'
							END label
						")
						->setWhere("ar.stat = 'posted' AND rva.stat = 'posted' AND ar.duedate < '$datefilter'")
						->buildSelect();

		$aging = array(
			'ap' => $this->getVoucherApplication($ap),
			'ar' => $this->getVoucherApplication($ar)
		);
		return $aging;
	}

	private function getVoucherApplication($query) {
		$result = $this->db->setTable("($query) query")
							->setFields('label, SUM(value) value')
							->setGroupBy('label')
							->runSelect(false)
							->getResult();

		if ($result) {
			return $result;
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
								->leftJoin("purchaseorder po ON po.period = m.month AND po.companycode = m.companycode AND po.fiscalyear = m.year AND po.stat NOT IN ('temporary', 'cancelled')")
								->setFields("IFNULL(SUM(amount), 0) value, CONCAT(m.year, '-', m.month) month")
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