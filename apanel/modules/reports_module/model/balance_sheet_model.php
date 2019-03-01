<?php
class balance_sheet_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->period = 1;
		$this->getPeriodStart();
	}

	public function getYear() {
		return $this->year = date('Y');
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
		$year			= ($year) ? $year : date('Y');
		$period			= $this->period;
		$periods 		= $this->getPeriodList($year);
		$period_list 	= array();
		if($periods) {
			foreach ($periods as $key => $value)
			{
				$period_list[] = $value->period;
			}
		}
		// var_dump($period_list);
		for ($x = 1; $x <= 12; $x++) {
			if ($period > 12) {
				$period = 1;
				$year++;
			}
			if(isset($period_list)){
				if(in_array($period, $period_list)){
					${"monthly{$x}"} = $this->getRecords("{$year}-{$period}-01", $this->getMonthEnd("{$year}-{$period}-01"));
				}else{
					${"monthly{$x}"} = 0;
				}
			}else{
				${"monthly{$x}"} = 0;
			}
			$period++;
		}
		return $this->buildStructure(array($monthly1, $monthly2, $monthly3, $monthly4, $monthly5, $monthly6, $monthly7, $monthly8, $monthly9, $monthly10, $monthly11, $monthly12));
	}

	public function getQuarterly($year = false) {
		$year	= ($year) ? $year : date('Y');
		$period	= $this->period;
		// echo $period;
		$periods = $this->getPeriodList($year);
		$period_list 	= array();
		if($periods) {
			foreach ($periods as $key => $value)
			{
				$period_list[] = $value->period;
			}
		}
		// var_dump($period_list);
		
		$period_start	= $period; 	//1 
		$period_end		= $period_start + 2; //3
		$year_start		= $year;
		$year_end		= $year;

		$quarter1 		= array();
		$quarter2 		= array();
		$quarter3 		= array();
		$quarter4 		= array();
		for ($x = 1; $x <= 4; $x++) {
			// echo "start ".$period_start."<br>";
			if ($period_start > 11) {
				$period_start = 1;
				$year_start++;
			}
			// echo "End ".$period_end."<br>";
			if ($period_end > 12) {
				$period_end -= 12;
				$year_end++;
			}
			// echo $period_start."<br>";
			
			if(isset($period_list)){
				foreach($period_list as $key=>$actual_period){ // 5 & 12
					// echo $actual_period."<br>";
					// echo $period_start." - ".$period_end."<br>";
					if($actual_period >= $period_start && $actual_period <= $period_end){
						${"quarter{$x}"} = $this->getRecords("{$year_start}-{$period_start}-01", $this->getMonthEnd("{$year_end}-{$period_end}-01"));
					}else{
						${"quarter{$x}"} = 0;
					}
				}
			}else{
				${"quarter{$x}"} = 0;
			}

			$period_start += 3;
			$period_end += 3;
		}
		// var_dump($quarter2);
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
		
		$result		=  $this->db->setTable('chartaccount c')
								->leftJoin("balance_table bt ON c.id = bt.accountcode AND c.accountclasscode IN ('OTHCA', 'CUASET', 'ACCREC', 'CASH', 'OTHNCA', 'PPE', 'PREPAID', 'INV', 'VAT', 'OTHCL', 'CULIAB', 'ACCPAY', 'LTP', 'TAX', 'INPVAT', 'NVNTRY', 'NCASET', 'NCLIAB', 'OUTVAT') AND transactiondate <= '$end'")
								->leftJoin('chartaccount c2 ON c.parentaccountcode = c2.segment5 AND c.companycode = c2.companycode')
								->setFields("c.id, c.accountname, c.accountnature, c2.accountnature parentnature, SUM(debit) debit, SUM(credit) credit, c.accountclasscode, '' earnings")
								->setWhere("c.fspresentation = 'BS'")
								->setGroupBy('c.id')
								->setOrderBy("CASE c.accountclasscode WHEN 'CASH' THEN 1 WHEN 'ACCREC' THEN 2 WHEN 'INV' THEN 3 WHEN 'OTHCA' THEN 4 WHEN 'PREPAID' THEN 5 WHEN 'VAT' THEN 6 WHEN 'PPE' THEN 7 WHEN 'OTHNCA' THEN 8 WHEN 'ACCPAY' THEN 9 WHEN 'OTHCL' THEN 10 WHEN 'TAX' THEN 11 WHEN 'LTP' THEN 12 WHEN 'EQUITY' THEN 13 END, c.id")
								->runSelect()
								->getResult();
		// if($end == "2018-06-30") {
		// 	echo $this->db->getQuery();
		// }
		$earnings = $this->getEarnings($start, $end);

		return array_merge($result, $earnings);
	}

	public function getPeriodList($year) {
		$result		=  $this->db->setTable('balance_table bal')
								->setFields("bal.period")
								->leftJoin('chartaccount coa ON coa.id = bal.accountcode ')
								->setWhere(" coa.fspresentation = 'BS' AND bal.fiscalyear = '$year' AND bal.period IS NOT NULL AND coa.accountclasscode IN ('OTHCA', 'CUASET', 'ACCREC', 'CASH', 'OTHNCA', 'PPE', 'PREPAID', 'INV', 'VAT', 'OTHCL', 'CULIAB', 'ACCPAY', 'LTP', 'TAX', 'INPVAT', 'NVNTRY', 'NCASET', 'NCLIAB', 'OUTVAT') ")
								->setGroupBy('bal.period')
								->runSelect()
								->getResult();
		return ($result) ? $result : false;
	}

	public function getEarnings($start, $end) {
		// echo $start."<br>";
		// echo $end."<br>";
		// $check_codes		= array('COST', 'COSTSA', 'EXP', 'INTAX', 'INCTAX', 'REV', 'RETEAR', 'REVENU', 'OPSEXP', 'OTREXP');
		// $current_earnings	= (object) array(
		// 	'accountname'		=> 'Current Year Earnings',
		// 	'accountnature'		=> 'Credit',
		// 	'parentnature'		=> 'Credit',
		// 	'debit'				=> 0,
		// 	'credit'			=> 0,
		// 	'accountclasscode'	=> 'Current'
		// );
		// $previous_earnings	= (object) array(
		// 	'accountname'		=> 'Retained Earnings',
		// 	'accountnature'		=> 'Credit',
		// 	'parentnature'		=> 'Credit',
		// 	'debit'				=> 0,
		// 	'credit'			=> 0,
		// 	'accountclasscode'	=> 'Previous'
		// );

		// $current	=  $this->db->setTable('chartaccount c')
		// 						->leftJoin("balance_table bt ON c.id = bt.accountcode AND c.accountclasscode IN ('EQUITY', 'REV', 'RETEAR', 'REVENU', 'COST' 'COSTSA', 'EXP', 'INTAX', 'INCTAX', 'OPSEXP', 'OTREXP') AND transactiondate >= '$start' AND transactiondate <= '$end'")
		// 						->leftJoin('chartaccount c2 ON c.parentaccountcode = c2.segment5 AND c.companycode = c2.companycode')
		// 						->setFields("c.id, c.accountname, c.accountnature, c2.accountnature parentnature, SUM(debit) debit, SUM(credit) credit, c.accountclasscode, 'current' earnings")
		// 						->setWhere("c.fspresentation = 'IS' AND bt.source = 'closing'")
		// 						->setGroupBy('c.id')
		// 						->setOrderBy("c.id")
		// 						->runSelect()
		// 						->getResult();

		// $previous	=  $this->db->setTable('chartaccount c')
		// 						->leftJoin("balance_table bt ON c.id = bt.accountcode AND c.accountclasscode IN ('EQUITY', 'REV', 'RETEAR', 'REVENU', 'COST' 'COSTSA', 'EXP', 'INTAX', 'INCTAX', 'OPSEXP', 'OTREXP') AND transactiondate < '$start'")
		// 						->leftJoin('chartaccount c2 ON c.parentaccountcode = c2.segment5 AND c.companycode = c2.companycode')
		// 						->setFields("c.id, c.accountname, c.accountnature, c2.accountnature parentnature, SUM(debit) debit, SUM(credit) credit, c.accountclasscode, 'previous' earnings")
		// 						->setWhere("c.fspresentation = 'IS' AND bt.source = 'closing'")
		// 						->setGroupBy('c.id')
		// 						->setOrderBy("c.id")
		// 						->runSelect()
		// 						->getResult();
		
		// $earnings = array_merge($current, $previous);

		// foreach ($earnings as $row) {
		// 	if ($row->debit || $row->credit) {
		// 		${$row->earnings . '_earnings'}->debit += $row->debit;
		// 		${$row->earnings . '_earnings'}->credit += $row->credit;
		// 	}
		// }

		// return array($current_earnings, $previous_earnings);

		$current	=  $this->db->setTable('chartaccount c')
							->leftJoin("balance_table bt ON c.id = bt.accountcode AND c.accountclasscode IN ('EQUITY', 'REV', 'RETEAR', 'REVENU', 'COST' 'COSTSA', 'EXP', 'INTAX', 'INCTAX', 'OPSEXP', 'OTREXP') AND transactiondate <= '$end'")
							->leftJoin('chartaccount c2 ON c.parentaccountcode = c2.segment5 AND c.companycode = c2.companycode AND c2.accounttype = "P"')
							->setFields("c.accountname, c.accountnature, c2.accountnature parentnature,  SUM(COALESCE(debit,0)) debit, SUM(COALESCE(credit,0)) credit, SUM(COALESCE(credit, 0)) credit, (SUM(COALESCE(debit, 0)) - SUM(COALESCE(credit, 0))) balance, IF( (SUM(COALESCE(debit, 0)) - SUM(COALESCE(credit, 0))) < 0, 'credit','debit')  type, c.accountclasscode accountclasscode, 'current' earnings")
							->setWhere("c.fspresentation = 'BS' AND (bt.source = 'closing' OR bt.source = 'yrend_closing') ")
							->setGroupBy('c.id')
							->setOrderBy("c.accountname")
							->runSelect()
							->getResult();

							// echo "CLOSING <br>";
							// echo $this->db->getQuery();
							// echo "<br><br>";

		// $previous	=  $this->db->setTable('chartaccount c')
		// 					->leftJoin("balance_table bt ON c.id = bt.accountcode AND c.accountclasscode IN ('EQUITY', 'REV', 'RETEAR', 'REVENU', 'COST' 'COSTSA', 'EXP', 'INTAX', 'INCTAX', 'OPSEXP', 'OTREXP') AND (transactiondate >= '$start' AND transactiondate <= '$end')")
		// 					->leftJoin('chartaccount c2 ON c.parentaccountcode = c2.segment5 AND c.companycode = c2.companycode AND c2.accounttype = "P"')
		// 					->setFields("c.accountname, c.accountnature, c2.accountnature parentnature,  SUM(COALESCE(debit,0)) debit, SUM(COALESCE(credit,0)) credit, SUM(COALESCE(credit, 0)) credit, (SUM(COALESCE(debit, 0)) - SUM(COALESCE(credit, 0))) balance, IF( (SUM(COALESCE(debit, 0)) - SUM(COALESCE(credit, 0))) < 0, 'credit','debit')  type,'Current' accountclasscode, 'current' earnings")
		// 					->setWhere("c.fspresentation = 'BS' AND bt.source = 'closing' ")
		// 					->setGroupBy('c.id')
		// 					->setOrderBy("c.accountname")
		// 					->runSelect()
		// 					->getResult();


							// echo "YR END <br>";
							// echo $this->db->getQuery();
							// echo "<br><br>";
							

		$previous	=  array();

		// if( $start == '2018-1-01' && $end =='2018-12-31') {
		// 	echo $this->db->getQuery();
		// 	echo "<br><br>";
		// }
		// $previous	=  $this->db->setTable('chartaccount c')
		// 					->leftJoin("balance_table bt ON c.id = bt.accountcode AND c.accountclasscode IN ('EQUITY', 'REV', 'RETEAR', 'REVENU', 'COST' 'COSTSA', 'EXP', 'INTAX', 'INCTAX', 'OPSEXP', 'OTREXP')  AND transactiondate < '$start' ")
		// 					->leftJoin('chartaccount c2 ON c.parentaccountcode = c2.segment5 AND c.companycode = c2.companycode')
		// 					->setFields("c.accountname, c.accountnature, c2.accountnature parentnature, SUM(debit) debit, SUM(credit) credit, c.accountclasscode, 'previous' earnings")
		// 					->setWhere("c.fspresentation = 'BS' AND bt.source = 'yrend_closing'")
		// 					->setGroupBy('c.id')
		// 					->setOrderBy("c.id")
		// 					->runSelect()
		// 					->getResult();
							// echo $this->db->getQuery();
		
		$earnings = array_merge($current, $previous);

		// print_r($earnings);

		return $earnings;
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
		$asset1_array		= array('OTHCA', 'CUASET', 'ACCREC', 'CASH', 'PREPAID', 'INV', 'NVNTRY', 'VAT', 'INPVAT', 'OUTVAT');
		$asset2_array		= array('OTHNCA', 'NCASET', 'PPE');
		$liability1_array	= array('OTHCL', 'CULIAB', 'ACCPAY', 'TAX');
		$liability2_array	= array('LTP', 'NCLIAB');
		$equity_array		= array('EQUITY');
		$earning_array		= array('COST', 'COSTSA', 'EXP', 'OPSEXP', 'OTREXP', 'INTAX', 'INCTAX');	
		$data_key = 0;
		foreach ($data as $key => $val) {
			if (count($data[$data_key]) < count($val)) {
				$data_key = $key;
			}
		}

		$col = array();
		if($data[$data_key]){
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
						$tot = (strtolower($accounts->accountnature) == 'debit') ? $account->debit - $account->credit : $account->credit - $account->debit;
						if (strtolower($accounts->accountnature) == 'credit') {
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
				} else {
					$total = 0;
				}
				if (($total !== 0 && !empty($accounttype))) {
					$y[$accounttype][$accountclass][$accountname] = $col;
				}
			}
		}
		return $y;
	}

}