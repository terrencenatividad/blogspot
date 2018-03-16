<?php
class controller extends wc_controller 
{

	public function __construct()
	{
		parent::__construct();
		$this->url 			    = new url();
		$this->receipt_voucher  = new receipt_voucher_model();
		$this->input            = new input();
		$this->ui 			    = new ui();
		$this->logs  			= new log;
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
		$applicableHeaderTable = "accountspayable";
		$applicationTable 	   = "pv_application";
		
		$invoice_data 	= (isset($check_rows) && (!empty($check_rows))) ? trim($check_rows) : "";
		$invoice_data  = str_replace('\\', '', $invoice_data);
		$decode_json   = json_decode($invoice_data, true);

		// var_dump($decode_json);

		if(!empty($decode_json))
		{
			for($i = 0; $i < count($decode_json); $i++)
			{
				$invoice = $decode_json[$i]["apvoucher"];
				$amount  = $decode_json[$i]["amount"];

				// accountspayable
				$invoice_amount				= $this->receipt_voucher->getValue($applicableHeaderTable, array("convertedamount"), "voucherno = '$invoice' AND stat = 'posted'");
				$applied_discount			= 0;

				// pv_application
				$applied_sum				= $this->receipt_voucher->getValue($applicationTable, array("SUM(convertedamount) AS convertedamount")," apvoucherno = '$invoice' AND stat = 'posted' ");

				// pv_application
				$applied_discount			= $this->receipt_voucher->getValue($applicationTable, array("SUM(discount) AS discount"), "apvoucherno = '$invoice' AND stat = 'posted' ");

				// pv_application
				$applied_forexamount		= $this->receipt_voucher->getValue($applicationTable, array("SUM(forexamount) AS forexamount"), "apvoucherno = '$invoice' AND stat = 'posted' ");

				$applied_sum				= $applied_sum[0]->convertedamount - $applied_forexamount[0]->forexamount;

				$invoice_amount				= (!empty($invoice_amount)) ? $invoice_amount[0]->convertedamount : 0;
				$applied_sum				= (!empty($applied_sum)) ? $applied_sum : 0;

				$invoice_balance			= $invoice_amount - $applied_sum - $applied_discount[0]->discount;

				$balance_info['amountpaid']	= $applied_sum + $applied_discount[0]->discount;
				// $balance_info['convertedamount']	= $applied_sum + $applied_discount[0]->discount;
				$balance_info['balance']	= $invoice_amount - $applied_sum - $applied_discount[0]->discount;

				// accountspayable
				$insertResult = $this->receipt_voucher->editData($balance_info, $applicableHeaderTable, "voucherno = '$invoice'");	
				if(!$insertResult)
					$errmsg["error"][] = "The system has encountered an error in updating Account Payable [$invoice]. Please contact admin to fix this issue.<br/>";
			}
		}
	}

	public function create()
	{	
		/**
		 * Method to lock screen when in use by other users
		 */
		$access	= $this->access->checkLockAccess('create');
		$cmp 	= $this->companycode;
		$seq 	= new seqcontrol();

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

		$data["ui"]                   = $this->ui;
		$data['show_input']           = $this->show_input;
		$data['button_name']          = "Save";
		$data["task"] 		          = "create";
		$data["ajax_post"] 	          = "";
		$data["row_ctr"] 			  = 0;
		$data["exchangerate"]         = "1.00";
		$data["row"] 			  		= 1;
		$data["transactiondate"]      = $this->date->dateFormat();

		// Retrieve customer list
		$data["customer_list"]          = $this->receipt_voucher->retrieveCustomerList();

		// Retrieve business type list
		$acc_entry_data               = array("id ind","CONCAT(segment5, ' - ', accountname) val");
		$acc_entry_cond               = "accounttype != 'P'";
		$data["account_entry_list"]   = $this->receipt_voucher->getValue("chartaccount", $acc_entry_data, $acc_entry_cond, "segment5");

		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->receipt_voucher->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

		// Retrieve generated ID
		// $gen_value                    = $this->receipt_voucher->getValue("paymentvoucher", "COUNT(*) as count", "voucherno != ''");	
		// $data["generated_id"]         = (!empty($gen_value[0]->count)) ? 'TMP_'.($gen_value[0]->count + 1) : 'TMP_1';
		$data["generated_id"]     = '';

		// Application Data
		$data['sum_applied'] 	= 0;
		$data['sum_discount']	= 0;
		$data['payments'] 		= "''";

		// Process form when form is submitted
		$data_validate = $this->input->post(array('referenceno', "h_voucher_no", "customer", "document_date", "h_save", "h_save_new", "h_save_preview", "h_check_rows_"));

		if (!empty($data_validate["customer"]) && !empty($data_validate["document_date"])) 
		{
			$errmsg = array();
			$temp 	= array();

			$voucherno = (isset($data_validate['h_voucher_no']) && (!empty($data_validate['h_voucher_no']))) ? htmlentities(trim($data_validate['h_voucher_no'])) : "";

			$isExist = $this->receipt_voucher->getValue("receiptvoucher", array("voucherno"), "voucherno = '$voucherno'");

			if($isExist[0]->voucherno)
			{
				/**UPDATE MAIN TABLES**/
				$generatedvoucher			= $seq->getValue('RV'); 
			
				$update_info				= array();
				$update_info['voucherno']	= $generatedvoucher;
				$update_info['stat']		= 'posted';
				$update_condition			= "voucherno = '$voucherno'";
				$updateTempRecord			= $this->receipt_voucher->editData($update_info,"receiptvoucher",$update_condition);
				$updateTempRecord			= $this->receipt_voucher->editData($update_info,"rv_details",$update_condition);
				$updateTempRecord			= $this->receipt_voucher->editData($update_info,"rv_application",$update_condition);
				$updateTempRecord			= $this->receipt_voucher->editData($update_info,"rv_cheques",$update_condition);

				/**UPDATE MAIN INVOICE**/
				$this->update_app($data_validate['selected_rows']);
			}
			
			if(empty($errmsg))
			{
				// For Admin Logs
				$this->logs->saveActivity("Add New Receipt Voucher [$generatedvoucher]");

				if(!empty($data_validate['h_save'])){
					$this->url->redirect(BASE_URL . 'financials/receipt_voucher');
				}else if(!empty($data_validate['h_save_preview'])){
					$this->url->redirect(BASE_URL . 'financials/receipt_voucher/view/' . $generatedvoucher);
				}else{
					$this->url->redirect(BASE_URL . 'financials/receipt_voucher/create');
				}
			}else{
				$data["errmsg"] = $errmsg;
			}
		}
		
		$this->view->load('receipt_voucher/receipt_voucher', $data);
	}

	public function view($sid)
	{
		$cmp 					   = $this->companycode;
	
		// Retrieve data
		$data         			   = $this->receipt_voucher->retrieveEditData($sid);

		$data["ui"]   			   = $this->ui;
		$data['show_input'] 	   = false;
		$data["button_name"] 	   = "Edit";
		$data["task"] 	  		   = "view";
		$data["sid"] 			   = $sid;
		$data["date"] 			   = date("M d, Y");

		$data["business_type_list"] = array();
		$data["account_entry_list"] = array();
		$data["customer_list"]    	= array();

		// Main
		$data["voucherno"]         = $data["main"]->voucherno;
		$data["vendorcode"]        = $data["main"]->customer;
		$data["v_convertedamount"] = $data["main"]->convertedamount;
		$data["exchangerate"]      = $data["main"]->exchangerate;
		$data["transactiondate"]   = $this->date->dateFormat($data["main"]->transactiondate);
		$data["referenceno"]       = $data["main"]->referenceno;
		$data["paymenttype"]       = $data["main"]->paymenttype;
		$data["particulars"]       = $data["main"]->particulars;

		// customer/Customer Details
		$data["v_vendor"] 		   = $data["cust"]->name;
		$data["v_email"] 		   = $data["cust"]->email;
		$data["tinno"] 		   	   = $data["cust"]->tinno;
		$data["address1"] 	       = $data["cust"]->address1;
		$data["terms"] 	   		   = $data["cust"]->terms;

		/**
		* Get the total forex amount applied
		*/
		$forex_result 			  = $this->receipt_voucher->getValue("pv_application", array("SUM(forexamount) as forexamount"), "apvoucherno = '$sid' AND stat = 'posted'");
		$forexamount			  = ($forex_result[0]->forexamount != '') ? $forex_result[0]->forexamount : 0;

		$data["forexamount"] 	  = $forexamount;

		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->receipt_voucher->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);
		$data["noCashAccounts"]  = false;
		
		if(empty($data["cash_account_list"]))
		{
			$data["noCashAccounts"]  = true;
		}

		$this->view->load('receipt_voucher/receipt_voucher', $data);
	}

	public function edit($sid)
	{
		$access				   	= $this->access->checkLockAccess('edit');
		$data         		   	= $this->receipt_voucher->retrieveEditData($sid);
		
		$data["ui"]            	= $this->ui;
		$data['show_input']    	= $this->show_input;
		$data["task"] 		   	= "edit";

		$data["generated_id"]  	= $sid;
		$data["sid"] 		   	= $sid;
		$data["date"] 		   	= date("M d, Y");

		// Retrieve customer list
		$data["customer_list"]          = $this->receipt_voucher->retrieveCustomerList();

		// Retrieve business type list
		$acc_entry_data               = array("id ind","CONCAT(segment5, ' - ', accountname) val");
		$acc_entry_cond               = "accounttype != 'P'";
		$data["account_entry_list"]   = $this->receipt_voucher->getValue("chartaccount", $acc_entry_data, $acc_entry_cond, "segment5");

		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->receipt_voucher->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

		// Header Data
		$data["voucherno"]       = $data["main"]->voucherno;
		$data["referenceno"]     = $data["main"]->referenceno;
		$data["vendorcode"]      = $data["main"]->customer;
		$data["exchangerate"]    = $data["main"]->exchangerate;
		$data["transactiondate"] = $this->date->dateFormat($data["main"]->transactiondate);
		$data["particulars"]     = $data["main"]->particulars;
		$data["paymenttype"]     = $data["main"]->paymenttype;
		
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

		// Process form when form is submitted
		$data_validate = $this->input->post(array('referenceno', "h_voucher_no", "customer", "document_date", "h_save", "h_save_new", "h_save_preview", "h_check_rows_"));

		if (!empty($data_validate["customer"]) && !empty($data_validate["document_date"])) 
		{
			$this->update_app($data_validate["h_check_rows_"]);

			// For Admin Logs
			$this->logs->saveActivity("Update Receipt Voucher [$sid]");

			if(!empty($data_validate['h_save']))
			{
				$this->url->redirect(BASE_URL . 'financials/receipt_voucher');
			}
			else if(!empty($data_validate['h_save_preview']))
			{
				$this->url->redirect(BASE_URL . 'financials/receipt_voucher/view/' . $sid);
			}
			else
			{
				$this->url->redirect(BASE_URL . 'financials/receipt_voucher/create');
			}	 
		}

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

		// Retrieve Cheque Details

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
			$cheque_join = "chartaccount chart ON pvc.chequeaccount = chart.id LEFT JOIN paymentvoucher pv ON pv.voucherno = pvc.voucherno" ;
			$cheque_group = "pvc.chequenumber";
			$chequeArray = $this->receipt_voucher->retrieveData($cheque_table, $cheque_fields, $cheque_cond, $cheque_join);
			$chequeArray_2 = $this->receipt_voucher->retrieveData($cheque_table, $cheque_fields, $cheque_cond, $cheque_join,"");
		}

		// Retrieve Applied Payment //
		$p_table = "rv_application pv";
		$p_fields = array("arvoucherno voucherno", "pv.amount amount", "ap.referenceno si_no", "pv.discount discount") ;
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
				->drawPDF('pv_voucher_' . $voucherno);
	}

	public function ajax($task)
	{
		header('Content-type: application/json');

		// if ($task == 'save_payable_data') 
		// {
		// 	$this->add();
		// }
		if ($task == 'update') 
		{
			$this->update();
		}
		else if ($task == 'delete_row') 
		{
			$this->delete_row();
		}
		else if ($task == 'load_list') 
		{
			$this->load_list();
		}
		else if ($task == 'export') 
		{
			$this->export2();
		}
		else if ($task == 'get_value') 
		{
			$this->get_value();
		}
		else if ($task == 'save_data') 
		{
			$this->save_data();
		}
		else if ($task == 'apply_payments') 
		{
			$this->apply_payments_();
		}
		else if ($task == 'delete_payments') 
		{
			$this->delete_payments();
		}
		else if ($task == 'load_payables') 
		{
			$this->load_payables();
		}
		else if ($task == 'apply_proforma') 
		{
			$this->apply_proforma();
		}
		else if($task == "load_ap")
		{
			$this->load_ap();
		}
		else if($task == "get_payments")
		{
			$this->get_payments();
		}
		else if($task == "getpvdetails")
		{
			$this->getpvdetails();
		}
		else if($task == "ajax_get_lock_access")
		{
			$result = $this->ajax_get_lock_access();
			echo json_encode($result);
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
				$apvoucherno 		= $row->apvoucherno;

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
									->setList(array("cash" => "Cash", "cheque" => "Cheque"))
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
								
				// $button = (strtolower($checkstat) != 'cleared') ? '<button class="btn btn-default btn-xs" onClick="editPaymentRow(event,\'edit'.$row_count.'\');" title="Edit Payment" ><span class="glyphicon glyphicon-pencil"></span></button>
				// <button class="btn btn-default btn-xs" onClick="deletePaymentRow(event,\'delete'.$row_count.'\');" title="Delete Payment" ><span class="glyphicon glyphicon-trash"></span></button>
				// <a role="button" class="btn btn-default btn-xs" href="'.BASE_URL.'financials/accounts_payable/print_preview/'.$sid.'" title="Print Payment Voucher" onClick = "print(\''.$paymentnumber.'\');"><span class="glyphicon glyphicon-print"></span></a>' : '<a role="button" class="btn btn-default btn-xs" href="'.BASE_URL.'financials/accounts_payable/print_preview/'.$sid.'" onClick = "print(\''.$paymentnumber.'\');" title="Print Payment Voucher" ><span class="glyphicon glyphicon-print"></span></a>';

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
		echo json_encode($dataArray);
	}

	private function load_ap()
	{
		$data_post = $this->input->post(array("searchkey", "sort", "sortBy", "customer"));

		$list   = $this->receipt_voucher->retrieveAPList($data_post);

		$table  = "";
		$j 		= 1;

		if( !empty($list) ) :
			for($i = 0; $i < count($list); $i++, $j++)
			{
				$date        = $list[$i]->transactiondate;
				$date        = date("M d, Y",strtotime($date));
				$voucher     = $list[$i]->voucherno; 
				$balance     = $list[$i]->balance; 
				$amount	  	 = $list[$i]->amount; 

				if($balance != $amount && $balance != 0)
				{
					$voucher_status = '<span class="label label-info">PARTIAL</span>';
				}
				else if($balance != 0)
				{
					$voucher_status = '<span class="label label-warning">UNPAID</span>';
				}
				else
				{
					$voucher_status = '<span class="label label-success">PAID&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
				}

				$table	.= '<tr class="list_row" style="cursor:pointer;" onClick="getAP(\''.$j.'\');">';
				$table	.= 	'<td class="text-left" style="vertical-align:middle;" >
								<p class="form-control-static" id="date'.$j.'">'.$date.'</p>
							</td>';
				$table	.= 	'<td class="text-left" style="vertical-align:middle;" >
								<p class="form-control-static" id="voucher'.$j.'">'.$voucher.'</p>
							</td>';
				$table	.= 	'<td class="text-right" style="vertical-align:middle;" >
								<p class="form-control-static" id="balance'.$j.'">'.number_format($balance,2).'</p>
							</td>';
				$table	.= 	'<td class="text-center" style="vertical-align:middle;" >
								<p class="form-control-static" id="status'.$j.'">'.$voucher_status.'</p>
							</td>';
				$table	.= '</tr>';
			}
		else:
			$table .= "<tr>
							<td colspan = '5' class = 'text-center'>No Records Found</td>
					  </tr>";
		endif;

		$dataArray = array( "transactions_list" => $table );
		echo json_encode($dataArray);
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
		echo json_encode($dataArray);
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
		echo json_encode($dataArray);
	}

	private function export()
	{
		$data_get = $this->input->get(array("daterangefilter", "vendfilter", "addCond", "search"));
		$data_get['daterangefilter'] = str_replace(array('%2F', '+'), array('/', ' '), $data_get['daterangefilter']);
		$result = $this->receipt_voucher->fileExportlist($data_get);
		$header = array("Document Date", "Voucher No", "customer", "Invoice No", "Amount", "Balance", "Notes"); 
		$csv 	= '';

		$filename = "export_receipt_voucher.csv";
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename='.$filename);
		$fp = fopen('php://output', 'w');
		fputcsv($fp, $header);

		$a 	= array();

		for($i = 0; $i < count($result); $i++)
		{
			$b = array(); 
			
			foreach($result[$i] as $fields)
			{
				$b[] = $fields;
			}
			$a[] = $b;
		}

		foreach ($a as $fields) 
		{
    		fputcsv($fp, $fields);
		}

		exit;
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

		echo json_encode($dataArray);
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
			* Save customer Detials
			*/
			$result = $this->receipt_voucher->saveDetails("partners", $data_post, "vendordetail");
		}

		if($result)
			$msg = "success";
		else
			$msg = "The system has encountered an error in saving. Please contact admin to fix this issue.";

		$dataArray = array( "msg" => $msg );
		echo json_encode($dataArray);
	
	}

	private function apply_payments_()
	{
		
		$data_post 	= $this->input->post();

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

		$dataArray = array("code" => $code, "voucher" => $voucher, "errmsg" => $errmsg);
		echo json_encode($dataArray);
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
		echo json_encode($dataArray);
	}

	private function load_payables()
	{
		$data       	= $this->input->post(array("customer", "voucherno"));
		$task       	= $this->input->post("task");
		$search			= $this->input->post('search');

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

				$appliedamount	= $this->receipt_voucher->getValue("pv_application", array("SUM(amount) AS amount"),"apvoucherno = '$voucher' AND stat IN('posted', 'temporary')");
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
				$table	.= 	'<td class="text-center" style="vertical-align:middle;">';
				$table	.= 		'<input type="checkbox" name="checkBox[]" id = "check'.$voucher.'" class = "icheckbox" toggleid="0" row="'.$voucher.'" '.$voucher_checked.'>'; 
				$table	.= 	'</td>';
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
						->draw(true).'</td>';
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
						->draw(true).'</td>';
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
									->draw(true).'</td>';
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
					->draw(true).'</td>';
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
		
		echo json_encode($dataArray);
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
			$accountcode = $this->receipt_voucher->getValue('ar_details apd LEFT JOIN chartaccount AS chart ON apd.accountcode = chart.id AND chart.companycode = apd.companycode','accountcode',"voucherno = '$apvoucherno' AND chart.accountclasscode = 'ACCREC'","","","apd.accountcode");
			$accountcode = $accountcode[0]->accountcode;
			if ( ! isset($account_amounts[$accountcode])) {
				$account_amounts[$accountcode] = 0;
			}
			$account_amounts[$accountcode] += str_replace(',', '', $decode_json[$i]["amt"]); 
			$apvoucher_[] = $apvoucherno;
			

		}
		$condi =  implode("','" , $apvoucher_);
		$cond = "('".$condi."')";

		$customer       	= $this->input->post("customer");
		$data["customer"] = $customer;
		$data["cond"]   = $cond;

		$results 		= $this->receipt_voucher->retrievePVDetails($data);
		$table 			= "";
		$row 			= 1;

		// Retrieve business type list
		$acc_entry_data     = array("id ind","accountname val");
		$acc_entry_cond     = "accounttype != 'P'";
		$account_entry_list = $this->receipt_voucher->getValue("chartaccount", $acc_entry_data, $acc_entry_cond, "segment5");

		$ui 	            = $this->ui;
		$show_input         = $this->show_input;

		$totalcredit = 0;
		
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
				$credit = (isset($account_amounts[$accountcode])) ? $account_amounts[$accountcode] : 0;
				$credit = number_format($credit,2);
				$totalcredit    	   += $debit;

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
							'</td>';
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
									->setClass('debit account_amount')
									->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('debit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);"))
									->setValue(number_format($debit,2))
									->draw($show_input).			
								'</td>';
				$table 	.= '<td class = "remove-margin">'
								.$ui->formField('text')
									->setSplit('', 'col-md-12')
									->setName('credit['.$row.']')
									->setClass("text-right  credit")
									->setId('credit['.$row.']')
									->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('credit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);", "" => ""))
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

		$dataArray = array( "table" => $table, "totaldebit" => number_format($totalcredit, 2) );
		echo json_encode($dataArray);

	}

	private function load_list()
	{
		$data_post = $this->input->post(array("daterangefilter", "custfilter", "addCond", "search", "sort"));

		$list   = $this->receipt_voucher->retrieveList($data_post);
		
		$table  = "";

		if( !empty($list->result) ) :
			foreach($list->result as $key => $row)
			{
				$date        = (($row->pvtransdate == '0000-00-00' || is_null)) ? $row->transactiondate : $row->pvtransdate;
				$date        = $this->date->dateFormat($date);
				$apvoucher   = $row->voucherno; 
				$balance     = $row->balance; 
				$amount	  	 = $row->amount; 
				$customer		 = $row->partnername; 
				$referenceno = $row->referenceno; 
				$pvvoucher   = $row->pv_voucherno; 

				if($balance != $amount && $balance != 0)
				{
					$voucher_status = '<span class="label label-info">PARTIAL</span>';

					$viewlink		= BASE_URL . "financials/receipt_voucher/view/$pvvoucher";
					$editlink		= BASE_URL . "financials/receipt_voucher/edit/$pvvoucher";
					$voucherlink	= MODULE_URL . "print_preview/$pvvoucher";
					$paymentlink	= BASE_URL . "financials/receipt_voucher/view/$pvvoucher#payment";

				}
				else if($balance != 0)
				{
					$voucher_status = '<span class="label label-warning">UNPAID</span>';

					$viewlink		= BASE_URL . "financials/accounts_payable/view/$apvoucher";
					$editlink		= BASE_URL . "financials/accounts_payable/edit/$apvoucher";
					$voucherlink	= BASE_URL . "financials/accounts_payable/print_preview/$apvoucher";
					$paymentlink	= BASE_URL . "financials/accounts_payable/view/$apvoucher#payment";
				}
				else
				{
					$voucher_status = '<span class="label label-success">PAID&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';

					$viewlink		= BASE_URL . "financials/receipt_voucher/view/$pvvoucher";
					$editlink		= BASE_URL . "financials/receipt_voucher/edit/$pvvoucher";
					$voucherlink	= MODULE_URL . "print_preview/$pvvoucher";
					$paymentlink	= BASE_URL . "financials/receipt_voucher/view/$pvvoucher#payment";

				}
				
				$task		= '<div class="btn-group task_buttons" name="task_buttons">
								<a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
								<ul class="dropdown-menu left">
									<li>
										<a class="btn-sm" href="'.$viewlink.'">
											<span class="glyphicon glyphicon-eye-open"></span> View
										</a>
									</li>';
		
				if($balance == $amount){
				$task		.= '<li><a class="btn-sm" href="'.$editlink.'"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>';
				}
				$task		.= '<li><a class="btn-sm" href="'.$voucherlink.'" target="_blank"><span class="glyphicon glyphicon-print"></span> Print Voucher</a></li>';

				if($balance != 0)
				{
					$task		.= '<li class="divider"></li><li><a class="btn-sm record-delete" href = "#deleteModalAP" data-toggle="modal" onClick="$(\'#deleteModalAP .modal-body #recordId\').val(\''.$apvoucher.'\');" data-id="'.$apvoucher.'"><span class="glyphicon glyphicon-trash"></span> Delete</a></li>';
				}
				else
				{
					$task		.= '<li class="divider"></li><li><a class="btn-sm record-delete" href="#deleteModal" data-toggle="modal" onClick="$(\'#deleteModal .modal-body #recordId\').val(\''.$apvoucher.'\');" data-id="'.$apvoucher.'"><span class="glyphicon glyphicon-trash"></span> Delete</a></li>';
				}
				
				$task		.= '</ul>
							</div>';

				$table	.= '<tr id="'.$viewlink.'" class="list_row">';
				$table	.= '<td class="text-center" style="vertical-align:middle;">'.$task.'</td>';
				$table	.= '<td  style="vertical-align:middle;">'.$date.'</td>';
				// $table	.= '<td class="text-left" style="vertical-align:middle;">&nbsp;'.$apvoucher.'</td>';
				$table	.= '<td  style="vertical-align:middle;">&nbsp;'.$pvvoucher.'</td>';
				$table	.= '<td  style="vertical-align:middle;">&nbsp;'.$customer.'</td>';
				// $table	.= '<td class="text-left" style="vertical-align:middle;">&nbsp;'.$referenceno.'</td>';
				$table	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;'.number_format($amount,2).'</td>';
				// $table	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;'.number_format($balance,2).'</td>';
				$table	.= '<td class="text-center" style="vertical-align:middle;">&nbsp;'.$voucher_status.'</td>';
				$table	.= '</tr>';
			}
		else:
			$table .= "<tr>
							<td colspan = '8' class = 'text-center'>No Records Found</td>
					  </tr>";
		endif;

		$dataArray = array( "list" => $table, "pagination" => $list->pagination , "csv" => $this->export2() );
		echo json_encode($dataArray);
	}

	private function export2(){
		$data_get = $this->input->post(array("daterangefilter", "vendfilter", "addCond", "search"));
		$data_get['daterangefilter'] = str_replace(array('%2F', '+'), array('/', ' '), $data_get['daterangefilter']);
		$result2 = $this->receipt_voucher->fileExportlist($data_get);
		$header = array("Voucher Date","Voucher No","Customer","Amount","Status");
		
		$csv = '';
		$csv = '"' . implode('","', $header) . '"';
		$csv .= "\n";

		if (!empty($result2)){
			foreach ($result2 as $key => $row){
				$date        = (($row->pvtransdate == '0000-00-00' || is_null)) ? $row->transactiondate : $row->pvtransdate;
				$date        = $this->date->dateFormat($date);
				$apvoucher   = $row->voucherno; 
				$balance     = $row->balance; 
				$amount	  	 = $row->amount; 
				$customer		 = $row->partnername; 
				$referenceno = $row->referenceno; 
				$pvvoucher   = $row->pv_voucherno; 

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
				
				$csv .= '"' . $date . '",';
				$csv .= '"' . $pvvoucher . '",';
				$csv .= '"' . $customer . '",';
				$csv .= '"' . $amount . '",';
				$csv .= '"' . $voucher_status . '"';
				$csv .= "\n";
			}
		}
		return $csv;
	}
	
}