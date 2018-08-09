<?php
	class statement_account extends wc_model
	{
		public function retrieveCustomerList()
		{
			$result = $this->db->setTable('partners p')
							->setFields("p.partnercode ind, p.partnername val")
							->leftJoin("accountsreceivable ar ON p.partnercode = ar.customer")
							->setWhere("p.partnercode != '' AND p.partnertype = 'customer' AND p.stat = 'active' AND ar.stat = 'posted'")
							->setGroupBy("val")
							->setOrderBy("val")
							->runSelect()
							->getResult();
			return $result;
		}

		public function retrieveCustomerDetails($custfilter)
		{
			$fields = " address1, tinno, terms, email, partnername ,partnername AS name ";
			$cond 	= " partnercode = '$custfilter' AND partnertype = 'customer' ";

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
			$ar_query 			= "";
			$rv_query 			= "";
			$cm_query 			= "";
			$dm_query 			= "";

			$fields 			= array('invoicedate, invoiceno, documenttype, reference, particulars, amount');
			$daterangefilter 	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
			$custfilter      	= isset($data['custfilter']) ? htmlentities($data['custfilter']) : ""; 	
			$datefilterArr		= explode(' - ',$daterangefilter);
			$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
			$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
			
			$ar_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND ar.transactiondate BETWEEN '$datefilterFrom' AND  '$datefilterTo ' " : "";
			$rv_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND rv.transactiondate BETWEEN '$datefilterFrom' AND  '$datefilterTo ' " : "";
			$cm_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND cm.transactiondate BETWEEN '$datefilterFrom' AND  '$datefilterTo ' " : "";
			$dm_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND dm.transactiondate BETWEEN '$datefilterFrom' AND  '$datefilterTo ' " : "";
			$ar_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND ar.customer = '$custfilter' " : "";
			$rv_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND rv.customer = '$custfilter' " : "";
			$cm_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND cm.partner = '$custfilter' " : "";
			$dm_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND dm.partner = '$custfilter' " : "";

			return $this->db->setTable("(
								select ar.transactiondate invoicedate, ar.sourceno invoiceno, 'Invoice' documenttype,
								ar.voucherno reference, ar.particulars as particulars, ar.convertedamount as amount,
								ar.companycode companycode
								from accountsreceivable as ar  
								where ar.stat = 'posted' $ar_query

								UNION ALL
 
								select rv.transactiondate as invoicedate, ar.sourceno as invoiceno, 'Payment' documenttype, 
								app.voucherno reference, rv.particulars as particulars, (app.convertedamount + app.discount) as amount,
								rv.companycode companycode
								from rv_application as app 
								left join accountsreceivable ar ON ar.voucherno = app.arvoucherno
								left join receiptvoucher rv ON rv.voucherno = app.voucherno
								where  app.stat IN('open','posted') $rv_query

								UNION ALL
 
								select cm.transactiondate invoicedate, cm.sourceno invoiceno, 'Credit Memo' documenttype,
								cm.voucherno reference, cm.particulars as particulars, cm.convertedamount as amount,
								cm.companycode companycode
								from journalvoucher as cm  
								where cm.stat IN('open','posted') AND cm.transtype = 'CM' $cm_query

								UNION ALL
 
								select dm.transactiondate invoicedate, dm.sourceno invoiceno, 'Debit Memo' documenttype,
								dm.voucherno reference, dm.particulars as particulars, dm.convertedamount as amount,
								dm.companycode companycode
								from journalvoucher as dm  
								where dm.stat IN('open','posted') AND dm.transtype = 'DM' $dm_query

							) as soa_details") 
							->setFields($fields)
							->setOrderBy("invoicedate, invoiceno ASC")
							->runPagination();
		}

		public function getPreviousBalance($data)
		{
			$ar_query 			= "";
			$rv_query 			= "";
			$cm_query 			= "";
			$dm_query 			= "";

			$fields 			= array('SUM(amount) amount, SUM(payment) payment');
			$daterangefilter 	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
			$custfilter      	= isset($data['custfilter']) ? htmlentities($data['custfilter']) : ""; 	
			$datefilterArr		= explode(' - ',$daterangefilter);
			$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
			
			$ar_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND ar.transactiondate < '$datefilterFrom' " : "";
			$rv_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND rv.transactiondate < '$datefilterFrom' " : "";
			$cm_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND cm.transactiondate < '$datefilterFrom' " : "";
			$dm_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND dm.transactiondate < '$datefilterFrom' " : "";
			$ar_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND ar.customer = '$custfilter' " : "";
			$rv_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND rv.customer = '$custfilter' " : "";
			$cm_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND cm.partner = '$custfilter' " : "";
			$dm_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND dm.partner = '$custfilter' " : "";

			return $this->db->setTable("(
								select COALESCE(SUM(ar.convertedamount),0) amount, '0' payment, ar.companycode companycode
								from accountsreceivable as ar  
								where ar.stat = 'posted' $ar_query

								UNION ALL
 
								select '0' amount, COALESCE(SUM((app.convertedamount + app.discount)),0) payment,
								rv.companycode companycode
								from rv_application as app 
								left join accountsreceivable ar ON ar.voucherno = app.arvoucherno
								left join receiptvoucher rv ON rv.voucherno = app.voucherno
								where  app.stat IN('open','posted') $rv_query
								
								UNION ALL
 
								select '0' amount, COALESCE(SUM(cm.convertedamount),0) payment, cm.companycode companycode
								from journalvoucher as cm  
								where cm.stat IN('open','posted') AND cm.transtype = 'CM' $cm_query

								UNION ALL
 
								select COALESCE(SUM(dm.convertedamount),0) amount, '0' payment, dm.companycode companycode
								from journalvoucher as dm  
								where dm.stat IN('open','posted') AND dm.transtype = 'DM' $dm_query

							) as soa_details") 
							->setFields($fields)
							->runSelect()
							->getResult();
		}

		public function getGrandBalance($data)
		{
			$ar_query 			= "";
			$rv_query 			= "";
			$cm_query 			= "";
			$dm_query 			= "";

			$fields 			= array('SUM(amount) amount, SUM(payment) payment');
			$daterangefilter 	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
			$custfilter      	= isset($data['custfilter']) ? htmlentities($data['custfilter']) : ""; 	
			$datefilterArr		= explode(' - ',$daterangefilter);
			$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
			$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
			
			$ar_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND ar.transactiondate BETWEEN '$datefilterFrom' AND  '$datefilterTo ' " : "";
			$rv_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND rv.transactiondate BETWEEN '$datefilterFrom' AND  '$datefilterTo ' " : "";
			$cm_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND cm.transactiondate BETWEEN '$datefilterFrom' AND  '$datefilterTo ' " : "";
			$dm_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND dm.transactiondate BETWEEN '$datefilterFrom' AND  '$datefilterTo ' " : "";
			$ar_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND ar.customer = '$custfilter' " : "";
			$rv_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND rv.customer = '$custfilter' " : "";
			$cm_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND cm.partner = '$custfilter' " : "";
			$dm_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND dm.partner = '$custfilter' " : "";

			return $this->db->setTable("(
								select COALESCE(SUM(ar.convertedamount),0) amount, '0' payment, ar.companycode companycode
								from accountsreceivable as ar  
								where ar.stat = 'posted' $ar_query

								UNION ALL
 
								select '0' amount, COALESCE(SUM((app.convertedamount + app.discount)),0) payment,
								rv.companycode companycode
								from rv_application as app 
								left join accountsreceivable ar ON ar.voucherno = app.arvoucherno
								left join receiptvoucher rv ON rv.voucherno = app.voucherno
								where  app.stat IN('open','posted') $rv_query
								
								UNION ALL
 
								select '0' amount, COALESCE(SUM(cm.convertedamount),0) payment, cm.companycode companycode
								from journalvoucher as cm  
								where cm.stat IN('open','posted') AND cm.transtype = 'CM' $cm_query

								UNION ALL
 
								select COALESCE(SUM(dm.convertedamount),0) amount, '0' payment, dm.companycode companycode
								from journalvoucher as dm  
								where dm.stat IN('open','posted') AND dm.transtype = 'DM' $dm_query

							) as soa_details") 
							->setFields($fields)
							->runSelect()
							->getResult();
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
							->runSelect()
							->getResult();
							echo $this->db->getQuery();
		}
	}
?>