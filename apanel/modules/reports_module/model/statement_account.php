<?php
	class statement_account extends wc_model
	{
		public function retrieveCustomerList()
		{
			$result = $this->db->setTable('partners')
						->setFields("partnercode ind, partnername val")
						->setWhere("partnercode != '' AND partnertype = 'customer' AND stat = 'active'")
						->setOrderBy("val")
						->runSelect()
						->getResult();
			return $result;
		}

		public function retrieveCustomerDetails($custfilter)
		{
			$fields = "address1, tinno, terms, email, partnername ,partnername AS name";
			$cond 	= "partnercode = '$custfilter'";

			$result = $this->db->setTable('partners')
								->setFields($fields)
								->setWhere($cond)
								->setLimit('1')
								->runSelect()
								->getRow();
			return $result;
		}
		
		public function retrieveListing($data)
		{

			$add_query1 		= "";
			$add_query2 		= "";
			$search_query 		= "";
			$fields 			= array('companycode, invoicedate, invoiceno, invoiceamount, particulars, transtype, stat, referenceno');
			$daterangefilter 	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
			$custfilter      	= isset($data['custfilter']) ? htmlentities($data['custfilter']) : ""; 	
			$searchkey 		 	= isset($data['search']) ? htmlentities($data['search']) : "";
			$filter 		 	= isset($data['filter']) ? htmlentities($data['filter']) : "";
			$sort 		 		= isset($data['sort']) ? htmlentities($data['sort']) : "";
			$datefilterArr		= explode(' - ',$daterangefilter);
			$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
			$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
			$search_query .= (!empty($searchkey) && $searchkey != 'none') ? " AND invoicedate LIKE '%$searchkey%' OR invoiceno LIKE '%$searchkey%' OR invoiceamount LIKE '%$searchkey%' OR particulars LIKE '%$searchkey%' OR referenceno LIKE '%$searchkey%' " : "";

			$add_query1 .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND ar.transactiondate BETWEEN '$datefilterFrom' AND  '$datefilterTo ' " : "";
			$add_query2 .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND rvm.transactiondate BETWEEN '$datefilterFrom' AND  '$datefilterTo ' " : "";
			$add_query1 .= (!empty($custfilter) && $custfilter != 'none') ? "AND ar.customer = '$custfilter' " : "";
			$add_query2 .= (!empty($custfilter) && $custfilter != 'none') ? "AND rvm.customer = '$custfilter' " : "";

			return $this->db->setTable("(
								select ar.companycode as companycode, ar.transactiondate as invoicedate,
								ar.sourceno as invoiceno, ar.amount as invoiceamount, ar.particulars as particulars,
								ar.transtype as transtype, ar.stat as stat, 
								'' AS referenceno 
								from accountsreceivable as ar  
								where ar.stat = 'posted'  $add_query1

								UNION ALL
 
								select rvm.companycode as companycode, rvm.transactiondate as invoicedate,
								ar.voucherno as invoiceno, rva.amount as invoiceamount, rvm.particulars as particulars, 
								rvm.transtype, rva.stat as stat, rvm.referenceno as referenceno 
								from rv_application as rva 
								left join receiptvoucher as rvm ON rvm.voucherno = rva.voucherno AND rvm.companycode = rva.companycode
 
								left join accountsreceivable as ar ON ar.voucherno = rva.arvoucherno AND ar.companycode = rva.companycode 
								where  rva.stat = 'posted'  $add_query2
							) as soa_details") 
							->setFields($fields)
							->setWhere("stat = 'posted' $search_query")
							->setOrderBy($sort)
							// ->setOrderBy("invoiceno ASC")
							->runPagination();
							// echo $this->db->getQuery();
		}

		public function fileExport($data)
		{

			$add_query1 		= "";
			$add_query2 		= "";
			$search_query 		= "";
			$fields 			= array('companycode, invoicedate, invoiceno, invoiceamount, particulars, transtype, stat, referenceno');
			$daterangefilter 	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
			$custfilter      	= isset($data['custfilter']) ? htmlentities($data['custfilter']) : ""; 	
			$searchkey 		 	= isset($data['search']) ? htmlentities($data['search']) : "";
			$filter 		 	= isset($data['filter']) ? htmlentities($data['filter']) : "";
			$datefilterArr		= explode(' - ',$daterangefilter);
			$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
			$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
			$search_query .= (!empty($searchkey) && $searchkey != 'none') ? " AND invoicedate LIKE '%$searchkey%' OR invoiceno LIKE '%$searchkey%' OR invoiceamount LIKE '%$searchkey%' OR particulars LIKE '%$searchkey%' OR referenceno LIKE '%$searchkey%' " : "";

			$add_query1 .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND ar.transactiondate BETWEEN '$datefilterFrom' AND  '$datefilterTo ' " : "";
			$add_query2 .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND rvm.transactiondate BETWEEN '$datefilterFrom' AND  '$datefilterTo ' " : "";
			$add_query1 .= (!empty($custfilter) && $custfilter != 'none') ? "AND ar.customer = '$custfilter' " : "";
			$add_query2 .= (!empty($custfilter) && $custfilter != 'none') ? "AND rvm.customer = '$custfilter' " : "";

			return $this->db->setTable("(
								select ar.companycode as companycode, ar.transactiondate as invoicedate,
								ar.voucherno as invoiceno, ar.amount as invoiceamount, ar.particulars as particulars,
								ar.transtype as transtype, ar.stat as stat, 
								'' AS referenceno 
								from accountsreceivable as ar  
								where ar.stat = 'posted'  $add_query1

								UNION ALL
 
								select rvm.companycode as companycode, rvm.transactiondate as invoicedate,
								ar.voucherno as invoiceno, rva.amount as invoiceamount, rvm.particulars as particulars, 
								rvm.transtype, rva.stat as stat, rvm.referenceno as referenceno 
								from rv_application as rva 
								left join receiptvoucher as rvm ON rvm.voucherno = rva.voucherno AND rvm.companycode = rva.companycode
 
								left join accountsreceivable as ar ON ar.voucherno = rva.arvoucherno AND ar.companycode = rva.companycode 
								where  rva.stat = 'posted'  $add_query2
							) as soa_details") 
							->setFields($fields)
							->setWhere("stat = 'posted' $search_query")
							->setOrderBy("invoiceno ASC")
							// ->runPagination();
							->runSelect()
							->getResult();
							echo $this->db->getQuery();
		}
	}
?>