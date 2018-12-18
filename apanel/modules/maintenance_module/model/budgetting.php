<?php
class budgetting extends wc_model
{
	public function __construct() 
	{
		parent::__construct();
		$this->log = new log();
	}

	public function getJobListing($data, $sort, $search, $filter)
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

	public function getApprover($id)
	{
		$result = $this->db->setTable('budget')
		->setFields('approver')
		->setWhere("id = '$id'")
		->runSelect()
		->getRow();

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
		->setFields('costcenter_code ind, costcenter_code val')
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
		->setFields('accountname, segment5')
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

	public function updateBudgetStatus($fields, $id)
	{
		$result 			   = $this->db->setTable('budget')
		->setValues($fields)
		->setWhere("id = '$id'")
		->setLimit(1)
		->runUpdate();

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
			$this->log->saveActivity("Update Content [$id]");
		}

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
		->leftJoin('chartaccount ca ON bd.accountcode = ca.segment5')
		->setWhere("budget_code = '$budgetcode' AND amount != 0")
		->runSelect()
		->getResult();

		return $result;
	}

	public function getBudgetAccountsOnEdit($budgetcode)
	{
		$result = $this->db->setTable('budget_details bd')
		->setFields('bd.accountcode as accountcode, ca.accountname as accountname, bd.description as description, bd.amount as amount')
		->leftJoin('chartaccount ca ON bd.accountcode = ca.segment5')
		->setWhere("budget_code = '$budgetcode'")
		->runSelect()
		->getResult();

		return $result;
	}

	public function saveBudget($budget, $budget_details) {
		$result = $this->db->setTable('budget')
		->setValues($budget)
		->runInsert();

		$result = $this->db->setTable('budget_details')
		->setValuesFromPost($budget_details)
		->runInsert();

		return $result;
	}
}
?>