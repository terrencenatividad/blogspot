<?php
class payment_voucher_model extends wc_model
{
	public function __construct() 
	{
		parent::__construct();
		$this->log = new log();
	}

	public function retrieveVendorList()
	{
		$result = $this->db->setTable('partners')
					->setFields("partnercode ind, companycode, CONCAT( first_name, ' ', last_name ), partnername val")
					->setWhere("partnercode != '' AND partnertype = 'supplier' AND stat = 'active'")
					->setOrderBy("val")
					->runSelect()
					->getResult();
		
		return $result;
	}

	public function retrieveProformaList()
	{
		$result = $this->db->setTable('proforma')
					->setFields("proformacode ind, proformadesc val")
					->setOrderBy("val")
					->runSelect()
					->getResult();
		
		return $result;
	}

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
		
		// var_dump($this->db->buildSelect());

		return $result;
	}

	public function retrieveDataPagination($table, $fields = array(), $cond = "", $join = "", $orderby = "", $groupby = "")
	{
		$result = $this->db->setTable($table)
					->setFields($fields)
					->leftJoin($join)
					->setGroupBy($groupby)
					->setWhere($cond)
					// ->setOrderBy($orderby)
					->runPagination();
					// echo $this->db->getQuery();
		return $result;
	}
	
	public function retrieveEditData($sid)
	{
		$setFields = "voucherno, transactiondate, vendor, referenceno, particulars, netamount, exchangerate, convertedamount, paymenttype";
		$cond = "voucherno = '$sid'";
		
		$temp = array();

		// Retrieve Header
		$retrieveArrayMain =  $this->db->setTable('paymentvoucher')
									->setFields($setFields)
									->setWhere($cond)
									->setLimit('1')
									->runSelect()
									->getRow();

		$temp["main"] = $retrieveArrayMain;

		// Retrieve Vendor Details
		$vendor_code = $temp["main"]->vendor;
		$custFields = "address1, tinno, terms, email, CONCAT( first_name, ' ', last_name ), partnername AS name";
		$cust_cond = "partnercode = '$vendor_code'";

		$custDetails = $this->db->setTable('partners')
							->setFields($custFields)
							->setWhere($cust_cond)
							->setLimit('1')
							->runSelect()
							->getRow();
		
		$temp["cust"] = $custDetails;

		// Retrieve Details
		$detailFields = "main.accountcode, chart.accountname, main.detailparticulars, main.debit, SUM(main.credit) credit";
		$detail_cond  = "main.voucherno = '$sid' AND main.stat != 'temporary'";
		$orderby 	  = "main.linenum";	
		$detailJoin   = "chartaccount as chart ON chart.id = main.accountcode AND chart.companycode = main.companycode";
		$groupby      = "main.accountcode";

		$retrieveArrayDetail = $this->db->setTable('pv_details as main')
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

		

		/*
		SELECT app.voucherno, main.transactiondate, detail.accountcode, chart.accountname, main.wtaxcode, ftax.shortname, main.paymenttype, main.referenceno, app.amount, app.stat, main.checkdate, main.atcCode, main.particulars, detail.checkstat, app.discount, app.exchangerate, SUM(app.convertedamount) convertedamount, app.apvoucherno, SUM(detail.credit) sum 
		FROM pv_application as app 
		LEFT JOIN paymentvoucher as main ON main.voucherno = app.voucherno AND main.companycode = app.companycode 
		LEFT JOIN pv_details as detail ON detail.apvoucherno = app.apvoucherno AND detail.companycode = app.companycode 
		LEFT JOIN chartaccount as chart ON chart.id = detail.accountcode AND chart.companycode = detail.companycode 
		LEFT JOIN fintaxcode as ftax ON ftax.fstaxcode = main.wtaxcode AND ftax.companycode = main.companycode 
		WHERE app.voucherno = 'PV0000000007' AND app.stat != 'cancelled' AND detail.credit != 0 AND app.companycode = 'CID' 
		*/
		
		// Retrieve Payments
		$applicationFields = "app.voucherno,main.transactiondate,detail.accountcode,chart.accountname,main.wtaxcode,ftax.shortname,main.paymenttype,main.referenceno,app.amount,app.stat,main.checkdate,main.atcCode,main.particulars,detail.checkstat,app.discount,app.exchangerate, app.convertedamount AS convertedamount, app.apvoucherno, detail.credit AS sum";

		$appJoin_pv  = "paymentvoucher as main ON main.voucherno = app.voucherno AND main.companycode = app.companycode";
		$appJoin_pvd = "pv_details as detail ON detail.apvoucherno = app.apvoucherno AND detail.companycode = app.companycode";
		$appJoin_ca  = "chartaccount as chart ON chart.id = detail.accountcode AND chart.companycode = detail.companycode";
		$appJoin_fin = "fintaxcode as ftax ON ftax.fstaxcode = main.wtaxcode AND ftax.companycode = main.companycode";

		$app_cond 	 = "app.voucherno = '$sid' AND app.stat != 'cancelled' AND detail.credit != 0";

		$applicationArray = $this->db->setTable('pv_application as app')
									->setFields($applicationFields)
									->leftJoin($appJoin_pv)
									->leftJoin($appJoin_pvd)
									->leftJoin($appJoin_ca)
									->leftJoin($appJoin_fin)
									->setWhere($app_cond)
									->runSelect()
									->getResult();
									// ->buildSelect();
		
		// var_dump($applicationArray);
		
		$temp["payments"] = $applicationArray;

		// Received Cheques
		$sub_select = $this->db->setTable("pv_application AS app")
							   ->setFields("app.voucherno")
							   ->setWhere("app.voucherno = '$sid'")
							   ->buildSelect();

		$chequeFields = 'voucherno, chequeaccount, chequenumber, chequedate, chequeamount, chequeconvertedamount';
		$cheque_cond  = "voucherno IN($sub_select)";

		$chequeArray  = $this->db->setTable('pv_cheques')
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
				$pvno					= $chequeArray[$c]->voucherno;
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
				
				$rollArray[$pvno][]				= $rollArray1;
			}
		}
		
		$temp["rollArray"] = $rollArray;

		// Received Cheques for View
		$chequeFieldsv = 'pvc.voucherno, pvc.chequeaccount, chart.accountname, pvc.chequenumber, pvc.chequedate, pvc.chequeamount, pvc.chequeconvertedamount';
		$cheque_condv  = "pvc.voucherno = '$sid'";
		$cheque_joinv  = "chartaccount chart ON chart.id = pvc.chequeaccount AND chart.companycode = pvc.companycode";
		
		$chequeArrayv  = $this->db->setTable('pv_cheques AS pvc')
									->setFields($chequeFieldsv)
									->leftJoin($cheque_joinv)
									->setWhere($cheque_condv)
									->runSelect()
									->getResult();
		
		$rollArrayv	  = array();
	
		if(!empty($chequeArrayv))
		{
			for($c = 0; $c < count($chequeArrayv); $c++)
			{
				$pvno					= $chequeArrayv[$c]->voucherno;
				$accountname			= $chequeArrayv[$c]->accountname;
				$chequeaccount			= $chequeArrayv[$c]->chequeaccount;
				$chequenumber			= $chequeArrayv[$c]->chequenumber; 
				$chequedate				= $chequeArrayv[$c]->chequedate; 
				$chequedate				= $this->date->dateFormat($chequedate);
				$chequeamount			= $chequeArrayv[$c]->chequeamount;
				$chequeconvertedamount	= $chequeArrayv[$c]->chequeconvertedamount;

				$rollArray2['accountname']			= $accountname;
				$rollArray2['chequeaccount']		= $chequeaccount;
				$rollArray2['chequenumber']			= $chequenumber;
				$rollArray2['chequedate']			= $chequedate;
				$rollArray2['chequeamount']			= $chequeamount;
				$rollArray2['chequeconvertedamount'] = $chequeconvertedamount;
				
				$rollArrayv[$pvno][]				= $rollArray2;
			}
		}
		
		$temp["rollArrayv"] = $rollArrayv;


		return $temp;
	}

	public function retrievePayments($voucherno)
	{
		$voucherno = (isset($voucherno) && !empty($voucherno)) ? $voucherno : "";

		$temp 	   = array();

		// Retrieve Payments
		$applicationFields = "app.voucherno,main.transactiondate,detail.accountcode,chart.accountname,main.wtaxcode,ftax.shortname,main.paymenttype,main.referenceno,app.amount,app.stat,main.checkdate,main.atcCode,main.particulars,detail.checkstat,app.discount,app.exchangerate,app.convertedamount,app.apvoucherno";

		$appJoin_pv  = "paymentvoucher as main ON main.voucherno = app.voucherno AND main.companycode = app.companycode";
		$appJoin_pvd = "pv_details as detail ON detail.voucherno = app.voucherno AND detail.companycode = app.companycode";
		$appJoin_ca  = "chartaccount as chart ON chart.id = detail.accountcode AND chart.companycode = detail.companycode";
		$appJoin_fin = "fintaxcode as ftax ON ftax.fstaxcode = main.wtaxcode AND ftax.companycode = main.companycode";

		$app_cond 	 = "app.voucherno = '$voucherno' AND detail.linenum = '1' AND app.stat != 'cancelled'";

		$applicationArray = $this->db->setTable('pv_application as app')
									->setFields($applicationFields)
									->leftJoin($appJoin_pv)
									->leftJoin($appJoin_pvd)
									->leftJoin($appJoin_ca)
									->leftJoin($appJoin_fin)
									->setWhere($app_cond)
									->runPagination();
									// ->runSelect()
									// ->getResult();
		
		// echo $this->db->buildSelect();
		
		$temp["payments"] = $applicationArray;

		// Received Cheques
		$sub_select = $this->db->setTable("pv_application AS app")
							   ->setFields("app.voucherno")
							   ->setWhere("app.apvoucherno = '$voucherno'")
							   ->buildSelect();

		$chequeFields = 'voucherno, chequeaccount, chequenumber, chequedate, chequeamount, chequeconvertedamount';
		$cheque_cond  = "voucherno IN($sub_select)";

		$chequeArray  = $this->db->setTable('pv_cheques')
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
				$pvno					= $chequeArray[$c]->voucherno;
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
				
				$rollArray[$pvno][]				= $rollArray1;
			}
		}
		
		$temp["rollArray"] = $rollArray;

		return $temp;
	}

	public function retrieveList($data)
	{
		$daterangefilter = isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$vendfilter      = isset($data['vendfilter']) ? htmlentities($data['vendfilter']) : ""; 
		$addCond         = isset($data['addCond']) ? htmlentities($data['addCond']) : "";
		$searchkey 		 = isset($data['search']) ? htmlentities($data['search']) : "";
		$sort 		 	 = isset($data['sort']) ? htmlentities($data['sort']) : "main.transactiondate";

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? $this->date->dateDbFormat($datefilterArr[0]) : ""; 
		$datefilterTo		= (!empty($datefilterArr[1])) ? $this->date->dateDbFormat($datefilterArr[1]) : "";

		$add_query 	= (!empty($searchkey)) ? "AND (main.voucherno LIKE '%$searchkey%' OR main.referenceno LIKE '%$searchkey%' OR p.partnername LIKE '%$searchkey%' OR pv.voucherno LIKE '%$searchkey%' ) " : "";
		$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND pv.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
		$add_query .= (!empty($vendfilter) && $vendfilter != '') ? "AND p.partnercode = '$vendfilter' " : "";

		/* var_dump of query
		
			SELECT main.voucherno as voucherno, main.transactiondate as transactiondate, SUM(main.convertedamount) as amount, SUM(main.balance) as balance, CONCAT( first_name, ' ', last_name ), main.referenceno as referenceno, p.partnername AS partnername, pv.voucherno AS pv_voucherno, pv.transactiondate as pvtransdate 
			FROM accountspayable as main 
			LEFT JOIN partners p ON p.partnercode = main.vendor 
			LEFT JOIN pv_application pv ON main.voucherno = pv.apvoucherno AND pv.stat = 'posted' 
			WHERE main.stat = 'posted' AND (main.voucherno LIKE '%PV0000000013%' OR main.referenceno LIKE '%PV0000000013%' OR pv.voucherno LIKE '%PV0000000013%' OR p.partnername LIKE '%PV0000000013%' ) AND pv.transactiondate BETWEEN '2017-07-01' AND '2017-07-31' AND main.convertedamount = 
			(
				SELECT COALESCE(SUM(pv.convertedamount), 0) + COALESCE(SUM(pv.discount), 0) - COALESCE(SUM(pv.forexamount), 0) 
				FROM pv_application AS pv 
				WHERE pv.apvoucherno = main.voucherno and pv.stat = 'posted' AND  pv.companycode = 'CID' 
			) OR
			(
				SELECT COALESCE(SUM(pv.convertedamount), 0) + COALESCE(SUM(pv.discount), 0) - COALESCE(SUM(pv.forexamount), 0) 
				FROM pv_application AS pv 
				WHERE pv.apvoucherno = main.voucherno and pv.stat = 'posted' AND  pv.companycode = 'CID'
			) > 0 AND main.convertedamount >
			(
				SELECT COALESCE(SUM(pv.convertedamount), 0) + COALESCE(SUM(pv.discount), 0) - COALESCE(SUM(pv.forexamount), 0) 
				FROM pv_application AS pv 
				WHERE pv.apvoucherno = main.voucherno and pv.stat = 'posted' AND  pv.companycode = 'CID' 
			)
			AND  main.companycode = 'CID'  
			GROUP BY pv.voucherno
			ORDER BY main.transactiondate asc
		*/

		// Sub Select for Paid
		$table_pv     = "pv_application AS pv";
		$pv_fields    = "COALESCE(SUM(pv.convertedamount),0) + COALESCE(SUM(pv.discount),0) - COALESCE(SUM(pv.forexamount),0)";
		$pv_cond      = "pv.apvoucherno = main.voucherno and pv.stat = 'posted'";
		$sub_select_paid   = $this->db->setTable($table_pv)
							->setFields($pv_fields)
							->setWhere($pv_cond)
							->buildSelect();
		
		// Sub Select for Partial
		$sub_select_partial_bal = $this->db->setTable($table_pv)
										->setFields($pv_fields)
										->setWhere($pv_cond)
										->buildSelect();
		
		$sub_select_partial_mainbal = $this->db->setTable($table_pv)
										->setFields($pv_fields)
										->setWhere($pv_cond)
										->buildSelect();

		$addCondition = "AND main.convertedamount = ($sub_select_paid) OR ($sub_select_partial_bal) > 0 AND main.convertedamount > ($sub_select_partial_mainbal) ";

		$add_query .= $addCondition;

		$main_fields = array("main.voucherno as voucherno", "main.transactiondate as transactiondate", "SUM(main.convertedamount) as amount","SUM(main.balance) as balance", "CONCAT( first_name, ' ', last_name )","main.referenceno as referenceno", "p.partnername AS partnername", "pv.voucherno AS pv_voucherno", "pv.transactiondate as pvtransdate");
		$main_join   = "partners p ON p.partnercode = main.vendor";
		$pv_join 	 = "pv_application pv ON main.voucherno = pv.apvoucherno AND pv.stat = 'posted'";
		$main_table  = "accountspayable as main";
		$main_cond   = "main.stat = 'posted' $add_query";
		$groupby 	 = "pv.voucherno";

		$query 		 = $this->db->setTable($main_table)
								->setFields($main_fields)
								->leftJoin($main_join)
								->leftJoin($pv_join)
								->setWhere($main_cond)
								->setOrderBy($sort)
								->setGroupBy($groupby)
								// ->buildSelect();
								->runPagination();

		// var_dump($query);

		return $query;

	}

	public function retrieveAPList($data,$search)
	{
		$vendorcode = (isset($data["vendor"]) && !empty($data["vendor"])) ? $data["vendor"]         : "";
		$voucherno  = (isset($data["voucherno"]) && !empty($data["voucherno"])) ? $data["voucherno"]: "";
		$tempArr    = array();
		$search_key = '';

		if ($search) {
			$search_key .= ' AND ' . $this->generateSearch($search, array("main.voucherno", "main.transactiondate ", "main.convertedamount ", "main.balance ", "p.partnername ", "main.referenceno "));
		}

		// Sub Select
		$table_pv  = "pv_application AS pv";
		$pv_fields = "COALESCE(SUM(pv.convertedamount),0) + COALESCE(SUM(pv.discount),0) - COALESCE(SUM(pv.forexamount),0)";
		$pv_cond   = "pv.apvoucherno = main.voucherno AND pv.stat IN('posted')"; //'temporary'
	
		// Main Queries
		$main_table   = "accountspayable as main";
		$main_fields  = array("main.voucherno as voucherno", "main.transactiondate as transactiondate", "main.convertedamount as amount", "main.balance as balance", "p.partnername AS vendor_name", "main.referenceno as referenceno");
		$main_join 	  = "partners p ON p.partnercode = main.vendor ";
		$orderby  	  = "main.transactiondate DESC";

		if($vendorcode && empty($voucherno))
		{
			$sub_select = $this->db->setTable($table_pv)
							   ->setFields($pv_fields)
							   ->setWhere($pv_cond)
							   ->buildSelect();
			
			$addCondition = "AND ($sub_select) = 0 OR ($sub_select) > 0 AND main.convertedamount > ($sub_select)";

			$main_cond    = "main.stat = 'posted'  AND main.vendor = '$vendorcode' $search_key $addCondition ";
			
			$query = $this->retrieveDataPagination($main_table, $main_fields, $main_cond, $main_join, $orderby);

			$tempArr["result"] = $query;

		}
		else if($voucherno)
		{
			// echo "else";

			// Display Paid AP
			$sub_select = $this->db->setTable($table_pv)
								->setFields($pv_fields)
								->setWhere($pv_cond)
								->buildSelect();

			$addCondition	= "AND main.convertedamount = ($sub_select AND pv.voucherno = '$voucherno') OR ($sub_select AND pv.voucherno = '$voucherno') > 0";

			$main_cond    = "main.stat = 'posted' AND main.vendor = '$vendorcode' $addCondition ";

			$query = $this->retrieveDataPagination($main_table, $main_fields, $main_cond, $main_join, $orderby);
			

			$tempArr["result"] = $query;

			// var_dump($query);

			/* Query for Paid and Partial
			SELECT main.voucherno as voucherno, main.transactiondate as transactiondate, main.convertedamount as amount, main.balance as balance, p.partnername AS vendor_name, main.referenceno as referenceno 
			FROM accountspayable as main 
			LEFT JOIN partners p ON p.partnercode = main.vendor 
			WHERE main.stat = 'posted' AND main.vendor = 'SUP_003' AND main.convertedamount = 
			(
				SELECT COALESCE(SUM(pv.convertedamount), 0) + COALESCE(SUM(pv.discount), 0) - COALESCE(SUM(pv.forexamount), 0) 
				FROM pv_application AS pv 
				WHERE pv.apvoucherno = main.voucherno AND pv.stat IN('posted') AND  pv.companycode = 'CID'  AND pv.voucherno = 'PV0000000006'
			) OR 
			(
				SELECT COALESCE(SUM(pv.convertedamount), 0) + COALESCE(SUM(pv.discount), 0) - COALESCE(SUM(pv.forexamount), 0) 
				FROM pv_application AS pv 
				WHERE pv.apvoucherno = main.voucherno AND pv.stat IN('posted') AND  pv.companycode = 'CID'  AND pv.voucherno = 'PV0000000006'
			) > 0 AND  main.companycode = 'CID' 
			*/
		}
		
		return $query;
	}

	public function retrievePVDetails($data)
	{
		$cond  		= (isset($data["cond"]) && !empty($data["cond"])) ? $data["cond"]: "";
		$vendorcode = (isset($data["vendor"]) && !empty($data["vendor"])) ? $data["vendor"]: "";

		/*
			SELECT main.voucherno as voucherno, main.transactiondate as transactiondate, main.convertedamount as amount, main.balance as balance, p.partnername AS vendor_name, main.referenceno as referenceno, apd.accountcode, chart.accountclasscode, SUM(apd.credit), apd.debit, apd.detailparticulars 
			FROM accountspayable as main 
			LEFT JOIN ap_details AS apd ON main.voucherno = apd.voucherno AND main.companycode = apd.companycode
			LEFT JOIN chartaccount AS chart ON apd.accountcode = chart.id AND chart.companycode = apd.companycode
			LEFT JOIN partners p ON p.partnercode = main.vendor 
			WHERE main.stat = 'posted' AND main.vendor = 'SUP_003' AND main.companycode = 'CID' AND chart.accountclasscode = "ACCPAY" AND apd.voucherno IN("AP0000000002", "AP0000000003")
			GROUP BY apd.accountcode
			ORDER BY main.transactiondate DESC
		*/

		// Main Queries
		$main_table   = "accountspayable as main";
		$main_fields  = array("main.voucherno as voucherno", "main.transactiondate as transactiondate", "main.convertedamount as amount", "main.balance as balance", "p.partnername AS vendor_name", "main.referenceno as referenceno", "apd.accountcode", "chart.accountclasscode", "SUM(apd.credit) AS sumcredit", "apd.detailparticulars");
		$apd_join 	  = "ap_details AS apd ON main.voucherno = apd.voucherno AND main.companycode = apd.companycode";
		$chart_join   = "chartaccount AS chart ON apd.accountcode = chart.id AND chart.companycode = apd.companycode";
		$main_join 	  = "partners p ON p.partnercode = main.vendor";
		$main_cond 	  = "main.stat = 'posted' AND main.vendor = '$vendorcode' AND chart.accountclasscode = 'ACCPAY' AND apd.voucherno $cond";
		$groupby 	  = "apd.accountcode";
		$orderby  	  = "main.transactiondate DESC";

		$query = $this->db->setTable($main_table)
							->setFields($main_fields)
							->leftJoin($apd_join)
							->leftJoin($chart_join)
							->leftJoin($main_join)
							->setGroupBy($groupby)
							->setWhere($main_cond)
							->setOrderBy($orderby)
							->runSelect()
							->getResult();
							// echo $this->db->getQuery();
							// ->buildSelect();
		
		// var_dump($query);

		return $query;
	}

	public function buildQuery($table, $fields = array(), $cond = "", $join)
	{	
		$sub_select = $this->db->setTable($table)
							   ->setFields($fields)
							   ->leftJoin($join)
							   ->setWhere($cond)
							   ->buildSelect();
		
		return $sub_select;
	}

	public function retrievePaymentDetails($voucherno)
	{
		/*
			SELECT app.apvoucherno as sourceno, main.checknumber, ap.referenceno, main.particulars as remarks, app.amount as amount, main.paymenttype 
			FROM pv_application as app 
			LEFT JOIN accountspayable as ap ON ap.voucherno = app.apvoucherno AND ap.companycode = app.companycode 
			LEFT JOIN paymentvoucher as main ON main.voucherno = app.voucherno AND main.companycode = app.companycode 
			WHERE app.voucherno = 'PV0000000014' AND app.companycode = 'CID' 
			ORDER BY app.linenum

		*/

		$paymentFields		= array("app.apvoucherno as sourceno","main.checknumber","ap.referenceno","main.particulars as remarks","app.amount as amount","main.paymenttype");
		$paymentJoin		= "accountspayable as ap ON ap.voucherno = app.apvoucherno AND ap.companycode = app.companycode";
		$paymentJoin_ 		= "paymentvoucher as main ON main.voucherno = app.voucherno AND main.companycode = app.companycode"; 
		$paymentCondition 	= "app.voucherno = '$voucherno'";
		$paymentOrderBy 	= "app.linenum";


		$result = $this->db->setTable("pv_application as app")
					->setFields($paymentFields)
					->leftJoin($paymentJoin)
					->leftJoin($paymentJoin_)
					->setWhere($paymentCondition)
					->setOrderBy($paymentOrderBy)
					->runSelect()
					->getResult();
					// ->buildSelect();

		// var_dump($result);
		
		return $result;
	}

	public function getValue($table, $cols = array(), $cond = "", $orderby = "", $bool = "", $groupby = "")
	{
		$result = $this->db->setTable($table)
					->setFields($cols)
					->setWhere($cond)
					->setOrderBy($orderby)
					->setGroupBy($groupby)
					->runSelect($bool)
					->getResult();
					// echo $this->db->getQuery();
					// ->buildSelect();

		// var_dump($result);

		return $result;
	}

	public function insert_detail_entry($voucherno, $post_detail)
	{
		$accountcode    = $post_detail["accountcode"];
		$linenum 	    = $post_detail["linenum"];
		$detailAppTable = "pv_details";
		$errmsg 		= "";

		// var_dump($post_detail);

		// pv_details
		$isAppDetailExist	= $this->getValue($detailAppTable, array("COUNT(*) AS count_pv"), "voucherno = '$voucherno' AND accountcode = '$accountcode' AND linenum = '$linenum'");

		if($isAppDetailExist[0]->count_pv > 0)
		{
			// echo "\n 4 \n";
		
			//pv_details
			$insertResult = $this->db->setTable($detailAppTable) 
								->setValues($post_detail)
								->setWhere("voucherno = '$voucherno' AND accountcode = '$accountcode' AND linenum = '$linenum'")
								// ->buildUpdate();
								->runUpdate();
			
			// var_dump($insertResult);

			if(!$insertResult)
				$errmsg = "<li>Updating PV Details.</li>";
		}
		else
		{
			// echo "\n 5 \n";

			//pv_details
			$insertResult = $this->db->setTable($detailAppTable) 
								->setValues($post_detail)
								// ->buildInsert();
								->runInsert();
			
			// var_dump($insertResult);
			
			if(!$insertResult)
				$errmsg = "<li>Saving Payment Voucher Details.</li>";
		}

		return $errmsg;
	}

	public function applyPayments_($data)
	{
		$errmsg				   = array();
		$seq 				   = new seqcontrol();
		$datetime			   = date("Y-m-d H:i:s");

		/**SET TABLES**/
		$mainAppTable		   = "paymentvoucher"; 
		$detailAppTable		   = "pv_details";
		$applicationTable	   = "pv_application"; 
		$chequeTable		   = "pv_cheques"; 
		$applicableHeaderTable = "accountspayable"; 
		$applicableDetailTable = "ap_details"; 

		$source				   = "PV"; 

		$continue_flag		   = 1;
		$insertResult		   = 0;
	
		$voucherno				= (isset($data['h_voucher_no']) && (!empty($data['h_voucher_no']))) ? htmlentities(addslashes(trim($data['h_voucher_no']))) : "";
		$vendor				= (isset($data['vendor']) && (!empty($data['vendor']))) ? htmlentities(addslashes(trim($data['vendor']))) : "";
		$referenceno		= (isset($data['paymentreference']) && (!empty($data['paymentreference']))) ? htmlentities(addslashes(trim($data['paymentreference']))) : "";
		$transactiondate	= (isset($data['document_date']) && (!empty($data['document_date']))) ? htmlentities(addslashes(trim($data['document_date']))) : "";
		$remarks			= (isset($data['remarks']) && (!empty($data['remarks']))) ? htmlentities(addslashes(trim($data['remarks']))) : "";
		$totalamount		= (isset($data['total_debit']) && (!empty($data['total_debit']))) ? htmlentities(addslashes(trim($data['total_debit']))) : "";
		$paymenttype		= (isset($data['paymentmode']) && (!empty($data['paymentmode']))) ? htmlentities(addslashes(trim($data['paymentmode']))) : "";
		$total_debit		= (isset($data['total_debit']) && (!empty($data['total_debit']))) ? htmlentities(addslashes(trim($data['total_debit']))) : "";
		$total_credit		= (isset($data['total_credit']) && (!empty($data['total_credit']))) ? htmlentities(addslashes(trim($data['total_credit']))) : "";
		$total_payment		= (isset($data['total_payment']) && (!empty($data['total_payment']))) ? htmlentities(addslashes(trim($data['total_payment']))) : $total_debit;
		$task 				= (isset($data['h_task']) && (!empty($data['h_task']))) ? htmlentities(addslashes(trim($data['h_task']))) : "";
		$h_check_rows 		= (isset($data['h_check_rows']) && (!empty($data['h_check_rows']))) ? htmlentities(addslashes(trim($data['h_check_rows']))) : "";
		$invoice_data  		= str_replace('\\', '', $h_check_rows);
		$invoice_data  		= html_entity_decode($invoice_data);
		$decode_json   		= json_decode($invoice_data, true);

		$exchangerate		= (!empty($data['paymentrate'])) ? $data['paymentrate'] : "1.00";
		$convertedamount	= (!empty($data['paymentconverted'])) ? $data['paymentconverted'] : $total_payment;
		$checkdate			= (!empty($data['checkdate'])) ? $data['checkdate'] : "0000-00-00";

		/**TRIM COMMAS FROM AMOUNTS**/
		$totalamount		= str_replace(',','',$totalamount);
		$total_payment		= str_replace(',','',$total_payment);
		$convertedamount 	= str_replace(',','',$convertedamount);
		$total_debit 		= str_replace(',','',$total_debit);
		$total_credit 		= str_replace(',','',$total_credit);
		
		// Decode JSON data to get the selected AP and amount
		$invoice_ = array();
		$amount_ = array();

		for($i = 0; $i < count($decode_json); $i++)
		{
			$invoice_[$i + 1] = $decode_json[$i]["apvoucher"];
			$amount_[$i + 1]  = $decode_json[$i]["amount"];

			$data["invoice_modal_"] = $invoice_;
			$data["pay_amount_"] = $amount_;
		}
		
		// var_dump($data);

		/**CLEAN PASSED DATA**/
		foreach($data as $postIndex => $postValue)
		{
			// echo "\n" . $postIndex . " ";
			// var_dump($postValue);

			if(($postIndex == 'invoice_modal_' || $postIndex=='accountcode' || $postIndex=='pay_amount_' || (array) $postIndex=='paymentdiscount' || (array) $postIndex=='paymentconverted' || (array) $postIndex=='paymentrate' ||  $postIndex=='detailparticulars' || $postIndex=='debit' || $postIndex=='credit' || $postIndex == "paid_amount") && !empty($postValue))
			{
				$a		= '';

				// echo "\n" . $postIndex . " ";
				// var_dump($postValue);

				foreach($postValue as $postValueIndex => $postValueIndexValue)
				{
					// echo "\n" . $postValueIndex . " ";
					// var_dump($postValueIndexValue);

					if($postIndex == 'pay_amount_' || $postIndex == 'paymentdiscount' || $postIndex == 'paymentconverted' || $postIndex == 'paymentrate' || $postIndex == 'debit' || $postIndex == 'credit' || $postIndex == "paid_amount")
					{
						$a = str_replace(',', '', $postValueIndexValue);
					}
					else if($postIndex == 'paymentdate' || $postIndex == 'checkdate')
					{
						$a = ($postValueIndexValue != '') ? date("Y-m-d", strtotime($postValueIndexValue)) : "0000-00-00";
					}
					else
					{
						$a = htmlentities(addslashes(trim($postValueIndexValue)));
					}
				
					$arrayData[$postIndex][$postValueIndex] = $a;	
				}	
			}
			
			if(($postIndex == 'chequeaccount' || $postIndex == 'chequenumber' || $postIndex == 'chequedate' || $postIndex == 'chequeamount' || $postIndex == 'chequeconvertedamount') && !empty($postValue) )
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
						$b = ($postValueIndexValue != '') ? date("Y-m-d", strtotime($postValueIndexValue)) : "0000-00-00";
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

		// var_dump($tempArray);
		
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
				$chequeaccount			= (!empty($newArrayValue['chequeaccount'])) ? $newArrayValue['chequeaccount'] : "";
				$chequenumber			= (!empty($newArrayValue['chequenumber'])) ? $newArrayValue['chequenumber'] : "";
				$chequedate				= (!empty($newArrayValue['chequedate'])) ? $newArrayValue['chequedate'] : "";
				$chequeamount			= (!empty($newArrayValue['chequeamount'])) ? $newArrayValue['chequeamount'] : "";
				// $chequeconvertedamount	= (!empty($newArrayValue['chequeconvertedamount'])) ? $newArrayValue['chequeconvertedamount'] : "";
				$chequedate				= $this->date->dateDbFormat($chequedate);
				
				if(!empty($chequedate) && !empty($chequeaccount) && !empty($chequenumber) && !empty($chequeamount))
				{
					$cheque_header['voucherno']				= $voucherno;
					$cheque_header['transtype']				= "PV";
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

		$isExist			= $this->getValue($mainAppTable, array("stat"), "voucherno = '$voucherno' AND stat = 'posted'");
		$status				= (!empty($isExist[0]->stat)) ? "posted" : "temporary";
		$valid 				= 0;

		$transactiondate				= $this->date->dateDbFormat($transactiondate); 
		$period							= date("n",strtotime($transactiondate));
		$fiscalyear						= date("Y",strtotime($transactiondate));

		// Start Header
		$post_header['voucherno']		= $voucherno;
		$post_header['vendor']			= $vendor;
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
			// paymentvoucher
			$this->db->setTable($mainAppTable)
					->setWhere("voucherno = '$voucherno'");
					// ->buildDelete();
			$insertResult = $this->db->runDelete();

			if(!$insertResult)
				$valid++;
			
			// paymentvoucher
			$this->db->setTable($mainAppTable) 
				->setValues($tempData);
				// ->buildInsert();

			$insertResult = ($valid == 0) ? $this->db->runInsert() : false;

			if(!$insertResult)
				$errmsg[] = "<li>Saving Payment Voucher Header.</li>";
		}
		else
		{
			// echo "\n2\n";

			$insertResult = $this->db->setTable($mainAppTable)
								->setValues($tempData)
								->setWhere("voucherno = '$voucherno'")
								->runUpdate();
								// ->buildUpdate();

			// var_dump($insertResult);

			if(!$insertResult)
				$errmsg[] = "<li>Updating Payment Voucher Header.</li>";
		}

		$counter = 0;

		$arrDB = array();
		$arrCB = array();

		foreach($tempArray as $tempArrayIndex => $tempArrayValue)
		{
			$accountcode 		= (!empty($tempArrayValue['accountcode'])) ? $tempArrayValue['accountcode'] : "";

			if($task == "create")
				$amount = (!empty($tempArrayValue['pay_amount_'])) ? $tempArrayValue['pay_amount_'] : "";
			else
				$amount = (!empty($tempArrayValue['paid_amount'])) ? $tempArrayValue['paid_amount'] : "";

			
			$convertedamount	= (!empty($tempArrayValue['paymentconverted'])) ? $tempArrayValue['paymentconverted'] : $amount;
		
			$exchangerate		= (!empty($tempArrayValue['paymentrate'])) ? $tempArrayValue['paymentrate'] : "1.00";
			$paymentdiscount 	= (!empty($tempArrayValue['paymentdiscount'])) ? $tempArrayValue['paymentdiscount'] : 0;

			// For PV Application
			$invoice			= (!empty($tempArrayValue['invoice_modal_'])) ? $tempArrayValue['invoice_modal_'] : "";
			$pay_amount			= (!empty($tempArrayValue['pay_amount_'])) ? $tempArrayValue['pay_amount_'] : "";

			// For PV Details
			$detailparticulars	= (!empty($tempArrayValue['detailparticulars'])) ? $tempArrayValue['detailparticulars'] : "";
			$debit			    = (!empty($tempArrayValue['debit'])) ? $tempArrayValue['debit'] : "";
			$credit			    = (!empty($tempArrayValue['credit'])) ? $tempArrayValue['credit'] : "";

			// accountspayable
			$payablerate		= $this->getValue($applicableHeaderTable, array("exchangerate"),"voucherno = '$invoice' AND stat = 'posted'"); 	
			$payablerate 		= (isset($payablerate[0]->exchangerate)) ? $payablerate[0]->exchangerate : "1.00";

			if(!empty($transactiondate) && !empty($paymenttype) && ( ($total_debit == $total_credit) || $paymentdiscount > 0))
			{
				$taxamount	      = 0;
				$tempDetail       = array();
				$discountaccount  = '120';

				// if(empty($tempArray['paymentconverted'])) will receive value of $amount
				$newamount			= $convertedamount - $paymentdiscount;
				$forexamount 		= 0;

				if($payablerate != $exchangerate)
				{
					// echo "if";
					$payableconverted	= $amount * $payablerate;
					$forexamount 		= $convertedamount - $payableconverted;
					$newamount			= ($forexamount > 0) ? $newamount - abs($forexamount) : $newamount + abs($forexamount);
					$forexaccount		= ($forexamount > 0) ? "208" : "209";
				}
				else
				{
					$payableconverted	= $convertedamount;
				}

				$creditamount		= ($newamount > 0) ? $convertedamount : abs($newamount);

				// For PV DETAILS
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

				if($paymenttype == 'cheque')
				{
					$other_detail = array();

					if($debit != 0)
					{
						$post_detail['linenum']				= $linenum;
						$post_detail['apvoucherno']			= $invoice;
						$post_detail['accountcode']			= $accountcode;
						$post_detail['debit']				= $debit;
						$post_detail['credit']  			= $credit;
						$post_detail['converteddebit']		= $post_detail['debit'];
						$post_detail['convertedcredit']		= $post_detail['credit'];
						$post_detail['detailparticulars']	= $detailparticulars;

						// $other_detail['linenum']			= $linenum;
						$other_detail['apvoucherno']		= $invoice;
						$other_detail['debit']				= $debit;
						$other_detail['converteddebit']		= $other_detail['debit'];

						$arrDB[] = $other_detail;

						// Start Insert for Debit
						$errmsg[] = $this->insert_detail_entry($voucherno, $post_detail);

						// var_dump($post_detail);
					} 
					else
					{

						$other_detail['linenum']			= $linenum;
						$other_detail['accountcode']		= $accountcode;
						$other_detail['credit']  			= $credit;
						$other_detail['convertedcredit']	= $other_detail['credit'];
						$other_detail['detailparticulars']	= $detailparticulars;

						$arrCB[] = $other_detail;

						// Insert for Credit
						for($i = 0; $i < count($arrDB); $i++)
						{
							for($j = 0; $j < count($arrCB); $j++)
							{
								$dis_credit                         = ($arrDB[$i]["debit"] / $total_debit) * $arrCB[$j]["credit"];

								$credit_detail["linenum"]           = ($arrCB[$j]["linenum"] + $i);
								$credit_detail["apvoucherno"]       = $arrDB[$i]["apvoucherno"];
								$credit_detail["accountcode"]       = $arrCB[$j]["accountcode"];
								$credit_detail["credit"]            = $dis_credit;
								$credit_detail["convertedcredit"]   = $dis_credit;
								$credit_detail["detailparticulars"] = $arrCB[$j]["detailparticulars"];

								$post_detail["linenum"] 			= $credit_detail["linenum"];
								$post_detail["apvoucherno"] 		= $credit_detail["apvoucherno"];
								$post_detail["accountcode"] 		= $credit_detail["accountcode"];
								$post_detail["credit"] 				= $credit_detail["credit"];
								$post_detail["convertedcredit"]     = $credit_detail["convertedcredit"];
								$post_detail["detailparticulars"]   = $credit_detail["detailparticulars"];

								// Start Insert for Credit
								if($dis_credit != 0)
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
							$post_detail['apvoucherno']		= $invoice;
							$post_detail['accountcode']		= $cheque_index;
							$post_detail['debit']			= 0;
							$post_detail['credit']			= $totalamount;
							
							$post_detail['converteddebit']	= $post_detail['debit'];
							$post_detail['convertedcredit']	= $post_detail['credit'];
							
							// pv_details
							$isAppDetailExist	= $this->getValue($detailAppTable, array("COUNT(*) AS count"),"voucherno = '$voucherno' AND accountcode = '$cheque_index' AND linenum = '$linenum'");
							
							// var_dump($post_detail);
						}
					}
				}
				else if($paymenttype == "cash")
				{
					if($paymentdiscount > 0 && $convertedamount == 0)
					{
						echo "\n 3 \n";
						// ap_details
						$accountcode = $this->getValue($applicableDetailTable, "accountcode", "voucherno = '$invoice' AND linenum = '1' LIMIT 1");
						$accountcode = $accountcode[0]->accountcode;
					}
					
					$other_detail = array();

					if($debit != 0)
					{
						$post_detail['linenum']				= $linenum;
						$post_detail['apvoucherno']			= $invoice;
						$post_detail['accountcode']			= $accountcode;
						$post_detail['debit']				= $debit;
						$post_detail['credit']  			= $credit;
						$post_detail['converteddebit']		= $post_detail['debit'];
						$post_detail['convertedcredit']		= $post_detail['credit'];
						$post_detail['detailparticulars']	= $detailparticulars;

						$other_detail['apvoucherno']		= $invoice;
						$other_detail['debit']				= $debit;
						$other_detail['converteddebit']		= $other_detail['debit'];

						$arrDB[] = $other_detail;

						// var_dump($post_detail);

						// Start Insert for Debit
						$errmsg[] = $this->insert_detail_entry($voucherno, $post_detail);
					} 
					else
					{

						$other_detail['linenum']			= $linenum;
						$other_detail['accountcode']		= $accountcode;
						$other_detail['credit']  			= $credit;
						$other_detail['convertedcredit']	= $other_detail['credit'];
						$other_detail['detailparticulars']	= $detailparticulars;

						$arrCB[] = $other_detail;

						// var_dump($other_detail);

						// Insert for Credit
						for($i = 0; $i < count($arrDB); $i++)
						{
							for($j = 0; $j < count($arrCB); $j++)
							{
								$dis_credit                         = ($arrDB[$i]["debit"] / $total_debit) * $arrCB[$j]["credit"];

								$credit_detail["linenum"]           = ($arrCB[$j]["linenum"] + $i) + 1;
								$credit_detail["apvoucherno"]       = $arrDB[$i]["apvoucherno"];
								$credit_detail["accountcode"]       = $arrCB[$j]["accountcode"];
								$credit_detail["credit"]            = $dis_credit;
								$credit_detail["convertedcredit"]   = $dis_credit;
								$credit_detail["detailparticulars"] = $arrCB[$j]["detailparticulars"];

								$post_detail["linenum"] 			= $credit_detail["linenum"];
								$post_detail["apvoucherno"] 		= $credit_detail["apvoucherno"];
								$post_detail["accountcode"] 		= $credit_detail["accountcode"];
								$post_detail["credit"] 				= $credit_detail["credit"];
								$post_detail["convertedcredit"]     = $credit_detail["convertedcredit"];
								$post_detail["detailparticulars"]   = $credit_detail["detailparticulars"];

								// Start Insert for Credit
								if($dis_credit != 0)
									$errmsg[] = $this->insert_detail_entry($voucherno, $post_detail);
								
							}
						}
					}

					// var_dump($post_detail);

					$linenum++;

				} // end else()

				/**DISCOUNT ACCOUNT**/
				if($paymentdiscount > 0)
				{
					echo "\n 8 \n";
					$post_detail['linenum']			= $linenum;
					$post_detail['accountcode']		= $discountaccount; // hardcoded
					$post_detail['debit']			= $paymentdiscount;
					$post_detail['credit']			= 0;

					$post_detail['converteddebit']	= $post_detail['debit'];
					$post_detail['convertedcredit']	= $post_detail['credit'];
					
					//pv_details
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
					echo "\n 9 \n";
					$post_detail['linenum']			= $linenum;
					$post_detail['accountcode']		= $forexaccount;
					$post_detail['debit']			= abs($forexamount);
					$post_detail['credit']			= 0;
					$post_detail['converteddebit']	= $post_detail['debit'];
					$post_detail['convertedcredit']	= $post_detail['credit'];
					
					//pv_details
					$this->db->setTable($detailAppTable) 
							->setValues($post_detail);
					
					$insertResult = false; //$this->db->runInsert();
					
					if(!$insertResult)
						$errmsg[] = "<li>Applying the Discount.</li>";

					$linenum++;
				}

				/**UPDATE APPLICATION TABLE**/
				$post_application['voucherno']		 = $voucherno;
				$post_application['transactiondate'] = $transactiondate;
				$post_application['transtype']		 = $source;
				$post_application['linenum']		 = $count;
				$post_application['apvoucherno']	 = $invoice;
				$post_application['discount']		 = $paymentdiscount;
				$post_application['amount']			 = $amount;
				$post_application['currencycode']	 = 'PHP';
				$post_application['exchangerate']	 = $exchangerate;
				$post_application['convertedamount'] = $convertedamount;
				$post_application['forexamount']	 = abs($forexamount);
				$post_application['stat']			 = $post_header['stat'];

				// pv_application
				$isAppDetailExist	= $this->getValue($applicationTable, array("COUNT(*) AS count"), "voucherno = '$voucherno' AND apvoucherno = '$invoice'");

				if($isAppDetailExist[0]->count > 0)
				{
					// echo "\n 10 \n";

					//pv_application
					$insertResult = $this->db->setTable($applicationTable) 
										->setValues($post_application)
										->setWhere("voucherno = '$voucherno' AND apvoucherno = '$invoice'")
										// ->buildUpdate();
										->runUpdate();

					// var_dump($insertResult);					

					if(!$insertResult)
						$errmsg[] = "<li>Updating Payment Voucher Application.</li>";

				}
				else if(!empty($isAppDetailExist) && !empty($invoice))
				{
					// echo "\n 11 \n";

					//pv_application
					$insertResult = $this->db->setTable($applicationTable) 
										->setValues($post_application)
										// ->buildInsert();
										->runInsert();
					
					// var_dump($insertResult);	
					
					if(!$insertResult)
						$errmsg[] = "<li>Updating Payment Voucher Application.</li>";
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

		// echo "\n 13 \n";
		// paymentvoucher
		$insertResult = $this->db->setTable($mainAppTable) 
							->setValues($update_info)
							->setWhere("voucherno = '$voucherno' AND stat = '$status'")
							// ->buildUpdate();
							->runUpdate();

		// var_dump($insertResult);

		if(!$insertResult)
			$errmsg[] = "<li>Updating Payment Voucher Header.</li>";
		
		/**INSERT TO CHEQUES TABLE**/
		if(strtolower($paymenttype) == 'cheque')
		{
			// echo "\n 16 \n";

			// Delete
			$insertResult = $this->db->setTable($chequeTable)
								->setWhere("voucherno = '$voucherno'")
								// ->buildDelete();
								->runDelete();
			
			// Insert
			if($insertResult && !empty($tempCheque))
			{
				// echo "\n 17 \n";

				$insertResult = $this->db->setTable($chequeTable)
									->setValues($tempCheque)
									// ->buildInsert();
									->runInsert();

				if(!$insertResult)
					$errmsg[] = "<li>Saving in Cheque Details.</li>";
			}
		}

		return $errmsg;

	} // end applyPayments_()

	public function saveDetails($table, $data, $form = "")
	{
		$result 				   = "";

		if($form == "vendordetail")
		{
			$data_insert["stat"]       = "active";
			$data_insert["terms"]      = $data["h_terms"];
			$data_insert["tinno"]      = $data["h_tinno"];
			$data_insert["address1"]   = $data["h_address1"];
		}
		// else if($form == "newVendor")
		// {
		// 	$data_insert["stat"]          = "active";
		// 	$data_insert["partnercode"]   = $data["partnercode"];
		// 	$data_insert["first_name"]    = $data["vendor_name"];
		// 	$data_insert["email"] 		  = $data["email"];
		// 	$data_insert["address1"]      = $data["address"];
		// 	$data_insert["businesstype"]  = $data["businesstype"];
		// 	$data_insert["tinno"]         = $data["tinno"];
		// 	$data_insert["terms"]  		  = $data["terms"];
		// 	$data_insert["partnertype"]   = "supplier";
		// 	$data_insert["autoap"]   	  = "Y";
		// 	$data_insert["currencycode"]  = "PHP";
		// }

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
			$generatedVoucher 	= $seq->getValue('AP');

			$period				= date("m",strtotime($docData[$i]['transactiondate']));
			$fiscalyear			= date("Y",strtotime($docData[$i]['transactiondate']));

			$voucherList[]		= $generatedVoucher;

			// Header accounts_payable
			$data_header["voucherno"]       = $generatedVoucher;
			$data_header["transactiondate"] = $docData[$i]['transactiondate'];
			$data_header["currencycode"] 	= "PHP";
			$data_header["referenceno"] 	= $docData[$i]['invoice'];
			$data_header["exchangerate"] 	= "1.00";
			$data_header["stat"]            = "posted";
			$data_header["transtype"]       = "AP";
			$data_header["invoicedate"]     = $docData[$i]['transactiondate'];
			$data_header["duedate"]    		= $docData[$i]['duedate'];
			$data_header["vendor"]    		= $docData[$i]['vendor'];
			$data_header["invoiceno"]    	= $docData[$i]['invoice'];
			$data_header["amount"] 			= $docData[$i]['amount'];
			$data_header["convertedamount"] = $docData[$i]['amount'];
			$data_header["period"] 			= $period;
			$data_header["fiscalyear"] 		= $fiscalyear;
			$data_header["terms"] 			= "0";
			$data_header["amountpaid"] 		= "0.00";
			$data_header["amountforpayment"] = "0.00";
			$data_header["particulars"]  	= $docData[$i]['notes'];
			$data_header["source"] 			= "AP";
			$data_header["balance"]  		= $docData[$i]['amount'];

			$tempHeader[] 					= $data_header;

			// Details Debit: ap_details
			$data_details["voucherno"] 		= $generatedVoucher;
			$data_details["transtype"]      = "AP";
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

			// Details Credit: ap_details
			$data_details["voucherno"] 		= $generatedVoucher;
			$data_details["transtype"]      = "AP";
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
		$insert_result = $this->db->setTable("accountspayable")
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
				$payableCount	= $this->getValue("accountspayable", array("COUNT(*) AS count"), 'companycode = "'.COMPANYCODE.'"');
				$payableCount 	= $payableCount[0]->count;

				// Delete Data
				$data_del["table"] = "accountspayable";
				$data_del["condition"] = "voucherno IN($voucherlist)";
				$this->deleteData($data_del);
				
				// Update
				$update_seq_info['current']	= ($payableCount - $x) + 1;
				
				$this->db->setTable("wc_sequence_control")
						->setValues($update_seq_info)
						->setWhere("code = 'AP'")
						->runUpdate();
			}
		}
		else
		{
			// Insert Details
			$insert_result = $this->db->setTable("ap_details")
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
					$payableCount	= $this->getValue("accountspayable", array("COUNT(*) AS count"), 'companycode = "'.COMPANYCODE.'"');
					$payableCount 	= $payableCount[0]->count;
					
					// Delete Data
					$data_del["table"] = "accountspayable";
					$data_del["condition"] = "voucherno IN($voucherlist)";
					$this->deleteData($data_del);

					// Delete Data
					$data_del["table"] = "ap_details";
					$data_del["condition"] = "voucherno IN($voucherlist)";
					$this->deleteData($data_del);

					// Update
					$update_seq_info['current']	= ($payableCount - $x) + 1;
					
					$this->db->setTable("wc_sequence_control")
							->setValues($update_seq_info)
							->setWhere("code = 'AP'")
							->runUpdate();
				
				}
			}
		}

		return $errmsg;
	}

	public function fileExport($data)
	{
		$daterangefilter = isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$vendfilter      = isset($data['vendfilter']) ? htmlentities($data['vendfilter']) : ""; 
		$addCond         = isset($data['addCond']) ? htmlentities($data['addCond']) : "";
		$searchkey 		 = isset($data['search']) ? htmlentities($data['search']) : "";

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";

		if($addCond	== 'paid')
		{
			$table_pv  = "pv_application AS pv";
			$pv_fields = "COALESCE(SUM(pv.amount),0) + COALESCE(SUM(pv.discount),0)";
			$pv_cond   = "pv.apvoucherno = main.voucherno and pv.stat = 'posted'";
			$sub_select = $this->db->setTable($table_pv)
							   ->setFields($pv_fields)
							   ->setWhere($pv_cond)
							   ->buildSelect();

			$addCondition	= "AND main.amount = ($sub_select)";

			// $addCondition	= " AND main.amount = (select COALESCE(SUM(pv.amount),0) + COALESCE(SUM(pv.discount),0) from pv_application as pv where pv.apvoucherno = main.voucherno and pv.stat = 'posted') ";

		}
		else if($addCond == 'partial')
		{
			$table_pv  = "pv_application AS pv";
			$pv_fields = "COALESCE(SUM(pv.amount),0) + COALESCE(SUM(pv.discount),0)";
			$pv_cond   = "pv.apvoucherno = main.voucherno and pv.stat = 'posted'";
			$sub_select = $this->db->setTable($table_pv)
							   ->setFields($pv_fields)
							   ->setWhere($pv_cond)
							   ->buildSelect();

			
			$pv_cond_   = "pv.apvoucherno = main.voucherno and pv.stat = 'posted'";
			$sub_select_ = $this->db->setTable($table_pv)
							   ->setFields($pv_fields)
							   ->setWhere($pv_cond_)
							   ->buildSelect();
			
			$addCondition = "AND ($sub_select) > 0 AND main.amount > ($sub_select_)";

			// $addCondition	= " AND (select COALESCE(SUM(pv.amount),0) + COALESCE(SUM(pv.discount),0) from pv_application as pv where pv.arvoucherno = main.voucherno and pv.stat = 'posted') > 0 AND main.amount > (select COALESCE(SUM(pv.amount),0) + COALESCE(SUM(pv.discount),0) from pv_application as pv where pv.arvoucherno = main.voucherno and pv.stat = 'posted') ";
		}
		else if($addCond == 'unpaid')
		{
			$table_pv  = "pv_application AS pv";
			$pv_fields = "COALESCE(SUM(pv.amount),0) + COALESCE(SUM(pv.discount),0)";
			$pv_cond   = "pv.apvoucherno = main.voucherno and pv.stat = 'posted'";
			$sub_select = $this->db->setTable($table_pv)
							   ->setFields($pv_fields)
							   ->setWhere($pv_cond)
							   ->buildSelect();
			
			$addCondition = "AND ($sub_select) = 0";

			// $addCondition	= " AND (select COALESCE(SUM(pv.amount),0) + COALESCE(SUM(pv.discount),0) from pv_application as pv where pv.arvoucherno = main.voucherno and pv.stat = 'posted') = 0 ";
		}
		else
		{
			$addCondition	= '';
		}

		
		$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND main.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
		$add_query .= (!empty($vendfilter) && $vendfilter != '') ? "AND p.partnercode = '$vendfilter' " : "";
		$add_query .= $addCondition;

		$main_fields = array("main.transactiondate as transactiondate", "main.voucherno as voucherno", "CONCAT( first_name, ' ', last_name ) AS vendor", "main.referenceno as referenceno", "main.amount as amount", "main.balance as balance", "main.particulars");

		$main_join   = "partners p ON p.partnercode = main.vendor"; //AND p.companycode
		$main_table  = "accountspayable as main";
		$main_cond   = "main.stat = 'posted' $add_query";
		$query 		 = $this->retrieveData($main_table, $main_fields, $main_cond, $main_join);

		return $query;
	}

	public function editData($data, $table, $cond)
	{
		$result = $this->db->setTable($table)
				->setValues($data)
				->setWhere($cond)
				// ->buildUpdate();
				->runUpdate();
		
		return $result;
	}

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

	public function deletePayments($voucher)
	{
		$update_info 	= array();
		$errmsg 	 	= array();

		$appTable		= "pv_application";
		$detailTable	= "pv_details";
		$mainTable		= "paymentvoucher";
		$table			= "accountspayable";
		$paymentField	= array('apvoucherno','amount','wtaxamount');
		
		$paymentArray   = $this->db->setTable($appTable)
							   ->setFields($paymentField)
							   ->setWhere("voucherno = '$voucher' AND stat = 'posted'")
							   ->runSelect()
							   ->getResult();

		if(!empty($paymentArray))
		{
			for($i = 0; $i < count($paymentArray); $i++)
			{
				$mainvoucher	= $paymentArray[$i]->apvoucherno;
				$amount			= $paymentArray[$i]->amount;
				$wtaxamount		= $paymentArray[$i]->wtaxamount;
				$discount		= 0;

				$balance		= $this->getValue($table, array("balance"), "voucherno = '$mainvoucher' AND stat = 'posted'");
				$balance 		= $balance[0]->balance;

				$update_info['balance']		= $balance + $amount + $discount;

				$amountpaid 	= $this->getValue($table, array("amountpaid"), "voucherno = '$mainvoucher' AND stat = 'posted'");
				$amountpaid 	= $amountpaid[0]->amountpaid;

				$update_info['amountpaid']	= $amountpaid - $amount - $discount;

				// Update accountspayable
				$result = $this->db->setTable($table)
							   ->setValues($update_info)
							   ->setWhere("voucherno = '$mainvoucher'")
							   ->runUpdate();
				
				if(!$result)
					$errmsg[] = "The system has encountered an error in updating Accounts Payable [$mainvoucher]. Please contact admin to fix this issue.";
				else
					$this->log->saveActivity("Update Accounts Payable [$mainvoucher]");
			}

			$update_info			= array();
			$update_info['stat']	= 'cancelled';
			
			// Update pv_application
			$result = $this->db->setTable($appTable)
					->setValues($update_info)
					->setWhere("voucherno = '$voucher'")
					->runUpdate();
			
			if(!$result)
				$errmsg[] = "The system has encountered an error in updating PV Application [$voucher]. Please contact admin to fix this issue.";
			else
				$this->log->saveActivity("Update PV Application [$voucher]");

			// Update pv_details
			$result = $this->db->setTable($detailTable)
					->setValues($update_info)
					->setWhere("voucherno = '$voucher'")
					->runUpdate();
			
			if(!$result)
				$errmsg[] = "The system has encountered an error in updating Payment Voucher Details [$voucher]. Please contact admin to fix this issue.";
			else
				$this->log->saveActivity("Update Payment Voucher Details [$voucher]");
			
			// Update paymentvoucher
			$result = $this->db->setTable($mainTable)
					->setValues($update_info)
					->setWhere("voucherno = '$voucher'")
					->runUpdate();	
			
			if(!$result)
				$errmsg[] = "The system has encountered an error in updating Payment Voucher [$voucher]. Please contact admin to fix this issue.";
			else
				$this->log->saveActivity("Update Payment Voucher [$voucher]");

			return $errmsg;
		}

	}

	// Retrieval for PV listing with tab filters: Unpaid Accounts, Partial, Paid, All
	public function retrieveList_($data)
	{
		$daterangefilter = isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$vendfilter      = isset($data['vendfilter']) ? htmlentities($data['vendfilter']) : ""; 
		$addCond         = isset($data['addCond']) ? htmlentities($data['addCond']) : "";
		$searchkey 		 = isset($data['search']) ? htmlentities($data['search']) : "";
		$sort 		 	 = isset($data['sort']) ? htmlentities($data['sort']) : "main.transactiondate";

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? $this->date->dateDbFormat($datefilterArr[0]) : ""; 
		$datefilterTo		= (!empty($datefilterArr[1])) ? $this->date->dateDbFormat($datefilterArr[1]) : "";

		$add_query 	= (!empty($searchkey)) ? "AND (main.voucherno LIKE '%$searchkey%' OR main.invoiceno LIKE '%$searchkey%' OR main.particulars LIKE '%$searchkey%' OR p.partnername LIKE '%$searchkey%' ) " : "";
		// $add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND main.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
		$add_query .= (!empty($vendfilter) && $vendfilter != '') ? "AND p.partnercode = '$vendfilter' " : "";

		if($addCond	== 'paid')
		{
			/*
				SELECT main.voucherno as voucherno, main.transactiondate as transactiondate, main.convertedamount as amount, main.balance as balance, CONCAT( first_name, ' ', last_name ) AS vendor, main.referenceno as referenceno, p.partnername 
				FROM accountspayable as main 
				LEFT JOIN partners p ON p.partnercode = main.vendor 
				WHERE main.stat = 'posted' AND main.transactiondate BETWEEN '2017-06-01' AND '2017-06-30' AND main.convertedamount = 
				(
					SELECT COALESCE(SUM(pv.convertedamount), 0) + COALESCE(SUM(pv.discount), 0) - COALESCE(SUM(pv.forexamount), 0) FROM pv_application AS pv 
					WHERE pv.apvoucherno = main.voucherno and pv.stat = 'posted' AND  pv.companycode = 'CID' 
				) AND  main.companycode = 'CID' 
			*/

			$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND pv.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

			$table_pv  = "pv_application AS pv";
			$pv_fields = "COALESCE(SUM(pv.convertedamount),0) + COALESCE(SUM(pv.discount),0) - COALESCE(SUM(pv.forexamount),0)";
			$pv_cond   = "pv.apvoucherno = main.voucherno and pv.stat = 'posted'";
			$sub_select = $this->db->setTable($table_pv)
							   ->setFields($pv_fields)
							   ->setWhere($pv_cond)
							   ->buildSelect();

			$addCondition	= "AND main.convertedamount = ($sub_select)";
		}
		else if($addCond == 'partial')
		{
			/*
				SELECT main.voucherno as voucherno, main.transactiondate as transactiondate, main.convertedamount as amount, main.balance as balance, CONCAT( first_name, ' ', last_name ) AS vendor, main.referenceno as referenceno, p.partnername 
				FROM accountspayable as main 
				LEFT JOIN partners p ON p.partnercode = main.vendor 
				WHERE main.stat = 'posted' AND main.transactiondate BETWEEN '2017-06-01' AND '2017-06-30' AND 
				(
					SELECT COALESCE(SUM(pv.convertedamount), 0) + COALESCE(SUM(pv.discount), 0) - COALESCE(SUM(pv.forexamount), 0) FROM pv_application AS pv 
					WHERE pv.apvoucherno = main.voucherno and pv.stat = 'posted' AND  pv.companycode = 'CID' 
				) > 0 AND main.convertedamount > 
				(
					SELECT COALESCE(SUM(pv.convertedamount), 0) + COALESCE(SUM(pv.discount), 0) - COALESCE(SUM(pv.forexamount), 0) FROM pv_application AS pv 
					WHERE pv.apvoucherno = main.voucherno and pv.stat = 'posted' AND  pv.companycode = 'CID' 
				) AND  main.companycode = 'CID' 
			*/

			$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND pv.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

			$table_pv  = "pv_application AS pv";
			$pv_fields = "COALESCE(SUM(pv.convertedamount),0) + COALESCE(SUM(pv.discount),0) - COALESCE(SUM(pv.forexamount),0)";
			$pv_cond   = "pv.apvoucherno = main.voucherno and pv.stat = 'posted'";
			$sub_select = $this->db->setTable($table_pv)
							   ->setFields($pv_fields)
							   ->setWhere($pv_cond)
							   ->buildSelect();

			
			$pv_cond_   = "pv.apvoucherno = main.voucherno and pv.stat = 'posted'";
			$sub_select_ = $this->db->setTable($table_pv)
							   ->setFields($pv_fields)
							   ->setWhere($pv_cond_)
							   ->buildSelect();
			
			$addCondition = "AND ($sub_select) > 0 AND main.convertedamount > ($sub_select_)";
		}
		else if($addCond == 'unpaid')
		{
			/*
				SELECT main.voucherno as voucherno, main.transactiondate as transactiondate, main.convertedamount as amount, main.balance as balance, CONCAT( first_name, ' ', last_name ) AS vendor, main.referenceno as referenceno, p.partnername, pv.voucherno AS pv_voucherno
				FROM accountspayable as main 
				LEFT JOIN pv_application pv ON main.voucherno = pv.apvoucherno AND pv.stat = 'posted'
				LEFT JOIN partners p ON p.partnercode = main.vendor 
				WHERE main.stat = 'posted' AND main.transactiondate BETWEEN '2017-06-01' AND '2017-06-30' AND 
				(
					SELECT COALESCE(SUM(pv.convertedamount), 0) + COALESCE(SUM(pv.discount), 0) - COALESCE(SUM(pv.forexamount), 0) FROM pv_application AS pv 
					WHERE pv.apvoucherno = main.voucherno and pv.stat = 'posted' AND  pv.companycode = 'CID' 
				) = 0 AND  main.companycode = 'CID' 
			*/

			$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND main.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

			$table_pv  = "pv_application AS pv";
			$pv_fields = "COALESCE(SUM(pv.convertedamount),0) + COALESCE(SUM(pv.discount),0) - COALESCE(SUM(pv.forexamount),0)";
			$pv_cond   = "pv.apvoucherno = main.voucherno and pv.stat = 'posted'";
			$sub_select = $this->db->setTable($table_pv)
							   ->setFields($pv_fields)
							   ->setWhere($pv_cond)
							   ->buildSelect();
			
			$addCondition = "AND ($sub_select) = 0";
		}
		else
		{
			$addCondition	= '';

			/*
				SELECT main.voucherno as voucherno, main.transactiondate as transactiondate, main.convertedamount as amount, main.balance as balance, CONCAT( first_name, ' ', last_name ) AS vendor, main.referenceno as referenceno, p.partnername 
				FROM accountspayable as main 
				LEFT JOIN partners p ON p.partnercode = main.vendor 
				WHERE main.stat = 'posted' AND main.transactiondate BETWEEN '2017-06-01' AND '2017-06-30'  AND  main.companycode = 'CID' 
			*/

		}

		$add_query .= $addCondition;

		$main_fields = array("main.voucherno as voucherno", "main.transactiondate as transactiondate", "main.convertedamount as amount","main.balance as balance", "CONCAT( first_name, ' ', last_name )","main.referenceno as referenceno", "p.partnername AS partnername", "pv.voucherno AS pv_voucherno", "pv.transactiondate as pvtransdate");
		$main_join   = "partners p ON p.partnercode = main.vendor"; //AND p.companycode
		$pv_join 	 = "pv_application pv ON main.voucherno = pv.apvoucherno AND pv.stat = 'posted'";
		$main_table  = "accountspayable as main";
		$main_cond   = "main.stat = 'posted' $add_query";

		$query 		 = $this->db->setTable($main_table)
								->setFields($main_fields)
								->leftJoin($main_join)
								->leftJoin($pv_join)
								->setWhere($main_cond)
								->setOrderBy($sort)
								// ->buildSelect();
								->runPagination();

		// var_dump($query);

		return $query;

	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

}