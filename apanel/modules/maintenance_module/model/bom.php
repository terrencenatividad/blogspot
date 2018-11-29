<?php
class bom extends wc_model
{
	public function __construct() 
	{
		parent::__construct();
		$this->log = new log();
	}

	public function getBOMListing($data, $sort, $search, $filter)
	{
		$condition = '';
		if ($search) {
			$condition .= $this->generateSearch($search, array('bom_code', 'description'));
		}

		$result = $this->db->setTable('bom')
		->setFields($data)
		->setWhere($condition)
		->setOrderBy($sort)
		->runPagination();

		return $result;
	}

	public function getItemDetails($itemcode) {
		$result = $this->db->setTable('items')
		->setFields('itemname, itemdesc, uom_base')
		->setWhere("itemcode = '$itemcode'")
		->setLimit(1)
		->runSelect()
		->getRow();

		return $result;
	}

	public function getItemDesc($itemcode) {
		$result = $this->db->setTable('items')
		->setFields('itemdesc')
		->setWhere("itemcode = '$itemcode'")
		->setLimit(1)
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

	public function getBundleList()
	{
		$result = $this->db->setTable('items')
		->setFields('itemcode ind, itemcode val')
		->setWhere("bundle = '1'")
		->runSelect()
		->getResult();

		return $result;
	}

	public function getItemList()
	{
		$result = $this->db->setTable('items')
		->setFields('itemcode ind, CONCAT(itemcode, " - " , itemname) val')
		->setWhere("bundle = '0'")
		->runSelect()
		->getResult();

		return $result;
	}

	public function getBOMById($data, $id)
	{
		$result = $this->db->setTable('bom')
		->setFields($data)
		->setWhere("id = '$id'")
		->runSelect()
		->getRow();

		return $result;
	}

	public function getBOMDetails($data, $bomcode) {
		$result = $this->db->setTable('bomdetails')
		->setFields($data)
		->setWhere("bom_code = '$bomcode'")
		->runSelect()
		->getResult();

		return $result;
	}

	public function getDetails($data, $code)
	{
		$result = $this->db->setTable('bomdetails')
		->setFields($data)
		->setWhere("bom_code = '$code'")
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

		$result 			   = $this->db->setTable('bom')
		->setValues($data)
		->setWhere($condition)
		->setLimit(1)
		->runUpdate();

		return $result;
	}

	public function updateBOM($data, $id, $bomcode)
	{

		$result = $this->db->setTable('bom')
		->setValues($data)
		->setWhere("id = '$id'")
		->setLimit(1)
		->runUpdate();

		$result = $this->db->setTable('bomdetails')
		->setWhere("bom_code = '$bomcode'")
		->runDelete();

		if ($result) {
			$this->log->saveActivity("Update Content [$id]");
		}

		return $result;
	}


	public function saveDetails($bom_details) {
		$result = $this->db->setTable('bomdetails')
		->setValuesFromPost($bom_details)
		->runInsert();

		return $result;
	}

	public function deleteBOM($id)
	{
		$cond   = "";
		$pieces = explode(',', $id["id"]);
		$errmsg = array();

		for($i = 0; $i < count($pieces); $i++)
		{
			$id     = $pieces[$i];

			$cond = "id = '$id'";

			$result = $this->db->setTable('bom')
			->setWhere($cond)
			->runDelete();
			
			if(!$result)
				$errmsg[] = "<p class = 'no-margin'>Deleting BOM ID: $id</p>";
			else
				$this->log->saveActivity("Delete ATC Code [$id]");
		}

		return $errmsg;
	}

	public function saveBOM($bom, $bom_details) {
		$result = $this->db->setTable('bom')
		->setValues($bom)
		->runInsert();

		$result = $this->db->setTable('bomdetails')
		->setValuesFromPost($bom_details)
		->runInsert();

		return $result;
	}
}
?>