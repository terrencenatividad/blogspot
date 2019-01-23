<?php
class budget_variance_model extends wc_model {

	public function getCostCenterList() {
		$result = $this->db->setTable('cost_center')
		->setFields("costcenter_code ind, costcenter_code val")
		->setOrderBy("id")
		->runSelect()
		->getResult();

		return $result;
	}

	public function getBudgetList($costcenter, $budget_type) {
		$condition = '';
		$type = '';
		
		if($costcenter == 'none' || empty($costcenter)) {
			$condition .= "b.budget_center_code != ''";
		} else if($costcenter != 'none'){
			$condition .= "b.budget_center_code = '$costcenter'";
		}

		if($budget_type == 'none' || empty($budget_type)) {
			$type .= " AND b.budget_type != ''";
		} else {
			$type .= " AND b.budget_type = '$budget_type'";
		}

		$result = $this->db->setTable('budget_details bd')
		->setFields('ca.segment5 segment5, ca.accountname description, bd.amount + IF(IFNULL(bs.amount,0) = 0,0,SUM(bs.amount)) as amount, IFNULL(ab.actual,0) as actual, b.effectivity_date as effectivity_date, bd.amount + IF(IFNULL(bs.amount,0) = 0,0,SUM(bs.amount)) - IFNULL(ab.actual,0) as variance')
		->leftJoin('budget b ON b.budget_code = bd.budget_code')
		->leftJoin('chartaccount ca ON bd.accountcode = ca.id')
		->leftJoin("(SELECT SUM(actual) as actual, accountcode, budget_code FROM actual_budget WHERE id != '' GROUP BY accountcode, budget_code)
			as ab ON  ab.accountcode = bd.accountcode AND ab.budget_code = bd.budget_code")
		->leftJoin("budget_supplement as bs ON b.id = bs.budget_id AND bs.accountcode = bd.accountcode AND bs.status = 'approved'")
		->setGroupBy('bd.accountcode, bd.budget_code')
		->setOrderBy('bd.accountcode')
		->setWhere($condition . $type)
		->runPagination();

		 // echo $this->db->getQuery();

		return $result;
	}

	public function getBudgetReportExport($costcenter, $budget_type) {
		$condition = '';
		$type = '';
		
		if($costcenter == 'none' || empty($costcenter)) {
			$condition .= "b.budget_center_code != ''";
		} else if($costcenter != 'none'){
			$condition .= "b.budget_center_code = '$costcenter'";
		}

		if($budget_type == 'none' || empty($budget_type)) {
			$type .= " AND b.budget_type != ''";
		} else {
			$type .= " AND b.budget_type = '$budget_type'";
		}

		$result = $this->db->setTable('budget_details bd')
		->setFields('bd.accountcode accountcode, ca.accountname description, b.effectivity_date as effectivity_date, bd.amount + IF(IFNULL(bs.amount,0) = 0,0,SUM(bs.amount)) as amount, IFNULL(ab.actual,0) as actual, b.effectivity_date as effectivity_date, bd.amount + IF(IFNULL(bs.amount,0) = 0,0,SUM(bs.amount)) - IFNULL(ab.actual,0) as variance')
		->leftJoin('budget b ON b.budget_code = bd.budget_code')
		->leftJoin('chartaccount ca ON bd.accountcode = ca.id')
		->leftJoin("(SELECT SUM(actual) as actual, accountcode, budget_code FROM actual_budget WHERE id != '' GROUP BY accountcode, budget_code)
			as ab ON  ab.accountcode = bd.accountcode AND ab.budget_code = bd.budget_code")
		->leftJoin("budget_supplement as bs ON b.id = bs.budget_id AND bs.accountcode = bd.accountcode AND bs.status = 'approved'")
		->setGroupBy('bd.accountcode, bd.budget_code')
		->setOrderBy('bd.accountcode')
		->setWhere($condition . $type)
		->runSelect()
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