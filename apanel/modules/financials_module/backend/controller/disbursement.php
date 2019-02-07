<?php
class controller extends wc_controller 
{

	public function __construct()
	{
		parent::__construct();
		$this->url 			    = new url();
		$this->payment_voucher  = new payment_voucher_model();
		$this->restrict 		= new financials_restriction_model();
		$this->input            = new input();
		$this->ui 			    = new ui();
		$this->logs  			= new log;
		$this->session			= new session();
		$this->view->title      = 'Disbursement Voucher';
		$this->show_input 	    = true;

		$this->companycode      = COMPANYCODE;
		$this->username 		= USERNAME;
	}

	public function listing()
	{
		$data["ui"]                    = $this->ui;
		$data['show_input']            = $this->show_input;
		$data['import_error_messages'] = array();
		$data["file_import_result"]    = "";
		$cmp 						   = $this->companycode;
		$data["date"] 			   	   = date("M d, Y");
		$data["task"] 			   	   = "";
		// Retrieve vendor list
		$data["vendor_list"]  = $this->payment_voucher->retrieveVendorList($data);

		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->payment_voucher->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

		$this->view->load('disbursement/disbursement_list', $data);
	}

	public function create()
	{	
		$cmp 						= $this->companycode;
		$this->view->title      	= 'Create Disbursement Voucher';
		$seq 						= new seqcontrol();

		// Initialize variables
		$data = $this->input->post(array(
			"voucherno",
			"referenceno",
			"vendorcode",
			"tinno",
			"address1",
			"duedate",
			"particulars",
			"terms",
			"date",
			"paymenttype"
		));

		$data["ui"]                	= $this->ui;
		$data['show_input']         = $this->show_input;
		$data['button_name']        = "Save";
		$data["task"] 		        = "create";
		$data["ajax_post"] 	        = "";
		$data["row_ctr"] 			= 0;
		$data["exchangerate"]       = "1.00";
		$data["row"] 			  	= 1;
		$data["transactiondate"]    = $this->date->dateFormat();
		$data["main_status"] 			= "";
		$data['budget_list'] = $this->payment_voucher->getBudgetCodes();
		// Retrieve vendor list
		$data["vendor_list"]        = $this->payment_voucher->retrieveVendorList($data);

		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;

		// Retrieve business type list
		$acc_entry_data               = array("coa.id ind","CONCAT(coa.segment5, ' - ', coa.accountname) val");
		$acc_entry_cond               = "coa.accounttype != '' AND coa.stat = 'active'";
		$acc_entry_join 			  = "chartaccount coa2 ON coa2.parentaccountcode = coa.id";
		$acc_entry_order 			  = "coa.segment5, coa2.segment5";
		$data["account_entry_list"]   = $this->payment_voucher->getValue("chartaccount coa", $acc_entry_data, $acc_entry_cond, $acc_entry_order,"","",$acc_entry_join);

		// Cash Account Options
		// $cash_account_fields 	  	= 'chart.id ind, chart.accountname val, class.accountclass';
		// $cash_account_join 	 	  	= "accountclass as class USING(accountclasscode)";
		// $cash_account_cond 	 	  	= "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P' AND stat = 'active'";
		// $cash_order_by 		 	  	= "class.accountclass";
		// $data["cash_account_list"] 	= $this->payment_voucher->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

		// Cash Account Options
		$cash_account_fields 	  	= "c.id ind , CONCAT(shortname,' - ' ,accountno ) val";
		$cash_account_cond 	 	  	= "b.stat = 'active' AND b.checking_account = 'yes'";
		$cash_order_by 		 	  	= "id desc";
		$cash_account_join 	 	  	= "chartaccount c ON b.gl_code = c.segment5";
		$data["cash_account_list"] 	= $this->payment_voucher->retrievebank("bank b", $cash_account_fields, $cash_account_cond ,$cash_account_join ,$cash_account_cond, '');

		$data["generated_id"]     	= '';

		// Application Data
		$data['sum_applied'] 		= 0;
		$data['sum_discount']		= 0;
		$data['payments'] 			= "''";

		$data["listofcheques"]		= "";
		$data["show_cheques"] 		= 'hidden';

		$data['restrict_dv'] 		= false;

		$data['status_badge']=  "";

		// Process form when form is submitted
		$data_validate = $this->input->post(array('referenceno', "h_voucher_no", "vendor", "document_date", "h_save", "h_save_new", "h_save_preview", "h_check_rows_"));
		
		if (!empty($data_validate["vendor"]) && !empty($data_validate["document_date"])) 
		{
			$errmsg = array();
			$temp 	= array();

			$voucherno = (isset($data_validate['h_voucher_no']) && (!empty($data_validate['h_voucher_no']))) ? htmlentities(trim($data_validate['h_voucher_no'])) : "";

			$isExist = $this->payment_voucher->getValue("paymentvoucher", array("voucherno"), "voucherno = '$voucherno'");

			if($isExist[0]->voucherno)
			{
				/**UPDATE MAIN TABLES**/
				$generatedvoucher			= $seq->getValue('DV');

				$update_info				= array();
				$fields = array();
				$update_info['voucherno']	= $generatedvoucher;
				$fields['voucherno'] = $update_info['voucherno'];
				$update_info['stat']		= 'open';
				$update_condition			= "voucherno = '$voucherno'";
				$updateTempRecord			= $this->payment_voucher->editData($update_info,"paymentvoucher",$update_condition);
				$updateTempRecord			= $this->payment_voucher->editData($update_info,"pv_details",$update_condition);
				$updateTempRecord			= $this->payment_voucher->updateBudget($fields, $voucherno);
				
				$update_cheque['voucherno']	= $generatedvoucher;
				$updateTempRecord			= $this->payment_voucher->editData($update_cheque,"pv_cheques",$update_condition);

				$getnextCheckno 			= $this->payment_voucher->get_check_no($generatedvoucher);
				foreach ($getnextCheckno as $value) {
					$cno = $value->checknum;
					$ca = $value->chequeaccount;
					$getBank = $this->payment_voucher->getbankid($ca);
					$bank_id = isset($getBank[0]->id) ? $getBank[0]->id : '';
					$updateCheckNo = $this->payment_voucher->updateCheck($bank_id, $cno);
				}
			}
			
			if(empty($errmsg))
			{
				// For Admin Logs
				$this->logs->saveActivity("Add New Disbursement Voucher [$generatedvoucher]");

				if(!empty($data_validate['h_save']) && $data_validate['h_save'] == 'save_preview'){
					$this->url->redirect(BASE_URL . 'financials/disbursement/view/' . $generatedvoucher);
				}else if(!empty($data_validate['h_save']) && $data_validate['h_save'] == 'save_new'){
					$this->url->redirect(BASE_URL . 'financials/disbursement/create');
				}else{
					$this->url->redirect(BASE_URL . 'financials/disbursement/');
				}
			}else{
				$data["errmsg"] = $errmsg;
			}
		}

		$this->view->load('disbursement/disbursement', $data);
	}

	public function view($sid)
	{
		$cmp 					   	= $this->companycode;

		$this->view->title			= 'View Disbursement Voucher';
		// Retrieve data
		$data         			   	= $this->payment_voucher->retrieveEditData($sid);

		$data["ui"]   			   	= $this->ui;
		$data['show_input'] 	   	= false;
		$data["button_name"] 	   	= "Edit";
		$data["task"] 	  		   	= "view";
		$data["generated_id"]  	   	= $sid;
		$data["sid"] 		   	   	= $sid;
		$data["date"] 			   	= date("M d, Y");
		$data['budget_list'] = $this->payment_voucher->getBudgetCodes();

		$data["business_type_list"]	= array();

		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;

		// Retrieve business type list
		$acc_entry_data               = array("coa.id ind","CONCAT(coa.segment5, ' - ', coa.accountname) val");
		$acc_entry_cond               = "coa.accounttype != '' AND coa.stat = 'active'";
		$acc_entry_join 			  = "chartaccount coa2 ON coa2.parentaccountcode = coa.id";
		$acc_entry_order 			  = "coa.segment5, coa2.segment5";
		$data["account_entry_list"]   = $this->payment_voucher->getValue("chartaccount coa", $acc_entry_data, $acc_entry_cond, $acc_entry_order,"","",$acc_entry_join);

		$data["vendor_list"]    	= array();

		// Main
		$vendor_details 		   	= $this->payment_voucher->getValue("partners", "partnername"," partnertype = 'supplier' AND partnercode = '".$data["main"]->vendor."'", "");
		$transactiondate 			= $data["main"]->transactiondate;
		$restrict_dv 				= $this->restrict->setButtonRestriction($transactiondate);

		$login						= $this->session->get('login');
		$groupname 					= $login['groupname'];
		$has_access 				= $this->payment_voucher->retrieveAccess($groupname, "Disbursement Voucher");
		$data['has_access'] 		= $has_access[0]->mod_edit;

		$data["voucherno"]         	= $data["main"]->voucherno;
		$data["vendorcode"]        	= $vendor_details[0]->partnername;
		$data["v_convertedamount"] 	= $data["main"]->convertedamount;
		$data["exchangerate"]      	= $data["main"]->exchangerate;
		$data["transactiondate"]   	= $this->date->dateFormat($transactiondate);
		$data["referenceno"]       	= $data["main"]->referenceno;
		$data["paymenttype"]       	= $data["main"]->paymenttype;
		$data["particulars"]       	= $data["main"]->particulars;
		$data['main_status']		= $data["main"]->status;
		$data['budget_list'] = $this->payment_voucher->getBudgetCodes();
		// Vendor/Customer Details
		$data["v_vendor"] 		   	= $data["vend"]->name;
		$data["v_email"] 		   	= $data["vend"]->email;
		$data["tinno"] 		   	   	= $data["vend"]->tinno;
		$data["address1"] 	       	= $data["vend"]->address1;
		$data["terms"] 	   		   	= $data["vend"]->terms;

		/**
		* Get the total forex amount applied
		*/
		$forex_result 			  = $this->payment_voucher->getValue("pv_application", array("SUM(forexamount) as forexamount"), "apvoucherno = '$sid' AND stat = 'posted'");
		$forexamount			  = ($forex_result[0]->forexamount != '') ? $forex_result[0]->forexamount : 0;

		$data["forexamount"] 	  = $forexamount;

		// Cash Account Options
		// $cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		// $cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		// $cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		// $cash_order_by 		 	  = "class.accountclass";
		// $data["cash_account_list"] = $this->payment_voucher->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

		$cash_account_fields 	  	= "c.id ind , CONCAT(shortname,' - ' ,accountno ) val";
		$cash_account_cond 	 	  	= "b.stat = 'active' AND b.checking_account = 'yes'";
		$cash_order_by 		 	  	= "id desc";
		$cash_account_join 	 	  	= "chartaccount c ON b.gl_code = c.segment5";
		$data["cash_account_list"] 	= $this->payment_voucher->retrievebank("bank b", $cash_account_fields, $cash_account_cond ,$cash_account_join ,$cash_account_cond, '');

		$data["noCashAccounts"]  = false;
		
		if(empty($data["cash_account_list"]))
		{
			$data["noCashAccounts"]  = true;
		}

		$payments 				= $data['payments'];

		$data["listofcheques"] 	= isset($data['rollArray'][$sid]) ? $data['rollArray'][$sid] : '';
		
		$data['payments'] 		=  json_encode($payments);
		$data["show_cheques"] 	= isset($data['rollArray'][$sid]) ? '' : 'hidden';

		$sum_applied		= 0;
		$sum_discount		= 0;
		if($payments){
			foreach($payments as $key=>$value){
				if(isset($value->amt))   
					$sum_applied += $value->amt;
				
				if(isset($value->dis))   
					$sum_discount += $value->dis;
			}
		}
		$data['sum_applied'] 	= $sum_applied;
		$data['sum_discount'] 	= $sum_discount;

		$data['restrict_dv'] 	= $restrict_dv;

		/**
		 * Status Badge
		 */
		
		$status 		= $data["main"]->status;
		if($status == 'cancelled'){
			$status_class 	= 'danger';
		} else if($status == 'open'){
			$status_class 	= 'info';
		} else if($status == 'posted'){
			$status_class 	= 'success';
		}

		$status_badge = '<span class="label label-'.$status_class.'">'.strtoupper($status).'</span>';
		$data['status_badge'] 	= $status_badge;

		$this->view->load('disbursement/disbursement', $data);
	}

	public function edit($sid)
	{
		$access				   	= $this->access->checkLockAccess('edit');
		$data         		   	= $this->payment_voucher->retrieveEditData($sid);
		
		$this->view->title		= 'Edit Disbursement Voucher';

		$data["ui"]            	= $this->ui;
		$data['show_input']    	= $this->show_input;
		$data["task"] 		   	= "edit";

		$data["generated_id"]  	= $sid;
		$data["sid"] 		   	= $sid;
		$data["date"] 		   	= date("M d, Y");
		$data['budget_list'] = $this->payment_voucher->getBudgetCodes();

		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$data['budget_list'] = $this->payment_voucher->getBudgetCodes();

		// Retrieve business type list
		$acc_entry_data               = array("coa.id ind","CONCAT(coa.segment5, ' - ', coa.accountname) val");
		$acc_entry_cond               = "coa.accounttype != '' AND coa.stat = 'active'";
		$acc_entry_join 			  = "chartaccount coa2 ON coa2.parentaccountcode = coa.id";
		$acc_entry_order 			  = "coa.segment5, coa2.segment5";
		$data["account_entry_list"]   = $this->payment_voucher->getValue("chartaccount coa", $acc_entry_data, $acc_entry_cond, $acc_entry_order,"","",$acc_entry_join);

		// // Cash Account Options
		// $cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		// $cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		// $cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		// $cash_order_by 		 	  = "class.accountclass";
		// $data["cash_account_list"] = $this->payment_voucher->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);		
		
		$cash_account_fields 	  	= "c.id ind , CONCAT(shortname,' - ' ,accountno ) val";
		$cash_account_cond 	 	  	= "b.stat = 'active' AND b.checking_account = 'yes'";
		$cash_order_by 		 	  	= "id desc";
		$cash_account_join 	 	  	= "chartaccount c ON b.gl_code = c.segment5";
		$data["cash_account_list"] 	= $this->payment_voucher->retrievebank("bank b", $cash_account_fields, $cash_account_cond ,$cash_account_join ,$cash_account_cond, '');

		// Header Data
		$voucherno 					= $data["main"]->voucherno;
		$data["voucherno"]       	= $voucherno;
		$data["referenceno"]     	= $data["main"]->referenceno;
		$data["vendorcode"]      	= $data["main"]->vendor;
		$data["exchangerate"]    	= $data["main"]->exchangerate;
		$data["transactiondate"] 	= $this->date->dateFormat($data["main"]->transactiondate);
		$data["particulars"]     	= $data["main"]->particulars;
		$data["paymenttype"]     	= $data["main"]->paymenttype;
		$data['main_status']		= $data["main"]->status;

		$data["listofcheques"]	 = isset($data['rollArray'][$sid]) ? $data['rollArray'][$sid] : array() ;
		$data["show_cheques"] 	 = isset($data['rollArray'][$sid]) ? '' : 'hidden';
		
		// Application Data
		$payments 			= $data['payments'];
		$sum_applied		= 0;
		$sum_discount		= 0;
		if($payments){
			foreach($payments as $key=>$value){
				if(isset($value->amt))   
					$sum_applied += $value->amt;
				
				if(isset($value->dis))   
					$sum_discount += $value->dis;
			}
		}
		$data['sum_applied'] 	= $sum_applied;
		$data['sum_discount'] 	= $sum_discount;
		$data['payments'] 		= json_encode($payments);

		$data['restrict_dv'] 	= true;	
		$data['has_access'] 	= 0;


		foreach ($data["listofcheques"] as $index => $cheque){
			$accountcode 	=	$cheque['chequeaccount'];
			$cash_account_fields 	  	= "c.id ind , CONCAT(shortname,' - ' ,accountno ) val, b.stat stat";
			$cash_account_cond 	 	  	= "b.stat = 'active' AND b.checking_account = 'yes' OR c.id = $accountcode";
			$cash_order_by 		 	  	= "id desc";
			$cash_account_join 	 	  	= "chartaccount c ON b.gl_code = c.segment5";
			$data["cash_account_list"] 	= $this->payment_voucher->retrievebank("bank b", $cash_account_fields, $cash_account_cond ,$cash_account_join ,$cash_account_cond, '');

		}
		// Retrieve vendor list
		$data["vendor_list"]          = $this->payment_voucher->retrieveVendorList($data);

		$data['status_badge']=  "";

		// Process form when form is submitted
		$data_validate = $this->input->post(array('referenceno', "h_voucher_no", "vendor", "document_date", "h_save", "h_save_new", "h_save_preview", "h_check_rows_"));

		if (!empty($data_validate["vendor"]) && !empty($data_validate["document_date"])) 
		{
			$update_info['stat']		= 'open';
			$update_condition			= "voucherno = '$voucherno'";
			$updateTempRecord			= $this->payment_voucher->editData($update_info,"paymentvoucher",$update_condition);
			$updateTempRecord			= $this->payment_voucher->editData($update_info,"pv_details",$update_condition);
			// $updateTempRecord			= $this->payment_voucher->editData($update_info,"pv_application",$update_condition);

			// For Admin Logs
			$this->logs->saveActivity("Update Payment Voucher [$sid]");
			$this->url->redirect(BASE_URL . 'financials/disbursement/');
		}

		$this->view->load('disbursement/disbursement', $data);
	}

	public function print_preview($voucherno) 
	{
		// Retrieve Document Info
		$sub_select = $this->payment_voucher->retrieveData("pv_application", array("SUM(amount) AS amount"), "voucherno = '$voucherno'");

		$sub_select[0]->amount;
		
		$docinfo_table  = "paymentvoucher as pv";
		$docinfo_fields = array('pv.transactiondate AS documentdate','pv.voucherno AS voucherno',"CONCAT( first_name, ' ', last_name )","'{$sub_select[0]->amount}' AS amount",'pv.amount AS pvamount', "pv.referenceno AS referenceno", "particulars AS remarks", "p.partnername AS vendor");
		$docinfo_join   = "partners as p ON p.partnercode = pv.vendor AND p.companycode = pv.companycode";
		$docinfo_cond 	= "pv.voucherno = '$voucherno'";

		$documentinfo  	= $this->payment_voucher->retrieveData($docinfo_table, $docinfo_fields, $docinfo_cond, $docinfo_join);

		$vendor 	    = $documentinfo[0]->vendor;

		// Retrieve Document Details
		$docdet_table   = "pv_details as dtl";
		$docdet_fields  = array("chart.segment5 as accountcode", "CONCAT(segment5, ' - ', accountname) as accountname", "SUM(dtl.debit) as debit","SUM(dtl.credit) as credit");
		$docdet_join    = "chartaccount as chart ON chart.id = dtl.accountcode AND chart.companycode = dtl.companycode ";
		$docdet_cond    = "dtl.voucherno = '$voucherno'";
		$docdet_groupby = "dtl.accountcode";
		$docdet_orderby = "CASE WHEN dtl.debit > 0 THEN 1 ELSE 2 END, dtl.linenum";
		
		$documentdetails = $this->payment_voucher->retrieveData($docdet_table, $docdet_fields, $docdet_cond, $docdet_join, $docdet_orderby, $docdet_groupby);

		// Retrieve Payment Details
		$paymentArray	 = $this->payment_voucher->retrievePaymentDetails($voucherno);

		// Retrieval of Voucher Status //
		$pv_v 		  = "";
		// $pv_voucherno = $this->payment_voucher->getValue("pv_application", array("voucherno"), "voucherno = '$voucherno'");
		$ap_voucher   = $this->payment_voucher->getValue("pv_details", array("apvoucherno"), "voucherno = '$voucherno'","","","apvoucherno" );
		$status = $this->payment_voucher->getStatus($voucherno);
		
		foreach ($ap_voucher as $row) {
			$apvoucher[] = $row->apvoucherno;
		}
		$ap =  implode("','" , $apvoucher);
		$ap_no = "('".$ap."')";

		$chequeArray = "";
		if(!empty($ap_voucher))
		{
			// for($p = 0; $p < count($pv_voucherno); $p++)
			// {
			// 	$pv_v .= "'".$pv_voucherno[$p]->voucherno."',";
			// }

			// $pv_v = rtrim($pv_v, ", ");

			// echo $pv_v;
			
			$cheque_table = "pv_cheques pvc";
			$cheque_fields = array("pv.referenceno referenceno", "CONCAT(segment5, ' - ', accountname) AS accountname", "pvc.chequenumber AS chequenumber", "pvc.chequedate AS chequedate", "pvc.chequeamount AS chequeamount");
			$cheque_cond = "pvc.voucherno  = '$voucherno'" ;
			$cheque_join = "chartaccount chart ON pvc.chequeaccount = chart.id LEFT JOIN paymentvoucher pv ON pv.voucherno = pvc.voucherno" ;
			$cheque_group = "pvc.chequenumber";
			$chequeArray = $this->payment_voucher->retrieveData($cheque_table, $cheque_fields, $cheque_cond, $cheque_join);
			$chequeArray_2 = $this->payment_voucher->retrieveData($cheque_table, $cheque_fields, $cheque_cond, $cheque_join,"");
		}

		// var_dump($chequeArray);

		// Retrieve Applied Payment //
		// $p_table = "pv_application pv";
		// $p_fields = array("apvoucherno voucherno", "pv.amount amount", "ap.referenceno si_no", "pv.discount discount") ;
		// $p_cond = "pv.voucherno IN($pv_v) " ;
		// $p_join = "accountspayable ap ON pv.apvoucherno = ap.voucherno" ;
		// $appliedpaymentArray = $this->payment_voucher->retrieveData($p_table, $p_fields, $p_cond, $p_join);
		
		// Setting for PDFs
		$print = new print_voucher_model('P', 'mm', 'Letter');
		$print->setDocumentType('Disbursement Voucher')
		->setDocumentInfo($documentinfo[0])
		->setVendor($vendor)
		->setVoucherStatus(strtoupper($status->stat))
				// ->setPayments($chequeArray_2)
		->setDocumentDetails($documentdetails)
		->setCheque($chequeArray)
				// ->setAppliedPayment($appliedpaymentArray)
		->drawPDF('dv_voucher_' . $voucherno);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function get_payments()
	{
		$data_post     = $this->input->post("voucherno");

		$list          = $this->payment_voucher->retrievePayments($data_post);

		$totalPayment  = 0;
		$totaldiscount = 0;
		$row_count 	   = 1;
		$table 		   = "";

		$ui 		   = $this->ui;
		$sid 		   = $data_post;

		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->payment_voucher->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

		if( !empty($list["payments"]->result) ) :
			foreach($list["payments"]->result as $key => $row)
			{
				$paymentnumber		= $row->voucherno;
				$paymentdate		= $row->transactiondate;
				$paymentdate		= $this->date->dateFormat($paymentdate);
				$paymentaccountcode	= $row->accountcode;
				$paymentaccount		= $row->accountname;
				$paymentmode		= $row->paymenttype;
				$reference			= $row->referenceno;
				$paymentamount		= $row->amount;
				$paymentstat		= $row->stat;
				$paymentcheckdate	= $row->checkdate;
				$paymentcheckdate	= ($paymentcheckdate != '0000-00-00') ? $this->date->dateFormat($paymentcheckdate) : "";
				$paymentatccode		= $row->atcCode;
				$paymentnotes		= $row->particulars;
				$checkstat			= $row->checkstat;
				$paymentdiscount	= $row->discount;
				$paymentrate		= (isset($row->exchangerate) && !empty($row->exchangerate)) ? $row->exchangerate : 1;
				$paymentconverted	= (isset($row->convertedamount) && $row->convertedamount > 0) ? $row->convertedamount : $paymentamount;
				$apvoucherno 		= $row->apvoucherno;

				$cheque_values		= "";

				$table .= '<tr>
				<td>'
				.$ui->formField('text')
				->setSplit('', 'col-md-12 no-pad')
				->setClass("input_label")
				->setName('paymentdate'.$row_count)
				->setId('paymentdate'.$row_count)
				->setValue($paymentdate)
				->setAttribute(array("readonly" => "readonly"))
				->draw(true).
				'<input value="'.$paymentnumber.'" name = "paymentnumber'.$row_count.'" id = "paymentnumber'.$row_count.'" type = "hidden">
				</td>';

				$table .= 	'<td>'
				.$ui->formField('text')
				->setSplit('', 'col-md-12 no-pad')
				->setClass("input_label")
				->setName("pmode".$row_count)
				->setId("pmode".$row_count)
				->setAttribute(array("disabled" => "disabled"))
				->setValue(ucwords($paymentmode))
				->draw(true).

				$ui->formField('dropdown')
				->setSplit('', 'col-md-12 no-pad')
				->setClass("input-sm hidden")
				->setPlaceholder('None')
				->setName('paymentmode'.$row_count)
				->setId('paymentmode'.$row_count)
				->setList(array("cash" => "Cash", "cheque" => "Check"))
				->setValue($paymentmode)
				->draw(true).
				'</td>';

				$table .= 	'<td>'
				.$ui->formField('text')
				->setSplit('', 'col-md-12 no-pad')
				->setClass("input_label")
				->setName("paymentreference".$row_count)
				->setId("paymentreference".$row_count)
				->setAttribute(array("readonly" => "readonly"))
				->setValue($reference)
				->draw(true).
				'<input value="'.$paymentcheckdate.'" name = "paymentcheckdate'.$row_count.'" id = "paymentcheckdate'.$row_count.'" type = "hidden">
				<input value="'.$paymentnotes.'" name = "paymentnotes'.$row_count.'" id = "paymentnotes'.$row_count.'" type = "hidden">
				</td>';

				$table .= '<td>'
				.$ui->formField('text')
				->setSplit('', 'col-md-12 no-pad')
				->setClass("input_label")
				->setName("pacct".$row_count)
				->setId("pacct".$row_count)
				->setValue($paymentaccount)
				->setAttribute(array("readonly" => "readonly"))
				->draw(true).

				$ui->formField('dropdown')
				->setSplit('', 'col-md-12 no-pad')
				->setClass("input-sm hidden")
				->setPlaceholder('None')
				->setName('paymentaccount'.$row_count)
				->setId('paymentaccount'.$row_count)
				->setList($data["cash_account_list"])
				->setValue($paymentaccountcode)
				->draw(true).
				'</td>';

				$table .= '<td>
				<input value="'.number_format($paymentamount,2).'" name = "paymentamount'.$row_count.'" id = "paymentamount'.$row_count.'" type = "hidden">
				<input value="'.number_format($paymentrate,2).'" name = "paymentrate'.$row_count.'" id = "paymentrate'.$row_count.'" type = "hidden">'

				.$ui->formField('text')
				->setSplit('', 'col-md-12 no-pad')
				->setClass("input_label text-right")
				->setName("paymentconverted".$row_count)
				->setId("paymentconverted".$row_count)
				->setAttribute(array("readonly" => "readonly"))
				->setValue(number_format($paymentconverted,2))
				->draw(true).

				$ui->formField('textarea')
				->setSplit('', 'col-md-12 no-pad')
				->setClass("hidden")
				->setName("chequeInput".$row_count)
				->setId("chequeInput".$row_count)
				->setValue($cheque_values)
				->draw(true).
				'</td>';

				$table .= '<td>'
				.$ui->formField('text')
				->setSplit('', 'col-md-12 no-pad')
				->setClass("input_label text-right")
				->setName("paymentdiscount".$row_count)
				->setId("paymentdiscount".$row_count)
				->setAttribute(array("readonly" => "readonly"))
				->setValue(number_format($paymentdiscount,2))
				->draw(true).
				'</td>';

				$button = (strtolower($checkstat) != 'cleared') ? '<button class="btn btn-default btn-xs" onClick="editPaymentRow(event,\'edit'.$row_count.'\', \''.$apvoucherno.'\', \''.$sid.'\');" title="Edit Payment"><span class="glyphicon glyphicon-pencil"></span></button>
				<button class="btn btn-default btn-xs" onClick="deletePaymentRow(event,\'delete'.$row_count.'\');" title="Delete Payment" ><span class="glyphicon glyphicon-trash"></span></button>
				<a role="button" class="btn btn-default btn-xs" href="'.BASE_URL.'financials/accounts_payable/print_preview/'.$sid.'" title="Print Payment Voucher" onClick = "print(\''.$paymentnumber.'\');"><span class="glyphicon glyphicon-print"></span></a>' : '<a role="button" class="btn btn-default btn-xs" href="'.BASE_URL.'financials/accounts_payable/print_preview/'.$sid.'" onClick = "print(\''.$paymentnumber.'\');" title="Print Payment Voucher" ><span class="glyphicon glyphicon-print"></span></a>';

				$table .= '<td class="text-center">'.$button.'</td>';
				$table .= '</tr>';

				$totalPayment += $paymentconverted;
				$totaldiscount+= $paymentdiscount;

				$row_count++;

			}
		else:
			$table .= "<tr>
			<td colspan = '7' class = 'text-center'>No payments issued for this payable</td>
			</tr>";
		endif;

		$dataArray = array( "list" => $table, "pagination" => $list["payments"]->pagination, "totalPayment" => $totalPayment, "totaldiscount" => $totaldiscount );
		return $dataArray;
	}

	private function update() 
	{
		$data = $this->input->post(array("table", "condition", "fields"));
		$data["condition"] = stripslashes($data["condition"]);

		/**
		* Update Database
		*/
		$result = $this->payment_voucher->editData($data["fields"], $data["table"], $data["condition"]);

		if($result)
			$msg = "success";
		else
			$msg = "error_update";

		$dataArray = array( "msg" => $msg );
		return $dataArray;
	}

	private function check()
	{
		$chequevalue = $this->input->post("chequevalue");

		/**
		* Validate cheque number
		*/
		$result  = $this->payment_voucher->getValue("pv_cheques", "chequenumber", "chequenumber = '$chequevalue'" );
		$success = false;
		$msg 	 = "";

		if(!empty($result))
		{
			$success = true;
		}

		return array( "success" => $success );
	}

	private function delete_row()
	{
		$data_var  = array('table', "condition");
		$data_post = $this->input->post($data_var);

		/**
		* Delete Database
		*/
		$result = $this->payment_voucher->deleteData($data_post);

		if($result)
			$msg = "success";
		else
			$msg = "The system has encountered an error in deleting. Please contact admin to fix this issue.";

		$dataArray = array( "msg" => $msg );
		return $dataArray;
	}

	private function ajax_delete()
	{
		$vouchers 		= $this->input->post('delete_id');
		$type 			= $this->input->post('type');
		$payments 		= "'" . implode("','", $vouchers) . "'";

		$result 		= $this->payment_voucher->deletePayments($payments,$type);

		if($result){
			$code 	= 1; 
			$msg 	= "Successfully ".$type." the vouchers.";
		}else{
			$code 	= 0; 
			$msg 	= "Sorry, the system was unable to ".$type." the vouchers.";
		}

		$dataArray = array("code" => $code,"msg"=> $msg );
		return $dataArray;
	}

	private function ajax_post() {
		$id 			= $this->input->post('id');
		$type 			= $this->input->post('type');

		$data['stat']	= "posted";
		$result 		= $this->payment_voucher->editData($data, "paymentvoucher", "voucherno = '$id'");
		$type 			= 'post';

		if($result){
			$this->payment_voucher->editData($data, "pv_details", "voucherno = '$id'");
			$code 	= 1; 
			$msg 	= "Successfully Posted voucher ".$id;
		}else{
			$code 	= 0; 
			$msg 	= "Sorry, the system was unable to Post the voucher.";
		}

		$dataArray = array("code" => $code,"msg"=> $msg );
		return $dataArray;
	}

	private function ajax_unpost() {
		$id 			= $this->input->post('id');
		$type 			= $this->input->post('type');

		$data['stat']	= "open";
		$result 		= $this->payment_voucher->editData($data, "paymentvoucher", "voucherno = '$id'");
		$type 			= 'unpost';

		if($result){
			$this->payment_voucher->editData($data, "pv_details", "voucherno = '$id'");
			$code 	= 1; 
			$msg 	= "Successfully Unposted voucher ".$id;
		}else{
			$code 	= 0; 
			$msg 	= "Sorry, the system was unable to Unpost the voucher.";
		}

		$dataArray = array("code" => $code,"msg"=> $msg );
		return $dataArray;
	}
	
	private function get_value()
	{
		$data_cond = $this->input->post("event");

		if($data_cond == "getPartnerInfo")
		{
			$data_var = array('address1', "tinno", "terms");
			$code 	  = $this->input->post("code");

			$cond 	  = "partnercode = '$code'";

			/**
			* Get Value
			*/
			$result = $this->payment_voucher->getValue("partners", $data_var, $cond);

			$dataArray = array( "address" => $result[0]->address1, "tinno" => $result[0]->tinno, "terms" => $result[0]->terms );
		}
		else if($data_cond == "exchange_rate")
		{
			$data_var = array("accountnature");
			$account  = $this->input->post("account");

			$cond 	  = "id = '$account'";

			/**
			* Get Value
			*/
			$result = $this->payment_voucher->getValue("chartaccount", $data_var, $cond);

			$dataArray = array("accountnature" => $result[0]->accountnature);
		}

		return $dataArray;
	}

	private function save_data()
	{
		$cmp       = $this->companycode;
		$result    = "";

		$data_cond = $this->input->post("h_form");
		
		if($data_cond == "vendordetail")
		{
			$data_var  = array("h_terms", "h_tinno", "h_address1", "h_querytype", "h_condition");
			$data_post = $this->input->post($data_var);
			
			/**
			* Save Vendor Detials
			*/
			$result = $this->payment_voucher->saveDetails("partners", $data_post, "vendordetail");
		}

		if($result)
			$msg = "success";
		else
			$msg = "The system has encountered an error in saving. Please contact admin to fix this issue.";

		$dataArray = array( "msg" => $msg );
		return $dataArray;

	}

	private function checkifacctisinbudget(){
		$accountcode = $this->input->post('accountcode');

		// Check if Account is used in a Budget
		$ret_result = $this->payment_voucher->checkifaccountisinbudget($accountcode);
		$result 	=	!empty($ret_result) ? 1 : 0;

		return $dataArray = array("result"=>$result);
	}

	private function checkifpairexistsinbudget() {
		$accountcode= $this->input->post('accountcode');
		$budget 	= $this->input->post('budgetcode');

		// Check if Budget_Accountcode pair exists
		$ret_result = $this->payment_voucher->checkifpairexistsinbudget($accountcode, $budget);
		$result 	= !empty($ret_result) ? 1 : 0;

		return $dataArray = array("result"=>$result);
	}
	
	private function apply_payments()
	{
		$warning = array();
		$accountchecker = array();
		$error = array();
		$save_budget = array();
		$date_checker = array();

		$data_post 	= $this->input->post();
		$codes = array('code' => 'code', 'amount' => 'amount');
		if(!empty($data_post['budgetcode'])) {
			for($arr = 0; $arr <= count($data_post['budgetcode']); $arr++) {
				if(!empty($data_post['budgetcode'][$arr])) {
					$date = date('Y-m-d', strtotime($data_post['document_date']));
					$check_date = $this->payment_voucher->checkEffectivityDate($data_post['budgetcode'][$arr], $date);
					$get_date = $this->payment_voucher->getEffectivityDate($data_post['budgetcode'][$arr]);
					if(!$check_date) {
						foreach($get_date as $key) {
							$date_checker[] = "The budget code " .$data_post['budgetcode'][$arr] . " is available on " . date('M d, Y', strtotime($key)). "<br>";
						}
					} else if(empty($date_checker)) {
						$getaccount = $this->payment_voucher->getAccountName($data_post['h_accountcode'][$arr]);
						$checkaccount = $this->payment_voucher->getAmountAndAccount($data_post['budgetcode'][$arr], $data_post['h_accountcode'][$arr], $date);
						$accountname = $getaccount->accountname;
						if(!$checkaccount) {
							$accountchecker[] = 'The account ' . $accountname . ' is not in your budget code ' .$data_post['budgetcode'][$arr]. '.</br>';
						} else {
							$amount = $checkaccount->amount;
							$type = $checkaccount->budget_check;

							if($type == 'Monitored') {
								if($data_post['debit'][$arr] != '0.00') {
									$check = in_array($data_post['budgetcode'][$arr], $codes);
									if(!$check) {
										$codes['code'] = $data_post['budgetcode'][$arr];
										$codes['amount'] = str_replace(',', '', $data_post['debit'][$arr]);
									} else {
										$codes['code'] = $data_post['budgetcode'][$arr];
										$codes['amount'] += str_replace(',', '',$data_post['debit'][$arr]);
									}
									if($check) {
										if($codes['amount'] > $amount) {
											$warning[0] = 'You were about to exceed from your budget code ' . $data_post['budgetcode'][$arr] . 
											' ' . $accountname . ' account <br>';	
										}
									} else {
										if(str_replace(',','',$data_post['debit'][$arr]) > $amount) {
											$warning[] = 'You were about to exceed from your budget code ' . $data_post['budgetcode'][$arr] . 
											' ' . $accountname . ' account <br>';
										}
									}
								} else {
									$check = in_array($data_post['budgetcode'][$arr], $codes);
									if(!$check) {
										$codes['code'] = $data_post['budgetcode'][$arr];
										$codes['amount'] = str_replace(',', '', $data_post['credit'][$arr]);
									} else {
										$codes['code'] = $data_post['budgetcode'][$arr];
										$codes['amount'] += str_replace(',', '',$data_post['credit'][$arr]);
									}
									if($check) {
										if($codes['amount'] > $amount) {
											$warning[0] = 'You were about to exceed from your budget code ' . $data_post['budgetcode'][$arr] . 
											' ' . $accountname . ' account <br>';	
										}
									} else {
										if($data_post['credit'][$arr] > $amount) {
											$warning[] = 'You were about to exceed from your budget code ' . $data_post['budgetcode'][$arr] . 
											' ' . $accountname . ' account <br>';
										}
									}
								}
							} else {
								if($data_post['debit'][$arr] != '0.00') {
									$check = in_array($data_post['budgetcode'][$arr], $codes);
									if(!$check) {
										$codes['code'] = $data_post['budgetcode'][$arr];
										$codes['amount'] = str_replace(',', '', $data_post['debit'][$arr]);
									} else {
										$codes['code'] = $data_post['budgetcode'][$arr];
										$codes['amount'] += str_replace(',', '',$data_post['debit'][$arr]);
									}
									if($check) {
										if($codes['amount'] > $amount) {
											$error[0] = 'You are not allowed to exceed budget in ' . $data_post['budgetcode'][$arr] . 
											' ' . $accountname . ' account <br>';
										} else {
											if(str_replace(',','',$data_post['debit'][$arr]) > $amount) {
												$error[] = 'You are not allowed to exceed budget in ' . $data_post['budgetcode'][$arr] . 
												' ' . $accountname . ' account <br>';
											}
										}
									} else {
										$check = in_array($data_post['budgetcode'][$arr], $codes);
										if(!$check) {
											$codes['code'] = $data_post['budgetcode'][$arr];
											$codes['amount'] = str_replace(',', '', $data_post['credit'][$arr]);
										} else {
											$codes['code'] = $data_post['budgetcode'][$arr];
											$codes['amount'] += str_replace(',', '',$data_post['credit'][$arr]);
										}

										if($check) {
											if($codes['amount'] > $amount) {
												$error[] = 'You are not allowed to exceed budget in ' . $data_post['budgetcode'][$arr] . 
												' ' . $accountname . ' account <br>';
											} else {
												if($data_post['credit'][$arr] > $amount) {
													$error[] = 'You are not allowed to exceed budget in ' . $data_post['budgetcode'][$arr] . 
													' ' . $accountname . ' account <br>';
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		$code 		= 0;
		$voucher 	= '';
		$errmsg 	= array();

		if(empty($error)) {
			$result    	= array_filter($this->payment_voucher->savePayment($data_post));
		} else {
			$code = 1;
			$result = false;
		}

		if($result)
		{
			$code 		= $result['code'];
			$voucher 	= $result['voucher'];
			$errmsg 	= $result['errmsg'];
		}

		// $book_ids	=json_decode(stripcslashes($data_post['book_ids']));
		// $book_end	=json_decode(stripcslashes($data_post['book_end']));
		// $book_last	= json_decode(stripcslashes($data_post['book_last']));

		// foreach ($book_ids as $bank => $book_id) {
		// 	foreach ($book_id as $key => $id) {
		// 		$book_last_num = isset($book_last->$bank->$id) ? $book_last->$bank->$id : $id;
		// 		$result = $this->payment_voucher->update_checks($book_last_num, $id, $bank, $book_end->{$bank}[$key]);
		// 	} 
		// }

		$dataArray = array("code" => $code, "voucher" => $voucher, "errmsg" => $errmsg, 'warning' => $warning, 'error' => $error, 'date_checker' => $date_checker);
		return $dataArray;
	}

	private function delete_payments()
	{
		$data_post = $this->input->post("voucher");

		/**
		* Delete Database
		*/
		$result = $this->payment_voucher->deletePayments($data_post);

		if(empty($result))
			$msg = "success";
		else
			$msg = $result;

		$dataArray = array( "msg" => $msg );
		return $dataArray;
	}

	private function load_payables()
	{
		$data       	= $this->input->post(array("vendor", "voucherno"));
		$task       	= $this->input->post("task");
		$search			= $this->input->post('search');

		$vno 			= $this->input->post('vno');
		
		$check_rows 	= (isset($vno) && (!empty($vno))) ? trim($vno) : "";
		$check_rows  	= str_replace('\\', '', $check_rows);
		$decode_json    = json_decode($check_rows,true);	

		$pagination     = $this->payment_voucher->retrieveAPList($data,$search);

		$table             = "";
		$j 	               = 1;
		$json_encode_array = array();
		$json_data         = array();
		$total_pay         = 0;
		$json_encode       = "";
		$edited_amount 	   = 0;

		$show_input 	   = ($task != "view") 	? 	1	:	0;

		if (empty($pagination->result)) {
			$table = '<tr><td class="text-center" colspan="8"><b>No Records Found</b></td></tr>';
		}

		if($pagination->result)
		{
			$voucher_checked = '';
			$amt_checked = '';
			$voucher_array = array();
			$amt_array = array();
			$checker = array();
			
			if(!empty($decode_json)) {
				foreach($decode_json as $value => $row)
				{	
					array_push($voucher_array, $row['vno']);
					$amt_array[$row['vno']] = $row;
				}	
			}

			for($i = 0; $i < count($pagination->result); $i++, $j++)
			{

				$date			= $pagination->result[$i]->transactiondate;
				$date			= $this->date->dateFormat($date);
				$voucher		= $pagination->result[$i]->voucherno;
				$balance		= $pagination->result[$i]->balance; 
				$totalamount	= $pagination->result[$i]->amount;
				$referenceno	= $pagination->result[$i]->referenceno;
				$voucher_checked = (in_array($voucher , $voucher_array)) ? 'checked' : '';
				$amt_checked = (in_array($voucher , $amt_array)) ? $amt_checked : '';

				$total_pay 		+= $totalamount;

				$json_encode_array["row"]       = $i;
				$json_encode_array["apvoucher"] = $voucher;
				$json_encode_array["amount"]    = $totalamount;
				$json_data[] 					= $json_encode_array;

				$json_encode 					= json_encode($json_data);

				$appliedamount	= $this->payment_voucher->getValue("pv_application", array("SUM(amount) AS amount"),"apvoucherno = '$voucher' AND stat IN('posted', 'temporary')");
				$appliedamount  = $appliedamount[0]->amount;

				$balance_2		= $balance;
				
				if (isset($amt_array[$voucher])) {
					$balance_2	= str_replace(',', '', $amt_array[$voucher]['bal']);
					$balance_2 	= str_replace(',', '', $balance_2); 
					$amount		= str_replace(',', '', $amt_array[$voucher]['amt']);
					$discount	= isset($amt_array[$voucher]['dis']) ? $amt_array[$voucher]['dis'] : '0';
					$balance_2	= ($balance_2 > 0) ? $balance_2 : $balance + $amount + $discount;

					$balance_2 	= $balance_2 - $amount - $discount;
					
				}

				$table	.= '<tr>'; 
				if($show_input){
					$table	.= 	'<td class="text-center" style="vertical-align:middle;">';
					$table	.= 		'<input type="checkbox" name="checkBox[]" id = "check'.$voucher.'" class = "icheckbox" toggleid="0" row="'.$voucher.'" '.$voucher_checked.'>'; 
					$table	.= 	'</td>';
				}
				$table	.= 	'<td class="text-left" style="vertical-align:middle;" onClick="selectPayable(\''.$voucher.'\',1);">'.$date.'</td>';
				$table	.= 	'<td class="text-left" style="vertical-align:middle;" onClick="selectPayable(\''.$voucher.'\',1);">'.$voucher.'</td>';
				$table	.= 	'<td class="text-left" style="vertical-align:middle;" onClick="selectPayable(\''.$voucher.'\',1);">'.$referenceno.'</td>';
				$table	.= 	'<td class="text-right" style="vertical-align:middle;" id = "payable_amount'.$voucher.'" onClick="selectPayable(\''.$voucher.'\',1);">'.number_format($totalamount,2).'</td>';
				$table	.= 	'<td class="text-right" style="vertical-align:middle;" id = "payable_balance'.$voucher.'" onClick="selectPayable(\''.$voucher.'\',1);" data-value="'.number_format($balance,2).'">'.number_format($balance_2,2).'</td>';
				if($voucher_checked == 'checked'){
					$table	.= 	'<td class="text-right pay" style="vertical-align:middle;">'.
					$this->ui->formField('text')
					->setSplit('', 'col-md-12')
					->setClass("input-sm text-right paymentamount")
					->setId('paymentamount'.$voucher)
					->setPlaceHolder("0.00")
					->setAttribute(
						array(
							"maxlength" => "20", 
							"onBlur" => ' formatNumber(this.id);', 
							"onClick" => " SelectAll(this.id); ",
							"onChange" => ' checkBalance(this.value,\''.$voucher.'\'); '
						)
					)
					->setValue(number_format($amount,2))
					->draw($show_input).'</td>';
				}
				else{
					$table	.= 	'<td class="text-right pay" style="vertical-align:middle;">'.
					$this->ui->formField('text')
					->setSplit('', 'col-md-12')
					->setClass("input-sm text-right paymentamount")
					->setId('paymentamount'.$voucher)
					->setPlaceHolder("0.00")
					->setAttribute(
						array(
							"maxlength" => "20", 
							"disabled" => "disabled", 
							"onBlur" => ' formatNumber(this.id);', 
							"onClick" => " SelectAll(this.id); ",
							"onChange" => ' checkBalance(this.value,\''.$voucher.'\'); '
						)
					)
					->setValue(number_format(0, 2))
					->draw($show_input).'</td>';
				}
				if($voucher_checked == 'checked'){
					$table	.= 	'<td class="text-right pay" style="vertical-align:middle;">'.
					$this->ui->formField('text')
					->setSplit('', 'col-md-12')
					->setClass("input-sm text-right discountamount")
					->setId('discountamount'.$voucher)
					->setPlaceHolder("0.00")
					->setAttribute(
						array(
							"maxlength" => "20", 
							"onBlur" => ' formatNumber(this.id);', 
							"onClick" => " SelectAll(this.id); ",
							"onChange" => ' checkBalance(this.value,\''.$voucher.'\'); '
						)
					)
					->setValue(number_format($discount, 2))
					->draw($show_input).'</td>';
					$table	.= '</tr>';
				}else{
					$table	.= 	'<td class="text-right pay" style="vertical-align:middle;">'.
					$this->ui->formField('text')
					->setSplit('', 'col-md-12')
					->setClass("input-sm text-right discountamount")
					->setId('discountamount'.$voucher)
					->setPlaceHolder("0.00")
					->setAttribute(
						array(
							"maxlength" => "20", 
							"disabled" => "disabled", 
							"onBlur" => ' formatNumber(this.id);', 
							"onClick" => " SelectAll(this.id); ",
							"onChange" => ' checkBalance(this.value,\''.$voucher.'\'); '
						)
					)
					->setValue(number_format(0, 2))
					->draw($show_input).'</td>';
					$table	.= '</tr>';
				}
			}
		}
		else
		{
			$table	.= '<tr>';
			$table	.= 	'<td class="text-center" colspan="6">- No Records Found -</td>';
			$table	.= '</tr>';
		}
		$pagination->table = $table;
		$dataArray = array( "table" => $pagination->table, "json_encode" => $json_encode, "pagination" => $pagination->pagination, "page" => $pagination->page, "page_limit" => $pagination->page_limit );
		
		return $dataArray;
	}


	private function getpvdetails()
	{
		$checkrows       = $this->input->post("checkrows");

		$invoice_data 	= (isset($checkrows) && (!empty($checkrows))) ? trim($checkrows) : "";
		$invoice_data  	= str_replace('\\', '', $invoice_data);
		$decode_json    = json_decode($invoice_data, true);
		// $cond 			= "IN(";
		$debit      	= '0.00';
		$account_amounts = array();
		
		$apvoucher_  = array();
		for($i = 0; $i < count($decode_json); $i++)
		{
			$apvoucherno = $decode_json[$i]["vno"];
			$accountcode = $this->payment_voucher->getValue('ap_details apd LEFT JOIN chartaccount AS chart ON apd.accountcode = chart.id AND chart.companycode = apd.companycode','accountcode',"voucherno = '$apvoucherno' AND chart.accountclasscode = 'ACCPAY'","","","apd.accountcode");
			$accountcode = $accountcode[0]->accountcode;
			if ( ! isset($account_amounts[$accountcode])) {
				$account_amounts[$accountcode] = 0;
			}
			$account_amounts[$accountcode] += str_replace(',', '', $decode_json[$i]["amt"]); 
			$apvoucher_[] = $apvoucherno;
			

		}
		$condi =  implode("','" , $apvoucher_);
		$cond = "('".$condi."')";

		$vendor       	= $this->input->post("vendor");
		$data["vendor"] = $vendor;
		$data["cond"]   = $cond;

		$results 		= $this->payment_voucher->retrievePVDetails($data);
		$table 			= "";
		$row 			= 1;

		// Retrieve business type list
		$acc_entry_data     = array("id ind","accountname val");
		$acc_entry_cond     = "accounttype != 'P'";
		$account_entry_list = $this->payment_voucher->getValue("chartaccount", $acc_entry_data, $acc_entry_cond, "segment5");

		$ui 	            = $this->ui;
		$show_input         = $this->show_input;

		$totaldebit = 0;
		
		if(!empty($results))
		{
			$credit      = '0.00';
			$count       = count($results);
			
			for($i = 0; $i < $count; $i++, $row++)
			{
				$accountcode       = (!empty($results[$i]->accountcode)) ? $results[$i]->accountcode : "";
				$detailparticulars = (!empty($results[$i]->detailparticulars)) ? $results[$i]->detailparticulars : "";

				// Sum of credit will go to debit side on PV
				// $debit         	   = number_format($results[$i]->sumcredit, 2);
				$debit = (isset($account_amounts[$accountcode])) ? $account_amounts[$accountcode] : 0;
				$totaldebit    	   += $credit;

				$table .= '<tr class="clone" valign="middle">';
				$table .= 	'<td class = "remove-margin">'	
				.$ui->formField('dropdown')
				->setPlaceholder('Select One')
				->setSplit('', 'col-md-12')
				->setName("accountcode[".$row."]")
				->setClass("accountcode")
				->setId("accountcode[".$row."]")
				->setList($account_entry_list)
				->setValue($accountcode)
				->draw($show_input).
				'	<input type = "hidden" class="h_accountcode" name="h_accountcode['.$row.']" id="h_accountcode['.$row.']" value="'.$accountcode.'">
				</td>';
				$table .= 	'<td class = "remove-margin">'
				.$ui->formField('text')
				->setSplit('', 'col-md-12')
				->setName('detailparticulars['.$row.']')
				->setId('detailparticulars['.$row.']')
				->setAttribute(array("maxlength" => "100"))
				->setValue($detailparticulars)
				->draw($show_input).
				'</td>';
				$table  .=  '<td class = "remove-margin">'
				.$ui->formField('text')
				->setSplit('', 'col-md-12')
				->setName('debit['.$row.']')
				->setId('debit['.$row.']')
				->setClass('debit')
				->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('debit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);"))
				->setValue(number_format($debit,2))
				->draw($show_input).			
				'</td>';
				$table 	.= '<td class = "remove-margin">'
				.$ui->formField('text')
				->setSplit('', 'col-md-12')
				->setName('credit['.$row.']')
				->setClass("text-right account_amount credit")
				->setId('credit['.$row.']')
				->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('credit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);", "readonly" => ""))
				->setValue($credit)
				->draw($show_input).
				'</td>';
				$table  .= '<td class="text-center">
				<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id='.$row.' name="chk[]" style="outline:none;" onClick="confirmDelete('.$row.');"><span class="glyphicon glyphicon-trash"></span></button>
				</td>';

				$table .= '</tr>';
			}
		}
		else
		{
			$table	.= '<tr>';
			$table	.= 	'<td class="text-center" colspan="5">- No Records Found -</td>';
			$table	.= '</tr>';
		}
		// var_dump($totaldebit);
		$dataArray = array( "table" => $table, "totaldebit" => number_format($totaldebit, 2) );
		return $dataArray;

	}

	private function load_list()
	{
		$data_post 				= $this->input->post(array("daterangefilter", "vendor", "filter", "search", "sort"));
		$data_post['voucher'] 	= "DV";
		
		$list   				= $this->payment_voucher->retrieveList($data_post);
		
		$table  	= "";

		if( !empty($list->result) ) :
			$prevvno = '';
			$nextvno = '';
			foreach($list->result as $key => $row)
			{
				$date        	= $row->paymentdate;
				$restrict_dv 	= $this->restrict->setButtonRestriction($date);
				$date       	= $this->date->dateFormat($date);
				$voucher   		= $row->voucherno; 
				$vendor		 	= $row->partner; 
				$reference		= $row->reference;
				$paymentmode 	= $row->paymentmode; 
				$amount	  	 	= $row->amount;
				$status   		= $row->status;
				/**
				 * Cheque Details
				 */
				$bankaccount   	= $row->bankaccount;
				$chequenumber   = $row->chequenumber;
				$chequedate   	= $this->date->dateFormat($row->chequedate);
				$chequeamount  	= $row->chequeamount;

				$prevvno 		= $voucher;
				$voucher_status = '<span class="label label-danger">'.strtoupper($status).'</span>';
				if($status == 'open'){
					$voucher_status = '<span class="label label-info">'.strtoupper($status).'</span>';
				}else if($status == 'posted'){
					$voucher_status = '<span class="label label-success">'.strtoupper($status).'</span>';
				}

				$login		= $this->session->get('login');
				$groupname 	= $login['groupname'];
				$has_access = $this->payment_voucher->retrieveAccess($groupname, "Disbursement Voucher");

				$show_btn 		= ($status == 'open' && $restrict_dv);
				$show_edit 		= ($status == 'open' && $has_access[0]->mod_edit == 1 && $restrict_dv);
				$show_dlt 		= ($status == 'open' && $has_access[0]->mod_delete == 1 && $restrict_dv);
				$show_post 		= ($status == 'open' && $has_access[0]->mod_post == 1 && $restrict_dv);
				$show_unpost 	= ($status == 'posted' && $has_access[0]->mod_unpost == 1 && $restrict_dv);

				$dropdown = $this->ui->loadElement('check_task')
				->addView()
				->addEdit($show_edit)
				->addOtherTask(
					'Post',
					'thumbs-up',
					$show_post
				)
				->addOtherTask(
					'Unpost',
					'thumbs-down',
					$show_unpost
				)
				->addOtherTask(
					'Cancel',
					'ban-circle',
					$show_btn
				)
				->addPrint()
							//->addDelete($show_dlt)
				->addCheckbox($show_btn)
				->setValue($voucher)
				->draw();

				if($nextvno != $prevvno){
					$table	.= '<tr>';
					$table	.= '<td class="text-center">'.$dropdown.'</td>';
					$table	.= '<td >'.$date.'</td>';
					$table	.= '<td >'.$voucher.'</td>';
					$table	.= '<td >'.$vendor.'</td>';
					$table	.= '<td >'.$reference.'</td>';
					$table	.= '<td >'.ucwords(($paymentmode == 'cheque') ? $paymentmode = 'check' : 'cash').'</td>';
					$table	.= '<td class="text-right" >'.number_format($amount,2).'</td>';
					$table	.= '<td >'.$voucher_status.'</td>';
					$table	.= '</tr>';
				}

				if($paymentmode == 'cheque'){
					if($nextvno != $prevvno){
						$table	.= '<tr>';
						$table	.= '<td></td>';
						$table	.= '<td colspan="2" class="warning" ><strong>Bank Account</strong></td>';
						$table	.= '<td class="warning" ><strong>Check Number</strong></td>';
						$table	.= '<td class="warning" ><strong>Check Date</strong></td>';
						$table	.= '<td class="warning" ><strong>Check Amount</strong></td>';
						$table	.= '</tr>';


					}
					$table	.= '<tr >';
					$table	.= '<td></td>';
					$table	.= '<td colspan="2" class="warning">'.$bankaccount.'</td>';
					$table	.= '<td class="warning">'.$chequenumber.'</td>';
					$table	.= '<td class="warning">'.$chequedate.'</td>';
					$table	.= '<td class="text-right warning">'.number_format($chequeamount,2).'</td>';
					$table	.= '<td colspan="2"></td>';
					$table	.= '</tr>';
				}

				$nextvno     	= $prevvno;
			}
		else:
			$table .= "<tr>
			<td colspan = '8' class = 'text-center'>No Records Found</td>
			</tr>";
		endif;

		$dataArray = array( "list" => $table, "pagination" => $list->pagination , "csv" => $this->export() );
		return $dataArray;
	}

	private function export(){
		$data_get 						= $this->input->post(array("daterangefilter", "vendor", "filter", "search", "sort"));
		$data_get['daterangefilter'] 	= str_replace(array('%2F', '+'), array('/', ' '), $data_get['daterangefilter']);
		$data_get['voucher'] 			= "PV";
		$result2 = $this->payment_voucher->fileExportlist($data_get);
		
		$header = array("Date","Voucher","Vendor","Reference","Amount","Status");
		
		$csv = '';
		$csv = '"' . implode('","', $header) . '"';
		$csv .= "\n";

		if (!empty($result2)){
			$prevvno = '';
			$nextvno = '';
			foreach ($result2 as $key => $row){
				$date        	= $row->paymentdate;
				$date       	= $this->date->dateFormat($date);
				$voucher   		= $row->voucherno; 
				$vendor		 	= $row->partner; 
				$reference		= $row->reference;
				$paymentmode 	= $row->paymentmode; 
				$amount	  	 	= $row->amount;
				$status   		= $row->status;
				/**
				 * Cheque Details
				 */
				$bankaccount   	= $row->bankaccount;
				$chequenumber   = $row->chequenumber;
				$chequedate   	= $this->date->dateFormat($row->chequedate);
				$chequeamount  	= $row->chequeamount;

				$prevvno 		= $voucher;
				$voucher_status = strtoupper($status);
				
				if($nextvno != $prevvno){
					$csv .= '"' . $date . '",';
					$csv .= '"' . $voucher . '",';
					$csv .= '"' . $vendor . '",';
					$csv .= '"' . $reference . '",';
					$csv .= '"' . ucwords($paymentmode) . '",';
					$csv .= '"' . number_format($amount,2) . '",';
					$csv .= '"' . $voucher_status . '"';
					$csv .= "\n";
				}

				if($paymentmode == 'cheque'){
					if($nextvno != $prevvno){
						$csv .= '"",';
						$csv .= '"Bank Account",';
						$csv .= '"Check Number",';
						$csv .= '"Check Date",';
						$csv .= '"Check Amount",';
						$csv .= "\n";
					}
					$csv .= '"",';
					$csv .= '"'.$bankaccount.'",';
					$csv .= '"'.$chequenumber.'",';
					$csv .= '"'.$chequedate.'",';
					$csv .= '"'.number_format($chequeamount,2).'",';
					$csv .= "\n";
				}
				$csv .= "\n";
				$nextvno     	= $prevvno;
			}
		}
		return $csv;
	}
	
	public function print_check($vno, $cno){
		$print_chkdtl   = $this->payment_voucher->print_check($vno,$cno);
		if ($print_chkdtl){
			$this->logs->saveActivity("Print Check [$cno] on Voucherno  [$vno]");
		}
		$print_dtls = new print_check();

		$print_dtls->setDocumentType('Payment Voucher')
		->setDocumentInfo($print_chkdtl)
		->drawPDF('pv_voucher_' . $vno);
	}

	public function getCheckdtl(){
		$bank_no = $this->input->post('bank');
		$current = $this->input->post('current_check');
		$bno = $this->input->post('bookno');
		$result1 = $this->payment_voucher->getcheckfirst($bank_no, $current, $bno);
		if ($result1){
			$nextcheckno  = $result1[0]->nextchequeno;
			$lastcheckno  = $result1[0]->lastchequeno;
			$fno 		  = $result1[0]->firstchequeno;
		} else {
			$nextcheckno  = 0;
			$lastcheckno  = 0;
			$fno 		  = 0;
		}
		// $bno 		  = $result[0]->booknumber;
		$data = array('nno' => $nextcheckno, 'last' => $lastcheckno, 'fno' => $fno);
		return $data; 
	}

	public function update_check_status(){
		$val = $this->input->post('val');
		$cno = $this->input->post('next');
		$getBank = $this->payment_voucher->getbankid($val);
		$bank_id = isset($getBank[0]->id) ? $getBank[0]->id : '';
		$result = $this->payment_voucher->update_check_status($bank_id, $cno);
		if ($result){
			$msg = 'success';
		}
		return $msg  ;
	}

	public function getbooknumber(){
		$bank = $this->input->post('bank');
		$book_ids = $this->input->post('book_ids');
		if (empty($book_ids)) {
			$book_ids = array();
		}
		$book_ids = "'" . implode("','", $book_ids) . "'";
		$getBank = $this->payment_voucher->getbankid($bank);
		$bank_id = isset($getBank[0]->id) ? $getBank[0]->id : '';
		$result = $this->payment_voucher->getbankbook($bank_id, $book_ids);
		$options = '<option id="0" value="0">None</option>';
		foreach ($result as $key => $row) {
			$booknum = $row->booknumber;
			$checknum = $row->firstchequeno.' - '.$row->lastchequeno;
			$firstchequenum = $row->firstchequeno;
			$options .= "<option value='$firstchequenum' id='$firstchequenum'>". $checknum ."</option>" ;
		}

		$data = array('opt' => $options );
		return $data;
	}

	public function get_next_booknum(){
		$bank = $this->input->post('bank');
		$getBank = $this->payment_voucher->getbankid($bank);
		$bank_id = isset($getBank[0]->id) ? $getBank[0]->id : '';
		$firstchequenum = $this->input->post('bookno');
		$result1 = $this->payment_voucher->get_next_booknum($bank_id, $current = 0, $firstchequenum);
		if ($result1){
			$nextcheckno  = $result1[0]->nextchequeno;
			$lastcheckno  = $result1[0]->lastchequeno;
			$fno 		  = $result1[0]->firstchequeno;
		} else {
			$nextcheckno  = 0;
			$lastcheckno  = 0;
			$fno 		  = 0;
		}
		// $bno 		  = $result[0]->booknumber;
		$data = array('nno' => $nextcheckno, 'last' => $lastcheckno);
		return $data; 
	}

	public function getNumbers() {
		$data = $this->input->post(array('bank', 'curr_seq'));
		$arr = explode(' - ', $data['bank']);
		$getBank = $this->payment_voucher->getbankid($arr[0]);
		$bank_id = isset($getBank->id) ? $getBank->id : '';
		$nums = $this->payment_voucher->getNextCheckNum($bank_id, $data['curr_seq']);
		$table = '';
		if(empty($nums->result)) {
			$table = false;
		} else {
			foreach($nums->result as $row) {
				$table .= '<tr class = "clickme" style = "cursor : pointer;">';
				$table .= '<td class = "text-center">'.$row->firstchequeno.'</td>';
				$table .= '<td class = "text-center">'.$row->lastchequeno.'</td>';
				$table .= '<td class = "nextchequeno text-center">'.$row->nextchequeno.'</td>';
				$table .= '</tr>';
			}
		}
		$nums->table = $table;
		$nums->bank_id = $bank_id;
		return $nums;
	}

}