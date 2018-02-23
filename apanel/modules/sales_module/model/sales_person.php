<?php
	class sales_person extends wc_model
	{
		public function retrieveListing($search, $sort)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND ( p.partnercode LIKE '%$search%' OR p.partnername LIKE '%$search%' OR p.first_name LIKE '%$search%' OR p.last_name LIKE '%$search%' OR p.email LIKE '%$search%' ) " 	: 	"";


			$fields 	=	array("p.partnercode","CONCAT(p.first_name,' ', p.last_name) as contact_person", "p.email","p.stat");

			return $this->db->setTable('partners p')
							->setFields($fields)
							->setWhere(" p.partnertype = 'sales' AND p.stat = 'active' $add_cond ")
							->setOrderBy($sort)
							->runPagination();
							
		}

		public function retrieveExistingSalesPerson($data, $partnercode)
		{
			$condition 		=	" partnertype = 'sales' AND stat = 'active'  AND partnercode = '$partnercode' ";

			return $this->db->setTable('partners')
							->setFields($data)
							->setWhere($condition)
							->runSelect()
							->getRow();
		}

		public function retrieveCustomerListing($salespersoncode)
		{
			$result =  $this->db->setTable('partners p')
								->setFields(array('p.partnercode as partnercode',"p.partnername as partnername", 'cd.customercode as tagged'))
								->leftJoin("sales_customer cd ON cd.customercode = p.partnercode AND cd.companycode = p.companycode ")
								->setWhere(" p.partnertype = 'customer' AND p.stat = 'active' AND (cd.salespersoncode = '$salespersoncode' OR cd.salespersoncode IS NULL)")
								->runSelect()
								->getResult();
			//echo $this->db->getQuery();
			return $result;
		}

		public function retrieveBusinessTypeDropdown()
		{
			return $this->db->setTable('wc_option')
							->setFields('code ind, value val')
							->setWhere(" type = 'businesstype' ")
							->runSelect(false)
							->getResult();
		}

		public function insertSalesPerson($data)
		{
			$data["stat"]     	   = "active";
			$data['partnertype']   = "sales";

			$result = $this->db->setTable('partners')
					->setValues($data)
					->runInsert();

			return $result;
		}

		public function updateSalesPerson($data, $partnercode)
		{
			$data["stat"]     	   = "active";

			$condition 			   = " partnercode = '$partnercode' AND partnertype = 'sales' ";

			$result = $this->db->setTable('partners')
								->setValues($data)
								->setWhere($condition)
								->setLimit(1)
								->runUpdate();

			return $result;
		}

		public function deleteSalesPerson($id)
		{
			$condition   = "";
			$id_array 	 = explode(',', $id['id']);
			$errmsg 	 = array();

			for($i = 0; $i < count($id_array); $i++)
			{
				// $pieces 		= explode('/',$id_array[$i]);
				// $partnercode    = $pieces[0];
				// $companycode 	= $pieces[1];

				$partnercode 	= $id_array[$i];

				$condition 		= " partnercode = '$partnercode' AND partnertype = 'sales' ";
					
				$result 		= $this->db->setTable('partners')
											->setWhere($condition)
											->runDelete();

				$error 			= $this->db->getError();

				if ($error == 'locked') {
					$errmsg[]  = "<p class = 'no-margin'>Deleting Sales Person: $partnercode</p>";
				}

			}
			
			return $errmsg;
		}

		public function searchSalesPerson($data, $search)
		{
			$condition = "(partnercode LIKE '%$search%' OR first_name LIKE '%$search%' OR last_name = '%$search%' ) AND partnertype = 'sales' ";
			
			$list = $this->db->setTable('partners')
						->setFields($data)
						->setWhere($condition)
						->runSelect()
						->getResult();
			
			return $list;
		}

		public function tagCustomer($sales_code, $tagged)
		{
			$result 	=	0;

			$this->db->setTable('sales_customer')
				->setWhere("salespersoncode = '$sales_code'")
				->runDelete();
			
			if(!empty($tagged))
			{
				foreach( $tagged as $key => $val )
				{
					$insert_tag['salespersoncode'] 	= 	$sales_code;
					$insert_tag['customercode'] 	=	$val;

					$result = $this->db->setTable('sales_customer')
						->setValues($insert_tag)
						->runInsert();
				}
			}

			return $result;

		}

		public function check_duplicate($code)
		{
			return $this->db->setTable('partners')
							->setFields('COUNT(partnercode) count')
							->setWhere(" partnercode = '$code'")
							->runSelect()
							->getResult();
		}

		public function export($search, $sort)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND ( p.partnercode LIKE '%$search%' OR p.partnername LIKE '%$search%' OR p.first_name LIKE '%$search%' OR p.last_name LIKE '%$search%' OR p.email LIKE '%$search%' ) " 	: 	"";


			$fields 	=	array("p.partnercode","p.first_name","p.last_name","p.address1","p.email","p.businesstype","p.tinno","p.terms","p.mobile");

			return $this->db->setTable('partners p')
							->setFields($fields)
							->setWhere(" p.partnertype = 'sales' AND p.stat = 'active' $add_cond ")
							->setOrderBy($sort)
							->runSelect()
							->getResult();
		}

		public function importSalesPerson($data)
		{

			$data["stat"]     	   = "active";
			$data['partnertype']   = "sales";
			//var_dump($data);

			$result = $this->db->setTable('partners')
					->setValuesFromPost($data)
					->runInsert();

			return $result;
		}
	}
?>