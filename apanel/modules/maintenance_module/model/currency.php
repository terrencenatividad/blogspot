<?php
	class currency extends wc_model
	{
		public function retrieveListing($search="", $sort ,$limit)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND (currencycode LIKE '%$search%' OR currency LIKE '%$search%' ) " 	: 	"";

			$fields 	=	array('currencycode','currency');

			$result = $this->db->setTable('currency')
							->setFields($fields)
							->setWhere(" stat = 'active' $add_cond ")
							->setOrderBy($sort)
							->runPagination();
			return $result;
			// echo $this->db->getQuery();
		}

		public function retrieveExistingCurrency($data, $currencycode)
		{
			$condition 		=	" currencycode = '$currencycode' ";
			
			return $this->db->setTable('currency')
							->setFields($data)
							->setWhere($condition)
							->runSelect()
							->getRow();
		}

		public function insertCurrency($data)
		{
			$result = $this->db->setTable('currency')
					->setValues($data)
					->runInsert();

			return $result;
		}

		public function updateCurrency($data, $currencycode)
		{
			
			$condition 			   = " currencycode = '$currencycode' ";

			$result 			   = $this->db->setTable('currency')
											  ->setValues($data)
											  ->setWhere($condition)
											  ->setLimit(1)
											  ->runUpdate();

			return $result;
		}

		public function deleteCurrency($id)
		{
			$condition   = "";
			$id_array 	 = explode(',', $id['id']);
			$errmsg 	 = array();

			for($i = 0; $i < count($id_array); $i++)
			{
				$currencycode    = $id_array[$i];

				$condition 		= " currencycode = '$currencycode'";
				
				$result 		= $this->db->setTable('currency')
										->setWhere($condition)
										->runDelete();
										// echo $this->db->getQuery();

				$error 			= $this->db->getError();		

				if ($error == 'locked') {
					$errmsg[]  = "<p class = 'no-margin'>Deleting Currency: $currencycode</p>";
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
	}
?>