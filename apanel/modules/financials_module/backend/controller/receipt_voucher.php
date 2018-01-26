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
		
		$session                = new session();
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
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-')
		 AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->receipt_voucher->retrieveData("chartaccount as chart", 
					$cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

		// For Import
		$errmsg 			= array();
		if(isset($_FILES['import_csv']))
		{
			$headerArray = array('Document Date','Invoice Number','Customer','Due Date','Amount','Notes');
			
			// Open File 
			//$filewrite	=  fopen( 'modules/financials_module/files/error_receivables_import.txt', "w+");
			
			$file_types = array( "text/comma-separated-values", "text/csv", "application/csv", 
			"application/excel", "application/vnd.ms-excel", "application/vnd.msexcel", "text/anytext");

			// Validate File Type
			if(!in_array($_FILES['import_csv']['type'],$file_types))
			{
				$errmsg[]	= "Invalid file type, file must be CSV(Comma Separated Values) File.<br/>";
				//fwrite($filewrite, "Invalid file type, file must be CSV(Comma Separated Values) File.\n");
			}

			/**VALIDATE FILE IF CORRUPT**/
			if(!empty($_FILES['import_csv']['error']))
			{
				$errmsg[] = "File being uploaded is corrupted.<br/>";
				//fwrite($filewrite, "File being uploaded is corrupted.\n");
			}

			$file		= fopen($_FILES['import_csv']['tmp_name'],"r");

			// Validate File Contents
			$docData	= array();
			$i			= 0;
			$row		= 2;

			while (($file_data = fgetcsv($file, 1000, ",")) !== FALSE) 
			{
				if(!array_intersect($file_data, $headerArray))
				{
					$documentdate	= addslashes(htmlentities(trim($file_data[0])));
					$invoice		= addslashes(htmlentities(trim($file_data[1])));
					$customer		= addslashes(htmlentities(trim($file_data[2])));
					$duedate		= addslashes(htmlentities(trim($file_data[3])));
					$amount			= addslashes(htmlentities(trim($file_data[4])));
					$notes			= addslashes(htmlentities(trim($file_data[5])));
					
					$amount			= str_replace(',','',$amount);
			
					$datecheck 		= date_parse($documentdate);
					$datecheck1 	= date_parse($duedate);

					/**VALIDATE CUSTOMER**/
					$customercode		= $this->receipt_voucher->getValue("partners",array("partnercode"),
					"CONCAT( first_name, ' ', last_name ) = '$customer'");

					$customercode 	= $customercode[0]->partnercode;

					if(empty($customercode))
					{
						$errmsg[] 	= "Customer [ <strong>".stripslashes($customercode)."</strong> ] 
						on row $row does not exist.";
						//fwrite($filewrite, "Customer [ ".stripslashes($customercode)." ] on row $row 
						//does not exist.\n");
					}
					
					/**CHECK IF AMOUNT IS EMPTY**/
					if(empty($amount))
					{
						$errmsg[] 	= "Amount on row $row should not be empty.";
						//fwrite($filewrite, "Amount on row $row should not be empty.\n");
					}
					else
					{
						if(!is_numeric($amount))
						{
							$errmsg[] 	= "Amount [ <strong>$unitprice</strong> ] on row $row is not 
							a valid amount.";
							//fwrite($filewrite, "Amount [ $unitprice ] on row $row is not a valid 
							//amount.\n");
						}
					}

					/**VALIDATE DOCUMENT DATE**/
					if($datecheck["error_count"] != 0 && !checkdate($datecheck["month"], 
					$datecheck["day"], $datecheck["year"]))
					{
						if(!empty($documentdate))
						{
							$errmsg[] 	= "Document Date [ <strong>$documentdate</strong> ] on row 
							$row is not a valid date format (".date('M d, Y').").";
							//fwrite($filewrite, "Document Date [ $documentdate ] on row 
							//$row is not a valid date format (".date('M d, Y').").\n");
						}
						else
						{
							$errmsg[] 	= "Document Date on row $row should not be empty.";
							//fwrite($filewrite, "Document Date on row $row should not be empty.\n");
						}
					}
					else
					{
						$documentdate	= date("Y-m-d",strtotime($documentdate));
					}

					/**VALIDATE DUE DATE**/
					if($datecheck1["error_count"] != 0 && !checkdate($datecheck1["month"], 
					$datecheck1["day"], $datecheck1["year"]))
					{
						if(!empty($duedate))
						{
							$errmsg[] 	= "Due Date [ <strong>$duedate</strong> ] on row $row 
							is not a valid date format (".date('M d, Y').").";
							//fwrite($filewrite, "Due Date [ $duedate ] on row $row 
							//is not a valid date format (".date('M d, Y').").\n");
						}
						else
						{
							$errmsg[] 	= "Due Date on row $row should not be empty.";
							//fwrite($filewrite, "Due Date on row $row should not be empty.\n");
						}
					}
					else
					{
						$duedate	= date("Y-m-d",strtotime($duedate));
					}
					
					/**ASSIGN TO NEW ARRAY**/
					$docData[$i]['transactiondate'] 	= $documentdate;
					$docData[$i]['invoice'] 			= $invoice;
					$docData[$i]['customer']			= $customercode;
					$docData[$i]['duedate']				= $duedate;
					$docData[$i]['amount']				= $amount;
					$docData[$i]['notes']				= $notes;
					$i++;
					$row++;
				}
			}
			//fclose($filewrite);
			fclose($file);

			$errmsg				           = array_filter($errmsg);
			$data['import_error_messages'] = $errmsg;

			// Insert File Data
			if(!empty($docData) && empty($errmsg))
			{
				$file_import_result = $this->receipt_voucher->fileInsert($docData);
				$data["file_import_result"] = $file_import_result;
			}
		}
		// End Import

		$this->view->load('receipt_voucher/receipt_voucher_list', $data);
	}
	//
	public function update_app($check_rows)
	{
		/**UPDATE MAIN INVOICE**/
		$applicableHeaderTable = "accountsreceivable";
		$applicationTable 	   = "rv_application";
		
		$invoice_data 	= (isset($check_rows) && (!empty($check_rows))) ? trim($check_rows) : "";
		$invoice_data  = str_replace('\\', '', $invoice_data);
		$decode_json   = json_decode($invoice_data, true);

		// var_dump($decode_json);

		if(!empty($decode_json))
		{
			for($i = 0; $i < count($decode_json); $i++)
			{
				$invoice = $decode_json[$i]["arvoucher"];
				$amount  = $decode_json[$i]["amount"];

				// accountsreceivable
				$invoice_amount				= $this->receipt_voucher->getValue($applicableHeaderTable,
				 array("convertedamount"), "voucherno = '$invoice' AND stat = 'posted'");
				$applied_discount			= 0;

				// rv_application
				$applied_sum				= $this->receipt_voucher->getValue($applicationTable, 
				array("SUM(convertedamount) AS convertedamount")," arvoucherno = '$invoice' 
				AND stat = 'posted' ");

				// rv_application
				$applied_discount			= $this->receipt_voucher->getValue($applicationTable, 
				array("SUM(discount) AS discount"), "arvoucherno = '$invoice' AND stat = 'posted' ");

				// rv_application
				$applied_forexamount		= $this->receipt_voucher->getValue($applicationTable, 
				array("SUM(forexamount) AS forexamount"), "arvoucherno = '$invoice' 
				AND stat = 'posted' ");

				$applied_sum				= $applied_sum[0]->convertedamount - 
											  $applied_forexamount[0]->forexamount;

				$invoice_amount				= (!empty($invoice_amount)) ? 
												$invoice_amount[0]->convertedamount : 0;
				$applied_sum				= (!empty($applied_sum)) ? $applied_sum : 0;

				$invoice_balance			= $invoice_amount - $applied_sum - 
											  $applied_discount[0]->discount;

				$balance_info['amountreceived']	= $applied_sum + $applied_discount[0]->discount;
				
				$balance_info['balance']	= $invoice_amount - $applied_sum - 
											 $applied_discount[0]->discount;

				// accountsreceivable
				$insertResult = $this->receipt_voucher->editData($balance_info, $applicableHeaderTable, "voucherno = '$invoice'");	
				if(!$insertResult)
					$errmsg["error"][] = "The system has encountered an error in updating Account 
					Receivable [$invoice]. Please contact admin to fix this issue.<br/>";
			}
		}
	}
	//
	public function create()
	{	
		$cmp = $this->companycode;
		$seq = new seqcontrol();

		// Initialize variables
		$data = $this->input->post(array(
			"voucherno",
			"referenceno",
			"customercode",
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

		// Retrieve customer list
		$data["customer_list"]          = $this->receipt_voucher->retrieveCustomerList();

		// Retrieve proforma list
		// $data["proforma_list"]        = $this->receipt_voucher->retrieveProformaList();

		// Retrieve business type list
		$bus_type_data                = array("code ind", "value val");
		$bus_type_cond                = "type = 'businesstype'";
		$data["business_type_list"]   = $this->receipt_voucher->getValue("wc_option", 
		$bus_type_data, $bus_type_cond, false);

		// Retrieve business type list
		$acc_entry_data               = array("id ind","accountname val");
		$acc_entry_cond               = "accounttype != 'P'";
		$data["account_entry_list"]   = $this->receipt_voucher->getValue("chartaccount", 
		$acc_entry_data, $acc_entry_cond, "segment5");

		// Retrieve receivable account list
		// $pay_account_data 			  = array("id ind", "accountname val");
		// $pay_account_cond 			  = "accountclasscode = 'ACCREC' AND accounttype != 'P'";
		// $data["receivable_account_list"] = $this->receipt_voucher->getValue("chartaccount", $pay_account_data, $pay_account_cond, "accountname");

		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->receipt_voucher->retrieveData("chartaccount as chart", 
		$cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

		// Retrieve generated ID
		$gen_value                    = $this->receipt_voucher->getValue("receiptvoucher", 
									"COUNT(*) as count", "voucherno != ''");	
		$data["generated_id"]         = (!empty($gen_value[0]->count)) ? 
									 'TMP_'.($gen_value[0]->count + 1) : 'TMP_1';

		// Process form when form is submitted
		$data_validate = $this->input->post(array('referenceno', 
		"h_voucher_no", "customer", "document_date", "h_save", "h_save_new", "h_save_preview", 
		"h_check_rows_"));

		if (!empty($data_validate["customer"]) && !empty($data_validate["document_date"])) 
		{
			$errmsg = array();
			$temp 	= array();

			$voucherno = (isset($data_validate['h_voucher_no']) && 
			(!empty($data_validate['h_voucher_no']))) ? 
			htmlentities(trim($data_validate['h_voucher_no'])) : "";

			$isExist = $this->receipt_voucher->getValue("receiptvoucher", array("voucherno"), 
			"voucherno = '$voucherno'");

			if($isExist[0]->voucherno)
			{
				/**UPDATE MAIN TABLES**/
				$generatedvoucher			= $seq->getValue('RV'); 
			
				$update_info				= array();
				$update_info['voucherno']	= $generatedvoucher;
				$update_info['stat']		= 'posted';
				$update_condition			= "voucherno = '$voucherno'";
				$updateTempRecord			= $this->receipt_voucher->editData($update_info,
											 "receiptvoucher",$update_condition);
				$updateTempRecord			= $this->receipt_voucher->editData($update_info,
											 "rv_details",$update_condition);
				$updateTempRecord			= $this->receipt_voucher->editData($update_info,
											 "rv_application",$update_condition);
				$updateTempRecord			= $this->receipt_voucher->editData($update_info,
											 "rv_cheques",$update_condition);

				// /**UPDATE MAIN INVOICE**/
				$this->update_app($data_validate['h_check_rows_']);
				
			}
			
			if(empty($errmsg))
			{
				// For Admin Logs
				$this->logs->saveActivity("Add New Receipt Voucher [$generatedvoucher]");

				if(!empty($data_validate['h_save']))
				{
					$this->url->redirect(BASE_URL . 'financials/receipt_voucher');
				}
				else if(!empty($data_validate['h_save_preview']))
				{
					$this->url->redirect(BASE_URL . 'financials/receipt_voucher/view/' . $generatedvoucher);
				}
				else
				{
					$this->url->redirect(BASE_URL . 'financials/receipt_voucher/create');
				}
			}
			else
				$data["errmsg"] = $errmsg;
			
				
		}
		
		$this->view->load('receipt_voucher/receipt_voucher', $data);
	}
	//
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
		$data["customercode"]      = $data["main"]->customer;
		$data["c_convertedamount"] = $data["main"]->convertedamount;
		$data["exchangerate"]      = $data["main"]->exchangerate;
		$data["transactiondate"]   = $this->date->dateFormat($data["main"]->transactiondate);
		$data["referenceno"]       = $data["main"]->referenceno;
		$data["paymenttype"]       = $data["main"]->paymenttype;
		$data["particulars"]       = $data["main"]->particulars;

		// Vendor/Customer Details
		$data["c_customer"] 	   = (isset($data["cust"]->name))? $data["cust"]->name : "" ;
		$data["c_email"] 		   = (isset($data["cust"]->email))? $data["cust"]->email : "";
		$data["tinno"] 		   	   = (isset($data["cust"]->tinno))? $data["cust"]->tinno : "";
		$data["address1"] 	       = (isset($data["cust"]->address1))? $data["cust"]->address1 : "";
		$data["terms"] 	   		   = (isset($data["cust"]->terms))? $data["cust"]->terms : "";

		/**
		* Get the total forex amount applied
		*/
		$forex_result 			  = $this->receipt_voucher->getValue("rv_application", 
		array("SUM(forexamount) as forexamount"), "arvoucherno = '$sid' AND stat = 'posted'");
		$forexamount			  = ($forex_result[0]->forexamount != '') ? 
		$forex_result[0]->forexamount : 0;

		$data["forexamount"] 	  = $forexamount;

		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') 
		AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->receipt_voucher->retrieveData("chartaccount as chart", 
		$cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);
		$data["noCashAccounts"]  = false;
		
		if(empty($data["cash_account_list"]))
		{
			$data["noCashAccounts"]  = true;
		}

		//var_dump($data["payments"]);

		$this->view->load('receipt_voucher/receipt_voucher', $data);
	}
	//
	public function edit($sid)
	{
		$data         		   = $this->receipt_voucher->retrieveEditData($sid);

		$data["ui"]            = $this->ui;
		$data['show_input']    = $this->show_input;
		$data["task"] 		   = "edit";

		$data["generated_id"]  = $sid;
		$data["sid"] 		   = $sid;
		$data["date"] 		   = date("M d, Y");

		// Retrieve customer list
		$data["customer_list"]          = $this->receipt_voucher->retrieveCustomerList();

		// Retrieve proforma list
		// $data["proforma_list"]        = $this->receipt_voucher->retrieveProformaList();

		// Retrieve business type list
		$bus_type_data                = array("code ind", "value val");
		$bus_type_cond                = "type = 'businesstype'";
		$data["business_type_list"]   = $this->receipt_voucher->getValue("wc_option", 
											$bus_type_data, $bus_type_cond, false);

		// Retrieve business type list
		$acc_entry_data               = array("id ind","accountname val");
		$acc_entry_cond               = "accounttype != 'P'";
		$data["account_entry_list"]   = $this->receipt_voucher->getValue("chartaccount", 
									    $acc_entry_data, $acc_entry_cond, "segment5");

		// Retrieve receivable account list
		// $pay_account_data 			  = array("id ind", "accountname val");
		// $pay_account_cond 			  = "accountclasscode = 'ACCREC' AND accounttype != 'P'";
		// $data["receivable_account_list"] = $this->receipt_voucher->getValue("chartaccount", $pay_account_data, $pay_account_cond, "accountname");
		
		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') 
									AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->receipt_voucher->retrieveData("chartaccount as chart", 
								$cash_account_fields, $cash_account_cond, $cash_account_join, 
								$cash_order_by);

		// Header Data
		$data["voucherno"]       = $data["main"]->voucherno;
		$data["referenceno"]     = $data["main"]->referenceno;
		$data["customercode"]    = $data["main"]->customer;
		$data["exchangerate"]    = $data["main"]->exchangerate;
		$data["transactiondate"] = $this->date->dateFormat($data["main"]->transactiondate);
		$data["particulars"]     = $data["main"]->particulars;
		$data["paymenttype"]     = $data["main"]->paymenttype;

		$data["terms"] 		 = (!empty($data["cust"]->terms)) ? $data["cust"]->terms : "";
		$data["tinno"] 		 = (!empty($data["cust"]->tinno)) ? $data["cust"]->tinno : "";
		$data["address1"] 	 = (!empty($data["cust"]->address1)) ? $data["cust"]->address1 : "";
		
		// Process form when form is submitted
		$data_validate = $this->input->post(array('referenceno', "h_voucher_no", "customer", 
		"document_date", "h_save", "h_save_new", "h_save_preview", "h_check_rows_"));

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
	//
	public function print_preview($voucherno) 
	{
		// Retrieve Document Info
		$sub_select = $this->receipt_voucher->retrieveData("rv_application", 
					  array("SUM(amount) AS amount"), "voucherno = '$voucherno'");

		$sub_select[0]->amount;

		$docinfo_table  = "receiptvoucher as rv";
		$docinfo_fields = array('rv.transactiondate AS documentdate','rv.voucherno AS voucherno',
		"CONCAT( first_name, ' ', last_name )","'{$sub_select[0]->amount}' AS amount",
		'rv.amount AS rvamount', "'' AS referenceno", "particulars AS remarks", 
						  "p.partnername AS customer");
		$docinfo_join   = "partners as p ON p.partnercode = rv.customer AND p.companycode = rv.companycode";
		$docinfo_cond 	= "rv.voucherno = '$voucherno'";

		$documentinfo  	= $this->receipt_voucher->retrieveData($docinfo_table, $docinfo_fields, 
						  $docinfo_cond, $docinfo_join);

		$customer 	    = $documentinfo[0]->customer;

		// Retrieve Document Details
		$docdet_table   = "rv_details as dtl";
		$docdet_fields  = array("chart.segment5 as accountcode", "chart.accountname as accountname", 
							"SUM(dtl.debit) as debit","SUM(dtl.credit) as credit");
		$docdet_join    = "chartaccount as chart ON chart.id = dtl.accountcode 
							AND chart.companycode = dtl.companycode";
		$docdet_cond    = "dtl.voucherno = '$voucherno'";
		$docdet_groupby = "dtl.accountcode";
		$docdet_orderby = "CASE WHEN dtl.debit > 0 THEN 1 ELSE 2 END, dtl.linenum";
		
		$documentdetails = $this->receipt_voucher->retrieveData($docdet_table, $docdet_fields, 
								$docdet_cond, $docdet_join, $docdet_orderby, $docdet_groupby);

		// Retrieve Payment Details
		$paymentArray	 = $this->receipt_voucher->retrievePaymentDetails($voucherno);

		// Retrieve Cheque Details
		$rv_v 		  = "";
		$rv_voucherno = $this->receipt_voucher->getValue("rv_application", array("voucherno"), 
						"voucherno = '$voucherno'");
		
		$chequeArray = "";
		if(!empty($rv_voucherno))
		{
			for($p = 0; $p < count($rv_voucherno); $p++)
			{
				$rv_v .= "'".$rv_voucherno[$p]->voucherno."',";
			}
		
			$rv_v = rtrim($rv_v, ", ");
			
			$cheque_table = "rv_cheques rvc";
			$cheque_fields = array("rvc.chequeaccount", "chart.accountname AS accountname", 
				"rvc.chequenumber AS chequenumber", "rvc.chequedate AS chequedate", 
				"rvc.chequeamount AS chequeamount");
			$cheque_cond = "rvc.voucherno IN($rv_v)";
			$cheque_join = "chartaccount chart ON rvc.chequeaccount = chart.id";
			$chequeArray = $this->receipt_voucher->retrieveData($cheque_table, 
							$cheque_fields, $cheque_cond, $cheque_join);
		}
		
		// Setting for PDFs
		$print = new print_voucher_model('P', 'mm', 'Letter');
		$print->setDocumentType('Receipt Voucher')
				->setDocumentInfo($documentinfo[0])
				->setCustomer($customer)
				->setPayments($paymentArray)
				->setDocumentDetails($documentdetails)
				->setCheque($chequeArray)
				->drawPDF('rv_voucher_' . $voucherno);
	}
	//
	public function ajax($task)
	{
		header('Content-type: application/json');

		if ($task == 'save_receivable_data') 
		{
			$result = $this->add();
		}
		else if ($task == 'update') 
		{
			$result = $this->update();
		}
		else if ($task == 'delete_row') 
		{
			$result = $this->delete_row();
		}
		else if ($task == 'load_list') 
		{
			$result =$this->load_list();
		}
		else if ($task == 'export') 
		{
			$result = $this->export();
		}
		else if ($task == 'get_value') 
		{
			$result = $this->get_value();
		}
		else if ($task == 'save_data') 
		{
			$result = $this->save_data();
		}
		else if ($task == 'apply_payments') 
		{
			$result = $this->apply_payments_();
		}
		else if ($task == 'delete_payments') 
		{
			$result = $this->delete_payments();
		}
		else if ($task == 'load_receivables') 
		{
			$result = $this->load_receivables();
		}
		// else if ($task == 'apply_proforma') 
		// {
		// 	$result = $this->apply_proforma();
		// }
		else if($task == "load_ar")
		{
			$result = $this->load_ar();
		}
		// else if($task == "get_payments")
		// {
		// 	$result = $this->get_payments();
		// }
		else if($task == "getrvdetails")
		{
			$result = $this->getrvdetails();
		}

		echo json_encode($result);
	}
	//
	private function load_list()
	{

		$data_post = $this->input->post(array("daterangefilter", "custfilter", 
					"addCond", "search", "sort"));

		$list   = $this->receipt_voucher->retrieveList($data_post);
		
		$table  = "";

		if( !empty($list->result) ) :
			foreach($list->result as $key => $row)
			{
				$date        = $row->transactiondate;
				$date        = $this->date->dateFormat($date);
				$arvoucher   = $row->voucherno; 
				$balance     = $row->balance; 
				$amount	  	 = $row->amount; 
				$customer	 = $row->partnername; 
				$referenceno = $row->referenceno; 
				$rvvoucher   = $row->rv_voucherno; 

				if($balance != $amount && $balance != 0)
				{
					$voucher_status = '<span class="label label-info">PARTIAL</span>';

					$viewlink		= BASE_URL . "financials/receipt_voucher/view/$rvvoucher";
					$editlink		= BASE_URL . "financials/receipt_voucher/edit/$rvvoucher";
					$voucherlink	= MODULE_URL . "print_preview/$rvvoucher";
					$paymentlink	= BASE_URL . "financials/receipt_voucher/view/$rvvoucher#payment";

				}
				else if($balance != 0)
				{
					$voucher_status = '<span class="label label-warning">UNPAID</span>';

					$viewlink		= BASE_URL . "financials/accounts_receivable/manage/view/$arvoucher";
					$editlink		= BASE_URL . "financials/accounts_receivable/manage/edit/$arvoucher";
					$voucherlink	= BASE_URL . "financials/accounts_receivable/print_preview/$arvoucher";
					$paymentlink	= BASE_URL . "financials/accounts_receivable/manage/view/$arvoucher#payment";
				}
				else
				{
					$voucher_status = '<span class="label label-success">PAID&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';

					$viewlink		= BASE_URL . "financials/receipt_voucher/view/$rvvoucher";
					$editlink		= BASE_URL . "financials/receipt_voucher/edit/$rvvoucher";
					$voucherlink	= MODULE_URL . "print_preview/$rvvoucher";
					$paymentlink	= BASE_URL . "financials/receipt_voucher/view/$rvvoucher#payment";

				}
				
				
				$task		= '<div class="btn-group task_buttons" name="task_buttons">
								<a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" 
								href="#"><span class="caret"></span></a>
								<ul class="dropdown-menu left">
									<li>
										<a class="btn-sm" href="'.$viewlink.'">
											<span class="glyphicon glyphicon-eye-open"></span> View
										</a>
									</li>';
			
				// $task		.= ($balance == $amount) ? '<li><a class="btn-sm" href="'.$editlink.'"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>' : '';
				
				$task		.= '<li><a class="btn-sm" href="'.$editlink.'">
							<span class="glyphicon glyphicon-pencil"></span> Edit</a></li>';

				$task		.= '<li><a class="btn-sm" href="'.$voucherlink.'" target="_blank">
							<span class="glyphicon glyphicon-print"></span> Print Voucher</a></li>';

				// $task		.= ($balance != 0) ? '<li class="divider"></li><li><a class="btn-sm" href="'.$paymentlink.'" title="Issue Payment"><span class="glyphicon glyphicon-credit-card"></span> Issue Payment</a></li>' : '';

				if($balance != 0)
				{
					$task		.= '<li class="divider"></li><li><a class="btn-sm record-delete" 
					href = "#deleteModalAR" data-toggle="modal" 
					onClick="$(\'#deleteModalAR .modal-body #recordId\').val(\''.$arvoucher.'\');" 
					data-id="'.$arvoucher.'">
					<span class="glyphicon glyphicon-trash"></span> Delete</a></li>';
				}
				else
				{
					$task		.= '<li class="divider"></li><li><a class="btn-sm record-delete" 
					href="#deleteModal" data-toggle="modal" 
					onClick="$(\'#deleteModal .modal-body #recordId\').val(\''.$arvoucher.'\');" 
					data-id="'.$arvoucher.'"><span class="glyphicon glyphicon-trash"></span> 
					Delete</a></li>';
				}
					

				// $task		.= ($balance == $amount) ? '<li class="divider"></li><li><a class="btn-sm record-delete" href="#deleteModal" data-toggle="modal" onClick="$(\'.modal-body #recordId\').val(\''.$apvoucher.'\');" data-id="'.$apvoucher.'"><span class="glyphicon glyphicon-trash"></span> Delete</a></li>' : '';

				$task		.= '</ul>
							</div>';

				$table	.= '<tr id="'.$viewlink.'" class="list_row">';
				$table	.= '<td class="text-center" style="vertical-align:middle;">'.$task.'</td>';
				$table	.= '<td class="text-center" style="vertical-align:middle;">'.$date.'</td>';
				$table	.= '<td class="text-left" style="vertical-align:middle;">&nbsp;'.$arvoucher.'</td>';
				$table	.= '<td class="text-left" style="vertical-align:middle;">&nbsp;'.$rvvoucher.'</td>';
				$table	.= '<td class="text-left" style="vertical-align:middle;">&nbsp;'.$customer.'</td>';
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

		$dataArray = array( "list" => $table );
		//echo json_encode($dataArray);
		return $dataArray;
	}
	
	//
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
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') 
							AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->receipt_voucher->retrieveData("chartaccount as chart", 
		$cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);


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
				$paymentcheckdate	= ($paymentcheckdate != '0000-00-00') ? 
							$this->date->dateFormat($paymentcheckdate) : "";
				$paymentatccode		= $row->atcCode;
				$paymentnotes		= $row->particulars;
				$checkstat			= $row->checkstat;
				$paymentdiscount	= $row->discount;
				$paymentrate		= (isset($row->exchangerate) && !empty($row->exchangerate)) ? 
				$row->exchangerate : 1;
				$paymentconverted	= (isset($row->convertedamount) && $row->convertedamount > 0) ? 
				$row->convertedamount : $paymentamount;
				$arvoucherno 		= $row->arvoucherno;

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
								'<input value="'.$paymentnumber.'" name = "paymentnumber'.
								$row_count.'" id = "paymentnumber'.$row_count.'" type = "hidden">
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

				$button = (strtolower($checkstat) != 'cleared') ? '<button class="btn btn-default btn-xs" 
				onClick="editPaymentRow(event,\'edit'.$row_count.'\', \''.$arvoucherno.'\', \''.$sid.'\');" 
				title="Edit Payment"><span class="glyphicon glyphicon-pencil"></span></button>
				<button class="btn btn-default btn-xs" onClick="deletePaymentRow(event,\'delete'.$row_count.'\');" 
				title="Delete Payment" ><span class="glyphicon glyphicon-trash"></span></button>
				<a role="button" class="btn btn-default btn-xs" 
				href="'.BASE_URL.'financials/accounts_receivable/print_preview/'.$sid.'" 
				title="Print Receipt Voucher" onClick = "print(\''.$paymentnumber.'\');">
				<span class="glyphicon glyphicon-print"></span></a>' : 
				'<a role="button" class="btn btn-default btn-xs" 
				href="'.BASE_URL.'financials/accounts_receivable/print_preview/'.$sid.'" 
				onClick = "print(\''.$paymentnumber.'\');" title="Print Receipt Voucher" >
				<span class="glyphicon glyphicon-print"></span></a>';

				$table .= '<td class="text-center">'.$button.'</td>';
				$table .= '</tr>';

				$totalPayment += $paymentconverted;
				$totaldiscount+= $paymentdiscount;

				$row_count++;

			}
		else:
			$table .= "<tr>
							<td colspan = '7' class = 'text-center'>No payments received for this receivable</td>
					  </tr>";
		endif;

		$dataArray = array( "list" => $table, "pagination" => $list["payments"]->pagination, 
		"totalPayment" => $totalPayment, "totaldiscount" => $totaldiscount );
		return $dataArray;
	}
	//
	private function load_ar()
	{
		$data_post = $this->input->post(array("searchkey", "sort", "sortBy", "customer"));

		$list   = $this->receipt_voucher->retrieveARList($data_post);

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
					$voucher_status = '<span class="label label-success">PAID&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;</span>';
				}

				$table	.= '<tr class="list_row" style="cursor:pointer;" 
							onClick="getAR(\''.$j.'\');">';
				$table	.= 	'<td class="text-left" style="vertical-align:middle;" >
								<p class="form-control-static" id="date'.$j.'">'.$date.'</p>
							</td>';
				$table	.= 	'<td class="text-left" style="vertical-align:middle;" >
								<p class="form-control-static" id="voucher'.$j.'">'.$voucher.'</p>
							</td>';
				$table	.= 	'<td class="text-right" style="vertical-align:middle;" >
								<p class="form-control-static" id="balance'.$j.'">'.
								number_format($balance,2).'</p>
							</td>';
				$table	.= 	'<td class="text-center" style="vertical-align:middle;" >
								<p class="form-control-static" id="status'.$j.'">'.
								$voucher_status.'</p>
							</td>';
				$table	.= '</tr>';
			}
		else:
			$table .= "<tr>
							<td colspan = '5' class = 'text-center'>No Records Found</td>
					  </tr>";
		endif;

		$dataArray = array( "transactions_list" => $table );
		return $dataArray;
	}
	//
	private function add()
	{
		$data_post = $this->input->post();
		$seq 	   = new seqcontrol();
		$msg 	   = "";

		$result    = $this->receipt_voucher->insertData($data_post);

		if(!empty($result))
			$msg = "error";
		else
		{	
			$msg = "success";
		}
			

		$dataArray = array("msg" => $msg);
		return $dataArray;
	}
	//
	private function update() 
	{
		$data = $this->input->post(array("table", "condition", "fields"));
		$data["condition"] = stripslashes($data["condition"]);

		/**
		* Update Database
		*/
		$result = $this->receipt_voucher->editData($data["fields"], $data["table"], 
		$data["condition"]);

		if($result)
			$msg = "success";
		else
			$msg = "error_update";

		$dataArray = array( "msg" => $msg );
		return $dataArray;
	}
	//
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
			$msg = "The system has encountered an error in deleting. 
			Please contact admin to fix this issue.";

		$dataArray = array( "msg" => $msg );
		return $dataArray;
	}
	//
	private function export()
	{
		$data_get = $this->input->get(array("daterangefilter", "custfilter", "addCond", "search"));
		$data_get['daterangefilter'] = str_replace(array('%2F', '+'), array('/', ' '), 
		$data_get['daterangefilter']);
		$result = $this->receipt_voucher->fileExport($data_get);
		$header = array("Document Date", "Voucher No", "Customer", "Invoice No", 
		"Amount", "Balance", "Notes"); 
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
	//
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

			$dataArray = array( "address" => $result[0]->address1, "tinno" => $result[0]->tinno, 
			"terms" => $result[0]->terms );
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
	//
	private function save_data()
	{
		$cmp       = $this->companycode;
		$result    = "";

		$data_cond = $this->input->post("h_form");
		
		if($data_cond == "customerdetail")
		{
			$data_var  = array("h_terms", "h_tinno", "h_address1", "h_querytype", "h_condition");
			$data_post = $this->input->post($data_var);
			
			/**
			* Save Customer Detials
			*/
			$result = $this->receipt_voucher->saveDetails("partners", $data_post, "customerdetail");
		}

		if($result)
			$msg = "success";
		else
			$msg = "The system has encountered an error in saving. 
			Please contact admin to fix this issue.";

		$dataArray = array( "msg" => $msg );
		return $dataArray;
	
	}
	//
	private function apply_payments_()
	{
		$success = false;

		$data_post = $this->input->post();

		$result = $this->receipt_voucher->applyPayments_($data_post);
		
		if(!empty($result["error"]))
		{
			$success = false;
		}
		else
		{
			$success = true;
			$result["error"] = array();
		}

		$dataArray = array("error" => $result["error"], "success" => $success);
		return $dataArray;
	}
	//
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
	//
	private function load_receivables()
	{
		$data       = $this->input->post(array("customer", "voucherno"));
		$task       = $this->input->post("task");

		$result     = $this->receipt_voucher->retrieveARList($data);

		$table             = "";
		$j 	               = 1;
		$json_encode_array = array();
		$json_data         = array();
		$total_pay         = 0;
		$json_encode       = "";
		$edited_amount 	   = 0;

	

		if(!empty($result["result"][0]->voucherno))
		{
			
			for($i = 0; $i < count($result["result"]); $i++, $j++)
			{
				$date			= $result["result"][$i]->transactiondate;
				$date			= $this->date->dateFormat($date);
				$voucher		= $result["result"][$i]->voucherno;
				$customer	    = $result["result"][$i]->customer_name;
				$balance		= $result["result"][$i]->balance; 
				$totalamount	= $result["result"][$i]->amount;
				

				$total_pay 		+= $totalamount;

				$json_encode_array["row"]       = $i;
				$json_encode_array["arvoucher"] = $voucher;
				$json_encode_array["amount"]    = $totalamount;
				$json_data[] 					= $json_encode_array;

				$json_encode 					= json_encode($json_data);

				$appliedamount	= $this->receipt_voucher->getValue("rv_application", 
				array("SUM(amount) AS amount"),"arvoucherno = '$voucher' 
				AND stat IN('posted', 'temporary')");
				$appliedamount  = (isset($appliedamount[0]->amount))? $appliedamount[0]->amount : 0;

				$table	.= '<tr>'; 
				$table	.= 	'<td class="text-center" style="vertical-align:middle;">';
				$table	.= 		'<input type="checkbox" name="checkBox[]" id = "row_check'.$i.'" 
							class = "icheckbox" row = '.$i.' toggleid = "0">'; 
				$table	.= 		'<input type="hidden" id="invoice['.$i.']" 
							name = "invoice_modal['.$j.']" >';
				$table	.= 	'</td>';
				$table	.= 	'<td class="text-center" style="vertical-align:middle;" 
							id = "date_modal'.$i.'" onClick="selectReceivable('.$i.',1);">&nbsp;'.
							$date.'</td>';
				
				$table	.= '<td class="text-left" style="vertical-align:middle;" 
							id = "arvoucher_modal'.$i.'" onClick="selectReceivable('.$i.',1);">'.
							$voucher.'</td>';
				$table	.= '<td class="text-right" style="vertical-align:middle;" 
							id = "totalamount_modal'.$i.'" onClick="selectReceivable('.$i.',1);">'.
							number_format($totalamount,2).'
							<input type = "hidden" name = "totalamountval['.$j.']" 
							id = "totalamountval['.$j.']" 
							value = "'.number_format($totalamount, 2).'"/>
							</td>';
				$table	.= '<td class="text-right" style="vertical-align:middle;" 
							id = "balance_modal'.$i.'" onClick="selectReceivable('.$i.',1);">'.
							number_format($balance,2).'</td>';
				
				if($task == "create")
				{
					$table	.= '<td class="text-center pay" style="vertical-align:middle;">'
									.$this->ui->formField('text')
												->setSplit('', 'col-md-12')
												->setClass("input-sm text-right paymentamount")
												->setName('paymentamount['.$i.']')
												->setId('paymentamount['.$i.']')
												->setPlaceHolder("0.00")
												->setAttribute(array("maxlength" => "50", 
												"disabled" => "disabled", 
												"onBlur" => 'checkBalance(this.value,'.$i.'); 
												formatNumber(this.id);', 
												"onClick" => "SelectAll(this.id);"))
												->setValue(number_format($appliedamount, 2))
												->draw(true).
									'<input type = "hidden" name = "pay_amount['.$j.']" 
						id = "pay_amount['.$j.']" value = "'.number_format($appliedamount, 2).'"/>
								</td>';
				}
				
				if($task == "edit")
				{
					$table	.= '<td class="text-center pay" style="vertical-align:middle;">'
									.$this->ui->formField('text')
												->setSplit('', 'col-md-12')
												->setClass("input-sm text-right")
												->setName('amount_paid['.$i.']')
												->setId('amount_paid['.$i.']')
												->setPlaceHolder("0.00")
												->setAttribute(array("maxlength" => "50", "disabled" => "disabled", "onBlur" => 'checkBalance(this.value,'.$i.'); formatNumber(this.id);', "onClick" => "SelectAll(this.id);"))
												->setValue(number_format($appliedamount, 2))
												->draw(true).
									'<input type = "hidden" name = "paid_amount['.$j.']" 
									id = "paid_amount['.$j.']" value = "'.$appliedamount.'"/>
								</td>';
				}
				
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
		return $dataArray;
	}
	//
	private function getrvdetails()
	{
		$checkrows       = $this->input->post("checkrows");

		$invoice_data 	= (isset($checkrows) && (!empty($checkrows))) ? trim($checkrows) : "";
		$invoice_data  	= str_replace('\\', '', $invoice_data);
		$decode_json    = json_decode($invoice_data, true);
		$cond 			= "IN(";

		for($i = 0; $i < count($decode_json); $i++)
		{
			$arvoucherno = $decode_json[$i]["arvoucher"];
			$cond 		.= "'".$arvoucherno."',";
		}

		$cond 			= substr($cond, 0, -1);
		$cond 			.= ")";

		$customer       	= $this->input->post("customer");
		// $paymentmode    = $this->input->post("paymentmode");
		$data["customer"] = $customer;
		$data["cond"]     = $cond;

		$results 		= $this->receipt_voucher->retrieveRVDetails($data);

		$table 			= "";
		$row 			= 1;

		// Retrieve business type list
		$acc_entry_data     = array("id ind","accountname val");
		$acc_entry_cond     = "accounttype != 'P'";
		$account_entry_list = $this->receipt_voucher->getValue("chartaccount", 
		$acc_entry_data, $acc_entry_cond, "segment5");

		$ui 	            = $this->ui;
		$show_input         = $this->show_input;

		if(!empty($results))
		{
			$debit      = '0.00';
			$totalcredit = 0;
			$count       = count($results);

			for($i = 0; $i < $count; $i++, $row++)
			{
				$accountcode       = (!empty($results[$i]->accountcode)) ? 
									$results[$i]->accountcode : "";
				$detailparticulars = (!empty($results[$i]->detailparticulars)) ? 
									$results[$i]->detailparticulars : "";

				// Sum of debit will go to credit side on RV
				$credit         	   = number_format($results[$i]->sumdebit, 2);

				$totalcredit    	   += $debit;

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
									->setAttribute(array("maxlength" => "20", 
									"onBlur" => "formatNumber(this.id); addAmountAll('debit');", 
									"onClick" => "SelectAll(this.id);", 
									"onKeyPress" => "isNumberKey2(event);"))
									->setValue($debit)
									->draw($show_input).			
								'</td>';
				$table 	.= '<td class = "remove-margin">'
								.$ui->formField('text')
									->setSplit('', 'col-md-12')
									->setName('credit['.$row.']')
									->setId('credit['.$row.']')
									->setAttribute(array("maxlength" => "20", 
									"onBlur" => "formatNumber(this.id); addAmountAll('credit');", 
									"onClick" => "SelectAll(this.id);", 
									"onKeyPress" => "isNumberKey2(event);", "readonly" => ""))
									->setValue($credit)
									->draw($show_input).
							'</td>';
				$table  .= '<td class="text-center">
								<button type="button" class="btn btn-danger 
								btn-flat confirm-delete" data-id='.$row.' name="chk[]" 
								style="outline:none;" onClick="confirmDelete('.$row.');">
								<span class="glyphicon glyphicon-trash"></span></button>
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
		return $dataArray;

	}

}