<?php
class budget_report_model extends wc_model {

	public function getBudgetCodeList() {
		$result = $this->db->setTable('budget')
		->setFields("budget_code ind, budget_code val")
		->setOrderBy("id")
		->runSelect()
		->getResult();

		return $result;
	}

	public function getYearList() {
		$result = $this->db->setTable('budget_report')
		->setFields("year as ind, year as val")
		->setOrderBy("year")
		->setGroupBy('year')
		->runSelect(false)
		->getResult();

		return $result;
	}

	public function getBudgetReportList($budgetcode, $year) {
		$budgetreport = array(
			'br.budget_code',
			'br.date_approved',
			'br.january',
			'br.february',
			'br.march',
			'br.april',
			'br.may',
			'br.june',
			'br.july',
			'br.august',
			'br.september',
			'br.october',
			'br.november',
			'br.december',
			'CONCAT(ca.segment5, " - ", ca.accountname) as accountname'
		);

		$condition = '';
		
		if($budgetcode == 'none' || empty($budgetcode)) {
			$condition .= "b.budget_code != ''";
		} else if($budgetcode != 'none'){
			$condition .= "b.budget_code = '$budgetcode'";
		}

		if($year == 'none' || empty($year)) {
			$condition .= " AND br.year != ''";
		} else if($year != 'none'){
			$condition .= " AND br.year = '$year'";
		}

		$result = $this->db->setTable('budget_report as br')
		->leftJoin('chartaccount as ca ON br.accountcode = ca.id')
		->leftJoin('budget as b ON b.budget_code = br.budget_code')
		->setFields($budgetreport)
		->setWhere($condition)
		->runPagination(false);

		return $result;
	}

	public function getBudgetReportExport($budgetcode) {
		$budgetreport = array(
			'br.budget_code',
			'br.january',
			'br.february',
			'br.march',
			'br.april',
			'br.may',
			'br.june',
			'br.july',
			'br.august',
			'br.september',
			'br.october',
			'br.november',
			'br.december',
			'CONCAT(ca.segment5, " - ", ca.accountname) as accountname'
		);

		$condition = '';

		if($budgetcode == 'none' || empty($budgetcode)) {
			$condition .= "b.budget_code != ''";
		} else if($budgetcode != 'none'){
			$condition .= "b.budget_code = '$budgetcode'";
		}

		$result = $this->db->setTable('budget_report as br')
		->leftJoin('chartaccount as ca ON br.accountcode = ca.id')
		->leftJoin('budget as b ON b.budget_code = br.budget_code')
		->setFields($budgetreport)
		->setWhere($condition)
		->runSelect(false)
		->getResult();

		return $result;
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