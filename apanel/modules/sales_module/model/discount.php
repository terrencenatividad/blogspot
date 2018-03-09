<?php
	class discount extends wc_model
	{
		public function retrieveDefaultDiscountTableList()
		{
			return $this->db->setTable('wc_option')
						->setFields('code ind, value val')
						->setWhere(" type = 'discount_template' ")
						->runSelect(false)
						->getResult();
		}

		public function retrieveDiscountListing($search, $sort)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND (discountcode LIKE '%$search%' OR discountname LIKE '%$search%') " 	: 	"";

			$fields 	=	array("discountcode, discountname, discountdesc");

			return $this->db->setTable('discount')
							->setFields($fields)
							->setWhere(" stat = 'active' $add_cond ")
							->setOrderBy($sort)
							->runPagination();
		}

		public function retrieveExistingDiscount($data, $discountcode)
		{
			$condition 		=	" discountcode = '$discountcode' ";
			
			return $this->db->setTable('discount')
							->setFields($data)
							->setWhere($condition)
							->runSelect()
							->getRow();
		}

		public function retrieveTaggedCustomers($discountcode)
		{
			$condition 		=	" discountcode = '$discountcode' ";
			
			return $this->db->setTable('customer_discount')
							->setFields(array('customercode'))
							->setWhere($condition)
							->runSelect()
							->getRow();
		}

		public function retrieveCustomerListing($discountcode, $tagged, $search)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND (p.partnercode LIKE '%$search%' OR p.partnername LIKE '%$search%') " 	: 	"";
			// $add_cond 	.=	( !empty($tagged) )  	? 	" AND (p.partnercode NOT IN ( '" . implode("','",$tagged). "') ) "  	: 	"";
			
			$result =  $this->db->setTable('partners p')
								->setFields(array('p.partnercode as partnercode',"p.partnername as partnername", 'cd.customercode as tagged'))
								->leftJoin("customer_discount cd ON cd.customercode = p.partnercode AND cd.companycode = p.companycode ")
								->setWhere(" p.partnertype = 'customer' AND p.stat = 'active' AND (cd.discountcode = '$discountcode' OR cd.discountcode IS NULL) $add_cond")
								->setOrderBy('cd.customerCode DESC, p.partnercode ASC')
								->runPagination();
			//echo $this->db->getQuery();
			return $result;
		}

		public function retrieveTagged($discountcode)
		{
			$result =  $this->db->setTable('partners p')
								->setFields(array('cd.customercode as tagged'))
								->leftJoin("customer_discount cd ON cd.customercode = p.partnercode AND cd.companycode = p.companycode ")
								->setWhere(" p.partnertype = 'customer' AND p.stat = 'active' AND (cd.discountcode = '$discountcode' OR cd.discountcode IS NULL)")
								->runSelect()
								->getResult();
			return $result;
		}

		public function retrieveSelectedCustomerListing($customers, $search)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND (p.partnercode LIKE '%$search%' OR p.partnername LIKE '%$search%') " 	: 	"";
			$imploded_values 	=	(!empty($customers)) 	?	implode("','",$customers) 	: 	"";
			$add_cond 	.=	" AND (p.partnercode IN ( '" . $imploded_values. "') ) " ;
			
			$result =  $this->db->setTable('partners p')
								->setFields(array('p.partnercode as partnercode',"p.partnername as partnername", 'cd.customercode as tagged'))
								->leftJoin("customer_discount cd ON cd.customercode = p.partnercode AND cd.companycode = p.companycode ")
								->setWhere(" p.partnertype = 'customer' AND p.stat = 'active' $add_cond")
								->runPagination();
			//echo $this->db->getQuery();
			return $result;
		}

		public function insertDiscount($data)
		{
			//Does not take input from Disabled Input ; Hardcoded for now. 
			$data['discounttype']  = "percentage";

			$result = $this->db->setTable('discount')
					->setValues($data)
					->runInsert();
		
			return $result;
		}

		public function updateDiscount($data, $discountcode)
		{
			//Does not take input from Disabled Input ; Hardcoded for now. 
			$data['discounttype']  = "percentage";

			$condition 			   = " discountcode = '$discountcode'";

			$result 			   = $this->db->setTable('discount')
											  ->setValues($data)
											  ->setWhere($condition)
											  ->setLimit(1)
											  ->runUpdate();

			return $result;
		}

		public function tagCustomer($discountcode, $tagged)
		{
			$result 	=	0;

			$this->db->setTable('customer_discount')
				->setWhere("discountcode = '$discountcode'")
				->runDelete();
			
			$tagged_array 	=	explode(',',$tagged);

			if(!empty($tagged_array))
			{
				foreach( $tagged_array as $key => $val )
				{
					$insert_tag['discountcode'] 	= 	$discountcode;
					$insert_tag['customercode'] 	=	$val;

					$result = $this->db->setTable('customer_discount')
						->setValues($insert_tag)
						->runInsert();
				}
			}

			return $result;

		}

		public function deleteDiscount($id)
		{
			$condition   = "";
			$id_array 	 = explode(',', $id['id']);
			$errmsg 	 = array();

			for($i = 0; $i < count($id_array); $i++)
			{
				$discountcode 	=	$id_array[$i];

				$exists 		= $this->retrieveTaggedCustomers($discountcode);

				if( !empty($exists) && count($exists) > 0){
					$errmsg[] = "<p class = 'no-margin'>Deleting Discount: $discountcode Failed. This Template is already being used.</p>";
				}
				else{
					$condition 		= " discountcode = '$discountcode' ";
					
					$result 		= $this->db->setTable('discount')
											//->innerJoin('customer_discount c ON c.discountcode = d.discountcode AND c.companycode = d.companycode ')
											->setWhere($condition)
											->runDelete();

					$error 			= $this->db->getError();

					if ($error == 'locked') {
						$errmsg[]  = "<p class = 'no-margin'>Deleting Discount: $discountcode</p>";
					}
				}
			
			}
			
			return $errmsg;
		}
		
		public function check_duplicate($current, $table, $column)
		{
			return $this->db->setTable($table)
							->setFields('COUNT('.$column.') count')
							->setWhere($column." = '$current'")
							->runSelect()
							->getResult();
		}

		public function check_duplicate_pair($customercode, $discountcode)
		{
			$result = $this->db->setTable('customer_discount')
							->setFields("COUNT(*) count")
							->setWhere(" discountcode = '$discountcode' AND customercode = '$customercode' ")
							->runSelect()
							->getResult();

			return $result;
			
		}

		public function importDiscounts($data)
		{

			$data["discounttype"]  = "percentage";
			$data["stat"] 		   = "active";

			$result = $this->db->setTable('discount')
					->setValuesFromPost($data)
					->runInsert();

			return $result;
		}

		public function importPair($data)
		{
			$result = $this->db->setTable('customer_discount')
					->setValuesFromPost($data)
					->runInsert();

			return $result;
		}

		public function export($search, $sort)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND ( d.discountcode LIKE '%$search%' OR d.discountname LIKE '%$search%' ) " 	: 	"";
				
			$fields 	=	array("d.discountcode","d.discountname","d.discountdesc","d.disc_1","d.disc_2","d.disc_3","d.disc_4");

			$result 	=	 $this->db->setTable('discount d')
							// ->leftJoin('customer_discount cd ON cd.discountcode = d.discountcode AND d.companycode = cd.companycode ')
							->setFields($fields)
							->setWhere(" d.discounttype = 'percentage' AND d.stat = 'active' $add_cond ")
							->setOrderBy($sort)
							->runSelect()
							->getResult();
							//echo $this->db->getQuery();
			return $result;
		}
	}
?>