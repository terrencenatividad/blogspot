<?php
class receipt_voucher_model extends wc_model
{
	public function __construct() 
	{
		parent::__construct();
		$this->log = new log();
	}

	public function retrieveCustomerList()
	{
		$result = $this->db->setTable('partners')
					->setFields("partnercode ind, companycode, CONCAT( first_name, ' ', last_name ), partnername val")
					->setWhere("partnercode != '' AND partnertype = 'customer' AND stat = 'active'")
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

	public function retrieveDataPagination($table, $fields = array(), $cond = "", $join = "", $groupby = "")
	{
		$result = $this->db->setTable($table)
					->setFields($fields)
					->leftJoin($join)
					->setGroupBy($groupby)
					->setWhere($cond)
					->runPagination();
		// echo $this->db->getQuery();
		return $result;
	}
	
	public function retrieveEditData($sid)
	{
		$setFields = "voucherno, transactiondate, customer,or_no, referenceno, particulars, netamount, exchangerate, convertedamount, paymenttype, amount, stat, credits_used";
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

		// Retrieve Details
		$detailFields = "main.accountcode, chart.accountname, main.detailparticulars, main.ischeck, main.debit, SUM(main.credit) credit";
		$detail_cond  = "main.voucherno = '$sid' AND main.stat != 'temporary'";
		$orderby 	  = "main.linenum";	
		$detailJoin   = "chartaccount as chart ON chart.id = main.accountcode AND chart.companycode = main.companycode";
		$groupby      = "main.linenum";
	
		$retrieveArrayDetail = $this->db->setTable('rv_details as main')
									->setFields($detailFields)
									->leftJoin($detailJoin)
									->setWhere($detail_cond)
									->setGroupBy($groupby)
									->setOrderBy($orderby)
									->runSelect()
									->getResult();

		$temp["details"] = $retrieveArrayDetail;
		
		$setFields = "partnername name, email, tinno, address1, terms";
		$customer    = $temp["main"]->customer;
		$cond = "partnercode = '$customer'";

		// Retrieve Header
		$retrieveArrayVendor =  $this->db->setTable('partners')
									->setFields($setFields)
									->setWhere($cond)
									->setLimit('1')
									->runSelect()
									->getRow();

		$temp["vend"] = $retrieveArrayVendor;

		// Retrieve Payments
		$applicationFields = "app.arvoucherno as vno, app.amount as amt, '0.00' as bal, app.discount as dis, app.credits_used as cred";
		$app_cond 	 = "app.voucherno = '$sid' AND app.amount > 0 AND app.stat NOT IN ('cancelled','temporary' )";
		// echo $sid;
		$applicationArray = $this->db->setTable('rv_application as app')
								->setFields($applicationFields)
								->setWhere($app_cond)
								->runSelect()
								->getResult();

		$temp["payments"] = $applicationArray;

		// Received Cheques for View
		$chequeFields = 'pvc.voucherno, pvc.chequeaccount, chart.accountname, pvc.bank, pvc.chequenumber, pvc.chequedate, pvc.chequeamount, pvc.chequeconvertedamount';
		$cheque_cond  = "pvc.voucherno = '$sid'";
		$cheque_join  = "chartaccount chart ON chart.id = pvc.chequeaccount AND chart.companycode = pvc.companycode";
		
		$chequeArray  = $this->db->setTable('rv_cheques AS pvc')
									->setFields($chequeFields)
									->leftJoin($cheque_join)
									->setWhere($cheque_cond)
									->runSelect()
									->getResult();
		
		$rollArray	  = array();
	
		if(!empty($chequeArray))
		{
			for($c = 0; $c < count($chequeArray); $c++)
			{
				$pvno					= $chequeArray[$c]->voucherno;
				$accountname			= $chequeArray[$c]->accountname;
				$chequeaccount			= $chequeArray[$c]->chequeaccount;
				$chequeaccount			= $chequeArray[$c]->chequeaccount;
				
				$bank					= $chequeArray[$c]->bank;
				$chequenumber			= $chequeArray[$c]->chequenumber; 
				$chequedate				= $chequeArray[$c]->chequedate; 
				$chequedate				= $this->date->dateFormat($chequedate);
				$chequeamount			= $chequeArray[$c]->chequeamount;
				$chequeconvertedamount	= $chequeArray[$c]->chequeconvertedamount;

				$rollArray['accountname']			= $accountname;
				$rollArray['chequeaccount']			= $chequeaccount;
				$rollArray['bank']					= $bank;
				
				$rollArray['chequenumber']			= $chequenumber;
				$rollArray['chequedate']			= $chequedate;
				$rollArray['chequeamount']			= $chequeamount;
				$rollArray['chequeconvertedamount'] = $chequeconvertedamount;
				
				$rollArray[$pvno][]					= $rollArray;
			}
		}
		
		$temp["rollArray"] = $rollArray;


		return $temp;
	}

	public function retrievePayments($voucherno)
	{
		$voucherno = (isset($voucherno) && !empty($voucherno)) ? $voucherno : "";

		$temp 	   = array();

		// Retrieve Payments
		$applicationFields = "app.voucherno,main.transactiondate,detail.accountcode,chart.accountname,main.wtaxcode,ftax.shortname,main.paymenttype,main.referenceno,app.amount,app.stat,main.checkdate,main.atcCode,main.particulars,detail.checkstat,app.discount,app.exchangerate,app.convertedamount,app.arvoucherno";

		$appJoin_pv  = "receiptvoucher as main ON main.voucherno = app.voucherno AND main.companycode = app.companycode";
		$appJoin_pvd = "rv_details as detail ON detail.voucherno = app.voucherno AND detail.companycode = app.companycode";
		$appJoin_ca  = "chartaccount as chart ON chart.id = detail.accountcode AND chart.companycode = detail.companycode";
		$appJoin_fin = "fintaxcode as ftax ON ftax.fstaxcode = main.wtaxcode AND ftax.companycode = main.companycode";

		$app_cond 	 = "app.voucherno = '$voucherno' AND detail.linenum = '1' AND app.stat != 'cancelled'";

		$applicationArray = $this->db->setTable('rv_application as app')
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
		$sub_select = $this->db->setTable("rv_application AS app")
							   ->setFields("app.voucherno")
							   ->setWhere("app.arvoucherno = '$voucherno'")
							   ->buildSelect();

		$chequeFields = 'voucherno, chequeaccount, chequenumber, chequedate, chequeamount, chequeconvertedamount';
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
		$daterangefilter	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$custfilter      	= isset($data['customer']) ? htmlentities($data['customer']) : ""; 
		$filter         	= isset($data['filter']) ? htmlentities($data['filter']) : "";
		$searchkey 		 	= isset($data['search']) ? htmlentities($data['search']) : "";
		$sort 		 	 	= isset($data['sort']) ? htmlentities($data['sort']) : "main.transactiondate DESC";

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? $this->date->dateDbFormat($datefilterArr[0]) : ""; 
		$datefilterTo		= (!empty($datefilterArr[1])) ? $this->date->dateDbFormat($datefilterArr[1]) : "";

		$add_query 			= " ";
		
		if ($filter != 'all' && $filter != ''){
			$add_query .= " AND main.stat = '$filter' ";
		}
		if ($daterangefilter){
			$add_query .= " AND main.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' ";
		}
		if ($custfilter && $custfilter != 'none'){
			$add_query .= " AND p.partnercode = '$custfilter' ";
		}
		if ($searchkey){
			$add_query .= " AND ".$this->generateSearch($searchkey, array("main.voucherno","p.partnername","coa.accountname","pvc.chequenumber","main.or_no"));
		}

		$query 		 = $this->db->setTable("receiptvoucher main")
								->setFields(
									array(
										"main.transactiondate as paymentdate",
										"main.voucherno as voucherno",
										"p.partnername as partner",
										"main.referenceno as reference",
										"main.paymenttype as paymentmode",
										"main.convertedamount as amount",
										"main.stat as status",
										"main.or_no",
										"coa.accountname as bankaccount",
										"pvc.chequenumber as chequenumber",
										"pvc.chequedate as chequedate",
										"pvc.bank as bank",
										"pvc.chequeamount as chequeamount",
										"pvc.stat as chequestat"
									)
								)
								->leftJoin("partners p ON p.partnercode = main.customer ")
								->leftJoin("rv_cheques as pvc ON pvc.voucherno = main.voucherno ")
								->leftJoin("chartaccount coa ON coa.id = pvc.chequeaccount ")
								->setWhere("main.stat NOT IN('deleted','temporary') ".$add_query)
								->setOrderBy($sort)
								->setGroupBy("main.voucherno, pvc.chequenumber")
								->runPagination();
		return $query;

	}

	public function retrieveAPList($data,$search)
	{
		$customercode = (isset($data["customer"]) && !empty($data["customer"])) ? $data["customer"]         : "";
		$voucherno  = (isset($data["voucherno"]) && !empty($data["voucherno"])) ? $data["voucherno"]: "";
		$tempArr    = array();
		$search_key = '';

		if ($search) {
			$search_key .= ' AND ' . $this->generateSearch($search, array("main.voucherno"));
		}

		// Sub Select
		$table_rv  = "rv_application AS rv";
		$rv_fields = "COALESCE(SUM(rv.convertedamount),0) + COALESCE(SUM(rv.discount),0) + COALESCE(SUM(rv.credits_used),0) - COALESCE(SUM(rv.forexamount),0)";

		$rv_cond   = "rv.arvoucherno = main.voucherno AND rv.stat IN('open','posted') AND rv.voucherno = '$voucherno' ";

		// Main Queries
		$main_table   = "accountsreceivable as main";
		$main_fields  = array("main.voucherno as voucherno", "main.transactiondate as transactiondate", "main.convertedamount as amount", "(main.convertedamount - COALESCE(SUM(app.convertedamount),0)) as balance", "p.partnername AS vendor_name", "main.referenceno as referenceno");
		$main_join 	  = "partners p ON p.partnercode = main.customer ";
		$orderby  	  = "main.transactiondate DESC";
		
		$rva_cond 	=	($voucherno != "") ?	" AND app.voucherno = '$voucherno'"	:	"";

		$mainTable	= "accountsreceivable as main";
		$mainFields	= array(
							"main.voucherno as voucherno", "main.transactiondate as transactiondate",
							"main.convertedamount as amount", "(main.convertedamount - COALESCE(SUM(app.convertedamount),0) - COALESCE(SUM(app.discount),0) - COALESCE(SUM(app.credits_used),0)) as balance", "main.referenceno as referenceno",
							"SUM(app.convertedamount) as payment","COALESCE(SUM(app.credits_used),0) credits_used"
						);
		$mainJoin	= "rv_application AS app ON app.arvoucherno = main.voucherno AND app.stat IN('open','posted') $rva_cond ";
		$groupBy 	= "main.voucherno";

		$groupBy 	.=	($voucherno != "") 	?	", app.voucherno":"";
		$sub_select 		= $this->db->setTable($table_rv)
										->setFields($rv_fields)
										->setWhere($rv_cond)
										->buildSelect();

		if($customercode && empty($voucherno)){
			$mainCondition   		= "main.stat = 'posted' AND main.customer = '$customercode' AND main.balance != 0 ";
			$query 				= $this->retrieveDataPagination($mainTable, $mainFields, $mainCondition, $mainJoin, $groupBy);
			$tempArr["result"] = $query;
		} else if($voucherno) {
			$mainCondition   		= "main.stat = 'posted' AND main.customer = '$customercode' AND ((main.balance - ($sub_select)) <= main.convertedamount) AND ( main.balance != 0 OR ($sub_select) != 0)";
			$query 				= $this->retrieveDataPagination($mainTable, $mainFields, $mainCondition, $mainJoin, $groupBy);
			$tempArr["result"] = $query;
		}

		// echo $this->db->getQuery();
		return $query;
	}
	
	public function retrieveRVDetails($data)
	{
		$cond  		= (isset($data["cond"]) && !empty($data["cond"])) ? $data["cond"]: "";
		$customercode = (isset($data["customer"]) && !empty($data["customer"])) ? $data["customer"]: "";

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
		$main_table   = "accountsreceivable as main";
		$main_fields  = array("main.voucherno as voucherno", "main.transactiondate as transactiondate", "main.convertedamount as amount", "main.balance as balance", "p.partnername AS vendor_name", "main.referenceno as referenceno", "apd.accountcode", "chart.accountclasscode", "SUM(apd.credit) AS sumcredit", "apd.detailparticulars");
		$apd_join 	  = "ar_details AS apd ON main.voucherno = apd.voucherno AND main.companycode = apd.companycode";
		$chart_join   = "chartaccount AS chart ON apd.accountcode = chart.id AND chart.companycode = apd.companycode";
		$main_join 	  = "partners p ON p.partnercode = main.customer";
		$main_cond 	  = "main.stat = 'posted' AND main.customer = '$customercode' AND chart.accountclasscode = 'ACCREC' AND apd.voucherno  IN $cond";
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

		$paymentFields		= array("app.arvoucherno as sourceno","main.checknumber","ap.referenceno","main.particulars as remarks","app.amount as amount","main.paymenttype");
		$paymentJoin		= "accountsreceivable as ap ON ap.voucherno = app.arvoucherno AND ap.companycode = app.companycode";
		$paymentJoin_ 		= "receiptvoucher as main ON main.voucherno = app.voucherno AND main.companycode = app.companycode"; 
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

		return $result;
	}

	public function insert_detail_entry($voucherno, $post_detail)
	{
		$accountcode    = $post_detail["accountcode"];
		$linenum 	    = $post_detail["linenum"];
		$detailAppTable = "rv_details";
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
				$errmsg = "<li>Updating RV Details.</li>";
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
			
			if(!$insertResult){
				$errmsg = "<li>Saving Receipt Voucher Details.</li>";
			}
		}

		return $errmsg;
	}

	public function savePayment($data)
	{
		$errmsg				   	= array();
		$seq 				   	= new seqcontrol();
		$datetime			   	= date("Y-m-d H:i:s");

		$errmsg[] 				= "";

		/**SET TABLES**/
		$mainAppTable		   	= "receiptvoucher"; 
		$detailAppTable		   	= "rv_details";
		$applicationTable	   	= "rv_application"; 
		$chequeTable		   	= "rv_cheques"; 
		$applicableHeaderTable 	= "accountsreceivable"; 
		$applicableDetailTable 	= "ar_details"; 
		$source				   	= "RV"; 
		$customerTable 			= "partners";

		$insertResult		   	= 0;
		$op_result 				= 0;
	
		$voucherno				= (isset($data['h_voucher_no']) && (!empty($data['h_voucher_no']))) ? htmlentities(addslashes(trim($data['h_voucher_no']))) : "";
		$customer				= (isset($data['customer']) && (!empty($data['customer']))) ? htmlentities(addslashes(trim($data['customer']))) : "";
		$or_no					= (isset($data['paymentreference']) && (!empty($data['paymentreference']))) ? htmlentities(addslashes(trim($data['paymentreference']))) : "";
		$transactiondate		= (isset($data['document_date']) && (!empty($data['document_date']))) ? htmlentities(addslashes(trim($data['document_date']))) : "";
		$remarks				= (isset($data['remarks']) && (!empty($data['remarks']))) ? htmlentities(addslashes(trim($data['remarks']))) : "";
		$totalamount			= (isset($data['total_debit']) && (!empty($data['total_debit']))) ? htmlentities(addslashes(trim($data['total_debit']))) : "";
		$paymenttype			= (isset($data['paymentmode']) && (!empty($data['paymentmode']))) ? htmlentities(addslashes(trim($data['paymentmode']))) : "";
		$total_debit			= (isset($data['total_debit']) && (!empty($data['total_debit']))) ? htmlentities(addslashes(trim($data['total_debit']))) : "";
		$total_credit			= (isset($data['total_credit']) && (!empty($data['total_credit']))) ? htmlentities(addslashes(trim($data['total_credit']))) : "";
		$total_payment			= (isset($data['total_payment']) && (!empty($data['total_payment']))) ? htmlentities(addslashes(trim($data['total_payment']))) : $total_debit;
		$overpayment 			= (isset($data['overpayment']) && (!empty($data['overpayment']))) ? htmlentities(addslashes(trim($data['overpayment']))) : 	"";
		$credits_applied 		= (isset($data['total_cred_used']) && (!empty($data['total_cred_used']))) ? htmlentities(addslashes(trim($data['total_cred_used']))) : 	"";
		$old_cred_used 			= (isset($data['old_cred_used']) && (!empty($data['old_cred_used']))) ? htmlentities(addslashes(trim($data['old_cred_used']))) : 	"";
		$task 					= (isset($data['h_task']) && (!empty($data['h_task']))) ? htmlentities(addslashes(trim($data['h_task']))) : "";
		$h_check_rows 			= (isset($data['selected_rows']) && (!empty($data['selected_rows']))) ? $data['selected_rows'] : "";
		// $credit_input 			= (isset($data['credit_input']) && (!empty($data['credit_input']))) ? htmlentities(addslashes($data['credit_input'])) 	:	0;
		$invoice_data  			= str_replace('\\', '', $h_check_rows);
		$invoice_data  			= html_entity_decode($invoice_data);
		$picked_payables		= json_decode($invoice_data, true);
		// var_dump($picked_payables);
		// $source				   	= (!empty($picked_payables)) ? "RV" : "";

		$exchangerate			= (!empty($data['paymentrate'])) ? $data['paymentrate'] : "1.00";
		$convertedamount		= (!empty($data['paymentconverted'])) ? $data['paymentconverted'] : $total_payment;
		$checkdate				= (!empty($data['checkdate'])) ? $data['checkdate'] : "0000-00-00";

		/**TRIM COMMAS FROM AMOUNTS**/
		$totalamount			= str_replace(',','',$totalamount);
		$total_payment			= str_replace(',','',$total_payment);
		$convertedamount 		= str_replace(',','',$convertedamount);
		$total_debit 			= str_replace(',','',$total_debit);
		$total_credit 			= str_replace(',','',$total_credit);
		$old_cred_used 			= str_replace(',','',$old_cred_used);

		$gen_value              = $this->getValue("receiptvoucher", "COUNT(*) as count", " voucherno != ''");	
		$temporary_voucher     	= (!empty($gen_value[0]->count)) ? 'RV_'.($gen_value[0]->count + 1) : 'RV_1';

		$voucherno 				= (!empty($voucherno)) ? $voucherno : $temporary_voucher;
		
		/**CLEAN PASSED DATA**/
		$aJournalData 	= array();
		$aChequeData 	= array();

		foreach($data as $postIndex => $postValue)
		{
			if($postIndex=='h_accountcode' ||	$postIndex=='detailparticulars'  || $postIndex=='ischeck' || $postIndex=='debit' || $postIndex=='credit')
			{
				$a		= '';
				foreach($postValue as $postValueIndex => $postValueIndexValue){
					if($postIndex == 'debit' || $postIndex == 'credit'){
						$a = str_replace(',', '', $postValueIndexValue);
					}
					else{
						$a = htmlentities(addslashes(trim($postValueIndexValue)));
					}
					$aJournalData[$postIndex][$postValueIndex] = $a;	
				}	
			}
			
			if(($postIndex == 'chequeaccount' || $postIndex == 'bank'  || $postIndex == 'chequenumber' || $postIndex == 'chequedate' || $postIndex == 'chequeamount' || $postIndex == 'chequeconvertedamount') && !empty($postValue) )
			{
				$b		= '';
				foreach($postValue as $postValueIndex => $postValueIndexValue){
					if($postIndex == 'chequeamount' || $postIndex == 'chequeconvertedamount'){
						$b = str_replace(',', '', $postValueIndexValue);
					}else if($postIndex == 'chequedate'){
						$b = ($postValueIndexValue != '') ? date("Y-m-d", strtotime($postValueIndexValue)) : "0000-00-00";
					}else{
						$b = htmlentities(addslashes(trim($postValueIndexValue)));
					}
					$aChequeData[$postIndex][$postValueIndex] = $b;
				}
			}
		}

		if(!empty($aJournalData)){
			foreach($aJournalData as $arrayDataIndex => $arrayDataValue){
				foreach($arrayDataValue as $postValueIndex => $postValueIndexValue){	
					$tempArray[$postValueIndex][$arrayDataIndex] = $postValueIndexValue;
				}
			}
		}

		/**CHEQUE DETAILS**/
		if(!empty($aChequeData))
		{
			foreach($aChequeData as $chequeDataIndex => $chequeDataValue){
				foreach($chequeDataValue as $chequeValueIndex => $chequeValueIndexValue){
					$newArray[$chequeValueIndex][$chequeDataIndex] = $chequeValueIndexValue;
				}
			}
		}

		$code				= 1;
		$linenum			= 1;
		$totalamount		= 0;
		
		$cheque_info		= array();
		$tempCheque 		= array();

		// For Cheque
		if(!empty($newArray))
		{
			$linecount		= 1;

			foreach($newArray as $newArrayIndex => $newArrayValue){
				$chequeaccount			= (!empty($newArrayValue['chequeaccount'])) ? $newArrayValue['chequeaccount'] : "";
				$bank					= (!empty($newArrayValue['bank'])) ? $newArrayValue['bank'] : "";
				
				$chequenumber			= (!empty($newArrayValue['chequenumber'])) ? $newArrayValue['chequenumber'] : "";
				$chequedate				= (!empty($newArrayValue['chequedate'])) ? $newArrayValue['chequedate'] : "";
				$chequeamount			= (!empty($newArrayValue['chequeamount'])) ? $newArrayValue['chequeamount'] : "";
				$chequedate				= $this->date->dateDbFormat($chequedate);
				
				if(!empty($chequedate) && !empty($chequeaccount) && !empty($chequenumber) && !empty($chequeamount)){
					$cheque_header['voucherno']				= $voucherno;
					$cheque_header['transtype']				= "RV";
					
					$cheque_header['linenum']				= $linecount;
					$cheque_header['chequeaccount']			= $chequeaccount;
					$cheque_header['chequenumber']			= $chequenumber;
					$cheque_header['bank']					= $bank;
					
					$cheque_header['chequedate']			= $chequedate;
					$cheque_header['chequeamount']			= $chequeamount;
					$cheque_header['chequeconvertedamount']	= $chequeamount;
					$cheque_header['stat']					= 'uncleared';
				
					$linecount++;
					$tempCheque[] 							= $cheque_header;
				}
			}
		}

		$isExist						= $this->getValue($mainAppTable, array("stat"), "voucherno = '$voucherno' AND stat IN ('posted','temporary','cancelled') ");
		$status							= (!empty($isExist[0]->stat)) ? "open" : "temporary";
		$valid 							= 0;

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
		$post_header['releaseby']		= USERNAME;
		$post_header['overpayment'] 	= $overpayment;
		$post_header['credits_used'] 	= $credits_applied;
		$post_header['currencycode']	= 'PHP';
		$post_header['amount']			= $total_payment;
		$post_header['exchangerate']	= $exchangerate;
		$post_header['convertedamount']	= $convertedamount;
		$post_header['source']			= $source;
		$post_header['paymenttype']		= $paymenttype;	
		// $post_header['referenceno']		= $referenceno;
		$post_header['or_no']			= $or_no;
		
		$post_header['stat']			= $status;
		$post_header['postedby']		= USERNAME;
		$post_header['postingdate']		= $datetime;

		// Header Data Array
		$tempData[] = $post_header;

		/**INSERT HEADER**/
		if($status == 'temporary')
		{	
			$this->db->setTable($mainAppTable)
					->setWhere("voucherno = '$voucherno'");
			$insertResult = $this->db->runDelete();

			if(!$insertResult)
				$valid++;
			
			$this->db->setTable($mainAppTable) 
				->setValues($tempData);

			$insertResult = ($valid == 0) ? $this->db->runInsert() : false;

			if(!$insertResult){
				$code 		= 0;
				$errmsg[] 	= "<li>Error in Saving Receipt Voucher Header.</li>";
			}	
		}
		else
		{
			$insertResult = $this->db->setTable($mainAppTable)
								->setValues($tempData)
								->setWhere("voucherno = '$voucherno'")
								->runUpdate();

			if(!$insertResult){
				$code 		= 0;
				$errmsg[] = "<li>Error in Updating Receipt Voucher Header.</li>";
				}
		}

		$iDetailLineNum = 1;
		$aPvDetailArray = array();
		// var_dump($tempArray);
		foreach($tempArray as $tempArrayIndex => $tempArrayValue)
		{
			$accountcode 						= $tempArrayValue['h_accountcode'];
			$detailparticulars					= $tempArrayValue['detailparticulars'];
			$debit			    				= $tempArrayValue['debit'];
			$credit			    				= $tempArrayValue['credit'];
			$ischeck 							= isset($tempArrayValue['ischeck']) && $tempArrayValue != "" 	?	$tempArrayValue['ischeck'] 	:	"no";

			$post_detail['voucherno']			= $voucherno;
			$post_detail['linenum']				= $iDetailLineNum;
			$post_detail['transtype']			= $source;
			$post_detail['accountcode']			= $accountcode;
			$post_detail['debit']				= $debit;
			$post_detail['credit']				= $credit;
			$post_detail['converteddebit']		= $debit;
			$post_detail['convertedcredit'] 	= $credit;
			$post_detail['currencycode']		= 'PHP';
			$post_detail['detailparticulars'] 	= $detailparticulars;
			$post_detail['ischeck']				= $ischeck;
			$post_detail['stat']				= $post_header['stat'];

			$iDetailLineNum++;
			$aPvDetailArray[]					= $post_detail;
		}

		$aPvApplicationArray 	= array();
		$total_credits_used 	= 0;
		if(!empty($picked_payables)){
			$iApplicationLineNum	= 1;
			foreach ($picked_payables as $pickedKey => $pickedValue) {
				$payable 	= $pickedValue['vno'];
				$amount 	= $pickedValue['amt'];
				$discount 	= $pickedValue['dis'];
				$credits 	= $pickedValue['cred'];
				
				$ret_bal	= $this->getValue("accountsreceivable", array("balance"), "voucherno = '$payable' AND stat NOT IN ('temporary','cancelled') ");
				$balance 	= isset($ret_bal[0]->balance) 	?	$ret_bal[0]->balance 	:	0;

				$amount 	= str_replace(',','',$amount);
				$discount 	= str_replace(',','',$discount);
				$credits 	= str_replace(',','',$credits);
				
				$totalamount+=$amount;
				$total_credits_used+=$credits;

				$post_application['voucherno']			= $voucherno;
				$post_application['transtype']			= 'RV';
				$post_application['linenum']			= $iApplicationLineNum;
				$post_application['arvoucherno']		= $payable;
				$post_application['discount']			= $discount;
				$post_application['amount']		 		= $amount;
				$post_application['credits_used'] 		= $credits;
				$post_application['overpayment'] 		= ($overpayment > 0) ? $amount - $balance : 0;
				$post_application['currencycode']		= 'PHP';
				$post_application['exchangerate']		= '1.00';
				$post_application['convertedamount']	= $amount;
				$post_application['stat']			 	= 'posted';

				$iApplicationLineNum++;
				$aPvApplicationArray[]					= $post_application;
			}
		}

		/**
		 * Get previous tagged payables
		 */
		$aOldApplicationObj = $this->db->setTable('rv_application rv')
									->leftJoin('receiptvoucher as main ON main.voucherno = rv.voucherno ')
									->setFields("rv.arvoucherno as vno, '0.00' as amt, '0.00' as bal, '0.00' as dis, '0.00' as cred")
									->setWhere(" rv.voucherno = '$voucherno' AND rv.stat NOT IN ('cancelled','temporary') ")
									->runSelect()
									->getResult();
		if(!empty($aOldApplicationObj) && !is_null($aOldApplicationObj)){
			$aOldApplicationArray 	= json_decode(json_encode($aOldApplicationObj), true);
			$combined_payables 		= $this->unique_multidim_array(array_merge($picked_payables,$aOldApplicationArray), 'vno');
		}else{
			$combined_payables 		= $picked_payables;
		}

		//var_dump($combined_payables);

		// details and pv_application
		if(!empty($aPvApplicationArray) && !is_null($aPvApplicationArray)){
			$isAppDetailExist	= $this->getValue($applicationTable, array("COUNT(*) AS count"), " voucherno = '$voucherno'");
			
			if($isAppDetailExist[0]->count > 0){
	
				$this->db->setTable($detailAppTable)
						->setWhere("voucherno = '$voucherno'")
						->runDelete();
	
				$insertResult = $this->db->setTable($detailAppTable) 
									->setValues($aPvDetailArray)
									->setWhere("voucherno = '$voucherno'")
									->runInsert();
								
				if(!$insertResult){
					$code 		= 0;
					$errmsg[] 	= "<li>Error in Saving Receipt Voucher Details.</li>";
				}
	
				$this->db->setTable($applicationTable)
						->setWhere("voucherno = '$voucherno'")
						->runDelete();
						
				$insertResult = $this->db->setTable($applicationTable) 
									->setValues($aPvApplicationArray)
									->setWhere("voucherno = '$voucherno'")
									->runInsert();
							
				if(!$insertResult){
					$code 		= 0;
					$errmsg[] 	= "<li>Error in Updating Receipt Voucher Application.</li>";
				}
			}else if(!empty($isAppDetailExist)){
				$insertResult = $this->db->setTable($detailAppTable) 
									->setValues($aPvDetailArray)
									->runInsert();
									
				if(!$insertResult){
					$code 		= 0;
					$errmsg[] 	= "<li>Error in Updating Receipt Voucher Details.</li>";
				}
	
				$insertResult = $this->db->setTable($applicationTable) 
									->setValues($aPvApplicationArray)
									->runInsert();
	
				if(!$insertResult){
					$code 		= 0;
					$errmsg[] 	= "<li>Error in Updating Receipt Voucher Application.</li>";
				}
			}
				
			/**UPDATE HEADER AMOUNTS**/
			$update_info				= array();
			$update_info['netamount']	= $totalamount;
	
			$insertResult = $this->db->setTable($mainAppTable) 
								->setValues($update_info)
								->setWhere("voucherno = '$voucherno'")
								->runUpdate();
	
			if(!$insertResult){
				$code 		= 0;
				$errmsg[] 	= "<li>Error in Updating Receipt Voucher Header.</li>";
			}	
		}
		
		/**INSERT TO CHEQUES TABLE**/
		if(strtolower($paymenttype) == 'cheque')
		{
			$insertResult = $this->db->setTable($chequeTable)
								->setWhere("voucherno = '$voucherno'")
								->runDelete();
			if($insertResult && !empty($tempCheque))
			{
				$insertResult = $this->db->setTable($chequeTable)
									->setValues($tempCheque)
									->runInsert();

				if(!$insertResult){
					$code 		= 0;
					$errmsg[] = "<li>Error in Saving in Cheque Details.</li>";
				}
			}
		}

		/**
		 * Update Accounts Payable Balance
		 */
		if(!empty($combined_payables)){
			$aPvApplicationArray 	= array();
			$iApplicationLineNum	= 1;
			foreach ($combined_payables as $pickedKey => $pickedValue) {
				$payable 					= $pickedValue['vno'];
				$amount 					= $pickedValue['amt'];
				$discount 					= $pickedValue['dis'];
				$credits 					= $pickedValue['cred'];

				$data['avoucherno'] 		= $payable;

				$applied_sum				= 0;
				$applied_discount			= 0;
				$applied_forexamount		= 0;

				$invoice_amounts			= $this->getValue(
												$applicableHeaderTable, 
												array(
													"amount as convertedamount"
												), 
												" voucherno = '$payable' AND stat IN('open','posted') "
											);

				$applied_amounts			= $this->getValue(
												$applicationTable, 
												array(
													"COALESCE(SUM(amount),0) convertedamount",
													"COALESCE(SUM(discount),0) discount",
													"COALESCE(SUM(credits_used),0) credits",
													"COALESCE(SUM(overpayment),0) overpayment",
													"COALESCE(SUM(forexamount),0) forexamount"
												), 
												"  arvoucherno = '$payable' AND stat IN('open','posted') "
											);
				
				$invoice_amount				= (!empty($invoice_amounts)) ? $invoice_amounts[0]->convertedamount : 0;
				$applied_credits 			= (!empty($applied_amounts[0]->credits)) ? $applied_amounts[0]->credits : 0;
				$applied_disc 				= (!empty($applied_amounts[0]->discount)) ? $applied_amounts[0]->discount : 0;
				$applied_sum				= $applied_amounts[0]->convertedamount - $applied_amounts[0]->forexamount + $applied_credits + $applied_disc;
				$applied_sum				= (!empty($applied_sum)) ? $applied_sum : 0;

				$balance_info['amountreceived']	= $applied_sum;
				$balance_info['excessamount'] 	= $overpayment;
				$balance_amt 					= $invoice_amount - $applied_sum;
				$balance_info['balance']		= ($balance_amt >= 0) 	?	$balance_amt	:	0;
				
				$insertResult = $this->db->setTable($applicableHeaderTable)
								->setValues($balance_info)
								->setWhere("voucherno = '$payable'")
								->runUpdate();
					
				if($insertResult){
					$partner_dtl 	=$this->getValue(
											$customerTable, 
											"credits_amount", 
											" partnercode = '$customer' "
										);

					$existing_credit	= ($partner_dtl[0]->credits_amount > 0) ? $partner_dtl[0]->credits_amount 	:	0;
					
					$existing_credit 	+=	$overpayment;
					$partner_info['credits_amount'] 	=	( $existing_credit - $credits_applied );

					$insertResult 	=	$this->db->setTable($customerTable)
												 ->setValues($partner_info)
												 ->setWhere("partnercode = '$customer'")
												 ->runUpdate();
				}

				// Insert Overpayment on Credit Memo
				if($insertResult && $total_credits_used > 0){
					$data['temp_voucher'] 	=	$voucherno;
					$data['overpayment'] 	= 	$credits;
					// echo $credits;
					$arvoucher 				= 	(isset($data['avoucherno']) && (!empty($data['avoucherno']))) ? htmlentities(addslashes(trim($data['avoucherno'])))	:	"";
					
					$reference_inv 			= 	$this->getValue("accountsreceivable", array("invoiceno as number"), "voucherno = '$arvoucher'");
					$invoiceno 				= 	isset($reference_inv[0]->number) 	?	$reference_inv[0]->number	:	"";
					// echo $old_cred_used; echo $total_credits_used;
					if( $task == 'create'){
						$op_result 			=	$this->generateCreditMemo($data);
					} else if($old_cred_used != $total_credits_used && $task == 'edit'){	
						// echo " si_no = '$voucherno' AND sourceno = '$invoiceno' AND invoiceno = '$arvoucher' AND transtype = 'CM' ";
						$cm_existing 		=	$this->getValue(
													"journalvoucher", 
													"voucherno", 
													" si_no = '$voucherno' AND sourceno = '$invoiceno' AND invoiceno = '$arvoucher' AND transtype = 'CM' "
												);
												// echo " sourceno = '$invoiceno' AND invoiceno = '$arvoucher' AND transtype = 'CM' ";

						$cm_voucher			= 	isset($cm_existing[0]->voucherno) ? $cm_existing[0]->voucherno 	:	"";
					
						if($credits == 0){
							$del_result 		=	$this->cancelCreditMemo($cm_voucher);
						} else {
							$del_result 		=	$this->deleteCreditMemoDetails($cm_voucher);

							if($del_result){
								$op_result 		=	$this->generateCreditMemo($data, $cm_voucher);
							}
						}
					} 
				} else {
					$arvoucher 				= 	(isset($data['avoucherno']) && (!empty($data['avoucherno']))) ? htmlentities(addslashes(trim($data['avoucherno'])))	:	"";
					
					$reference_inv 			= 	$this->getValue("accountsreceivable", array("invoiceno as number"), "voucherno = '$arvoucher'");
					$invoiceno 				= 	isset($reference_inv[0]->number) 	?	$reference_inv[0]->number	:	"";

					if($old_cred_used != $total_credits_used && $task == 'edit'){	
						// echo " si_no = '$voucherno' AND sourceno = '$invoiceno' AND invoiceno = '$arvoucher' AND transtype = 'CM' ";
						$cm_existing 		=	$this->getValue(
													"journalvoucher", 
													"voucherno", 
													" si_no = '$voucherno' AND sourceno = '$invoiceno' AND invoiceno = '$arvoucher' AND transtype = 'CM' "
												);
												// echo " sourceno = '$invoiceno' AND invoiceno = '$arvoucher' AND transtype = 'CM' ";

						$cm_voucher			= 	isset($cm_existing[0]->voucherno) ? $cm_existing[0]->voucherno 	:	"";
					
						if($credits == 0){
							$del_result 		=	$this->cancelCreditMemo($cm_voucher);
						} else {
							$del_result 		=	$this->deleteCreditMemoDetails($cm_voucher);

							if($del_result){
								$op_result 		=	$this->generateCreditMemo($data, $cm_voucher);
							}
						}
					}
				}
			}
		}

		return array(
			'code' 		=> $code,
			'voucher' 	=> $voucherno,
			'errmsg' 	=> $errmsg
		);
	}

	public function cancelCreditMemo($voucherno) {
		$result	= $this->db->setTable('journalvoucher')
							->setValues(array('stat'=>'cancelled'))
							->setWhere("voucherno = '$voucherno' AND transtype = 'CM'")
							->runUpdate();
		if ($result) {
			if ($result) {
				$this->log->saveActivity("Cancel Journal Voucher [$voucherno]");
			}
			$result = $this->cancelCreditMemoDetails($voucherno);
		}

		return $result;
	}

	public function cancelCreditMemoDetails($voucherno) {
		$result	= $this->db->setTable('journaldetails')
							->setValues(array('stat'=>'cancelled'))
							->setWhere("voucherno = '$voucherno' AND transtype = 'CM'")
							->runUpdate();
		
		if($result){
			$result = $this->reverseEntries($voucherno);
		}
		return $result;
	}

	public function deleteCreditMemo($voucherno) {
		// echo $voucherno;
		$result	= $this->db->setTable('journalvoucher')
							->setWhere("voucherno = '$voucherno' AND transtype = 'CM'")
							->setLimit(1)
							->runDelete();
		if ($result) {
			$this->log->saveActivity("Delete Journal Voucher [$voucherno]");
			// $result = $this->deleteCreditMemoDetails($voucherno);
		}
		return $result;
	}

	public function deleteCreditMemoDetails($voucherno) {
		$result	= $this->db->setTable('journaldetails')
							->setWhere("voucherno = '$voucherno' AND transtype = 'CM'")
							->runDelete();
		
		if ($result) {
			$result = $this->deleteCreditMemo($voucherno);
		}
							
		return $result;
	}

	public function updateJournalVoucher($data, $data2, $voucherno, $log = false) {
		$debit	= $this->removeComma($data2['debit']);
		$total	= 0;
		foreach ($debit as $entry) {
			$total += $entry;
		}
		$data['stat']		= 'posted';
		$data['period']		= date("n", strtotime($data['transactiondate']));
		$data['fiscalyear']	= date("Y", strtotime($data['transactiondate']));
		$data['amount']		= $total;
		$data['transtype']	= 'JV';
		
		$result = $this->db->setTable('journalvoucher')
							->setValues($data)
							->setWhere("voucherno = '$voucherno'")
							->setLimit(1)
							->runUpdate();
		if (isset($data['voucherno'])) {
			$voucherno =  $data['voucherno'];
		}
		if ($result) {
			if ($result && $log) {
				$this->log->saveActivity("$log Journal Voucher [$voucherno}");
			}
			$result = $this->updateJournalVoucherDetails($data2, $voucherno);
		}
		return $result;
	}

	public function updateJournalVoucherDetails($data, $voucherno) {
		$result = $this->db->setTable('journaldetails')
							->setWhere("voucherno = '$voucherno'")
							->runDelete();
		
		$linenum = array();
		foreach ($data['debit'] as $key => $value) {
			$linenum[] = $key + 1;
		}
		if ($result) {
			$data['voucherno']			= $voucherno;
			$data['transtype']			= 'JV';
			$data['stat']				= 'posted';
			$data['debit']				= $this->removeComma($data['debit']);
			$data['credit']				= $this->removeComma($data['credit']);
			$data['converteddebit']		= $this->removeComma($data['debit']);
			$data['convertedcredit']	= $this->removeComma($data['credit']);
			$data['linenum']			= $linenum;
			$result = $this->db->setTable('journaldetails')
								->setValuesFromPost($data)
								->runInsert();
		}
		return $result;
	}

	public function reverseEntries($voucherno) {
		$count = $this->db->setTable('journaldetails')
				->setFields('*')
				->setWhere("voucherno = '$voucherno' AND transtype = 'CM'")
				->runSelect()
				->getResult();
				
		$result 	=	0;

		if(!empty($count))
		{
			$ctr = count($count) + 1;
			for($i = 0; $i < count($count); $i++)
			{
				$insert_info['voucherno']			= $count[$i]->voucherno;
				$insert_info['checkno']				= $count[$i]->checkno;
				$insert_info['transtype']			= $count[$i]->transtype;
				$insert_info['linenum']				= $ctr;
				$insert_info['slcode']				= $count[$i]->slcode;
				$insert_info['source']				= $count[$i]->source;
				$insert_info['costcentercode']		= $count[$i]->costcentercode;
				$insert_info['accountcode']			= $count[$i]->accountcode;
				$insert_info['debit']				= $count[$i]->credit;
				$insert_info['credit']				= $count[$i]->debit;
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

				$result = $this->db->setTable('journaldetails')
									->setValues($insert_info)
									->runInsert();
				$ctr++;
			}
		}
		return $result;
			
	}

	public function generateCreditMemo($data, $cmvoucher=""){
		$voucherno				= (isset($data['temp_voucher']) && (!empty($data['temp_voucher']))) ? htmlentities(addslashes(trim($data['temp_voucher']))) : "";
		$customer				= (isset($data['customer']) && (!empty($data['customer']))) ? htmlentities(addslashes(trim($data['customer']))) : "";
		$or_no					= (isset($data['paymentreference']) && (!empty($data['paymentreference']))) ? htmlentities(addslashes(trim($data['paymentreference']))) : "";
		$transactiondate		= (isset($data['transactiondate']) && (!empty($data['transactiondate']))) ? htmlentities(addslashes(trim($data['transactiondate']))) : "";
		$remarks				= (isset($data['remarks']) && (!empty($data['remarks']))) ? htmlentities(addslashes(trim($data['remarks']))) : "";
		$overpayment 			= (isset($data['overpayment']) && (!empty($data['overpayment']))) ? htmlentities(addslashes(trim($data['overpayment']))) : 	"";
		$task 					= (isset($data['h_task']) && (!empty($data['h_task']))) ? htmlentities(addslashes(trim($data['h_task']))) : "";
		$arvoucher 				= (isset($data['avoucherno']) && (!empty($data['avoucherno']))) ? htmlentities(addslashes(trim($data['avoucherno'])))	:	"";
		
		$seq 					= new seqcontrol();
		$cm_no 					= ($cmvoucher != "") ? $cmvoucher 	:	$seq->getValue("CM");
		$exchangerate			= '1.00';
		$datetime	  			= date("Y-m-d H:i:s");

		$transactiondate			= $this->date->dateDbFormat($transactiondate); 
		$period						= date("n",strtotime($transactiondate));
		$fiscalyear					= date("Y",strtotime($transactiondate));

		$total_credits_used 		= 0;

		$reference_inv 				= $this->getValue("accountsreceivable", array("invoiceno as number"), "voucherno = '$arvoucher'");
		$invoiceno 					= isset($reference_inv[0]->number) 	?	$reference_inv[0]->number	:	"";

		$s_ret 						= $this->getValue("salesinvoice", array("voucherno"), "voucherno = '$invoiceno'");
		$invoiceno 					= isset($s_ret[0]->voucherno) 	?	$s_ret[0]->voucherno	:	"";

		$op_arr['voucherno'] 		= $cm_no;
		$op_arr['transtype'] 		= "CM";
		$op_arr['stat'] 			= "posted";
		$op_arr['credit_stat'] 		= "unused";
		$op_arr['transactiondate']	= $transactiondate;
		$op_arr['fiscalyear'] 		= $fiscalyear;
		$op_arr['period'] 			= $period;
		$op_arr['customer']			= $customer;
		$op_arr['partner']			= $customer;
		$op_arr['currencycode']		= "PHP";
		$op_arr['exchangerate']		= $exchangerate;
		$op_arr['invoiceno'] 		= $arvoucher;
		$op_arr['amount']			= $overpayment;
		$op_arr['convertedamount']	= $overpayment * $exchangerate;
		$op_arr['referenceno'] 		= ($invoiceno!="") ? $invoiceno 	:	$arvoucher;
		$op_arr['source'] 			= "excess";
		$op_arr['sourceno']			= $invoiceno;
		$op_arr['si_no'] 			= $voucherno;
		$op_arr['remarks'] 			= "Reference : ".$invoiceno." - ".$arvoucher;

		$result 	=	 $this->insertdata('journalvoucher',$op_arr);

		if( $result ){
			$data['cvoucher'] 	=	$cm_no;
			$this->log->saveActivity("Added/Updated Credit Memo [$cm_no]");
			$result 			=	$this->generateCMDetails($data);
		}

		return $result;
	}

	public function generateCMDetails($data){
		$voucherno				= (isset($data['h_voucher_no']) && (!empty($data['h_voucher_no']))) ? htmlentities(addslashes(trim($data['h_voucher_no']))) : "";
		$customer				= (isset($data['customer']) && (!empty($data['customer']))) ? htmlentities(addslashes(trim($data['customer']))) : "";
		$or_no					= (isset($data['paymentreference']) && (!empty($data['paymentreference']))) ? htmlentities(addslashes(trim($data['paymentreference']))) : "";
		$transactiondate		= (isset($data['transactiondate']) && (!empty($data['transactiondate']))) ? htmlentities(addslashes(trim($data['transactiondate']))) : "";
		$remarks				= (isset($data['remarks']) && (!empty($data['remarks']))) ? htmlentities(addslashes(trim($data['remarks']))) : "";
		$overpayment 			= (isset($data['overpayment']) && (!empty($data['overpayment']))) ? htmlentities(addslashes(trim($data['overpayment']))) : 	"";
		$task 					= (isset($data['h_task']) && (!empty($data['h_task']))) ? htmlentities(addslashes(trim($data['h_task']))) : "";
		$cm_no					= (isset($data['cvoucher']) && (!empty($data['cvoucher']))) ? htmlentities(addslashes(trim($data['cvoucher']))) : "";

		$transactiondate 		= 	$this->date->dateDBFormat();
		$period					= 	date("n",strtotime($transactiondate));
		$fiscalyear				= 	date("Y",strtotime($transactiondate));

		$result 				=	0;

		$exchangerate			= '1.00';

		$ret_acct 				=	$this->retrieveOPdetails();
		$debit_acct 			=	isset($ret_acct[0]->accountcode) 	?	$ret_acct[0]->accountcode 	:	"";
	
		$details['voucherno'] 			=	$cm_no;
		$details['transtype'] 			=	'CM';
		$details['linenum'] 			=	1;
		$details['accountcode'] 		= 	$debit_acct;
		$details['debit'] 				=  	$overpayment;
		$details['credit'] 				=	0;
		$details['exchangerate'] 		= 	$exchangerate;
		$details['converteddebit'] 		= 	$overpayment * $exchangerate;
		$details['convertedcredit'] 	= 	0;
		$details['detailparticulars'] 	= 	"";
		$details['stat'] 				= 	"posted";

		$result 	=	 $this->insertdata('journaldetails',$details);

		if( $result ) {
			$ret_acct 		=	$this->retrieveOPDebitdetails();
			$op_acct 		=	isset($ret_acct[0]->accountcode) 	?	$ret_acct[0]->accountcode 	:	"";
		
			$details['voucherno'] 			=	$cm_no;
			$details['transtype'] 			=	'CM';
			$details['linenum'] 			=	2;
			$details['accountcode'] 		= 	$op_acct;
			$details['debit'] 				=  	0;
			$details['credit'] 				=	$overpayment;
			$details['exchangerate'] 		= 	$exchangerate;
			$details['converteddebit'] 		= 	0;
			$details['convertedcredit'] 	= 	$overpayment * $exchangerate;
			$details['detailparticulars'] 	= 	"";
			$details['stat'] 				= 	"posted";

			$result 	=	 $this->insertdata('journaldetails',$details);
		}

		return $result;
	}

	private function unique_multidim_array($array, $key) { 
		$temp_array = array(); 
		$i = 0; 
		$key_array = array(); 
		
		foreach($array as $val) { 
			if (!in_array($val[$key], $key_array)) { 
				$key_array[$i] = $val[$key]; 
				$temp_array[$i] = $val; 
			} 
			$i++; 
		} 
		return $temp_array; 
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
			$data_header["received"] 		= "0.00";
			$data_header["amountforreceipt"]= "0.00";
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
				$payableCount	= $this->getValue("accountsreceivable", array("COUNT(*) AS count"), 'companycode = "'.COMPANYCODE.'"');
				$payableCount 	= $payableCount[0]->count;

				// Delete Data
				$data_del["table"] = "accountsreceivable";
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
					$payableCount	= $this->getValue("accountsreceivable", array("COUNT(*) AS count"), 'companycode = "'.COMPANYCODE.'"');
					$payableCount 	= $payableCount[0]->count;
					
					// Delete Data
					$data_del["table"] = "accountsreceivable";
					$data_del["condition"] = "voucherno IN($voucherlist)";
					$this->deleteData($data_del);

					// Delete Data
					$data_del["table"] = "ar_details";
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
			$table_pv  = "rv_application AS pv";
			$pv_fields = "COALESCE(SUM(pv.amount),0) + COALESCE(SUM(pv.discount),0) + COALESCE(SUM(pv.credits_used),0)";
			$pv_cond   = "pv.arvoucherno = main.voucherno and pv.stat = 'posted'";
			$sub_select = $this->db->setTable($table_pv)
							   ->setFields($pv_fields)
							   ->setWhere($pv_cond)
							   ->buildSelect();

			$addCondition	= "AND main.amount = ($sub_select)";

			// $addCondition	= " AND main.amount = (select COALESCE(SUM(pv.amount),0) + COALESCE(SUM(pv.discount),0) from pv_application as pv where pv.apvoucherno = main.voucherno and pv.stat = 'posted') ";

		}
		else if($addCond == 'partial')
		{
			$table_pv  = "rv_application AS pv";
			$pv_fields = "COALESCE(SUM(pv.amount),0) + COALESCE(SUM(pv.discount),0) + COALESCE(SUM(pv.credits_used),0)";
			$pv_cond   = "pv.arvoucherno = main.voucherno and pv.stat = 'posted'";
			$sub_select = $this->db->setTable($table_pv)
							   ->setFields($pv_fields)
							   ->setWhere($pv_cond)
							   ->buildSelect();

			
			$pv_cond_   = "pv.arvoucherno = main.voucherno and pv.stat = 'posted'";
			$sub_select_ = $this->db->setTable($table_pv)
							   ->setFields($pv_fields)
							   ->setWhere($pv_cond_)
							   ->buildSelect();
			
			$addCondition = "AND ($sub_select) > 0 AND main.amount > ($sub_select_)";

			// $addCondition	= " AND (select COALESCE(SUM(pv.amount),0) + COALESCE(SUM(pv.discount),0) from pv_application as pv where pv.arvoucherno = main.voucherno and pv.stat = 'posted') > 0 AND main.amount > (select COALESCE(SUM(pv.amount),0) + COALESCE(SUM(pv.discount),0) from pv_application as pv where pv.arvoucherno = main.voucherno and pv.stat = 'posted') ";
		}
		else if($addCond == 'unpaid')
		{
			$table_pv  = "rv_application AS pv";
			$pv_fields = "COALESCE(SUM(pv.amount),0) + COALESCE(SUM(pv.discount),0) + COALESCE(SUM(pv.credits_used),0)";
			$pv_cond   = "pv.arvoucherno = main.voucherno and pv.stat = 'posted'";
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

		$add_query = '';
 		$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND main.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
		$add_query .= (!empty($vendfilter) && $vendfilter != '') ? "AND p.partnercode = '$vendfilter' " : "";
		$add_query .= $addCondition;

		$main_fields = array("main.transactiondate as transactiondate", "main.voucherno as voucherno", "CONCAT( first_name, ' ', last_name ) AS vendor", "main.referenceno as referenceno", "main.amount as amount", "main.balance as balance", "main.particulars");

		$main_join   = "partners p ON p.partnercode = main.customer"; //AND p.companycode
		$main_table  = "accountsreceivable as main";
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
		
		$this->db->setTable($table)
				 ->setWhere($cond);
		
		// echo $this->db->buildDelete();

		$result = $this->db->runDelete();

		return $result;
	}

	public function deletePayments($payments, $type = 'delete')
	{
		$update_info 	= array();
		$errmsg 	 	= array();

		$appTable		= "rv_application";
		$detailTable	= "rv_details";
		$mainTable		= "receiptvoucher";
		$table			= "accountsreceivable";
		$paymentField	= array('rvapp.arvoucherno','rvapp.convertedamount','rvapp.wtaxamount','rvapp.credits_used','rvapp.overpayment');
		
		$paymentArray   = $this->db->setTable("$appTable rvapp")
							   ->setFields($paymentField)
							   ->setWhere("rvapp.voucherno IN($payments) AND rvapp.stat NOT IN('cancelled','temporary')")
							   ->runSelect()
							   ->getResult();
	
		if(!empty($paymentArray))
		{
			for($i = 0; $i < count($paymentArray); $i++)
			{
				$mainvoucher	= $paymentArray[$i]->arvoucherno;
				$amount			= $paymentArray[$i]->convertedamount;
				$wtaxamount		= $paymentArray[$i]->wtaxamount;
				$credits		= $paymentArray[$i]->credits_used;
				$overpayment	= $paymentArray[$i]->overpayment;
				$discount		= 0;

				$ar_content		= $this->getValue($table, array("balance","excessamount","amountreceived"), "voucherno = '$mainvoucher' AND stat = 'posted' ");
				$balance 		= isset($ar_content[0]->balance) 		?	$ar_content[0]->balance			: 0;
				$excessamount 	= isset($ar_content[0]->excessamount) 	?	$ar_content[0]->excessamount	: 0;
				$amountpaid 	= isset($ar_content[0]->amountreceived) 	?	$ar_content[0]->amountreceived	: 0;

				$update_info['balance']		= ($balance + $amount + $discount + $credits) - $excessamount;

				// $amountpaid 	= $this->getValue($table, array("amountreceived"), "voucherno = '$mainvoucher' AND stat = 'posted' ");
				// $amountpaid 	= $amountpaid[0]->amountreceived;

				$update_info['amountreceived']	= $amountpaid - $amount - $discount - $credits;
				$update_info['excessamount']	= $excessamount - $overpayment; 

				$result = $this->db->setTable($table)
							   ->setValues($update_info)
							   ->setWhere("voucherno = '$mainvoucher'")
							   ->runUpdate();
							
			}

			$update_info			= array();
			$update_info['stat']	= ($type == 'delete') ? 'deleted' : 'cancelled';

			$app_info['stat']	= ($type == 'delete') ? 'deleted' : 'cancelled';
			// $app_info['convertedamount']	= 0.00;
			// $app_info['discount']			= 0.00;
			
			// Update pv_application
			$result = $this->db->setTable($appTable)
					->setValues($app_info)
					->setWhere("voucherno IN($payments)")
					->runUpdate();
					//    echo $this->db->getQuery();
					
			
			// Update pv_details
			$result = $this->db->setTable($detailTable)
					->setValues($update_info)
					->setWhere("voucherno IN($payments)")
					->runUpdate();

			$count = $this->db->setTable($detailTable)
					->setFields('*')
					->setWhere("voucherno IN($payments)")
					->runSelect()
					->getResult();
	
			if(!empty($count))
			{
				$ctr = count($count) + 1;
				for($i = 0; $i < count($count); $i++)
				{
					$insert_info['voucherno']			= $count[$i]->voucherno;
					$insert_info['slcode']				= $count[$i]->slcode;
					$insert_info['bankrecon_id']		= $count[$i]->bankrecon_id;
					$insert_info['linenum']				= $ctr;
					$insert_info['arvoucherno']			= $count[$i]->arvoucherno;
					$insert_info['transtype']			= $count[$i]->transtype;
					$insert_info['costcentercode']		= $count[$i]->costcentercode;
					$insert_info['accountcode']			= $count[$i]->accountcode;
					$insert_info['debit']				= $count[$i]->credit;
					$insert_info['credit']				= $count[$i]->debit;
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
					$insert_info['checkstat']			= $count[$i]->checkstat;
					$insert_info['checknumber']			= $count[$i]->checknumber;
	
					// var_dump($insert_info);
					$result = $this->db->setTable($detailTable)
										->setValues($insert_info)
										->runInsert();
					$ctr++;
				}
			}
			
			// Update paymentvoucher
			$result = $this->db->setTable($mainTable)
					->setValues($update_info)
					->setWhere("voucherno IN($payments) ")
					->runUpdate();
			
			if(!$result){
				$errmsg[] = "The system has encountered an error in updating Receipt Voucher [$payments]. Please contact admin to fix this issue.";
			}else{
				$this->log->saveActivity(ucfirst($type)." Receipt Vouchers [".str_replace("'","",$payments)."]");
			}
			return $errmsg;
		}

	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

	public function fileExportlist($data){

		$daterangefilter	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$vendfilter      	= isset($data['vendor']) ? htmlentities($data['vendor']) : ""; 
		$filter         	= isset($data['filter']) ? htmlentities($data['filter']) : "";
		$searchkey 		 	= isset($data['search']) ? htmlentities($data['search']) : "";
		$sort 		 	 	= isset($data['sort']) ? htmlentities($data['sort']) : "main.transactiondate DESC";

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? $this->date->dateDbFormat($datefilterArr[0]) : ""; 
		$datefilterTo		= (!empty($datefilterArr[1])) ? $this->date->dateDbFormat($datefilterArr[1]) : "";

		$add_query 			= '';
		
		if ($filter != 'all' && $filter != ''){
			$add_query .= " AND main.stat = '$filter' ";
		}
		if ($daterangefilter){
			$add_query .= " AND main.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' ";
		}
		if ($vendfilter && $vendfilter != 'none'){
			$add_query .= " AND p.partnercode = '$vendfilter' ";
		}
		if ($searchkey){
			$add_query .= " AND ".$this->generateSearch($searchkey, array("main.voucherno","p.partnername","coa.accountname","pvc.chequenumber","main.or_no"));
		}

		$query 		 = $this->db->setTable("receiptvoucher main")
								->setFields(
									array(
										"main.transactiondate as paymentdate",
										"main.voucherno as voucherno",
										"p.partnername as partner",
										"main.referenceno as reference",
										"main.paymenttype as paymentmode",
										"main.convertedamount as amount",
										"main.stat as status",
										"coa.accountname as bankaccount",
										"pvc.chequenumber as chequenumber",
										"pvc.chequedate as chequedate",
										"pvc.chequeamount as chequeamount",
										"pvc.stat as chequestat"
									)
								)
								->leftJoin("partners p ON p.partnercode = main.customer ")
								->leftJoin("rv_cheques as pvc ON pvc.voucherno = main.voucherno ")
								->leftJoin("chartaccount coa ON coa.id = pvc.chequeaccount ")
								->setWhere("main.stat != 'temporary' ".$add_query)
								->setOrderBy($sort)
								->setGroupBy("main.voucherno, pvc.chequenumber")
								->runSelect()
								->getResult();
		return $query;

	}

	public function retrieveAccess($groupname){
		$result = $this->db->setTable("wc_module_access")
					->setFields(array("mod_add","mod_view","mod_edit","mod_delete","mod_list","mod_print","mod_post","mod_unpost"))
					->setWhere("groupname = '$groupname' AND companycode = 'CID' AND module_name = 'Receipt Voucher'")
					->setLimit(1)
					->runSelect()
					->getResult();
		return $result;
	}

	public function retrieve_existing_credits($customer){
		
		$leftJoin 		= 	$this->db->setTable("receiptvoucher rv")
									 ->setFields("rv.customer, SUM(rv.credits_used) applied")
									 ->setWhere("rv.customer = '$customer' AND stat NOT IN ('cancelled','temporary')")
									 ->setGroupBy("rv.customer")
									 ->buildSelect();

		$credits 		= 	$this->db->setTable("accountsreceivable ar")
									 ->setFields("ar.customer, SUM(ar.excessamount) overpayment, payment.applied, IF(SUM(ar.excessamount)-payment.applied > 0, SUM(ar.excessamount)-payment.applied, 0) curr_credit")
									 ->leftJoin("($leftJoin) payment ON payment.customer = ar.customer")
									 ->setWhere("ar.customer = '$customer' AND ar.stat NOT IN ('cancelled','temporary')")
									 ->setGroupBy("ar.customer")
									 ->setOrderBy("ar.customer ASC")
									 ->runSelect()
									 ->getResult();

									//  echo $this->db->getQuery();
		return $credits;
	}

	public function retrieveOPdetails(){
		$query 	=	$this->db->setTable("fintaxcode")
							 ->setFields("salesAccount accountcode, 'yes' is_overpayment")
							 ->setWhere("fstaxcode = 'OP'")
							 ->runSelect()
							 ->getResult();

		return $query;
	}

	public function retrieveOPDebitdetails(){
		$query 	=	$this->db->setTable("fintaxcode")
							 ->setFields("purchaseAccount accountcode, 'yes' is_overpayment")
							 ->setWhere("fstaxcode = 'OP'")
							 ->runSelect()
							 ->getResult();

		return $query;
	}

	public function insertData($table, $data){
		$result 	=	$this->db->setTable($table)
				 		->setValues($data)
				 		->runInsert();
		// echo $result;

		return $result;
	}
}