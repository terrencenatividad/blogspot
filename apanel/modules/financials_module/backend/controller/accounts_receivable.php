<?php
class controller extends wc_controller 
{

	public function __construct()
	{
		parent::__construct();
		$this->url 			   	 	= new url();
		$this->accounts_receivable 	= new accounts_receivable();
		$this->restrict 			= new financials_restriction_model();
		$this->input            	= new input();
		$this->seq 					= new seqcontrol();
		$this->ui 			    	= new ui();
		$this->logs 				= new log();
		$this->view->title      	= 'Accounts Receivable';
		$this->show_input 	    	= true;

		$this->companycode      	= COMPANYCODE;
		$this->username 			= USERNAME;
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
		$data["customer_list"]  = $this->accounts_receivable->retrieveCustomerList();

		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->accounts_receivable->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

		// For Import
		$errmsg 			= array();
		if(isset($_FILES['import_csv']))
		{
			$headerArray = array('Document Date','Invoice Number','Customer','Due Date','Amount','Notes');
			
			// Open File 
			$filewrite	=  fopen( 'modules/financials_module/files/error_receivables_import.txt', "w+");
			
			$file_types = array( "text/comma-separated-values", "text/csv", "application/csv", "application/excel", "application/vnd.ms-excel", "application/vnd.msexcel", "text/anytext");

			// Validate File Type
			if(!in_array($_FILES['import_csv']['type'],$file_types))
			{
				$errmsg[]	= "Invalid file type, file must be CSV(Comma Separated Values) File.<br/>";
				fwrite($filewrite, "Invalid file type, file must be CSV(Comma Separated Values) File.\n");
			}

			/**VALIDATE FILE IF CORRUPT**/
			if(!empty($_FILES['import_csv']['error']))
			{
				$errmsg[] = "File being uploaded is corrupted.<br/>";
				fwrite($filewrite, "File being uploaded is corrupted.\n");
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

					/**VALIDATE VENDOR**/
					$customercode		= $this->accounts_receivable->getValue("partners",array("partnercode"),"CONCAT( first_name, ' ', last_name ) = '$vendor'");

					$customercode 		= $customercode[0]->partnercode;

					if(empty($vendorcode))
					{
						$errmsg[] 	= "Customer [ <strong>".stripslashes($customercode)."</strong> ] on row $row does not exist.";
						fwrite($filewrite, "Vendor [ ".stripslashes($customercode)." ] on row $row does not exist.\n");
					}
					
					/**CHECK IF AMOUNT IS EMPTY**/
					if(empty($amount))
					{
						$errmsg[] 	= "Amount on row $row should not be empty.";
						fwrite($filewrite, "Amount on row $row should not be empty.\n");
					}
					else
					{
						if(!is_numeric($amount))
						{
							$errmsg[] 	= "Amount [ <strong>$unitprice</strong> ] on row $row is not a valid amount.";
							fwrite($filewrite, "Amount [ $unitprice ] on row $row is not a valid amount.\n");
						}
					}

					/**VALIDATE DOCUMENT DATE**/
					if($datecheck["error_count"] != 0 && !checkdate($datecheck["month"], $datecheck["day"], $datecheck["year"]))
					{
						if(!empty($documentdate))
						{
							$errmsg[] 	= "Document Date [ <strong>$documentdate</strong> ] on row $row is not a valid date format (".date('M d, Y').").";
							fwrite($filewrite, "Document Date [ $documentdate ] on row $row is not a valid date format (".date('M d, Y').").\n");
						}
						else
						{
							$errmsg[] 	= "Document Date on row $row should not be empty.";
							fwrite($filewrite, "Document Date on row $row should not be empty.\n");
						}
					}
					else
					{
						$documentdate	= date("Y-m-d",strtotime($documentdate));
					}

					/**VALIDATE DUE DATE**/
					if($datecheck1["error_count"] != 0 && !checkdate($datecheck1["month"], $datecheck1["day"], $datecheck1["year"]))
					{
						if(!empty($duedate))
						{
							$errmsg[] 	= "Due Date [ <strong>$duedate</strong> ] on row $row is not a valid date format (".date('M d, Y').").";
							fwrite($filewrite, "Due Date [ $duedate ] on row $row is not a valid date format (".date('M d, Y').").\n");
						}
						else
						{
							$errmsg[] 	= "Due Date on row $row should not be empty.";
							fwrite($filewrite, "Due Date on row $row should not be empty.\n");
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
			fclose($filewrite);
			fclose($file);

			$errmsg				           = array_filter($errmsg);
			$data['import_error_messages'] = $errmsg;

			// Insert File Data
			if(!empty($docData) && empty($errmsg))
			{
				$file_import_result = $this->accounts_receivable->fileInsert($docData);
				$data["file_import_result"] = $file_import_result;
			}
		}
		// End Import

		$this->view->load('accounts_receivable/accounts_receivable_list', $data);
	}

	public function create()
	{	
		$cmp = $this->companycode;
		$seq = new seqcontrol();

		// Initialize variables
		$data = $this->input->post(array(
			"voucherno",
			"referenceno",
			"customercode",
			"transactiondate",
			"tinno",
			"address1",
			"duedate",
			"particulars",
			"proformacode",
			"terms",
			"date",
			"invoiceno"
		));

		$this->view->title      	= 'Create Accounts Receivable';		
		$data["ui"]                 = $this->ui;
		$data['show_input']         = $this->show_input;
		$data['button_name']        = "Save";
		$data["task"] 		        = "create";
		$data["ajax_post"] 	        = "";
		$data["row_ctr"] 			= 0;
		$data["exchangerate"]       = "1.00";
		$data["transactiondate"]    = $this->date->dateFormat();
		$data["duedate"]      		= $this->date->dateFormat();

		// Retrieve customer list
		$data["customer_list"]          = $this->accounts_receivable->retrieveCustomerList();

		// Retrieve proforma list
		$data["proforma_list"]        = $this->accounts_receivable->retrieveProformaList();

		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;

		// Retrieve business type list
		$bus_type_data                = array("code ind", "value val");
		$bus_type_cond                = "type = 'businesstype'";
		$data["business_type_list"]   = $this->accounts_receivable->getValue("wc_option", $bus_type_data, $bus_type_cond, false);

		// Retrieve business type list
		$acc_entry_data               = array("id ind","CONCAT(segment5, ' - ', accountname) val");
		$acc_entry_cond               = "accounttype != 'P' AND stat = 'active'";
		$data["account_entry_list"]   = $this->accounts_receivable->getValue("chartaccount", $acc_entry_data, $acc_entry_cond, "segment5");

		// Retrieve Receivable account list
		$pay_account_data 			  = array("id ind", "CONCAT(segment5, ' - ', accountname) val");
		$pay_account_cond 			  = "accountclasscode = 'ACCREC' AND accounttype != 'P' AND stat = 'active'";
		$data["receivable_account_list"] = $this->accounts_receivable->getValue("chartaccount", $pay_account_data, $pay_account_cond, "accountname");

		// Retrieve generated ID
		$gen_value                    = $this->accounts_receivable->getValue("accountsreceivable", "COUNT(*) as count", "voucherno != ''");	
		$data["generated_id"]         = (!empty($gen_value[0]->count)) ? 'TMP_'.($gen_value[0]->count + 1) : 'TMP_1';

		$data['restrict_ar'] 		  = false;
		// Process form when form is submitted
		$data_validate = $this->input->post(array('referenceno', "h_voucher_no", "customer", "document_date", "h_save", "h_save_new", "h_save_preview"));

		if (!empty($data_validate["customer"]) && !empty($data_validate["document_date"])) 
		{
			$voucherno = (isset($data_validate['h_voucher_no']) && (!empty($data_validate['h_voucher_no']))) ? htmlentities(trim($data_validate['h_voucher_no'])) : "";

			$isExist = $this->accounts_receivable->getValue("accountsreceivable", array("voucherno"), "voucherno = '$voucherno'");
			
			if($isExist[0]->voucherno)
			{
				/**UPDATE MAIN TABLES**/
				$generatedvoucher			= $seq->getValue('AR'); 
				
				$update_info				= array();
				$update_info['voucherno']	= $generatedvoucher;
				$update_info['stat']		= 'posted';
				$update_condition			= "voucherno = '$voucherno'";
				$updateTempRecord			= $this->accounts_receivable->editData($update_info,"accountsreceivable",$update_condition);
				$updateTempRecord			= $this->accounts_receivable->editData($update_info,"ar_details",$update_condition);
			}
			
			// For Admin Logs
			$this->logs->saveActivity("Add New Accounts Receivable [$generatedvoucher]");

			if(!empty($data_validate['h_save']))
			{
				$this->url->redirect(BASE_URL . 'financials/accounts_receivable');
			}
			else if(!empty($data_validate['h_save_preview']))
			{
				$this->url->redirect(BASE_URL . 'financials/accounts_receivable/view/' . $generatedvoucher);
			}
			else
			{
				$this->url->redirect(BASE_URL . 'financials/accounts_receivable/create');
			}
			
		}
		
		$this->view->load('accounts_receivable/accounts_receivable', $data);
	}

	public function view($sid)
	{
		$cmp 					   = $this->companycode;
		
		// Retrieve data
		$data         			   = $this->accounts_receivable->retrieveEditData($sid);
		
		$this->view->title         = 'View Accounts Receivable';		
		$data["ui"]   			   = $this->ui;
		$data['show_input'] 	   = false;
		$data["button_name"] 	   = "Edit";
		$data["task"] 	  		   = "view";
		$data["sid"] 			   = $sid;
		$data["date"] 			   = $this->date->dateFormat();//date("M d, Y");e

		$data['checker'] 		   = isset($data['main']->importchecker) && !empty($data['main']->importchecker) 	?	$data['main']->importchecker 	:	"";

		$data["business_type_list"] = array();
		$data["account_entry_list"] = array();

		// Main
		$data["v_voucherno"]       = $data["main"]->voucherno;
		$data["v_customercode"]    = $data["main"]->customer;
		$data["v_convertedamount"] = $data["main"]->convertedamount;
		$data["v_exchangerate"]    = $data["main"]->exchangerate;
		$data["proformacode"]      = $data["main"]->proformacode;
		$data["v_transactiondate"] = $this->date->dateFormat($data["main"]->transactiondate); 
		$data["v_duedate"]         = $this->date->dateFormat($data["main"]->duedate);
		$data["v_referenceno"]     = $data["main"]->referenceno;
		$data["v_invoiceno"]       = $data["main"]->invoiceno;
		$data["v_balance"]     	   = $data["main"]->balance;
		$data["v_notes"]     	   = $data["main"]->particulars;
		$data['stat'] 		   	   = $data["main"]->stat;
		
		// Vendor/Customer Details
		$data["v_customer"] 		= (!empty($data["cust"]->name)) ? $data["cust"]->name : "";
		$data["v_email"] 		   	= (!empty($data["cust"]->email)) ? $data["cust"]->email : "";
		$data["v_tinno"] 		  	= (!empty($data["cust"]->tinno)) ? $data["cust"]->tinno : "";
		$data["v_address1"] 	   	= (!empty($data["cust"]->address1)) ? $data["cust"]->address1 : "";

		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;

		/**
		* Get the total forex amount applied
		*/
		$forex_result 			  = $this->accounts_receivable->getValue("rv_application", array("SUM(forexamount) as forexamount"), "arvoucherno = '$sid' AND stat = 'posted'");
		$forexamount			  = ($forex_result[0]->forexamount != '') ? $forex_result[0]->forexamount : 0;

		$data["forexamount"] 	  = $forexamount;

		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->accounts_receivable->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);
		$data["noCashAccounts"]  = false;
		
		if(empty($data["cash_account_list"]))
		{
			$data["noCashAccounts"]  = true;
		}

		// Get Prefix
		$ap_prefix 		  		 = $this->accounts_receivable->getValue("wc_sequence_control", array("prefix"), "code = 'AR'");
		$data["prefix"] 		 = $ap_prefix[0]->prefix;

		$data['show_paymentdetails'] 	=	(!empty($data['payments']) && !is_null($data['payments'])) 		?  	1 	: 0;
		
		/**
		 * Status Badge
		 */
		$balance 	= $data["main"]->balance;
		$amount 	= $data["main"]->amount;
		$stat 		= $data["main"]->stat;
		if($balance != 0 && $stat == 'cancelled'){
			$status 		= 'cancelled';
			$status_class 	= 'danger';
		}
		else if($balance != $amount && $balance != 0 && $stat == 'cancelled'){
			$status 		= 'cancelled';
			$status_class 	= 'danger';
		}
		else if($balance != $amount && $balance != 0){
			$status  		= 'partial';
			$status_class 	= 'info';
		}else if($balance != 0){
			$status 		= 'unpaid';
			$status_class 	= 'warning';
		}
		else{
			$status 		= 'paid';
			$status_class 	= 'success';
		}

		$status_badge = '<span class="label label-'.$status_class.'">'.strtoupper($status).'</span>';
		$data['status_badge'] 	= $status_badge;
		
		$restrict_ar	 	= $this->restrict->setButtonRestriction($data["main"]->transactiondate);
		$data['restrict_ar']= $restrict_ar;
		
		$this->view->load('accounts_receivable/accounts_receivable_view', $data);
	}

	public function edit($sid)
	{
		$cmp 		   		   = $this->companycode;
		$data         		   = $this->accounts_receivable->retrieveEditData($sid);

		$this->view->title     = 'Edit Accounts Receivable';
		$data["ui"]            = $this->ui;
		$data['show_input']    = $this->show_input;
		$data["task"] 		   = "edit";

		$data["generated_id"]  = $sid;
		$data["sid"] 		   = $sid;
		$data["date"] 		   = date("M d, Y");

		// Retrieve vendor list
		$data["customer_list"]          = $this->accounts_receivable->retrieveCustomerList();

		// Retrieve proforma list
		$data["proforma_list"]        = $this->accounts_receivable->retrieveProformaList();

		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;

		// Retrieve business type list
		$bus_type_data                = array("code ind", "value val");
		$bus_type_cond                = "type = 'businesstype'";
		$data["business_type_list"]   = $this->accounts_receivable->getValue("wc_option", $bus_type_data, $bus_type_cond, false);

		// Retrieve business type list
		$acc_entry_data               = array("id ind","CONCAT(segment5, ' - ', accountname) val");
		$acc_entry_cond               = "accounttype != 'P' AND stat = 'active'";
		$data["account_entry_list"]   = $this->accounts_receivable->getValue("chartaccount", $acc_entry_data, $acc_entry_cond, "segment5");

		// Retrieve Receivable account list
		$pay_account_data 			  = array("id ind", "CONCAT(segment5, ' - ', accountname) val");
		$pay_account_cond 			  = "accountclasscode = 'ACCREC' AND accounttype != 'P' AND stat = 'active'";
		$data["receivable_account_list"] = $this->accounts_receivable->getValue("chartaccount", $pay_account_data, $pay_account_cond, "accountname");
		
		// Header Data
		$data["voucherno"]       = $data["main"]->voucherno;
		$data["referenceno"]     = $data["main"]->referenceno;
		$data["invoiceno"]       = $data["main"]->invoiceno;
		$data["customercode"]    = $data["main"]->customer;
		$data["exchangerate"]    = $data["main"]->exchangerate;
		$data["proformacode"]    = $data["main"]->proformacode;
		$data["transactiondate"] = $this->date->dateFormat($data["main"]->transactiondate);
		$data["particulars"]     = $data["main"]->particulars;

		$data["terms"] 		 = $data["cust"]->terms;
		$data["tinno"] 		 = $data["cust"]->tinno;
		$data["address1"] 	 = $data["cust"]->address1;
		$data["duedate"]     = $this->date->dateFormat($data["main"]->duedate); 
		
		$data['restrict_ar'] = false;
		// Process form when form is submitted
		$data_validate = $this->input->post(array('referenceno', "h_voucher_no", "customer", "document_date", "h_save", "h_save_new", "h_save_preview"));

		if (!empty($data_validate["customer"]) && !empty($data_validate["document_date"])) 
		{
			// For Admin Logs
			$this->logs->saveActivity("Updated Accounts Receivable [$sid]");

			$this->url->redirect(BASE_URL . 'financials/accounts_receivable');	 
		}

		$this->view->load('accounts_receivable/accounts_receivable', $data);
	}

	public function print_preview($voucherno) 
	{
		$companycode = COMPANYCODE;

		// Retrieve Document Info
		$sub_select = $this->accounts_receivable->retrieveData("rv_application", array("SUM(amount) AS amount"), "arvoucherno = '$voucherno'");

		// Total paid amount
		$sub_select[0]->amount;

		// Retrieve sum of credit in ar_details
		$apamount 		= $this->accounts_receivable->retrieveData("ar_details", "SUM(credit) AS amount", "voucherno = '$voucherno'");
		$apamount[0]->amount;

		$docinfo_table  = "accountsreceivable as ar";
		$docinfo_fields = array('ar.transactiondate AS documentdate','ar.voucherno AS voucherno',"CONCAT( first_name, ' ', last_name )","IF('{$sub_select[0]->amount}' != \"\", '{$sub_select[0]->amount}', '{$apamount[0]->amount}') AS amount",'ar.amount AS apamount', "'' AS referenceno", "particulars AS remarks", "p.partnername AS customer");
		$docinfo_join   = "partners as p ON p.partnercode = ar.customer AND p.companycode = ar.companycode";
		$docinfo_cond 	= "ar.voucherno = '$voucherno'";

		$documentinfo  	= $this->accounts_receivable->retrieveData($docinfo_table, $docinfo_fields, $docinfo_cond, $docinfo_join);

		$customer 	    = $documentinfo[0]->customer;

		// Retrieve Document Details
		$docdet_table   = "ar_details as dtl";
		$docdet_fields  = array("chart.segment5 as accountcode", "chart.accountname as accountname", "SUM(dtl.debit) as debit","SUM(dtl.credit) as credit");
		$docdet_join    = "chartaccount as chart ON chart.id = dtl.accountcode AND chart.companycode = dtl.companycode";
		$docdet_cond    = "dtl.voucherno = '$voucherno'";
		$docdet_groupby = "dtl.accountcode";
		$docdet_orderby = "CASE WHEN dtl.debit > 0 THEN 1 ELSE 2 END, dtl.linenum";
		
		$documentdetails = $this->accounts_receivable->retrieveData($docdet_table, $docdet_fields, $docdet_cond, $docdet_join, $docdet_orderby, $docdet_groupby);

		// Retrieve Payment Details
		$paymentArray	 = $this->accounts_receivable->retrievePaymentDetails($voucherno);

		// Retrieve Cheque Details
		$pv_v 		  = "";
		$pv_voucherno = $this->accounts_receivable->getValue("rv_application", array("voucherno"), "arvoucherno = '$voucherno'");
		
		$chequeArray = "";
		if(!empty($pv_voucherno))
		{
			for($p = 0; $p < count($pv_voucherno); $p++)
			{
				$pv_v .= "'".$pv_voucherno[$p]->voucherno."',";
			}
			
			$pv_v = rtrim($pv_v, ", ");
			
			$cheque_table = "rv_cheques rvc";
			$cheque_fields = array("rvc.chequeaccount", "chart.accountname AS accountname", "rvc.chequenumber AS chequenumber", "rvc.chequedate AS chequedate", "rvc.chequeamount AS chequeamount");
			$cheque_cond = "rvc.voucherno IN($pv_v)";
			$cheque_join = "chartaccount chart ON rvc.chequeaccount = chart.id";
			$chequeArray = $this->accounts_receivable->retrieveData($cheque_table, $cheque_fields, $cheque_cond, $cheque_join);
		}
		
		// Setting for PDFs
		$print = new print_voucher_model('P', 'mm', 'Letter');
		$print->setDocumentType('Accounts Receivable')
		->setDocumentInfo($documentinfo[0])
		->setVendor($customer)
		->setPayments($paymentArray)
		->setDocumentDetails($documentdetails)
		->setCheque($chequeArray)
		->drawPDF('ar_voucher_' . $voucherno);
	}

	public function ajax($task)
	{
		header('Content-type: application/json');

		if ($task == 'save_receivable_data') {
			$this->add();
		} else if ($task == 'update') {
			$this->update();
		} else if ($task == 'delete_row') {
			$this->delete_row();
		} else if ($task == 'ajax_list') {
			$this->ajax_list();
		} else if ($task == 'export') {
			$this->export();
		} else if ($task == 'get_value') {
			$this->get_value();
		} else if ($task == 'save_data') {
			$this->save_data();
		} else if ($task == 'apply_payments') {
			$this->apply_payments();
		} else if ($task == 'delete_payments') {
			$this->delete_payments();
		} else if ($task == 'load_receivables') {
			$this->load_receivables();
		} else if ($task == 'apply_proforma') {
			$this->apply_proforma();
		} else if ($task == 'ajax_delete') {
			$this->delete_invoice();
		} else if($task == 'get_import') {
			$this->get_import();
		} else if($task == 'save_import') {
			$this->save_import();
		}
	}

	private function delete_invoice()
	{
		$vouchers 		= $this->input->post('delete_id');
		$invoices = "'" . implode("','", $vouchers) . "'";
		$data['stat'] 	= "cancelled";
		
		$cond 			= " voucherno IN ($invoices) ";

		$result 		= $this->accounts_receivable->updateData($data, "accountsreceivable", $cond);

		if( $result )
		{
			$result 	= $this->accounts_receivable->updateData($data, "ar_details", $cond);
		}

		if( $result )
		{
			$result 	= $this->accounts_receivable->reverseEntries($invoices, "ar_details", $cond);
		}

		if($result){
			$code 	= 1; 
			$msg 	= "Successfully cancelled the vouchers.";
		}else{
			$code 	= 0; 
			$msg 	= "Sorry, the system was unable to delete the vouchers.";
		}

		$dataArray = array( "code" => $code, "msg" => $msg );
		echo json_encode($dataArray);
	}

	private function ajax_list()
	{
		$data_post = $this->input->post(array("daterangefilter", "customer", "filter", "search", "sort"));

		$list   = $this->accounts_receivable->retrieveList($data_post);
		
		$table  = "";

		if( !empty($list->result) ) :
			foreach($list->result as $key => $row)
			{
				$date        			= $row->transactiondate;
				$restrict_ar 			= $this->restrict->setButtonRestriction($date);
				$date        			= $this->date->dateFormat($date);
				$voucher     			= $row->voucherno; 
				$balance     			= $row->balance; 
				$amount	  	 			= $row->amount; 
				$customer	 			= $row->customer; 
				$referenceno 			= $row->referenceno; 
				$checker 	 			= $row->importchecker; 
				$import 				= ($checker=='import') 	?	"Yes" 	:	"No";
				$stat					= $row->stat;
				$payment_status			= $row->payment_status;
				
				// if($balance != 0 && $stat == 'cancelled')
				// {
				// 	$voucher_status = '<span class="label label-danger">CANCELLED</span>';
				// }
				// else if($balance != $amount && $balance != 0 && $stat == 'cancelled')
				// {
				// 	$voucher_status = '<span class="label label-danger">CANCELLED</span>';
				// }
				// else if($balance != $amount && $balance != 0)
				// {
				// 	$voucher_status = '<span class="label label-info">PARTIAL</span>';
				// }
				// else if($balance != 0)
				// {
				// 	$voucher_status = '<span class="label label-warning">UNPAID</span>';
				// }
				// else
				// {
				// 	$voucher_status = '<span class="label label-success">PAID&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
				// }

				if($payment_status == "paid") {
					$voucher_status = '<span class="label label-success">PAID&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
				} else if($payment_status == 'unpaid'){
					$voucher_status = '<span class="label label-warning">UNPAID</span>';
				} else if($payment_status == 'cancelled'){
					$voucher_status = '<span class="label label-danger">CANCELLED</span>';
				} else if($payment_status == "partial"){
					$voucher_status = '<span class="label label-info">PARTIAL</span>';
				}

				$show_edit 		= ($balance == $amount  && $stat != 'cancelled');
				$show_delete 	= ($balance == $amount && $stat != 'cancelled');
				$show_payment 	= ($balance != 0  && $stat != 'cancelled');
				$dropdown = $this->ui->loadElement('check_task')
				->addView()
				->addEdit($show_edit && $checker != "import" && $restrict_ar)
				->addOtherTask(
					'Receive Payment',
					'credit-card',
					$show_payment  && $restrict_ar
				)
				->addDelete($show_delete && $checker != "import"  && $restrict_ar)
				->addCheckbox($show_delete && $checker != "import"  && $restrict_ar)
				->setValue($voucher)
				->setLabels(array('delete' => 'Cancel'))
				->draw();
				$viewlink		= BASE_URL . "financials/accounts_receivable/view/$voucher";
				$editlink		= BASE_URL . "financials/accounts_receivable/edit/$voucher";
				$voucherlink	= MODULE_URL . "print_preview/$voucher";
				$paymentlink	= BASE_URL . "financials/accounts_receivable/view/$voucher#payment";
				
				$table	.= '<tr>';
				$table	.= '<td class="text-center" style="vertical-align:middle;">'.$dropdown.'</td>';
				$table	.= '<td style="vertical-align:middle;">'.$date.'</td>';
				$table	.= '<td style="vertical-align:middle;">'.$import.'</td>';
				$table	.= '<td style="vertical-align:middle;">&nbsp;'.$voucher.'</td>';
				$table	.= '<td style="vertical-align:middle;">&nbsp;'.$customer.'</td>';
				$table	.= '<td style="vertical-align:middle;">&nbsp;'.$referenceno.'</td>';
				$table	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;'.number_format($amount,2).'</td>';
				$table	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;'.number_format($balance,2).'</td>';
				$table	.= '<td class="text-center" style="vertical-align:middle;">&nbsp;'.$voucher_status.'</td>';
				$table	.= '</tr>';
			}
		else:
			$table .= "<tr>
			<td colspan = '8' class = 'text-center'><strong>No Records Found</strong></td>
			</tr>";
		endif;

		$dataArray = array( "list" => $table, "pagination" => $list->pagination );
		echo json_encode($dataArray);
	}

	private function add()
	{
		$data_post = $this->input->post();
		$seq 	   = new seqcontrol();
		$msg 	   = "";

		$result    = $this->accounts_receivable->insertData($data_post);

		if(!empty($result))
			$msg = $result;
		else
			$msg = "success";
		
		$dataArray = array("msg" => $msg);
		echo json_encode($dataArray);
	}

	private function update() 
	{
		$data = $this->input->post(array("table", "condition", "fields"));
		$data["condition"] = stripslashes($data["condition"]);

		/**
		* Update Database
		*/
		$result = $this->accounts_receivable->editData($data["fields"], $data["table"], $data["condition"]);

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
		$result = $this->accounts_receivable->deleteData($data_post);

		if($result)
			$msg = "success";
		else
			$msg = "The system has encountered an error in deleting. Please contact admin to fix this issue.";

		$dataArray = array( "msg" => $msg );
		echo json_encode($dataArray);
	}

	private function export()
	{
		$data_get = $this->input->get(array("daterangefilter", "customer", "filter", "search"));
		$data_get['daterangefilter'] = str_replace(array('%2F', '+'), array('/', ' '), $data_get['daterangefilter']);
		$result = $this->accounts_receivable->fileExport($data_get);
		$header = array("Document Date", "Voucher No", "Customer", "Invoice No", "Amount", "Balance", "Notes"); 
		$csv 	= '';

		$filename = "export_accounts_receivable.csv";
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
			$result = $this->accounts_receivable->getValue("partners", $data_var, $cond);

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
			$result = $this->accounts_receivable->getValue("chartaccount", $data_var, $cond);

			$dataArray = array("accountnature" => $result[0]->accountnature);
		}

		echo json_encode($dataArray);
	}

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
			$result = $this->accounts_receivable->saveDetails("partners", $data_post, "customerdetail");
		}
		else if ($data_cond == "newCustomer")
		{
			$data_var  = array("partnercode", "customer_name", "email", "address", "businesstype", "tinno", "terms", "h_querytype");
			$data_post = $this->input->post($data_var);
			
			/**
			* Save Customer Detials
			*/
			$result = $this->accounts_receivable->saveDetails("partners", $data_post, "newCustomer");
		}

		if($result)
			$msg = "success";
		else
			$msg = "The system has encountered an error in saving. Please contact admin to fix this issue.";

		$dataArray = array( "msg" => $msg );
		echo json_encode($dataArray);
		
	}

	private function apply_payments()
	{
		$data_post = $this->input->post(array("invoiceno", "paymentdate", "paymentnumber", "paymentaccount", "paymentmode", "paymentreference", "paymentamount", "paymentdiscount", "paymentnotes" ,"paymentrate", "paymentconverted", "customer", "chequeaccount", "chequenumber", "chequedate", "chequeamount", "chequeconvertedamount"));

		$result = $this->accounts_receivable->applyPayments($data_post);

		$dataArray = array("msg" => $result);
		echo json_encode($dataArray);
	}

	private function delete_payments()
	{
		$data_post = $this->input->post("voucher");

		/**
		* Delete Database
		*/
		$result = $this->accounts_receivable->deletePayments($data_post);

		if(empty($result))
			$msg = "success";
		else
			$msg = $result;

		$dataArray = array( "msg" => $msg );
		echo json_encode($dataArray);
	}

	private function load_payables()
	{
		$vendor = $this->input->post("customer");

		$table   = "accountsreceivable as main";
		$fields  = array("main.transactiondate as date, main.voucherno as voucher, main.vendor as vendor, main.balance as dueamount, main.amount as totalamount, main.stat as invoice_status");
		$cond    = "(main.voucherno != '' AND main.voucherno != '-') AND main.balance > 0 AND main.customer = '$customer' AND main.stat = 'posted'";
		$orderby = "main.voucherno DESC";

		$result = $this->accounts_receivable->retrieveData($table, $fields, $cond, "", $orderby);

		$table = "";

		if(!empty($result))
		{
			for($i = 0; $i < count($result); $i++)
			{
				$date			= $result[$i]->date;
				$date			= date("M d, Y",strtotime($date));
				$voucher		= $result[$i]->voucher;
				$customer		= $result[$i]->customer;
				$balance		= $result[$i]->dueamount; 
				$totalamount	= $result[$i]->totalamount;
				$invoice_status	= $result[$i]->invoice_status;

				$appliedamount	= $this->accounts_receivable->getValue("rv_application", array("SUM(amount) AS amount"),"arvoucherno = '$voucher' AND stat = 'posted'");
				$appliedamount  = $appliedamount[0]->amount;

				$table	.= '<tr>'; 
				$table	.= 	'<td class="col-md-1 text-center" style="vertical-align:middle;">';
				$table	.= 		'<input type="checkbox" name="checkBox[]" class = "icheckbox" row = '.$i.' toggleid = "0">'; //onClick="selectPayable('.$i.',0);"
				$table	.= 		'<input type="hidden" id="invoice['.$i.']" value="'.$voucher.'">';
				$table	.= 	'</td>';
				$table	.= 	'<td class="col-md-2 text-center" style="vertical-align:middle;" onClick="selectPayable('.$i.',1);">&nbsp;'.$date.'</td>';
				
				$table	.= '<td class="col-md-2 text-left" style="vertical-align:middle;" onClick="selectPayable('.$i.',1);">&nbsp;'.$voucher.'</td>';
				$table	.= '<td class="col-md-2 text-right" style="vertical-align:middle;" onClick="selectPayable('.$i.',1);">'.number_format($totalamount,2).'</td>';
				$table	.= '<td class="col-md-2 text-right" style="vertical-align:middle;" onClick="selectPayable('.$i.',1);">'.number_format($balance,2).'</td>';
				
				$table	.= '<td class="col-md-3 text-center pay" style="vertical-align:middle;">'
				.$this->ui->formField('text')
				->setSplit('', 'col-md-12')
				->setClass("input-sm text-right paymentamount")
				->setName('paymentamount['.$i.']')
				->setId('paymentamount['.$i.']')
				->setPlaceHolder("0.00")
				->setAttribute(array("maxlength" => "50", "disabled" => "disabled", "onBlur" => 'checkBalance(this.value,'.$i.'); formatNumber(this.id);', "onClick" => "SelectAll(this.id);"))
				->setValue("")
				->draw(true).
				'</td>';
				
				$table	.= '</tr>';

			}
		}
		else
		{
			$table	.= '<tr>';
			$table	.= 	'<td class="text-center" colspan="6">- No Records Found -</td>';
			$table	.= '</tr>';
		}

		$dataArray = array( "table" => $table );
		echo json_encode($dataArray);
	}

	private function apply_proforma()
	{
		$code       = $this->input->post("code");
		$ui         = $this->ui;
		$show_input = $this->show_input;
		
		// RETRIEVE ACCOUNT CODE
		$acc_entry_data     = array("id ind","accountname val");
		// $acc_entry_cond     = "accounttype != 'P'";
		$account_entry_list = $this->accounts_receivable->getValue("chartaccount", $acc_entry_data, "", "segment5");


		$dataArray		= $this->accounts_receivable->retrieveData("proforma_details",array('accountcodeid'),"proformacode = '$code'");
		$tempObj 		= array(
			'0'=>array("accountcodeid"=>"0")
			,
			'1'=>
			array("accountcodeid"=>"0")
		);
		
		$dataArray		= ($dataArray) ? $dataArray : $tempObj ;
		
		$row			= 1;
		$table 			= "";

		if(!empty($dataArray))
		{
			for($i = 0; $i < count($dataArray); $i++)
			{
				$accountcode = ($code != '' && $code != 'none') ? $dataArray[$i]->accountcodeid : '';
				
				$table	.= '<tr class="clone">';
				
				$table	.= '<td class = "remove-margin">';
				$table 	.= $ui->formField('dropdown')
				->setPlaceholder('Select One')
				->setSplit('', 'col-md-12')
				->setName("accountcode[".$row."]")
				->setId("accountcode[".$row."]")
				->setList($account_entry_list)
				->setValue($accountcode)
				->draw($show_input);
				$table	.= '</td>';
				
				$table	.= '<td class = "remove-margin">';
				$table  .= $ui->formField('text')
				->setSplit('', 'col-md-12')
				->setName('detailparticulars['.$row.']')
				->setId('detailparticulars['.$row.']')
				->setAttribute(array("maxlength" => "100"))
				->setValue("")
				->draw($show_input);
				$table	.= '</td>';
				
				$table	.= '<td class = "remove-margin">';
				$table  .= $ui->formField('text')
				->setSplit('', 'col-md-12')
				->setName('debit['.$row.']')
				->setId('debit['.$row.']')
				->setAttribute(array("maxlength" => "20", "onBlur" => "addAmountAll('debit'); formatNumber(this.id);", "onClick" => "SelectAll(this.id);", "onKeyPress" => "return isNumberKey2(e);"))
				->setClass("format_values_db format_values text-right")
				->setValue("0.00")
				->draw($show_input);
				$table	.= '</td>';
				
				$table	.= '<td class = "remove-margin">';
				$table 	.= $ui->formField('text')
				->setSplit('', 'col-md-12')
				->setName('credit['.$row.']')
				->setId('credit['.$row.']')
				->setAttribute(array("maxlength" => "20", "onBlur" => "addAmountAll('credit'); formatNumber(this.id);", "onClick" => "SelectAll(this.id);", "onKeyPress" => "return isNumberKey2(e);"))
				->setClass("format_values_cr format_values text-right")
				->setValue("0.00")
				->draw($show_input);
				$table	.= '</td>';
				
				$table	.= '<td class=" text-center">';
				$table	.= '<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete('.$row.');"><span class="glyphicon glyphicon-trash"></span></button>';
				$table	.= '</td>';
				
				$table	.= '</tr>';
				
				$row++;
			}
		}


		$returnArray = array( "table" => $table );
		echo json_encode($returnArray);

	}

	public function get_import(){
		header('Content-type: application/csv');
		$header = array('Document Set','Transaction Date','Due Date','Customer Code','Invoice No.','Reference No.','Notes','Account Name','Description','Debit','Credit');
		$return = "";
		
		$return .= '"' . implode('","',$header) . '"';
		$return .= "\n";

		echo $return;
	}

	private function save_import(){
		$file		= fopen($_FILES['file']['tmp_name'],'r') or exit ("File Unable to upload") ;

		$filedir	= $_FILES["file"]["tmp_name"];

		$file_types = array( "text/x-csv","text/tsv","text/comma-separated-values", "text/csv", "application/csv", "application/excel", "application/vnd.ms-excel", "application/vnd.msexcel", "text/anytext");

		$errmsg 	=	array();
		$proceed 	=	false;

		/**VALIDATE FILE IF CORRUPT**/
		if(!empty($_FILES['file']['error'])){
			$errmsg[] = "File being uploaded is corrupted.<br/>";
		}

		/**VALIDATE FILE TYPE**/
		if(!in_array($_FILES['file']['type'],$file_types)){
			$errmsg[]= "Invalid file type, file must be .csv.<br/>";
		}
		
		$headerArr = array('Document Set','Transaction Date','Due Date','Customer Code','Invoice No.','Reference No.','Notes','Account Name','Description','Debit','Credit');
		
		if( empty($errmsg) ) {
			$x = array_map('str_getcsv', file($_FILES['file']['tmp_name']));
			$error 	=	array();
			for ($n = 0; $n < count($x); $n++) {
				if($n==0 && empty($errmsg)) {
					$layout = count($headerArr);
					$template = count($x);
					$header = $x[$n];

					for ($m=0; $m< $layout; $m++){
						$template_header = $header[$m];
						
						$error = (empty($template_header) || !in_array($template_header,$headerArr)) ? "error" : "";
					}	
					
					$errmsg[]	= (!empty($error) || $error != "" ) ? "Invalid template. Please download the template from the system first.<br/>" : "";
					
					$errmsg		= array_filter($errmsg);

				}
				if ( $n >= 1 ) {
					$z[] = $x[$n];
				}
			}
			
			$line 				=	2;
			$post 				=	array();
			$warning 			=	array();
			$vouchlist 			= 	array();
			$h_vouchlist 		=	array();
			$datelist 			= 	array();
			$duedatelist 		=	array();
			$customerlist 		=	array();
			$invoicelist 		=	array();
			$referencelist 		=	array();
			$noteslist 			=	array();
			$accountlist 		=	array();
			$descriptions 		= 	array();
			$debitlist 			= 	array();
			$creditlist 		= 	array();
			$totaldebit 		=	array();
			$totalcredit 		=	array();							

			$close_date 		= 	$this->restrict->getClosedDate();

			if( empty($errmsg)  && !empty($z) ){
				$total_debit 	=	0;
				$total_credit 	=	0;
				$prev_no 		=	$prev_date 		=	$prev_ref 	=	$prev_notes 	=	$voucherno 		= "";
				$prev_duedate 	=	$prev_invno 	=	$prev_cust 	= 	"";
				foreach ($z as $key => $b) {
					if ( ! empty($b)) {	
						$jvno 			=	isset($b[0]) 					? 	$b[0] 										:	"";
						$transdate 		=	isset($b[1]) 					? 	$b[1] 										:	"";
						$transdate 		=	($transdate != "") 				?	$this->date->dateDbFormat($transdate)	:	"";
						$duedate 		=	isset($b[2]) 					? 	$b[2] 	:	"";
						$duedate 		=	($duedate != "") 				?	$this->date->dateDbFormat($duedate)		:	"";
						$customer 		=	isset($b[3]) 					?	htmlentities(trim($b[3]))	:	"";
						$invoiceno 		=	isset($b[4]) 					?	htmlentities(trim($b[4]))	:	"";
						$reference 		=	isset($b[5]) 					?	htmlentities(trim($b[5]))	:	"";
						$notes 			=	isset($b[6]) 					?	htmlentities(trim($b[6]))	:	"";
						$account 		=	isset($b[7]) 					?	htmlentities(trim($b[7]))	:	"";
						$account 		= 	str_replace('&ndash;', '-', $account);
						$description 	=	isset($b[8]) 					?	htmlentities(trim($b[8]))	:	"";
						$debit 			=	isset($b[9]) && !empty($b[9]) 	?	$b[9]	:	0;
						$credit 		=	isset($b[10]) && !empty($b[10])	?	$b[10]	:	0;
						//Check if account Name exists
						$acct_exists 	=	$this->accounts_receivable->check_if_exists('id','chartaccount'," accountname = '$account' ");
						$acct_count 	=	$acct_exists[0]->count;
						
						if(!empty($account)){
							if( $acct_count <= 0 ) {
								$errmsg[]	= "Account Name [<strong>$account</strong>] on <strong>row $line</strong> does not exist.<br/>";
								$errmsg		= array_filter($errmsg);
							}
						}else{
							$errmsg[]	= "Account Name on <strong>row $line</strong> should not be empty.<br/>";
							$errmsg		= array_filter($errmsg);
						}
						if( $key == 0 ){
							// Check if Document Set is not empty. 
							if($jvno == ""){
								$errmsg[]	= "Document Set on <strong>row $line</strong> should not be empty.<br/>";
								$errmsg		= array_filter($errmsg);
							} else {
								$voucherno		= $this->seq->getValue('AR');
							}
							// Check if Transaction Date is not Empty 
							if($transdate == ''){
								$errmsg[]	= "Transaction Date on <strong>row $line</strong> should not be empty.<br/>";
								$errmsg		= array_filter($errmsg);
							}
							//Check if Transaction Date is not within Closed Date Period
							if($transdate <= $close_date){
								$errmsg[]	= "Transaction Date [<strong>$transdate</strong>] on <strong>row $line</strong> must not be within the Closed Period.<br/>";
								$errmsg		= array_filter($errmsg);
							}
							// Check if Due Date is not Empty 
							if($duedate == ''){
								$errmsg[]	= "Due Date on <strong>row $line</strong> should not be empty.<br/>";
								$errmsg		= array_filter($errmsg);
							}
							//Check if Due Date is not within Closed Date Period
							if($duedate <= $close_date){
								$errmsg[]	= "Due Date [<strong>$duedate</strong>] on <strong>row $line</strong> must not be within the Closed Period.<br/>";
								$errmsg		= array_filter($errmsg);
							}
							//Check if Customer Code is not empty
							if($customer == ''){
								$errmsg[]	= "Customer on <strong>row $line</strong> should not be empty.<br/>";
								$errmsg		= array_filter($errmsg);
							} else {
								//Check if Customer Code exists 
								$cust_exists 	=	$this->accounts_receivable->check_if_exists('partnercode','partners'," partnercode = '$customer' ");
								$cust_count 	=	$cust_exists[0]->count;	
								if( $cust_count <= 0 ) {
									$errmsg[]	= "Customer Code [<strong>$customer</strong>] on <strong>row $line</strong> does not exist.<br/>";
									$errmsg		= array_filter($errmsg);
								}
							}
							//Check if Account is not empty
							if($account == ''){
								$errmsg[]	= "Account on <strong>row $line</strong> should not be empty.<br/>";
								$errmsg		= array_filter($errmsg);
							}
							//Check if Debit / Credit has an amount
							if($debit == '' && $credit == ''){
								$errmsg[]	= "Debit or Credit on <strong>$line</strong> should have a value.<br/>";
								$errmsg		= array_filter($errmsg);
							}
						} else {
							if ($jvno == '') {
								$jvno = $prev_no;
							} else if ($jvno != $prev_no) {
								$total_credit 	= 0;
								$total_debit 	= 0;
								$voucherno		= $this->seq->getValue('AR');
							} 
							if ($jvno == $prev_no) {
								//Check Transaction Date if the same
								if($transdate == ''){
									$transdate 	= $prev_date;
								} else {
									//Checks if Transaction Date is the same for the same Document Set
									if ($transdate != $prev_date) {
										$errmsg[]	= "Transaction Date [<strong>$transdate</strong>] on <strong>row $line</strong> should be the same for vouchers # <strong>$jvno</strong>.<br/>";
										$errmsg		= array_filter($errmsg);
									}
								}
								//Check if Transaction Date is not within Closed Date Period
								if(($transdate!="") && $transdate <= $close_date){
									$errmsg[]	= "Transaction Date [<strong>$transdate</strong>] on <strong>row $line</strong> must not be within the Closed Period.<br/>";
									$errmsg		= array_filter($errmsg);
								}
								//Check Due Date is the same
								if($duedate == ''){
									$duedate 	= $prev_duedate;
								} else {
									//Checks if Due Date is the same for the same Document Set
									if ($duedate != $prev_duedate) {
										$errmsg[]	= "Due Date [<strong>$duedate</strong>] on <strong>row $line</strong> should be the same for vouchers # <strong>$jvno</strong>.<br/>";
										$errmsg		= array_filter($errmsg);
									}
								}	
								//Check if Due Date is not within Closed Date Period
								if($duedate <= $close_date){
									$errmsg[]	= "Due Date [<strong>$duedate</strong>] on <strong>row $line</strong> must not be within the Closed Period.<br/>";
									$errmsg		= array_filter($errmsg);
								}
								//Compare Transaction Date and Due Date. Due Date must not be earlier than Transaction date. 
								if( ($duedate != "" && $transdate != "") && ($transdate > $duedate)){
									$errmsg[]	= "Due Date [<strong>$duedate</strong>] on <strong>row $line</strong> must not be earlier than the Transaction Date [<strong>$transdate</strong>].<br/>";
									$errmsg		= array_filter($errmsg);
								}
								//Check the Customer if the same
								if($customer == ''){
									$customer 	=	$prev_cust;
								}  
								//Checks if Customer is the same for the same Document Set
								if ($customer != $prev_cust) {
									$errmsg[]	= "Customer Code [<strong>$customer</strong>] on <strong>row $line</strong> should be the same for vouchers # <strong>$jvno</strong>.<br/>";
									$errmsg		= array_filter($errmsg);
								}
								//Check if Customer Code exists 
								$cust_exists 	=	$this->accounts_receivable->check_if_exists('partnercode','partners'," partnercode = '$customer' ");
								$cust_count 	=	$cust_exists[0]->count;	
								if( $cust_count <= 0 ) {
									$errmsg[]	= "Customer Code [<strong>$customer</strong>] on <strong>row $line</strong> does not exist.<br/>";
									$errmsg		= array_filter($errmsg);
								}
								//Check the Invoice #
								if($invoiceno == ''){
									$invoiceno 	=	$prev_invno;
								} else {
									//Checks if the Invoice No is the same for the same Document Set
									if ($invoiceno != $prev_invno) {
										$errmsg[]	= "Invoice No. [<strong>$invoiceno</strong>] on <strong>row $line</strong> should be the same for vouchers # <strong>$jvno</strong>.<br/>";
										$errmsg		= array_filter($errmsg);
									}
								}
								//Check the Reference #
								if($reference == ''){
									$reference 	=	$prev_ref;
								} else {
									//Checks if the Reference No. is the same for the same Document Set
									if ($reference != $prev_ref) {
										$errmsg[]	= "Reference No. [<strong>$reference</strong>] on <strong>row $line</strong> should be the same for vouchers # <strong>$jvno</strong>.<br/>";
										$errmsg		= array_filter($errmsg);
									}
								}
								//Check the Notes
								if($notes == ''){
									$notes 	=	$prev_notes;
								} else {
									//Checks if the Notes is the same for the same Document Set
									if ($notes != $prev_notes) {
										$errmsg[]	= "Notes [<strong>$notes</strong>] on <strong>row $line</strong> should be the same for vouchers # <strong>$jvno</strong>.<br/>";
										$errmsg		= array_filter($errmsg);
									}
								}
								//Check if Credit != 0 && Debit != 0
								if( $total_credit == 0 && $total_debit == 0 ){
									$errmsg[]	= "The Total Debit and Total Credit on <strong>row $line</strong> must have a value.<br/>";
									$errmsg		= array_filter($errmsg);
								}
							}
						}

						$total_credit 	+=	$credit;
						$total_debit 	+=	$debit;

						//Check if Debit Total == Credit Total
						if ( ! isset($z[$key + 1]) || ($jvno != $z[$key + 1][0] && $z[$key + 1][0] != '')) {
							$totaldebit[] 	= $total_debit;
							$totalcredit[]	= $total_credit;
							if ($total_credit != $total_debit){
								$errmsg[]	= "The Total Debit and Total Credit on <strong>row $line</strong> must be equal.<br/>";
								$errmsg		= array_filter($errmsg);
							}
						}

						if(empty($errmsg)){
							$vouchlist[] 		= $voucherno;
							$accountlist[] 		= $this->accounts_receivable->getAccountId($account);
							$descriptions[] 	= $description;
							$debitlist[] 		= $debit; 
							$creditlist[] 		= $credit;

							if( !isset($h_vouchlist) || !in_array($voucherno, $h_vouchlist) ){
								$h_vouchlist[] 		= $voucherno;
								$datelist[] 		= $transdate;
								$duedatelist[] 		= $duedate;
								$customerlist[] 	= $customer;
								$invoicelist[] 		= $invoiceno;
								$noteslist[] 		= $notes;
								$referencelist[] 	= $reference;
							}
						}

						$prev_no 		= $jvno;
						$prev_date		= $transdate;
						$prev_duedate	= $duedate;
						$prev_cust 		= $customer;
						$prev_invno 	= $invoiceno;
						$prev_ref 		= $reference;
						$prev_notes 	= $notes;
						
						$line++;
					}
				}

				if( empty($errmsg) ) {

					$header 	=	array(
						'voucherno' 		=> $h_vouchlist,
						'transactiondate' 	=> $datelist,
						'duedate' 			=> $duedatelist,
						'customer' 			=> $customerlist,
						'invoiceno' 		=> $invoicelist,
						'referenceno'		=> $referencelist,
						'particulars'		=> $noteslist,
						'amount' 			=> $totaldebit,
						'convertedamount' 	=> $totaldebit,
						'balance' 			=> $totaldebit
					); 
					
					foreach ($header['voucherno'] as $key => $row) {
						$header['currencycode'][] 	= "PHP";
						$header['exchangerate'][] 	= 1;
						$header['stat'][] 			= "posted";
						$header['transtype'][] 		= "AR";
						$header['source'][] 		= "AR";
						$header['lockkey'][] 	= "import";
					}

					foreach ($header['transactiondate'] as $key => $row) {
						$period					= date("n",strtotime($row));
						$fiscalyear				= date("Y",strtotime($row));
						$header['period'][] 	= $period;
						$header['fiscalyear'][] = $fiscalyear;
					}	
					// var_dump($header);

					$proceed  			= $this->accounts_receivable->save_import("accountsreceivable",$header);
					
					$details = array(
						'voucherno' 		=> $vouchlist,
						'accountcode'		=> $accountlist,
						'detailparticulars'	=> $descriptions,
						'debit' 			=> $debitlist,
						'credit' 			=> $creditlist,
						'converteddebit' 	=> $debitlist,
						'convertedcredit'	=> $creditlist
					);

					$linenum = 1;

					foreach ($details['voucherno'] as $key => $row) {
						$details['currencycode'][] 	= "PHP";
						$details['exchangerate'][] 	= 1;
						$details['linenum'][] 	= $linenum;
						$details['stat'][] 		= "posted";
						$details['transtype'][] = "AR";
						$details['source'][] 	= "AR";
						$linenum++;
						if (isset($details['voucherno'][$key + 1]) && $details['voucherno'][$key + 1] != $row) {
							$linenum = 1;
						}
					}

					if($proceed){
						$proceed 	=	$this->accounts_receivable->save_import("ar_details",$details);

						if($proceed){
							$this->logs->saveActivity("Imported Account Receivables.");
						}
					} 
				}
			}
		}

		$error_messages		= implode(' ', $errmsg);
		$warning_messages	= implode(' ', $warning);

		$resultArray 	=	 array("proceed" => $proceed,"errmsg"=>$error_messages, "warning"=>$warning_messages);
		echo json_encode($resultArray);
	}

}