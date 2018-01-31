<?php
	class supplier extends wc_model
	{
		public function retrieveListing($search, $sort)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND ( p.partnercode LIKE '%$search%' OR p.partnername LIKE '%$search%' OR p.first_name LIKE '%$search%' OR p.last_name LIKE '%$search%' OR p.email LIKE '%$search%' ) " 	: 	"";


			$fields 	=	array("p.partnercode","p.partnername","CONCAT(p.first_name,' ', p.last_name) as contact_person", "p.email","p.stat");

			return $this->db->setTable('partners p')
							->setFields($fields)
							->setWhere(" p.partnertype = 'supplier' AND p.stat = 'active' $add_cond ")
							->setOrderBy($sort)
							->runPagination();
		}

		public function retrieveExistingSupplier($data, $partnercode)
		{
			$condition 		=	" partnertype = 'supplier' AND stat = 'active'  AND partnercode = '$partnercode' ";

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

		public function insertSupplier($data)
		{
			$data["stat"]     	   = "active";
			$data['partnertype']   = "supplier";

			$result = $this->db->setTable('partners')
					->setValues($data)
					->runInsert();

			return $result;
		}

		public function updateSupplier($data, $partnercode)
		{
			$data["stat"]     	   = "active";

			$condition 			   = " partnercode = '$partnercode' AND partnertype = 'supplier' ";

			$result = $this->db->setTable('partners')
								->setValues($data)
								->setWhere($condition)
								->setLimit(1)
								->runUpdate();

			return $result;
		}

		public function deleteSupplier($id)
		{
			$condition   = "";
			$id_array 	 = explode(',', $id['id']);
			$errmsg 	 = array();

			for($i = 0; $i < count($id_array); $i++)
			{
				$partnercode 	= $id_array[$i];

				$condition 		= " partnercode = '$partnercode' AND partnertype = 'supplier' ";
					
				$result 		= $this->db->setTable('partners')
											->setWhere($condition)
											->runDelete();

				$error = $this->db->getError();

				if ($error == 'locked') {
					$errmsg[]  = "<p class = 'no-margin'>Canbcelling Supplier: $partnercode</p>";
				}

			}
			
			return $errmsg;
		}

		public function searchSupplier($data, $search)
		{
			$condition = "(partnercode LIKE '%$search%' OR first_name LIKE '%$search%' OR last_name = '%$search%' )";
			
			$list = $this->db->setTable('partners')
						->setFields($data)
						->setWhere($condition)
						->runSelect()
						->getResult();
			
			return $list;
		}

		public function check_duplicate($code)
		{
			return $this->db->setTable('partners')
							->setFields('COUNT(partnercode) count')
							->setWhere(" partnercode = '$code'  AND partnertype = 'supplier' ")
							->runSelect()
							->getResult();
		}

		public function export($search, $sort)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND ( p.partnercode LIKE '%$search%' OR p.partnername LIKE '%$search%' OR p.first_name LIKE '%$search%' OR p.last_name LIKE '%$search%' OR p.email LIKE '%$search%' ) " 	: 	"";

			$fields 	=	array("p.partnercode","p.partnername","p.first_name","p.last_name","p.address1","p.email","p.businesstype","p.tinno","p.terms","p.mobile");

			return $this->db->setTable('partners p')
							->setFields($fields)
							->setWhere(" p.partnertype = 'supplier' AND p.stat = 'active' $add_cond ")
							->setOrderBy($sort)
							->runSelect()
							->getResult();
		}	

		public function importsupplier($data)
		{

			$data["stat"]     	   = "active";
			$data['partnertype']   = "supplier";
			//var_dump($data);

			$result = $this->db->setTable('partners')
					->setValuesFromPost($data)
					->runInsert();

			return $result;
		}

	}
?>