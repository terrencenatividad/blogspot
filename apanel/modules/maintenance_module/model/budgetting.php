<?php
class budgetting extends wc_model
{
	public function __construct() 
	{
		parent::__construct();
		$this->log = new log();
	}

	public function getBudgetListing($data, $sort, $search, $filter)
	{
		$condition = '';
		if ($search) {
			$condition .= $this->generateSearch($search, array('budget_code', 'budgetdesc'));
		}

		$result = $this->db->setTable('budget')
		->setFields($data)
		->setWhere($condition)
		->setOrderBy($sort)
		->runPagination();

		return $result;
	}

	public function getBudgetListingExport($data, $sort, $search, $filter)
	{
		$condition = '';
		if ($search) {
			$condition .= $this->generateSearch($search, array('budget_code', 'budgetdesc'));
		}

		$result = $this->db->setTable('budget')
		->setFields($data)
		->setWhere($condition)
		->setOrderBy($sort)
		->runSelect()
		->getResult();

		return $result;
	}

	public function getApprover($id)
	{
		$result = $this->db->setTable('budget')
		->setFields('approver')
		->setWhere("id = '$id'")
		->runSelect()
		->getRow();

		return $result;
	}

	public function getApproverSupplement($id)
	{
		$result = $this->db->setTable('budget_supplement bs')
		->setFields('b.approver')
		->leftJoin('budget as b ON bs.budget_id = b.id')
		->setWhere("bs.id = '$id'")
		->runSelect(false)
		->getRow();

		return $result;
	}

	public function getBudgetAccountsOnSupplement($id)
	{
		$result = $this->db->setTable('budget b')
		->setFields('bd.accountcode ind, CONCAT(ca.segment5, " - ", ca.accountname) as val')
		->leftJoin('budget_details as bd ON b.budget_code = bd.budget_code')
		->leftJoin('chartaccount as ca ON ca.id = bd.accountcode')
		->setWhere("b.id = '$id'")
		->runSelect()
		->getResult();

		return $result;
	}

	public function getSupplementValues($id)
	{
		$result = $this->db->setTable('budget_supplement bs')
		->setFields('bs.accountcode as accountcode, CONCAT(ca.segment5, " - ", ca.accountname) as accountname, bs.description as description, bs.amount as amount, bs.status as status, bs.effectivity_date as effectivity_date, bs.transactiondate as transactiondate, bs.prepared_by as prepared_by')
		->leftJoin('chartaccount as ca ON ca.id = bs.accountcode')
		->setWhere("bs.id = '$id'")
		->runSelect(false)
		->getRow();

		return $result;
	}

	public function updateSupplement($id,$fields)
	{
		$result 			   = $this->db->setTable('budget_supplement')
		->setValues($fields)
		->setWhere("id = '$id'")
		->setLimit(1)
		->runUpdate(false);

		return $result;
	}

	public function getBudgetSupplements($id)
	{
		$result = $this->db->setTable('budget_supplement bs')
		->setFields('b.approver, bs.id as id, CONCAT(ca.segment5, " - ", ca.accountname) as accountname, bs.description as description, bs.amount as amount, bs.status as status, bs.effectivity_date as effectivity_date')
		->leftJoin('chartaccount as ca ON ca.id = bs.accountcode')
		->leftJoin('budget b ON bs.budget_id = b.id')
		->setWhere("bs.budget_id = '$id'")
		->runPagination(false);

		return $result;
	}

	public function saveSupplement($supplements) {
		$result = $this->db->setTable('budget_supplement')
		->setValues($supplements)
		->runInsert(false);

		return $result;
	}

	public function saveBudgetSupplementReport($fields) {
		$result = $this->db->setTable('budget_report')
		->setValues($fields)
		->runInsert(false);

		return $result;
	}

	public function getApproverName($code)
	{
		$result = $this->db->setTable('cost_center')
		->setFields('approver')
		->setWhere("costcenter_code = '$code'")
		->runSelect()
		->getRow();

		return $result;
	}

	public function getBOMCode($id)
	{
		$result = $this->db->setTable('bom')
		->setFields('bom_code')
		->setWhere("id = '$id'")
		->runSelect()
		->getRow();

		return $result;
	}

	public function getBudgetCenter()
	{
		$result = $this->db->setTable('cost_center')
		->setFields('costcenter_code ind, CONCAT(costcenter_code, " - ", name) val')
		->runSelect()
		->getResult();

		return $result;
	}

	public function getUserList()
	{
		$result = $this->db->setTable('wc_users')
		->setFields('username ind, CONCAT(firstname, "  ", lastname) val')
		->runSelect()
		->getResult();

		return $result;
	}


	public function getAccounts($type)
	{
		$result = $this->db->setTable('chartaccount')
		->setFields('id ind, CONCAT(segment5, " - ", accountname) val')
		->setWhere("fspresentation = '$type'")
		->runSelect()
		->getResult();

		return $result;
	}

	public function getBudgetById($data, $id)
	{
		$result = $this->db->setTable('budget')
		->setFields($data)
		->setWhere("id = '$id'")
		->runSelect()
		->getRow();

		return $result;
	}

	public function getBudgetCode($id)
	{
		$result = $this->db->setTable('budget')
		->setFields('budget_code')
		->setWhere("id = '$id'")
		->runSelect()
		->getRow();

		return $result;
	}

	public function getBudgetDetails($data, $id)
	{
		$code = $this->getBudgetCode($id);
		$result = $this->db->setTable('budget_details')
		->setFields($data)
		->setWhere("budget_code = '$code->budget_code'")
		->runSelect()
		->getResult();

		return $result;
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

	public function fileExport($data, $sort, $search)
	{
		$condition = '';
		if ($search) {
			$condition .= $this->generateSearch($search, array('bom_code', 'description'));
		}

		$result = $this->db->setTable('bom')
		->setFields($data)
		->setWhere($condition)
		->setOrderBy($sort)
		->runSelect()
		->getResult();
		
		return $result;
	}

	public function updateStat($data,$code)
	{
		$condition 			   = " id = '$code' ";

		$result 			   = $this->db->setTable('budget')
		->setValues($data)
		->setWhere($condition)
		->setLimit(1)
		->runUpdate();

		return $result;
	}

	public function updateSupplementApprove($id, $fields)
	{
		$result 			   = $this->db->setTable('budget_supplement')
		->setValues($fields)
		->setWhere("id = '$id'")
		->setLimit(1)
		->runUpdate(false);

		$months = array(
			'1' => 'january',
			'2' => 'february',
			'3' => 'march',
			'4' => 'april',
			'5' => 'may',
			'6' => 'june',
			'7' => 'july',
			'8' => 'august',
			'9' => 'september',
			'10' => 'october',
			'11' => 'november',
			'12' => 'december'
		);

		$getdetails = $this->getIdOfBudgetCode($id);
		$temp = array();
		if($getdetails) {
			$budget_code = $getdetails->budget_code;
			$accountcode = $getdetails->accountcode;
			$description = $getdetails->description;
			$budget_check = $getdetails->budget_check;
			$effectivity_date = $getdetails->effectivity_date;
			$month = date('m', strtotime($effectivity_date));
			$year = date('Y', strtotime($effectivity_date));
			$amount = $getdetails->amount;
			$budget_per_month = (12 - $month) + 1;
			$rounded = round($amount / $budget_per_month);
			$temp['budget_code'] = $budget_code;
			$temp['accountcode'] = $accountcode;
			for($i = round($month); $i <= count($months); $i++) {
				$temp[$months[$i]] = $rounded;
			}
			$temp['year'] = $year;
			$temp['budget_check'] = $budget_check;
			$temp['year'] = date('Y');
			if($fields['status'] == 'approved') {
				$result = $this->db->setTable('budget_report')
				->setValues($temp)
				->runInsert(false);
			}
		}

		return $result;
	}



	public function updateBudgetSupplementReport($id, $fields)
	{
		$budgetcode = $this->getIdOfBudgetCode($id);
		$code = $budgetcode->budget_code;
		$accountcode = $budgetcode->accountcode;
		$result 			   = $this->db->setTable('budget_report')
		->setValues($fields)
		->setWhere("budget_code = '$code' AND accountcode = '$accountcode'")
		->setLimit(1)
		->runUpdate(false);

		return $result;
	}

	public function updateSupplementReject($id, $fields)
	{
		$result 			   = $this->db->setTable('budget_supplement')
		->setValues($fields)
		->setWhere("id = '$id'")
		->setLimit(1)
		->runUpdate(false);

		return $result;
	}

	public function updateBudgetStatus($fields, $id)
	{
		$result = $this->db->setTable('budget')
		->setValues($fields)
		->setWhere("id = '$id'")
		->setLimit(1)
		->runUpdate();
		$months = array(
			'1' => 'january',
			'2' => 'february',
			'3' => 'march',
			'4' => 'april',
			'5' => 'may',
			'6' => 'june',
			'7' => 'july',
			'8' => 'august',
			'9' => 'september',
			'10' => 'october',
			'11' => 'november',
			'12' => 'december'
		);

		$stat = ucfirst($fields['status']);

		$budget = $this->getIdOfBudget($id);
		$temp = array();
		$reports = array();
		if($budget) {
			foreach($budget as $row) {
				$budget_code = $row->budget_code;
				$accountcode = $row->accountcode;
				$description = $row->description;
				$budget_check = $row->budget_check;
				$amount = $row->amount;
				$effectivity_date = $row->effectivity_date;
				$month = date('m', strtotime($effectivity_date));
				$year = date('Y', strtotime($effectivity_date));
				$budget_per_month = (12 - $month) + 1;
				$rounded = round($amount / $budget_per_month);
				$temp['budget_code'] = $budget_code;
				$temp['accountcode'] = $accountcode;
				for($i = round($month); $i <= count($months); $i++) {
					$temp[$months[$i]] = $rounded;	
				}
				$temp['year'] = $year;
				$temp['budget_check'] = $budget_check;
				$reports[] = $temp;	
			}			
			if($fields['status'] == 'approved') {
				$result = $this->db->setTable('budget_report')
				->setValues($reports)
				->runInsert(false);
			}
		}
		$budgetcode = $budget[0]->budget_code;

		if ($result) {
			$this->log->saveActivity("$stat Budget [$budgetcode]");
		}

		return $result;
	}

	public function updateBudget($data, $id, $budget_code)
	{
		$result = $this->db->setTable('budget')
		->setValues($data)
		->setWhere("id = '$id'")
		->setLimit(1)
		->runUpdate();

		$result = $this->db->setTable('budget_details')
		->setWhere("budget_code = '$budget_code'")
		->runDelete();

		if ($result) {
			$this->log->saveActivity("Edited Budget [$budget_code]");
		}

		return $result;
	}

	public function deleteSupplement($id)
	{
		$result = $this->db->setTable('budget_supplement')
		->setWhere("id = '$id'")
		->runDelete(false);

		return $result;
	}


	public function saveDetails($budget_details) {
		$result = $this->db->setTable('budget_details')
		->setValuesFromPost($budget_details)
		->runInsert();

		return $result;
	}

	public function deleteBudget($id)
	{
		$cond   = "";
		$pieces = explode(',', $id["id"]);
		$errmsg = array();

		for($i = 0; $i < count($pieces); $i++)
		{
			$id     = $pieces[$i];

			$cond = "id = '$id'";

			$result = $this->db->setTable('budget')
			->setWhere($cond)
			->runDelete();
			
			if(!$result)
				$errmsg[] = "<p class = 'no-margin'>Deleting BOM ID: $id</p>";
			else
				$this->log->saveActivity("Delete ATC Code [$id]");
		}

		return $errmsg;
	}

	public function getBudgetAccounts($budgetcode)
	{
		$result = $this->db->setTable('budget_details bd')
		->setFields('bd.accountcode as accountcode, ca.accountname as accountname, bd.description as description, bd.amount as amount')
		->leftJoin('chartaccount ca ON bd.accountcode = ca.id')
		->setWhere("budget_code = '$budgetcode' AND amount != 0")
		->runSelect()
		->getResult();

		return $result;
	}

	public function getSupplementAccounts($id)
	{
		$result = $this->db->setTable('budget_supplement bs')
		->setFields('bs.accountcode as accountcode, bs.description as description, SUM(bs.amount) as amount')
		->leftJoin('chartaccount ca ON bs.accountcode = ca.id')
		->setWhere("bs.budget_id = '$id' AND bs.status = 'approved'")
		->setGroupBy('bs.accountcode')
		->runSelect(false)
		->getResult();

		return $result;
	}

	public function getBudgetAccountsOnEdit($budgetcode)
	{
		$result = $this->db->setTable('budget_details bd')
		->setFields('bd.accountcode as accountcode, ca.accountname as accountname, bd.description as description, bd.amount as amount')
		->leftJoin('chartaccount ca ON bd.accountcode = ca.id')
		->setWhere("budget_code = '$budgetcode'")
		->runSelect()
		->getResult();

		return $result;
	}

	public function saveBudget($budget, $budget_details) {
		$result = $this->db->setTable('budget')
		->setValues($budget)
		->runInsert();
		$budget_code = $budget['budget_code'];

		$result = $this->db->setTable('budget_details')
		->setValuesFromPost($budget_details)
		->runInsert();

		if ($result) {
			$this->log->saveActivity("Created Budget [$budget_code]");
		}

		// echo $this->db->getQuery();

		return $result;
	}

	public function getIdOfBudgetCode($id) {
		$result  = $this->db->setTable('budget_supplement bs')
		->leftJoin('budget_details as bd ON bs.accountcode = bd.accountcode')
		->leftJoin('budget as b ON bd.budget_code = b.budget_code')
		->setFields('bd.budget_code, bs.accountcode, bs.description, bs.amount, bs.effectivity_date, b.budget_check')
		->setWhere("bs.id = '$id' AND bd.budget_code = b.budget_code")
		->runSelect(false)
		->getRow();
		return $result;
	}

	public function saveBudgetReportSupplement($id) {
		$getdetails = $this->getIdOfBudgetCode($id);
		$return = false;
		if($getdetails) {
			$temp = array();
			$budget_code = $getdetails->budget_code;
			$accountcode = $getdetails->accountcode;
			$description = $getdetails->description;
			$amount = $getdetails->amount;
			$rounded = round($amount / 12);
			$temp['budget_code'] = $budget_code;
			$temp['accountcode'] = $accountcode;
			$temp['january'] = $rounded;
			$temp['february'] = $rounded;
			$temp['march'] = $rounded;
			$temp['april'] = $rounded;
			$temp['may'] = $rounded;
			$temp['june'] = $rounded;
			$temp['july'] = $rounded;
			$temp['august'] = $rounded;
			$temp['september'] = $rounded;
			$temp['october'] = $rounded;
			$temp['november'] = $rounded;
			$temp['december'] = $rounded;
			$temp['year'] = date('Y');
			$result = $this->db->setTable('budget_report')
			->setValues($temp)
			->runInsert(false);	
		}
		return $result;
	}

	public function getIdOfBudget($id) {
		$result  = $this->db->setTable('budget_details bd')
		->leftJoin('budget as b ON bd.budget_code = b.budget_code')
		->setFields('bd.budget_code, bd.accountcode, bd.description, bd.amount, b.budget_check, b.effectivity_date')
		->setWhere("b.id = '$id'")
		->runSelect()
		->getResult();
		return $result;
	}

	public function saveBudgetReport($id) {
		$budget = $this->getIdOfBudget($id);
		$temp = array();
		$fields = array();
		if($budget) {
			foreach($budget as $row) {
				$budget_code = $row->budget_code;
				$accountcode = $row->accountcode;
				$description = $row->description;
				$budget_check = $row->budget_check;
				$amount = $row->amount;
				$rounded = round($amount / 12);
				$temp['budget_code'] = $budget_code;
				$temp['accountcode'] = $accountcode;
				$temp['january'] = $rounded;
				$temp['february'] = $rounded;
				$temp['march'] = $rounded;
				$temp['april'] = $rounded;
				$temp['may'] = $rounded;
				$temp['june'] = $rounded;
				$temp['july'] = $rounded;
				$temp['august'] = $rounded;
				$temp['september'] = $rounded;
				$temp['october'] = $rounded;
				$temp['november'] = $rounded;
				$temp['december'] = $rounded;
				$temp['year'] = date('Y');
				$temp['budget_check'] = $budget_check;
				$fields[] = $temp;	
			}			
			$result = $this->db->setTable('budget_report')
			->setValues($fields)
			->runInsert(false);
		} else {
			$result = false;
		}

		return $result;
	}

	public function updateBudgetReport($budgetcode, $budgetreport) {
		$result = $this->db->setTable('budget_report')
		->setWhere("budget_code = '$budgetcode'")
		->runDelete(false);

		$result = $this->db->setTable('budget_report')
		->setValues($budgetreport)
		->runInsert(false);

		return $result;
	}
}
?>