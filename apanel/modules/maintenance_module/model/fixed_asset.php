<?php
	class fixed_asset extends wc_model
	{
		public function retrieveListing($search="", $sort ,$limit, $fields)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND (itemno LIKE '%$search%' OR description LIKE '%$search%' ) " 	: 	"";

			$result = $this->db->setTable('fixed_asset')
							->setFields($fields)
							->setWhere($add_cond)
							->setOrderBy($sort)
							->runPagination();
			return $result;
			echo $this->db->getQuery();
		}

		public function retrieveListingCategory($search="", $sort ,$limit, $fields1)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND (description LIKE '%$search%' ) " 	: 	"";
			$fields = array('id','description');
			$result = $this->db->setTable('fixed_asset_category')
							->setFields($fields)
							->setWhere($add_cond)
							->setOrderBy($sort)
							->runPagination();
			return $result;
		}

		public function retrieveExistingFixedAsset($data, $itemno)
		{
			$condition 		=	" itemno = '$itemno' ";
			
			return $this->db->setTable('fixed_asset')
							->setFields($data)
							->setWhere($condition)
							->runSelect()
							->getRow();
		}

		public function retrieveExistingFixedAssetCategory($data, $id)
		{
			$condition 		=	" id = '$id' ";
			
			return $this->db->setTable('fixed_asset_category')
							->setFields($data)
							->setWhere($condition)
							->runSelect()
							->getRow();
		}

		public function getCategory() {
			return $this->db->setTable('fixed_asset_category')
							->setFields('id ind, description val')
							->setWhere(1)
							->runSelect()
							->getResult();
		}

		public function insertFixedAsset($data)
		{
			$result = $this->db->setTable('fixed_asset')
							   ->setValues($data)
							   ->runInsert();

			return $result;
		}

		public function insertFixedAssetCategory($data)
		{
			$result = $this->db->setTable('fixed_asset_category')
							   ->setValues($data)
							   ->runInsert();

			return $result;
		}

		public function updateFixedAsset($data, $itemno)
		{
			
			$condition 			   = " itemno = '$itemno' ";

			$result 			   = $this->db->setTable('fixed_asset')
											  ->setValues($data)
											  ->setWhere($condition)
											  ->setLimit(1)
											  ->runUpdate();

			return $result;
		}

		public function updateFixedAssetCategory($data,$code)
		{
			$condition 			   = " id = '$code' ";

			$result 			   = $this->db->setTable('fixed_asset_category')
											  ->setValues($data)
											  ->setWhere($condition)
											  ->setLimit(1)
											  ->runUpdate();

			return $result;
		}

		public function deleteFixedAsset($id)
		{
			$condition   = "";
			$id_array 	 = explode(',', $id['id']);
			$errmsg 	 = array();

			for($i = 0; $i < count($id_array); $i++)
			{
				$itemno    = $id_array[$i];

				$condition 		= " itemno = '$itemno'";
				
				$result 		= $this->db->setTable('fixed_asset')
										->setWhere($condition)
										->runDelete();
										// echo $this->db->getQuery();

				$error 			= $this->db->getError();		

				if ($error == 'locked') {
					$errmsg[]  = "<p class = 'no-margin'>Deleting Fixed Asset: $itemno</p>";
				}
			
			}
			
			return $errmsg;
		}

		public function deleteFixedAssetCategory($id)
		{
			$condition   = "";
			$id_array 	 = explode(',', $id['id']);
			$errmsg 	 = array();

			for($i = 0; $i < count($id_array); $i++)
			{
				$ids    = $id_array[$i];
				$result 		= $this->db->setTable('fixed_asset_category')
										->setWhere("id = '$ids'")
										->runDelete();

				$error 			= $this->db->getError();		

				if ($error == 'locked') {
					$errmsg[]  = "<p class = 'no-margin'>Deleting Fixed Asset Category: $id</p>";
				}
			
			}
			
			return $errmsg;
		}

		public function check_duplicate($current)
		{
			return $this->db->setTable('currency')
							->setFields('COUNT(currencycode) count')
							->setWhere(" currencycode = '$current'")
							->runSelect()
							->getResult();
		}

		public function updateStat($data,$code)
	{
		$condition 			   = " currencycode = '$code' ";

		$result 			   = $this->db->setTable('currency')
											->setValues($data)
											->setWhere($condition)
											->setLimit(1)
											->runUpdate();

		return $result;
	}
	}
?>