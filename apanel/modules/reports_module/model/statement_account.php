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
								select ar.transactiondate invoicedate, ar.invoiceno invoiceno, 'Invoice' documenttype,
								ar.voucherno reference, ar.particulars as particulars, ar.convertedamount as amount,
								ar.companycode companycode, ar.entereddate entereddate
								from accountsreceivable as ar  
								where ar.stat = 'posted' $ar_query

								UNION ALL
 
								select rv.transactiondate as invoicedate, ar.sourceno as invoiceno, 'Payment' documenttype, 
								app.voucherno reference, rv.particulars as particulars, (app.convertedamount + app.discount - app.overpayment) as amount,
								rv.companycode companycode, rv.entereddate entereddate
								from rv_application as app 
								left join accountsreceivable ar ON ar.voucherno = app.arvoucherno
								left join receiptvoucher rv ON rv.voucherno = app.voucherno
								where  app.stat IN('open','posted') $rv_query

								UNION ALL
 
								select cm.transactiondate invoicedate, cm.sourceno invoiceno, 'Credit Memo' documenttype,
								cm.voucherno reference, cm.remarks as particulars, cm.convertedamount as amount,
								cm.companycode companycode, cm.entereddate entereddate
								from journalvoucher as cm  
								where cm.stat IN('open','posted') AND cm.transtype = 'CM' $cm_query

								UNION ALL
 
								select dm.transactiondate invoicedate, dm.sourceno invoiceno, 'Debit Memo' documenttype,
								dm.voucherno reference, dm.remarks as particulars, dm.convertedamount as amount,
								dm.companycode companycode, dm.entereddate entereddate
								from journalvoucher as dm  
								where dm.stat IN('open','posted') AND dm.transtype = 'DM' $dm_query

							) as soa_details") 
							->setFields($fields)
							->setOrderBy("entereddate, invoicedate, invoiceno ASC")
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
								select COALESCE(SUM(ar.convertedamount + ar.excessamount),0) amount, '0' payment, ar.companycode companycode
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
			
			$ar_query 			.= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND ar.transactiondate BETWEEN '$datefilterFrom' AND  '$datefilterTo ' " : "";
			$rv_query 			.= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND rv.transactiondate BETWEEN '$datefilterFrom' AND  '$datefilterTo ' " : "";
			$cm_query 			.= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND cm.transactiondate BETWEEN '$datefilterFrom' AND  '$datefilterTo ' " : "";
			$dm_query 			.= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND dm.transactiondate BETWEEN '$datefilterFrom' AND  '$datefilterTo ' " : "";
			$ar_query 			.= (!empty($custfilter) && $custfilter != 'none') ? "AND ar.customer = '$custfilter' " : "";
			$rv_query 			.= (!empty($custfilter) && $custfilter != 'none') ? "AND rv.customer = '$custfilter' " : "";
			$cm_query 			.= (!empty($custfilter) && $custfilter != 'none') ? "AND cm.partner = '$custfilter' " : "";
			$dm_query 			.= (!empty($custfilter) && $custfilter != 'none') ? "AND dm.partner = '$custfilter' " : "";

			return $this->db->setTable("(
								select ar.transactiondate invoicedate, ar.invoiceno invoiceno, 'Invoice' documenttype,
								ar.voucherno reference, ar.particulars as particulars, ar.convertedamount as amount,
								ar.companycode companycode, ar.entereddate entereddate
								from accountsreceivable as ar  
								where ar.stat = 'posted' $ar_query

								UNION ALL
 
								select rv.transactiondate as invoicedate, ar.sourceno as invoiceno, 'Payment' documenttype, 
								app.voucherno reference, rv.particulars as particulars, (app.convertedamount + app.discount - app.overpayment) as amount,
								rv.companycode companycode, rv.entereddate entereddate
								from rv_application as app 
								left join accountsreceivable ar ON ar.voucherno = app.arvoucherno
								left join receiptvoucher rv ON rv.voucherno = app.voucherno
								where  app.stat IN('open','posted') $rv_query

								UNION ALL
 
								select cm.transactiondate invoicedate, cm.sourceno invoiceno, 'Credit Memo' documenttype,
								cm.voucherno reference, cm.remarks as particulars, cm.convertedamount as amount,
								cm.companycode companycode, cm.entereddate entereddate
								from journalvoucher as cm  
								where cm.stat IN('open','posted') AND cm.transtype = 'CM' $cm_query

								UNION ALL
 
								select dm.transactiondate invoicedate, dm.sourceno invoiceno, 'Debit Memo' documenttype,
								dm.voucherno reference, dm.remarks as particulars, dm.convertedamount as amount,
								dm.companycode companycode, dm.entereddate entereddate
								from journalvoucher as dm  
								where dm.stat IN('open','posted') AND dm.transtype = 'DM' $dm_query

							) as soa_details") 
							->setFields($fields)
							->setOrderBy("entereddate, invoicedate, invoiceno ASC")
							->runSelect()
							->getResult();
		}

		public function getInvoice($voucherno)
		{
			$result = $this->db->setTable("accountsreceivable")
						->setFields("invoiceno")
						->setWhere(" voucherno = '$voucherno' ")
						->setLimit('1')
						->runSelect()
						->getRow();

			return $result;
		}
	}
?>