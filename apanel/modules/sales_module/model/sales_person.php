<?php
	class sales_person extends wc_model
	{
		public function retrieveListing($search, $sort)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND ( p.partnercode LIKE '%$search%' OR p.partnername LIKE '%$search%' OR p.first_name LIKE '%$search%' OR p.last_name LIKE '%$search%' OR p.email LIKE '%$search%' ) " 	: 	"";


			$fields 	=	array("p.partnercode","CONCAT(p.first_name,' ', p.last_name) as contact_person", "p.email","p.stat");

			return $this->db->setTable('partners p')
							->setFields($fields)
							->setWhere(" p.partnertype = 'sales' AND p.stat != 'deleted' $add_cond ")
							->setOrderBy($sort)
							->runPagination();
		}

		public function retrieveOtherSalesPersonListing($current)
		{
			$fields 	=	array("p.partnercode","CONCAT(p.first_name,' ', p.last_name) as name");

			return $this->db->setTable('partners p')
							->setFields($fields)
							->setWhere(" p.partnertype = 'sales' AND p.partnercode != '$current' AND p.stat = 'active' ")
							->runSelect()
							->getResult();
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

		public function retrieveCustomerListing($salespersoncode, $search)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND (p.partnercode LIKE '%$search%' OR p.partnername LIKE '%$search%') " 	: 	"";
			
			$result =  $this->db->setTable('partners p')
								->setFields(array('p.partnercode as partnercode',"p.partnername as partnername", 'cd.customercode as tagged'))
								->leftJoin("sales_customer cd ON cd.customercode = p.partnercode AND cd.companycode = p.companycode ")
								->setWhere(" p.partnertype = 'customer' AND p.stat = 'active' AND (cd.salespersoncode = '$salespersoncode' OR cd.salespersoncode IS NULL) $add_cond")
								->setOrderBy('cd.customerCode DESC, p.partnercode ASC')
								->runPagination();

			return $result;
		}

		public function retrieveTagged($salespersoncode)
		{
			$result =  $this->db->setTable('partners p')
								->setFields(array('cd.customercode as tagged'))
								->leftJoin("sales_customer cd ON cd.customercode = p.partnercode AND cd.companycode = p.companycode ")
								->setWhere(" p.partnertype = 'customer' AND p.stat = 'active' AND (cd.salespersoncode = '$salespersoncode' OR cd.salespersoncode IS NULL)")
								->runSelect()
								->getResult();
			return $result;
		}

		public function retrieveLinkedCustomerListing($salespersoncode)
		{
			$result =  $this->db->setTable('sales_customer cd')
								->setFields(array('p.partnercode as partnercode',"CONCAT(p.first_name,' ',p.last_name) as partnername"))
								->leftJoin("partners p ON cd.customercode = p.partnercode AND cd.salespersoncode = '$salespersoncode' AND cd.companycode = p.companycode ")
								->setWhere(" p.partnertype = 'customer' AND p.stat = 'active'")
								->runPagination();
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

		public function transferCustomers($current, $new)
		{
			
			$update_data["salespersoncode"]   = $new;

			$condition 		= " salespersoncode = '$current'";

			$update 		= $this->db->setTable('sales_customer')
										->setValues($update_data)
										->setWhere($condition)
										->runUpdate();
			if( $update )
			{
				$data["previous_sp"]  = $current;
				$data['current_sp']   = $new;

				$result = $this->db->setTable('sp_transfer_history')
						->setValues($data)
						->runInsert();
				return $result;
			}
			
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

		public function retrieveTaggedCustomers($spcode)
		{
			$condition 		=	" salespersoncode = '$spcode' ";
			
			return $this->db->setTable('sales_customer')
							->setFields(array('salespersoncode'))
							->setWhere($condition)
							->runSelect()
							->getRow();
		}

		public function deleteSalesPerson($id)
		{
			$condition   = "";
			$id_array 	 = explode(',', $id['id']);
			$errmsg 	 = array();

			for($i = 0; $i < count($id_array); $i++)
			{
				$salespersoncode 	=	$id_array[$i];

				$exists 		= $this->retrieveTaggedCustomers($salespersoncode);

				if( !empty($exists) && count($exists) > 0){
					$errmsg[] = "<p class = 'no-margin'>Unable to Delete Sales Person: Sales Person in use.</p>";
				}
				else{
					$condition 		= " partnercode = '$salespersoncode' AND partnertype = 'sales' ";
					
					$result 		= $this->db->setTable('partners')
											//->innerJoin('customer_discount c ON c.discountcode = d.discountcode AND c.companycode = d.companycode ')
											->setWhere($condition)
											->runDelete();

					$error 			= $this->db->getError();

					if ($error == 'locked') {
						$errmsg[]  = "<p class = 'no-margin'>Deleting Sales Person : $salespersoncode</p>";
					}
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
			
			$tagged_array 	=	explode(',',$tagged);

			if(!empty($tagged_array))
			{
				foreach( $tagged_array as $key => $val )
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

		public function getValue($table, $cols = array(), $cond, $orderby = "", $bool = "")
		{
			$result = $this->db->setTable($table)
						->setFields($cols)
						->setWhere($cond)
						->setOrderBy($orderby)
						->runSelect($bool)
						->getResult();

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

		public function check_custom_duplicate($current, $table, $column)
		{
			return $this->db->setTable($table)
							->setFields('COUNT('.$column.') count')
							->setWhere($column." = '$current'")
							->runSelect()
							->getResult();
		}

		public function check_duplicate_pair($customercode, $spcode)
		{
			$result = $this->db->setTable('sales_customer')
							->setFields("COUNT(*) count")
							->setWhere(" salespersoncode = '$spcode' AND customercode = '$customercode' ")
							->runSelect()
							->getResult();

			return $result;
			
		}

		public function importPair($data)
		{
			$result = $this->db->setTable('sales_customer')
					->setValuesFromPost($data)
					->runInsert();

			return $result;
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