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
		->setFields('ca.segment5 segment5, ca.accountname description, SUM(bd.amount) + SUM(bs.amount) amount, SUM(ab.actual) actual, (SUM(bd.amount)-SUM(bd.actual)) variance')
		->leftJoin('budget b ON b.budget_code = bd.budget_code')
		->leftJoin('actual_budget as ab ON ab.accountcode = bd.accountcode')
		->leftJoin('chartaccount ca ON bd.accountcode = ca.id')
		->leftJoin('budget_supplement as bs ON b.id = bs.budget_id AND bs.accountcode = bd.accountcode')
		->setGroupBy('bd.accountcode')
		->setOrderBy('bd.accountcode')
		->setWhere($condition . $type)
		->runPagination();

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
		->setFields('bd.accountcode accountcode, ca.accountname description, SUM(bd.amount) amount, SUM(bd.actual) actual, (SUM(bd.amount)-SUM(bd.actual)) variance')
		->leftJoin('budget b ON b.budget_code = bd.budget_code')
		->leftJoin('chartaccount ca ON bd.accountcode = ca.segment5')
		->setGroupBy('bd.accountcode')
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