<?php
class controller extends wc_controller 
{

	public function __construct()
	{
		parent::__construct();
		$this->url 			    = new url();
		$this->accounts_payable = new accounts_payable();
		$this->input            = new input();
		$this->ui 			    = new ui();
		$this->logs  			= new log;
		$this->view->title      = 'Accounts Payable';
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
		$data["vendor_list"]  = $this->accounts_payable->retrieveVendorList();

		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->accounts_payable->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

		// For Import
		$errmsg 			= array();
		if(isset($_FILES['import_csv']))
		{
			$headerArray = array('Document Date','Invoice Number','Vendor','Due Date','Amount','Notes');
			
			// Open File 
			$filewrite	=  fopen( 'modules/financials_module/files/error_payables_import.txt', "w+");
			
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
					$vendor			= addslashes(htmlentities(trim($file_data[2])));
					$duedate		= addslashes(htmlentities(trim($file_data[3])));
					$amount			= addslashes(htmlentities(trim($file_data[4])));
					$notes			= addslashes(htmlentities(trim($file_data[5])));
					
					$amount			= str_replace(',','',$amount);
			
					$datecheck 		= date_parse($documentdate);
					$datecheck1 	= date_parse($duedate);

					/**VALIDATE VENDOR**/
					$vendorcode		= $this->accounts_payable->getValue("partners",array("partnercode"),"CONCAT( first_name, ' ', last_name ) = '$vendor'");

					$vendorcode 	= $vendorcode[0]->partnercode;

					if(empty($vendorcode))
					{
						$errmsg[] 	= "Vendor [ <strong>".stripslashes($vendorcode)."</strong> ] on row $row does not exist.";
						fwrite($filewrite, "Vendor [ ".stripslashes($vendorcode)." ] on row $row does not exist.\n");
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
					$docData[$i]['vendor']				= $vendorcode;
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
				$file_import_result = $this->accounts_payable->fileInsert($docData);
				$data["file_import_result"] = $file_import_result;
			}
		}
		// End Import

		$this->view->load('accounts_payable/accounts_payable_list', $data);
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
			"transactiondate",
			"tinno",
			"address1",
			"duedate",
			"particulars",
			"terms",
			"date",
			"invoiceno"
		));

		$data["ui"]                 = $this->ui;
		$data['show_input']         = $this->show_input;
		$data['button_name']        = "Save";
		$data["task"] 		        = "create";
		$data["ajax_post"] 	        = "";
		$data["row_ctr"] 			= 0;
		$data["exchangerate"]       = "1.00";
		$data["transactiondate"]    = $this->date->dateFormat();
		$data["duedate"]      		= $this->date->dateFormat();

		// Retrieve vendor list
		$data["vendor_list"]          = $this->accounts_payable->retrieveVendorList();

		// Retrieve proforma list
		$data["proforma_list"]        = $this->accounts_payable->retrieveProformaList();

		// Retrieve business type list
		$bus_type_data                = array("code ind", "value val");
		$bus_type_cond                = "type = 'businesstype'";
		$data["business_type_list"]   = $this->accounts_payable->getValue("wc_option", $bus_type_data, $bus_type_cond, false);

		// Retrieve business type list
		$acc_entry_data               = array("id ind","accountname val");
		$acc_entry_cond               = "accounttype != 'P'";
		$data["account_entry_list"]   = $this->accounts_payable->getValue("chartaccount", $acc_entry_data, $acc_entry_cond, "segment5");

		// Retrieve payable account list
		$pay_account_data 			  = array("id ind", "accountname val");
		$pay_account_cond 			  = "accountclasscode = 'ACCPAY' AND accounttype != 'P'";
		$data["payable_account_list"] = $this->accounts_payable->getValue("chartaccount", $pay_account_data, $pay_account_cond, "accountname");

		// Retrieve generated ID
		$gen_value                    = $this->accounts_payable->getValue("accountspayable", "COUNT(*) as count", "voucherno != ''");	
		$data["generated_id"]         = (!empty($gen_value[0]->count)) ? 'TMP_'.($gen_value[0]->count + 1) : 'TMP_1';

		// Process form when form is submitted
		$data_validate = $this->input->post(array('referenceno', "h_voucher_no", "vendor", "document_date", "h_save", "h_save_new", "h_save_preview"));

		if (!empty($data_validate["vendor"]) && !empty($data_validate["document_date"])) 
		{
			$voucherno = (isset($data_validate['h_voucher_no']) && (!empty($data_validate['h_voucher_no']))) ? htmlentities(trim($data_validate['h_voucher_no'])) : "";

			$isExist = $this->accounts_payable->getValue("accountspayable", array("voucherno"), "voucherno = '$voucherno'");
			
			if($isExist[0]->voucherno)
			{
				/**UPDATE MAIN TABLES**/
				$generatedvoucher			= $seq->getValue('AP'); 
			
				$update_info				= array();
				$update_info['voucherno']	= $generatedvoucher;
				$update_info['stat']		= 'posted';
				$update_condition			= "voucherno = '$voucherno'";
				$updateTempRecord			= $this->accounts_payable->editData($update_info,"accountspayable",$update_condition);
				$updateTempRecord			= $this->accounts_payable->editData($update_info,"ap_details",$update_condition);
			}
			
			// For Admin Logs
			$this->logs->saveActivity("Add New Accounts Payable [$generatedvoucher]");

			if(!empty($data_validate['h_save']))
			{
				$this->url->redirect(BASE_URL . 'financials/accounts_payable');
			}
			else if(!empty($data_validate['h_save_preview']))
			{
				$this->url->redirect(BASE_URL . 'financials/accounts_payable/view/' . $generatedvoucher);
			}
			else
			{
				$this->url->redirect(BASE_URL . 'financials/accounts_payable/create');
			}
		
		}
		
		$this->view->load('accounts_payable/accounts_payable', $data);
	}

	public function view($sid)
	{
		$cmp 					   = $this->companycode;
	
		// Retrieve data
		$data         			   = $this->accounts_payable->retrieveEditData($sid);
	
		$data["ui"]   			   = $this->ui;
		$data['show_input'] 	   = false;
		$data["button_name"] 	   = "Edit";
		$data["task"] 	  		   = "view";
		$data["sid"] 			   = $sid;
		$data["date"] 			   = $this->date->dateFormat();//date("M d, Y");

		$data["business_type_list"] = array();
		$data["account_entry_list"] = array();

		// Main
		$data["v_voucherno"]       = $data["main"]->voucherno;
		$data["v_vendorcode"]      = $data["main"]->vendor;
		$data["v_convertedamount"] = $data["main"]->convertedamount;
		$data["v_exchangerate"]    = $data["main"]->exchangerate;
		$data["v_transactiondate"] = $this->date->dateFormat($data["main"]->transactiondate); 
		$data["v_duedate"]         = $this->date->dateFormat($data["main"]->duedate);
		$data["v_referenceno"]     = $data["main"]->referenceno;
		$data["v_invoiceno"]       = $data["main"]->invoiceno;
		$data["v_balance"]     	   = $data["main"]->balance;
		$data['v_notes'] 		   = $data["main"]->particulars;

		// Vendor/Customer Details
		$data["v_vendor"] 		   = (!empty($data["cust"]->name)) ? $data["cust"]->name : "";
		$data["v_email"] 		   = (!empty($data["cust"]->email)) ? $data["cust"]->email : "";
		$data["v_tinno"] 		   = (!empty($data["cust"]->tinno)) ? $data["cust"]->tinno : "";
		$data["v_address1"] 	   = (!empty($data["cust"]->address1)) ? $data["cust"]->address1 : "";

		/**
		* Get the total forex amount applied
		*/
		$forex_result 			  = $this->accounts_payable->getValue("pv_application", array("SUM(forexamount) as forexamount"), "apvoucherno = '$sid' AND stat = 'posted'");
		$forexamount			  = ($forex_result[0]->forexamount != '') ? $forex_result[0]->forexamount : 0;

		$data["forexamount"] 	  = $forexamount;

		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->accounts_payable->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);
		$data["noCashAccounts"]  = false;
		
		if(empty($data["cash_account_list"]))
		{
			$data["noCashAccounts"]  = true;
		}

		// Get Prefix
		$ap_prefix 		  		 = $this->accounts_payable->getValue("wc_sequence_control", array("prefix"), "code = 'AP'");
		$data["prefix"] 		 = $ap_prefix[0]->prefix;

		$data['show_paymentdetails'] 	=	(!empty($data['payments'] && !is_null($data['payments']))) 		?  	1 	: 0;
		$data['show_chequedetails'] 	=	(!empty($data['rollArrayv'] && !is_null($data['rollArrayv']))) 	?  	1 	: 0;
		
		/**
		 * Status Badge
		 */
		$balance 	= $data["main"]->balance;
		$amount 	= $data["main"]->amount;
		if($balance != $amount && $balance != 0){
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

		$this->view->load('accounts_payable/accounts_payable_view', $data);
	}

	public function edit($sid)
	{
		$cmp 		   		   = $this->companycode;
		$data         		   = $this->accounts_payable->retrieveEditData($sid);

		$data["ui"]            = $this->ui;
		$data['show_input']    = $this->show_input;
		$data["task"] 		   = "edit";

		$data["generated_id"]  = $sid;
		$data["sid"] 		   = $sid;
		$data["date"] 		   = date("M d, Y");

		// Retrieve vendor list
		$data["vendor_list"]          = $this->accounts_payable->retrieveVendorList();

		// Retrieve proforma list
		$data["proforma_list"]        = $this->accounts_payable->retrieveProformaList();

		// Retrieve business type list
		$bus_type_data                = array("code ind", "value val");
		$bus_type_cond                = "type = 'businesstype'";
		$data["business_type_list"]   = $this->accounts_payable->getValue("wc_option", $bus_type_data, $bus_type_cond, false);

		// Retrieve business type list
		$acc_entry_data               = array("id ind","accountname val");
		$acc_entry_cond               = "accounttype != 'P'";
		$data["account_entry_list"]   = $this->accounts_payable->getValue("chartaccount", $acc_entry_data, $acc_entry_cond, "segment5");

		// Retrieve payable account list
		$pay_account_data 			  = array("id ind", "accountname val");
		$pay_account_cond 			  = "accountclasscode = 'ACCPAY' AND accounttype != 'P'";
		$data["payable_account_list"] = $this->accounts_payable->getValue("chartaccount", $pay_account_data, $pay_account_cond, "accountname");
		
		// Header Data
		$data["voucherno"]       = $data["main"]->voucherno;
		$data["referenceno"]     = $data["main"]->referenceno;
		$data["invoiceno"]       = $data["main"]->invoiceno;
		$data["vendorcode"]      = $data["main"]->vendor;
		$data["exchangerate"]    = $data["main"]->exchangerate;
		$data["transactiondate"] = $this->date->dateFormat($data["main"]->transactiondate);
		$data["particulars"]     = $data["main"]->particulars;

		$data["terms"] 		 = $data["cust"]->terms;
		$data["tinno"] 		 = $data["cust"]->tinno;
		$data["address1"] 	 = $data["cust"]->address1;
		$data["duedate"]     = $this->date->dateFormat($data["main"]->duedate); 
		
		// Process form when form is submitted
		$data_validate = $this->input->post(array('referenceno', "h_voucher_no", "vendor", "document_date", "h_save", "h_save_new", "h_save_preview"));

		if (!empty($data_validate["vendor"]) && !empty($data_validate["document_date"])) 
		{
			// For Admin Logs
			$this->logs->saveActivity("Updated Accounts Payable [$sid]");

			if(!empty($data_validate['h_save']))
			{
				$this->url->redirect(BASE_URL . 'financials/accounts_payable');
			}
			else if(!empty($data_validate['h_save_preview']))
			{
				$this->url->redirect(BASE_URL . 'financials/accounts_payable/view/' . $sid);
			}
			else
			{
				$this->url->redirect(BASE_URL . 'financials/accounts_payable/create');
			}	 
		}

		$this->view->load('accounts_payable/accounts_payable', $data);
	}

	public function print_preview($voucherno) 
	{
		$companycode = COMPANYCODE;

		// Retrieve Document Info
		$sub_select = $this->accounts_payable->retrieveData("pv_application", array("SUM(amount) AS amount"), "apvoucherno = '$voucherno'");

		// Total paid amount
		$sub_select[0]->amount;

		// Retrieve sum of credit in ap_details
		$apamount 		= $this->accounts_payable->retrieveData("ap_details", "SUM(credit) AS amount", "voucherno = '$voucherno'");
		$apamount[0]->amount;

		$docinfo_table  = "accountspayable as ap";
		$docinfo_fields = array('ap.transactiondate AS documentdate','ap.voucherno AS voucherno',"CONCAT( first_name, ' ', last_name )","IF('{$sub_select[0]->amount}' != \"\", '{$sub_select[0]->amount}', '{$apamount[0]->amount}') AS amount",'ap.amount AS apamount', "'' AS referenceno", "particulars AS remarks", "p.partnername AS vendor");
		$docinfo_join   = "partners as p ON p.partnercode = ap.vendor AND p.companycode = ap.companycode";
		$docinfo_cond 	= "ap.voucherno = '$voucherno'";

		$documentinfo  	= $this->accounts_payable->retrieveData($docinfo_table, $docinfo_fields, $docinfo_cond, $docinfo_join);

		$vendor 	    = $documentinfo[0]->vendor;

		// Retrieve Document Details
		$docdet_table   = "ap_details as dtl";
		$docdet_fields  = array("chart.segment5 as accountcode", "chart.accountname as accountname", "SUM(dtl.debit) as debit","SUM(dtl.credit) as credit");
		$docdet_join    = "chartaccount as chart ON chart.id = dtl.accountcode AND chart.companycode = dtl.companycode";
		$docdet_cond    = "dtl.voucherno = '$voucherno'";
		$docdet_groupby = "dtl.accountcode";
		$docdet_orderby = "CASE WHEN dtl.debit > 0 THEN 1 ELSE 2 END, dtl.linenum";
		
		$documentdetails = $this->accounts_payable->retrieveData($docdet_table, $docdet_fields, $docdet_cond, $docdet_join, $docdet_orderby, $docdet_groupby);

		// Retrieve Payment Details
		$paymentArray	 = $this->accounts_payable->retrievePaymentDetails($voucherno);

		// Retrieve Cheque Details
		$pv_v 		  = "";
		$pv_voucherno = $this->accounts_payable->getValue("pv_application", array("voucherno"), "apvoucherno = '$voucherno'");
		
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
			$chequeArray = $this->accounts_payable->retrieveData($cheque_table, $cheque_fields, $cheque_cond, $cheque_join);
		}
		
		// Setting for PDFs
		$print = new print_voucher_model('P', 'mm', 'Letter');
		$print->setDocumentType('Accounts Payable')
				->setDocumentInfo($documentinfo[0])
				->setVendor($vendor)
				->setPayments($paymentArray)
				->setDocumentDetails($documentdetails)
				->setCheque($chequeArray)
				->drawPDF('ap_voucher_' . $voucherno);
	}

	public function ajax($task)
	{
		header('Content-type: application/json');

		if ($task == 'save_payable_data') 
		{
			$this->add();
		}
		else if ($task == 'update') 
		{
			$this->update();
		}
		else if ($task == 'delete_row') 
		{
			$this->delete_row();
		}
		else if ($task == 'ajax_list') 
		{
			$this->ajax_list();
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
			$this->apply_payments();
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
		else if ($task == 'ajax_delete')
		{
			$this->delete_invoice();
		}
	}

	private function delete_invoice()
	{
		$vouchers 		= $this->input->post('delete_id');
		$invoices = "'" . implode("','", $vouchers) . "'";
		$data['stat'] 	= "cancelled";
		
		$cond 			= " voucherno IN ($invoices) ";

		$result 		= $this->accounts_payable->updateData($data, "accountspayable", $cond);

		if( $result )
		{
			$result 	= $this->accounts_payable->updateData($data, "ap_details", $cond);
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
		$data_post = $this->input->post(array("daterangefilter", "vendor", "filter", "search", "sort"));

		$list   = $this->accounts_payable->retrieveList($data_post);
		
		$table  = "";

		if( !empty($list->result) ) :
			foreach($list->result as $key => $row)
			{
				$date        = $row->transactiondate;
				$date        = $this->date->dateFormat($date);
				$voucher     = $row->voucherno; 
				$balance     = $row->balance; 
				$amount	  	 = $row->amount; 
				$vendor		 = $row->vendor; 
				$referenceno = $row->referenceno; 

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

				$show_edit 		= ($balance == $amount);
				$show_delete 	= ($balance == $amount);
				$show_payment 	= ($balance != 0);
				$dropdown = $this->ui->loadElement('check_task')
							->addView()
							->addEdit($show_edit)
							->addOtherTask(
								'Issue Payment',
								'credit-card',
								$show_payment
							)
							->addDelete($show_delete)
							->addCheckbox($show_delete)
							->setValue($voucher)
							->setLabels(array('delete' => 'Cancel'))
							->draw();
				$viewlink		= BASE_URL . "financials/accounts_payable/view/$voucher";
				$editlink		= BASE_URL . "financials/accounts_payable/edit/$voucher";
				$voucherlink	= MODULE_URL . "print_preview/$voucher";
				$paymentlink	= BASE_URL . "financials/accounts_payable/view/$voucher#payment";
			
				$table	.= '<tr>';
				$table	.= '<td class="text-center" style="vertical-align:middle;">'.$dropdown.'</td>';
				$table	.= '<td class="text-center" style="vertical-align:middle;">'.$date.'</td>';
				$table	.= '<td class="text-left" style="vertical-align:middle;">&nbsp;'.$voucher.'</td>';
				$table	.= '<td class="text-left" style="vertical-align:middle;">&nbsp;'.$vendor.'</td>';
				$table	.= '<td class="text-left" style="vertical-align:middle;">&nbsp;'.$referenceno.'</td>';
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

		$result    = $this->accounts_payable->insertData($data_post);

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
		$result = $this->accounts_payable->editData($data["fields"], $data["table"], $data["condition"]);

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
		$result = $this->accounts_payable->deleteData($data_post);

		if($result)
			$msg = "success";
		else
			$msg = "The system has encountered an error in deleting. Please contact admin to fix this issue.";

		$dataArray = array( "msg" => $msg );
		echo json_encode($dataArray);
	}

	private function export()
	{
		$data_get = $this->input->get(array("daterangefilter", "vendor", "filter", "search"));
		$data_get['daterangefilter'] = str_replace(array('%2F', '+'), array('/', ' '), $data_get['daterangefilter']);
		$result = $this->accounts_payable->fileExport($data_get);
		$header = array("Document Date", "Voucher No", "Vendor", "Invoice No", "Amount", "Balance", "Notes"); 
		$csv 	= '';

		$filename = "export_accounts_payable.csv";
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
			$result = $this->accounts_payable->getValue("partners", $data_var, $cond);

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
			$result = $this->accounts_payable->getValue("chartaccount", $data_var, $cond);

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
			$result = $this->accounts_payable->saveDetails("partners", $data_post, "vendordetail");
		}
		else if ($data_cond == "newVendor")
		{
			$data_var  = array("partnercode", "vendor_name", "email", "address", "businesstype", "tinno", "terms", "h_querytype");
			$data_post = $this->input->post($data_var);
			
			/**
			* Save Vendor Detials
			*/
			$result = $this->accounts_payable->saveDetails("partners", $data_post, "newVendor");
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
		$data_post = $this->input->post(array("invoiceno", "paymentdate", "paymentnumber", "paymentaccount", "paymentmode", "paymentreference", "paymentamount", "paymentdiscount", "paymentnotes" ,"paymentrate", "paymentconverted", "vendor", "chequeaccount", "chequenumber", "chequedate", "chequeamount", "chequeconvertedamount"));

		$result = $this->accounts_payable->applyPayments($data_post);

		$dataArray = array("msg" => $result);
		echo json_encode($dataArray);
	}

	private function delete_payments()
	{
		$data_post = $this->input->post("voucher");

		/**
		* Delete Database
		*/
		$result = $this->accounts_payable->deletePayments($data_post);

		if(empty($result))
			$msg = "success";
		else
			$msg = $result;

		$dataArray = array( "msg" => $msg );
		echo json_encode($dataArray);
	}

	private function load_payables()
	{
		$vendor = $this->input->post("vendor");

		$table   = "accountspayable as main";
		$fields  = array("main.transactiondate as date, main.voucherno as voucher, main.vendor as vendor, main.balance as dueamount, main.amount as totalamount, main.stat as invoice_status");
		$cond    = "(main.voucherno != '' AND main.voucherno != '-') AND main.balance > 0 AND main.vendor = '$vendor' AND main.stat = 'posted'";
		$orderby = "main.voucherno DESC";

		$result = $this->accounts_payable->retrieveData($table, $fields, $cond, "", $orderby);

		$table = "";

		if(!empty($result))
		{
			for($i = 0; $i < count($result); $i++)
			{
				$date			= $result[$i]->date;
				$date			= date("M d, Y",strtotime($date));
				$voucher		= $result[$i]->voucher;
				$vendor			= $result[$i]->vendor;
				$balance		= $result[$i]->dueamount; 
				$totalamount	= $result[$i]->totalamount;
				$invoice_status	= $result[$i]->invoice_status;

				$appliedamount	= $this->accounts_payable->getValue("pv_application", array("SUM(amount) AS amount"),"apvoucherno = '$voucher' AND stat = 'posted'");
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
		$account_entry_list = $this->accounts_payable->getValue("chartaccount", $acc_entry_data, "", "segment5");


		$dataArray		= $this->accounts_payable->retrieveData("proforma_details",array('accountcodeid'),"proformacode = '$code'");
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

}