<?php
class receipt_voucher_model extends wc_model
{
	//
	public function retrieveCustomerList()
	{
		$result = $this->db->setTable('partners')
					->setFields("partnercode ind, companycode, partnername val")
					->setWhere("partnercode != '' AND partnertype = 'customer' AND stat = 'active'")
					->setOrderBy("val")
					->runSelect()
					->getResult();
		
		return $result;
	}
	//
	public function retrieveProformaList()
	{
		$result = $this->db->setTable('proforma')
					->setFields("proformacode ind, proformadesc val")
					->setOrderBy("val")
					->runSelect()
					->getResult();
		
		return $result;
	}
	//
	public function retrieveData($table, $fields = array(), $cond = "", $join = "", $orderby = "", $groupby = "")
	{
	
		$result = $this->db->setTable($table)
					->setFields($fields)
					->leftJoin($join)
					->setGroupBy($groupby)
					->setWhere($cond)
					->setOrderBy($orderby)
					->runSelect()
					->getResult();
		
		//var_dump($this->db->getQuery());

		return $result;
	}
	//
	public function retrieveEditData($sid)
	{
		$setFields = "voucherno, transactiondate, customer, referenceno, particulars, 
		netamount, exchangerate, convertedamount, paymenttype";
		$cond = "voucherno = '$sid'";
		
		$temp = array();

		// Retrieve Header
		$retrieveArrayMain =  $this->db->setTable('receiptvoucher')
									->setFields($setFields)
									->setWhere($cond)
									->setLimit('1')
									->runSelect()
									->getRow();

		$temp["main"] = $retrieveArrayMain;

		// Retrieve Customer Details
		$customer_code = $temp["main"]->customer;
		$custFields = "address1, tinno, terms, email, partnername AS name";
		$cust_cond = "partnercode = '$customer_code'";

		$custDetails = $this->db->setTable('partners')
							->setFields($custFields)
							->setWhere($cust_cond)
							->setLimit('1')
							->runSelect()
							->getRow();
		
		$temp["cust"] = $custDetails;

		// Retrieve Details
		$detailFields = "main.accountcode, chart.accountname, main.detailparticulars, main.debit, 
						SUM(main.credit) credit";
		$detail_cond  = "main.voucherno = '$sid' AND main.stat != 'temporary'";
		$orderby 	  = "main.linenum";	
		$detailJoin   = "chartaccount as chart ON chart.id = main.accountcode 
						AND chart.companycode = main.companycode";
		$groupby      = "main.accountcode";

		$retrieveArrayDetail = $this->db->setTable('rv_details as main')
									->setFields($detailFields)
									->leftJoin($detailJoin)
									->setWhere($detail_cond)
									->setGroupBy($groupby)
									->setOrderBy($orderby)
									->runSelect()
									->getResult();
									// ->buildSelect();
		
		// var_dump($retrieveArrayDetail);

		$temp["details"] = $retrieveArrayDetail;

		// Retrieve Payments
		
		$applicationFields = "app.voucherno,main.transactiondate,detail.accountcode,
			chart.accountname,main.wtaxcode,ftax.shortname,main.paymenttype,main.referenceno,
			app.amount,app.stat,main.checkdate,main.atcCode,main.particulars,detail.checkstat,
			app.discount,app.exchangerate,SUM(app.convertedamount) convertedamount, app.arvoucherno, 
			SUM(detail.credit) sum";

		$appJoin_rv  = "receiptvoucher as main ON main.voucherno = app.voucherno 
						AND main.companycode = app.companycode";
		$appJoin_rvd = "rv_details as detail ON detail.arvoucherno = app.arvoucherno 
						AND detail.companycode = app.companycode";
		$appJoin_ca  = "chartaccount as chart ON chart.id = detail.accountcode 
						AND chart.companycode = detail.companycode";
		$appJoin_fin = "fintaxcode as ftax ON ftax.fstaxcode = main.wtaxcode 
						AND ftax.companycode = main.companycode";

		$app_cond 	 = "app.voucherno = '$sid' AND app.stat != 'cancelled' AND detail.credit != 0";

		$applicationArray = $this->db->setTable('rv_application as app')
									->setFields($applicationFields)
									->leftJoin($appJoin_rv)
									->leftJoin($appJoin_rvd)
									->leftJoin($appJoin_ca)
									->leftJoin($appJoin_fin)
									->setWhere($app_cond)
									->runSelect()
									->getResult();
									// ->buildSelect();
		
		// var_dump($applicationArray);
		
		$temp["payments"] = $applicationArray;

		// Received Cheques
		$sub_select = $this->db->setTable("rv_application AS app")
							   ->setFields("app.voucherno")
							   ->setWhere("app.voucherno = '$sid'")
							   ->buildSelect();

		$chequeFields = 'voucherno, chequeaccount, chequenumber, chequedate, chequeamount, 
						chequeconvertedamount';
		$cheque_cond  = "voucherno IN($sub_select)";

		$chequeArray  = $this->db->setTable('rv_cheques')
									->setFields($chequeFields)
									->setWhere($cheque_cond)
									// ->buildSelect();
									->runSelect()
									->getResult();
		
		// var_dump($chequeArray);

		$rollArray	  = array();
	
		if(!empty($chequeArray))
		{
			$checkArray	= array();
			
			$chequeListArray	= array();

			for($c = 0; $c < count($chequeArray); $c++)
			{
				$rvno					= $chequeArray[$c]->voucherno;
				$chequeaccount			= $chequeArray[$c]->chequeaccount;
				$chequenumber			= $chequeArray[$c]->chequenumber; 
				$chequedate				= $chequeArray[$c]->chequedate; 
				$chequedate				= $this->date->dateFormat($chequedate);
				$chequeamount			= $chequeArray[$c]->chequeamount;
				$chequeconvertedamount	= $chequeArray[$c]->chequeconvertedamount;

				$rollArray1['chequeaccount']		= $chequeaccount;
				$rollArray1['chequenumber']			= $chequenumber;
				$rollArray1['chequedate']			= $chequedate;
				$rollArray1['chequeamount']			= $chequeamount;
				$rollArray1['chequeconvertedamount'] = $chequeconvertedamount;
				
				$rollArray[$rvno][]				= $rollArray1;
			}
		}
		
		$temp["rollArray"] = $rollArray;

		return $temp;
	}

	public function retrieveViewData($sid)
	{
		$setFields = "voucherno, transactiondate, customer, referenceno, particulars, duedate, amount, balance, exchangerate, convertedamount";
		$cond = "voucherno = '$sid'";
		
		$temp = array();

		// Retrieve Header
		$retrieveArrayMain =  $this->db->setTable('accountsreceivable')
									->setFields($setFields)
									->setWhere($cond)
									->setLimit('1')
									->runSelect()
									->getRow();

		$temp["main"] = $retrieveArrayMain;

		// Retrieve Customer Details
		$customer_code = $temp["main"]->customer;
		$custFields = "address1, tinno, terms, email, partnername AS name";
		$cust_cond = "partnercode = '$customer_code'";

		$custDetails = $this->db->setTable('partners')
							->setFields($custFields)
							->setWhere($cust_cond)
							->setLimit('1')
							->runSelect()
							->getRow();
		
		$temp["cust"] = $custDetails;

		// Retrieve Details
		$detailFields = "main.accountcode, chart.accountname, main.detailparticulars, main.debit, main.credit";
		$detail_cond  = "main.voucherno = '$sid'";
		$orderby 	  = "main.linenum";	
		$detailJoin   = "chartaccount as chart ON chart.id = main.accountcode AND chart.companycode = main.companycode";

		$retrieveArrayDetail = $this->db->setTable('ar_details as main')
									->setFields($detailFields)
									->leftJoin($detailJoin)
									->setWhere($detail_cond)
									->setOrderBy($orderby)
									->runSelect()
									->getResult();
		
		$temp["details"] = $retrieveArrayDetail;

		// Retrieve Payments
		$applicationFields = "app.voucherno,main.transactiondate,detail.accountcode,
		chart.accountname,main.wtaxcode,ftax.shortname,main.paymenttype,main.referenceno,
		app.amount,app.stat,main.checkdate,main.atcCode,main.particulars,detail.checkstat,
		app.discount,app.exchangerate,app.convertedamount";

		$appJoin_rv  = "receiptvoucher as main ON main.voucherno = app.voucherno 
						AND main.companycode = app.companycode";
		$appJoin_rvd = "rv_details as detail ON detail.voucherno = app.voucherno 
						AND detail.companycode = app.companycode";
		$appJoin_ca  = "chartaccount as chart ON chart.id = detail.accountcode 
						AND chart.companycode = detail.companycode";
		$appJoin_fin = "fintaxcode as ftax ON ftax.fstaxcode = main.wtaxcode 
						AND ftax.companycode = main.companycode";

		$app_cond 	 = "app.arvoucherno = '$sid' AND detail.linenum = '1' 
						AND app.stat != 'cancelled'";

		$applicationArray = $this->db->setTable('rv_application as app')
									->setFields($applicationFields)
									->leftJoin($appJoin_rv)
									->leftJoin($appJoin_rvd)
									->leftJoin($appJoin_ca)
									->leftJoin($appJoin_fin)
									->setWhere($app_cond)
									->runSelect()
									->getResult();
		
		//echo $this->db->buildSelect();
		
		$temp["payments"] = $applicationArray;

		// Received Cheques
		$sub_select = $this->db->setTable("rv_application AS app")
							   ->setFields("app.voucherno")
							   ->setWhere("app.arvoucherno = '$sid'")
							   ->buildSelect();

		$chequeFields = 'voucherno, chequeaccount, chequenumber, chequedate, chequeamount, 
						chequeconvertedamount';
		$cheque_cond  = "voucherno IN($sub_select)";

		$chequeArray  = $this->db->setTable('rv_cheques')
									->setFields($chequeFields)
									->setWhere($cheque_cond)
									->runSelect()
									->getResult();

		$rollArray	  = array();
	
		if(!empty($chequeArray))
		{
			$checkArray	= array();
			
			$chequeListArray	= array();

			for($c = 0; $c < count($chequeArray); $c++)
			{
				$rvno					= $chequeArray[$c]->voucherno;
				$chequeaccount			= $chequeArray[$c]->chequeaccount;
				$chequenumber			= $chequeArray[$c]->chequenumber; 
				$chequedate				= $chequeArray[$c]->chequedate; 
				$chequedate				= date("M d, Y",strtotime($chequedate));
				$chequeamount			= $chequeArray[$c]->chequeamount;
				$chequeconvertedamount	= $chequeArray[$c]->chequeconvertedamount;

				$rollArray1['chequeaccount']		= $chequeaccount;
				$rollArray1['chequenumber']			= $chequenumber;
				$rollArray1['chequedate']			= $chequedate;
				$rollArray1['chequeamount']			= $chequeamount;
				$rollArray1['chequeconvertedamount'] = $chequeconvertedamount;
				
				$rollArray[$rvno][]				= $rollArray1;
			}
		}
		
		$temp["rollArray"] = $rollArray;

		return $temp;
	}


	public function retrieveCashAccountList()
	{
		$result = $this->db->setTable('chartaccount chart')
							->setFields("id ind , accountname val")
							->leftJoin("accountclass as class USING(accountclasscode)")
							->setOrderBy("class.accountclass")
							->runSelect()
							->getResult();
		return $result;
	}
	//
	public function retrievePayments($voucherno)
	{
		$voucherno = (isset($voucherno) && !empty($voucherno)) ? $voucherno : "";

		$temp 	   = array();

		// Retrieve Payments
		$applicationFields = "app.voucherno,main.transactiondate,detail.accountcode,
		chart.accountname,main.wtaxcode,ftax.shortname,main.paymenttype,main.referenceno,
		app.amount,app.stat,main.checkdate,main.atcCode,main.particulars,detail.checkstat,
		app.discount,app.exchangerate,app.convertedamount,app.arvoucherno";

		$appJoin_rv  = "receiptvoucher as main ON main.voucherno = app.voucherno 
						AND main.companycode = app.companycode";
		$appJoin_rvd = "rv_details as detail ON detail.voucherno = app.voucherno 
						AND detail.companycode = app.companycode";
		$appJoin_ca  = "chartaccount as chart ON chart.id = detail.accountcode 
						AND chart.companycode = detail.companycode";
		$appJoin_fin = "fintaxcode as ftax ON ftax.fstaxcode = main.wtaxcode 
						AND ftax.companycode = main.companycode";

		$app_cond 	 = "app.voucherno = '$voucherno' AND detail.linenum = '1' 
					   AND app.stat != 'cancelled'";

		$applicationArray = $this->db->setTable('rv_application as app')
									->setFields($applicationFields)
									->leftJoin($appJoin_rv)
									->leftJoin($appJoin_rvd)
									->leftJoin($appJoin_ca)
									->leftJoin($appJoin_fin)
									->setWhere($app_cond)
									->runPagination();
									// ->runSelect()
									// ->getResult();
		
		// echo $this->db->buildSelect();
		
		$temp["payments"] = $applicationArray;

		// Received Cheques
		$sub_select = $this->db->setTable("rv_application AS app")
							   ->setFields("app.voucherno")
							   ->setWhere("app.arvoucherno = '$voucherno'")
							   ->buildSelect();

		$chequeFields = 'voucherno, chequeaccount, chequenumber, chequedate, 
						chequeamount, chequeconvertedamount';
		$cheque_cond  = "voucherno IN($sub_select)";

		$chequeArray  = $this->db->setTable('rv_cheques')
									->setFields($chequeFields)
									->setWhere($cheque_cond)
									->runSelect()
									->getResult();

		$rollArray	  = array();
	
		if(!empty($chequeArray))
		{
			$checkArray	= array();
			
			$chequeListArray	= array();

			for($c = 0; $c < count($chequeArray); $c++)
			{
				$rvno					= $chequeArray[$c]->voucherno;
				$chequeaccount			= $chequeArray[$c]->chequeaccount;
				$chequenumber			= $chequeArray[$c]->chequenumber; 
				$chequedate				= $chequeArray[$c]->chequedate; 
				$chequedate				= date("M d, Y",strtotime($chequedate));
				$chequeamount			= $chequeArray[$c]->chequeamount;
				$chequeconvertedamount	= $chequeArray[$c]->chequeconvertedamount;

				$rollArray1['chequeaccount']		= $chequeaccount;
				$rollArray1['chequenumber']			= $chequenumber;
				$rollArray1['chequedate']			= $chequedate;
				$rollArray1['chequeamount']			= $chequeamount;
				$rollArray1['chequeconvertedamount'] = $chequeconvertedamount;
				
				$rollArray[$rvno][]				= $rollArray1;
			}
		}
		
		$temp["rollArray"] = $rollArray;

		return $temp;
	}
	//
	public function retrieveList($data)
	{
		$daterangefilter = isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$custfilter      = isset($data['custfilter']) ? htmlentities($data['custfilter']) : ""; 
		$addCond         = isset($data['addCond']) ? htmlentities($data['addCond']) : "";
		$searchkey 		 = isset($data['search']) ? htmlentities($data['search']) : "";
		$sort 		 	 = isset($data['sort']) ? htmlentities($data['sort']) : "main.transactiondate";

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? $this->date->dateDbFormat($datefilterArr[0]) : ""; //date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? $this->date->dateDbFormat($datefilterArr[1]) : ""; //date("Y-m-d",strtotime($datefilterArr[1])) : "";

		if($addCond	== 'paid')
		{
			/*
				SELECT main.voucherno as voucherno, main.transactiondate as transactiondate, 
				main.convertedamount as amount, main.balance as balance, 
				CONCAT( first_name, ' ', last_name ) AS customer, main.referenceno as referenceno, 
				p.partnername 
				FROM accountsreceivable as main 
				LEFT JOIN partners p ON p.partnercode = main.customer 
				WHERE main.stat = 'posted' AND main.transactiondate BETWEEN '2017-06-01' 
				AND '2017-06-30' AND main.convertedamount = 
				(
					SELECT COALESCE(SUM(rv.convertedamount), 0) + COALESCE(SUM(rv.discount), 0) 
					- COALESCE(SUM(rv.forexamount), 0) FROM rv_application AS rv 
					WHERE rv.arvoucherno = main.voucherno and rv.stat = 'posted' 
					AND  rv.companycode = 'CID' 
				) AND  main.companycode = 'CID' 
			*/

			$table_rv  = "rv_application AS rv";
			$rv_fields = "COALESCE(SUM(rv.convertedamount),0) + COALESCE(SUM(rv.discount),0) 
			- COALESCE(SUM(rv.forexamount),0)";
			$rv_cond   = "rv.arvoucherno = main.voucherno and rv.stat = 'posted'";
			$sub_select = $this->db->setTable($table_rv)
							   ->setFields($rv_fields)
							   ->setWhere($rv_cond)
							   ->buildSelect();

			$addCondition	= "AND main.convertedamount = ($sub_select)";
		}
		else if($addCond == 'partial')
		{
			/*
				SELECT main.voucherno as voucherno, main.transactiondate as transactiondate, 
				main.convertedamount as amount, main.balance as balance, 
				CONCAT( first_name, ' ', last_name ) AS customer, main.referenceno as referenceno, 
				p.partnername 
				FROM accountsreceivable as main 
				LEFT JOIN partners p ON p.partnercode = main.customer 
				WHERE main.stat = 'posted' AND main.transactiondate BETWEEN '2017-06-01' 
				AND '2017-06-30' AND 
				(
					SELECT COALESCE(SUM(rv.convertedamount), 0) + COALESCE(SUM(rv.discount), 0) 
					- COALESCE(SUM(rv.forexamount), 0) FROM rv_application AS rv 
					WHERE rv.arvoucherno = main.voucherno 
					and rv.stat = 'posted' AND  rv.companycode = 'CID' 
				) > 0 AND main.convertedamount > 
				(
					SELECT COALESCE(SUM(rv.convertedamount), 0) + COALESCE(SUM(rv.discount), 0) 
					- COALESCE(SUM(rv.forexamount), 0) FROM rv_application AS rv 
					WHERE rv.arvoucherno = main.voucherno and rv.stat = 'posted' 
					AND  rv.companycode = 'CID' 
				) AND  main.companycode = 'CID' 
			*/

			$table_rv  = "rv_application AS rv";
			$rv_fields = "COALESCE(SUM(rv.convertedamount),0) + COALESCE(SUM(rv.discount),0) 
						- COALESCE(SUM(rv.forexamount),0)";
			$rv_cond   = "rv.arvoucherno = main.voucherno and rv.stat = 'posted'";
			$sub_select = $this->db->setTable($table_rv)
							   ->setFields($rv_fields)
							   ->setWhere($rv_cond)
							   ->buildSelect();

			
			$rv_cond_   = "rv.arvoucherno = main.voucherno and rv.stat = 'posted'";
			$sub_select_ = $this->db->setTable($table_rv)
							   ->setFields($rv_fields)
							   ->setWhere($rv_cond_)
							   ->buildSelect();
			
			$addCondition = "AND ($sub_select) > 0 AND main.convertedamount > ($sub_select_)";
		}
		else if($addCond == 'unpaid')
		{
			/*
				SELECT main.voucherno as voucherno, main.transactiondate as transactiondate, 
				main.convertedamount as amount, main.balance as balance, 
				CONCAT( first_name, ' ', last_name ) AS customer, main.referenceno as referenceno, 
				p.partnername, rv.voucherno AS rv_voucherno
				FROM accountsreceivable as main 
				LEFT JOIN rv_application rv ON main.voucherno = rv.arvoucherno AND rv.stat = 'posted'
				LEFT JOIN partners p ON p.partnercode = main.customer 
				WHERE main.stat = 'posted' AND main.transactiondate BETWEEN '2017-06-01' 
				AND '2017-06-30' AND 
				(
					SELECT COALESCE(SUM(rv.convertedamount), 0) + COALESCE(SUM(rv.discount), 0) 
					- COALESCE(SUM(rv.forexamount), 0) FROM rv_application AS rv 
					WHERE rv.arvoucherno = main.voucherno and rv.stat = 'posted' 
					AND  rv.companycode = 'CID' 
				) = 0 AND  main.companycode = 'CID' 
			*/


			$table_rv  = "rv_application AS rv";
			$rv_fields = "COALESCE(SUM(rv.convertedamount),0) + COALESCE(SUM(rv.discount),0) 
						- COALESCE(SUM(rv.forexamount),0)";
			$rv_cond   = "rv.arvoucherno = main.voucherno and rv.stat = 'posted'";
			$sub_select = $this->db->setTable($table_rv)
							   ->setFields($rv_fields)
							   ->setWhere($rv_cond)
							   ->buildSelect();
			
			$addCondition = "AND ($sub_select) = 0";
		}
		else
		{
			$addCondition	= '';

			/*
				SELECT main.voucherno as voucherno, main.transactiondate as transactiondate, 
				main.convertedamount as amount, main.balance as balance, 
				CONCAT( first_name, ' ', last_name ) AS customer, 
				main.referenceno as referenceno, p.partnername 
				FROM accountsreceivable as main 
				LEFT JOIN partners p ON p.partnercode = main.customer 
				WHERE main.stat = 'posted' AND main.transactiondate BETWEEN '2017-06-01' 
				AND '2017-06-30'  AND  main.companycode = 'CID' 
			*/

		}

		$add_query 	= (!empty($searchkey)) ? "AND (main.voucherno LIKE '%$searchkey%' 
		OR main.invoiceno LIKE '%$searchkey%' OR main.particulars LIKE '%$searchkey%' 
		OR p.partnername LIKE '%$searchkey%' ) " : "";
		$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? 
		"AND rvm.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
		$add_query .= (!empty($custfilter) && $custfilter != '') ? 
		"AND p.partnercode = '$custfilter' " : "";
		$add_query .= $addCondition;

		$main_fields = array("main.voucherno as voucherno", 
		"rvm.transactiondate as transactiondate", 
		"main.convertedamount as amount","main.balance as balance", 
		"CONCAT( first_name, ' ', last_name )","main.referenceno as referenceno", 
		"p.partnername AS partnername", "rv.voucherno AS rv_voucherno");
		$main_join   = "partners p ON p.partnercode = main.customer"; //AND p.companycode
		
		$rv_join 	 = "rv_application rv ON main.voucherno = rv.arvoucherno 
		AND rv.stat = 'posted'";
		$rvmain_join = "receiptvoucher rvm ON rvm.voucherno = rv.voucherno AND rvm.stat = 'posted' ";
		$main_table  = "accountsreceivable as main";
		$main_cond   = "main.stat = 'posted' $add_query";

		$query 		 = $this->db->setTable($main_table)
								->setFields($main_fields)
								->leftJoin($main_join)
								->leftJoin($rv_join)
								->leftJoin($rvmain_join)
								->setWhere($main_cond)
								->setOrderBy($sort)
								// ->buildSelect();
								->runPagination();
		//var_dump($this->db->getQuery());
		// var_dump($query);

		return $query;
		
	}

	//
	public function retrieveARList($data)
	{
		$customercode = (isset($data["customer"]) && !empty($data["customer"])) ? 
						$data["customer"]         : "";
		$voucherno  = (isset($data["voucherno"]) && !empty($data["voucherno"])) ? 
						$data["voucherno"]: "";
		$tempArr    = array();

		// Sub Select
		$table_rv  = "rv_application AS rv";
		$rv_fields = "COALESCE(SUM(rv.convertedamount),0) + COALESCE(SUM(rv.discount),0) 
					- COALESCE(SUM(rv.forexamount),0)";
		$rv_cond   = "rv.arvoucherno = main.voucherno AND rv.stat IN('posted')"; //'temporary'
	
		// Main Queries
		$main_table   = "accountsreceivable as main";
		$main_fields  = array("main.voucherno as voucherno", 
						"main.transactiondate as transactiondate", 
						"main.convertedamount as amount", "main.balance as balance", 
						"p.partnername AS customer_name", "main.referenceno as referenceno");
		$main_join 	  = "partners p ON p.partnercode = main.customer";
		$orderby  	  = "main.transactiondate DESC";

		if($customercode && empty($voucherno))
		{
			// echo "if";
			// Display Unpaid AR

			$sub_select = $this->db->setTable($table_rv)
							   ->setFields($rv_fields)
							   ->setWhere($rv_cond)
							   ->buildSelect();
			
			$addCondition = "AND ($sub_select) = 0";

			$main_cond    = "main.stat = 'posted' AND main.customer = '$customercode' 
							$addCondition";

			$query = $this->retrieveData($main_table, $main_fields, $main_cond, 
							$main_join, $orderby);

			$tempArr["result"] = $query;

		}
		else if($voucherno)
		{
			// echo "else";

			// Display Paid AR
			$sub_select = $this->db->setTable($table_rv)
								->setFields($rv_fields)
								->setWhere($rv_cond)
								->buildSelect();

			$addCondition	= "AND main.convertedamount = ($sub_select 
							AND rv.voucherno = '$voucherno') 
							OR ($sub_select AND rv.voucherno = '$voucherno') > 0";

			$main_cond    = "main.stat = 'posted' AND main.customer = '$customercode' 
			$addCondition";

			$query = $this->retrieveData($main_table, $main_fields, $main_cond, 
			$main_join, $orderby);

			$tempArr["result"] = $query;

		}
		//var_dump($tempArr);
		return $tempArr;

	}
	//
	public function retrieveRVDetails($data)
	{
		$cond  		= (isset($data["cond"]) && !empty($data["cond"])) ? $data["cond"]: "";
		$customercode = (isset($data["customer"]) && !empty($data["customer"])) ? 
						$data["customer"]: "";

		// Main Queries
		$main_table   = "accountsreceivable as main";
		$main_fields  = array("main.voucherno as voucherno", 
						"main.transactiondate as transactiondate", 
						"main.convertedamount as amount", "main.balance as balance", 
						"p.partnername AS customer_name", "main.referenceno as referenceno", 
						"ard.accountcode", "chart.accountclasscode", 
						"SUM(ard.debit) AS sumdebit", "ard.detailparticulars");
		$ard_join 	  = "ar_details AS ard ON main.voucherno = ard.voucherno 
						AND main.companycode = ard.companycode";
		$chart_join   = "chartaccount AS chart ON ard.accountcode = chart.id 
						AND chart.companycode = ard.companycode";
		$main_join 	  = "partners p ON p.partnercode = main.customer";
		$main_cond 	  = "main.stat = 'posted' AND main.customer = '$customercode' 
						AND chart.accountclasscode = 'ACCREC' AND ard.voucherno $cond";
		$groupby 	  = "ard.accountcode";
		$orderby  	  = "main.transactiondate DESC";

		$query = $this->db->setTable($main_table)
							->setFields($main_fields)
							->leftJoin($ard_join)
							->leftJoin($chart_join)
							->leftJoin($main_join)
							->setGroupBy($groupby)
							->setWhere($main_cond)
							->setOrderBy($orderby)
							->runSelect()
							->getResult();
							// ->buildSelect();
		//var_dump($this->db->getQuery());

		return $query;
	}

	public function buildQuery($table, $fields = array(), $cond = "")
	{	
		$sub_select = $this->db->setTable($table)
							   ->setFields($fields)
							   ->setWhere($cond)
							   ->buildSelect();
		
		return $sub_select;
	}
	//
	public function retrievePaymentDetails($voucherno)
	{
		$paymentFields		= array("app.arvoucherno as sourceno","main.checknumber",
							"ar.referenceno","main.particulars as remarks","app.amount as amount",
							"main.paymenttype");
		$paymentJoin		= "accountsreceivable as ar ON ar.voucherno = app.arvoucherno 
							  AND ar.companycode = app.companycode";
		$paymentJoin_ 		= "receiptvoucher as main ON main.voucherno = app.voucherno 
							  AND main.companycode = app.companycode"; 
		$paymentCondition 	= "app.voucherno = '$voucherno'";
		$paymentOrderBy 	= "app.linenum";


		$result = $this->db->setTable("rv_application as app")
					->setFields($paymentFields)
					->leftJoin($paymentJoin)
					->leftJoin($paymentJoin_)
					->setWhere($paymentCondition)
					->setOrderBy($paymentOrderBy)
					->runSelect()
					->getResult();

		// var_dump($this->db->buildSelect());
		
		return $result;
	}


	public function getValue($table, $cols = array(), $cond = "", $orderby = "", $bool = "")
	{
		$result = $this->db->setTable($table)
					->setFields($cols)
					->setWhere($cond)
					->setOrderBy($orderby)
					->runSelect($bool)
					->getResult();

		// var_dump($this->db->buildSelect());

		return $result;
	}
	//
	public function insert_detail_entry($voucherno, $post_detail)
	{
		$accountcode    = $post_detail["accountcode"];
		$linenum 	    = $post_detail["linenum"];
		$detailAppTable = "rv_details";

		// var_dump($post_detail);

		// rv_details
		$isAppDetailExist	= $this->getValue($detailAppTable, array("COUNT(*) AS count_rv"), 
		"voucherno = '$voucherno' AND accountcode = '$accountcode' AND linenum = '$linenum'");
		//var_dump("isAppDetailExist: " . $isAppDetailExist[0]->count_rv);
		if($isAppDetailExist[0]->count_rv > 0)
		{
			/*>>>>>>>*/
			//rv_details
			$insertResult = $this->db->setTable($detailAppTable) 
								->setValues($post_detail)
								->setWhere("voucherno = '$voucherno' 
								AND accountcode = '$accountcode' AND linenum = '$linenum'")
								// ->buildUpdate();
								->runUpdate();
			
			// var_dump($insertResult);
			/*>>>>>>*/
			if(!$insertResult)
				$errmsg["error"][] = "The system has encountered an error in updating 
				RV Details. Please contact admin to fix this issue.<br/>";
		}
		else
		{
			/*>>>>>>*/
			//rv_details
			$insertResult = $this->db->setTable($detailAppTable) 
								->setValues($post_detail)
								// ->buildInsert();
								->runInsert();
			
			// var_dump($insertResult);
			/*>>>>>>*/
			if(!$insertResult)
				$errmsg["error"][] = "The system has encountered an error in saving 
					RV Details. Please contact admin to fix this issue.<br/>";
		}
	}
	//
	public function applyPayments_($data)
	{
		$errmsg				   = array();
		$seq 				   = new seqcontrol();
		$datetime			   = date("Y-m-d H:i:s");

		/**SET TABLES**/
		$mainAppTable		   = "receiptvoucher"; 
		$detailAppTable		   = "rv_details";
		$applicationTable	   = "rv_application"; 
		$chequeTable		   = "rv_cheques"; 
		$applicableHeaderTable = "accountsreceivable"; 
		$applicableDetailTable = "ar_details"; 

		$source				   = "RV"; 

		$continue_flag		   = 1;
		$insertResult		   = 0;
		//var_dump($data);
		$voucherno			= (isset($data['h_voucher_no']) && (!empty($data['h_voucher_no']))) ? 
							htmlentities(addslashes(trim($data['h_voucher_no']))) : "";
		$customer			= (isset($data['customer']) && (!empty($data['customer']))) ? 
							htmlentities(addslashes(trim($data['customer']))) : "";
		$referenceno		= (isset($data['paymentreference']) && (!empty($data['paymentreference']))) ? 
							htmlentities(addslashes(trim($data['paymentreference']))) : "";
		$transactiondate	= (isset($data['document_date']) && (!empty($data['document_date']))) ? 
							htmlentities(addslashes(trim($data['document_date']))) : "";
		$remarks			= (isset($data['remarks']) && (!empty($data['remarks']))) ? 
							htmlentities(addslashes(trim($data['remarks']))) : "";
		$totalamount		= (isset($data['total_debit']) && (!empty($data['total_debit']))) ? 
							htmlentities(addslashes(trim($data['total_debit']))) : "";
		$paymenttype		= (isset($data['paymentmode']) && (!empty($data['paymentmode']))) ? 
							htmlentities(addslashes(trim($data['paymentmode']))) : "";
		$total_debit		= (isset($data['total_debit']) && (!empty($data['total_debit']))) ? 
							htmlentities(addslashes(trim($data['total_debit']))) : "";
		$total_credit		= (isset($data['total_credit']) && (!empty($data['total_credit']))) ? 
							htmlentities(addslashes(trim($data['total_credit']))) : "";
		$total_payment		= (isset($data['total_payment']) && (!empty($data['total_payment']))) ? 
							htmlentities(addslashes(trim($data['total_payment']))) : $total_debit;
		$task 				= (isset($data['h_task']) && (!empty($data['h_task']))) ? 
							htmlentities(addslashes(trim($data['h_task']))) : "";
		$h_check_rows 		= (isset($data['h_check_rows']) && (!empty($data['h_check_rows']))) ? 
							htmlentities(addslashes(trim($data['h_check_rows']))) : "";
		$invoice_data  		= str_replace('\\', '', $h_check_rows);
		$invoice_data  		= html_entity_decode($invoice_data);
		$decode_json   		= json_decode($invoice_data, true);

		$exchangerate		= (!empty($data['paymentrate'])) ? $data['paymentrate'] : "1.00";
		$convertedamount	= (!empty($data['paymentconverted'])) ? $data['paymentconverted'] : 
							$total_payment;
		$checkdate			= (!empty($data['checkdate'])) ? $data['checkdate'] : "0000-00-00";

		/**TRIM COMMAS FROM AMOUNTS**/
		$totalamount		= str_replace(',','',$totalamount);
		$total_payment		= str_replace(',','',$total_payment);
		$convertedamount 	= str_replace(',','',$convertedamount);
		$total_debit 		= str_replace(',','',$total_debit);
		$total_credit 		= str_replace(',','',$total_credit);
		
		// Decode JSON data to get the selected AR and amount
		$invoice_ = array();
		$amount_ = array();

		for($i = 0; $i < count($decode_json); $i++)
		{
			$invoice_[$i + 1] = $decode_json[$i]["arvoucher"];
			$amount_[$i + 1]  = $decode_json[$i]["amount"];

			$data["invoice_modal_"] = $invoice_;
			$data["pay_amount_"] = $amount_;
		}
		


		/**CLEAN PASSED DATA**/
		foreach($data as $postIndex => $postValue)
		{
			 //echo "\n" . $postIndex . " ";
			 //var_dump($postValue);

			if(($postIndex == 'invoice_modal_' || $postIndex=='accountcode' 
			|| $postIndex=='pay_amount_' || (array) $postIndex=='paymentdiscount' 
			|| (array) $postIndex=='paymentconverted' || (array) $postIndex=='paymentrate' 
			||  $postIndex=='detailparticulars' || $postIndex=='debit' || $postIndex=='credit' 
			|| $postIndex == "paid_amount") && !empty($postValue))
			{
				$a		= '';

				// echo "\n" . $postIndex . " ";
				// var_dump($postValue);

				foreach($postValue as $postValueIndex => $postValueIndexValue)
				{
					// echo "\n" . $postValueIndex . " ";
					// var_dump($postValueIndexValue);

					if($postIndex == 'pay_amount_' || $postIndex == 'paymentdiscount' 
					|| $postIndex == 'paymentconverted' || $postIndex == 'paymentrate' 
					|| $postIndex == 'debit' || $postIndex == 'credit' 
					|| $postIndex == "paid_amount")
					{
						$a = str_replace(',', '', $postValueIndexValue);
					}
					else if($postIndex == 'paymentdate' || $postIndex == 'checkdate')
					{
						$a = ($postValueIndexValue != '') ? 
						date("Y-m-d", strtotime($postValueIndexValue)) : "0000-00-00";
					}
					else
					{
						$a = htmlentities(addslashes(trim($postValueIndexValue)));
					}
				
					$arrayData[$postIndex][$postValueIndex] = $a;	
				}	
			}
			
			if(($postIndex == 'chequeaccount' || $postIndex == 'chequenumber' 
			|| $postIndex == 'chequedate' || $postIndex == 'chequeamount' 
			|| $postIndex == 'chequeconvertedamount') && !empty($postValue) )
			{
				$b		= '';

				foreach($postValue as $postValueIndex => $postValueIndexValue)
				{
					if($postIndex == 'chequeamount' || $postIndex == 'chequeconvertedamount')
					{
						// echo "\n1";
						$b = str_replace(',', '', $postValueIndexValue);
					}
					else if($postIndex == 'chequedate')
					{
						// echo "\n2";
						$b = ($postValueIndexValue != '') ? date("Y-m-d", 
						strtotime($postValueIndexValue)) : "0000-00-00";
					}
					else
					{
						// echo "\n3";
						$b = htmlentities(addslashes(trim($postValueIndexValue)));
					}
					
					$chequeData[$postIndex][$postValueIndex] = $b;
				}
			}
		}

		// var_dump($arrayData);

		foreach($arrayData as $arrayDataIndex => $arrayDataValue)
		{
			foreach($arrayDataValue as $postValueIndex => $postValueIndexValue)
			{	
				$tempArray[$postValueIndex][$arrayDataIndex] = $postValueIndexValue;
			}
		}

		//var_dump($tempArray);
		
		/**CHEQUE DETAILS**/
		if(!empty($chequeData))
		{
			foreach($chequeData as $chequeDataIndex => $chequeDataValue)
			{
				foreach($chequeDataValue as $chequeValueIndex => $chequeValueIndexValue)
				{
					$newArray[$chequeValueIndex][$chequeDataIndex] = $chequeValueIndexValue;
				}
			}
		}

		$count				= 1;
		$linenum			= 1;
		$totalamount		= 0;
		$totaltaxamount		= 0;
		$totalwtaxamount	= 0;
		
		$cheque_info		= array();
		$tempCheque 		= array();

		// For Cheque
		if(!empty($newArray))
		{
			$linecount		= 1;

			foreach($newArray as $newArrayIndex => $newArrayValue)
			{
				$chequeaccount			= (!empty($newArrayValue['chequeaccount'])) ? 
										$newArrayValue['chequeaccount'] : "";
				$chequenumber			= (!empty($newArrayValue['chequenumber'])) ? 
										$newArrayValue['chequenumber'] : "";
				$chequedate				= (!empty($newArrayValue['chequedate'])) ? 
										$newArrayValue['chequedate'] : "";
				$chequeamount			= (!empty($newArrayValue['chequeamount'])) ? 
										$newArrayValue['chequeamount'] : "";
				// $chequeconvertedamount	= (!empty($newArrayValue['chequeconvertedamount'])) ? $newArrayValue['chequeconvertedamount'] : "";
				$chequedate				= $this->date->dateDbFormat($chequedate);
				
				if(!empty($chequedate) && !empty($chequeaccount) && !empty($chequenumber) 
				& !empty($chequeamount))
				{
					$cheque_header['voucherno']				= $voucherno;
					$cheque_header['transtype']				= "RV";
					$cheque_header['linenum']				= $linecount;
					$cheque_header['chequeaccount']			= $chequeaccount;
					$cheque_header['chequenumber']			= $chequenumber;
					$cheque_header['chequedate']			= $chequedate;
					$cheque_header['chequeamount']			= $chequeamount;
					$cheque_header['chequeconvertedamount']	= $chequeamount; //$chequeconvertedamount;
					$cheque_header['stat']					= 'uncleared';
				
					$linecount++;
					
					$cheque_info[$chequeaccount]['amount'][]	 = $chequeamount; //$chequeconvertedamount;
					
					$tempCheque[] = $cheque_header;
				}
			}
		}

		$isExist			= $this->getValue($mainAppTable, array("stat"), 
							"voucherno = '$voucherno' AND stat = 'posted'");
		$status				= (!empty($isExist[0]->stat)) ? "posted" : "temporary";
		$valid 				= 0;

		$transactiondate				= $this->date->dateDbFormat($transactiondate); 
		$period							= date("n",strtotime($transactiondate));
		$fiscalyear						= date("Y",strtotime($transactiondate));

		// Start Header
		$post_header['voucherno']		= $voucherno;
		$post_header['customer']		= $customer;
		$post_header['transactiondate']	= $transactiondate;
		$post_header['transtype']		= $source;
		$post_header['particulars']		= $remarks;
		$post_header['period']			= $period;
		$post_header['fiscalyear']		= $fiscalyear;
		$post_header['checkrelease']	= $transactiondate;
		$post_header['releaseby']		= USERNAME;
		$post_header['currencycode']	= 'PHP';
		$post_header['amount']			= $total_payment;
		$post_header['exchangerate']	= $exchangerate;
		$post_header['convertedamount']	= $convertedamount;
		$post_header['source']			= $source;
		$post_header['paymenttype']		= $paymenttype;	
		$post_header['bankcode']		= '';
		$post_header['account']			= '';

		if(strtolower($paymenttype) == 'cheque')
		{
			$post_header['checknumber']		= $referenceno;
			$post_header['referenceno']		= $referenceno;
		}
		else
		{
			$post_header['checknumber']		= $referenceno;
			$post_header['referenceno']		= $referenceno;
		}

		$post_header['checkdate']		= $checkdate;
		$post_header['checkstat']		= '';
		$post_header['stat']			= $status;
		$post_header['postedby']		= USERNAME;
		$post_header['postingdate']		= $datetime;

		// Header Data Array
		$tempData[] = $post_header;

		/**INSERT HEADER**/
		if($status == 'temporary')
		{	
			// echo "\n1\n";
			
			// Delete temporary data
			// receiptvoucher
			$this->db->setTable($mainAppTable)
					->setWhere("voucherno = '$voucherno'");
					// ->buildDelete();
			/*>>>>>>*/
			$insertResult = $this->db->runDelete();
			/*>>>>>>*/
			if(!$insertResult)
			 	$valid++;
			
			// receiptvoucher
			$this->db->setTable($mainAppTable) 
		 		 ->setValues($tempData);
				 //->buildInsert();
			//var_dump($this->db->getQuery());
			/*>>>>>>*/
			$insertResult = ($valid == 0) ? $this->db->runInsert() : false;
			/*>>>>>>*/
			if(!$insertResult)
				$errmsg[] = "<li>Saving Receipt Voucher Header.</li>";
				// $errmsg["error"][] = "The system has encountered an error in inserting 
				// Receipt Voucher. Please contact admin to fix this issue.<br/>";
		}
		else
		{
			// echo "\n2\n";
			/*>>>>>>*/
			$insertResult = $this->db->setTable($mainAppTable)
								->setValues($tempData)
								->setWhere("voucherno = '$voucherno'")
								->runUpdate();
								// ->buildUpdate();

			// var_dump($insertResult);
			/*>>>>>>*/
			if(!$insertResult)
				$errmsg[] = "<li>Updating Receipt Voucher Header.</li>";
				// $errmsg["error"][] = "The system has encountered an error in updating 
				// Receipt Voucher. Please contact admin to fix this issue.<br/>";
		}

		$counter = 0;

		$arrDB = array();
		$arrCB = array();

		foreach($tempArray as $tempArrayIndex => $tempArrayValue)
		{
			$accountcode 		= (!empty($tempArrayValue['accountcode'])) ? 
			$tempArrayValue['accountcode'] : "";

			if($task == "create")
				$amount = (!empty($tempArrayValue['pay_amount_'])) ? 
				$tempArrayValue['pay_amount_'] : "";
			else
				$amount = (!empty($tempArrayValue['paid_amount'])) ? 
				$tempArrayValue['paid_amount'] : "";

			
			$convertedamount	= (!empty($tempArrayValue['paymentconverted'])) ? 
				$tempArrayValue['paymentconverted'] : $amount;
		
			$exchangerate		= (!empty($tempArrayValue['paymentrate'])) ? 
			$tempArrayValue['paymentrate'] : "1.00";
			$paymentdiscount 	= (!empty($tempArrayValue['paymentdiscount'])) ? 
			$tempArrayValue['paymentdiscount'] : 0;
			//var_dump($paymentdiscount);
			// For RV Application
			$invoice			= (!empty($tempArrayValue['invoice_modal_'])) ? 
			$tempArrayValue['invoice_modal_'] : "";
			$pay_amount			= (!empty($tempArrayValue['pay_amount_'])) ? 
			$tempArrayValue['pay_amount_'] : "";

			// For RV Details
			$detailparticulars	= (!empty($tempArrayValue['detailparticulars'])) ? 
			$tempArrayValue['detailparticulars'] : "";
			$debit			    = (!empty($tempArrayValue['debit'])) ? 
			$tempArrayValue['debit'] : "";
			$credit			    = (!empty($tempArrayValue['credit'])) ? 
			$tempArrayValue['credit'] : "";

			// accountsreceivable
			$receivablerate		= $this->getValue($applicableHeaderTable, 
			array("exchangerate"),"voucherno = '$invoice' AND stat = 'posted'"); 	
			$receivablerate 		= (isset($receivablerate[0]->exchangerate)) ? 
			$receivablerate[0]->exchangerate : "1.00";
			// var_dump("transactiondate:".$transactiondate);
			// var_dump("paymenttype:".$paymenttype);
			// var_dump("paymentdiscount:".$paymentdiscount);
			// var_dump("total_debit:".$total_debit);
			// var_dump("total_credit:".$total_credit);
			if(!empty($transactiondate) && !empty($paymenttype) 
			&& ( ($total_debit == $total_credit) || $paymentdiscount > 0))
			{
				$taxamount	      = 0;
				$tempDetail       = array();
				$discountaccount  = '120';

				// if(empty($tempArray['paymentconverted'])) will receive value of $amount
				$newamount			= $convertedamount - $paymentdiscount;
				$forexamount 		= 0;

				if($receivablerate != $exchangerate)
				{
					// echo "if";
					$receivableconverted	= $amount * $receivablerate;
					$forexamount 		= $convertedamount - $receivableconverted;
					$newamount			= ($forexamount > 0) ? $newamount - abs($forexamount) : 
										  $newamount + abs($forexamount);
					$forexaccount		= ($forexamount > 0) ? "208" : "209";
				}
				else
				{
					$receivableconverted	= $convertedamount;
				}

				$creditamount		= ($newamount > 0) ? $convertedamount : abs($newamount);

				// For RV DETAILS
				$post_detail = array();
				$post_detail['voucherno']		= $voucherno;
				$post_detail['transtype']		= $source;
				$post_detail['slcode']			= '-';
				$post_detail['costcentercode']	= '-';
				$post_detail['checkstat']		= 'uncleared';
				$post_detail['checknumber']		= $post_header['checknumber'];
				$post_detail['postedby']		= USERNAME;
				$post_detail['postingdate']		= $datetime;
				$post_detail['stat']			= $post_header['stat'];
				//var_dump($post_detail);
				if($paymenttype == 'cheque')
				{
					$other_detail = array();

					if($credit != 0)
					{
						$post_detail['linenum']				= $linenum;
						$post_detail['arvoucherno']			= $invoice;
						$post_detail['accountcode']			= $accountcode;
						$post_detail['debit']				= $debit;
						$post_detail['credit']  			= $credit;
						$post_detail['converteddebit']		= $post_detail['debit'];
						$post_detail['convertedcredit']		= $post_detail['credit'];
						$post_detail['detailparticulars']	= $detailparticulars;

						// $other_detail['linenum']			= $linenum;
						$other_detail['arvoucherno']		= $invoice;
						$other_detail['credit']				= $credit;
						$other_detail['convertedcredit']	= $other_detail['credit'];

						$arrCB[] = $other_detail;
						/*>>>>>>*/
						// Start Insert for Credit
						$this->insert_detail_entry($voucherno, $post_detail);

						// var_dump($post_detail);


					} 
					else
					{

						$other_detail['linenum']			= $linenum;
						$other_detail['accountcode']		= $accountcode;
						$other_detail['debit']  			= $debit;
						$other_detail['converteddebit']		= $other_detail['debit'];
						$other_detail['detailparticulars']	= $detailparticulars;

						$arrDB[] = $other_detail;

						// Insert for Debit
						for($i = 0; $i < count($arrCB); $i++)
						{
							for($j = 0; $j < count($arrDB); $j++)
							{
								$dis_debit                         = ($arrCB[$i]["credit"] / $total_credit) 
																	  * $arrDB[$j]["debit"];

								$debit_detail["linenum"]           = ($arrDB[$j]["linenum"] + $i) + 1;
								$debit_detail["arvoucherno"]       = $arrCB[$i]["arvoucherno"];
								$debit_detail["accountcode"]       = $arrDB[$j]["accountcode"];
								$debit_detail["debit"]             = $dis_debit;
								$debit_detail["converteddebit"]    = $dis_debit;
								$debit_detail["detailparticulars"] = $arrDB[$j]["detailparticulars"];

								$post_detail["linenum"] 			= $debit_detail["linenum"];
								$post_detail["arvoucherno"] 		= $debit_detail["arvoucherno"];
								$post_detail["accountcode"] 		= $debit_detail["accountcode"];
								$post_detail["debit"] 				= $debit_detail["debit"];
								$post_detail["converteddebit"]      = $debit_detail["converteddebit"];
								$post_detail["detailparticulars"]   = $debit_detail["detailparticulars"];
								/*>>>>>>*/
								// Start Insert for Debit
								if($dis_debit != 0)
									$errmsg[] = $this->insert_detail_entry($voucherno, $post_detail);
							}
						}
					}

					// var_dump($post_detail);

					$linenum++;


					if(!empty($cheque_info))
					{
						foreach($cheque_info as $cheque_index => $cheque_value)
						{
							$totalamount					= array_sum($cheque_value['amount']);
							$post_detail['linenum']			= $linenum;
							$post_detail['arvoucherno']		= $invoice;
							$post_detail['accountcode']		= $cheque_index;
							$post_detail['debit']			= $totalamount;
							$post_detail['credit']			= 0;
							
							$post_detail['converteddebit']	= $post_detail['debit'];
							$post_detail['convertedcredit']	= $post_detail['credit'];
							
							// rv_details
							$isAppDetailExist	= $this->getValue($detailAppTable, 
							array("COUNT(*) AS count"),"voucherno = '$voucherno' 
							AND accountcode = '$cheque_index' AND linenum = '$linenum'");
							
							// var_dump($post_detail);

							// $tempDetail[] = $post_detail;


							/*if($isAppDetailExist[0]->count > 0)
							{
								echo "\n 14 \n";

								//pv_details
								$insertResult = $this->db->setTable($detailAppTable)
													->setValues($post_detail)
													->setWhere("voucherno = '$voucherno' AND accountcode = '$cheque_index' AND linenum = '$linenum'")
													->buildUpdate();
													// ->runUpdate();

								var_dump($insertResult);

								if(!$insertResult)
									$errmsg["error"][] = "The system has encountered an error in inserting PV Details [cheque]. Please contact admin to fix this issue.<br/>";
									

							}
							else
							{
								echo "\n 15 \n";

								//pv_details
								$insertResult = $this->db->setTable($detailAppTable)
													->setValues($post_detail)
													->buildInsert();
													// ->runInsert();
								
								var_dump($insertResult);

								if(!$insertResult)
									$errmsg["error"][] = "The system has encountered an error in inserting PV Details [cheque]. Please contact admin to fix this issue.<br/>";
							}
							*/
							// $linenum++;
						}
					}
				}
				else if($paymenttype == "cash")
				{
					if($paymentdiscount > 0 && $convertedamount == 0)
					{
						//echo "\n 3 \n";
						// ar_details
						$accountcode = $this->getValue($applicableDetailTable, "accountcode", 
						"voucherno = '$invoice' AND linenum = '1' ",false,true);
						$accountcode = (isset($accountcode[0]->accountcode))? $accountcode[0]->accountcode : "";
						
					}

					// $post_detail['linenum']				= $linenum;
					// $post_detail['apvoucherno']			= $invoice;
					// $post_detail['accountcode']			= $accountcode;
					// $post_detail['debit']				= $debit;
					// $post_detail['credit']  			= $credit;
					// $post_detail['converteddebit']		= $post_detail['debit'];
					// $post_detail['convertedcredit']		= $post_detail['credit'];
					// $post_detail['detailparticulars']	= $detailparticulars;
					
					$other_detail = array();
					//var_dump("debit:" . $debit);
					//var_dump("credit:" . $credit);
					if($credit != 0)
					{
						$post_detail['linenum']				= $linenum;
						$post_detail['arvoucherno']			= $invoice;
						$post_detail['accountcode']			= $accountcode;
						$post_detail['debit']				= $debit;
						$post_detail['credit']  			= $credit;
						$post_detail['converteddebit']		= $post_detail['debit'];
						$post_detail['convertedcredit']		= $post_detail['credit'];
						$post_detail['detailparticulars']	= $detailparticulars;

						$other_detail['arvoucherno']		= $invoice;
						$other_detail['credit']				= $credit;
						$other_detail['convertedcredit']	= $other_detail['credit'];

						$arrCB[] = $other_detail;

						//var_dump($post_detail);
						/*>>>>>*/
						// Start Insert for Credit
						$errmsg[] = $this->insert_detail_entry($voucherno, $post_detail);

					} 
					else
					{

						$other_detail['linenum']			= $linenum;
						$other_detail['accountcode']		= $accountcode;
						$other_detail['debit']  			= $debit;
						$other_detail['converteddebit']	    = $other_detail['debit'];
						$other_detail['detailparticulars']	= $detailparticulars;

						$arrDB[] = $other_detail;

						// var_dump($other_detail);

						// Insert for Debit
						for($i = 0; $i < count($arrCB); $i++)
						{
							for($j = 0; $j < count($arrDB); $j++)
							{
								$dis_debit                         = ($arrCB[$i]["credit"] / $total_credit) 
																	 * $arrDB[$j]["debit"];

								$debit_detail["linenum"]           = ($arrDB[$j]["linenum"] + $i) + 1;
								$debit_detail["arvoucherno"]       = $arrCB[$i]["arvoucherno"];
								$debit_detail["accountcode"]       = $arrDB[$j]["accountcode"];
								$debit_detail["debit"]             = $dis_debit;
								$debit_detail["converteddebit"]    = $dis_debit;
								$debit_detail["detailparticulars"] = $arrDB[$j]["detailparticulars"];

								$post_detail["linenum"] 			= $debit_detail["linenum"];
								$post_detail["arvoucherno"] 		= $debit_detail["arvoucherno"];
								$post_detail["accountcode"] 		= $debit_detail["accountcode"];
								$post_detail["debit"] 				= $debit_detail["debit"];
								$post_detail["converteddebit"]      = $debit_detail["converteddebit"];
								$post_detail["detailparticulars"]   = $debit_detail["detailparticulars"];
								/*>>>>>*/
								// Start Insert for Debit
								if($dis_debit != 0)
									$this->insert_detail_entry($voucherno, $post_detail);
								
							}
						}
					}

					// var_dump($post_detail);

					$linenum++;

				} // end else()

				/**DISCOUNT ACCOUNT**/
				if($paymentdiscount > 0)
				{
					//echo "\n 8 \n";
					$post_detail['linenum']			= $linenum;
					$post_detail['accountcode']		= $discountaccount; // hardcoded
					$post_detail['debit']			= 0;
					$post_detail['credit']			= $paymentdiscount;

					$post_detail['converteddebit']	= $post_detail['debit'];
					$post_detail['convertedcredit']	= $post_detail['credit'];
					
					//rv_details
					echo $this->db->setTable($detailAppTable) 
							->setValues($post_detail);
					
					$insertResult = false; //$this->db->runInsert();

					if(!$insertResult)
						$errmsg[] = "<li>Applying the Discount.</li>";

					$linenum++;
				}

				/**FOREX GAIN / LOSS**/
				if(abs($forexamount) > 0)
				{
					//echo "\n 9 \n";
					$post_detail['linenum']			= $linenum;
					$post_detail['accountcode']		= $forexaccount;
					$post_detail['debit']			= 0;
					$post_detail['credit']			= abs($forexamount);
					$post_detail['converteddebit']	= $post_detail['debit'];
					$post_detail['convertedcredit']	= $post_detail['credit'];
					
					//rv_details
					$this->db->setTable($detailAppTable) 
							->setValues($post_detail);
					
					$insertResult = false; //$this->db->runInsert();
					
					if(!$insertResult)
						$errmsg[] = "<li>Applying the Discount.</li>";
						// $errmsg["error"][] = "The system has encountered an error in inserting 
						// RV Details [discount]. Please contact admin to fix this issue.<br/>";

					$linenum++;
				}

				/**UPDATE APPLICATION TABLE**/
				$post_application['voucherno']		= $voucherno;
				$post_application['transtype']		= $source;
				$post_application['linenum']		= $count;
				$post_application['arvoucherno']	= $invoice;
				$post_application['discount']		= $paymentdiscount;
				$post_application['amount']			= $amount;
				$post_application['currencycode']	= 'PHP';
				$post_application['exchangerate']	= $exchangerate;
				$post_application['convertedamount']= $convertedamount;
				$post_application['forexamount']	= abs($forexamount);
				$post_application['stat']			= $post_header['stat'];
				//var_dump($post_application);
				
				// rv_application
				$isAppDetailExist	= $this->getValue($applicationTable, 
				array("COUNT(*) AS count"), "voucherno = '$voucherno' AND arvoucherno = '$invoice'");

				if($isAppDetailExist[0]->count > 0)
				{
					// echo "\n 10 \n";
					/*>>>>>>*/
					//rv_application
					$insertResult = $this->db->setTable($applicationTable) 
										->setValues($post_application)
										->setWhere("voucherno = '$voucherno' 
										AND arvoucherno = '$invoice'")
										// ->buildUpdate();
										->runUpdate();

					// var_dump($insertResult);					
					/*>>>>>>*/
					if(!$insertResult)
						$errmsg[] = "<li>Updating Receipt Voucher Application.</li>";
						/*$errmsg["error"][] = "The system has encountered an error in updating 
						RV Application. Please contact admin to fix this issue.<br/>";*/

				}
				else if(!empty($isAppDetailExist) && !empty($invoice))
				{
					// echo "\n 11 \n";
					/*>>>>>*/
					//rv_application
					$insertResult = $this->db->setTable($applicationTable) 
										->setValues($post_application)
										// ->buildInsert();
										->runInsert();
					
					// var_dump($insertResult);	
					/*>>>>>*/
					if(!$insertResult)
						$errmsg[] = "<li>Updating Receipt Voucher Application.</li>";
						/*$errmsg["error"][] = "The system has encountered an error in updating 
						RV Application. Please contact admin to fix this issue.<br/>";*/
				}
				
				$count++;

				$totalamount		+= $amount;
				$totaltaxamount		+= $taxamount;

			} // end if()

		} // end foreach()

		/**UPDATE HEADER AMOUNTS**/
		$update_info				= array();
		$update_info['netamount']	= $totalamount;
		$update_info['taxamount']	= $totaltaxamount;
		/*>>>>>*/
		// echo "\n 13 \n";
		// receiptvoucher
		$insertResult = $this->db->setTable($mainAppTable) 
							->setValues($update_info)
							->setWhere("voucherno = '$voucherno' AND stat = '$status'")
							// ->buildUpdate();
							->runUpdate();

		// var_dump($insertResult);
		/*>>>>>*/
		if(!$insertResult)
			$errmsg[] = "<li>Updating Receipt Voucher Header.</li>";
			/*$errmsg["error"][] = "The system has encountered an error in updating 
			Receipt Voucher. Please contact admin to fix this issue.<br/>";*/
		
		/**INSERT TO CHEQUES TABLE**/
		if(strtolower($paymenttype) == 'cheque')
		{
			// echo "\n 16 \n";
			/*>>>>>*/
			// Delete
			$insertResult = $this->db->setTable($chequeTable)
								->setWhere("voucherno = '$voucherno'")
								// ->buildDelete();
								->runDelete();
			
			// Insert
			if($insertResult && !empty($tempCheque))
			{
				// echo "\n 17 \n";
				/*>>>>>*/
				$insertResult = $this->db->setTable($chequeTable)
									->setValues($tempCheque)
									// ->buildInsert();
									->runInsert();
				/*>>>>>*/
				if(!$insertResult)
					$errmsg[] = "<li>Saving in Cheque Details.</li>";
					/*$errmsg["error"][] = "The system has encountered an error in saving 
					Cheque Details. Please contact admin to fix this issue.<br/>";*/
			}
		}

		return $errmsg;

	} // end applyPayments_()
	//
	public function saveDetails($table, $data, $form = "")
	{
		$result 				   = "";

		if($form == "customerdetail")
		{
			$data_insert["stat"]       = "active";
			$data_insert["terms"]      = $data["h_terms"];
			$data_insert["tinno"]      = $data["h_tinno"];
			$data_insert["address1"]   = $data["h_address1"];
		}

		if($data["h_querytype"] == "insert")
		{
			$this->db->setTable($table)
				 ->setValues($data_insert);
			
			// echo $this->db->buildInsert();

			$result = $this->db->runInsert();
		
		}
		else if($data["h_querytype"] == 'update')
		{
			$partnercode = $data["h_condition"];
			$cond 		 = "partnercode = '$partnercode'";
			
			$this->db->setTable($table)
					->setValues($data_insert)
					->setWhere($cond);
			// echo $this->db->buildUpdate();
			$result = $this->db->runUpdate();
		}

		return $result;
	}
	//
	public function fileInsert($docData)
	{
		$datetime      	= date("Y-m-d H:i:s"); 
		$seq 			= new seqcontrol();
		
		$x				= 0;
		$debitaccount	= '122';
		$creditaccount	= '42';
		$voucherList 	= array();

		$tempHeader 	= array();
		$tempDetail 	= array();
		$errmsg 		= array();

		for($i = 0; $i < count($docData); $i++)
		{	
			$generatedVoucher 	= $seq->getValue('AR');

			$period				= date("m",strtotime($docData[$i]['transactiondate']));
			$fiscalyear			= date("Y",strtotime($docData[$i]['transactiondate']));

			$voucherList[]		= $generatedVoucher;

			// Header accounts_receivable
			$data_header["voucherno"]       = $generatedVoucher;
			$data_header["transactiondate"] = $docData[$i]['transactiondate'];
			$data_header["currencycode"] 	= "PHP";
			$data_header["referenceno"] 	= $docData[$i]['invoice'];
			$data_header["exchangerate"] 	= "1.00";
			$data_header["stat"]            = "posted";
			$data_header["transtype"]       = "AR";
			$data_header["invoicedate"]     = $docData[$i]['transactiondate'];
			$data_header["duedate"]    		= $docData[$i]['duedate'];
			$data_header["customer"]    	= $docData[$i]['customer'];
			$data_header["invoiceno"]    	= $docData[$i]['invoice'];
			$data_header["amount"] 			= $docData[$i]['amount'];
			$data_header["convertedamount"] = $docData[$i]['amount'];
			$data_header["period"] 			= $period;
			$data_header["fiscalyear"] 		= $fiscalyear;
			$data_header["terms"] 			= "0";
			$data_header["amountreceived"] 		= "0.00";
			$data_header["amountforpayment"] = "0.00";
			$data_header["particulars"]  	= $docData[$i]['notes'];
			$data_header["source"] 			= "AR";
			$data_header["balance"]  		= $docData[$i]['amount'];

			$tempHeader[] 					= $data_header;

			// Details Debit: ar_details
			$data_details["voucherno"] 		= $generatedVoucher;
			$data_details["transtype"]      = "AR";
			$data_details["linenum"] 		= "1";
			$data_details["accountcode"] 	= $debitaccount;
			$data_details["debit"] 			= $docData[$i]['amount'];
			$data_details["credit"] 		= "0.00";
			$data_details["currencycode"]   = "PHP";
			$data_details["exchangerate"]   = "1.00";
			$data_details["converteddebit"] = $docData[$i]['amount'];
			$data_details["convertedcredit"] = "0.00";
			$data_details["stat"] 			= "posted";

			$tempDetail[] 					= $data_details;

			// Details Credit: ar_details
			$data_details["voucherno"] 		= $generatedVoucher;
			$data_details["transtype"]      = "AR";
			$data_details["linenum"] 		= "2";
			$data_details["accountcode"] 	= $creditaccount;
			$data_details["debit"] 			= "0.00";
			$data_details["credit"] 		= $docData[$i]['amount'];
			$data_details["currencycode"]   = "PHP";
			$data_details["exchangerate"]   = "1.00";
			$data_details["converteddebit"] = "0.00";
			$data_details["convertedcredit"] = $docData[$i]['amount'];
			$data_details["stat"] 			= "posted";

			$tempDetail[] 					= $data_details;

			$x++;
		}

		// Insert Header
		$insert_result = $this->db->setTable("accountsreceivable")
								->setValues($tempHeader)
								->runInsert();

		// var_dump($this->db->buildInsert());
		
		if(!$insert_result)
		{
			$errmsg[]	= "Selected file was not uploaded successfully.";

			/**ROLL BACK**/
			if(!empty($voucherList))
			{
				$voucherlist	= "'".implode("','",$voucherList)."'";
				$receivableCount= $this->getValue("accountsreceivable", array("COUNT(*) AS count"), 
								'companycode = "'.COMPANYCODE.'"');
				$receivableCount 	= $receivableCount[0]->count;

				// Delete Data
				$data_del["table"] = "accountsreceivable";
				$data_del["condition"] = "voucherno IN($voucherlist)";
				$this->deleteData($data_del);
				
				// Update
				$update_seq_info['current']	= ($receivableCount - $x) + 1;
				
				$this->db->setTable("wc_sequence_control")
						->setValues($update_seq_info)
						->setWhere("code = 'AR'")
						->runUpdate();
			}
		}
		else
		{
			// Insert Details
			$insert_result = $this->db->setTable("ar_details")
								->setValues($tempDetail)
								->runInsert();

			// var_dump($this->db->buildInsert());

			if($insert_result != 1)
			{
				$errmsg[]	= "Selected file was not uploaded successfully.";

				/**ROLL BACK**/
				if(!empty($voucherList))
				{
					$voucherlist 	= "'".implode("','",$voucherList)."'";
					$receivableCount= $this->getValue("accountsreceivable", 
					array("COUNT(*) AS count"), 'companycode = "'.COMPANYCODE.'"');
					$receivableCount= $receivableCount[0]->count;
					
					// Delete Data
					$data_del["table"] = "accountsreceivable";
					$data_del["condition"] = "voucherno IN($voucherlist)";
					$this->deleteData($data_del);

					// Delete Data
					$data_del["table"] = "ar_details";
					$data_del["condition"] = "voucherno IN($voucherlist)";
					$this->deleteData($data_del);

					// Update
					$update_seq_info['current']	= ($receivableCount - $x) + 1;
					
					$this->db->setTable("wc_sequence_control")
							->setValues($update_seq_info)
							->setWhere("code = 'AR'")
							->runUpdate();
				
				}
			}
		}

		return $errmsg;
	}
	//
	public function fileExport($data)
	{
		$daterangefilter = isset($data['daterangefilter']) ? 
							htmlentities($data['daterangefilter']) : ""; 
		$custfilter      = isset($data['custfilter']) ? 
							htmlentities($data['custfilter']) : ""; 
		$addCond         = isset($data['addCond']) ? htmlentities($data['addCond']) : "";
		$searchkey 		 = isset($data['search']) ? htmlentities($data['search']) : "";

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? 
							 date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? 
							date("Y-m-d",strtotime($datefilterArr[1])) : "";

		if($addCond	== 'paid')
		{
			$table_rv  = "rv_application AS rv";
			$rv_fields = "COALESCE(SUM(rv.amount),0) + COALESCE(SUM(rv.discount),0)";
			$rv_cond   = "rv.arvoucherno = main.voucherno and rv.stat = 'posted'";
			$sub_select = $this->db->setTable($table_rv)
							   ->setFields($rv_fields)
							   ->setWhere($rv_cond)
							   ->buildSelect();

			$addCondition	= "AND main.amount = ($sub_select)";

			// $addCondition	= " AND main.amount = (select COALESCE(SUM(pv.amount),0) + COALESCE(SUM(pv.discount),0) from pv_application as pv where pv.apvoucherno = main.voucherno and pv.stat = 'posted') ";

		}
		else if($addCond == 'partial')
		{
			$table_rv  = "rv_application AS rv";
			$rv_fields = "COALESCE(SUM(rv.amount),0) + COALESCE(SUM(rv.discount),0)";
			$rv_cond   = "rv.arvoucherno = main.voucherno and rv.stat = 'posted'";
			$sub_select = $this->db->setTable($table_rv)
							   ->setFields($rv_fields)
							   ->setWhere($rv_cond)
							   ->buildSelect();

			
			$rv_cond_   = "rv.arvoucherno = main.voucherno and rv.stat = 'posted'";
			$sub_select_ = $this->db->setTable($table_rv)
							   ->setFields($rv_fields)
							   ->setWhere($rv_cond_)
							   ->buildSelect();
			
			$addCondition = "AND ($sub_select) > 0 AND main.amount > ($sub_select_)";		

		}
		else if($addCond == 'unpaid')
		{
			$table_rv  = "rv_application AS rv";
			$rv_fields = "COALESCE(SUM(rv.amount),0) + COALESCE(SUM(rv.discount),0)";
			$rv_cond   = "rv.arvoucherno = main.voucherno and rv.stat = 'posted'";
			$sub_select = $this->db->setTable($table_rv)
							   ->setFields($rv_fields)
							   ->setWhere($rv_cond)
							   ->buildSelect();
			
			$addCondition = "AND ($sub_select) = 0";

		}
		else
		{
			$addCondition	= '';
		}

		$add_query 	= (!empty($searchkey)) ? "AND (main.voucherno LIKE '%$searchkey%' 
			OR main.invoiceno LIKE '%$searchkey%' OR main.particulars LIKE '%$searchkey%' 
			OR p.first_name LIKE '%$searchkey%' OR p.last_name LIKE '%$searchkey%') " : "";
		$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? 
			"AND main.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
		$add_query .= (!empty($custfilter) && $custfilter != '') ? 
			"AND p.partnercode = '$custfilter' " : "";
		$add_query .= $addCondition;

		$main_fields = array("main.transactiondate as transactiondate", 
		"main.voucherno as voucherno", "CONCAT( first_name, ' ', last_name ) AS customer", 
		"main.referenceno as referenceno", "main.amount as amount", "main.balance as balance", 
		"main.particulars");

		$main_join   = "partners p ON p.partnercode = main.customer"; //AND p.companycode
		$main_table  = "accountsreceivable as main";
		$main_cond   = "main.stat = 'posted' $add_query";
		$query 		 = $this->retrieveData($main_table, $main_fields, $main_cond, $main_join);

		return $query;
	}
	//
	public function editData($data, $table, $cond)
	{
		$result = $this->db->setTable($table)
				->setValues($data)
				->setWhere($cond)
				// ->buildUpdate();
				->runUpdate();
		
		return $result;
	}
	//
	public function deleteData($data)
	{
		$table = $data["table"];
		$cond  = stripslashes($data["condition"]);
		
		$this->db->setTable($table)
				 ->setWhere($cond);
		
		// echo $this->db->buildDelete();

		$result = $this->db->runDelete();

		return $result;
	}
	//
	public function deletePayments($voucher)
	{
		$update_info 	= array();
		$errmsg 	 	= array();

		$appTable		= "rv_application";
		$detailTable	= "rv_details";
		$mainTable		= "receiptvoucher";
		$table			= "accountsreceivable";
		$paymentField	= array('arvoucherno','amount','wtaxamount');
		
		$paymentArray   = $this->db->setTable($appTable)
							   ->setFields($paymentField)
							   ->setWhere("voucherno = '$voucher' AND stat = 'posted'")
							   ->runSelect()
							   ->getResult();

		if(!empty($paymentArray))
		{
			for($i = 0; $i < count($paymentArray); $i++)
			{
				$mainvoucher	= $paymentArray[$i]->arvoucherno;
				$amount			= $paymentArray[$i]->amount;
				$wtaxamount		= $paymentArray[$i]->wtaxamount;
				$discount		= 0;

				$balance		= $this->getValue($table, array("balance"), 
								"voucherno = '$mainvoucher' AND stat = 'posted'");
				$balance 		= $balance[0]->balance;

				$update_info['balance']		= $balance + $amount + $discount;

				$amountreceived = $this->getValue($table, array("amountreceived"), 
								 "voucherno = '$mainvoucher' AND stat = 'posted'");
				$amountreceived = $amountreceived[0]->amountreceived;

				$update_info['amountreceived']	= $amountreceived - $amount - $discount;

				// Update accountsreceivable
				$result = $this->db->setTable($table)
							   ->setValues($update_info)
							   ->setWhere("voucherno = '$mainvoucher'")
							   ->runUpdate();
				
				if(!$result)
					$errmsg[] = "The system has encountered an error in updating 
					Accounts Receivable [$mainvoucher]. Please contact admin to fix this issue.";
				else
					$this->log->saveActivity("Update Accounts Receivable [$mainvoucher]");
			}

			$update_info			= array();
			$update_info['stat']	= 'cancelled';
			
			// Update rv_application
			$result = $this->db->setTable($appTable)
					->setValues($update_info)
					->setWhere("voucherno = '$voucher'")
					->runUpdate();
			
			if(!$result)
				$errmsg[] = "The system has encountered an error in updating 
						RV Application [$voucher]. Please contact admin to fix this issue.";
			else
				$this->log->saveActivity("Update RV Application [$voucher]");

			// Update rv_details
			$result = $this->db->setTable($detailTable)
					->setValues($update_info)
					->setWhere("voucherno = '$voucher'")
					->runUpdate();
			
			if(!$result)
				$errmsg[] = "The system has encountered an error in updating 
				Receipt Voucher Details [$voucher]. Please contact admin to fix this issue.";
			else
				$this->log->saveActivity("Update Receipt Voucher Details [$voucher]");
			
			// Update receiptvoucher
			$result = $this->db->setTable($mainTable)
					->setValues($update_info)
					->setWhere("voucherno = '$voucher'")
					->runUpdate();	
			
			if(!$result)
				$errmsg[] = "The system has encountered an error in updating 
				Receipt Voucher [$voucher]. Please contact admin to fix this issue.";
			else
				$this->log->saveActivity("Update Receipt Voucher [$voucher]");

			return $errmsg;
		}

	}

}