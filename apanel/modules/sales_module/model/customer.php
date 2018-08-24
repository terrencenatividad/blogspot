<?php
	class customer extends wc_model
	{
		public function retrieveListing($search, $sort, $limit)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND ( p.partnercode LIKE '%$search%' OR p.partnername LIKE '%$search%' OR p.first_name LIKE '%$search%' OR p.last_name LIKE '%$search%' OR p.email LIKE '%$search%' ) " 	: 	"";

			$fields 	=	array("p.partnercode","p.partnername","CONCAT(p.first_name,' ', p.last_name) as contact_person", "p.email","p.stat","p.credit_limit");

			return $this->db->setTable('partners p')
							->setFields($fields)
							->setWhere(" p.partnertype = 'customer' AND p.stat != 'deleted' $add_cond ")
							->setOrderBy($sort)
							->runPagination();
							
		}

		public function retrieveExistingCustomer($data, $partnercode)
		{
			$condition 		=	" partnertype = 'customer' AND stat = 'active' AND partnercode = '$partnercode' ";
			return $this->db->setTable('partners')
							->setFields($data)
							->setWhere($condition)
							->runSelect()
							->getRow();
		}

		public function retrieveBusinessTypeDropdown()
		{
			return $this->db->setTable('wc_option')
							->setFields('code ind, value val')
							->setWhere(" type = 'businesstype' ")
							->runSelect(false)
							->getResult();
		}

		public function insertCustomer($data)
		{

			$data["stat"]     	   = "active";
			$data['partnertype']   = "customer";
			$data['credit_limit']  = str_replace(",","",$data['credit_limit']);
			//var_dump($data);

			$result = $this->db->setTable('partners')
					->setValues($data)
					->runInsert();

			return $result;
		}

		public function importCustomers($data)
		{

			$data["stat"]     	   = "active";
			$data['partnertype']   = "customer";
			$data['credit_limit']  = str_replace(",","",$data['credit_limit']);
			//var_dump($data);

			$result = $this->db->setTable('partners')
					->setValuesFromPost($data)
					->runInsert();

			return $result;
		}

		public function updateCustomer($data, $partnercode)
		{
			$data["stat"]     	   = "active";
			$data['credit_limit']  = str_replace(",","",$data['credit_limit']);

			$condition 			   = " partnercode = '$partnercode' AND partnertype = 'customer' ";

			$result 			   = $this->db->setTable('partners')
											  ->setValues($data)
											  ->setWhere($condition)
											  ->setLimit(1)
											  ->runUpdate();
											//echo $this->db->getQuery();
			return $result;
		}

		public function deleteCustomer($id)
		{
			$condition   = "";
			$id_array 	 = explode(',', $id['id']);
			$errmsg 	 = array();

			for($i = 0; $i < count($id_array); $i++)
			{
				$partnercode 	=	$id_array[$i];

				$condition 		= " partnercode = '$partnercode' AND partnertype = 'customer' AND stat = 'active' ";
				
				$result 		= $this->db->setTable('partners')
										->setWhere($condition)
										->runDelete();
				$error 			= $this->db->getError();

				if ($error == 'locked') {
					$errmsg[]  = "<p class = 'no-margin'>Deleting Customer: $partnercode . There are existing transactions for this customer.</p>";
				}
			
			}
			
			return $errmsg;
		}

		public function check_duplicate($current)
		{
			return $this->db->setTable('partners')
							->setFields('COUNT(partnercode) count')
							->setWhere(" partnercode = '$current'")
							->runSelect()
							->getResult();
		}

		public function export($search, $sort)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND ( p.partnercode LIKE '%$search%' OR p.partnername LIKE '%$search%' OR p.first_name LIKE '%$search%' OR p.last_name LIKE '%$search%' OR p.email LIKE '%$search%' ) " 	: 	"";

			$fields 	=	array("p.partnercode","p.partnername","p.first_name","p.last_name","p.address1","p.email","p.businesstype","p.tinno","p.terms","p.mobile","p.credit_limit");

			return $this->db->setTable('partners p')
							->setFields($fields)
							->setWhere(" p.partnertype = 'customer' AND p.stat = 'active' $add_cond ")
							->setOrderBy($sort)
							->runSelect()
							->getResult();
		}

		public function updateStat($data,$code)
		{
			$condition 			   = " partnercode = '$code' ";

			$result 			   = $this->db->setTable('partners')
												->setValues($data)
												->setWhere($condition)
												->setLimit(1)
												->runUpdate();

			return $result;
		}

	}
?>