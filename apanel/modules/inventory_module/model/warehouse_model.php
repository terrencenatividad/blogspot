<?php
class warehouse_model extends wc_model {

	public function retrieveListing($search, $sort)
	{
		$fields 			= array('warehousecode','description','stat');

		$addtl_cond 		= (!empty($search)) ? "AND (warehousecode LIKE '%$search%' OR description LIKE '%$search%'  ) " : "";

		return $this->db->setTable('warehouse')
						->setFields($fields)
						->setWhere("$addtl_cond")
						->setOrderBy($sort)
						->runPagination();


		//echo $this->db->getQuery();
	}

	public function retrieveExistingWarehouse($data, $warehousecode)
	{
		$condition 		=	" stat = 'active' AND warehousecode = '$warehousecode' ";
		return $this->db->setTable('warehouse')
						->setFields($data)
						->setWhere($condition)
						->runSelect()
						->getRow();
	}

	public function checkifUsed($warehousecode)
	{
		return $this->db->setTable('invfile')
						->setFields(array('itemcode'))
						->setWhere("warehouse='$warehousecode'")
						->runSelect()
						->getRow();
	}

	public function insertWarehouse($data)
	{
		$data["stat"]     	   = "active";

		$result = $this->db->setTable('warehouse')
				->setValues($data)
				->runInsert();

		return $result;
	}

	public function updateWarehouse($data, $warehousecode)
	{
		$data["stat"]     	   = "active";

		$condition 			   = " warehousecode = '$warehousecode' ";

		$result 			   = $this->db->setTable('warehouse')
											->setValues($data)
											->setWhere($condition)
											->setLimit(1)
											->runUpdate();

		return $result;
	}

	public function deleteWarehouse($id)
	{
		$condition   = "";
		$id_array 	 = explode(',', $id['id']);
		$errmsg 	 = array();

		for($i = 0; $i < count($id_array); $i++)
		{
			$warehousecode  = $id_array[$i];

			$ret_warehouse = $this->checkifUsed($warehousecode);
	
			if( !empty($ret_warehouse) && count($ret_warehouse) > 0 )
			{
				$errmsg[]  = "<p class = 'no-margin'>Deleting Warehouse: $warehousecode Failed. This Warehouse is already being used. </p>";
			}
			else
			{
				$condition 		= " warehousecode = '$warehousecode'";
				
				$result 		= $this->db->setTable('warehouse')
										->setWhere($condition)
										->runDelete();

				$error 			= $this->db->getError();		

				if ($error == 'locked') {
					$errmsg[]  = "<p class = 'no-margin'>Deleting Warehouse: $warehousecode</p>";
				}
			}
		}

		return $errmsg;
	}

	public function export($search, $sort)
	{
		$fields 			= array('warehousecode','description');

		$addtl_cond 		= (!empty($search)) ? "AND (warehousecode LIKE '%$search%' OR description LIKE '%$search%'  ) " : "";

		return $this->db->setTable('warehouse')
						->setFields($fields)
						->setWhere(" stat = 'active' $addtl_cond")
						->setOrderBy($sort)
						->runSelect()
						->getResult();
	}

	public function check_duplicate($current)
	{
		return $this->db->setTable('warehouse')
						->setFields('COUNT(warehousecode) count')
						->setWhere(" warehousecode = '$current'")
						->runSelect()
						->getResult();
	}

	public function importCustomers($data)
	{
		$result = $this->db->setTable('warehouse')
				->setValuesFromPost($data)
				->runInsert();

		return $result;
	}

	public function updateStat($data,$warehousecode)
	{
		$condition 			   = " warehousecode = '$warehousecode' ";

		$result 			   = $this->db->setTable('warehouse')
											->setValues($data)
											->setWhere($condition)
											->setLimit(1)
											->runUpdate();

		return $result;
	}
}