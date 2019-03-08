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
			"paymenttype",
			"taxcode",
			"taxbase_amount"
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
		
		$acc_entry_data               = array("coa.id ind","CONCAT(coa.segment5, ' - ', coa.accountname) val");
		$acc_entry_cond               = "coa.accounttype != '' AND coa.stat = 'active'";
		$acc_entry_join 			  = "chartaccount coa2 ON coa2.parentaccountcode = coa.id";
		$acc_entry_order 			  = "coa.segment5, coa2.segment5";
		$data["account_entry_list"]   = $this->receipt_voucher->getValue("chartaccount coa", $acc_entry_data, $acc_entry_cond, $acc_entry_order,"","",$acc_entry_join);

		$atc_entry_data               = array("atcId ind","CONCAT(atc_code, ' - ', short_desc) val");
		$atc_entry_cond               = "stat = 'active'";
		$data["atc_list"]   		  = $this->receipt_voucher->getValue("atccode", $atc_entry_data, $atc_entry_cond, "","","","");

		// Cash Account Options
		$cash_account_fields 	  	= "c.id ind , CONCAT(shortname,' - ' ,accountno ) val";
		$cash_account_cond 	 	  	= "b.stat = 'active' AND b.checking_account = 'yes'";
		$cash_order_by 		 	  	= "id desc";
		$cash_account_join 	 	  	= "chartaccount c ON b.gl_code = c.segment5";
		$data["cash_account_list"] 	= $this->receipt_voucher->retrievebank("bank b", $cash_account_fields, $cash_account_cond ,$cash_account_join ,$cash_account_cond, '');

		$data["generated_id"]     		= '';
		$cred_acct						= $this->receipt_voucher->retrieve_existing_credacct();
		$data["existingcreditaccount"]	= isset($cred_acct[0]->account) ? $cred_acct[0]->account	:	"";
		$data['cred_id'] 				= isset($cred_acct[0]->id) ? $cred_acct[0]->id	:	"";
		$data['advcredacct'] 			= $this->receipt_voucher->retrieveCredAccountsList();
		$op_acct						= $this->receipt_voucher->retrieve_existing_opacct();
		$data["existingopaccount"]		= isset($op_acct[0]->account) 	? $op_acct[0]->account	:	"";
		$data['op_acct'] 				= isset($op_acct[0]->id) 		? $op_acct[0]->id		:	"";
		$data['opacctlist'] 			= $this->receipt_voucher->retrieveOverpaymentAccountList();
		$data['ap_checker'] 	 		= 0;
		$data['op_checker'] 	 		= 0;
		$data['cwt_checker'] 	 		= 0;

		// Application Data
		$data['sum_applied'] 				= 0;
		$data['sum_discount']				= 0;
		$data['credits_applied'] 			= 0;
		$data['payments'] 					= "''";
		$data['credits_box'] 				= "''";
		$data['available_credits'] 			= "0.00";
		$data['credits_used'] 				= 0;
		$data['overpayment'] 				= 0;
		$data['current_tagged_receivables'] = 0;

		$data["listofcheques"]		= "";
		$data["show_cheques"] 		= 'hidden';

		$data['restrict_rv'] 		= true;

		$ar_acct 			=	'';
		$data['ar_acct'] 	=	$ar_acct;
		$data['status_badge']=  "";

		$this->view->load('receipt_voucher/receipt_voucher', $data);
	}

	public function update_temporarily_saved_data(){
		
		$data_validate 	= $this->input->post();
		$ap_checker 	= $data_validate['advance_payment'];
		$op_checker 	= $data_validate['overpayment'];

		$errmsg 			= array();
		$temp 				= array();
		$updateTempRecord 	= 0;
		$generatedvoucher 	= "";
		$task 			= $data_validate['h_task'];
		$generatedvoucher = '';

		if (!empty($data_validate["customer"]) && !empty($data_validate["document_date"])) {
			$voucherno  = (isset($data_validate['h_voucher_no']) && (!empty($data_validate['h_voucher_no']))) ? htmlentities(trim($data_validate['h_voucher_no'])) : "";
			$customer 	= $data_validate["customer"];

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
				$rvappcond					= "voucherno = '$voucherno' AND stat = 'temporary'";
				$updateTempRecord			= $this->receipt_voucher->editData($update_info,"rv_application",$rvappcond);
				$update_cheque['voucherno']	= $generatedvoucher;
				$updateTempRecord			= $this->receipt_voucher->editData($update_cheque,"rv_cheques",$update_condition);
				// Update TMP source of CM
				$update_source['si_no']  	= $generatedvoucher;
				$source_cond 				= "si_no = '$voucherno' AND transtype = 'CM'";
				$updateTempRecord			= $this->receipt_voucher->editData($update_source,"journalvoucher",$source_cond);
					
				// Generate Credit Voucher

				if($updateTempRecord && ($ap_checker == "yes" || $op_checker == "yes")){
					$updateTempRecord 		= $this->receipt_voucher->generateCreditVoucher($data_validate, $generatedvoucher, $ap_checker);
				}

				// Update the Applied Credit Voucher 
				$update_cred['rv_voucher']  = $generatedvoucher;
				$update_cred['stat'] 		= "active";
				$cred_cond 					= "rv_voucher = '$voucherno'";
				$updateTempRecord			= $this->receipt_voucher->editData($update_cred,"creditvoucher_applied",$cred_cond);

				// Update the AR
				$applied_sum				= 0;
				$applied_discount			= 0;
				$applied_forexamount		= 0;

				$ar_voucher 				= $this->receipt_voucher->getValue("rv_application", "arvoucherno", "voucherno = '$generatedvoucher'");
				// $ar_voucher 				= isset($ar_voucher[0]->arvoucherno) 	?	$ar_voucher[0]->arvoucherno 	:	"";

				foreach($ar_voucher as $key => $row){
					$arvoucher 	=	isset($row->arvoucherno) ? $row->arvoucherno : "";

					$invoice_amounts			= $this->receipt_voucher->getValue("accountsreceivable", array("amount as convertedamount"), " voucherno = '$arvoucher' AND stat IN('open','posted') ");
					$applied_amounts			= $this->receipt_voucher->getValue(
													"rv_application",
													array(
														"COALESCE(SUM(amount),0) convertedamount",
														"COALESCE(SUM(discount),0) discount",
														"COALESCE(SUM(credits_used),0) credits",
														"COALESCE(SUM(overpayment),0) overpayment",
														"COALESCE(SUM(forexamount),0) forexamount"
													), 
													"  arvoucherno = '$arvoucher' AND stat IN('open','posted') "
												);

					$invoice_amount				= (!empty($invoice_amounts)) ? $invoice_amounts[0]->convertedamount : 0;
					$applied_credits 			= (!empty($applied_amounts[0]->credits)) ? $applied_amounts[0]->credits : 0;
					$applied_disc 				= (!empty($applied_amounts[0]->discount)) ? $applied_amounts[0]->discount : 0;
					$applied_over 				= (!empty($applied_amounts[0]->overpayment)) ? $applied_amounts[0]->overpayment : 0;
					$applied_sum				= $applied_amounts[0]->convertedamount - $applied_amounts[0]->forexamount + $applied_credits + $applied_over + $applied_disc;
					$applied_sum				= (!empty($applied_sum)) ? $applied_sum : 0;

					$balance_info['amountreceived']	= $applied_sum;
					$balance_info['excessamount'] 	= ($applied_over >= 0) 	?	$applied_over 	:	0;
					$balance_amt 					= $invoice_amount - $applied_sum;
					$balance_info['balance']		= ($balance_amt >= 0) 	?	$balance_amt	:	0;	
				
					$updateTempRecord = $this->receipt_voucher->updateData("accountsreceivable", $balance_info, "voucherno = '$arvoucher'");
						
				}
				if($updateTempRecord){
					$partner_dtl 	=$this->receipt_voucher->getValue(
											"partners", 
											"credits_amount", 
											" partnercode = '$customer' "
										);

					$existing_credit	= ($partner_dtl[0]->credits_amount > 0) ? $partner_dtl[0]->credits_amount 	:	0;
					
					$existing_credit 	+=	$applied_over;
					$partner_info['credits_amount'] 	=	( $existing_credit - $applied_credits );

					$updateTempRecord = $this->receipt_voucher->updateData("partners", $partner_info, "partnercode = '$customer'");
				}		
			}
		}

		if($updateTempRecord){
			if($task == 'create'){
				$this->logs->saveActivity("Add New Receipt Voucher [$generatedvoucher]");
			} else if($task == 'edit'){
				$voucherno 	=	$data_validate['h_voucher_no'];
				$this->logs->saveActivity("Update Receipt Voucher [$voucherno]");
			}
		}

		$dataArray = array("success"=>$updateTempRecord,"error"=>$errmsg, 'voucher' => $generatedvoucher);
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

		$acc_entry_data               = array("coa.id ind","CONCAT(coa.segment5, ' - ', coa.accountname) val");
		$acc_entry_cond               = "coa.accounttype != '' AND coa.stat = 'active'";
		$acc_entry_join 			  = "chartaccount coa2 ON coa2.parentaccountcode = coa.id";
		$acc_entry_order 			  = "coa.segment5, coa2.segment5";
		$data["account_entry_list"]   = $this->receipt_voucher->getValue("chartaccount coa", $acc_entry_data, $acc_entry_cond, $acc_entry_order,"","",$acc_entry_join);
		
		$data["customer_list"]    	= array();

		$atc_entry_data               = array("atcId ind","atc_code val");
		$atc_entry_cond               = "stat = 'active'";
		$data["atc_list"]   		  = $this->receipt_voucher->getValue("atccode", $atc_entry_data, $atc_entry_cond, "","","","");

		// Main
		$vendor_details 		   	= $this->receipt_voucher->getValue("partners", "partnername"," partnertype = 'customer' AND partnercode = '".$data["main"]->customer."'", "");

		$transactiondate 			= $data["main"]->transactiondate;
		$voucherno 					= $data["main"]->voucherno;
		$restrict_rv 				= $this->restrict->setButtonRestriction($transactiondate);
		$data["voucherno"]         	= $voucherno;
		$data["customercode"]       = $vendor_details[0]->partnername;
		$data["v_convertedamount"] 	= $data["main"]->convertedamount;
		$data["exchangerate"]      	= $data["main"]->exchangerate;
		$data["transactiondate"]   	= $this->date->dateFormat($transactiondate);
		$data["or_no"]       		= $data["main"]->or_no;
		$data["paymenttype"]       	= $data["main"]->paymenttype;
		$data["particulars"]       	= $data["main"]->particulars;
		$data['status']				= $data["main"]->stat;
		$data['ap_checker'] 	 	= ($data['main']->advancepayment == 'yes') ? 1 : 0;
		$data['op_checker'] 	 	= ($data['main']->opchecker == 'yes') ? 1 : 0;
		$data['cwt_checker'] 	 	= ($data['main']->cwt == 'yes') ? 1 : 0;

		// Vendor/Customer Details
		$data["v_vendor"] 		   	= $data["vend"]->name;
		$data["v_email"] 		   	= $data["vend"]->email;
		$data["tinno"] 		   	   	= $data["vend"]->tinno;
		$data["address1"] 	       	= $data["vend"]->address1;
		$data["terms"] 	   		   	= $data["vend"]->terms;
		$data["businesstype"] 	   	= $data["vend"]->businesstype;
		$data["partnercode"] 	   	= $data["vend"]->partnercode;
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
		$data['sum_applied'] 					= $sum_applied;
		$data['sum_discount'] 					= $sum_discount;
		$data['current_tagged_receivables'] 	= 0;
		//Credits
		$credits_applied 		= $data['credits'];

		$total_cr_applied		= 0;
		$applied 				= [];
		if($credits_applied){
			foreach($credits_applied as $key=>$row){
				if(isset($row->amount)) {
					$total_cr_applied += $row->amount;
				}
			}
		}
		$data['credits_applied'] 	= $total_cr_applied;
		$data['credits_box']     	= json_encode($applied);

		$data['restrict_rv'] 			= $restrict_rv;
		$cred_acct						= $this->receipt_voucher->retrieve_existing_credacct();
		$data["existingcreditaccount"]	= isset($cred_acct[0]->account) ? $cred_acct[0]->account	:	"";
		$data['cred_id'] 				= isset($cred_acct[0]->id) ? $cred_acct[0]->id	:	"";
		$data['advcredacct'] 			= $this->receipt_voucher->retrieveCredAccountsList();
		$op_acct						= $this->receipt_voucher->retrieve_existing_opacct();
		$data["existingopaccount"]		= isset($op_acct[0]->account) 	? $op_acct[0]->account	:	"";
		$data['op_acct'] 				= isset($op_acct[0]->id) 		? $op_acct[0]->id		:	"";
		$data['opacctlist'] 			= $this->receipt_voucher->retrieveOverpaymentAccountList();
	
		// Overpayment Acct
		// $op_acct  						= $this->receipt_voucher->retrieveOPDetails();
		// $data['op_acct'] 				= isset($op_acct[0]->accountcode) 	? 	$op_acct[0]->accountcode 	:	"";

		// Credit Voucher Checker
		$cv_checker 					= $this->receipt_voucher->checkifCVinuse($voucherno);
		$cv_status 						= isset($cv_checker->status) 	?	$cv_checker->status 	:	""; 	
		$data['cv_status'] 				= $cv_status;

		$ar_acct 	=	'';
		$details = $data['details'];
		foreach($details as $key=>$row){
			if(isset($row->accrecid)) {
				$ar_acct = $row->accrecid;
			}
		}
		$data['ar_acct'] 	=	$ar_acct;

		/**
		 * Status Badge
		 */
		
		$status 		= $data["main"]->stat;
		if($status == 'cancelled'){
			$status_class 	= 'danger';
		} else if($status == 'open'){
			$status_class 	= 'info';
		} else if($status == 'posted'){
			$status_class 	= 'success';
		}

		$status_badge = '<span class="label label-'.$status_class.'">'.strtoupper($status).'</span>';
		$data['status_badge'] 	= $status_badge;

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

		$atc_entry_data               = array("atcId ind","CONCAT(atc_code, ' - ', short_desc) val");
		$atc_entry_cond               = "stat = 'active'";
		$data["atc_list"]   		  = $this->receipt_voucher->getValue("atccode", $atc_entry_data, $atc_entry_cond, "","","","");

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
		
		$acc_entry_data               = array("coa.id ind","CONCAT(coa.segment5, ' - ', coa.accountname) val");
		$acc_entry_cond               = "coa.accounttype != '' AND coa.stat = 'active'";
		$acc_entry_join 			  = "chartaccount coa2 ON coa2.parentaccountcode = coa.id";
		$acc_entry_order 			  = "coa.segment5, coa2.segment5";
		$data["account_entry_list"]   = $this->receipt_voucher->getValue("chartaccount coa", $acc_entry_data, $acc_entry_cond, $acc_entry_order,"","",$acc_entry_join);

		$dis_entry 					= $this->receipt_voucher->getValue("fintaxcode", array("salesAccount"), "fstaxcode = 'DC'");
		$discount_code 				= isset($dis_entry[0]->salesAccount) ? $dis_entry[0]->salesAccount	: "";
		$data['discount_code'] 		= $discount_code;

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
		$data['overpayment'] 	 = $data["main"]->overpayment;
		$data["credits_used"]    = $credits_used;
		$available_credits 		 = $this->receipt_voucher->retrieve_existing_credits($customer);
		$data["available_credits"] = isset($available_credits[0]->curr_credit) 	?	$available_credits[0]->curr_credit 	+	$credits_used	:	"0.00";
		$data['status']			 = $data["main"]->stat;
		$data['ap_checker'] 	 = ($data['main']->advancepayment == 'yes') ? 1 : 0;
		$data['op_checker'] 	 = ($data['main']->opchecker == 'yes') ? 1 : 0;
		$data['cwt_checker'] 	 = ($data['main']->cwt == 'yes') ? 1 : 0;
		$data["listofcheques"]	 = isset($data['rollArray'][$sid]) ? $data['rollArray'][$sid] : array();

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

		$data['sum_applied'] 					= $sum_applied;
		$data['sum_discount'] 					= $sum_discount;
		$data['payments'] 						= json_encode($payments);
		$data['current_tagged_receivables'] 	= $sum_applied;

		//Credits
		$credits_applied 		= $data['credits'];
		$total_cr_applied		= 0;
		$total_cr_opapplied		= 0;
		$credits 				= 0;
		$applied 				= [];
		if($credits_applied){
			foreach($credits_applied as $key=>$row){
				if(isset($row->amount)) {
					$vno 		=	$row->cvo;
					$source 	=	$row->source;
					// $total_cr_applied += $row->amount;
					$applied[$vno][$source]['amount']  = $row->balance;
					$applied[$vno][$source]['toapply'] = $row->amount;
					$applied[$vno][$source]['balance'] = $row->balance - $row->amount;
					$total_cr_applied 	+= $row->amount;
					$total_cr_opapplied += 	($source == "OP") ?  	$row->amount	:	0;

				}
			}
		}
		$data['credits_applied'] 			= $total_cr_applied;
		$data['total_opcredits_to_apply'] 	= $total_cr_opapplied;
		$data['credits_box'] 				= json_encode($applied);

		$data['restrict_rv'] 			= true;
		$data['has_access'] 			= 0;

		$cred_acct						= $this->receipt_voucher->retrieve_existing_credacct();
		$data["existingcreditaccount"]	= isset($cred_acct[0]->account) ? $cred_acct[0]->account	:	"";
		$data['cred_id'] 				= isset($cred_acct[0]->id) ? $cred_acct[0]->id	:	"";
		$data['advcredacct'] 			= $this->receipt_voucher->retrieveCredAccountsList();
		$op_acct						= $this->receipt_voucher->retrieve_existing_opacct();
		$data["existingopaccount"]		= isset($op_acct[0]->account) 	? $op_acct[0]->account	:	"";
		$data['op_acct'] 				= isset($op_acct[0]->id) 		? $op_acct[0]->id		:	"";
		$data['opacctlist'] 			= $this->receipt_voucher->retrieveOverpaymentAccountList();
		$data['saved_op_acct'] 			= $data["main"]->opcode;
		$data['saved_adv_acct'] 		= $data["main"]->advcode;
		// $op_acct  					= $this->receipt_voucher->retrieveOPDetails();
		// $data['op_acct'] 			= isset($op_acct[0]->accountcode) 	? 	$op_acct[0]->accountcode 	:	"";
		
		$ar_acct 	=	'';
		$details = $data['details'];
		foreach($details as $key=>$row){
			if(isset($row->accrecid)) {
				$ar_acct = $row->accrecid;
			}
		}
		$data['ar_acct'] 	=	$ar_acct;
		$data['status_badge']=  "";

		$this->view->load('receipt_voucher/receipt_voucher', $data);
	}

	public function print_preview($voucherno) 
	{
		// Retrieve Document Info
		$sub_select = $this->receipt_voucher->retrieveData("rv_application", array("SUM(amount) AS amount"), "voucherno = '$voucherno'");

		$sub_select[0]->amount;
		
		$docinfo_table  = "receiptvoucher as pv";
		$docinfo_fields = array('pv.transactiondate AS documentdate','pv.voucherno AS voucherno',"CONCAT( first_name, ' ', last_name )","'{$sub_select[0]->amount}' AS amount",'pv.amount AS pvamount', "or_no AS referenceno", "particulars AS remarks", "p.partnername AS customer","advancepayment");
		$docinfo_join   = "partners as p ON p.partnercode = pv.customer AND p.companycode = pv.companycode";
		$docinfo_cond 	= "pv.voucherno = '$voucherno'";

		$documentinfo  	= $this->receipt_voucher->retrieveData($docinfo_table, $docinfo_fields, $docinfo_cond, $docinfo_join);

		$customer 	    = $documentinfo[0]->customer;
		$ap_checker 	= $documentinfo[0]->advancepayment;

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
		$status = $this->receipt_voucher->getStatus($voucherno);
		
		foreach ($ap_voucher as $row) {
			$apvoucher[] = $row->arvoucherno;
		}
		$ap =  !empty($apvoucher) ? implode("','" , $apvoucher) : "";
		$ap_no = "('".$ap."')";
		$ap_amount    = $this->receipt_voucher->getValue("accountsreceivable", array("SUM(amount) total_amount"), "voucherno IN $ap_no" );
		$total_amount = $ap_amount[0]->total_amount;
		$amount = isset($paymentArray[0]->amount) ? $paymentArray[0]->amount : 0;
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
		$chequeArray_2 = "";
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
		if($ap_checker != "yes"){
			$p_table = "rv_application pv";
			$p_fields = array("arvoucherno voucherno", "pv.amount amount", "ap.sourceno si_no", "pv.discount discount") ;
			$p_cond = (!empty($pv_v)) ? "pv.voucherno IN($pv_v) " : "";
			$p_join = "accountsreceivable ap ON pv.arvoucherno = ap.voucherno" ;
			$appliedpaymentArray = $this->receipt_voucher->retrieveData($p_table, $p_fields, $p_cond, $p_join);
		}

		// Setting for PDFs
		$print = new print_voucher_model('P', 'mm', 'Letter');
		$print->setDocumentType('Receipt Voucher')
				->setDocumentInfo($documentinfo[0])
				->setCustomer($customer)
				->setVoucherStatus(strtoupper($status->stat))
				->setPayments($chequeArray_2)
				->setDocumentDetails($documentdetails)
				->setCheque($chequeArray);
		if($ap_checker!="yes"){
			$print->setAppliedPayment($appliedpaymentArray);
		}
			$print->drawPDF('rv_voucher_' . $voucherno);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_savebusinesstype() {
		$businesstype = $_GET['type'];
		$partnercode = $_GET['partnercode'];
		$result = $this->receipt_voucher->updateBusinessType($businesstype, $partnercode);
		return array(
			// 'redirect'	=> MODULE_URL . 'sawt_csv?' . $client_id,
			'success'	=> $result
		);
	}
	
	private function load_credit_vouchers(){
		$data       	= $this->input->post(array("customer", "voucherno", "avl_cred"));
		$task       	= $this->input->post("task");
		$search			= $this->input->post('search');
		$avl_credit 	= $this->input->post("avl_cred");
		$vno 			= $this->input->post('vno');
		$customer 		= $this->input->post('customer');
		
		$check_rows 	= (isset($vno) && (!empty($vno))) ? trim($vno) : "";
		$check_rows  	= str_replace('\\', '', $check_rows);
		$decode_json    = json_decode($check_rows,true);

		$pagination     = $this->receipt_voucher->retrieveCreditsList($customer,$vno);

		$table             = "";
		$j 	               = 1;
		$json_encode_array = array();
		$json_data         = array();
		$total_pay         = 0;
		$json_encode       = "";
		$edited_amount 	   = 0;

		$show_input 	   = ($task != "view") 	? 	1	:	0;
	
		if (empty($pagination->result)) {
			$table = '<tr><td class="text-center" colspan="10"><b>No Records Found</b></td></tr>';
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
					array_push($voucher_array, $value);
					$amt_array[$value] = $row;
				}	
			}

			for($i = 0; $i < count($pagination->result); $i++, $j++){
				$voucherno 		=	isset($pagination->result[$i]->voucherno) 	? 	$pagination->result[$i]->voucherno 		: 	"";
				$totalamt		=	isset($pagination->result[$i]->amount) 		? 	$pagination->result[$i]->amount 		:	0;
				$balance 		=	isset($pagination->result[$i]->balance)		?	$pagination->result[$i]->balance 		: 	0;
				$invoiceno		=	isset($pagination->result[$i]->invoiceno) 	? 	$pagination->result[$i]->invoiceno 		: 	0;
				$referenceno 	=	isset($pagination->result[$i]->referenceno) ? 	$pagination->result[$i]->referenceno	: 	"";
				$receivableno 	=	isset($pagination->result[$i]->receivableno)? 	$pagination->result[$i]->receivableno	:	"";
				$orig_balance 	=	isset($pagination->result[$i]->orig_balance)? 	$pagination->result[$i]->orig_balance 	:	0;
				$source 		=	isset($pagination->result[$i]->source)		? 	$pagination->result[$i]->source			:	"";

				$voucher_checked= (in_array($voucherno , $voucher_array)) ? 'checked' : '';

				$balance_2		= $balance;
				$amount 		= 0;
				if (isset($amt_array[$voucherno][$source])) {
					$amount		= str_replace(',','',$amt_array[$voucherno][$source]['toapply']);
					$balance_2	= str_replace(',','',$amt_array[$voucherno][$source]['balance']);
					// $balance 	+= $balance_2;
				}		
				// echo "total amount ".$totalamt . "\n";
				// echo 'amount' . $amount."\n";
				// echo 'balance' . $balance."\n";
				$balance 		= ($balance_2 > 0 && $amount > 0) ? $balance_2 + $amount : $balance;
				// echo "computed balance ". $balance."\n";
				$disable_checkbox 	=	"";
				$disable_onclick 	=	'onClick="selectCredits(\''.$voucherno.'\',1);"';

				$table	.= '<tr>'; 
				$table	.= 	'<td class="text-center" style="vertical-align:middle;">';
				$table	.= 		'<input type="checkbox" name="checkBox[]" id = "check'.$voucherno.'" class = "icheckbox" toggleid="0" row="'.$voucherno.'" '.$voucher_checked.' '.$disable_checkbox.'>'; 
				$table	.= 	'</td>';
				$table	.= 	'<td class="text-left" style="vertical-align:middle;" '.$disable_onclick.'>'.$voucherno.'</td>';
				$table	.= 	'<td class="text-left" style="vertical-align:middle;" '.$disable_onclick.'>'.$invoiceno.'</td>';
				$table	.= 	'<td class="text-left" style="vertical-align:middle;" '.$disable_onclick.'>'.$referenceno.'</td>';
				$table	.= 	'<td class="text-right" style="vertical-align:middle;" id = "credits_amount'.$voucherno.'" '.$disable_onclick.' data-value="'.number_format($totalamt,2).'">'.number_format($totalamt,2).'</td>';
				$table	.= 	'<td class="text-right balances" style="vertical-align:middle;" id = "credits_balance'.$voucherno.'" '.$disable_onclick.' data-value="'.number_format($balance,2).'">'.number_format($balance_2,2).'</td>';
				$table	.= 	'<td class="text-right hidden sourcetype" style="vertical-align:middle;" id = "source'.$voucherno.'" '.$disable_onclick.' data-value="'.$source.'">'.$source.'</td>';
				
				if($voucher_checked == 'checked'){
					$table	.= 	'<td class="text-right pay" style="vertical-align:middle;">'.
					$this->ui->formField('text')
						->setSplit('', 'col-md-12')
						->setClass("input-sm text-right credittoapply")
						->setId('credittoapply'.$voucherno)
						->setPlaceHolder("0.00")
						->setMaxLength(20)
						->setAttribute(
							array(
								"onBlur" => ' formatNumber(this.id);', 
								"onClick" => " SelectAll(this.id); ",
								"onChange" => ' computeCreditBalance(\''.$voucherno.'\',this.value); '
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
						->setClass("input-sm text-right credittoapply")
						->setId('credittoapply'.$voucherno)
						->setPlaceHolder("0.00")
						->setMaxLength(20)
						->setAttribute(
							array(
								"disabled" => "disabled", 
								"onBlur" => ' formatNumber(this.id);', 
								"onClick" => " SelectAll(this.id); ",
								"onChange" => ' computeCreditBalance(\''.$voucherno.'\',this.value); '
							)
						)
						->setValidation('decimal')
						->setValue(number_format(0, 2))
						->draw($show_input).'</td>';
				}
			}
		}
		$pagination->table = $table;
		$dataArray = array( "table" => $pagination->table, "json_encode" => $json_encode, "pagination" => $pagination->pagination, "page" => $pagination->page, "page_limit" => $pagination->page_limit );
		
		return $dataArray;
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
	
	private function cancel_cv_entries()
	{
		$vouchers 		= $this->input->post('delete_id');
		$payments 		= "'" . implode("','", $vouchers) . "'";

		$cv_vouchers 	= $this->receipt_voucher->getValue("creditvoucher", "voucherno", "transtype = 'CV' AND referenceno IN ($payments)");

		$result 		= 0;
		foreach($cv_vouchers as $key => $content){
			$cm_no 			=  	$content->voucherno;
			$result 		= 	$this->receipt_voucher->cancelCreditVoucher($cm_no);
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
			$this->logs->saveActivity("Posted Receipt Voucher [$id]");
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
			$this->logs->saveActivity("Unposted Receipt Voucher [$id]");
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

		// echo $submit;
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
		$data       	= $this->input->post(array("customer", "voucherno", "avl_cred","task"));
		$rvno 			= $this->input->post('voucherno');
		$task       	= $this->input->post("task");
		$search			= $this->input->post('search');
		$avl_credit 	= $this->input->post("avl_cred");
		$vno 			= $this->input->post('vno');
		$rv 			= $this->input->post('voucherno');

		$check_rows 	= (isset($vno) && (!empty($vno))) ? trim($vno) : "";
		$check_rows  	= str_replace('\\', '', $check_rows);
		$decode_json    = json_decode($check_rows,true);	
		// var_dump($decode_json);
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
			$table = '<tr><td class="text-center" colspan="10"><b>No Records Found</b></td></tr>';
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
			// var_dump($amt_array);

			for($i = 0; $i < count($pagination->result); $i++, $j++){
				$date			= $pagination->result[$i]->transactiondate;
				$restrict_rv 	= $this->restrict->setButtonRestriction($date);
				$date			= $this->date->dateFormat($date);
				$voucher		= $pagination->result[$i]->voucherno;
				$balance		= $pagination->result[$i]->balance; 
				$totalamount	= $pagination->result[$i]->amount;
				$referenceno	= $pagination->result[$i]->referenceno;
				$overpayment	= $pagination->result[$i]->overpayment;
				$payment		= $pagination->result[$i]->payment;	

				$voucher_checked= (in_array($voucher , $voucher_array)) ? 'checked' : '';
				$amt_checked 	= (in_array($voucher , $amt_array)) ? $amt_checked : '';

				$total_pay 		+= $totalamount;

				$json_encode_array["row"]       = $i;
				$json_encode_array["vno"] 		= $voucher;
				$json_encode_array["amt"]    	= $totalamount;
				$json_encode_array["bal"]   	= $balance;
				$json_encode_array['over']  	= $overpayment;
			
				$json_data[] 					= $json_encode_array;
			
				$json_encode 					= json_encode($json_data);

				$result_rvapp	= $this->receipt_voucher->getValue("rv_application", array("arvoucherno","SUM(convertedamount) AS amount", "SUM(discount) as discount", "SUM(overpayment) overpayment", "SUM(credits_used) credits_used"),"arvoucherno = '$voucher' AND stat IN('open','posted')", "", "", "arvoucherno");

				$appliedvoucher  = isset($result_rvapp[0]->arvoucherno) 	?	$result_rvapp[0]->arvoucherno	:  '';
				$appliedamount  = isset($result_rvapp[0]->amount) 			?	$result_rvapp[0]->amount		:	0;
				$applieddiscount= isset($result_rvapp[0]->discount)			?	$result_rvapp[0]->discount		:	0;
				$appliedover  	= isset($result_rvapp[0]->overpayment) 		?	$result_rvapp[0]->overpayment	:	0;

				// echo $appliedamount . "\n";

				$balance_2 	=	0;
				if (isset($amt_array[$voucher])) {
					$amount 	= isset($amt_array[$voucher]['amt']) ? $amt_array[$voucher]['amt'] : $totalamount;
					$balance_2 	= isset($amt_array[$voucher]['bal']) && $amt_array[$voucher]['bal'] != 0 ? $amt_array[$voucher]['bal'] : $totalamount;
					$discount	= isset($amt_array[$voucher]['dis']) ? $amt_array[$voucher]['dis'] : '0.00';
					$amount		= str_replace(',','',$amount);
					$balance_2	= str_replace(',','',$balance_2);
					// echo "Balance 2 = ".$balance_2;
					// echo "Amount 2 = ".$amount;
					// echo "Discount 2 = ".$discount;
					$balance_2	= ($balance_2 > 0) ? $balance_2 : $balance + $amount + $discount;
					$balance_2 	= $balance_2 - $amount - $discount;
					$balance_2 	= ($amount > $balance_2) ? 0 	:	$balance_2;
					// echo "Balance 2 = ".$balance_2;
					$balance 	= ($task == "edit") ? $appliedamount + $applieddiscount  : $balance;
					// $balance 	= ($balance - $appliedamount - $applieddiscount);
					// $balance_2 	= ($amount > 0) ? $balance - $amount - $discount : $balance;
				} else {
					$balance_2 	= ($task == "edit" && $balance == 0) ? ($balance + $appliedamount + $applieddiscount) : $balance;
				}
				
				$balance 		= ($task == "edit" && $balance == 0) ? ($balance + $appliedamount + $applieddiscount) : $balance;
	
				// echo $balance_2."\n\n";

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
				$table	.= 	'<td class="text-right over hidden" style="vertical-align:middle;" id = "overpayment_'.$voucher.'" '.$disable_onclick.' data-value="'.number_format($overpayment,2).'">'.number_format($overpayment,2).'</td>';
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
			}
		}
		$pagination->table = $table;
		$dataArray = array( "table" => $pagination->table, "json_encode" => $json_encode, "pagination" => $pagination->pagination, "page" => $pagination->page, "page_limit" => $pagination->page_limit );
		
		return $dataArray;
	}

	private function getrvdetails()
	{
		$checkrows       = $this->input->post("checkrows");
		$cheques       	 = $this->input->post("cheques");
		$overpayment 	 = $this->input->post("overpayment");
		$advance 		 = $this->input->post("advance");
		$over 			 = $this->input->post("over");

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

		$accountcode_ 	= 	"";
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
			$accountcode_ = $accountcode;
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

		$acc_entry_data     = array("id ind","CONCAT(segment5, ' - ', accountname) val");
		$acc_entry_cond     = "";
		$account_entry_list = $this->receipt_voucher->getValue("chartaccount", $acc_entry_data, $acc_entry_cond, "segment5");

		$dis_entry 			= $this->receipt_voucher->getValue("fintaxcode", array("salesAccount"), "fstaxcode = 'DC'");
		$discount_code 		= isset($dis_entry[0]->salesAccount) ? $dis_entry[0]->salesAccount	: "";

		$op_code 			= isset($overpaymentacct[0]->accountcode) ? $overpaymentacct[0]->accountcode : "";
		$ui 	            = $this->ui;
		$show_input         = $this->show_input;

		$totalcredit = 0;
		// var_dump($results);
		if(!empty($results)){
			$credit      = '0.00';
			$count       = count($results);
	
			for($i = 0; $i <= $count; $i++, $row++)
			{
				if($i==$count && $advance == 'no') {
					break;
				}
				$accountcode       = (!empty($results[$i]->accountcode))		? $results[$i]->accountcode 		: "";
				$detailparticulars = (!empty($results[$i]->detailparticulars)) 	? $results[$i]->detailparticulars 	: "";
				$ischeck 			= (!empty($results[$i]->ischeck)) 			? $results[$i]->ischeck 			: "no";
				$isop 				= (!empty($results[$i]->isop)) 			? $results[$i]->isop 			: "no";
				$isadv 				= (!empty($results[$i]->isadv)) 			? $results[$i]->isadv 			: "no";
				$isoverpayment 		= (!empty($results[$i]->is_overpayment)) 	? $results[$i]->is_overpayment 		: "no";
				$debit 				= (isset($results[$i]->chequeamount)) 		? $results[$i]->chequeamount 		: "0";

				if($isoverpayment == 'yes'){
					$credit 			= number_format($overpayment,2);
				} else {
					$credit 			= (isset($account_total[$accountcode])) ? $account_total[$accountcode] : 0;
					$credit 			= ($overpayment > 0 && $credit > 0) ? $credit - $overpayment 	:	$credit;
					$credit 			= number_format($credit,2);
				}
				$totalcredit     	+= $debit; 

				$table .= '<tr class="clone" valign="middle">';
				$table	.= '<td class = "remove-margin hidden">';
				$table	.=  $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName("taxcode[".$row."]")
								->setId("taxcode[".$row."]")
								->setClass('taxcode')
								->setValue("")
								->draw($show_input);
				$table	.= '</td>';
				$table	.= '<td class = "remove-margin hidden">';
				$table	.=  $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName("taxbase_amount[".$row."]")
								->setId("taxbase_amount[".$row."]")
								->setClass('taxbase_amount')
								->setValue("")
								->draw($show_input);
				$table	.= '</td>';
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
								<input type = "hidden" class="isop" value="'.$isop.'" name="isop['.$row.']" id="isop['.$row.']">
								<input type = "hidden" class="isadv" value="'.$isadv.'" name="isadv['.$row.']" id="isadv['.$row.']">
							</td>';
				$table  .=  '<td class = "remove-margin">'
								.$ui->formField('text')
									->setSplit('', 'col-md-12')
									->setName('debit['.$row.']')
									->setId('debit['.$row.']')
									->setClass('text-right debit account_amount')
									->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); compute_cash_amount(); addAmountAll('debit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);"))
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
		} else {
			$table	.= '<tr>';
			$table	.= 	'<td class="text-center" colspan="5">- No Records Found -</td>';
			$table	.= '</tr>';
		}


		$dataArray = array( "table" => $table, "totaldebit" => number_format($totalcredit, 2),"discount_code"=>$discount_code, "op_code"=>$op_code, "arv_acct"=>$accountcode_);
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

				$cv_checker 	= $this->receipt_voucher->checkifCVinuse($voucher);
				$cv_status 		= isset($cv_checker->status) 	?	$cv_checker->status 	:	""; 	

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

				$show_btn 		= ($status == 'open' && $restrict_rv && ($cv_status=="" || $cv_status != "used"));
				$show_edit 		= ($status == 'open' && $has_edit == 1 && $restrict_rv && ($cv_status=="" || $cv_status != "used"));
				$show_dlt 		= ($status == 'open' && $has_delete == 1 && $restrict_rv && ($cv_status=="" || $cv_status != "used"));
				$show_post 		= ($status == 'open' && $has_post == 1 && $restrict_rv && ($cv_status=="" || $cv_status != "used"));
				$show_unpost 	= ($status == 'posted' && $has_unpost == 1 && $restrict_rv && ($cv_status=="" || $cv_status != "used"));

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

	private function retrieve_advpayment_acct(){
		$advpaymentacct 	=	$this->receipt_voucher->retrieveADVPdetails();
		$advp_acct 			=	isset($advpaymentacct[0]->accountcode) 	?	$advpaymentacct[0]->accountcode 	:	"";
		$dataArray 			=	array("account"=>$advp_acct);
		return $dataArray;
	}

	public function get_account(){
		$tax_account = $this->input->post("tax_account");
		$tax_amount = $this->input->post("tax_amount");
		$result 	=  $this->receipt_voucher->getAccount($tax_account);
		$tax = $result[0]->tax_rate;
		$account = $result[0]->tax_account;
		$amount = ($tax_amount * $tax);
	
		$returnArray = array("amount" => $amount);
		return $returnArray;
	}

	// public function get_tax(){
	// 	$account = $this->input->post("account");
	// 	$result = $this->receipt_voucher->getValues("chartaccount",array("accountname"),"id = '$account' ");
	// 	$result_class = $result[0]->accountname;

	// 	$bus_type_data                = array("atcId ind", "CONCAT(atc_code ,' - ', short_desc) val");
	// 	$bus_type_cond                = "cwt = '$account' AND atc.stat = 'active'";
	// 	$join 						  =  "chartaccount ca ON atc.cwt = ca.id";
	// 	$tax_list  			 		  = $this->receipt_voucher->getTax("atccode atc", $bus_type_data,$join ,$bus_type_cond, false);

	// 	$ret = '';
	// 	foreach ($tax_list as $key) {
	// 		$in  = $key->ind;
	// 		$val = $key->val;
	// 		$ret .= "<option value=". $in.">" .$val. "</option>";
	// 	}
		
	// 	$returnArray = array( "result" => $result_class, "ret" => $ret);
	// 	return $returnArray;
	// }

	public function get_cwt(){
		$result = $this->receipt_voucher->getValues("chartaccount",array("id,accountname"),"accountname = 'Creditable Withholding Tax' ");
		$result_id = $result[0]->id;
		$result_name = $result[0]->accountname;
		
		$tax_account = $this->input->post("tax_account");
		$tax_amount = $this->input->post("tax_amount");
		$result1 	=  $this->receipt_voucher->getAccount($tax_account);
		$tax = $result1[0]->tax_rate;
		$account = $result1[0]->tax_account;
		$amount = ($tax_amount * $tax);
	
		$returnArray = array( "id" => $result_id, "accountname" => $result_name, "amount" => $amount);
		return $returnArray;
	}

	public function update_credit_account(){
		$credit_account 		=	$this->input->post('cred_account');
		$data['salesAccount'] 	=	$credit_account;
		$table 					= 	"fintaxcode";
		$cond 					=	"fstaxcode = 'ADV' AND stat = 'active'";
		$result = $this->receipt_voucher->editData($data, $table, $cond);

		$return = array( "result" => $result);
		return $return;
	}

	public function retrieve_existing_credacct(){
		$cred_acct				= $this->receipt_voucher->retrieve_existing_credacct();
		$existingcreditaccount	= isset($cred_acct[0]->account) ? $cred_acct[0]->account	:	"";
		$cred_id 				= isset($cred_acct[0]->id) ? $cred_acct[0]->id	:	"";

		$return = array('credit_id'=>$cred_id, "credit_account"=>$existingcreditaccount);
		return $return;
	}

	public function update_overpayment_account(){
		$credit_account 		=	$this->input->post('op_acct');
		$data['salesAccount'] 	=	$credit_account;
		$table 					= 	"fintaxcode";
		$cond 					=	"fstaxcode = 'OP' AND stat = 'active'";
		$result = $this->receipt_voucher->editData($data, $table, $cond);

		$return = array( "result" => $result);
		return $return;
	}

	public function retrieve_existing_opacct(){
		$op_acct			= $this->receipt_voucher->retrieve_existing_opacct();
		$existingopaccount	= isset($op_acct[0]->account) ? $op_acct[0]->account	:	"";
		$op_id 				= isset($op_acct[0]->id) ? $op_acct[0]->id	:	"";

		$return = array('op_id'=>$op_id, "op_account"=>$existingopaccount);
		return $return;
	}

	public function retrieve_accountclasscode(){
		$accountcode 		=	$this->input->post('accountcode');
		// checkifCash
		$ret_data 			=	$this->receipt_voucher->checkifCash($accountcode);
		// var_dump($ret_data);
		$accountclasscode	=	isset($ret_data[0]->accountclasscode) ? 	$ret_data[0]->accountclasscode 	:	"";

		return array("accountclasscode"=>$accountclasscode);
	}

	public function cancel_connected_entries(){
		$vouchers 		= $this->input->post('delete_id');
		
		$result 		= 0;
		foreach($vouchers as $key=>$voucherno){
			$details = $this->receipt_voucher->rvDetailsChecker($voucherno);
			
			$overpayment  	=	(isset($details->overpayment) && $details->overpayment == "yes") ? "yes" 	: 	"no";
			$advance 		= 	(isset($details->advancepayment) && $details->advancepayment == "yes" ) ? $details->advancepayment 	: "no";

			$count_applied 	= 	$this->receipt_voucher->checkExistingAppliedCreditVoucher($voucherno);

			if($advance == "yes" || $overpayment == "yes") {
				$result 		= 	$this->receipt_voucher->cancelCreditVoucher($voucherno);
			} else {
				if($count_applied->total > 0){
					$result 		= 	$this->receipt_voucher->cancelCreditVoucherApplied($voucherno);
				} else {
				  	$result 		= 	1;
				}
			}

			if($result){
				$code 	= 1; 
				$msg 	= "Successfully cancelled the voucher(s).";
			}else{
				$code 	= 0; 
				$msg 	= "Sorry, the system was unable to cancelled the voucher(s).";
			}

		}
		$dataArray = array("code" => $code,"msg"=> $msg );
		return $dataArray;
	}
}