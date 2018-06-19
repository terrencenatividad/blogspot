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
								->setWhere("accountclasscode IN ('COST')")
								->buildSelect();

		$coa_rev 	= 	$this->db->setTable("chartaccount")
								->setFields("id")
								->setWhere("accountclasscode IN ('REV')")
								->buildSelect();

		$current 	=	$this->db->setTable("({$this->current_month_query}) m")
								->leftJoin("($coa_rev) n ON 1 = 1")
								->leftJoin("balance_table pr ON pr.period = m.month AND pr.fiscalyear = m.year AND pr.accountcode = n.id ")
								->setFields("IFNULL(SUM(pr.credit)-SUM(pr.debit), 0) revenue, CONCAT(m.year, '-', m.month) month")
								->setGroupBy('m.month')
								->runSelect()
								->getResult();

		$current2 	=	$this->db->setTable("({$this->current_month_query}) m")
								->leftJoin("($coa_cost) n ON 1 = 1")
								->leftJoin("balance_table pr ON pr.period = m.month AND pr.fiscalyear = m.year AND pr.accountcode = n.id ")
								->setFields("IFNULL(SUM(pr.credit)-SUM(pr.debit), 0) expense")
								->setGroupBy('m.month')
								->runSelect()
								->getResult();

		$previous 	=	$this->db->setTable("({$this->previous_month_query}) m")
								->leftJoin("($coa_rev) n ON 1 = 1")
								->leftJoin("balance_table pr ON pr.period = m.month AND pr.fiscalyear = m.year AND pr.accountcode = n.id ")
								->setFields("IFNULL(SUM(pr.credit)-SUM(pr.debit), 0) revenue, CONCAT(m.year, '-', m.month) month")
								->setGroupBy('m.month')
								->runSelect()
								->getResult();

		$previous2 	=	$this->db->setTable("({$this->previous_month_query}) m")
								->leftJoin("($coa_cost) n ON 1 = 1")
								->leftJoin("balance_table pr ON pr.period = m.month AND pr.fiscalyear = m.year AND pr.accountcode = n.id ")
								->setFields("IFNULL(SUM(pr.credit)-SUM(pr.debit), 0) expense, CONCAT(m.year, '-', m.month) month")
								->setGroupBy('m.month')
								->runSelect()
								->getResult();

		foreach ($current as $key => $row) {
			$current[$key]->expense = $current2[$key]->expense;
		}

		foreach ($previous as $key => $row) {
			$previous[$key]->expense = $previous2[$key]->expense;
		}
		// var_dump($current);
		$rae = array(
			'current'	=> $current,
			'previous'	=> $previous
		);
		return $rae;
	}

	public function getAging() {
		$datefilter = $this->date->dateDbFormat() . ' 00:00:00';
		$ap	= $this->db->setTable('accountspayable ap')
						->setFields("
							ap.balance value,
							CASE
								WHEN DATEDIFF('$datefilter', ap.duedate) > 60 THEN '60 days over'
								WHEN DATEDIFF('$datefilter', ap.duedate) > 30 THEN '31 to 60 days'
								WHEN DATEDIFF('$datefilter', ap.duedate) > 0 THEN '1 to 30 days'
							END label
						")
						->setWhere("ap.stat = 'posted' AND ap.duedate < '$datefilter' AND ap.balance > 0")
						->buildSelect();


		$ar	= $this->db->setTable('accountsreceivable ar')
						->setFields("
							ar.balance value,
							CASE
								WHEN DATEDIFF('$datefilter', ar.duedate) > 60 THEN '60 days over'
								WHEN DATEDIFF('$datefilter', ar.duedate) > 30 THEN '31 to 60 days'
								WHEN DATEDIFF('$datefilter', ar.duedate) > 0 THEN '1 to 30 days'
							END label
						")
						->setWhere("ar.stat = 'posted' AND ar.duedate < '$datefilter' AND ar.balance > 0")
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
			foreach ($result as $content) {
				$labelarr[] 	=	$content->label;
			}
			if(!in_array("1 to 30 days",$labelarr)){
				$objects['label'] = "1 to 30 days";
				$objects['value'] = 0;
				$result[] 		  = $objects;
			}
			if(!in_array("31 to 60 days",$labelarr)){
				$objects['label'] = "31 to 60 days";
				$objects['value'] = 0;
				$result[] 		  = $objects;
			}
			if(!in_array("60 days over",$labelarr)){
				$objects['label'] = "60 days over";
				$objects['value'] = 0;
				$result[] 		  = $objects;
			} 
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