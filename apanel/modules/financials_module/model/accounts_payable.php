<?php
class accounts_payable extends wc_model
{
	public function __construct() 
	{
		parent::__construct();
		$this->log = new log();
	}

	public function retrieveVendorList()
	{
		$result = $this->db->setTable('partners')
		->setFields("partnercode ind, companycode, CONCAT( first_name, ' ', last_name ), CONCAT(partnercode,' - ',partnername) val")
		->setWhere("partnercode != '' AND partnertype = 'supplier' AND stat = 'active'")
		->setOrderBy("val")
		->runSelect()
		->getResult();
		
		return $result;
	}

	public function checkBudgetAccount($accountcode)
	{
		$result = $this->db->setTable('budget_details')
		->setFields("accountcode")
		->setWhere("accountcode = '$accountcode'")
		->runSelect()
		->getRow();
		
		return $result;
	}

	public function getAccountName($id)
	{
		$result = $this->db->setTable('chartaccount')
		->setFields("CONCAT(segment5, ' - ', accountname) as accountname")
		->setWhere("id = '$id'")
		->runSelect()
		->getRow();
		
		return $result;
	}

	public function checkEffectivityDate($budgetcode, $date)
	{
		$result = $this->db->setTable('budget')
		->setFields("effectivity_date")
		->setWhere("budget_code = '$budgetcode' AND effectivity_date <= '$date'")
		->runSelect()
		->getRow();
		
		return $result;
	}

	public function getEffectivityDate($budgetcode)
	{
		$result = $this->db->setTable('budget')
		->setFields("effectivity_date")
		->setWhere("budget_code = '$budgetcode'")
		->runSelect()
		->getRow();
		
		return $result;
	}

	public function getBudgetAmount($budgetcode, $accountcode, $effectivity_date)
	{
		$result = $this->db->setTable('budget_details as bd')
		->setFields("IF(IFNULL(bs.amount, 0) = 0, 0, SUM(bs.amount)) + bd.amount - IF(IFNULL(ac.actual, 0) = 0, 0, ac.actual) as amount, b.budget_check as budget_check, CONCAT(ca.segment5, ' - ', ca.accountname) as accountname")
		->leftJoin("budget as b ON bd.budget_code = b.budget_code AND b.status = 'approved'")
		->leftJoin("budget_supplement as bs ON bs.budget_id = b.id AND bs.accountcode = '$accountcode' AND bs.status = 'approved' AND bs.effectivity_date <= '$effectivity_date'")
		->leftJoin('chartaccount as ca ON ca.id = bd.accountcode')
		->leftJoin("(SELECT SUM(actual) as actual, accountcode, budget_code, voucherno FROM actual_budget WHERE voucherno NOT LIKE '%DV_%' AND voucherno not like '%TMP_%' GROUP BY accountcode, budget_code) as ac ON ac.accountcode = bd.accountcode AND ac.budget_code = '$budgetcode'")
		->setWhere("bd.budget_code = '$budgetcode' AND bd.accountcode = '$accountcode'")
		->setGroupBy('bd.accountcode, bd.budget_code')
		->runSelect()
		->getRow();
		// var_dump($effectivity_date);
		
		return $result;
	}

	public function getBudgetCodes()
	{
		$result = $this->db->setTable('budget')
		->setFields("budget_code ind, budget_code val")
		->setWhere("status = 'approved'")
		->runSelect()
		->getResult();
		
		return $result;
	}

	public function setButtonRestriction($transactiondate) {
		$closed_date     =   $this->getClosedDate();

		if( $closed_date >= $transactiondate ){
			return 0;
		} else {
			return 1;
		}
	}

	public function checkRefNo($referenceno) {
		$result = $this->db->setTable('purchasereceipt')
		->setFields("voucherno")
		->setWhere("voucherno = '$referenceno'")
		->runSelect()
		->getRow();
		
		return $result;
	}

	public function getJobList() {
		$result = $this->db->setTable('job')
		->setFields("job_no, stat")
		->setWhere('stat = "on-going"')
		->setGroupBy('job_no')
		->runPagination();
		
		return $result;
	}

	public function retrieveAssetId() {
		$result = $this->db->setTable('asset_master')
		->setFields("asset_number ind, asset_number val")
		->runSelect()
		->getResult();
		
		return $result;
	}

	public function getAssetDetails($asset) {
		$result = $this->db->setTable('asset_master am')
		->setFields("am.gl_asset ind, CONCAT(ca.segment5, '-', ca.accountname) val")
		->leftJoin('chartaccount ca ON am.gl_asset = ca.id')
		->setWhere("am.asset_number = '$asset'")
		->runSelect()
		->getRow();
		
		return $result;
	}


	public function getClosedDate() {
		$result     =   $this->db->setTable("journalvoucher")
		->setFields("transactiondate")
		->setWhere("stat = 'posted' AND source = 'closing'")
		->setOrderBy("transactiondate DESC")
		->setLimit(1)
		->runSelect()
		->getRow();

		$resultArr     =   ($result)    ?   explode(" ",$result->transactiondate)   :   "";

		$returnvalue    =   "";
		if( $resultArr ) {
			$returnvalue    =   $resultArr[0];
		} else {
			$returnvalue    =   "";
		}

		return $returnvalue;
	}

	public function getVendorDetails($vendor)
	{
		$result = $this->db->setTable('partners')
		->setFields("tinno, terms, address1")
		->setWhere("partnercode = '$vendor'")
		->runSelect()
		->getRow();
		
		return $result;
	}

	public function getCurrencyCode()
	{
		$result = $this->db->setTable('currency')
		->setFields("currencycode ind, currencycode val")
		->runSelect()
		->getResult();
		
		return $result;
	}

	public function getExchangeRate($currencycode)
	{
		$result = $this->db->setTable('exchangerate')
		->setFields("exchangerate")
		->setWhere("exchangecurrencycode = '$currencycode'")
		->setLimit(1)
		->runSelect()
		->getRow();
		
		return $result;
	}

	public function getTaxRate($taxaccount)
	{
		$result = $this->db->setTable('atccode')
		->setFields("tax_rate")
		->setWhere("atcId = '$taxaccount'")
		->setLimit(1)
		->runSelect()
		->getRow();
		
		return $result;
	}

	public function retrieveProformaList($data)
	{
		$result = $this->db->setTable('proforma')
		->setFields("proformacode ind, proformadesc val, stat stat")
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

		return $result;
	}

	public function retrieveAccounts()
	{
		$result = $this->db->setTable('chartaccount')
		->setFields('id ind, CONCAT(segment5, " - ", accountname) val')
		->runSelect()
		->getResult();

		return $result;
	}

	public function getBusiness()
	{
		$result = $this->db->setTable('wc_option')
		->setFields('code ind, value val')
		->setWhere("type = 'businesstype'")
		->runSelect(false)
		->getResult();

		return $result;
	}
	
	public function retrieveEditData($sid)
	{
		$setFields = "voucherno, transactiondate, vendor, referenceno, proformacode, particulars, duedate, amount, balance, exchangerate, convertedamount, invoiceno,companycode, lockkey as importchecker, stat ";
		$cond = "voucherno = '$sid'";	
		$temp = array();

		// Retrieve Header
		$retrieveArrayMain =  $this->db->setTable('accountspayable')
		->setFields($setFields)
		->setWhere($cond)
		->setLimit('1')
		->runSelect()
		->getRow();

		$temp["main"] = $retrieveArrayMain;

		// Retrieve Vendor Details
		$vendor_code = $temp["main"]->vendor;
		$custFields = "address1, tinno, terms, email, CONCAT( first_name, ' ', last_name ), partnername AS name"; 
		$cust_cond = "partnercode = '$vendor_code' AND partnertype = 'supplier'";

		$custDetails = $this->db->setTable('partners')
		->setFields($custFields)
		->setWhere($cust_cond)
		->setLimit('1')
		->runSelect()
		->getRow();
		
		$temp["cust"] = $custDetails;

		// Retrieve Details
		$detailFields = "main.accountcode, CONCAT(chart.segment5, ' - ', chart.accountname) accountname, main.detailparticulars, main.debit, main.credit,main.taxcode,main.taxbase_amount";
		$detail_cond  = "main.voucherno = '$sid'";
		$orderby 	  = "main.linenum";	
		$detailJoin   = "chartaccount as chart ON chart.id = main.accountcode AND chart.companycode = main.companycode";

		$retrieveArrayDetail = $this->db->setTable('ap_details as main')
		->setFields($detailFields)
		->leftJoin($detailJoin)
		->setWhere($detail_cond)
		->setOrderBy($orderby)
		->runSelect()
		->getResult();
		
		$temp["details"] = $retrieveArrayDetail;

		// Retrieve Payments
		$applicationFields = "app.voucherno,main.transactiondate,detail.accountcode,CONCAT(chart.segment5, ' - ', chart.accountname) accountname,main.wtaxcode,ftax.shortname,main.paymenttype,main.referenceno,app.amount,app.stat,main.checkdate,main.atcCode,main.particulars,detail.checkstat,app.discount,app.exchangerate,app.convertedamount";

		$appJoin_pv  = "paymentvoucher as main ON main.voucherno = app.voucherno AND main.companycode = app.companycode";
		$appJoin_pvd = "pv_details as detail ON detail.voucherno = app.voucherno AND detail.companycode = app.companycode";
		$appJoin_ca  = "chartaccount as chart ON chart.id = detail.accountcode AND chart.companycode = detail.companycode";
		$appJoin_fin = "fintaxcode as ftax ON ftax.fstaxcode = main.wtaxcode AND ftax.companycode = main.companycode";

		$app_cond 	 = "app.apvoucherno = '$sid' AND detail.linenum = '1' AND app.stat IN('open','posted') ";

		$applicationArray = $this->db->setTable('pv_application as app')
		->setFields($applicationFields)
		->leftJoin($appJoin_pv)
		->leftJoin($appJoin_pvd)
		->leftJoin($appJoin_ca)
		->leftJoin($appJoin_fin)
		->setWhere($app_cond)
		->runSelect()
		->getResult();
		
		$temp["payments"] = $applicationArray;

		// Received Cheques
		$sub_select = $this->db->setTable("pv_application AS app")
		->setFields("app.voucherno")
		->setWhere("app.apvoucherno = '$sid'")
		->buildSelect();
		
		$chequeFields = 'pv.voucherno as voucherno, pv.chequeaccount as chequeaccount, chart.accountname as chequeaccountname,
		pv.chequenumber as chequenumber, pv.chequedate as chequedate, 
		pv.chequeamount as chequeamount, pv.chequeconvertedamount as chequeconvertedamount';
		$cheque_cond  = "pv.voucherno IN($sub_select)";

		$chequeArray  = $this->db->setTable('pv_cheques pv')
		->setFields($chequeFields)
		->leftJoin("chartaccount chart ON chart.id = pv.chequeaccount")
		->setWhere($cheque_cond)
		->runSelect()
		->getResult();

		$rollArray	  = array();
		$rollArrayv	  = array();
		if(!empty($chequeArray))
		{
			$checkArray	= array();
			
			$chequeListArray	= array();

			for($c = 0; $c < count($chequeArray); $c++)
			{
				$pvno					= $chequeArray[$c]->voucherno;
				$chequeaccount			= $chequeArray[$c]->chequeaccount;
				$chequeaccountname		= $chequeArray[$c]->chequeaccountname;
				$chequenumber			= $chequeArray[$c]->chequenumber; 
				$chequedate				= $chequeArray[$c]->chequedate; 
				$chequedate				= date("M d, Y",strtotime($chequedate));
				$chequeamount			= $chequeArray[$c]->chequeamount;
				$chequeconvertedamount	= $chequeArray[$c]->chequeconvertedamount;

				$rollArray1['chequeaccount']		= $chequeaccount;
				$rollArray1['chequeaccountname']	= $chequeaccountname;
				$rollArray1['chequenumber']			= $chequenumber;
				$rollArray1['chequedate']			= $chequedate;
				$rollArray1['chequeamount']			= $chequeamount;
				$rollArray1['chequeconvertedamount'] = $chequeconvertedamount;
				
				$rollArray[$pvno][]					= $rollArray1;
				$rollArrayv[]						= $rollArray1;
			}
		}
		
		$temp["rollArray"] = $rollArray;

		// Received Cheques for View
		$chequeFieldsv = 'pva.apvoucherno, pvc.voucherno, pvc.chequeaccount, chart.accountname, pvc.chequenumber, pvc.chequedate, pvc.chequeamount, pvc.chequeconvertedamount';
		$pvc_join 	   = "pv_cheques AS pvc ON pva.voucherno = pvc.voucherno AND pva.companycode = pvc.companycode";
		$cheque_joinv  = "chartaccount chart ON chart.id = pvc.chequeaccount AND chart.companycode = pvc.companycode";
		$cheque_condv  = "pva.apvoucherno = '$sid' ";

		$chequeArrayv  = $this->db->setTable('pv_application pva')
		->setFields($chequeFieldsv)
		->leftJoin($pvc_join)
		->leftJoin($cheque_joinv)
		->setWhere($cheque_condv)
		->runSelect()
		->getResult();
									// ->buildSelect();
		
		// $rollArrayv	  = array();
		// if(!empty($chequeArrayv))
		// {
		// 	for($c = 0; $c < count($chequeArrayv); $c++)
		// 	{
		// 		$pvno					= $chequeArrayv[$c]->apvoucherno;
		// 		$accountname			= $chequeArrayv[$c]->accountname;
		// 		$chequeaccount			= $chequeArrayv[$c]->chequeaccount;
		// 		$chequenumber			= $chequeArrayv[$c]->chequenumber; 
		// 		$chequedate				= $chequeArrayv[$c]->chequedate; 
		// 		$chequedate				= $this->date->dateFormat($chequedate);
		// 		$chequeamount			= $chequeArrayv[$c]->chequeamount;
		// 		$chequeconvertedamount	= $chequeArrayv[$c]->chequeconvertedamount;

		// 		$rollArray2['accountname']			= $accountname;
		// 		$rollArray2['chequeaccount']		= $chequeaccount;
		// 		$rollArray2['chequenumber']			= $chequenumber;
		// 		$rollArray2['chequedate']			= $chequedate;
		// 		$rollArray2['chequeamount']			= $chequeamount;
		// 		$rollArray2['chequeconvertedamount'] = $chequeconvertedamount;

		// 		$rollArrayv[$pvno][]				= $rollArray2;
		// 	}
		// }
		
		$temp["rollArrayv"] = $rollArrayv;

		return $temp;
	}

	public function retrieveEditDataDtl($sid)
	{
		$setFields = "voucherno, transactiondate, vendor, referenceno, particulars, duedate, amount, balance, exchangerate, convertedamount, invoiceno,companycode";
		$cond = "voucherno = '$sid'";
		
		$temp = array();

		// Retrieve Header
		$retrieveArrayMain =  $this->db->setTable('accountspayable')
		->setFields($setFields)
		->setWhere($cond)
		->setLimit('1')
		->runSelect()
		->getRow();

		$temp["main"] = $retrieveArrayMain;

		// Retrieve Vendor Details
		$vendor_code = $temp["main"]->vendor;
		$custFields = "address1, tinno, terms, email, CONCAT( first_name, ' ', last_name ), partnername AS name"; 
		$cust_cond = "partnercode = '$vendor_code' AND partnertype = 'supplier'";

		$custDetails = $this->db->setTable('partners')
		->setFields($custFields)
		->setWhere($cust_cond)
		->setLimit('1')
		->runSelect()
		->getRow();
		
		$temp["cust"] = $custDetails;

		// Retrieve Details
		$detailFields = "CONCAT(atc.short_desc) atccode,main.accountcode, CONCAT(chart.segment5, ' - ', chart.accountname) accountname, main.detailparticulars, main.debit, main.credit,main.taxcode,main.taxbase_amount,atc_code ";
		$detail_cond  = "main.voucherno = '$sid' AND main.taxcode != '' ";
		$orderby 	  = "main.linenum";	
		$detailJoin   = "chartaccount as chart ON chart.id = main.accountcode AND chart.companycode = main.companycode LEFT JOIN atccode atc ON atc.atcId = main.taxcode";

		$retrieveArrayDetail = $this->db->setTable('ap_details as main')
		->setFields($detailFields)
		->leftJoin($detailJoin)
		->setWhere($detail_cond)
		->setOrderBy($orderby)
		->runSelect()
		->getResult();
		
		$temp["wtax_details"] = $retrieveArrayDetail;
		return $temp;
	}

	public function retrieveList($data)
	{
		$daterangefilter = isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$vendfilter      = isset($data['vendor']) ? htmlentities($data['vendor']) : ""; 
		$addCond         = isset($data['filter']) ? htmlentities($data['filter']) : "";
		$searchkey 		 = isset($data['search']) ? htmlentities($data['search']) : "";
		$sort 		 	 = isset($data['sort']) ? htmlentities($data['sort']) : "main.transactiondate";

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";

		// if($addCond	== 'paid')
		// {
		// 	$table_pv  = "pv_application AS pv";
		// 	$pv_fields = "COALESCE(SUM(pv.convertedamount),0) + COALESCE(SUM(pv.discount),0) - COALESCE(SUM(pv.forexamount),0)";
		// 	$pv_cond   = "pv.apvoucherno = main.voucherno and pv.stat = 'posted'";
		// 	$sub_select = $this->db->setTable($table_pv)
		// 					   ->setFields($pv_fields)
		// 					   ->setWhere($pv_cond)
		// 					   ->buildSelect();

		// 	$addCondition	= "AND main.convertedamount = ($sub_select)";
		// }
		// else if($addCond == 'partial')
		// {
		// 	$table_pv  = "pv_application AS pv";
		// 	$pv_fields = "COALESCE(SUM(pv.convertedamount),0) + COALESCE(SUM(pv.discount),0) - COALESCE(SUM(pv.forexamount),0)";
		// 	$pv_cond   = "pv.apvoucherno = main.voucherno and pv.stat = 'posted'";
		// 	$sub_select = $this->db->setTable($table_pv)
		// 					   ->setFields($pv_fields)
		// 					   ->setWhere($pv_cond)
		// 					   ->buildSelect();


		// 	$pv_cond_   = "pv.apvoucherno = main.voucherno and pv.stat = 'posted'";
		// 	$sub_select_ = $this->db->setTable($table_pv)
		// 					   ->setFields($pv_fields)
		// 					   ->setWhere($pv_cond_)
		// 					   ->buildSelect();

		// 	$addCondition = "AND ($sub_select) > 0 AND main.convertedamount > ($sub_select_)";
		// }
		// else if($addCond == 'unpaid')
		// {
		// 	$table_pv  = "pv_application AS pv";
		// 	$pv_fields = "COALESCE(SUM(pv.convertedamount),0) + COALESCE(SUM(pv.discount),0) - COALESCE(SUM(pv.forexamount),0)";
		// 	$pv_cond   = "pv.apvoucherno = main.voucherno and pv.stat = 'posted'";
		// 	$sub_select = $this->db->setTable($table_pv)
		// 					   ->setFields($pv_fields)
		// 					   ->setWhere($pv_cond)
		// 					   ->buildSelect();

		// 	$addCondition = "AND ($sub_select) = 0";
		// }
		// else
		// {
		// 	$addCondition	= '';
		// }

		// // OR p.first_name LIKE '%$searchkey%' OR p.last_name LIKE '%$searchkey%' 

		// $add_query 	= (!empty($searchkey)) ? "AND (main.voucherno LIKE '%$searchkey%' OR main.invoiceno LIKE '%$searchkey%' OR main.particulars LIKE '%$searchkey%' OR p.partnername LIKE '%$searchkey%' OR main.referenceno LIKE '%$searchkey%') " : "";
		// $add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND main.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
		// $add_query .= (!empty($vendfilter) && $vendfilter != 'none') ? "AND p.partnercode = '$vendfilter' " : "";
		// $add_query .= $addCondition;

		// $main_fields = array("main.voucherno as voucherno", "main.transactiondate as transactiondate", "main.convertedamount as amount","main.balance as balance", "CONCAT( first_name, ' ', last_name )","main.referenceno as referenceno", "p.partnername AS vendor","main.lockkey as importchecker","main.stat as stat");
		// $main_join   = "partners p ON p.partnercode = main.vendor"; //AND p.companycode
		// $main_table  = "accountspayable as main";
		// $main_cond   = "main.stat IN('posted','cancelled') $add_query";
		
		// $query 		 = $this->db->setTable($main_table)
		// 						->setFields($main_fields)
		// 						->leftJoin($main_join)
		// 						->setWhere($main_cond)
		// 						->setOrderBy($sort)
		// 						// ->buildSelect();
		// 						->runPagination();
		
		// // var_dump($query);

		// return $query;

		if($addCond	== 'unpaid'){
			$addCondition	= " payment_status = 'unpaid'";
		} else if($addCond == 'partial'){
			$addCondition = " payment_status = 'partial'";
		} else if($addCond == 'paid'){
			$addCondition = " payment_status = 'paid'";
		} else if($addCond == 'cancelled'){
			$addCondition = " payment_status = 'cancelled'";
		}else{
			$addCondition	= '';
		}

		$add_query 	= (!empty($searchkey)) ? "AND (main.voucherno LIKE '%$searchkey%' OR main.invoiceno LIKE '%$searchkey%' OR main.particulars LIKE '%$searchkey%' OR p.partnername LIKE '%$searchkey%' OR main.referenceno LIKE '%$searchkey%') " : "";
		$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND main.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
		$add_query .= (!empty($vendfilter) && $vendfilter != 'none') ? "AND p.partnercode = '$vendfilter' " : "";
		// $add_query .= $addCondition;

		$pv_app_fields 	=	array("(COALESCE(SUM(pvapp.convertedamount),0) + COALESCE(SUM(pvapp.discount),0) - COALESCE(SUM(pvapp.forexamount), 0)) amount",
			"pvapp.voucherno rvoucher", "pvapp.apvoucherno apno");
		$pv_table 		=	"pv_application pvapp";
		$pv_cond 		=	"pvapp.stat NOT IN('cancelled','temporary')";
		$pv_groupby 	=	"pvapp.apvoucherno";					

		$sub_select = $this->db->setTable($pv_table)
		->setFields($pv_app_fields)
		->setWhere($pv_cond)
		->setGroupBy($pv_groupby)
		->buildSelect();

		$ap_fields 	=	array(
			'main.entereddate as entereddate',
			"main.voucherno as voucherno", 
			"main.companycode as companycode", 
			"main.transactiondate as transactiondate",
			"main.amount as amount",
			"main.balance as balance", 
			"p.partnername AS vendor",
			"main.referenceno as referenceno",
			"main.lockkey as importchecker",
			"main.stat as stat",
			"IF(
			(main.convertedamount - payment.amount)>0 AND main.stat!='cancelled','partial',
			IF((main.convertedamount - payment.amount)=0 AND main.stat!='cancelled','paid',
			IF(main.stat!='cancelled','unpaid','cancelled')
			)
		) payment_status"
	);
		$ap_table 	=	"accountspayable as main";
		$ap_cond 	=	"main.stat IN ('posted','cancelled') $add_query";
		$ap_join 	=	"partners p ON p.partnercode = main.vendor AND p.partnertype = 'supplier' AND p.companycode = main.companycode";


		$result 	=	$this->db->setTable($ap_table)
		->setFields($ap_fields)
		->leftJoin($ap_join)
		->leftJoin("($sub_select) payment ON payment.apno = main.voucherno ")
		->setWhere($ap_cond)
		->setHaving($addCondition)
		->setOrderBy($sort)
		->runPagination();
		return $result;

	}

	public function buildQuery($table, $fields = array(), $cond = "")
	{	
		$sub_select = $this->db->setTable($table)
		->setFields($fields)
		->setWhere($cond)
		->buildSelect();
		
		return $sub_select;
	}

	public function getAccountClasscode($accountcode){
		$result = $this->db->setTable("chartaccount")
		->setFields('accountclasscode')
		->setWhere("id IN($accountcode)")
		->runSelect()
		->getResult();

		return $result;
	}

	public function checkCWT($accountcode){
		$result = $this->db->setTable("chartaccount")
		->setFields('accountclasscode')
		->setWhere("id = '$accountcode'")
		->runSelect()
		->getRow();

		return $result;
	}

	public function retrievePaymentDetails($voucherno)
	{
		/*
			--------- ORIGINAL QUERY -------
			SELECT ap.sourceno as sourceno, main.checknumber, main.referenceno AS referenceno, ap.particulars as remarks, app.amount as amount, main.paymenttype, ap.invoiceno AS invoiceno 
			FROM pv_application as app 
			LEFT JOIN accountspayable as ap ON ap.voucherno = app.apvoucherno AND ap.companycode = app.companycode 
			LEFT JOIN paymentvoucher as main ON main.voucherno = app.voucherno AND main.companycode = app.companycode 
			WHERE app.apvoucherno = 'AP0000000026' AND app.companycode = 'CID' 
			ORDER BY app.linenum
		*/


			$paymentFields		= array("ap.invoiceno as sourceno","main.checknumber","ap.referenceno AS referenceno","ap.particulars as remarks","app.amount as amount","main.paymenttype", "ap.invoiceno AS invoiceno");
			$paymentJoin		= "accountspayable as ap ON ap.voucherno = app.apvoucherno AND ap.companycode = app.companycode";
			$paymentJoin_ 		= "paymentvoucher as main ON main.voucherno = app.voucherno AND main.companycode = app.companycode"; 
			$paymentCondition 	= "app.apvoucherno = '$voucherno'";
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

		public function getValue($table, $cols = array(), $cond = "", $orderby = "", $bool = "", $groupby = "", $join="")
		{
			$result = $this->db->setTable($table)
			->setFields($cols)
			->setWhere($cond)
			->leftJoin($join)
			->setOrderBy($orderby)
			->setGroupBy($groupby)
			->runSelect($bool)
			->getResult();
		// echo $this->db->getQuery();
			return $result;
		}

		public function getTax($table, $cols = array(), $join = "" ,$cond = "", $orderby = "", $bool = "" )
		{
			$result = $this->db->setTable($table)
			->setFields($cols)
			->innerJoin($join)
			->setWhere($cond)
			->setOrderBy($orderby)
			->runSelect($bool)
			->getResult();
			return $result;
		}

		public function getATC($account) {
			$result = $this->db->setTable('atccode atc')
			->setFields('atcId ind, CONCAT(atc.atc_code ," - ", atc.short_desc) val')
			->leftJoin('chartaccount as ca ON atc.tax_account = ca.id')
			->setWhere("atc.tax_account = '$account' AND atc.stat = 'active'")
			->runSelect()
			->getResult();

			return $result;
		}

		public function validatePayableAccount($data)
		{
			$ischeck_pay    = array();

			for($i = 0; $i < count($data); $i++)
			{
				$chart_id 	    = $data[$i];
				$ischeck_result = $this->retrieveData("chartaccount", array("accountclasscode"), "id = '$chart_id'");

				$ischeck_pay[] = $ischeck_result[0]->accountclasscode;
			}

			if(!in_array("ACCPAY", $ischeck_pay))
				return false;
			else
				return true;
		}

		public function insertData($data)
		{
			$mainInvTable		= "accountspayable";
			$detailInvTable		= "ap_details";
			$insertResult		= 0;
			$errmsg				= array();
			$tempData 			= array();

			$task 				= isset($data['h_task']) ? $data['h_task'] : "create";

		// Journal Data
			$voucherno			= (isset($data['h_voucher_no']) && (!empty($data['h_voucher_no']))) ? htmlentities(addslashes(trim($data['h_voucher_no']))) : "";
			$vendor				= (isset($data['vendor']) && (!empty($data['vendor']))) ? htmlentities(addslashes(trim($data['vendor']))) : "";
			$referenceno		= (isset($data['referenceno']) && (!empty($data['referenceno']))) ? htmlentities(addslashes(trim($data['referenceno']))) : "";
			$invoiceno			= (isset($data['invoiceno']) && (!empty($data['invoiceno']))) ? htmlentities(addslashes(trim($data['invoiceno']))) : "";
			$transactiondate	= (isset($data['document_date']) && (!empty($data['document_date']))) ? htmlentities(addslashes(trim($data['document_date']))) : "";
			$duedate			= (isset($data['due_date']) && (!empty($data['due_date']))) ? htmlentities(addslashes(trim($data['due_date']))) : "";
			$proformacode		= (isset($data['proformacode']) && (!empty($data['proformacode']))) ? htmlentities(addslashes(trim($data['proformacode']))) : "";
			$remarks			= (isset($data['remarks']) && (!empty($data['remarks']))) ? htmlentities(addslashes(trim($data['remarks']))) : "";
			$terms				= (isset($data['vendor_terms']) && (!empty($data['vendor_terms']))) ? htmlentities(addslashes(trim($data['vendor_terms']))) : 0;
			$totalamount		= (isset($data['total_debit']) && (!empty($data['total_debit']))) ? htmlentities(addslashes(trim($data['total_debit']))) : "";

			$isExist			= $this->getValue($mainInvTable, array("stat"), "voucherno = '$voucherno' AND stat = 'posted'");
			$status				= (!empty($isExist)) ? "posted" : "temporary";

		/**
		* Add amounts as part of the exchangerate update
		*/
		$amount				= (isset($data['h_amount']) && (!empty($data['h_amount']))) ? htmlentities(addslashes(trim($data['h_amount']))) : "";
		$exchangerate		= (isset($data['h_exchangerate']) && (!empty($data['h_exchangerate']))) ? htmlentities(addslashes(trim($data['h_exchangerate']))) : "1.00";
		$convertedamount	= (isset($data['h_convertedamount']) && (!empty($data['h_convertedamount']))) ? htmlentities(addslashes(trim($data['h_convertedamount']))) : "";

		/**TRIM COMMAS FROM AMOUNTS**/
		$totalamount		= str_replace(',','',$totalamount);
		
		$amount				= str_replace(',','',$amount);
		$exchangerate		= str_replace(',','',$exchangerate);
		$convertedamount	= str_replace(',','',$convertedamount);

		/**FORMAT DATES**/
		$transactiondate				= date("Y-m-d",strtotime($transactiondate));
		$duedate						= date("Y-m-d",strtotime($duedate));
		$period							= date("n",strtotime($transactiondate));
		$fiscalyear						= date("Y",strtotime($transactiondate));

		$post_header['voucherno']		= $voucherno;
		$post_header['transactiondate']	= $transactiondate;
		$post_header['currencycode']	= 'PHP';
		$post_header['referenceno']		= $referenceno;
		$post_header['exchangerate']	= $exchangerate;
		$post_header['stat']			= $status;
		$post_header['proformacode']	= $proformacode;
		$post_header['transtype']		= 'AP';
		$post_header['invoicedate']		= $transactiondate;
		$post_header['duedate']			= $duedate;
		$post_header['vendor']			= $vendor;
		$post_header['invoiceno']		= $invoiceno; //$referenceno;
		$post_header['amount']			= ($amount == $convertedamount) ? $totalamount : $amount;
		$post_header['convertedamount']	= ($amount == $convertedamount) ? $totalamount : $convertedamount;
		$post_header['period']			= $period;
		$post_header['fiscalyear']		= $fiscalyear;
		$post_header['terms']			= $terms;
		$post_header['amountpaid']		= 0;
		$post_header['amountforpayment']= 0;
		$post_header['particulars']		= $remarks;
		$post_header['source']			= 'AP';
		$post_header['sourceno']		= '';
		$post_header['balance']			= ($amount == $convertedamount) ? $totalamount : $convertedamount;
		$post_header['sitecode']		= '-';
		
		/**INSERT DETAILS**/
		foreach($data as $postIndex => $postValue)
		{
			if($postIndex == 'accountcode' ||  $postIndex=='detailparticulars' || $postIndex=='debit' || $postIndex=='credit' || $postIndex=='taxcode' || $postIndex=='taxbase_amount')
			{
				$a		= '';
				
				foreach($postValue as $postValueIndex => $postValueIndexValue)
				{
					if($postIndex == 'debit' || $postIndex == 'credit' || $postIndex == 'taxbase_amount')
					{
						$a = str_replace(',', '', $postValueIndexValue);
					}
					else
					{
						$a = htmlentities(addslashes($postValueIndexValue));
					}
					
					$arrayData[$postIndex][$postValueIndex] = $a;
				}	
			}
		}

		/**START OF INSERT QUERY**/
		foreach($arrayData as $arrayDataIndex => $arrayDataValue)
		{
			foreach($arrayDataValue as $postValueIndex => $postValueIndexValue)
			{											
				$tempArray[$postValueIndex][$arrayDataIndex] = $postValueIndexValue;
			}
		}

		$linenum			= 1;
		$tempArr 			= array();
		$tempAccArr 		= array();
		
		foreach($tempArray as $tempArrayIndex => $tempArrayValue)
		{
			$data_insert["voucherno"]         = $voucherno;
			$data_insert['transtype']         = 'AP';
			$data_insert['linenum']	          = $linenum;
			$data_insert['slcode']			  = '-';
			$data_insert['bankrecon_id']	  = '';
			$data_insert['checkstat']		  = 'uncleared';
			$data_insert['costcentercode']	  = '-';
			$data_insert['accountcode']		  = $tempArrayValue['accountcode'];
			$data_insert['debit']			  = $tempArrayValue['debit'];
			$data_insert['credit']			  = $tempArrayValue['credit'];
			$data_insert['currencycode']	  = 'PHP';
			$data_insert['exchangerate']	  = $exchangerate;
			$data_insert['converteddebit']	  = $tempArrayValue['debit'];
			$data_insert['convertedcredit']	  = $tempArrayValue['credit'];
			$data_insert['taxcode']			  = '';
			$data_insert['taxacctflg']		  = '';
			$data_insert['taxline']			  = '';
			$data_insert['vatflg']			  = '';
			$data_insert['detailparticulars'] = $tempArrayValue['detailparticulars'];
			$data_insert['taxcode']			  = $tempArrayValue['taxcode'];
			$data_insert['taxbase_amount']		= $tempArrayValue['taxbase_amount'];
			$data_insert['convertedtaxbase_amount']	= $tempArrayValue['taxbase_amount'];
			$data_insert['stat']		      = $status;

			$linenum++;

			// Data for inserting
			if($tempArrayValue['debit'] == 0 && $tempArrayValue['credit'] == 0)
			{
				
			}
			else
				$tempArr[] 						= $data_insert;


			// Data checking for accountcode
			$tempAccArr[] 					=  $tempArrayValue['accountcode'];
		}

		/**INSERT AP DETAILS**/
		$isDetailExist	= $this->getValue($detailInvTable, array("COUNT(*) as count"),"voucherno = '$voucherno'");

		// Check if at least one entry is a payable account 
		$check_payable = $this->validatePayableAccount($tempAccArr);

		if($isDetailExist[0]->count == 0)
		{
			if($check_payable)
			{
				$this->db->setTable($detailInvTable)
				->setValues($tempArr);

				$insertResult = $this->db->runInsert();

				if(!$insertResult)
					$errmsg[] = "<li>Saving in Accounts Payable Details.</li>";
			}
			else
			{
				$errmsg[] = "<li>At least one entry should be a payable account. Please refer to your Chart of Accounts.</li>";
			}

		}
		else
		{
			// Delete data if existing
			$this->db->setTable($detailInvTable)
			->setWhere("voucherno = '$voucherno'");
			$this->db->runDelete();

			// Check if at least one entry is a payable account 
			$check_payable = $this->validatePayableAccount($tempAccArr);

			if($check_payable)
			{
				// Then insert data
				$this->db->setTable($detailInvTable)
				->setValues($tempArr);
				$insertResult = $this->db->runInsert();

				if(!$insertResult)
					$errmsg[] = "<li>Saving in Accounts Payable Details.</li>";
			}
			else
				$errmsg[] = "<li>At least one entry should be a payable account. Please refer to your Chart of Accounts</li>";

		}
		
		/**
		 * Update payable amount based on payable accounts
		 */
		if(!is_null($tempAccArr) && !empty($tempAccArr)){

			$accountlist 			= "'".implode("','",$tempAccArr)."'";

			$sum_payable_account 	= $this->db->setTable("ap_details main")
			->setFields(
				array(
					"SUM(debit) debit",
					"SUM(credit) credit",
					"SUM(converteddebit) converteddebit",
					"SUM(convertedcredit) convertedcredit"
				)
			)
			->leftJoin("chartaccount chart ON chart.id = main.accountcode")
			->setGroupBy("main.accountcode")
			->setWhere("chart.accountclasscode = 'ACCPAY' AND main.accountcode IN($accountlist) AND main.voucherno = '$voucherno' ")
			->runSelect()
			->getResult();
			$payable_amount				= 0;
			$payable_convertedamount	= 0;						
			if($sum_payable_account){
				$payable_amount 		= ($sum_payable_account[0]->credit != 0) ? $sum_payable_account[0]->credit : $sum_payable_account[0]->debit;
				$payable_convertedamount= ($sum_payable_account[0]->convertedcredit != 0) ? $sum_payable_account[0]->convertedcredit : $sum_payable_account[0]->converteddebit;
			}
			
			$post_header['amount']			= $payable_amount;
			$post_header['convertedamount']	= $payable_convertedamount;
			$post_header['balance']			= $payable_convertedamount;

		}
		/**INSERT HEADER**/
		if($status == 'temporary')
		{	
			// Delete temporary data
			$this->db->setTable($mainInvTable)
			->setWhere("voucherno = '$voucherno'");
			$insertResult = $this->db->runDelete();

			if($insertResult)
			{
				// Handle Insert
				if($check_payable)
				{
					$this->db->setTable($mainInvTable)
					->setValues($post_header);

					$insertResult = $this->db->runInsert();

					if(!$insertResult)
						$errmsg[] = "<li>Saving in Accounts Payable Header.</li>";
				}
			}
		}else{
			if($check_payable)
			{
				$this->db->setTable($mainInvTable)
				->setValues($post_header)
				->setWhere("voucherno = '$voucherno'");
				
				$insertResult = $this->db->runUpdate();

				if(!$insertResult)
					$errmsg[] = "<li>Update in Accounts Payable Header.</li>";
			}
		}

		return $errmsg;
	}

	public function applyPayments($data)
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

		$invoice			   = (isset($_POST['invoice']) && (!empty($_POST['invoice']))) ? $_POST['invoice']: "";

		/**CLEAN PASSED DATA**/
		foreach($data as $postIndex => $postValue)
		{
			if(($postIndex == 'invoiceno' || $postIndex=='paymentdate' || $postIndex == 'paymentnumber' || $postIndex=='paymentaccount' || $postIndex=='paymentmode' || $postIndex=='paymentreference' || $postIndex=='paymentamount' || $postIndex == 'paymenttaxcode' || $postIndex == 'paymentnotes' || $postIndex == 'vendor' || $postIndex == 'customer' || $postIndex == 'paymentdiscount' || $postIndex == 'paymentconverted' || $postIndex == 'paymentrate') && !empty($postValue))
			{
				$a		= '';

				foreach($postValue as $postValueIndex => $postValueIndexValue)
				{
					if($postIndex == 'paymentamount' || $postIndex == 'paymentdiscount' || $postIndex == 'paymentconverted' || $postIndex == 'paymentrate')
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
						$b = str_replace(',', '', $postValueIndexValue);
					}
					else if($postIndex == 'chequedate')
					{
						$b = ($postValueIndexValue != '') ? date("Y-m-d", strtotime($postValueIndexValue)) : "0000-00-00";
					}
					else
					{
						$b = htmlentities(addslashes(trim($postValueIndexValue)));
					}
					
					$chequeData[$postIndex][$postValueIndex] = $b;
				}
			}
		}

		foreach($arrayData as $arrayDataIndex => $arrayDataValue)
		{
			foreach($arrayDataValue as $postValueIndex => $postValueIndexValue)
			{											
				$tempArray[$postValueIndex][$arrayDataIndex] = $postValueIndexValue;
			}
		}

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

		$voucherno			 = (isset($tempArray[0]['paymentnumber']) && !empty($tempArray[0]['paymentnumber'])) ? $tempArray[0]['paymentnumber'] : $seq->getValue('PV');
		
		$count				= 1;
		$linenum			= 1;
		$totalamount		= 0;
		$totaltaxamount		= 0;
		$totalwtaxamount	= 0;
		
		$cheque_info		= array();
		$tempCheque 		= array();

		if(!empty($newArray))
		{
			$linecount		= 1;

			foreach($newArray as $newArrayIndex => $newArrayValue)
			{
				$chequeaccount			= (!empty($newArrayValue['chequeaccount'])) ? $newArrayValue['chequeaccount'] : "";
				$chequenumber			= (!empty($newArrayValue['chequenumber'])) ? $newArrayValue['chequenumber'] : "";
				$chequedate				= (!empty($newArrayValue['chequedate'])) ? $newArrayValue['chequedate'] : "";
				$chequeamount			= (!empty($newArrayValue['chequeamount'])) ? $newArrayValue['chequeamount'] : "";
				$chequeconvertedamount	= (!empty($newArrayValue['chequeconvertedamount'])) ? $newArrayValue['chequeconvertedamount'] : "";
				$chequedate				= date("Y-m-d",strtotime($chequedate));
				
				if(!empty($chequedate) && !empty($chequeaccount) && !empty($chequenumber) && !empty($chequeamount))
				{
					$cheque_header['voucherno']				= $voucherno;
					$cheque_header['transtype']				= "PV";
					$cheque_header['linenum']				= $linecount;
					$cheque_header['chequeaccount']			= $chequeaccount;
					$cheque_header['chequenumber']			= $chequenumber;
					$cheque_header['chequedate']			= $chequedate;
					$cheque_header['chequeamount']			= $chequeamount;
					$cheque_header['chequeconvertedamount']	= $chequeamount;
					$cheque_header['stat']					= 'uncleared';

					$linecount++;
					
					$cheque_info[$chequeaccount]['amount'][]	 = $chequeamount;
					
					$tempCheque[] = $cheque_header;
				}
			}
		}

		foreach($tempArray as $tempArrayIndex => $tempArrayValue)
		{
			$transactiondate				= (!empty($tempArrayValue['paymentdate'])) ? $tempArrayValue['paymentdate'] : "";

			$accountcode					= (!empty($tempArrayValue['paymentaccount'])) ? $tempArrayValue['paymentaccount'] : "";
			
			if($count == 1)
			{
				$paymenttype				= (!empty($tempArrayValue['paymentmode'])) ? $tempArrayValue['paymentmode'] : "";
			}
			
			$referenceno					= (!empty($tempArrayValue['paymentreference'])) ? $tempArrayValue['paymentreference'] : "";
			$amount							= (!empty($tempArrayValue['paymentamount'])) ? $tempArrayValue['paymentamount'] : "";
			$convertedamount				= (!empty($tempArrayValue['paymentconverted'])) ? $tempArrayValue['paymentconverted'] : $amount;
			$exchangerate					= (!empty($tempArrayValue['paymentrate'])) ? $tempArrayValue['paymentrate'] : "1.00";
			$checkdate						= (!empty($tempArrayValue['checkdate'])) ? $tempArrayValue['checkdate'] : "0000-00-00";
			$particulars					= (!empty($tempArrayValue['paymentnotes'])) ? $tempArrayValue['paymentnotes'] : "";
			
			$paymentdiscount				= (!empty($tempArrayValue['paymentdiscount'])) ? $tempArrayValue['paymentdiscount'] : 0;
			
			$invoice						= (!empty($tempArrayValue['invoiceno'])) ? $tempArrayValue['invoiceno'] : "";

			$payablerate					= $this->getValue($applicableHeaderTable, array("exchangerate"),"voucherno = '$invoice' AND stat = 'posted'"); 

			$payablerate 					= $payablerate[0]->exchangerate;
			$vendor							= (!empty($tempArrayValue['vendor'])) ? $tempArrayValue['vendor'] : "";
			$customer						= (!empty($tempArrayValue['customer'])) ? $tempArrayValue['customer'] : "";

			if(!empty($transactiondate) && !empty($paymenttype) && ($amount > 0 || $paymentdiscount > 0))
			{
				$transactiondate				= date("Y-m-d",strtotime($transactiondate));
				$period							= date("n",strtotime($transactiondate));
				$fiscalyear						= date("Y",strtotime($transactiondate));

				$post_header['voucherno']		= $voucherno;
				$post_header['vendor']			= $vendor;
				$post_header['transactiondate']	= $transactiondate;
				$post_header['transtype']		= $source;
				$post_header['particulars']		= $particulars;
				$post_header['period']			= $period;
				$post_header['fiscalyear']		= $fiscalyear;
				$post_header['checkrelease']	= $transactiondate;
				$post_header['releaseby']		= USERNAME;
				$post_header['currencycode']	= 'PHP';
				$post_header['amount']			= $amount;
				$post_header['exchangerate']	= $exchangerate;
				$convertedamount 				= $amount;
				$post_header['convertedamount']	= $convertedamount;
				$post_header['source']			= $source;
				$post_header['paymenttype']		= $paymenttype;	
				$post_header['bankcode']		= '';
				$post_header['account']			= '';
				$post_header['checknumber']		= $referenceno;
				$post_header['referenceno']		= $referenceno;
				$post_header['checkdate']		= $checkdate;
				$post_header['checkstat']		= '';
				$post_header['stat']			= 'open';
				$post_header['postedby']		= USERNAME;
				$post_header['postingdate']		= $datetime;
				
				$tempData[] = $post_header;

				if($count == 1)
				{
					/**INSERT HEADER**/
					$isDetailExist	= $this->getValue($mainAppTable, array("COUNT(*) as count"), "voucherno = '$voucherno'");

					if($isDetailExist[0]->count > 0)
					{
						$this->db->setTable($applicationTable) //pv_application
						->setWhere("voucherno = '$voucherno'");
						$this->db->runDelete();
						
						$this->db->setTable($detailAppTable) //pv_details
						->setWhere("voucherno = '$voucherno'"); 
						$this->db->runDelete();

						$this->db->setTable($chequeTable) //pv_cheques
						->setWhere("voucherno = '$voucherno'");
						$this->db->runDelete();	
						
						
						$this->db->setTable($mainAppTable) //paymentvoucher
						->setValues($tempData)
						->setWhere("voucherno = '$voucherno'");
						
						$insertResult = $this->db->runUpdate();
					}
					else
					{
						$insertResult = $this->db->setTable($mainAppTable)
						->setValues($tempData)
						->runInsert();
					}
				}
				
				$taxamount	= 0;
				$tempDetail = array();
				$testing = array();

				$discountaccount	= $this->getValue("fintaxcode",array('salesAccount as account'), " fstaxcode = 'DC' ");
				$newamount			= $convertedamount - $paymentdiscount;
				$forexamount 		= 0;

				if($payablerate != $exchangerate)
				{
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

				$post_detail['voucherno']		= $voucherno;
				$post_detail['transtype']		= $source;
				$post_detail['slcode']			= '-';
				$post_detail['costcentercode']	= '-';
				$post_detail['checkstat']		= 'uncleared';
				$post_detail['checknumber']		= $post_header['checknumber'];
				$post_detail['postedby']		= USERNAME;
				$post_detail['postingdate']		= $datetime;
				$post_detail['stat']			= $post_header['stat'];

				/**CREDIT ACCOUNT**/
				if($paymenttype == 'cheque')
				{
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
							
							$isAppDetailExist	= $this->getValue($detailAppTable, array("COUNT(*) AS count"),"voucherno = '$voucherno' AND accountcode = '$cheque_index' AND linenum = '$linenum'");

							$tempDetail[] = $post_detail;
							$testing[] = $isAppDetailExist;

							$linenum++;
						}

						if($isAppDetailExist[0]->count > 0)
						{
							$insertResult = $this->db->setTable($detailAppTable)
							->setValues($tempDetail)
							->setWhere("voucherno = '$voucherno' AND accountcode = '$cheque_index' AND linenum = '$linenum'")
							->runUpdate();

						}
						else
						{
							$insertResult = $this->db->setTable($detailAppTable)
							->setValues($tempDetail)
							->runInsert();

						}
						
						if($insertResult != 1)
						{
							$errmsg[] 		= "error saving payment detail [$linenum] : ".$insertResult;
						}

					}
				}else{
					if($paymentdiscount > 0 && $convertedamount == 0)
					{
						$accountcode = $this->getValue($applicableDetailTable, "accountcode", "voucherno = '$invoice' AND linenum = '1' LIMIT 1");
						$accountcode = $accountcode[0]->accountcode;
					}

					$post_detail['linenum']			= $linenum;
					$post_detail["apvoucherno"]     = $invoice;
					$post_detail['accountcode']		= $accountcode;
					$post_detail['debit']			= 0;
					$post_detail['credit']			= $creditamount;
					$post_detail['converteddebit']	= $post_detail['debit'];
					$post_detail['convertedcredit']	= $post_detail['credit'];

					$isAppDetailExist	= $this->getValue($detailAppTable, array("COUNT(*) AS count_pv"), "voucherno = '$voucherno' AND accountcode = '$accountcode' AND linenum = '$linenum'");


					if($isAppDetailExist[0]->count_pv > 0)
					{
						$this->db->setTable($detailAppTable) //pv_details
						->setValues($post_detail)
						->setWhere("voucherno = '$voucherno' AND accountcode = '$accountcode' AND linenum = '$linenum'");

						$insertResult = $this->db->runUpdate();
					}
					else
					{
						$insertResult = $this->db->setTable($detailAppTable) //pv_details
						->setValues($post_detail)
						->runInsert();
					}
					
					if($insertResult != 1)
					{
						$errmsg[] 		= "error saving payment detail [$linenum] : ". $insertResult;
					}
					
					$linenum++;
				}
				
				if($newamount > 0)
				{
					/**DEBIT ACCOUNT**/
					$apDebitAccount = $this->db->setTable("ap_details as app")
					->setFields("app.accountcode")
					->leftJoin("chartaccount as chart ON chart.id = app.accountcode")
					->setWhere(" chart.accountclasscode = 'ACCPAY' AND app.voucherno = '$invoice' ")
					->setOrderBy(" app.linenum LIMIT 1 ")
					->runSelect()
					->getResult();


					$apDebitAccount  				= $apDebitAccount[0]->accountcode;

					$post_detail['linenum']			= $linenum;
					$post_detail["apvoucherno"]     = $invoice;
					$post_detail['accountcode']		= $apDebitAccount;
					$post_detail['debit']			= $newamount;
					$post_detail['credit']			= 0;
					$post_detail['converteddebit']	= $post_detail['debit'];
					$post_detail['convertedcredit']	= $post_detail['credit'];

					// pv_details
					$isAppDetailExist				= $this->getValue($detailAppTable, array("COUNT(*) AS count"),"voucherno = '$voucherno' AND accountcode = '$apDebitAccount' AND linenum = '$linenum'");

					if($isAppDetailExist[0]->count > 0)
					{
						$this->db->setTable($detailAppTable) //pv_details
						->setValues($post_detail)
						->setWhere("voucherno = '$voucherno' AND accountcode = '$apDebitAccount' AND linenum = '$linenum'");
						
						$insertResult = $this->db->runUpdate();
					}
					else
					{
						$insertResult = $this->db->setTable($detailAppTable) //pv_details
						->setValues($post_detail)
						->runInsert();
					}

					$linenum++;
				}

				/**DISCOUNT ACCOUNT**/
				if($paymentdiscount > 0)
				{
					$post_detail['linenum']			= $linenum;
					$post_detail['accountcode']		= $discountaccount[0]->account;
					$post_detail['debit']			= $paymentdiscount;
					$post_detail['credit']			= 0;

					$post_detail['converteddebit']	= $post_detail['debit'];
					$post_detail['convertedcredit']	= $post_detail['credit'];
					
					$insertResult = $this->db->setTable($detailAppTable) //pv_details
					->setValues($post_detail)
					->runInsert();
					
					$linenum++;
				}

				/**FOREX GAIN / LOSS**/
				if(abs($forexamount) > 0)
				{
					$post_detail['linenum']			= $linenum;
					$post_detail['accountcode']		= $forexaccount;
					$post_detail['debit']			= abs($forexamount);
					$post_detail['credit']			= 0;
					$post_detail['converteddebit']	= $post_detail['debit'];
					$post_detail['convertedcredit']	= $post_detail['credit'];
					
					$insertResult =  $this->db->setTable($detailAppTable)
					->setValues($post_detail)
					->runInsert();
					
					$linenum++;
				}

				/**UPDATE APPLICATION TABLE**/
				$post_application['voucherno']		= $voucherno;
				$post_application['transtype']		= $source;
				$post_application['linenum']		= $count;
				$post_application['apvoucherno']	= $invoice;
				$post_application['discount']		= $paymentdiscount;
				$post_application['amount']			= $amount;
				$post_application['currencycode']	= 'PHP';
				$post_application['exchangerate']	= $exchangerate;
				$post_application['convertedamount']= $amount;
				$post_application['forexamount']	= abs($forexamount);
				$post_application['stat']			= $post_header['stat'];

				$isAppDetailExist	= $this->getValue($applicationTable, array("COUNT(*) AS count"), "voucherno = '$voucherno' AND apvoucherno = '$invoice'");

				if($isAppDetailExist[0]->count > 0)
				{
					$insertResult = $this->db->setTable($applicationTable) //pv_application
					->setValues($post_application)
					->setWhere("voucherno = '$voucherno' AND apvoucherno = '$invoice'")
					->runUpdate();
				}
				else
				{
					$insertResult = $this->db->setTable($applicationTable)
					->setValues($post_application)
					->runInsert();
				}

				/**UPDATE MAIN INVOICE**/
				$invoice_amount				= $this->getValue($applicableHeaderTable, array("amount as convertedamount"), "voucherno = '$invoice' AND stat IN('open','posted')");
				$applied_discount			= 0;

				$applied_sum				= $this->getValue($applicationTable, array("SUM(amount) AS convertedamount")," apvoucherno = '$invoice' AND stat IN('open','posted') ");

				$applied_discount			= $this->getValue($applicationTable, array("SUM(discount) AS discount"), "apvoucherno = '$invoice' AND stat IN('open','posted') ");

				$applied_forexamount		= $this->getValue($applicationTable, array("SUM(forexamount) AS forexamount"), "apvoucherno = '$invoice' AND stat IN('open','posted') ");

				$applied_sum				= $applied_sum[0]->convertedamount - $applied_forexamount[0]->forexamount;

				$invoice_amount				= (!empty($invoice_amount)) ? $invoice_amount[0]->convertedamount : 0;
				$applied_sum				= (!empty($applied_sum)) ? $applied_sum : 0;

				$invoice_balance			= $invoice_amount - $applied_sum - $applied_discount[0]->discount;

				$balance_info['amountpaid']	= $applied_sum + $applied_discount[0]->discount;

				$balance_info['balance']	= $invoice_amount - $applied_sum - $applied_discount[0]->discount;
				
				// Update
				$insertResult = $this->db->setTable($applicableHeaderTable)
				->setValues($balance_info)
				->setWhere("voucherno = '$invoice'")
				->runUpdate();

				$count++;
				
				$totalamount		+= $amount;
				$totaltaxamount		+= $taxamount;

			}
		}

		/**UPDATE HEADER AMOUNTS**/
		$update_info				= array();
		$update_info['netamount']	= $totalamount;
		$update_info['taxamount']	= $totaltaxamount;

		$insertResult = $this->db->setTable($mainAppTable) //paymentvoucher
		->setValues($update_info)
		->setWhere("voucherno = '$voucherno' AND stat IN('open','posted')")
		->runUpdate();

		/**INSERT TO CHEQUES TABLE**/
		if(strtolower($paymenttype) == 'cheque')
		{
			// Delete
			$this->db->setTable($chequeTable)
			->setWhere("voucherno = '$voucherno'");
			
			$insertResult = $this->db->runDelete();
			
			// Insert
			if($insertResult)
			{
				$insertResult =  $this->db->setTable($chequeTable)
				->setValues($tempCheque)
				->runInsert();
			}
			
			if($insertResult != 1)
			{
				$errmsg[] 		= "error saving cheque payments : ".$insertResult;
			}
		}

		if($continue_flag == 1)
		{
			$continue_flag	= ($insertResult != 1) ? 0 : 1;
			
			if($continue_flag == 0)
			{
				$continue_flag	= 2;
				$errmsg[] 		= "The system has encountered an error in saving. Please contact admin to fix this issue.<br/>";
			}
		}

		return $errmsg;
	}

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

		// OR p.first_name LIKE '%$searchkey%' OR p.last_name LIKE '%$searchkey%'

		$add_query 	= (!empty($searchkey)) ? "AND (main.voucherno LIKE '%$searchkey%' OR main.invoiceno LIKE '%$searchkey%' OR main.particulars LIKE '%$searchkey%' OR p.partnername LIKE '%$searchkey%') " : "";
		$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND main.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
		$add_query .= (!empty($vendfilter) && $vendfilter != '') ? "AND p.partnercode = '$vendfilter' " : "";
		$add_query .= $addCondition;

		$main_fields = array("main.transactiondate as transactiondate", "main.voucherno as voucherno", "CONCAT( first_name, ' ', last_name )", "main.referenceno as referenceno", "main.amount as amount", "main.balance as balance", "main.particulars", "p.partnername AS vendor");

		$main_join   = "partners p ON p.partnercode = main.vendor AND p.companycode = main.companycode AND p.partnertype = 'supplier'"; //AND p.companycode
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
		->runUpdate();
		
		return $result;
	}

	public function deleteData($data)
	{
		$table = $data["table"];
		$cond  = stripslashes($data["condition"]);
		
		// var_dump($cond);

		$this->db->setTable($table)
		->setWhere($cond);
		
		// echo $this->db->buildDelete();

		$result = $this->db->runDelete();

		if($result)
		{
			$this->log->saveActivity('Delete Accounts Payable $cond');
			return $result;
		}

		
	}

	public function updateData($data, $table, $cond)
	{
		$result = $this->db->setTable($table)
		->setValues($data)
		->setWhere($cond)
		->runUpdate();
		
		return $result;
	}

	public function updateActualBudget($voucherno, $fields)
	{
		unset($fields['id']);
		$result = $this->db->setTable('actual_budget')
		->setValues($fields)
		->setWhere("voucherno = '$voucherno'")
		->runUpdate(false);
		
		return $result;
	}

	public function updateEntry($fields, $data) {
		$error_id = array();
		foreach ($data as $id) {
			$result =  $this->db->setTable('accountspayable')
			->setValues($fields)
			->setWhere("voucherno = '$id'")
			->setLimit(1)	
			->runUpdate();

			if ($result) {
				$this->log->saveActivity("Delete Item Type [$id]");
			} else {
				if ($this->db->getError() == 'locked') {
					$error_id[] = $id;
				}
			}
		}

		return $error_id;
	}

	public function deleteEntry($data) {
		$error_id = array();
		foreach ($data as $id) {
			$result =  $this->db->setTable('financial_jobs')
			->setWhere("voucherno = '$id'")
			->runDelete(false);

			if ($result) {
				$this->log->saveActivity("Delete Item Type [$id]");
			} else {
				if ($this->db->getError() == 'locked') {
					$error_id[] = $id;
				}
			}
		}

		return $error_id;
	}



	public function getDetailsByVoucher($voucher) {
		$result = $this->db->setTable('ap_details ad')
		->setFields('ad.voucherno as voucherno, ad.linenum as linenum, ad.exchangerate as exchangerate, ad.accountcode as accountcode, ad.detailparticulars as description, ad.taxbase_amount as taxbase_amount, ad.source as source, ad.currencycode as currencycode,
			ad.taxcode as taxcode, ad.debit as debit, ad.credit as credit, ad.converteddebit as convdebit, ad.convertedcredit as convcredit')
		->leftJoin('chartaccount as ca ON ca.id = ad.accountcode')
		->setWhere("voucherno IN ($voucher)")
		->runSelect()
		->getResult();

		return $result;
	}

	public function reverseEntries($invoices)
	{
		$count = $this->db->setTable('ap_details')
		->setFields('*')
		->setWhere("voucherno IN($invoices)")
		->runSelect()
		->getResult();

		if(!empty($count))
		{
			$ctr = count($count) + 1;
			for($i = 0; $i < count($count); $i++)
			{
				$insert_info['voucherno']			= $count[$i]->voucherno;
				$insert_info['transtype']			= $count[$i]->transtype;
				$insert_info['linenum']				= $ctr;
				$insert_info['slcode']				= $count[$i]->slcode;
				$insert_info['bankrecon_id']		= $count[$i]->bankrecon_id;
				$insert_info['checkstat']			= $count[$i]->checkstat;
				$insert_info['costcentercode']		= $count[$i]->costcentercode;
				$insert_info['accountcode']			= $count[$i]->accountcode;
				$insert_info['debit']				= $count[$i]->credit;
				$insert_info['credit']				= $count[$i]->debit;
				$insert_info['taxbase_amount']		= $count[$i]->taxbase_amount;
				$insert_info['source']				= $count[$i]->source;
				$insert_info['sourcecode']			= $count[$i]->sourcecode;
				$insert_info['currencycode']		= $count[$i]->currencycode;
				$insert_info['exchangerate']		= $count[$i]->exchangerate;
				$insert_info['converteddebit']		= $count[$i]->convertedcredit;
				$insert_info['convertedcredit']		= $count[$i]->converteddebit;
				$insert_info['taxcode']				= $count[$i]->taxcode;
				$insert_info['taxacctflg']			= $count[$i]->taxacctflg;
				$insert_info['taxline']				= $count[$i]->taxline;
				$insert_info['vatflg']				= $count[$i]->vatflg;
				$insert_info['detailparticulars']	= $count[$i]->detailparticulars;
				$insert_info['stat']				= $count[$i]->stat;

				$result = $this->db->setTable('ap_details')
				->setValues($insert_info)
				->runInsert();

				$result = $this->db->setTable('accountspayable')
				->setValues(array('balance' => '0'))
				->setWhere("voucherno = $invoices AND stat = 'cancelled'")
				->runUpdate();

				$ctr++;
			}
			
		}
		return $count;
		
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
		->setWhere(" voucherno = '$voucher' AND stat IN('open','posted') ")
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
				//else
					//$this->log->saveActivity("Update Accounts Payable [$mainvoucher]");
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
				$this->log->saveActivity("Delete issued payment [$voucher]");

			// Update pv_details
			$result = $this->db->setTable($detailTable)
			->setValues($update_info)
			->setWhere("voucherno = '$voucher'")
			->runUpdate();

			if(!$result)
				$errmsg[] = "The system has encountered an error in updating Payment Voucher Details [$voucher]. Please contact admin to fix this issue.";
			//else
				//$this->log->saveActivity("Update Payment Voucher Details [$voucher]");

			// Update paymentvoucher
			$result = $this->db->setTable($mainTable)
			->setValues($update_info)
			->setWhere("voucherno = '$voucher'")
			->runUpdate();	

			if(!$result)
				$errmsg[] = "The system has encountered an error in updating Payment Voucher [$voucher]. Please contact admin to fix this issue.";
			//else
				//$this->log->saveActivity("Update Payment Voucher [$voucher]");

			return $errmsg;
		}

	}

	public function saveAP($ap, $ap_details) {
		$result = $this->db->setTable('accountspayable')
		->setValues($ap)
		->runInsert();
		$result = $this->db->setTable('ap_details')
		->setValuesFromPost($ap_details)
		->runInsert();

		return $result;
	}

	public function saveActualBudget($fields) {
		$result = $this->db->setTable('actual_budget')
		->setValues($fields)
		->runInsert(false);

		return $result;
	}

	public function saveFinancialsJob($fields) {
		$result = $this->db->setTable('financial_jobs')
		->setValuesFromPost($fields)
		->runInsert(false);

		return $result;
	}

	public function getAccount($tax_account){
		$result = $this->db->setTable('atccode a')
		->setFields("tax_rate,tax_account")
		->leftJoin("chartaccount c ON c.id = a.tax_account ")
		->setWhere("atcId = '$tax_account'")
		->runSelect()
		->getResult();
		return $result;
	}

	public function checkVoucherOnFinancialsJob($voucherno){
		$result = $this->db->setTable('financial_jobs')
		->setFields('voucherno')
		->setWhere("voucherno = '$voucherno'")
		->runSelect(false)
		->getRow();

		return $result;
	}

	public function check_if_exists($column, $table, $condition) {
		return $this->db->setTable($table)
		->setFields("COUNT(".$column.") count")
		->setWhere($condition)
		->runSelect()
		->getResult();
	}

	public function getAccountId($accountname) {
		$result = $this->db->setTable('chartaccount')
		->setFields("id")
		->setWhere("accountname = '$accountname'")
		->runSelect()
		->getResult();

		return $result[0]->id;
	}

	public function save_import($table, $posted_data){
		$result = $this->db->setTable($table)
		->setValuesFromPost($posted_data)
		->runInsert();
		return $result;
	}

	public function companySettings($fields) {
		$session	= new session();
		$login		= $session->get('login');
		$company	= isset($login['companycode']) ? $login['companycode'] : '';

		$result = $this->db->setTable('company')
		->setFields($fields)
		->setWhere("companycode = '$company'")
		->runSelect()
		->getResult();
		return $result;
	}


	public function getAPById($fields, $voucher) {
		$result = $this->db->setTable('accountspayable')
		->setFields($fields)
		->setWhere("voucherno = '$voucher'")
		->runSelect()
		->getRow();

		return $result;
	}

	public function getAPDetails($id) {
		$result = $this->db->setTable('ap_details ad')
		->setFields('ad.budgetcode as budgetcode, ad.accountcode as accountcode, ad.detailparticulars as description, ad.debit as debit, ad.credit as credit, IF(ad.debit != 0, converteddebit, convertedcredit) as currencyamount, ad.linenum as linenum, ad.converteddebit as converteddebit, ad.convertedcredit as convertedcredit')
		->leftJoin('chartaccount as ca ON ca.id = ad.accountcode')
		->setWhere("voucherno = '$id'")
		->runSelect()
		->getResult();

		return $result;
	}

	public function getSupplierDetails($vendor) {
		$result = $this->db->setTable('partners')
		->setFields('email, address1, tinno, partnername')
		->setWhere("partnercode = '$vendor'")
		->runSelect()
		->getRow();

		return $result;
	}

	public function updateFinancialsJobs($fields, $voucherno)
	{
		$result = $this->db->setTable('financial_jobs')
		->setWhere("voucherno = '$voucherno'")
		->runDelete(false);

		$result = $this->db->setTable('financial_jobs')
		->setValues($fields)
		->runInsert(false);

		return $result;
	}

	public function updateAP($voucherno, $fields)
	{

		$result = $this->db->setTable('accountspayable')
		->setValues($fields)
		->setWhere("voucherno = '$voucherno'")
		->setLimit(1)
		->runUpdate();

		$result = $this->db->setTable('ap_details')
		->setWhere("voucherno = '$voucherno'")
		->runDelete();

		if ($result) {
			$this->log->saveActivity("Update Content [$voucherno]");
		}

		return $result;
	}

	public function saveAPDetails($ap_details) {
		$result = $this->db->setTable('ap_details')
		->setValuesFromPost($ap_details)
		->runInsert();

		return $result;
	}

	public function checkStat($voucherno) {
		$result = $this->db->setTable('accountspayable')
		->setFields('amountpaid, balance, amount')
		->setWhere("voucherno = '$voucherno'")
		->runSelect()
		->getRow();

		return $result;
	}

	public function getPVDetails($voucherno) {
		$result = $this->db->setTable('paymentvoucher as pv')
		->setFields('pa.entereddate as date, IF(IFNULL(pc.chequenumber, 0) = 0, pv.paymenttype, CONCAT(pv.paymenttype, " - ", pc.chequenumber)) as mode ,CONCAT(ca.segment5, " - ", ca.accountname) as paymentaccount,  pv.particulars as reference, pa.amount as amount, pa.discount as discount, pv.voucherno as voucherno')
		->leftJoin('pv_application as pa ON pv.voucherno = pa.voucherno')
		->leftJoin('pv_details as pd ON pv.voucherno = pd.voucherno')
		->leftJoin('pv_cheques as pc ON pc.voucherno = pd.voucherno')
		->leftJoin('chartaccount as ca ON pd.accountcode = ca.id')
		->setWhere("pa.apvoucherno = '$voucherno' AND pd.debit != 0 AND pv.stat != 'cancelled'")
		->runSelect()
		->getResult();

		return $result;
	}

	public function getAsset($assetid)
	{
		$fields = array('capitalized_cost','balance_value','useful_life','salvage_value','depreciation_month');
		$result = $this->db->setTable('asset_master')
		->setFields($fields)
		->setWhere("asset_number = '$assetid'")
		->runSelect()
		->getRow();

		return $result;
	}

	public function updateAsset($assetid,$amount,$asd1,$asd2)
	{
		$fields['capitalized_cost'] = $amount+$asd1;
		$fields['balance_value'] = $amount+$asd2;
		$result = $this->db->setTable('asset_master')
		->setValues($fields)
		->setWhere("asset_number = '$assetid'")
		->setLimit(1)
		->runUpdate();

		if ($result) {
			$this->log->saveActivity("Update Asset Value [$assetid]");
		}

		return $result;
	}

	public function updateAssetMasterSchedule($assetid,$final,$depreciation,$depreciation_amount)
	{
		$fields['depreciation_amount'] = $depreciation_amount;
		$fields['accumulated_dep'] = $depreciation;
		// var_dump($fields);
		$result = $this->db->setTable('depreciation_schedule')
		->setValues($fields)
		->setWhere("asset_id = '$assetid' AND depreciation_date = '$final'")
		->setLimit(1)
		->runUpdate();

		if ($result) {
			$this->log->saveActivity("Update Asset Value [$assetid]");
		}

		return $result;
	}

	public function checkifaccountisinbudget($accountcode){
		$result = $this->db->setTable('budget_details bd')
		->setFields("bd.budget_code")
		->leftJoin('budget as b ON bd.budget_code = b.budget_code')
		->setWhere("bd.accountcode = '$accountcode' AND b.status = 'approved'")
		->runSelect()
		->getResult();
		return $result;
	}

	public function checkifpairexistsinbudget($accountcode, $budget){
		$result = $this->db->setTable('budget_details bd')
		->setFields("bd.id")
		->setWhere("bd.accountcode = '$accountcode' AND bd.budget_code = '$budget'")
		->runSelect()
		->getRow();
		return $result;
	}
	
	public function getLatestAPRecord() {
		$getRow = $this->db->setTable('accountspayable')
							->setFields('voucherno')
							->setOrderBy('accountspayable.entereddate DESC')
							->runSelect()
							->getRow();

		$result = $getRow->voucherno;

		return $result;
	}

	public function getNextId($table,$field,$subcon = "") {
		$result = $this->db->setTable($table)
			->setFields('MAX('.$field.') as current')
			->setWhere(" $field != '' " . $subcon)
			->runSelect()
			->getRow();

		if ($result) {
			$return = $result->current += 1;
		} else {
			$return = '1';
		}
		return $return;
	}

	public function getCurrentId($table,$voucherno) {
		$result = $this->db->setTable($table)
			->setFields('attachment_id')
			->setWhere("reference='$voucherno'")
			->runSelect()
			->getRow();

		if ($result) {
			$return = $result->attachment_id;
		} else {
			$return = '0';
		}
		return $return;
	}

	public function uploadAttachment($data) {
		$reference = $data['reference'];
		$filename = $data['attachment_name'];
		
		$getFiles = $this->db->setTable('accountspayable_attachments')
							->setFields("COUNT('attachment_id') count")
							->setWhere("reference = '$reference'")
							->runSelect()
							->getRow();
							// echo $this->db->getQuery();

		if ($getFiles) {
			$this->deleteAttachment($reference, $filename);
		}

		$result = $this->db->setTable('accountspayable_attachments')
							->setValues($data)
							->runInsert();
		
		if ($result) {
			$this->log->saveActivity("Approve [$reference] with attachment");		
		}
		return $result;
	}

	public function deleteAttachment($reference, $filename) {
		$result = $this->db->setTable('accountspayable_attachments')
							->setFields('attachment_name')
							->setWhere("reference='$reference'")
							->setOrderBy('entereddate DESC')
							->setLimit(1)
							->runSelect()
							->getRow();
		if ($result) {
			unlink('files/'.$result->attachment_name);
		}
		
		$result = $this->db->setTable('accountspayable_attachments')
							->setWhere("reference='$reference' AND attachment_name != '$filename'")
							->runDelete();

		return $result;
	}

	public function deleteExistingFile($filename){
		$result = $this->db->setTable('accountspayable_attachments')
							->setFields('attachment_name')
							->setWhere("attachment_name='$filename'")
							->setOrderBy('entereddate DESC')
							->setLimit(1)
							->runSelect()
							->getRow();

		if ($result) {
			unlink('files/'.$result->attachment_name);
		} 
		
		return $result;
	}

	public function replaceAttachment($data) {
		$reference = $data['reference'];
		$result = $this->db->setTable('accountspayable_attachments')
							->setValues($data)
							->setWhere("reference='$reference'")
							->runUpdate();
		if ($result) {
			$this->log->saveActivity("Update attachment with [$reference]");		
		}
		return $result;
	}

	public function getAttachmentFile($voucherno) {
		
		$result = $this->db->setTable('accountspayable_attachments')
							->setFields(array('attachment_name','attachment_url', 'attachment_type'))
							->setWhere("reference = '$voucherno'")
							->setOrderBy('attachment_id DESC')
							->setLimit(1)
							->runSelect()
							->getRow();
							// echo $this->db->getQuery();
		
		return $result;
	}
}