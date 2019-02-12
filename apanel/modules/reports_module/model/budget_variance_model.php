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

	public function getBudgetList($costcenter, $budget_type, $date) {
		$condition = '';
		$type = '';
		$get_date = '';
		
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

		$first_date = '';
		$second_date = '';

		if($date == 'none' || empty($date)) {
			$get_date .= " AND b.effectivity_date != ''";
		} else {
			$dates = explode('-', $date);
			$arr = array();
			for($i=0;$i<count($dates);$i++) {
				$arr[] = $dates[$i];
			}
			$first_date = date('Y-m-d', strtotime($arr[0]));
			$second_date = date('Y-m-d', strtotime($arr[1]));
			$get_date .= " AND b.effectivity_date BETWEEN '$first_date' AND '$second_date'";
		}
		$query = '';
		if($first_date != '') {
			$query .= " AND effectivity_date BETWEEN '$first_date' AND '$second_date'";
		}

		$result = $this->db->setTable('budget_details bd')
		->setFields('ca.segment5 segment5, ca.accountname description, bd.amount + IF(IFNULL(bs.amount,0) = 0,0,SUM(bs.amount)) - IF(IFNULL(ab.allocated, 0) = 0, 0, ab.allocated) - IF(IFNULL(ab.actual, 0) = 0, 0, ab.actual) as available,
			IF(IFNULL(ab.allocated, 0) = 0, 0, ab.allocated) as allocated,  bd.amount + IF(IFNULL(bs.amount,0) = 0,0,SUM(bs.amount)) as amount, IF(IFNULL(ab.actual, 0) = 0, 0, ab.actual) as actual, b.effectivity_date as effectivity_date, bd.amount + IF(IFNULL(bs.amount,0) = 0,0,SUM(bs.amount)) - IFNULL(ab.actual,0) as variance')
		->leftJoin('budget b ON b.budget_code = bd.budget_code AND b.status = "approved"')
		->leftJoin('chartaccount ca ON bd.accountcode = ca.id')
		->leftJoin("(SELECT SUM(actual) as actual, SUM(allocated) as allocated, accountcode, budget_code FROM actual_budget WHERE id != '' AND voucherno NOT LIKE '%DV_%' GROUP BY accountcode, budget_code)
			as ab ON  ab.accountcode = bd.accountcode AND ab.budget_code = bd.budget_code")
		->leftJoin("(SELECT SUM(amount) as amount, accountcode, effectivity_date, budget_id FROM budget_supplement WHERE status = 'approved' '$query' GROUP BY accountcode) as bs ON b.id = bs.budget_id AND bs.accountcode = bd.accountcode")
		->setGroupBy('bd.accountcode, bd.budget_code')
		->setOrderBy('bd.accountcode')
		->setWhere($condition . $type . $get_date)
		->runPagination();

		// echo $this->db->getQuery();

		return $result;
	}

	public function getBudgetReportExport($costcenter, $budget_type, $date) {
		$condition = '';
		$type = '';
		$get_date = '';
		
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

		$first_date = '';
		$second_date = '';

		if($date == 'none' || empty($date)) {
			$get_date .= " AND b.effectivity_date != ''";
		} else {
			$dates = explode('-', $date);
			$arr = array();
			for($i=0;$i<count($dates);$i++) {
				$arr[] = $dates[$i];
			}
			$first_date = date('Y-m-d', strtotime($arr[0]));
			$second_date = date('Y-m-d', strtotime($arr[1]));
			$get_date .= " AND b.effectivity_date BETWEEN '$first_date' AND '$second_date'";
		}

		$query = '';
		if($first_date != '') {
			$query .= " AND effectivity_date BETWEEN '$first_date' AND '$second_date'";
		}

		$result = $this->db->setTable('budget_details bd')
		->setFields('ca.segment5 segment5, ca.accountname description, bd.amount + IF(IFNULL(bs.amount,0) = 0,0,SUM(bs.amount)) - IF(IFNULL(ab.allocated, 0) = 0, 0, ab.allocated) - IF(IFNULL(ab.actual, 0) = 0, 0, ab.actual) as available,
			IF(IFNULL(ab.allocated, 0) = 0, 0, ab.allocated) as allocated,  bd.amount + IF(IFNULL(bs.amount,0) = 0,0,SUM(bs.amount)) as amount, IF(IFNULL(ab.actual, 0) = 0, 0, ab.actual) as actual, b.effectivity_date as effectivity_date, bd.amount + IF(IFNULL(bs.amount,0) = 0,0,SUM(bs.amount)) - IFNULL(ab.actual,0) as variance')
		->leftJoin('budget b ON b.budget_code = bd.budget_code AND b.status = "approved"')
		->leftJoin('chartaccount ca ON bd.accountcode = ca.id')
		->leftJoin("(SELECT SUM(actual) as actual, SUM(allocated) as allocated, accountcode, budget_code FROM actual_budget WHERE id != '' AND voucherno NOT LIKE '%DV_%' GROUP BY accountcode, budget_code)
			as ab ON  ab.accountcode = bd.accountcode AND ab.budget_code = bd.budget_code")
		->leftJoin("(SELECT SUM(amount) as amount, accountcode, effectivity_date, budget_id FROM budget_supplement WHERE status = 'approved' '$query' GROUP BY accountcode) as bs ON b.id = bs.budget_id AND bs.accountcode = bd.accountcode")
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