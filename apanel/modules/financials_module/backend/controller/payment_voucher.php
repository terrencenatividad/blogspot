<?php
class controller extends wc_controller 
{

	public function __construct()
	{
		parent::__construct();
		$this->url 			    = new url();
		$this->payment_voucher  = new payment_voucher_model();
		$this->input            = new input();
		$this->ui 			    = new ui();
		$this->logs  			= new log;
		$this->view->title      = 'Payment Voucher';
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

		// Retrieve vendor list
		$data["vendor_list"]  = $this->payment_voucher->retrieveVendorList();

		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->payment_voucher->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);


		$this->view->load('payment_voucher/payment_voucher_list', $data);
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
				$invoice_amount				= $this->payment_voucher->getValue($applicableHeaderTable, array("convertedamount"), "voucherno = '$invoice' AND stat = 'posted'");
				$applied_discount			= 0;

				// pv_application
				$applied_sum				= $this->payment_voucher->getValue($applicationTable, array("SUM(convertedamount) AS convertedamount")," apvoucherno = '$invoice' AND stat = 'posted' ");

				// pv_application
				$applied_discount			= $this->payment_voucher->getValue($applicationTable, array("SUM(discount) AS discount"), "apvoucherno = '$invoice' AND stat = 'posted' ");

				// pv_application
				$applied_forexamount		= $this->payment_voucher->getValue($applicationTable, array("SUM(forexamount) AS forexamount"), "apvoucherno = '$invoice' AND stat = 'posted' ");

				$applied_sum				= $applied_sum[0]->convertedamount - $applied_forexamount[0]->forexamount;

				$invoice_amount				= (!empty($invoice_amount)) ? $invoice_amount[0]->convertedamount : 0;
				$applied_sum				= (!empty($applied_sum)) ? $applied_sum : 0;

				$invoice_balance			= $invoice_amount - $applied_sum - $applied_discount[0]->discount;

				$balance_info['amountpaid']	= $applied_sum + $applied_discount[0]->discount;
				// $balance_info['convertedamount']	= $applied_sum + $applied_discount[0]->discount;
				$balance_info['balance']	= $invoice_amount - $applied_sum - $applied_discount[0]->discount;

				// accountspayable
				$insertResult = $this->payment_voucher->editData($balance_info, $applicableHeaderTable, "voucherno = '$invoice'");	
				if(!$insertResult)
					$errmsg["error"][] = "The system has encountered an error in updating Account Payable [$invoice]. Please contact admin to fix this issue.<br/>";
			}
		}
	}

	public function create()
	{	
		$cmp = $this->companycode;
		$seq = new seqcontrol();

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
		$data["transactiondate"]      = $this->date->dateFormat();

		// Retrieve vendor list
		$data["vendor_list"]          = $this->payment_voucher->retrieveVendorList();

		// Retrieve business type list
		$bus_type_data                = array("code ind", "value val");
		$bus_type_cond                = "type = 'businesstype'";
		$data["business_type_list"]   = $this->payment_voucher->getValue("wc_option", $bus_type_data, $bus_type_cond, false);

		// Retrieve business type list
		$acc_entry_data               = array("id ind","accountname val");
		$acc_entry_cond               = "accounttype != 'P'";
		$data["account_entry_list"]   = $this->payment_voucher->getValue("chartaccount", $acc_entry_data, $acc_entry_cond, "segment5");

		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->payment_voucher->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

		// Retrieve generated ID
		$gen_value                    = $this->payment_voucher->getValue("paymentvoucher", "COUNT(*) as count", "voucherno != ''");	
		$data["generated_id"]         = (!empty($gen_value[0]->count)) ? 'TMP_'.($gen_value[0]->count + 1) : 'TMP_1';

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
				$generatedvoucher			= $seq->getValue('PV'); 
			
				$update_info				= array();
				$update_info['voucherno']	= $generatedvoucher;
				$update_info['stat']		= 'posted';
				$update_condition			= "voucherno = '$voucherno'";
				$updateTempRecord			= $this->payment_voucher->editData($update_info,"paymentvoucher",$update_condition);
				$updateTempRecord			= $this->payment_voucher->editData($update_info,"pv_details",$update_condition);
				$updateTempRecord			= $this->payment_voucher->editData($update_info,"pv_application",$update_condition);
				$updateTempRecord			= $this->payment_voucher->editData($update_info,"pv_cheques",$update_condition);

				/**UPDATE MAIN INVOICE**/
				$this->update_app($data_validate['h_check_rows_']);
			}
			
			if(empty($errmsg))
			{
				// For Admin Logs
				$this->logs->saveActivity("Add New Payment Voucher [$generatedvoucher]");

				if(!empty($data_validate['h_save']))
				{
					$this->url->redirect(BASE_URL . 'financials/payment_voucher');
				}
				else if(!empty($data_validate['h_save_preview']))
				{
					$this->url->redirect(BASE_URL . 'financials/payment_voucher/view/' . $generatedvoucher);
				}
				else
				{
					$this->url->redirect(BASE_URL . 'financials/payment_voucher/create');
				}
			}
			else
				$data["errmsg"] = $errmsg;
			
				
		}
		
		$this->view->load('payment_voucher/payment_voucher', $data);
	}

	public function view($sid)
	{
		$cmp 					   = $this->companycode;
	
		// Retrieve data
		$data         			   = $this->payment_voucher->retrieveEditData($sid);

		$data["ui"]   			   = $this->ui;
		$data['show_input'] 	   = false;
		$data["button_name"] 	   = "Edit";
		$data["task"] 	  		   = "view";
		$data["sid"] 			   = $sid;
		$data["date"] 			   = date("M d, Y");

		$data["business_type_list"] = array();
		$data["account_entry_list"] = array();
		$data["vendor_list"]    	= array();

		// Main
		$data["voucherno"]         = $data["main"]->voucherno;
		$data["vendorcode"]        = $data["main"]->vendor;
		$data["v_convertedamount"] = $data["main"]->convertedamount;
		$data["exchangerate"]      = $data["main"]->exchangerate;
		$data["transactiondate"]   = $this->date->dateFormat($data["main"]->transactiondate);
		$data["referenceno"]       = $data["main"]->referenceno;
		$data["paymenttype"]       = $data["main"]->paymenttype;
		$data["particulars"]       = $data["main"]->particulars;

		// Vendor/Customer Details
		$data["v_vendor"] 		   = $data["cust"]->name;
		$data["v_email"] 		   = $data["cust"]->email;
		$data["tinno"] 		   	   = $data["cust"]->tinno;
		$data["address1"] 	       = $data["cust"]->address1;
		$data["terms"] 	   		   = $data["cust"]->terms;

		/**
		* Get the total forex amount applied
		*/
		$forex_result 			  = $this->payment_voucher->getValue("pv_application", array("SUM(forexamount) as forexamount"), "apvoucherno = '$sid' AND stat = 'posted'");
		$forexamount			  = ($forex_result[0]->forexamount != '') ? $forex_result[0]->forexamount : 0;

		$data["forexamount"] 	  = $forexamount;

		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->payment_voucher->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);
		$data["noCashAccounts"]  = false;
		
		if(empty($data["cash_account_list"]))
		{
			$data["noCashAccounts"]  = true;
		}

		// var_dump($data["payments"]);

		$this->view->load('payment_voucher/payment_voucher', $data);
	}

	public function edit($sid)
	{
		$data         		   = $this->payment_voucher->retrieveEditData($sid);

		$data["ui"]            = $this->ui;
		$data['show_input']    = $this->show_input;
		$data["task"] 		   = "edit";

		$data["generated_id"]  = $sid;
		$data["sid"] 		   = $sid;
		$data["date"] 		   = date("M d, Y");

		// Retrieve vendor list
		$data["vendor_list"]          = $this->payment_voucher->retrieveVendorList();

		// Retrieve business type list
		$bus_type_data                = array("code ind", "value val");
		$bus_type_cond                = "type = 'businesstype'";
		$data["business_type_list"]   = $this->payment_voucher->getValue("wc_option", $bus_type_data, $bus_type_cond, false);

		// Retrieve business type list
		$acc_entry_data               = array("id ind","accountname val");
		$acc_entry_cond               = "accounttype != 'P'";
		$data["account_entry_list"]   = $this->payment_voucher->getValue("chartaccount", $acc_entry_data, $acc_entry_cond, "segment5");

		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->payment_voucher->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

		// Header Data
		$data["voucherno"]       = $data["main"]->voucherno;
		$data["referenceno"]     = $data["main"]->referenceno;
		$data["vendorcode"]      = $data["main"]->vendor;
		$data["exchangerate"]    = $data["main"]->exchangerate;
		$data["transactiondate"] = $this->date->dateFormat($data["main"]->transactiondate);
		$data["particulars"]     = $data["main"]->particulars;
		$data["paymenttype"]     = $data["main"]->paymenttype;

		$data["terms"] 		 = (!empty($data["cust"]->terms)) ? $data["cust"]->terms : "";
		$data["tinno"] 		 = (!empty($data["cust"]->tinno)) ? $data["cust"]->tinno : "";
		$data["address1"] 	 = (!empty($data["cust"]->address1)) ? $data["cust"]->address1 : "";
		
		// Process form when form is submitted
		$data_validate = $this->input->post(array('referenceno', "h_voucher_no", "vendor", "document_date", "h_save", "h_save_new", "h_save_preview", "h_check_rows_"));

		if (!empty($data_validate["vendor"]) && !empty($data_validate["document_date"])) 
		{
			$this->update_app($data_validate["h_check_rows_"]);

			// For Admin Logs
			$this->logs->saveActivity("Update Payment Voucher [$sid]");

			if(!empty($data_validate['h_save']))
			{
				$this->url->redirect(BASE_URL . 'financials/payment_voucher');
			}
			else if(!empty($data_validate['h_save_preview']))
			{
				$this->url->redirect(BASE_URL . 'financials/payment_voucher/view/' . $sid);
			}
			else
			{
				$this->url->redirect(BASE_URL . 'financials/payment_voucher/create');
			}	 
		}

		$this->view->load('payment_voucher/payment_voucher', $data);
	}

	public function print_preview($voucherno) 
	{
		// Retrieve Document Info
		$sub_select = $this->payment_voucher->retrieveData("pv_application", array("SUM(amount) AS amount"), "voucherno = '$voucherno'");

		$sub_select[0]->amount;
		
		$docinfo_table  = "paymentvoucher as pv";
		$docinfo_fields = array('pv.transactiondate AS documentdate','pv.voucherno AS voucherno',"CONCAT( first_name, ' ', last_name )","'{$sub_select[0]->amount}' AS amount",'pv.amount AS pvamount', "'' AS referenceno", "particulars AS remarks", "p.partnername AS vendor");
		$docinfo_join   = "partners as p ON p.partnercode = pv.vendor AND p.companycode = pv.companycode";
		$docinfo_cond 	= "pv.voucherno = '$voucherno'";

		$documentinfo  	= $this->payment_voucher->retrieveData($docinfo_table, $docinfo_fields, $docinfo_cond, $docinfo_join);

		$vendor 	    = $documentinfo[0]->vendor;

		// Retrieve Document Details
		$docdet_table   = "pv_details as dtl";
		$docdet_fields  = array("chart.segment5 as accountcode", "chart.accountname as accountname", "SUM(dtl.debit) as debit","SUM(dtl.credit) as credit");
		$docdet_join    = "chartaccount as chart ON chart.id = dtl.accountcode AND chart.companycode = dtl.companycode";
		$docdet_cond    = "dtl.voucherno = '$voucherno'";
		$docdet_groupby = "dtl.accountcode";
		$docdet_orderby = "CASE WHEN dtl.debit > 0 THEN 1 ELSE 2 END, dtl.linenum";
		
		$documentdetails = $this->payment_voucher->retrieveData($docdet_table, $docdet_fields, $docdet_cond, $docdet_join, $docdet_orderby, $docdet_groupby);

		// Retrieve Payment Details
		$paymentArray	 = $this->payment_voucher->retrievePaymentDetails($voucherno);

		// Retrieve Cheque Details
		$pv_v 		  = "";
		$pv_voucherno = $this->payment_voucher->getValue("pv_application", array("voucherno"), "voucherno = '$voucherno'");
		
		$chequeArray = "";
		if(!empty($pv_voucherno))
		{
			for($p = 0; $p < count($pv_voucherno); $p++)
			{
				$pv_v .= "'".$pv_voucherno[$p]->voucherno."',";
			}
		
			$pv_v = rtrim($pv_v, ", ");
			
			$cheque_table = "pv_cheques pvc";
			$cheque_fields = array("pvc.chequeaccount", "chart.accountname AS accountname", "pvc.chequenumber AS chequenumber", "pvc.chequedate AS chequedate", "pvc.chequeamount AS chequeamount");
			$cheque_cond = "pvc.voucherno IN($pv_v)";
			$cheque_join = "chartaccount chart ON pvc.chequeaccount = chart.id";
			$chequeArray = $this->payment_voucher->retrieveData($cheque_table, $cheque_fields, $cheque_cond, $cheque_join);
		}
		
		// Setting for PDFs
		$print = new print_voucher_model('P', 'mm', 'Letter');
		$print->setDocumentType('Payment Voucher')
				->setDocumentInfo($documentinfo[0])
				->setVendor($vendor)
				->setPayments($paymentArray)
				->setDocumentDetails($documentdetails)
				->setCheque($chequeArray)
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
			$this->export();
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
		$data_post = $this->input->post(array("searchkey", "sort", "sortBy", "vendor"));

		$list   = $this->payment_voucher->retrieveAPList($data_post);

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
		$result = $this->payment_voucher->editData($data["fields"], $data["table"], $data["condition"]);

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
		$result = $this->payment_voucher->deleteData($data_post);

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
		$result = $this->payment_voucher->fileExport($data_get);
		$header = array("Document Date", "Voucher No", "Vendor", "Invoice No", "Amount", "Balance", "Notes"); 
		$csv 	= '';

		$filename = "export_payment_voucher.csv";
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
			* Save Vendor Detials
			*/
			$result = $this->payment_voucher->saveDetails("partners", $data_post, "vendordetail");
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
		$success   = false;
		$msg 	   = "";
		
		$data_post = $this->input->post();

		$result    = array_filter($this->payment_voucher->applyPayments_($data_post));

		if(!empty($result))
		{
			$success = false;
			$msg 	 = $result;
		}
		else
		{
			$success = true;
		}

		$dataArray = array("msg" => $msg, "success" => $success);
		echo json_encode($dataArray);
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
		echo json_encode($dataArray);
	}

	private function load_payables()
	{
		$data       = $this->input->post(array("vendor", "voucherno"));
		$task       = $this->input->post("task");

		$result     = $this->payment_voucher->retrieveAPList($data);

		$table             = "";
		$j 	               = 1;
		$json_encode_array = array();
		$json_data         = array();
		$total_pay         = 0;
		$json_encode       = "";
		$edited_amount 	   = 0;

		// var_dump($result);

		if(!empty($result["result"][0]->voucherno))
		{
			// echo "inside";
			for($i = 0; $i < count($result["result"]); $i++, $j++)
			{
				$date			= $result["result"][$i]->transactiondate;
				$date			= $this->date->dateFormat($date);
				$voucher		= $result["result"][$i]->voucherno;
				$vendor			= $result["result"][$i]->vendor_name;
				$balance		= $result["result"][$i]->balance; 
				$totalamount	= $result["result"][$i]->amount;
				$referenceno	= $result["result"][$i]->referenceno;

				$total_pay 		+= $totalamount;

				$json_encode_array["row"]       = $i;
				$json_encode_array["apvoucher"] = $voucher;
				$json_encode_array["amount"]    = $totalamount;
				$json_data[] 					= $json_encode_array;

				$json_encode 					= json_encode($json_data);

				$appliedamount	= $this->payment_voucher->getValue("pv_application", array("SUM(amount) AS amount"),"apvoucherno = '$voucher' AND stat IN('posted', 'temporary')");
				$appliedamount  = $appliedamount[0]->amount;

				$table	.= '<tr>'; 
				// $table	.= 	'<td class="text-center" style="vertical-align:middle;">';
				// $table	.= 		'<input type="checkbox" name="checkBox[]" id = "row_check'.$i.'" class = "icheckbox" row = '.$i.' toggleid = "0" onClick="selectPayable('.$i.',0);">'; 
				// $table	.= 		'<input type="hidden" id="invoice['.$i.']" name = "invoice_modal['.$j.']" >';
				// $table	.= 	'</td>';
				// $table	.= 	'<td class="text-left" style="vertical-align:middle;" id = "date_modal'.$i.'" onClick="selectPayable('.$i.',1);">'.$date.'</td>';
				
				// $table	.= '<td class="text-left" style="vertical-align:middle;" id = "apvoucher_modal'.$i.'" onClick="selectPayable('.$i.',1);">'.$voucher.'</td>';
				// $table	.= '<td class="text-left" style="vertical-align:middle;" id = "reference_modal'.$i.'" onClick="selectPayable('.$i.',1);">'.$referenceno.'</td>';
				// $table	.= '<td class="text-right" style="vertical-align:middle;" id = "totalamount_modal'.$i.'" onClick="selectPayable('.$i.',1);">'.number_format($totalamount,2).'
				// 			<input type = "hidden" name = "totalamountval['.$j.']" id = "totalamountval['.$j.']" value = "'.number_format($totalamount, 2).'"/>
				// 			</td>';
				// $table	.= '<td class="text-right" style="vertical-align:middle;" id = "balance_modal'.$i.'" onClick="selectPayable('.$i.',1);">'.number_format($balance,2).'</td>';
				// $table	.= '<td class="text-right pay" style="vertical-align:middle;">'
				// 				.$this->ui->formField('text')
				// 							->setSplit('', 'col-md-12')
				// 							->setClass("input-sm text-right paymentamount")
				// 							->setId('paymentamount'.$voucher)
				// 							->setPlaceHolder("0.00")
				// 							->setAttribute(array("maxlength" => "50", "disabled" => "disabled", "onBlur" => 'checkBalance(this.value,'.$i.'); formatNumber(this.id);', "onClick" => "SelectAll(this.id);"))
				// 							->setValue(number_format($appliedamount, 2))
				// 							->draw(true).
				// 				'<input type = "hidden" name = "pay_amount['.$j.']" id = "pay_amount['.$j.']" value = "'.number_format($appliedamount, 2).'"/>
				// 			</td>';
				
				$table	.= 	'<td class="text-center" style="vertical-align:middle;">';
				$table	.= 		'<input type="checkbox" name="checkBox[]" id = "check'.$voucher.'" class = "icheckbox" toggleid="0" row="'.$voucher.'">'; 
				$table	.= 	'</td>';
				$table	.= 	'<td class="text-left" style="vertical-align:middle;" onClick="selectPayable(\''.$voucher.'\',1);">'.$date.'</td>';
				$table	.= 	'<td class="text-left" style="vertical-align:middle;" onClick="selectPayable(\''.$voucher.'\',1);">'.$voucher.'</td>';
				$table	.= 	'<td class="text-left" style="vertical-align:middle;" onClick="selectPayable(\''.$voucher.'\',1);">'.$referenceno.'</td>';
				$table	.= 	'<td class="text-right" style="vertical-align:middle;" id = "payable_amount'.$voucher.'" onClick="selectPayable(\''.$voucher.'\',1);">'.number_format($totalamount,2).'</td>';
				$table	.= 	'<td class="text-right" style="vertical-align:middle;" id = "payable_balance'.$voucher.'" onClick="selectPayable(\''.$voucher.'\',1);">'.number_format($balance,2).'</td>';
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
									->setValue(number_format($appliedamount, 2))
									->draw(true).'</td>';
				
				$table	.= '</tr>';
			}
		}
		else
		{
			// echo "else";
			$table	.= '<tr>';
			$table	.= 	'<td class="text-center" colspan="6">- No Records Found -</td>';
			$table	.= '</tr>';
		}

		$dataArray = array( "table" => $table, "json_encode" => $json_encode);
		echo json_encode($dataArray);
	}

	private function getpvdetails()
	{
		$checkrows       = $this->input->post("checkrows");

		$invoice_data 	= (isset($checkrows) && (!empty($checkrows))) ? trim($checkrows) : "";
		$invoice_data  	= str_replace('\\', '', $invoice_data);
		$decode_json    = json_decode($invoice_data, true);
		$cond 			= "IN(";

		for($i = 0; $i < count($decode_json); $i++)
		{
			$apvoucherno = $decode_json[$i]["vno"];
			$cond 		.= "'".$apvoucherno."',";
		}

		$cond 			= substr($cond, 0, -1);
		$cond 			.= ")";

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
				$debit         	   = number_format($results[$i]->sumcredit, 2);

				$totaldebit    	   += $credit;

				$table .= '<tr class="clone" valign="middle">';
				$table .= 	'<td class = "remove-margin">'	
									.$ui->formField('dropdown')
										->setPlaceholder('Select One')
										->setSplit('', 'col-md-12')
										->setName("accountcode[".$row."]")
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
									->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('debit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);"))
									->setValue($debit)
									->draw($show_input).			
								'</td>';
				$table 	.= '<td class = "remove-margin">'
								.$ui->formField('text')
									->setSplit('', 'col-md-12')
									->setName('credit['.$row.']')
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

		$dataArray = array( "table" => $table, "totaldebit" => number_format($totaldebit, 2) );
		echo json_encode($dataArray);

	}

	private function load_list()
	{
		$data_post = $this->input->post(array("daterangefilter", "vendfilter", "addCond", "search", "sort"));

		$list   = $this->payment_voucher->retrieveList($data_post);
		
		$table  = "";

		if( !empty($list->result) ) :
			foreach($list->result as $key => $row)
			{
				$date        = (is_null($row->pvtransdate)) ? $row->transactiondate : $row->pvtransdate;
				$date        = $this->date->dateFormat($date);
				$apvoucher   = $row->voucherno; 
				$balance     = $row->balance; 
				$amount	  	 = $row->amount; 
				$vendor		 = $row->partnername; 
				$referenceno = $row->referenceno; 
				$pvvoucher   = $row->pv_voucherno; 

				if($balance != $amount && $balance != 0)
				{
					$voucher_status = '<span class="label label-info">PARTIAL</span>';

					$viewlink		= BASE_URL . "financials/payment_voucher/view/$pvvoucher";
					$editlink		= BASE_URL . "financials/payment_voucher/edit/$pvvoucher";
					$voucherlink	= MODULE_URL . "print_preview/$pvvoucher";
					$paymentlink	= BASE_URL . "financials/payment_voucher/view/$pvvoucher#payment";

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

					$viewlink		= BASE_URL . "financials/payment_voucher/view/$pvvoucher";
					$editlink		= BASE_URL . "financials/payment_voucher/edit/$pvvoucher";
					$voucherlink	= MODULE_URL . "print_preview/$pvvoucher";
					$paymentlink	= BASE_URL . "financials/payment_voucher/view/$pvvoucher#payment";

				}
				
				$task		= '<div class="btn-group task_buttons" name="task_buttons">
								<a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
								<ul class="dropdown-menu left">
									<li>
										<a class="btn-sm" href="'.$viewlink.'">
											<span class="glyphicon glyphicon-eye-open"></span> View
										</a>
									</li>';
		
				
				$task		.= '<li><a class="btn-sm" href="'.$editlink.'"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>';

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
				$table	.= '<td class="text-center" style="vertical-align:middle;">'.$date.'</td>';
				// $table	.= '<td class="text-left" style="vertical-align:middle;">&nbsp;'.$apvoucher.'</td>';
				$table	.= '<td class="text-left" style="vertical-align:middle;">&nbsp;'.$pvvoucher.'</td>';
				$table	.= '<td class="text-left" style="vertical-align:middle;">&nbsp;'.$vendor.'</td>';
				$table	.= '<td class="text-left" style="vertical-align:middle;">&nbsp;'.$referenceno.'</td>';
				$table	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;'.number_format($amount,2).'</td>';
				$table	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;'.number_format($balance,2).'</td>';
				$table	.= '<td class="text-center" style="vertical-align:middle;">&nbsp;'.$voucher_status.'</td>';
				$table	.= '</tr>';
			}
		else:
			$table .= "<tr>
							<td colspan = '8' class = 'text-center'>No Records Found</td>
					  </tr>";
		endif;

		$dataArray = array( "list" => $table, "pagination" => $list->pagination );
		echo json_encode($dataArray);
	}
	
}