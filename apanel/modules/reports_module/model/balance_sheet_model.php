<?php
class balance_sheet_model extends wc_model {

	public function getMonthly($year = false) {
		$year = ($year) ? $year : date('Y');
		$monthly1	= $this->getRecords("{$year}-01-01", $this->getMonthEnd("{$year}-01-1"));
		$monthly2	= $this->getRecords("{$year}-02-01", $this->getMonthEnd("{$year}-02-1"));
		$monthly3	= $this->getRecords("{$year}-03-01", $this->getMonthEnd("{$year}-03-1"));
		$monthly4	= $this->getRecords("{$year}-04-01", $this->getMonthEnd("{$year}-04-1"));
		$monthly5	= $this->getRecords("{$year}-05-01", $this->getMonthEnd("{$year}-05-1"));
		$monthly6	= $this->getRecords("{$year}-06-01", $this->getMonthEnd("{$year}-06-1"));
		$monthly7	= $this->getRecords("{$year}-07-01", $this->getMonthEnd("{$year}-07-1"));
		$monthly8	= $this->getRecords("{$year}-08-01", $this->getMonthEnd("{$year}-08-1"));
		$monthly9	= $this->getRecords("{$year}-09-01", $this->getMonthEnd("{$year}-09-1"));
		$monthly10	= $this->getRecords("{$year}-10-01", $this->getMonthEnd("{$year}-10-1"));
		$monthly11	= $this->getRecords("{$year}-11-01", $this->getMonthEnd("{$year}-11-1"));
		$monthly12	= $this->getRecords("{$year}-12-01", $this->getMonthEnd("{$year}-12-1"));
		return $this->buildStructure(array($monthly1, $monthly2, $monthly3, $monthly4, $monthly5, $monthly6, $monthly7, $monthly8, $monthly9, $monthly10, $monthly11, $monthly12));
	}

	public function getQuarterly($year = false) {
		$year		= ($year) ? $year : date('Y');
		$quarter1	= $this->getRecords("{$year}-01-01", "{$year}-03-31");
		$quarter2	= $this->getRecords("{$year}-04-01", "{$year}-06-31");
		$quarter3	= $this->getRecords("{$year}-07-01", "{$year}-09-30");
		$quarter4	= $this->getRecords("{$year}-10-01", "{$year}-12-31");
		return $this->buildStructure(array($quarter1, $quarter2, $quarter3, $quarter4));
	}

	public function getYearly($year = false) {
		$year	= ($year) ? $year : date('Y');
		$year1	= $this->getRecords("{$year}-01-01", "{$year}-12-31");
		$year2	= $this->getRecords(($year - 1) . "-01-01", ($year - 1) . "-12-31");
		return $this->buildStructure(array($year1, $year2));
	}

	public function getRecords($start, $end) {
		$result		=  $this->db->setTable('chartaccount c')
								->leftJoin("balance_table bt ON c.id = bt.accountcode AND c.accountclasscode IN ('OTHCA', 'ACCREC', 'CASH', 'OTHNCA', 'PPE', 'PREPAID', 'INV', 'VAT', 'OTHCL', 'ACCPAY', 'LTP', 'TAX') AND transactiondate <= '$end'")
								->leftJoin('chartaccount c2 ON c.parentaccountcode = c2.segment5 AND c.companycode = c2.companycode')
								->setFields("c.id, c.accountname, c.accountnature, c2.accountnature parentnature, SUM(debit) debit, SUM(credit) credit, c.accountclasscode, '' earnings")
								->setWhere("c.fspresentation = 'BS'")
								->setGroupBy('c.id')
								->setOrderBy("CASE c.accountclasscode WHEN 'CASH' THEN 1 WHEN 'ACCREC' THEN 2 WHEN 'INV' THEN 3 WHEN 'OTHCA' THEN 4 WHEN 'PREPAID' THEN 5 WHEN 'VAT' THEN 6 WHEN 'PPE' THEN 7 WHEN 'OTHNCA' THEN 8 WHEN 'ACCPAY' THEN 9 WHEN 'OTHCL' THEN 10 WHEN 'TAX' THEN 11 WHEN 'LTP' THEN 12 WHEN 'EQUITY' THEN 13 END, c.id")
								->runSelect()
								->getResult();

		$earnings = $this->getEarnings($start, $end);

		return array_merge($result, $earnings);
	}

	public function getEarnings($start, $end) {
		$check_codes		= array('COST', 'EXP', 'INTAX', 'REV');
		$current_earnings	= (object) array(
			'accountname'		=> 'Current Period Earnings',
			'accountnature'		=> 'Credit',
			'parentnature'		=> 'Credit',
			'debit'				=> 0,
			'credit'			=> 0,
			'accountclasscode'	=> 'Current'
		);
		$previous_earnings	= (object) array(
			'accountname'		=> 'Retained Earnings',
			'accountnature'		=> 'Credit',
			'parentnature'		=> 'Credit',
			'debit'				=> 0,
			'credit'			=> 0,
			'accountclasscode'	=> 'Previous'
		);

		$current	=  $this->db->setTable('chartaccount c')
								->leftJoin("balance_table bt ON c.id = bt.accountcode AND c.accountclasscode IN ('EQUITY', 'REV', 'COST', 'EXP', 'INTAX') AND transactiondate >= '$start' AND transactiondate <= '$end'")
								->leftJoin('chartaccount c2 ON c.parentaccountcode = c2.segment5 AND c.companycode = c2.companycode')
								->setFields("c.id, c.accountname, c.accountnature, c2.accountnature parentnature, SUM(debit) debit, SUM(credit) credit, c.accountclasscode, 'current' earnings")
								->setWhere("c.fspresentation = 'IS'")
								->setGroupBy('c.id')
								->setOrderBy("c.id")
								->runSelect()
								->getResult();

		$previous	=  $this->db->setTable('chartaccount c')
								->leftJoin("balance_table bt ON c.id = bt.accountcode AND c.accountclasscode IN ('EQUITY', 'REV', 'COST', 'EXP', 'INTAX') AND transactiondate < '$start'")
								->leftJoin('chartaccount c2 ON c.parentaccountcode = c2.segment5 AND c.companycode = c2.companycode')
								->setFields("c.id, c.accountname, c.accountnature, c2.accountnature parentnature, SUM(debit) debit, SUM(credit) credit, c.accountclasscode, 'previous' earnings")
								->setWhere("c.fspresentation = 'IS'")
								->setGroupBy('c.id')
								->setOrderBy("c.id")
								->runSelect()
								->getResult();
		
		$earnings = array_merge($current, $previous);

		foreach ($earnings as $row) {
			if ($row->debit || $row->credit) {
				// if ($row->accountnature != $row->parentnature) {
				// 	$row->debit = $row->debit * -1;
				// 	$row->credit = $row->credit * -1;
				// }

				// if ($row->accountnature == 'Credit') {
				// 	$credit			= $row->credit;
				// 	$debit			= $row->debit;
				// 	$row->credit	= $debit;
				// 	$row->debit		= $credit;
				// }

				${$row->earnings . '_earnings'}->debit += $row->debit;
				${$row->earnings . '_earnings'}->credit += $row->credit;
			}
		}

		return array($current_earnings, $previous_earnings);
	}

	public function getYearList() {
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
		$asset1_array		= array('OTHCA', 'ACCREC', 'CASH', 'PREPAID', 'INV', 'VAT');
		$asset2_array		= array('OTHNCA', 'PPE');
		$liability1_array	= array('OTHCL', 'ACCPAY', 'TAX');
		$liability2_array	= array('LTP');
		$equity_array		= array('EQUITY');
		$earning_array		= array('COST', 'EXP', 'INTAX');
		
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
			if (in_array($accountclasscode, $asset1_array)) {
				$accounttype	= 'Assets';
				$accountclass	= 'Current Assets';
			} else if (in_array($accountclasscode, $asset2_array)) {
				$accounttype	= 'Assets';
				$accountclass	= 'Non - Current Assets';
			} else if (in_array($accountclasscode, $liability1_array)) {
				$accounttype	= 'Liabilities';
				$accountclass	= 'Current Liabilities';
			} else if (in_array($accountclasscode, $liability2_array)) {
				$accounttype	= 'Liabilities';
				$accountclass	= 'Non - Current Liabilities';
			} else if (in_array($accountclasscode, $equity_array)) {
				$accounttype	= 'Equity';
				$accountclass	= '';
			} else if ($accountclasscode == 'Current') {
				$accounttype	= 'Equity';
				$accountclass	= '';
			} else if ($accountclasscode == 'Previous') {
				$accounttype	= 'Equity';
				$accountclass	= '';
			} else {
				$total = 0;
			}
			if (($total !== 0 && ! empty($accounttype)) || in_array($accountclasscode, array('Current', 'Previous'))) {
				$y[$accounttype][$accountclass][$accountname] = $col;
			}
		}
		return $y;
	}

}