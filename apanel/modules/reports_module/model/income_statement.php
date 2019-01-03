<?php
class income_statement extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->period = 1;
		$this->year = date('Y');
		$this->getPeriodStart();
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

	public function getPeriod() {
		return $this->period;
	}

	public function getMonthly($year = false) {
		$year	= ($year) ? $year : date('Y');
		$period	= $this->period;
		for ($x = 1; $x <= 12; $x++) {
			if ($period > 12) {
				$period = 1;
				$year++;
			}
			${"monthly{$x}"} = $this->getRecords("{$year}-{$period}-01", $this->getMonthEnd("{$year}-{$period}-01"));
			$period++;
		}
		return $this->buildStructure(array($monthly1, $monthly2, $monthly3, $monthly4, $monthly5, $monthly6, $monthly7, $monthly8, $monthly9, $monthly10, $monthly11, $monthly12));
	}

	public function getQuarterly($year = false) {
		$year	= ($year) ? $year : date('Y');
		$period	= $this->period;

		$period_start	= $period;
		$period_end		= $period_start + 2;
		$year_start		= $year;
		$year_end		= $year;

		for ($x = 1; $x <= 4; $x++) {
			if ($period_start > 11) {
				$period_start = 1;
				$year_start++;
			}
			if ($period_end > 12) {
				$period_end -= 12;
				$year_end++;
			}
			${"quarter{$x}"} = $this->getRecords("{$year_start}-{$period_start}-01", $this->getMonthEnd("{$year_end}-{$period_end}-01"));

			$period_start += 3;
			$period_end += 3;
		}
		return $this->buildStructure(array($quarter1, $quarter2, $quarter3, $quarter4));
	}

	public function getYearly($year = false) {
		$period_start	= $this->period;
		$period_end		= (($this->period - 1) < 1) ? 12 : $this->period - 1;
		$year_start		= ($year) ? $year : date('Y');
		$year_end		= $year_start;
		if ($this->period > date('n')) {
			$year_end++;
		}
		$year1	= $this->getRecords(($year_start - 1) . "-{$period_start}-01", ($year_end - 1) . "-{$period_end}-31");
		$year2	= $this->getRecords("{$year_start}-{$period_start}-01", "{$year_end}-{$period_end}-31");
		return $this->buildStructure(array($year1, $year2));
	}

	public function getRecords($start, $end) {
		$filter = " AND (bt.transactiondate >= '$start' AND bt.transactiondate <= '$end') ";
		$filter2 = " AND (b.transactiondate >= '$start' AND b.transactiondate <= '$end') ";
		$result =  $this->db->setTable('balance_table as bt')
		->setFields("ca.accountnature parentnature, ca.accountclasscode as accountclasscode, bt.accountcode as accountcode, ca.accountname as accountname, bt.transactiondate as transactiondate, SUM(bt.debit) as debit, SUM(bt.credit) as credit, ca.parentaccountcode as parent, ca.accountnature as accountnature")
		->leftJoin('chartaccount as ca ON ca.id = bt.accountcode AND ca.companycode = bt.companycode ')
		->setWhere("ca.fspresentation = 'IS' AND ca.accountclasscode IN('REV','REVENU') AND bt.source != 'closing' $filter")
		->setGroupBy('bt.accountcode')
		->setOrderBy("bt.accountcode")
		->runSelect()
		->getResult();

		$result1 =  $this->db->setTable('balance_table bt')
		->setFields("ca.accountnature parentnature, ca.accountclasscode as accountclasscode, bt.accountcode as accountcode,ca.accountname as accountname, bt.transactiondate as transactiondate, SUM(bt.debit) as debit, SUM(bt.credit) as credit, ca.parentaccountcode as parent, ca.accountnature as accountnature")
		->leftJoin('chartaccount as ca ON ca.id = bt.accountcode AND ca.companycode = bt.companycode ')
		->setWhere("ca.fspresentation = 'IS' AND ca.accountclasscode IN('OTHINC','OTRINC') AND bt.source != 'closing' $filter")
		->setGroupBy('bt.accountcode')
		->setOrderBy("bt.accountcode")
		->runSelect()
		->getResult();

		$result2 =  $this->db->setTable('balance_table bt')
		->setFields("ca.accountnature parentnature, ca.accountclasscode as accountclasscode, bt.accountcode as accountcode,ca.accountname as accountname, bt.transactiondate as transactiondate, SUM(bt.debit) as debit, SUM(bt.credit) as credit, ca.parentaccountcode as parent, ca.accountnature as accountnature")
		->leftJoin('chartaccount as ca ON ca.id = bt.accountcode AND ca.companycode = bt.companycode ')
		->setWhere("ca.fspresentation = 'IS' AND ca.accountclasscode IN('COST','COSTSA') AND bt.source != 'closing' $filter")
		->setGroupBy('bt.accountcode')
		->setOrderBy("bt.accountcode")
		->runSelect()
		->getResult();

		$result3 =  $this->db->setTable('balance_table bt')
		->setFields("ca.accountnature parentnature, ca.accountclasscode as accountclasscode, bt.accountcode as accountcode,ca.accountname as accountname, bt.transactiondate as transactiondate, SUM(bt.debit) as debit, SUM(bt.credit) as credit, ca.parentaccountcode as parent, ca.accountnature as accountnature")
		->leftJoin('chartaccount as ca ON ca.id = bt.accountcode AND ca.companycode = bt.companycode ')
		->setWhere("ca.fspresentation = 'IS' AND ca.accountclasscode IN('EXP','OPSEXP','OTREXP') $filter")
		->setGroupBy('bt.accountcode')
		->setOrderBy("bt.accountcode")
		->runSelect()
		->getResult();

		$result4 =  $this->db->setTable('budget b')
		->setFields("ca.accountnature parentnature, ca.accountclasscode as accountclasscode, bd.accountcode as accountcode,ca.accountname as accountname, b.transactiondate as transactiondate, SUM(bd.amount) as debit, 0 as credit, ca.parentaccountcode as parent, ca.accountnature as accountnature")
		->leftJoin('budget_details as bd ON bd.budget_code = b.budget_code')
		->leftJoin('chartaccount as ca ON ca.id = bd.accountcode')
		->setWhere("ca.fspresentation IN('IS','BS') AND ca.accountclasscode IN('REV','REVENU','OTHINC','OTRINC','COST','COSTSA','EXP','OPSEXP','OTREXP') AND bd.amount != 0 $filter2")
		->setGroupBy('bd.accountcode')
		->setOrderBy("bd.accountcode")
		->runSelect(false)
		->getResult();

		return array_merge($result, $result1, $result2, $result3, $result4);
	}

	public function getYearList($year_now) {
		$year_list	= array();
		$year_now	= date('Y');
		for ($year = $year_now; $year > $year_now - 5; $year--) {
			$year_list[$year] = $year;
		}
		return $year_list;
	}

	private function getMonthEnd($date) {
		return date("Y-m-t", strtotime($date));
	}

	private function buildStructure($data) {
		$y				= array();
		$revenue_array 	= array('REV','REVENU');
		$otherinc_array = array('OTHINC','OTRINC');
		$cost_array 	= array('COST','COSTSA');
		$exp_array 		= array('EXP','OPSEXP','OTREXP');
		$inc_array 		= array('INCTAX');

		$data_key = 0;
		
		foreach ($data as $key => $val) {
			if (count($data[$data_key]) < count($val)) {
				$data_key = $key;
			}
		}

		$col = array();
		foreach ($data[$data_key] as $key => $accounts) {
			$col				= array();
			$total				= 0;
			$accountname		= $accounts->accountname;
			$accountclasscode	= $accounts->accountclasscode;
			$parentaccount		= ($accounts->parentnature) ? $accounts->parentnature : $accounts->accountnature;
			$accounttype		= '';
			$accountclass		= '';

			foreach ($data as $x) {
				if (isset($x[$key])) {
					$account	= $x[$key];
					$tot = ($accounts->accountnature == 'Debit') ? $account->debit - $account->credit : $account->credit - $account->debit;
					if ($accounts->accountnature != $parentaccount) {
						$tot = $tot * -1;
					}
					$total	+= $tot;
					$col[]	= $tot;
				} else {
					$col[]	= 0;
				}
			}

			if (in_array($accountclasscode, $revenue_array)) {
				$accounttype = 'Revenue';
			} else if (in_array($accountclasscode, $otherinc_array)) {
				$accounttype = 'Other Income';
			} else if (in_array($accountclasscode, $cost_array)) {
				$accounttype = 'Cost';
			} else if (in_array($accountclasscode, $exp_array)) {
				$accounttype = 'Expense';
			} else if (in_array($accountclasscode, $inc_array)) {
				$accounttype = 'Income Tax';
			}else {
				$total = 0;
			}
			if ($total !== 0 && ! empty($accounttype)) {
				$y[$accounttype][$accountname] = $col;
			}
		}
		return $y;
	}

}