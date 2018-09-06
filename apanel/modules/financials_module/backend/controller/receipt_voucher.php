<?php
class controller extends wc_controller 
{

	public function __construct()
	{
		parent::__construct();
		$this->url 			    = new url();
		$this->receipt_voucher  = new receipt_voucher_model();
		$this->restrict 		= new financials_restriction_model();
		$this->input            = new input();
		$this->ui 			    = new ui();
		$this->logs  			= new log;
		$this->session			= new session();
		$this->seq 				= new seqcontrol();
		$this->view->title      = 'Receipt Voucher';
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

		// Retrieve customer list
		$data["customer_list"]  = $this->receipt_voucher->retrieveCustomerList();

		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->receipt_voucher->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);


		$this->view->load('receipt_voucher/receipt_voucher_list', $data);
	}

	public function update_app($check_rows)
	{
		/**UPDATE MAIN INVOICE**/
		$applicableHeaderTable = "accountsreceivable";
		$applicationTable 	   = "rv_application";
	
		$invoice_data 	= (isset($check_rows) && (!empty($check_rows))) ? trim($check_rows) : "";
		$invoice_data  = str_replace('\\', '', $invoice_data);
		$decode_json   = json_decode($invoice_data, true);

		$insertResult 	= 0;
		$errmsg 	= array();
		if(!empty($decode_json))
		{
			for($i = 0; $i < count($decode_json); $i++)
			{
				$invoice = $decode_json[$i]["vno"];
			    $amount  = $decode_json[$i]["amt"];

				// accountspayable
				$invoice_amount				= $this->receipt_voucher->getValue($applicableHeaderTable, array("convertedamount"), "voucherno = '$invoice' AND stat = 'posted'");
				$applied_discount			= 0;

				// rv_application
				$applied_sum				= $this->receipt_voucher->getValue($applicationTable, array("SUM(convertedamount) AS convertedamount")," arvoucherno = '$invoice' AND stat = 'posted' ");

				// rv_application
				$applied_discount			= $this->receipt_voucher->getValue($applicationTable, array("SUM(discount) AS discount"), "arvoucherno = '$invoice' AND stat = 'posted' ");

				// rv_application
				$applied_forexamount		= $this->receipt_voucher->getValue($applicationTable, array("SUM(forexamount) AS forexamount"), "arvoucherno = '$invoice' AND stat = 'posted' ");

				$applied_sum				= $applied_sum[0]->convertedamount - $applied_forexamount[0]->forexamount;

				$invoice_amount				= (!empty($invoice_amount)) ? $invoice_amount[0]->convertedamount : 0;
				$applied_sum				= (!empty($applied_sum)) ? $applied_sum : 0;

				$invoice_balance			= $invoice_amount - $applied_sum - $applied_discount[0]->discount;

				$balance_info['received']	= $applied_sum + $applied_discount[0]->discount;
				// $balance_info['convertedamount']	= $applied_sum + $applied_discount[0]->discount;
				$balance_info['balance']	= $invoice_amount - $applied_sum - $applied_discount[0]->discount;

				// accountspayable
				$insertResult = $this->receipt_voucher->editData($balance_info, $applicableHeaderTable, "voucherno = '$invoice'");	
				if(!$insertResult)
					$errmsg["error"][] = "The system has encountered an error in updating Account Payable [$invoice]. Please contact admin to fix this issue.<br/>";
			}
		}

		$dataArray 	=	array("success"=>$insertResult,"error"=>$errmsg);
		return $dataArray;
	}

	public function create()
	{	
		/**
		 * Method to lock screen when in use by other users
		 */
		$access	= $this->access->checkLockAccess('create');
		$cmp 	= $this->companycode;

		// Initialize variables
		$data = $this->input->post(array(
			"voucherno",
			"or_no",
			"customercode",
			"tinno",
			"address1",
			"duedate",
			"particulars",
			"terms",
			"date",
			"paymenttype"
		));

		$this->view->title			  = 'Create Receipt Voucher';
		$data["ui"]                   = $this->ui;
		$data['show_input']           = $this->show_input;
		$data['button_name']          = "Save";
		$data["task"] 		          = "create";
		$data["ajax_post"] 	          = "";
		$data["row_ctr"] 			  = 0;
		$data["exchangerate"]         = "1.00";
		$data["row"] 			  		= 1;
		$data["transactiondate"]      = $this->date->dateFormat();
		$data["status"] 				= "";

		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;

		// Retrieve customer list
		$data["customer_list"]          = $this->receipt_voucher->retrieveCustomerList();
		
		// Retrieve business type list
		$acc_entry_data               = array("id ind","CONCAT(segment5, ' - ', accountname) val");
		$acc_entry_cond               = "accounttype != '' AND stat = 'active'";
		$data["account_entry_list"]   = $this->receipt_voucher->getValue("chartaccount", $acc_entry_data, $acc_entry_cond, "segment5");

		// $cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		// $cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		// $cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P' AND stat = 'active'";
		// $cash_order_by 		 	  = "class.accountclass";
		// $data["cash_account_list"] = $this->receipt_voucher->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

		// Cash Account Options
		$cash_account_fields 	  	= "c.id ind , CONCAT(shortname,' - ' ,accountno ) val";
		$cash_account_cond 	 	  	= "b.stat = 'active' AND b.checking_account = 'yes'";
		$cash_order_by 		 	  	= "id desc";
		$cash_account_join 	 	  	= "chartaccount c ON b.gl_code = c.segment5";
		$data["cash_account_list"] 	= $this->receipt_voucher->retrievebank("bank b", $cash_account_fields, $cash_account_cond ,$cash_account_join ,$cash_account_cond, '');


		// Retrieve generated ID
		// $gen_value                    = $this->receipt_voucher->getValue("paymentvoucher", "COUNT(*) as count", "voucherno != ''");	
		// $data["generated_id"]         = (!empty($gen_value[0]->count)) ? 'TMP_'.($gen_value[0]->count + 1) : 'TMP_1';
		$data["generated_id"]     = '';

		// Application Data
		$data['sum_applied'] 	= 0;
		$data['sum_discount']	= 0;
		$data['payments'] 		= "''";
		$data['available_credits'] = "0.00";
		$data['credits_used'] 	= 0;

		$data["listofcheques"]	= "";
		$data["show_cheques"] 	= 'hidden';

		$data['restrict_rv'] 		= true;
		
		$this->view->load('receipt_voucher/receipt_voucher', $data);
	}

	public function update_temporarily_saved_data(){
		$data_validate 	= $this->input->post(array('referenceno',"h_task","h_voucher_no", "customer", "document_date", "h_save", "h_save_new", "h_save_preview", "h_check_rows_","selected_rows"));
		$btn_type 		= $data_validate['h_save'];

		$errmsg 			= array();
		$temp 				= array();
		$updateTempRecord 	= 0;
		$generatedvoucher 	= "";
		$task 			= $data_validate['h_task'];
		$generatedvoucher = '';

		if (!empty($data_validate["customer"]) && !empty($data_validate["document_date"])) {
			$voucherno = (isset($data_validate['h_voucher_no']) && (!empty($data_validate['h_voucher_no']))) ? htmlentities(trim($data_validate['h_voucher_no'])) : "";

			$isExist = $this->receipt_voucher->getValue("receiptvoucher", array("voucherno"), "voucherno = '$voucherno'");

			if($isExist[0]->voucherno){
				/**UPDATE MAIN TABLES**/
				$generatedvoucher			= ($task == 'create') ? $this->seq->getValue('RV')	: $data_validate['h_voucher_no']; 
			
				$update_info				= array();
				$update_info['voucherno']	= $generatedvoucher;
				$update_info['stat']		= 'open';
				$update_condition			= "voucherno = '$voucherno'";
				$updateTempRecord			= $this->receipt_voucher->editData($update_info,"receiptvoucher",$update_condition);
				$updateTempRecord			= $this->receipt_voucher->editData($update_info,"rv_details",$update_condition);
				$updateTempRecord			= $this->receipt_voucher->editData($update_info,"rv_application",$update_condition);
				$update_cheque['voucherno']	= $generatedvoucher;
				$updateTempRecord			= $this->receipt_voucher->editData($update_cheque,"rv_cheques",$update_condition);
				// Update TMP source of CM
				$update_source['si_no']  	= $generatedvoucher;
				$source_cond 				= "si_no = '$voucherno' AND transtype = 'CM'";
				$updateTempRecord			= $this->receipt_voucher->editData($update_source,"journalvoucher",$source_cond);

				/**UPDATE MAIN INVOICE**/
				// $updateTempRecord 			= $this->update_app($data_validate['selected_rows']);
			}
			
			// if(empty($errmsg))
			// {
			// 	// For Admin Logs
			// 	$this->logs->saveActivity("Add New Receipt Voucher [$generatedvoucher]");

			// 	if(!empty($data_validate['h_save'])){
			// 		$this->url->redirect(BASE_URL . 'financials/receipt_voucher');
			// 	}else if(!empty($data_validate['h_save']) && $data_validate['h_save'] == 'h_save_preview'){
			// 		$this->url->redirect(BASE_URL . 'financials/receipt_voucher/view/' . $generatedvoucher);
			// 	}else{
			// 		$this->url->redirect(BASE_URL . 'financials/receipt_voucher/create');
			// 	}
			// }else{
			// 	$data["errmsg"] = $errmsg;
			// }
		}

		if($updateTempRecord){
			if($task == 'create'){
				$this->logs->saveActivity("Add New Receipt Voucher [$generatedvoucher]");
			} else if($task == 'edit'){
				$voucherno 	=	$data_validate['h_voucher_no'];
				$this->logs->saveActivity("Update Receipt Voucher [$voucherno]");
			}
		}

		$dataArray = array("success"=>$updateTempRecord,"error"=>$errmsg, "btn_type"=>$btn_type , 'voucher' => $generatedvoucher);
		return $dataArray;
	}

	public function view($sid)
	{
		$this->view->title			= 'View Receipt Voucher';
		$cmp 					   	= $this->companycode;
		// Retrieve data
		$data         			   	= $this->receipt_voucher->retrieveEditData($sid);

		$data["ui"]   			   	= $this->ui;
		$data['show_input'] 	   	= false;
		$data["button_name"] 	   	= "Edit";
		$data["task"] 	  		   	= "view";
		$data["generated_id"]  	   	= $sid;
		$data["sid"] 		   	   	= $sid;
		$data["date"] 			   	= date("M d, Y");
		
		$data["business_type_list"]	= array();
		
		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;

		// Retrieve business type list
		$acc_entry_data             = array("id ind","CONCAT(segment5, ' - ', accountname) val");
		$acc_entry_cond             = "accounttype != ''";
		$data["account_entry_list"] = $this->receipt_voucher->getValue("chartaccount", $acc_entry_data, $acc_entry_cond, "segment5");

		$data["customer_list"]    	= array();

		// Main
		$vendor_details 		   	= $this->receipt_voucher->getValue("partners", "partnername"," partnertype = 'customer' AND partnercode = '".$data["main"]->customer."'", "");

		$transactiondate 			= $data["main"]->transactiondate;
		$restrict_rv 				= $this->restrict->setButtonRestriction($transactiondate);
		$data["voucherno"]         = $data["main"]->voucherno;
		$data["customercode"]        = $vendor_details[0]->partnername;
		$data["v_convertedamount"] = $data["main"]->convertedamount;
		$data["exchangerate"]      = $data["main"]->exchangerate;
		$data["transactiondate"]   = $this->date->dateFormat($transactiondate);
		$data["or_no"]       		= $data["main"]->or_no;
		$data["paymenttype"]       = $data["main"]->paymenttype;
		$data["particulars"]       = $data["main"]->particulars;
		$data['status']				= $data["main"]->stat;
		// Vendor/Customer Details
		$data["v_vendor"] 		   	= $data["vend"]->name;
		$data["v_email"] 		   	= $data["vend"]->email;
		$data["tinno"] 		   	   	= $data["vend"]->tinno;
		$data["address1"] 	       	= $data["vend"]->address1;
		$data["terms"] 	   		   	= $data["vend"]->terms;
		//For User Access
		$login						= $this->session->get('login');
		$groupname 					= $login['groupname'];
		$has_access					= $this->receipt_voucher->retrieveAccess($groupname);
		$data['has_access'] 		= $has_access[0]->mod_edit;
		/**
		* Get the total forex amount applied
		*/
		$forex_result 			  = $this->receipt_voucher->getValue("rv_application", array("SUM(forexamount) as forexamount"), "arvoucherno = '$sid' AND stat = 'posted'");
		$forexamount			  = ($forex_result[0]->forexamount != '') ? $forex_result[0]->forexamount : 0;

		$data["forexamount"] 	  = $forexamount;

		$dis_entry 					= $this->receipt_voucher->getValue("fintaxcode", array("salesAccount"), "fstaxcode = 'DC'");
		$discount_code 				= isset($dis_entry[0]->salesAccount) ? $dis_entry[0]->salesAccount	: "";
		$data['discount_code'] 		= $discount_code;

		// Cash Account Options
		// $cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		// $cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		// $cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		// $cash_order_by 		 	  = "class.accountclass";
		// $data["cash_account_list"] = $this->receipt_voucher->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

		// Cash Account Options
		$cash_account_fields 	  	= "c.id ind , CONCAT(shortname,' - ' ,accountno ) val";
		$cash_account_cond 	 	  	= "b.stat = 'active' AND b.checking_account = 'yes'";
		$cash_order_by 		 	  	= "id desc";
		$cash_account_join 	 	  	= "chartaccount c ON b.gl_code = c.segment5";
		$data["cash_account_list"] 	= $this->receipt_voucher->retrievebank("bank b", $cash_account_fields, $cash_account_cond ,$cash_account_join ,$cash_account_cond, '');

		
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

		$data['restrict_rv'] 	= $restrict_rv;

		$this->view->load('receipt_voucher/receipt_voucher', $data);
	}

	public function edit($sid)
	{
		$this->view->title		= 'Edit Receipt Voucher';
		$access				   	= $this->access->checkLockAccess('edit');
		$data         		   	= $this->receipt_voucher->retrieveEditData($sid);
		
		$data["ui"]            	= $this->ui;
		$data['show_input']    	= $this->show_input;
		$data["task"] 		   	= "edit";

		$data["generated_id"]  	= $sid;
		$data["sid"] 		   	= $sid;
		$data["date"] 		   	= date("M d, Y");

		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		
		// Retrieve vendor list
		$data["customer_list"]          = $this->receipt_voucher->retrieveCustomerList();

		$coa_array	= array();
		foreach ($data['details'] as $index => $dtl){
			$coa			= $dtl->accountcode;
			$coa_array[]	= $coa;
		}
		
		$condition = ($coa_array) ? " OR id IN ('".implode("','",$coa_array)."')" : "";
		// Retrieve business type list
		$acc_entry_data               = array("id ind","CONCAT(segment5, ' - ', accountname) val, stat stat");
		$acc_entry_cond               = "accounttype != ''";
		$data["account_entry_list"]   = $this->receipt_voucher->getValue("chartaccount", $acc_entry_data, $acc_entry_cond. $condition, "segment5");

		$dis_entry 					= $this->receipt_voucher->getValue("fintaxcode", array("salesAccount"), "fstaxcode = 'DC'");
		$discount_code 				= isset($dis_entry[0]->salesAccount) ? $dis_entry[0]->salesAccount	: "";
		$data['discount_code'] 		= $discount_code;

		// // Cash Account Options
		// $cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		// $cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		// $cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		// $cash_order_by 		 	  = "class.accountclass";
		// $data["cash_account_list"] = $this->receipt_voucher->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

		
		// // Cash Account Options
		// $cash_account_fields 	  	= "c.id ind , CONCAT(shortname,' - ' ,accountno ) val";
		// $cash_account_cond 	 	  	= "b.stat = 'active' AND b.checking_account = 'yes'";
		// $cash_order_by 		 	  	= "id desc";
		// $cash_account_join 	 	  	= "chartaccount c ON b.gl_code = c.segment5";
		// $data["cash_account_list"] 	= $this->receipt_voucher->retrievebank("bank b", $cash_account_fields, $cash_account_cond ,$cash_account_join ,$cash_account_cond, '');


	
		// Header Data
		$voucherno 				 = $data["main"]->voucherno;
		$customer 				 = $data['main']->customer;
		$data["voucherno"]       = $voucherno;
		$data["or_no"]    		 = $data["main"]->or_no;
		$data["customercode"]    = $customer;
		$data["exchangerate"]    = $data["main"]->exchangerate;
		$data["transactiondate"] = $this->date->dateFormat($data["main"]->transactiondate);
		$data["particulars"]     = $data["main"]->particulars;
		$data["paymenttype"]     = $data["main"]->paymenttype;
		$credits_used 			 = $data["main"]->credits_used;
		$data["credits_used"]    = $credits_used;
		$available_credits 		 = $this->receipt_voucher->retrieve_existing_credits($customer);
		$data["available_credits"] = isset($available_credits[0]->curr_credit) 	?	$available_credits[0]->curr_credit 	+	$credits_used	:	"0.00";
		$data['status']			 = $data["main"]->stat;
	 		
		$data["listofcheques"]	 = isset($data['rollArray'][$sid]) ? $data['rollArray'][$sid] : '';

		$account_array	= array();
		foreach ($data['listofcheques'] as $index => $dtl){
			$accountcode 	=	$dtl['chequeaccount'];
			$account_array[] = $accountcode;
		}
		$account_array = ($account_array) ? " OR c.id IN ('".implode("','",$account_array)."')" : "";

		$cash_account_fields 	  	= "c.id ind , CONCAT(shortname,' - ' ,accountno ) val, b.stat stat";
		$cash_account_cond 	 	  	= "b.stat = 'active' AND b.checking_account = 'yes' $account_array" ;
		$cash_order_by 		 	  	= "id desc";
		$cash_account_join 	 	  	= "chartaccount c ON b.gl_code = c.segment5";
		$data["cash_account_list"] 	= $this->receipt_voucher->retrievebank("bank b", $cash_account_fields, $cash_account_cond ,$cash_account_join ,$cash_account_cond, '');

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

		$data['restrict_rv'] 	= true;
		$data['has_access'] 	= 0;

		// print_r($data);

		// Process form when form is submitted
		// $data_validate = $this->input->post(array('referenceno', "h_voucher_no", "customer", "document_date", "h_save", "h_save_new", "h_save_preview", "h_check_rows_"));

		// if (!empty($data_validate["customer"]) && !empty($data_validate["document_date"])) 
		// {
		// 	$update_info				= array();
		// 	$update_info['stat']		= 'open';
		// 	$update_condition			= "voucherno = '$voucherno'";
		// 	$updateTempRecord			= $this->receipt_voucher->editData($update_info,"receiptvoucher",$update_condition);
		// 	$updateTempRecord			= $this->receipt_voucher->editData($update_info,"rv_details",$update_condition);
		// 	$updateTempRecord			= $this->receipt_voucher->editData($update_info,"rv_application",$update_condition);
		// 	$updateTempRecord			= $this->receipt_voucher->editData($update_info,"rv_cheques",$update_condition);
		// 	// Update TMP source of CM
		// 	// $update_source['si_no']  	= $generatedvoucher;
		// 	// $source_cond 				= "si_no = '$voucherno' AND transtype = 'CM'";
		// 	// $updateTempRecord			= $this->receipt_voucher->editData($update_source,"journalvoucher",$source_cond);

		// 	$this->update_app($data_validate["h_check_rows_"]);

		// 	// For Admin Logs
		// 	$this->logs->saveActivity("Update Receipt Voucher [$sid]");

		// 	if(!empty($data_validate['h_save']))
		// 	{
		// 		$this->url->redirect(BASE_URL . 'financials/receipt_voucher');
		// 	}
		// 	else if(!empty($data_validate['h_save_preview']))
		// 	{
		// 		$this->url->redirect(BASE_URL . 'financials/receipt_voucher/view/' . $sid);
		// 	}
		// 	else
		// 	{
		// 		$this->url->redirect(BASE_URL . 'financials/receipt_voucher/create');
		// 	}	 
		// }

		$this->view->load('receipt_voucher/receipt_voucher', $data);
	}

	public function print_preview($voucherno) 
	{
		// Retrieve Document Info
		$sub_select = $this->receipt_voucher->retrieveData("rv_application", array("SUM(amount) AS amount"), "voucherno = '$voucherno'");

		$sub_select[0]->amount;
		
		$docinfo_table  = "receiptvoucher as pv";
		$docinfo_fields = array('pv.transactiondate AS documentdate','pv.voucherno AS voucherno',"CONCAT( first_name, ' ', last_name )","'{$sub_select[0]->amount}' AS amount",'pv.amount AS pvamount', "'' AS referenceno", "particulars AS remarks", "p.partnername AS customer");
		$docinfo_join   = "partners as p ON p.partnercode = pv.customer AND p.companycode = pv.companycode";
		$docinfo_cond 	= "pv.voucherno = '$voucherno'";

		$documentinfo  	= $this->receipt_voucher->retrieveData($docinfo_table, $docinfo_fields, $docinfo_cond, $docinfo_join);

		$customer 	    = $documentinfo[0]->customer;

		// Retrieve Document Details
		$docdet_table   = "rv_details as dtl";
		$docdet_fields  = array("chart.segment5 as accountcode", "CONCAT(segment5, ' - ', accountname) as accountname", "SUM(dtl.debit) as debit","SUM(dtl.credit) as credit");
		$docdet_join    = "chartaccount as chart ON chart.id = dtl.accountcode AND chart.companycode = dtl.companycode ";
		$docdet_cond    = "dtl.voucherno = '$voucherno'";
		$docdet_groupby = "dtl.accountcode";
		$docdet_orderby = "CASE WHEN dtl.debit > 0 THEN 1 ELSE 2 END, dtl.linenum";
		
		$documentdetails = $this->receipt_voucher->retrieveData($docdet_table, $docdet_fields, $docdet_cond, $docdet_join, $docdet_orderby, $docdet_groupby);

		// Retrieve Payment Details
		$paymentArray	 = $this->receipt_voucher->retrievePaymentDetails($voucherno);

		// Retrieval of Voucher Status //
		$pv_v 		  = "";
		$pv_voucherno = $this->receipt_voucher->getValue("rv_application", array("voucherno"), "voucherno = '$voucherno'");
		$ap_voucher   = $this->receipt_voucher->getValue("rv_details", array("arvoucherno"), "voucherno = '$voucherno'","","","arvoucherno" );
		
		foreach ($ap_voucher as $row) {
			$apvoucher[] = $row->arvoucherno;
		}
		$ap =  implode("','" , $apvoucher);
		$ap_no = "('".$ap."')";
		$ap_amount    = $this->receipt_voucher->getValue("accountsreceivable", array("SUM(amount) total_amount"), "voucherno IN $ap_no" );
		$total_amount = $ap_amount[0]->total_amount;
		$amount = $paymentArray[0]->amount;
		$balance = $total_amount - $amount;
		
		if($balance != $amount && $balance != 0)
		{
			$voucher_status = 'PARTIAL';
		}
		else if($balance != 0)
		{
			$voucher_status = 'UNPAID';
		}
		else
		{
			$voucher_status = 'PAID';
		}

		$chequeArray = "";
		if(!empty($pv_voucherno))
		{
			for($p = 0; $p < count($pv_voucherno); $p++)
			{
				$pv_v .= "'".$pv_voucherno[$p]->voucherno."',";
			}
		
			$pv_v = rtrim($pv_v, ", ");
			
			$cheque_table = "rv_cheques pvc";
			$cheque_fields = array("pv.referenceno referenceno", "CONCAT(segment5, ' - ', accountname) AS accountname", "pvc.chequenumber AS chequenumber", "pvc.chequedate AS chequedate", "pvc.chequeamount AS chequeamount");
			$cheque_cond = "pvc.voucherno IN($pv_v) " ;
			$cheque_join = "chartaccount chart ON pvc.chequeaccount = chart.id LEFT JOIN receiptvoucher pv ON pv.voucherno = pvc.voucherno" ;
			$cheque_group = "pvc.chequenumber";
			$chequeArray = $this->receipt_voucher->retrieveData($cheque_table, $cheque_fields, $cheque_cond, $cheque_join);
			$chequeArray_2 = $this->receipt_voucher->retrieveData($cheque_table, $cheque_fields, $cheque_cond, $cheque_join,"");
		}

		// Retrieve Applied Payment //
		$p_table = "rv_application pv";
		$p_fields = array("arvoucherno voucherno", "pv.amount amount", "ap.sourceno si_no", "pv.discount discount") ;
		$p_cond = "pv.voucherno IN($pv_v) " ;
		$p_join = "accountsreceivable ap ON pv.arvoucherno = ap.voucherno" ;
		$appliedpaymentArray = $this->receipt_voucher->retrieveData($p_table, $p_fields, $p_cond, $p_join);
		
		// Setting for PDFs
		$print = new print_voucher_model('P', 'mm', 'Letter');
		$print->setDocumentType('Receipt Voucher')
				->setDocumentInfo($documentinfo[0])
				->setCustomer($customer)
				->setVoucherStatus($voucher_status)
				->setPayments($chequeArray_2)
				->setDocumentDetails($documentdetails)
				->setCheque($chequeArray)
				->setAppliedPayment($appliedpaymentArray)
				->drawPDF('rv_voucher_' . $voucherno);
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

		$list          = $this->receipt_voucher->retrievePayments($data_post);

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
		$data["cash_account_list"] = $this->receipt_voucher->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

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
				$apvoucherno 		= $row->arvoucherno;

				$apvoucherno 		= $row->arvoucherno;

				$cheque_values		= "";//(!is_null($rollArray) && !empty($rollArray[$paymentnumber])) ? json_encode($rollArray[$paymentnumber]) : "";
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
								
				$button = (strtolower($checkstat) != 'cleared') ? '<button class="btn btn-default btn-xs" onClick="editPaymentRow(event,\'edit'.$row_count.'\', \''.$arvoucherno.'\', \''.$sid.'\');" title="Edit Payment"><span class="glyphicon glyphicon-pencil"></span></button>
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
		$result = $this->receipt_voucher->editData($data["fields"], $data["table"], $data["condition"]);

		if($result)
			$msg = "success";
		else
			$msg = "error_update";

		$dataArray = array( "msg" => $msg );
		return $dataArray;
	}

	private function delete_row()
	{
		$data_var  = array('table', "condition");
		$data_post = $this->input->post($data_var);

		/**
		* Delete Database
		*/
		$result = $this->receipt_voucher->deleteData($data_post);

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

		$result 		= $this->receipt_voucher->deletePayments($payments,$type);

		if($result){
			$code 	= 1; 
			$msg 	= "Successfully ".$type." the vouchers.";
		}else{
			$code 	= 0; 
			$msg 	= "Sorry, the system was unable to ".$type." the vouchers.";
		}

		$dataArray = array("code" => 1,"msg"=> "" );
		return $dataArray;
	}

	private function cancel_cm_entries()
	{
		$vouchers 		= $this->input->post('delete_id');
		$payments 		= "'" . implode("','", $vouchers) . "'";

		$cm_vouchers 	= $this->receipt_voucher->getValue("journalvoucher", "voucherno", "transtype = 'CM' AND si_no IN ($payments)");

		$result 		= 0;
		foreach($cm_vouchers as $key => $content){
			$cm_no 			=  	$content->voucherno;
			$result 		= 	$this->receipt_voucher->cancelCreditMemo($cm_no);
		}

		if($result){
			$code 	= 1; 
			$msg 	= "Successfully cancelled the vouchers.";
		}else{
			$code 	= 0; 
			$msg 	= "Sorry, the system was unable to cancelled the vouchers.";
		}

		$dataArray = array("code" => $code,"msg"=> $msg );
		return $dataArray;
	}

	private function ajax_post() {
		$id 			= $this->input->post('id');
		$type 			= $this->input->post('type');

		$data['stat']	= "posted";
		$result 		= $this->receipt_voucher->editData($data, "receiptvoucher", "voucherno = '$id'");
		$type 			= 'post';

		if($result){
			$this->receipt_voucher->editData($data, "rv_details", "voucherno = '$id'");
			$code 	= 1; 
			$msg 	= "Successfully Posted Voucher ".$id;
		}else{
			$code 	= 0; 
			$msg 	= "Sorry, the system was unable to Post the Voucher.";
		}

		$dataArray = array("code" => $code,"msg"=> $msg );
		return $dataArray;
	}

	private function ajax_unpost() {
		$id 			= $this->input->post('id');
		$type 			= $this->input->post('type');

		$data['stat']	= "open";
		$result 		= $this->receipt_voucher->editData($data, "receiptvoucher", "voucherno = '$id'");
		$type 			= 'unpost';

		if($result){
			$this->receipt_voucher->editData($data, "rv_details", "voucherno = '$id'");
			$code 	= 1; 
			$msg 	= "Successfully Unposted Voucher ".$id;
		}else{
			$code 	= 0; 
			$msg 	= "Sorry, the system was unable to Unpost the Voucher.";
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
			$result = $this->receipt_voucher->getValue("partners", $data_var, $cond);

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
			$result = $this->receipt_voucher->getValue("chartaccount", $data_var, $cond);

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
			$result = $this->receipt_voucher->saveDetails("partners", $data_post, "vendordetail");
		}

		if($result)
			$msg = "success";
		else
			$msg = "The system has encountered an error in saving. Please contact admin to fix this issue.";

		$dataArray = array( "msg" => $msg );
		return $dataArray;
	
	}

	private function create_payments(){
		$data_post 	= $this->input->post();
		$submit = $data_post['submit'];

		$result    	= array_filter($this->receipt_voucher->savePayment($data_post));

		$code 		= 0;
		$voucher 	= '';
		$errmsg 	= array();

		if($result)
		{
			$code 		= $result['code'];
			$voucher 	= $result['voucher'];
			$errmsg 	= $result['errmsg'];
		}

		$redirect_url = MODULE_URL;
		if ($submit == 'save_new') {
			$redirect_url = MODULE_URL . 'create';
		} else if ($submit == 'save') {
			$redirect_url = MODULE_URL . 'view/' . $voucher;
		} else if ($submit == 'save_exit') {
			$redirect_url = MODULE_URL;
		}

		$dataArray = array("code" => $code, "voucher" => $voucher, "errmsg" => $errmsg);
		return $dataArray;
	}

	private function delete_payments()
	{
		$data_post = $this->input->post("voucher");

		/**
		* Delete Database
		*/
		$result = $this->receipt_voucher->deletePayments($data_post);

		if(empty($result))
			$msg = "success";
		else
			$msg = $result;

		$dataArray = array( "msg" => $msg );
		return $dataArray;
	}

	private function load_payables()
	{
		$data       	= $this->input->post(array("customer", "voucherno", "avl_cred"));
		$task       	= $this->input->post("task");
		$search			= $this->input->post('search');
		$avl_credit 	= $this->input->post("avl_cred");
		$vno 			= $this->input->post('vno');
		
		$check_rows 	= (isset($vno) && (!empty($vno))) ? trim($vno) : "";
		$check_rows  	= str_replace('\\', '', $check_rows);
		$decode_json    = json_decode($check_rows,true);	

		$pagination     = $this->receipt_voucher->retrieveAPList($data,$search);

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
				foreach($decode_json as $value => $row){	
					array_push($voucher_array, $row['vno']);
					$amt_array[$row['vno']] = $row;
				}	
			}

			for($i = 0; $i < count($pagination->result); $i++, $j++)
			{

				$date			= $pagination->result[$i]->transactiondate;
				$restrict_rv 	= $this->restrict->setButtonRestriction($date);
				$date			= $this->date->dateFormat($date);
				$voucher		= $pagination->result[$i]->voucherno;
				$balance		= $pagination->result[$i]->balance; 
				$totalamount	= $pagination->result[$i]->amount;
				$referenceno	= $pagination->result[$i]->referenceno;
				$credit_used	= $pagination->result[$i]->credits_used;

				$voucher_checked= (in_array($voucher , $voucher_array)) ? 'checked' : '';
				$amt_checked 	= (in_array($voucher , $amt_array)) ? $amt_checked : '';

				$total_pay 		+= $totalamount;

				$json_encode_array["row"]       = $i;
				$json_encode_array["vno"] 		= $voucher;
				$json_encode_array["amt"]    	= $totalamount;
				$json_encode_array["bal"]   	= $balance;
				$json_encode_array["cred"]		= $credit_used;
			
				$json_data[] 					= $json_encode_array;
			
				$json_encode 					= json_encode($json_data);

				$appliedamount	= $this->receipt_voucher->getValue("rv_application", array("SUM(amount) AS amount"),"arvoucherno = '$voucher' AND stat IN('posted', 'temporary')");
				$appliedamount  = $appliedamount[0]->amount;
	
				$balance_2		= $balance;
				if (isset($amt_array[$voucher])) {
					$balance_2	= str_replace(',','',$amt_array[$voucher]['bal']);
					$amount		= str_replace(',','',$amt_array[$voucher]['amt']);
					$discount	= isset($amt_array[$voucher]['dis']) ? $amt_array[$voucher]['dis'] : '0.00';
					$credit_used= isset($amt_array[$voucher]['cred']) ? $amt_array[$voucher]['cred'] : '0.00';
					$balance_2	= ($balance_2 > 0) ? $balance_2 : $balance + $amount + $discount + $credit_used;
					$balance_2 	= $balance_2 - $amount - $discount	- $credit_used;
				}
				
				$disable_checkbox 	=	"";
				$disable_onclick 	=	'onClick="selectPayable(\''.$voucher.'\',1);"';

				$table	.= '<tr>'; 
				if(!$restrict_rv){
					$disable_checkbox 	=	"disabled='disabled'";
					$disable_onclick 	= 	'';
				}
				$table	.= 	'<td class="text-center" style="vertical-align:middle;">';
				$table	.= 		'<input type="checkbox" name="checkBox[]" id = "check'.$voucher.'" class = "icheckbox" toggleid="0" row="'.$voucher.'" '.$voucher_checked.' '.$disable_checkbox.'>'; 
				$table	.= 	'</td>';
				$table	.= 	'<td class="text-left" style="vertical-align:middle;" '.$disable_onclick.'>'.$date.'</td>';
				$table	.= 	'<td class="text-left" style="vertical-align:middle;" '.$disable_onclick.'>'.$voucher.'</td>';
				$table	.= 	'<td class="text-left" style="vertical-align:middle;" '.$disable_onclick.'>'.$referenceno.'</td>';
				$table	.= 	'<td class="text-right" style="vertical-align:middle;" id = "payable_amount'.$voucher.'" '.$disable_onclick.' data-value="'.number_format($totalamount,2).'">'.number_format($totalamount,2).'</td>';
				$table	.= 	'<td class="text-right balances" style="vertical-align:middle;" id = "payable_balance'.$voucher.'" '.$disable_onclick.' data-value="'.number_format($balance,2).'">'.number_format($balance_2,2).'</td>';
				// $table	.= 	'<td class="text-right" style="vertical-align:middle;" id = "credit_used'.$voucher.'" '.$disable_onclick.' data-value="'.number_format($credits,2).'">'.number_format($credits,2).'</td>';
				if($voucher_checked == 'checked'){
					$table	.= 	'<td class="text-right pay" style="vertical-align:middle;">'.
					$this->ui->formField('text')
						->setSplit('', 'col-md-12')
						->setClass("input-sm text-right paymentamount")
						->setId('paymentamount'.$voucher)
						->setPlaceHolder("0.00")
						->setMaxLength(20)
						->setAttribute(
							array(
								"onBlur" => ' formatNumber(this.id);', 
								"onClick" => " SelectAll(this.id); ",
								"onChange" => ' checkBalance(this.value,\''.$voucher.'\'); '
							)
						)
						->setValidation('decimal')
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
						->setMaxLength(20)
						->setAttribute(
							array(
								"disabled" => "disabled", 
								"onBlur" => ' formatNumber(this.id);', 
								"onClick" => " SelectAll(this.id); ",
								"onChange" => ' checkBalance(this.value,\''.$voucher.'\'); '
							)
						)
						->setValidation('decimal')
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
										->setMaxLength(20)
										->setAttribute(
											array(
												"onBlur" => ' formatNumber(this.id);', 
												"onClick" => " SelectAll(this.id); ",
												"onChange" => ' checkBalance(this.value,\''.$voucher.'\'); '
											)
										)
										->setValidation('decimal')
										->setValue(number_format($discount, 2))
										->draw($show_input).'</td>';
				}else{
					$table	.= 	'<td class="text-right pay" style="vertical-align:middle;">'.
									$this->ui->formField('text')
										->setSplit('', 'col-md-12')
										->setClass("input-sm text-right discountamount")
										->setId('discountamount'.$voucher)
										->setPlaceHolder("0.00")
										->setMaxLength(20)
										->setAttribute(
											array(
												"disabled" => "disabled", 
												"onBlur" => ' formatNumber(this.id);', 
												"onClick" => " SelectAll(this.id); ",
												"onChange" => ' checkBalance(this.value,\''.$voucher.'\'); '
											)
										)
										->setValidation('decimal')
										->setValue(number_format(0, 2))
										->draw($show_input).'</td>';
				}
				// echo $voucher_checked;
				$avl_credit 	=	str_replace(',','',$avl_credit);
				//&& $avl_credit > 0
				if($voucher_checked == 'checked'){
					$table	.= 	'<td class="text-right pay" style="vertical-align:middle;">'.
					$this->ui->formField('text')
						->setSplit('', 'col-md-12')
						->setClass("input-sm text-right credits_used")
						->setId('credits_used'.$voucher)
						->setPlaceHolder("0.00")
						->setMaxLength(20)
						->setAttribute(
							array(
								"onBlur" => ' formatNumber(this.id);', 
								"onClick" => " SelectAll(this.id); ",
								"onChange" => ' checkCredit(this.value,\''.$voucher.'\'); '
							)
						)
						->setValidation('decimal')
						->setValue(number_format($credit_used,2))
						->draw($show_input).'</td>';
					$table	.= '</tr>';
				}
				else{
					$table	.= 	'<td class="text-right pay" style="vertical-align:middle;">'.
					$this->ui->formField('text')
						->setSplit('', 'col-md-12')
						->setClass("input-sm text-right credits_used")
						->setId('credits_used'.$voucher)
						->setPlaceHolder("0.00")
						->setMaxLength(20)
						->setAttribute(
							array(
								"disabled" => "disabled", 
								"onBlur" => ' formatNumber(this.id);', 
								"onClick" => " SelectAll(this.id); ",
								"onChange" => ' checkCredit(this.value,\''.$voucher.'\'); '
							)
						)
						->setValidation('decimal')
						->setValue(number_format(0, 2))
						->draw($show_input).'</td>';
					$table	.= '</tr>';
				}
			}
		}
		// else
		// {
		// 	$table	.= '<tr>';
		// 	$table	.= 	'<td class="text-center" colspan="6">- No Records Found -</td>';
		// 	$table	.= '</tr>';
		// }
		$pagination->table = $table;
		$dataArray = array( "table" => $pagination->table, "json_encode" => $json_encode, "pagination" => $pagination->pagination, "page" => $pagination->page, "page_limit" => $pagination->page_limit );
		
		return $dataArray;
	}


	private function getrvdetails()
	{
		$checkrows       = $this->input->post("checkrows");
		$cheques       	 = $this->input->post("cheques");
		$overpayment 	 = $this->input->post("overpayment");

		$invoice_data 	= (isset($checkrows) && (!empty($checkrows))) ? trim($checkrows) : "";
		$invoice_data  	= str_replace('\\', '', $invoice_data);
		$decode_json    = json_decode($invoice_data, true);

		$cheques = json_decode(str_replace('\\', '', $cheques));

		$debit      	= '0.00';
		$account_amounts = array();
		$account_dis = array();
		$account_total = array();
		$arvoucher_  = array();
		$dis_amount  = array();

		for($i = 0; $i < count($decode_json); $i++)
		{
			$apvoucherno = $decode_json[$i]["vno"];
			$accountcode = $this->receipt_voucher->getValue('ar_details apd LEFT JOIN chartaccount AS chart ON apd.accountcode = chart.id AND chart.companycode = apd.companycode','accountcode',"voucherno = '$apvoucherno' AND chart.accountclasscode = 'ACCREC'","","","apd.accountcode");
			$accountcode = isset($accountcode[0]->accountcode) 	?	$accountcode[0]->accountcode 	:	"";
			if ( ! isset($account_amounts[$accountcode])) {
				$account_amounts[$accountcode] = 0;
			}
			if ( ! isset($account_dis[$accountcode])) {
				$account_dis[$accountcode] = 0;
			}
			$account_amounts[$accountcode] += str_replace(',','',$decode_json[$i]["amt"]) ; 
			$account_dis[$accountcode] += $decode_json[$i]["dis"] ; 
			$account_total[$accountcode] = $account_amounts[$accountcode] + $account_dis[$accountcode];
			
			$arvoucher_[] = $apvoucherno;
		}

		$condi =  implode("','" , $arvoucher_);
		$cond = "('".$condi."')";

		$customer       	= $this->input->post("customer");
		$data["customer"] 	= $customer;
		$data["cond"]   	= $cond;

		$results			= ($cheques) ? $cheques : array(array());
		$result 			= $this->receipt_voucher->retrieveRVDetails($data);
		$results			= array_merge($results, $result);
		$table 				= "";
		$row 				= 1;
		
		if($overpayment > 0 && isset($overpayment)){
			$overpaymentacct 	=	$this->receipt_voucher->retrieveOPDetails();
			$results 			=	array_merge($results,$overpaymentacct);
		}

		// Retrieve business type list
		$acc_entry_data     = array("id ind","CONCAT(segment5, ' - ', accountname) val");
		$acc_entry_cond     = "";
		$account_entry_list = $this->receipt_voucher->getValue("chartaccount", $acc_entry_data, $acc_entry_cond, "segment5");

		$dis_entry 			= $this->receipt_voucher->getValue("fintaxcode", array("salesAccount"), "fstaxcode = 'DC'");
		$discount_code 		= isset($dis_entry[0]->salesAccount) ? $dis_entry[0]->salesAccount	: "";

		$ui 	            = $this->ui;
		$show_input         = $this->show_input;

		$totalcredit = 0;
		// var_dump($results);
		if(!empty($results))
		{
			$credit      = '0.00';
			$count       = count($results);
			
			for($i = 0; $i < $count; $i++, $row++)
			{
				$accountcode       = (!empty($results[$i]->accountcode)) ? $results[$i]->accountcode : "";
				$detailparticulars = (!empty($results[$i]->detailparticulars)) ? $results[$i]->detailparticulars : "";
				$ischeck 			= (!empty($results[$i]->ischeck)) 				? $results[$i]->ischeck 			: "no";
				$isoverpayment 		= (!empty($results[$i]->is_overpayment)) 	?	$results[$i]->is_overpayment 	:	"no";
				$debit 				= (isset($results[$i]->chequeamount)) ? $results[$i]->chequeamount : "0";
	
				if($isoverpayment == 'yes'){
					$credit 			= number_format($overpayment,2);
				} else {
					$credit 			= (isset($account_total[$accountcode])) ? $account_total[$accountcode] : 0;
					$credit 			= ($overpayment > 0) ? $credit - $overpayment 	:	$credit;
					$credit 			= number_format($credit,2);
				}
				$totalcredit     	+= $debit; 

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
									->setClass('description')
									->setValue($detailparticulars)
									->draw($show_input).
							'	<input type = "hidden" class="ischeck" value="'.$ischeck.'" name="ischeck['.$row.']" id="ischeck['.$row.']">
							</td>';
				$table  .=  '<td class = "remove-margin">'
								.$ui->formField('text')
									->setSplit('', 'col-md-12')
									->setName('debit['.$row.']')
									->setId('debit['.$row.']')
									->setClass('text-right debit account_amount')
									->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('debit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);"))
									->setValue((number_format($debit, 2)))
									->draw($show_input).			
								'</td>';
				$table 	.= '<td class = "remove-margin">'
								.$ui->formField('text')
									->setSplit('', 'col-md-12')
									->setName('credit['.$row.']')
									->setClass("text-right  credit")
								    ->setId('credit['.$row.']')
									->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('credit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);"))
									->setValue($credit)
									->draw($show_input).
							'</td>';
				$table  .= '<td class="text-center">
								<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id='.$row.' id='.$row.' name="chk[]" style="outline:none;" onClick="confirmDelete('.$row.');"><span class="glyphicon glyphicon-trash"></span></button>
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
		$dataArray = array( "table" => $table, "totaldebit" => number_format($totalcredit, 2),"discount_code"=>$discount_code );
		return $dataArray;
	}

	private function load_list()
	{
		$data_post 	= $this->input->post(array("daterangefilter", "customer", "filter", "search", "sort"));

		$list   	= $this->receipt_voucher->retrieveList($data_post);
		
		$table  	= "";
		$login		= $this->session->get('login');
		$groupname 	= $login['groupname'];
		
		$has_access = $this->receipt_voucher->retrieveAccess($groupname);
		
		if( !empty($list->result) ) :
			$prevvno = '';
			$nextvno = '';
			foreach($list->result as $key => $row)
			{
				$date        	= $row->paymentdate;
				$restrict_rv 	= $this->restrict->setButtonRestriction($date);
				$date       	= $this->date->dateFormat($date);
				$voucher   		= $row->voucherno; 
				$customer		= $row->partner; 
				$reference		= $row->reference;
				$paymentmode 	= $row->paymentmode; 
				$amount	  	 	= $row->amount;
				$status   		= $row->status;
				$or_no   		= $row->or_no;
				/**
				 * Cheque Details
				 */
				$bankaccount   	= $row->bankaccount;
				$chequenumber   = $row->chequenumber;
				$bank   		= $row->bank;
				$chequedate   	= $this->date->dateFormat($row->chequedate);
				$chequeamount  	= $row->chequeamount;

				$prevvno 		= $voucher;
				$voucher_status = '<span class="label label-danger">'.strtoupper($status).'</span>';
				if($status == 'open'){
					$voucher_status = '<span class="label label-info">'.strtoupper($status).'</span>';
				}else if($status == 'posted'){
					$voucher_status = '<span class="label label-success">'.strtoupper($status).'</span>';
				}

				$has_edit 		= isset($has_access[0]->mod_edit)		?	$has_access[0]->mod_edit	:	0;
				$has_delete		= isset($has_access[0]->mod_delete)		?	$has_access[0]->mod_delete	:	0;
				$has_post  		= isset($has_access[0]->mod_post)		?	$has_access[0]->mod_post	:	0;
				$has_unpost 	= isset($has_access[0]->mod_unpost)		?	$has_access[0]->mod_unpost	:	0;

				$show_btn 		= ($status == 'open' && $restrict_rv);
				$show_edit 		= ($status == 'open' && $has_edit == 1 && $restrict_rv);
				$show_dlt 		= ($status == 'open' && $has_delete == 1 && $restrict_rv);
				$show_post 		= ($status == 'open' && $has_post == 1 && $restrict_rv);
				$show_unpost 	= ($status == 'posted' && $has_unpost == 1 && $restrict_rv);

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
							// ->addDelete($show_dlt)
							->addCheckbox($show_btn)
							->setValue($voucher)
							->draw();
			

				if($nextvno != $prevvno){
					$table	.= '<tr>';
					$table	.= '<td class="text-center">'.$dropdown.'</td>';
					$table	.= '<td >'.$date.'</td>';
					$table	.= '<td >'.$voucher.'</td>';
					$table	.= '<td >'.$customer.'</td>';
					$table	.= '<td >'.$or_no.'</td>';
					$table	.= '<td >'.ucwords($paymentmode).'</td>';
					$table	.= '<td class="text-right" >'.number_format($amount,2).'</td>';
					$table	.= '<td >'.$voucher_status.'</td>';
					$table	.= '</tr>';
				}

				if($paymentmode == 'cheque'){
					if($nextvno != $prevvno){
						$table	.= '<tr>';
						$table	.= '<td></td>';
						$table	.= '<td colspan="2" class="warning" ><strong>Bank Account</strong></td>';
						$table	.= '<td class="warning" ><strong>Bank</strong></td>';
						
						$table	.= '<td class="warning" ><strong>Check Number</strong></td>';
						$table	.= '<td class="warning" ><strong>Check Date</strong></td>';
						$table	.= '<td class="warning" ><strong>Check Amount</strong></td>';
						$table	.= '</tr>';


					}
					$table	.= '<tr >';
					$table	.= '<td></td>';
					$table	.= '<td colspan="2" class="warning">'.$bankaccount.'</td>';
					$table	.= '<td class="warning">'.$bank.'</td>';
					
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
		$data_get = $this->input->post(array("daterangefilter", "customer", "filter", "search", "sort"));
		$data_get['daterangefilter'] = str_replace(array('%2F', '+'), array('/', ' '), $data_get['daterangefilter']);
		$result2 = $this->receipt_voucher->fileExportlist($data_get);
		
		$header = array("Date","Voucher","Customer","Reference","Amount","Status");
		
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
				$customer		 	= $row->partner; 
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
					$csv .= '"' . $customer . '",';
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
	
	private function retrieve_credits(){
		$customer 	=	$this->input->post("customer");

		$ret_credit =	$this->receipt_voucher->retrieve_existing_credits($customer);
		$credits 	= 	(isset($ret_credit[0]->curr_credit) && $ret_credit[0]->curr_credit > 0) ? $ret_credit[0]->curr_credit 	:	0;

		$dataArray = array("credits"=>$credits);
		return $dataArray;
	}

	private function retrieve_op_acct(){
		$overpaymentacct 	=	$this->receipt_voucher->retrieveOPDetails();
		$op_acct 			=	isset($overpaymentacct[0]->accountcode) 	?	$overpaymentacct[0]->accountcode 	:	"";
		$dataArray 	=	array("account"=>$op_acct);
		return $dataArray;
	}
}