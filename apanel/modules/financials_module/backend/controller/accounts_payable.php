<?php
class controller extends wc_controller 
{

	public function __construct()
	{
		parent::__construct();
		$this->url 			    = new url();
		$this->accounts_payable = new accounts_payable();
		$this->restrict 		= new financials_restriction_model();
		$this->report_model		= $this->checkoutModel('reports_module/report_model');
		$this->input            = new input();
		$this->ui 			    = new ui();
		$this->logs  			= new log;
		$this->view->title      = 'Accounts Payable';
		$this->show_input 	    = true;

		$this->companycode      = COMPANYCODE;
		$this->username 		= USERNAME;

		$this->fields = array(
			"voucherno",
			'job_no',
			'assetid',
			'months',
			"transactiondate",
			'currencycode',
			"referenceno",
			'exchangerate',
			'stat',
			"proformacode",
			'transtype',
			'invoicedate',
			"duedate",
			"vendor",
			"invoiceno",
			'amount',
			'convertedamount',
			'fiscalyear',
			"terms",
			"particulars",
			'balance'
		);

		$this->apdetails = array(
			'voucherno',
			'transtype',
			'budgetcode',
			'accountcode',
			'debit',
			'credit',
			'taxbase_amount',
			'taxcode',
			'checkstat',
			'linenum',
			'source',
			'detailparticulars',
			'currencycode',
			'converteddebit',
			'convertedcredit'
		);

		$this->jobs = array(
			'id',
			'voucherno',
			'job_no'
		);

		$this->actualbudget = array(
			'id',
			'voucherno',
			'budget_code',
			'accountcode',
			'actual'
		);
	}

	public function listing() {
		$this->view->title = 'Accounts Payable';
		$all = (object) array('ind' => 'null', 'val' => 'Filter: All');
		$data['ui'] = $this->ui;
		$data['show_input'] = true;
		$data['vendor_list'] = $this->accounts_payable->retrieveVendorList('');
		$this->view->load('accounts_payable/accounts_payable_list', $data);
	}

	public function create()
	{	
		$this->view->title			= 'Create Accounts Payable';
		$this->view->addCSS(array(
			'jquery.fileupload.css'
		)
		);  
		$this->view->addJS(
			array(
				'jquery.dirrty.js',
				'jquery.ui.widget.js',
				'jquery.iframe-transport.js',
				'jquery.fileupload.js',
				'jquery.fileupload-process.js',
				'jquery.fileupload-validate.js',
				'jquery.fileupload-ui.js'
			)
		);
		$data = $this->input->post($this->fields);
		$data["ui"]                 = $this->ui;
		$data['show_input']         = true;
		$data["ajax_task"] 		    = "ajax_create";
		$data["ajax_post"] 	        = "";
		$data['budget_list'] = $this->accounts_payable->getBudgetCodes();
		$close_date 				= $this->accounts_payable->getClosedDate();
		$data['close_date']			= $close_date;
		$data["transactiondate"]    = $this->date->dateFormat();
		$data["currencycode"]    	= 'PHP';
		$data["duedate"]      		= $this->date->dateFormat();
		$data['currencycodes'] = $this->accounts_payable->getCurrencyCode();
		$data["vendor_list"]          = $this->accounts_payable->retrieveVendorList();
		$data["proforma_list"]        = $this->accounts_payable->retrieveProformaList($data);
		$data['account_list'] = $this->accounts_payable->retrieveAccounts();
		$data['asset_list'] = $this->accounts_payable->retrieveAssetId();
		$data["business_type_list"]   = $this->accounts_payable->getBusiness();
		
		$this->view->load('accounts_payable/accounts_payable', $data);
	}

	public function view($id)
	{
		$this->view->title         = 'View Accounts Payable';
		$this->view->addCSS(array(
			'jquery.fileupload.css'
		)
		);  
		$this->view->addJS(
			array(
				'jquery.dirrty.js',
				'jquery.ui.widget.js',
				'jquery.iframe-transport.js',
				'jquery.fileupload.js',
				'jquery.fileupload-process.js',
				'jquery.fileupload-validate.js',
				'jquery.fileupload-ui.js'
			)
		);
		$data         			   = (array) $this->accounts_payable->getAPById($this->fields, $id);
		$details = $this->accounts_payable->getAPDetails($id);
		$vendor = $data['vendor'];
		$det = (array) $this->accounts_payable->getSupplierDetails($vendor);
		$close_date 				= $this->accounts_payable->getClosedDate();
		$data['close_date']			= $close_date;

		$check_stat = $this->accounts_payable->checkStat($id);
		$amountpaid = $check_stat->amountpaid;
		$bal = $check_stat->balance;
		$amount = $check_stat->amount;
		$stat = '';
		if($bal == $amount && $data['stat'] == 'posted') {
			$stat = 'unpaid';
		} else if($bal != $amount && $bal != 0 && $data['stat'] == 'posted') {
			$stat = 'partial';
		} else if($bal == 0 && $amountpaid == $amount && $data['stat'] == 'posted'){
			$stat = 'paid';
		} else if($bal != 0 && $data['stat'] == 'cancelled'){
			$stat = 'cancelled';
		}
		if($stat == 'partial' || $stat == 'paid') {
			$data['payments'] = $this->accounts_payable->getPVDetails($id);
			$data['yes'] = 'yes';
			$data['table'] = '';
		} else {
			$data['yes'] = 'no';
			$data['table'] = 'hidden';
		}
		$data['status'] = $stat;
		$data['stat'] = $this->colorStat($stat);
		$data['currencycodes'] = $this->accounts_payable->getCurrencyCode();
		$data["vendor_list"]          = $this->accounts_payable->retrieveVendorList();
		$data["proforma_list"]        = $this->accounts_payable->retrieveProformaList($data);
		$data['account_list'] = $this->accounts_payable->retrieveAccounts();
		$data["business_type_list"]   = $this->accounts_payable->getBusiness();
		$checker_pr = $this->accounts_payable->checkRefNo($data['referenceno']);
		$checker = ($checker_pr) ? true : false;
		$data['checker'] = $checker;
		$data['details'] = $details;
		$data['currency'] = $data['currencycode'];
		$data['address'] = $det['address1'];
		$data['email'] = $det['email'];
		$data['budget_list'] = $this->accounts_payable->getBudgetCodes();
		$data['tinno'] = $det['tinno'];
		$data['vendor'] = $det['partnername'];
		$data["ui"]   			   = $this->ui;
		$data['show_input'] 	   = false;
		$data["ajax_task"] 	  		   = "ajax_view";
		$data['ajax_post'] = "&id=$id";
		$data["transactiondate"] 			   = $this->date->dateFormat($data['transactiondate']);
		$data['duedate'] = $this->date->dateFormat($data['duedate']);
		$data['asset_list'] = $this->accounts_payable->retrieveAssetId();
		$data['voucherno'] = $id;

		$attachment						= $this->accounts_payable->getAttachmentFile($id);
		$data['attachment_url']			= '';
		$data['attachment_filename']	= '';
		$data['attachment_filetype']	= '';
		if (isset($attachment->attachment_url)) {
			$data['attachment_url'] 		= $attachment->attachment_url;
			$data['attachment_filename']	= $attachment->attachment_name;
			$data['attachment_filetype']	= $attachment->attachment_type;
		} 

		$this->view->load('accounts_payable/accounts_payable_view', $data);
	}

	public function edit($id)
	{
		$this->view->title         = 'Edit Accounts Payable';
		$this->view->addCSS(array(
			'jquery.fileupload.css'
		)
		);  
		$this->view->addJS(
			array(
				'jquery.dirrty.js',
				'jquery.ui.widget.js',
				'jquery.iframe-transport.js',
				'jquery.fileupload.js',
				'jquery.fileupload-process.js',
				'jquery.fileupload-validate.js',
				'jquery.fileupload-ui.js'
			)
		);
		$data         			   = (array) $this->accounts_payable->getAPById($this->fields, $id);
		$close_date 				= $this->accounts_payable->getClosedDate();
		$details = $this->accounts_payable->getAPDetails($id);
		$vendor = $data['vendor'];
		$det = (array) $this->accounts_payable->getSupplierDetails($vendor);
		$data['close_date']			= $close_date;
		$data['currencycodes'] = $this->accounts_payable->getCurrencyCode();
		$data["vendor_list"]          = $this->accounts_payable->retrieveVendorList();
		$data["proforma_list"]        = $this->accounts_payable->retrieveProformaList($data);
		$data['account_list'] = $this->accounts_payable->retrieveAccounts();
		$data["business_type_list"]   = $this->accounts_payable->getBusiness();
		$data['details'] = $details;
		$data['currency'] = $data['currencycode'];
		$data['email'] = $det['email'];
		$data['address1'] = $det['address1'];
		$data['tinno'] = $det['tinno'];
		$data['budget_list'] = $this->accounts_payable->getBudgetCodes();
		$data["ui"]   			   = $this->ui;
		$data['status_badge'] = $this->colorStat($data['stat']);
		$data["ajax_task"] 	  		   = "ajax_edit";
		$data['ajax_post'] = "&id=$id";
		$data["transactiondate"] 			   = $this->date->dateFormat($data['transactiondate']);
		$data['duedate'] = $this->date->dateFormat();
		$data['voucherno'] = $id;
		$data['asset_list'] = $this->accounts_payable->retrieveAssetId();
		$data['show_input'] 	   = true;
		$attachment						= $this->accounts_payable->getAttachmentFile($id);
		$data['attachment_url']			= '';
		$data['attachment_filename']	= '';
		$data['attachment_filetype']	= '';
		if (isset($attachment->attachment_url)) {
			$data['attachment_url'] 		= $attachment->attachment_url;
			$data['attachment_filename']	= $attachment->attachment_name;
			$data['attachment_filetype']	= $attachment->attachment_type;
		} 
		$this->view->load('accounts_payable/accounts_payable_edit', $data);
	}

	public function apply_bir($sid){
		
		$data        			    = $this->accounts_payable->retrieveEditDataDtl($sid);
		$ven						= $data["main"]->vendor; 
		$transactiondate			= $data["main"]->transactiondate; 
		$ven_dtl 	 				= $this->accounts_payable->getValue("partners",array("partnername","address1","tinno"),"partnercode = '$ven' ");
		$data['partnername']        = $ven_dtl[0]->partnername;
		$data['address1']       	= $ven_dtl[0]->address1;
		$data['tinno']      	  	= $ven_dtl[0]->tinno;
		$cmp						= $data["main"]->companycode; 
		$cmp_dtl 	 				= $this->accounts_payable->getValue("company",array("companyname","address","tin"),"companycode = '$cmp' "); 
		$data['companyname']        = $cmp_dtl[0]->companyname;
		$data['address']       		= $cmp_dtl[0]->address;
		$data['tin']      	  		= $cmp_dtl[0]->tin;
		$data["sid"] 		   		= $sid;
		$data["ajax_task"] 		   		= "apply_bir";
		$data["ui"]   			    = $this->ui;
		$data['show_input'] 	    = false;
		$from = date('Y-m-01', strtotime($transactiondate));
		$from = explode('-',$from);
		$data['f_yr']  = $from[0];
		$data['f_mo']  = $from[1];
		$data['f_dy']  = $from[2];
		$to = date('Y-m-t', strtotime($transactiondate));
		$to = explode('-',$to);
		$data['to_yr']  = $to[0];
		$data['to_mo']  = $to[1];
		$data['to_dy']  = $to[2];
		$data["button_name"] 	    = "Edit";
		$this->view->load('accounts_payable/accounts_payable_apply_bir', $data);
	}

	public function generate_pdf($sid){
		$data        			    = $this->accounts_payable->retrieveEditDataDtl($sid);
		$transactiondate			= $data["main"]->transactiondate; 
		$from = date('Y-m-01', strtotime($transactiondate));
		$from = explode('-',$from);
		$data['f_yr']  = $from[0];
		$data['f_mo']  = $from[1];
		$data['f_dy']  = $from[2];
		$to = date('Y-m-t', strtotime($transactiondate));
		$to = explode('-',$to);
		$data['to_yr']  = $to[0];
		$data['to_mo']  = $to[1];
		$data['to_dy']  = $to[2];
		$ven							= $data["main"]->vendor; 
		$ven_dtl 	 					= $this->accounts_payable->getValue("partners",array("partnername","address1","tinno"),"partnercode = '$ven' ");
		$data_ven['partnername']        = $ven_dtl[0]->partnername;
		$data_ven['address1']       	= $ven_dtl[0]->address1;
		$data_ven['tinno']      	  	= $ven_dtl[0]->tinno;
		$cmp							= $data["main"]->companycode; 
		$cmp_dtl 	 					= $this->accounts_payable->getValue("company",array("companyname","address","tin"),"companycode = '$cmp' "); 
		$data_payor['companyname']  	= $cmp_dtl[0]->companyname;
		$data_payor['address']       	= $cmp_dtl[0]->address;
		$data_payor['tin']      	  	= $cmp_dtl[0]->tin;

		$print_test = new print_tax();
		$print_test->setFile('modules/financials_module/model/2307_form.pdf')
		->setDocumentInfoPayee($data)
		->setDocumentInfoVendor($data_ven)
		->setDocumentInfoPayor($data_payor)
		->setDetails($data)
		->Output();
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
		$docinfo_fields = array('ap.transactiondate AS documentdate','ap.voucherno AS voucherno',"CONCAT( first_name, ' ', last_name )","IF('{$sub_select[0]->amount}' != \"\", '{$sub_select[0]->amount}', '{$apamount[0]->amount}') AS amount",'ap.amount AS apamount', "referenceno AS referenceno", "particulars AS remarks", "p.partnername AS vendor", 'ap.currencycode as currencycode', 'ap.exchangerate as exchangerate');
		$docinfo_join   = "partners as p ON p.partnercode = ap.vendor AND p.companycode = ap.companycode";
		$docinfo_cond 	= "ap.voucherno = '$voucherno'";

		$documentinfo  	= $this->accounts_payable->retrieveData($docinfo_table, $docinfo_fields, $docinfo_cond, $docinfo_join);

		$vendor 	    = $documentinfo[0]->vendor;

		// Retrieve Document Details
		$docdet_table   = "ap_details as dtl";
		$docdet_fields  = array("chart.segment5 as accountcode", "chart.accountname as accountname", "SUM(dtl.debit) as debit","SUM(dtl.credit) as credit", 'IF(dtl.debit = 0, SUM(dtl.convertedcredit), SUM(dtl.converteddebit)) as currency');
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
		$print = new print_payables_model('P', 'mm', 'Letter');
		$print->setDocumentType('Accounts Payable')
		->setDocumentInfo($documentinfo[0])
		->setVendor($vendor)
		->setPayments($paymentArray)
		->setDocumentDetails($documentdetails)
		->setCheque($chequeArray)
		->drawPDF('ap_voucher_' . $voucherno);
	}

	public function ajax($ajax_task) {
		$ajax = $this->{$ajax_task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		$invoices = "'" . implode("','", $delete_id) . "'";
		$get_values = $this->accounts_payable->getDetailsByVoucher($invoices, $this->apdetails);
		$data['stat'] 	= "cancelled";
		$error_id = array();
		if ($delete_id) {
			$error_id = $this->accounts_payable->updateEntry($data, $delete_id);
			$delete_fin = $this->accounts_payable->deleteEntry($delete_id);
			$result 	= $this->accounts_payable->reverseEntries($invoices);
		}
		return array(
			'success'	=> (empty($error_id)),
			'error_id'	=> $error_id
		);
	}

	// private function delete_invoice()
	// {
	// 	$vouchers 		= $this->input->post('delete_id');
	// 	$invoices = "'" . implode("','", $vouchers) . "'";
	// 	$data['stat'] 	= "cancelled";

	// 	$cond 			= " voucherno IN ($invoices) ";

	// 	$result 		= $this->accounts_payable->updateData($data, "accountspayable", $cond);

	// 	if( $result )
	// 	{
	// 		$result 	= $this->accounts_payable->updateData($data, "ap_details", $cond);
	// 	}

	// 	if( $result )
	// 	{
	// 		$result 	= $this->accounts_payable->reverseEntries($invoices, "ap_details", $cond);
	// 	}

	// 	if($result){
	// 		$code 	= 1; 
	// 		$msg 	= "Successfully cancelled the vouchers.";
	// 	}else{
	// 		$code 	= 0; 
	// 		$msg 	= "Sorry, the system was unable to delete the vouchers.";
	// 	}

	// 	$dataArray = array( "code" => $code, "msg" => $msg );
	// 	echo json_encode($dataArray);
	// }

	private function colorStat($stat) {
		$color = 'default';
		switch ($stat) {
			case 'paid':
			$color = 'success';
			break;
			case 'unpaid':
			$color = 'warning';
			break;	
			case 'cancelled':
			$color = 'danger';
			break;	
			case 'partial':
			$color = 'info';
			break;	
		}
		return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
	}

	private function colorStatJob($stat) {
		$color = 'default';
		switch ($stat) {
			case 'closed':
			$color = 'success';
			break;
			case 'cancelled':
			$color = 'danger';
			break;
			case 'on-going':
			$color = 'warning';
			break;
		}
		return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
	}

	private function ajax_list()
	{
		$data_post = $this->input->post(array("daterangefilter", "vendor", "filter", "search", "sort"));
		$pagination   = $this->accounts_payable->retrieveList($data_post);
		$table  = "";
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}

		foreach($pagination->result as $key => $row)
		{
			$date        			= $row->transactiondate;
			$restrict = $this->accounts_payable->setButtonRestriction($date);
			$date        		= $this->date->dateFormat($date);

			$voucher     		= $row->voucherno; 				
			$balance     		= $row->balance; 
			$amount	  	 		= $row->amount; 
			$vendor		 		= $row->vendor; 
			$referenceno 		= $row->referenceno; 
			$checker 	 		= $row->importchecker;
			$import 	 		= ($checker == 'import') 	?	"Yes" 	:	"No";
			$import_checker = ($checker == 'import');
			$checker_pr 		= $this->accounts_payable->checkRefNo($referenceno);
			$pr 				= ($checker_pr == true);
			$stat				= $row->stat;
			$payment_status 	= $row->payment_status;
			$status 			= ($row->stat != 'cancelled');
			$status_paid 		= ($row->balance != '0.00');
			$is_tax 	 		= $this->accounts_payable->getValue("ap_details",array("taxcode"),"voucherno = '$voucher' ");
			$bir_link 			= false;
			foreach ($is_tax as $rows) {
				if ($rows->taxcode != '') {
					$bir_link = true;
				}
			}
			$dropdown = $this->ui->loadElement('check_task')
			->addView()
			->addEdit($status && $restrict && !$pr && $status_paid && !$import_checker)
			->addPrint(!$import_checker)
			->addOtherTask(
				'Print 2307',
				'print',
				$bir_link
			)
			->addDelete($status && $restrict && $status_paid && !$import_checker)
			->addCheckbox($status && $restrict && $status_paid && !$import_checker)
			->setValue($voucher)
			->setLabels(array('delete' => 'Cancel'))
			->draw();

			$table	.= '<tr>';
			$table	.= '<td class = "text-center">'.$dropdown.'</td>';
			$table	.= '<td>'.$date.'</td>';
			$table	.= '<td>'.$import.'</td>';
			$table	.= '<td>&nbsp;'.$voucher.'</td>';
			$table	.= '<td>&nbsp;'.$vendor.'</td>';
			$table	.= '<td>&nbsp;'.$referenceno.'</td>';
			$table	.= '<td>&nbsp;'.number_format($amount,2).'</td>';
			$table	.= '<td>&nbsp;'.number_format($balance,2).'</td>';
			$table	.= '<td>'. $this->colorStat($payment_status). '</td>';
			$table	.= '</tr>';
		}

		$pagination->table = $table;

		return $pagination;
	}

	private function ajax_create()
	{
		$post = $this->input->post();
		$finjobs = $this->input->post($this->jobs);
		$ap = $this->input->post($this->fields);
		$button = $this->input->post('button_trigger');
		$ap_details = $this->input->post($this->apdetails);
		$addmonths = $this->input->post('addmonths');
		$seq 	   = new seqcontrol();
		$ap['voucherno'] = $seq->getValue('AP');
		$ap["transactiondate"]    = date('Y-m-d', strtotime($ap['transactiondate']));
		$ap["duedate"]      		=  date('Y-m-d', strtotime($ap['duedate']));
		$ap['invoicedate'] =  date('Y-m-d', strtotime($ap['invoicedate']));
		$ap['transtype'] = 'AP';
		$ap['fiscalyear'] = date('Y');
		$ap['convertedamount'] = str_replace(',', '', $ap['exchangerate']) * str_replace(',', '', $post['total_debit']);
		$ap['amount'] = str_replace(',', '', $post['total_debit']);
		$ap['exchangerate'] = str_replace(',', '', $ap['exchangerate']);
		$ap['balance'] = str_replace(',', '', $post['total_debit']);
		$ap['assetid'] = str_replace("none","",$ap['assetid']);
		$ap['terms'] = $post['vendor_terms'];
		$ap['stat'] = 'posted';
		$ap['job_no'] = $post['job'];
		$ap['months'] = $addmonths;
		$jobs = explode(',', $post['job']);
		
		if(!empty($jobs[0])) {
			$finjobs['voucherno'] = $ap['voucherno'];
			$finjobs['job_no'] = $jobs;
			$fin_job = $this->accounts_payable->saveFinancialsJob($finjobs);
		}

		if($ap['assetid'] != ''){
			$getAsset = $this->accounts_payable->getAsset($ap['assetid']);
			$capitalized_cost = $getAsset->capitalized_cost;
			$balance_value    = $getAsset->balance_value;
			$salvage_value	  = $getAsset->salvage_value;
			$useful_life	  = $getAsset->useful_life;
			$depreciation_month = $getAsset->depreciation_month;
			$time  					= strtotime($depreciation_month);
			
			$bv = $balance_value + $ap_details['debit'][0];

			$this->accounts_payable->updateAsset($ap['assetid'],$ap_details['debit'][0],$capitalized_cost,$balance_value,$useful_life,$addmonths);
			
			// $depreciation = 0;
			// for($x=1;$x<=$useful_life;$x++){
			// 	$depreciation_amount 	= ($bv - $salvage_value) / $useful_life;
			// 	$depreciation += ($bv - $salvage_value) / $useful_life;
			// 	$final = date("Y-m-d", strtotime("+$x month", $time));
			// 	$sched = $this->accounts_payable->updateAssetMasterSchedule($ap['assetid'],$final,$depreciation,$depreciation_amount);
			// }
		}

		$ap_details['transtype'] = 'AP';
		$ap_details['checkstat'] = 'uncleared';
		$ap_details['voucherno'] = $ap['voucherno'];
		$ap_details['source'] = $ap['referenceno'];
		$rate = str_replace(',', '', $ap['exchangerate']);
		$debit = str_replace(',', '', $ap_details['debit']);
		$credit = str_replace(',', '', $ap_details['credit']);
		$convdebit = [];
		$convcredit = [];
		foreach($debit as $row) {
			$convdebit[] = $rate * $row;
		}
		foreach($credit as $row) {
			$convcredit[] = $rate * $row;
		}
		$ap_details['debit'] = str_replace(',', '', $ap_details['debit']);
		$ap_details['credit'] = str_replace(',', '', $ap_details['credit']);
		$ap_details['converteddebit'] = $convdebit;
		$ap_details['convertedcredit'] = $convcredit;

		$account = $this->input->post('account');
		$checker = false;
		$result = false;
		$date_check = array();
		$warning = array();
		$accountchecker = array();
		$error = array();
		$codes = array('code' => 'code', 'amount' => 'amount');
		$actualbudget = $this->input->post($this->actualbudget);
		for($count = 0; $count < count($ap_details['budgetcode']); $count++) {
			if(!empty($ap_details['budgetcode'][$count])) {
				$check_date = $this->accounts_payable->checkEffectivityDate($ap_details['budgetcode'][$count], $ap['transactiondate']);
				$get_date = $this->accounts_payable->getEffectivityDate($ap_details['budgetcode'][$count]);
				if(!$check_date) {
					foreach($get_date as $key) {
						$date_check[] = "The budget code " .$ap_details['budgetcode'][$count] . " is available on " . date('M d, Y', strtotime($key)). "<br>";
					}
				} else if(empty($date_check)) {
					$get_accountname = $this->accounts_payable->getAccountName($ap_details['accountcode'][$count]);
					$get_amount = $this->accounts_payable->getBudgetAmount($ap_details['budgetcode'][$count], $ap_details['accountcode'][$count], $ap['transactiondate']);
					$accountname = $get_accountname->accountname;
					if(!$get_amount) {
						$accountchecker[] = 'The account ' . $accountname . ' is not in your budget code ' .$ap_details['budgetcode'][$count]. '.';
					} else {
						$amount = $get_amount->amount;
						$type = $get_amount->budget_check;
						if($type == 'Monitored') {
							if($ap_details['debit'][$count] != '0.00') {
								$check = in_array($ap_details['budgetcode'][$count], $codes);
								if(!$check) {
									$codes['code'] = $ap_details['budgetcode'][$count];
									$codes['amount'] = $ap_details['debit'][$count];
								} else {
									$codes['code'] = $ap_details['budgetcode'][$count];
									$codes['amount'] += $ap_details['debit'][$count];
								}
								if($check) {
									if($codes['amount'] > $amount) {
										$warning[0] = 'You were about to exceed from your budget code ' . $ap_details['budgetcode'][$count] . 
										' ' . $accountname . ' account <br>';
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['debit'][$count];
										$save_budget = $this->accounts_payable->saveActualBudget($actualbudget);
									} else {
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['debit'][$count];
										$save_budget = $this->accounts_payable->saveActualBudget($actualbudget);
									}
								} else {
									if($ap_details['debit'][$count] > $amount) {
										$warning[] = 'You were about to exceed from your budget code ' . $ap_details['budgetcode'][$count] . 
										' ' . $accountname . ' account <br>';
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['debit'][$count];
										$save_budget = $this->accounts_payable->saveActualBudget($actualbudget);
									} else {
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['debit'][$count];
										$save_budget = $this->accounts_payable->saveActualBudget($actualbudget);
									}
								}
							} else {
								$check = in_array($ap_details['budgetcode'][$count], $codes);
								if(!$check) {
									$codes['code'] = $ap_details['budgetcode'][$count];
									$codes['amount'] = $ap_details['credit'][$count];
								} else {
									$codes['code'] = $ap_details['budgetcode'][$count];
									$codes['amount'] += $ap_details['credit'][$count];
								}
								if($check) {
									if($codes['amount'] > $amount) {
										$warning[0] = 'You were about to exceed from your budget code ' . $ap_details['budgetcode'][$count] . 
										' ' . $accountname . ' account <br>';
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['credit'][$count];
										$save_budget = $this->accounts_payable->saveActualBudget($actualbudget);
									} else {
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['credit'][$count];
										$save_budget = $this->accounts_payable->saveActualBudget($actualbudget);
									}
								} else {
									if($ap_details['credit'][$count] > $amount) {
										$warning[] = 'You were about to exceed from your budget code ' . $ap_details['budgetcode'][$count] . 
										' ' . $accountname . ' account <br>';
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['credit'][$count];
										$save_budget = $this->accounts_payable->saveActualBudget($actualbudget);
									} else {
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['credit'][$count];
										$save_budget = $this->accounts_payable->saveActualBudget($actualbudget);
									}
								}
							}
						} else {
							$check = in_array($ap_details['budgetcode'][$count], $codes);
							if(!$check) {
								$codes['code'] = $ap_details['budgetcode'][$count];
								$codes['amount'] = $ap_details['debit'][$count];
							} else {
								$codes['code'] = $ap_details['budgetcode'][$count];
								$codes['amount'] += $ap_details['debit'][$count];
							}

							if($ap_details['debit'][$count] != '0.00') {
								if($check) {
									if($codes['amount'] > $amount) {
										$error[] = 'You are not allowed to exceed budget in ' . $ap_details['budgetcode'][$count] . 
										' ' . $accountname . ' account <br>';
									} else {
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['debit'][$count];
										$save_budget = $this->accounts_payable->saveActualBudget($actualbudget);
									}
								} else {
									if($ap_details['debit'][$count] > $amount) {
										$error[] = 'You are not allowed to exceed budget in ' . $ap_details['budgetcode'][$count] . 
										' ' . $accountname . ' account <br>';
									} else {
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['debit'][$count];
										$save_budget = $this->accounts_payable->saveActualBudget($actualbudget);
									}
								}
							} else {
								$check = in_array($ap_details['budgetcode'][$count], $codes);
								if(!$check) {
									$codes['code'] = $ap_details['budgetcode'][$count];
									$codes['amount'] = $ap_details['credit'][$count];
								} else {
									$codes['code'] = $ap_details['budgetcode'][$count];
									$codes['amount'] += $ap_details['credit'][$count];
								}

								if($check) {
									if($codes['amount'] > $amount) {
										$error[] = 'You are not allowed to exceed budget in ' . $ap_details['budgetcode'][$count] . 
										' ' . $accountname . ' account <br>';
									} else {
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['credit'][$count];
										$save_budget = $this->accounts_payable->saveActualBudget($actualbudget);
									}
								} else {
									if($ap_details['credit'][$count] > $amount) {
										$error[] = 'You are not allowed to exceed budget in ' . $ap_details['budgetcode'][$count] . 
										' ' . $accountname . ' account <br>';
									} else {
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['debit'][$count];
										$save_budget = $this->accounts_payable->saveActualBudget($actualbudget);
									}
								}
							}
						}
					}
				}
			}
		}

		if(!empty($account)) {
			$classcode = $this->accounts_payable->getAccountClasscode($account);
			foreach($classcode as $row) {
				if($row->accountclasscode == 'ACCPAY') {
					$checker = true;
					if(empty($error)) {
						$result    = $this->accounts_payable->saveAP($ap, $ap_details);
					}
				}
			}	
		} else {
			if(empty($error)) {
				$result    = $this->accounts_payable->saveAP($ap, $ap_details);
			}
		}


		if($button == 'save_preview')
		{
			$redirect = MODULE_URL . 'view/' . $ap['voucherno'];
		}
		else if($button == 'save_new')
		{
			$redirect = MODULE_URL . 'create/';
		}
		else if($button == 'save_exit')
		{
			$redirect = MODULE_URL;
		}

		if ($this->report_model){ 
			$this->report_model->generateAssetActivity();
		} 

		return array(
			'redirect'	=> $redirect,
			'success'	=> $result,
			'check'		=> $checker,
			'warning'  	=> $warning,
			'error'		=> $error,
			'date_check'	=> $date_check
		);
	}

	private function ajax_edit()
	{	
		$post = $this->input->post();
		$ap = $this->input->post($this->fields);
		$button = $this->input->post('button_trigger');
		$ap_details = $this->input->post($this->apdetails);
		$ap['voucherno'] = $post['voucher'];
		$ap["transactiondate"]    = date('Y-m-d', strtotime($ap['transactiondate']));
		$ap["duedate"]      		=  date('Y-m-d', strtotime($ap['duedate']));
		$ap['invoicedate'] =  date('Y-m-d', strtotime($ap['invoicedate']));
		$ap['transtype'] = 'AP';
		$ap['fiscalyear'] = date('Y');
		$ap['convertedamount'] = str_replace(',', '', $ap['exchangerate']) * str_replace(',', '', $post['total_debit']);
		$ap['amount'] = str_replace(',', '', $post['total_debit']);
		$ap['exchangerate'] = str_replace(',', '', $ap['exchangerate']);
		$ap['balance'] = str_replace(',', '', $post['total_debit']);
		$ap['assetid'] = str_replace("none","",$ap['assetid']);
		$ap['terms'] = $post['vendor_terms'];
		$ap['stat'] = 'posted';
		if(empty($post['job'])) {
			$ap['job_no'] = $post['jobs_tagged'];
		} else {
			$ap['job_no'] = $post['job'];
		}

		$jobs = explode(',', $ap['job_no']);
		$check_voucher = $this->accounts_payable->checkVoucherOnFinancialsJob($ap['voucherno']);
		$bool = (!empty($check_voucher)) ? true : $check_voucher;
		$finjobs = array();
		$finArr = array();
		if(!empty($jobs[0])) {
			foreach ($jobs as $row) {
				if($bool) {
					$finjobs['voucherno']= $ap['voucherno'];
					$finjobs['job_no']= $row;
				} else {
					$finjobs['voucherno']= $ap['voucherno'];
					$finjobs['job_no'] = $row;
					$fin_job = $this->accounts_payable->saveFinancialsJob($finjobs);
				}
				$finArr[] 						= $finjobs;
			}
			$fin_job = $this->accounts_payable->updateFinancialsJobs($finArr, $ap['voucherno']);
		}

		$ap_details['transtype'] = 'AP';
		$ap_details['checkstat'] = 'uncleared';
		$ap_details['voucherno'] = $ap['voucherno'];
		$ap_details['source'] = $ap['referenceno'];
		$rate = str_replace(',', '', $ap['exchangerate']);
		$debit = str_replace(',', '', $ap_details['debit']);
		$credit = str_replace(',', '', $ap_details['credit']);
		$convdebit = [];
		$convcredit = [];
		foreach($debit as $row) {
			$convdebit[] = $rate * $row;
		}
		foreach($credit as $row) {
			$convcredit[] = $rate * $row;
		}
		$ap_details['debit'] = str_replace(',', '', $ap_details['debit']);
		$ap_details['credit'] = str_replace(',', '', $ap_details['credit']);
		$ap_details['converteddebit'] = $convdebit;
		$ap_details['convertedcredit'] = $convcredit;

		$account = $this->input->post('account');
		$classcode = $this->accounts_payable->getAccountClasscode($account);
		$check = false;
		$result = false;
		$details = false;
		$warning = array();
		$accountchecker = array();
		$error = array();
		$date_check = array();
		$codes = array('code' => 'code', 'amount' => 'amount');

		$actualbudget = $this->input->post($this->actualbudget);
		for($count = 0; $count < count($ap_details['budgetcode']); $count++) {
			if(!empty($ap_details['budgetcode'][$count])) {
				$check_date = $this->accounts_payable->checkEffectivityDate($ap_details['budgetcode'][$count], $ap['transactiondate']);
				$get_date = $this->accounts_payable->getEffectivityDate($ap_details['budgetcode'][$count]);
				if(!$check_date) {
					foreach($get_date as $key) {
						$date_check[] = "The budget code " .$ap_details['budgetcode'][$count] . " is available on " . date('M d, Y', strtotime($key)). "<br>";
					}
				} else if(empty($date_check)) {
					$get_accountname = $this->accounts_payable->getAccountName($ap_details['accountcode'][$count]);
					$get_amount = $this->accounts_payable->getBudgetAmount($ap_details['budgetcode'][$count], $ap_details['accountcode'][$count], $ap['transactiondate']);
					$accountname = $get_accountname->accountname;
					if(!$get_amount) {
						$accountchecker[] = 'The account ' . $accountname . ' is not in your budget code ' .$ap_details['budgetcode'][$count]. '.';
					} else {
						$amount = $get_amount->amount;
						$type = $get_amount->budget_check;
						if($type == 'Monitored') {
							if($ap_details['debit'][$count] != '0.00') {
								$check = in_array($ap_details['budgetcode'][$count], $codes);
								if(!$check) {
									$codes['code'] = $ap_details['budgetcode'][$count];
									$codes['amount'] = str_replace(',', '',$ap_details['debit'][$count]);
								} else {
									$codes['code'] = $ap_details['budgetcode'][$count];
									$codes['amount'] += str_replace(',', '', $ap_details['debit'][$count]);
								}
								if($check) {
									if($codes['amount'] > $amount) {
										$warning[0] = 'You were about to exceed from your budget code ' . $ap_details['budgetcode'][$count] . 
										' ' . $accountname . ' account. <br>';
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['debit'][$count];
										$update_budget = $this->accounts_payable->updateActualBudget($ap['voucherno'], $actualbudget);
									} else {
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['debit'][$count];
										$update_budget = $this->accounts_payable->updateActualBudget($ap['voucherno'], $actualbudget);
									}
								} else {
									if(str_replace(',', '', $ap_details['debit'][$count]) > $amount) {
										$warning[] = 'You were about to exceed from your budget code ' . $ap_details['budgetcode'][$count] . 
										' ' . $accountname . ' account. <br>';
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['debit'][$count];
										$update_budget = $this->accounts_payable->updateActualBudget($ap['voucherno'], $actualbudget);
									} else {
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['debit'][$count];
										$update_budget = $this->accounts_payable->updateActualBudget($ap['voucherno'], $actualbudget);
									}
								}
							} else {
								$check = in_array($ap_details['budgetcode'][$count], $codes);
								if(!$check) {
									$codes['code'] = $ap_details['budgetcode'][$count];
									$codes['amount'] = str_replace(',', '', $ap_details['credit'][$count]);
								} else {
									$codes['code'] = $ap_details['budgetcode'][$count];
									$codes['amount'] += str_replace(',', '', $ap_details['credit'][$count]);
								}
								if($check) {
									if($codes['amount'] > $amount) {
										$warning[0] = 'You were about to exceed from your budget code ' . $ap_details['budgetcode'][$count] . 
										' ' . $accountname . ' account <br>';
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['credit'][$count];
										$update_budget = $this->accounts_payable->updateActualBudget($ap['voucherno'], $actualbudget);
									} else {
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['credit'][$count];
										$update_budget = $this->accounts_payable->updateActualBudget($ap['voucherno'], $actualbudget);
									}
								} else {
									if(str_replace(',', '', $ap_details['credit'][$count]) > $amount) {
										$warning[] = 'You were about to exceed from your budget code ' . $ap_details['budgetcode'][$count] . 
										' ' . $accountname . ' account <br>';
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['credit'][$count];
										$update_budget = $this->accounts_payable->updateActualBudget($ap['voucherno'], $actualbudget);
									} else {
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['credit'][$count];
										$update_budget = $this->accounts_payable->updateActualBudget($ap['voucherno'], $actualbudget);
									}
								}
							}
						} else {
							$check = in_array($ap_details['budgetcode'][$count], $codes);
							if(!$check) {
								$codes['code'] = $ap_details['budgetcode'][$count];
								$codes['amount'] = str_replace(',', '', $ap_details['debit'][$count]);
							} else {
								$codes['code'] = $ap_details['budgetcode'][$count];
								$codes['amount'] += str_replace(',', '', $ap_details['debit'][$count]);
							}

							if($ap_details['debit'][$count] != '0.00') {
								if($check) {
									if($codes['amount'] > $amount) {
										$error[] = 'You are not allowed to exceed budget in ' . $ap_details['budgetcode'][$count] . 
										' ' . $accountname . ' account <br>';
									} else {
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['debit'][$count];
										$update_budget = $this->accounts_payable->updateActualBudget($ap['voucherno'], $actualbudget);
									}
								} else {
									if(str_replace(',', '', $ap_details['debit'][$count]) > $amount) {
										$error[] = 'You are not allowed to exceed budget in ' . $ap_details['budgetcode'][$count] . 
										' ' . $accountname . ' account <br>';
									} else {
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['debit'][$count];
										$update_budget = $this->accounts_payable->updateActualBudget($ap['voucherno'], $actualbudget);
									}
								}
							} else {
								$check = in_array($ap_details['budgetcode'][$count], $codes);
								if(!$check) {
									$codes['code'] = $ap_details['budgetcode'][$count];
									$codes['amount'] = str_replace(',', '', $ap_details['credit'][$count]);
								} else {
									$codes['code'] = $ap_details['budgetcode'][$count];
									$codes['amount'] += str_replace(',', '', $ap_details['credit'][$count]);
								}

								if($check) {
									if($codes['amount'] > $amount) {
										$error[] = 'You are not allowed to exceed budget in ' . $ap_details['budgetcode'][$count] . 
										' ' . $accountname . ' account <br>';
									} else {
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['credit'][$count];
										$update_budget = $this->accounts_payable->updateActualBudget($ap['voucherno'], $actualbudget);
									}
								} else {
									if(str_replace(',', '', $ap_details['credit'][$count]) > $amount) {
										$error[] = 'You are not allowed to exceed budget in ' . $ap_details['budgetcode'][$count] . 
										' ' . $accountname . ' account <br>';
									} else {
										$actualbudget['voucherno'] = $ap['voucherno'];
										$actualbudget['budget_code'] = $ap_details['budgetcode'][$count];
										$actualbudget['accountcode'] = $ap_details['accountcode'][$count];
										$actualbudget['actual'] = $ap_details['debit'][$count];
										$update_budget = $this->accounts_payable->updateActualBudget($ap['voucherno'], $actualbudget);
									}
								}
							}
						}
					}
				}
			}
		}

		if(!empty($account)) {
			foreach($classcode as $row) {
				if($row->accountclasscode == 'ACCPAY') {
					$check = true;
					if(empty($error)) {
						$result    = $this->accounts_payable->updateAP($ap['voucherno'], $ap);
						$details = $this->accounts_payable->saveAPDetails($ap_details);
					}
				}
			}
		} else {
			if(empty($error)) {
				$result    = $this->accounts_payable->updateAP($ap['voucherno'], $ap);
				$details = $this->accounts_payable->saveAPDetails($ap_details);
			}
		}


		if($button == 'save_preview')
		{
			$redirect = MODULE_URL . 'view/' . $ap['voucherno'];
		}
		else if($button == 'save_new')
		{
			$redirect = MODULE_URL . 'create/';
		}
		else if($button == 'save_exit')
		{
			$redirect = MODULE_URL;
		}

		return array(
			'redirect'	=> $redirect,
			'success'	=> $result,
			'check'		=> $check,
			'warning'  	=> $warning,
			'error'		=> $error,
			'date_check'	=> $date_check
		);
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
		$header = array("Document Date", "Voucher No", "Supplier", "Invoice No", "Amount", "Balance", "Notes"); 
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
		$company_setting = $this->accounts_payable->companySettings(
			array(
				'wtax_option'
			)
		);
		$data["wtax_option"] 		  = $company_setting[0]->wtax_option;
		$wtax_option = $data["wtax_option"];
		
		// RETRIEVE ACCOUNT CODE
		$acc_entry_data     = array("id ind","accountname val");
		// $acc_entry_cond     = "accounttype != ''";
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

				$table	.= '<td class = "checkbox-select remove-margin text-center '.$toggle_wtax.'">';
				$table	.=  $ui->formField('checkbox')
				->setSplit('', 'col-md-12')
							// ->setName("wtax[".$row."]")
				->setId("wtax[".$row."]")
				->setClass("wtax")
				->setDefault("")
				->setValue(1)
				->setAttribute(array("disabled" => "disabled"))
				->draw($show_input);
				$table	.= '</td>';
				
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
		$header = array('Document Set','Transaction Date','Due Date','Supplier Code','Invoice No.','Reference No.','Notes','Account Name','Description','Debit','Credit');
		$return = "";
		
		$return .= '"' . implode('","',$header) . '"';
		$return .= "\n";

		echo $return;
	}

	public function get_account(){
		$tax_account = $this->input->post("tax_account");
		$tax_amount = $this->input->post("tax_amount");
		$result 	=  $this->accounts_payable->getAccount($tax_account);
		$tax = $result[0]->tax_rate;
		$account = $result[0]->tax_account;
		$amount = ($tax_amount * $tax) ;
		$returnArray = array( "tax_amount" => $tax_amount, "tax_account" => $account ,"amount" => $amount);
		echo json_encode($returnArray);
	}

	public function get_tax(){
		$account = $this->input->post("account");
		$result = $this->accounts_payable->getValue("chartaccount",array("accountclasscode"),"id = '$account' ");
		$result_class = $result[0]->accountclasscode;

		$bus_type_data                = array("atcId ind", "CONCAT(atc_code ,' - ', short_desc) val");
		$bus_type_cond                = "tax_account = '$account' AND atc.stat = 'active'";
		$join 						  =  "chartaccount ca ON atc.tax_account = ca.id";
		$tax_list  			 = $this->accounts_payable->getTax("atccode atc", $bus_type_data,$join ,$bus_type_cond, false);

		$ret = '';
		foreach ($tax_list as $key) {
			$in  = $key->ind;
			$val = $key->val;
			$ret .= "<option value=". $in.">" .$val. "</option>";
		}
		
		$returnArray = array( "result" => $result_class, "ret" => $ret);
		echo json_encode($returnArray);
	}

	private function save_import(){
		$seq 		= new seqcontrol();
		$file		= fopen($_FILES['file']['tmp_name'],'r') or exit ("File Unable to upload") ;

		$filedir	= $_FILES["file"]["tmp_name"];

		$file_types = array( "text/x-csv","text/tsv","text/comma-separated-values", "text/csv", "application/csv", "application/excel", "application/vnd.ms-excel", "application/vnd.msexcel", "text/anytext", "application/octet-stream");

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
		
		$headerArr = array('Document Set','Transaction Date','Due Date','Supplier Code','Invoice No.','Reference No.','Notes','Account Name','Description','Debit','Credit');

		$warning 			=	array();
		if( empty($errmsg) ) {
			$x = array_map('str_getcsv', file($_FILES['file']['tmp_name']));
			$error 	=	array();
			$rowcnt	= 0;
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
				$rowcnt++;
			}
			
			if($rowcnt < 2){
				$errmsg[]	= "Unable to upload a empty template. Please add at least one(1) data to proceed.<br/>";
			}
			$line 				=	2;
			$post 				=	array();
			$vouchlist 			= 	array();
			$h_vouchlist 		=	array();
			$datelist 			= 	array();
			$duedatelist 		=	array();
			$vendorlist 		=	array();
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
				$prev_duedate 	=	$prev_invno 	=	$prev_vendor 	= 	"";
				foreach ($z as $key => $b) {
					if ( ! empty($b)) {	
						$jvno 			=	isset($b[0]) 					? 	$b[0] 										:	"";
						$transdate 		=	isset($b[1]) 					? 	$b[1] 										:	"";
						$transdate 		=	($transdate != "") 				?	$this->date->dateDbFormat($transdate)	:	"";
						$duedate 		=	isset($b[2]) 					? 	$b[2] 	:	"";
						$duedate 		=	($duedate != "") 				?	$this->date->dateDbFormat($duedate)		:	"";
						$vendor 		=	isset($b[3]) 					?	htmlentities(trim($b[3]))	:	"";
						$invoiceno 		=	isset($b[4]) 					?	htmlentities(trim($b[4]))	:	"";
						$reference 		=	isset($b[5]) 					?	htmlentities(trim($b[5]))	:	"";
						$notes 			=	isset($b[6]) 					?	htmlentities(trim($b[6]))	:	"";
						$account 		=	isset($b[7]) 					?	htmlentities(trim($b[7]))	:	"";
						$account 		= 	str_replace('&ndash;', '-', $account);
						$description 	=	isset($b[8]) 					?	htmlentities(trim($b[8]))	:	"";
						$debit 			=	isset($b[9]) && !empty($b[9]) 	?	$b[9]	:	0;
						$credit 		=	isset($b[10]) && !empty($b[10])	?	$b[10]	:	0;
						//Check if account Name exist
						$acct_exist 	=	$this->accounts_payable->check_if_exists('id','chartaccount'," accountname = '$account' ");
						$acct_count 	=	$acct_exist[0]->count;

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
								$voucherno		= $seq->getValue('AP');
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
							//Check if Vendor Code is not empty
							if($vendor == ''){
								$errmsg[]	= "Supplier Code on <strong>row $line</strong> should not be empty.<br/>";
								$errmsg		= array_filter($errmsg);
							} else {
								//Check if Vendor Code exist 
								$cust_exist 	=	$this->accounts_payable->check_if_exists('partnercode','partners'," partnercode = '$vendor' ");
								$cust_count 	=	$cust_exist[0]->count;	
								if( $cust_count <= 0 ) {
									$errmsg[]	= "Supplier Code [<strong>$vendor</strong>] on <strong>row $line</strong> does not exist.<br/>";
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
								$voucherno		= $seq->getValue('AP');
							} 
							if ($jvno == $prev_no) {
								//Check Transaction Date if the same
								if($transdate == ''){
									$transdate 	= $prev_date;
								} else if ($transdate != $prev_date) {
									$errmsg[]	= "Transaction Date [<strong>$transdate</strong>] on <strong>row $line</strong> should be the same for vouchers # <strong>$jvno</strong>.<br/>";
									$errmsg		= array_filter($errmsg);
								}
								//Check Due Date is the same
								if($duedate == ''){
									$duedate 	= $prev_duedate;
								} 
								if ($duedate != $prev_duedate) {
									$errmsg[]	= "Due Date [<strong>$duedate</strong>] on <strong>row $line</strong> should be the same for vouchers # <strong>$jvno</strong>.<br/>";
									$errmsg		= array_filter($errmsg);
								}
								//Compare Transaction Date and Due Date. Due Date must not be earlier than Transaction date. 
								if( ($duedate != "" && $transdate != "") && ($transdate > $duedate)){
									$errmsg[]	= "Due Date [<strong>$duedate</strong>] on <strong>row $line</strong> must not be earlier than the Transaction Date [<strong>$transdate</strong>].<br/>";
									$errmsg		= array_filter($errmsg);
								}
								//Check if Transaction Date is not within Closed Date Period
								if($transdate <= $close_date){
									$errmsg[]	= "Transaction Date [<strong>$transdate</strong>] on <strong>row $line</strong> must not be within the Closed Period.<br/>";
									$errmsg		= array_filter($errmsg);
								}
								//Check if Due Date is not within Closed Date Period
								if($duedate <= $close_date){
									$errmsg[]	= "Due Date [<strong>$duedate</strong>] on <strong>row $line</strong> must not be within the Closed Period.<br/>";
									$errmsg		= array_filter($errmsg);
								}
								//Check the Vendor if the same
								if($vendor == ''){
									$vendor 	=	$prev_vendor;
								}  
								if ($vendor != $prev_vendor) {
									$errmsg[]	= "Supplier Code [<strong>$vendor</strong>] on <strong>row $line</strong> should be the same for vouchers # <strong>$jvno</strong>.<br/>";
									$errmsg		= array_filter($errmsg);
								}
								//Check if Vendor Code exist 
								$vendor_exist 	=	$this->accounts_payable->check_if_exists('partnercode','partners'," partnercode = '$vendor' ");
								$vendor_count 	=	$vendor_exist[0]->count;	
								if( $vendor_count <= 0 ) {
									$errmsg[]	= "Supplier Code [<strong>$vendor</strong>] on <strong>row $line</strong> does not exist.<br/>";
									$errmsg		= array_filter($errmsg);
								}
								//Check the Invoice #
								if($invoiceno == ''){
									$invoiceno 	=	$prev_invno;
								} 
								if ($invoiceno != $prev_invno) {
									$errmsg[]	= "Invoice No. [<strong>$invoiceno</strong>] on <strong>row $line</strong> should be the same for vouchers # <strong>$jvno</strong>.<br/>";
									$errmsg		= array_filter($errmsg);
								}
								//Check the Reference #
								if($reference == ''){
									$reference 	=	$prev_ref;
								}  
								if ($reference != $prev_ref) {
									$errmsg[]	= "Reference No. [<strong>$reference</strong>] on <strong>row $line</strong> should be the same for vouchers # <strong>$jvno</strong>.<br/>";
									$errmsg		= array_filter($errmsg);
								}
								//Check the Notes
								if($notes == ''){
									$notes 	=	$prev_notes;
								} 
								if ($notes != $prev_notes) {
									$errmsg[]	= "Notes [<strong>$notes</strong>] on <strong>row $line</strong> should be the same for vouchers # <strong>$jvno</strong>.<br/>";
									$errmsg		= array_filter($errmsg);
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
							$accountlist[] 		= $this->accounts_payable->getAccountId($account);
							$descriptions[] 	= $description;
							$debitlist[] 		= $debit; 
							$creditlist[] 		= $credit;
							$noteslist[] 		= $notes;
							$referencelist[] 	= $reference;

							if( !isset($h_vouchlist) || !in_array($voucherno, $h_vouchlist) ){
								$h_vouchlist[] 		= $voucherno;
								$datelist[] 		= $transdate;
								$duedatelist[] 		= $duedate;
								$vendorlist[] 		= $vendor;
								$invoicelist[] 		= $invoiceno;
							}
							// if( !isset($datelist) || !in_array($transdate, $datelist) ){	
							// 	$datelist[] 		= $transdate;
							// }
							// if( !isset($duedatelist) || !in_array($duedate, $duedatelist) ){	
							// 	$duedatelist[] 		= $duedate;
							// }
							// if( !isset($vendorlist) || !in_array($vendor, $vendorlist) ){
							// 	$vendorlist[] 		= $vendor;
							// }
							// if( !isset($invoicelist) || !in_array($invoiceno, $invoicelist) ){
							// 	$invoicelist[] 		= $invoiceno;
							// }
						}

						$prev_no 		= $jvno;
						$prev_date		= $transdate;
						$prev_duedate	= $duedate;
						$prev_vendor	= $vendor;
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
						'vendor' 			=> $vendorlist,
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
						$header['transtype'][] 		= "AP";
						$header['source'][] 		= "AP";
						$header['lockkey'][] 		= "import";
					}

					foreach ($header['transactiondate'] as $key => $row) {
						$period					= date("n",strtotime($row));
						$fiscalyear				= date("Y",strtotime($row));
						$header['period'][] 	= $period;
						$header['fiscalyear'][] = $fiscalyear;
					}	

					$proceed  			= $this->accounts_payable->save_import("accountspayable",$header);
					
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
						$proceed 	=	$this->accounts_payable->save_import("ap_details",$details);

						if($proceed){
							$this->logs->saveActivity("Imported Account Payables.");
						}
					} 
				}
			}
		}

		$error_messages		= implode(' ', $errmsg);
		$warning_messages	= implode(' ', $warning);

		return array("proceed" => $proceed,"errmsg"=>$error_messages, "warning"=>$warning_messages);
	}

	private function ajax_get_currency_val() {
		$currencycode = $this->input->post('currencycode');
		$result = $this->accounts_payable->getExchangeRate($currencycode);
		return array("exchangerate" => ($result) ? $result->exchangerate : '1.00');
	}

	private function ajax_get_details() {
		$vendor = $this->input->post('vendor');
		$result = $this->accounts_payable->getVendorDetails($vendor);
		return $result;
	}

	private function ajax_check_cwt() {
		$accountcode = $this->input->post('accountcode');
		$checker = '';
		$accountclasscode = $this->accounts_payable->checkCWT($accountcode);
		$acode = $accountclasscode->accountclasscode;
		if($acode == 'OTHCL' || $acode == 'TAX' || $acode == 'CULIAB') {
			$checker = 'true';
		}
		$tax_list  	= $this->accounts_payable->getATC($accountcode);
		$ret = '';
		foreach ($tax_list as $key) {
			$in  = $key->ind;
			$val = $key->val;
			$ret .= "<option value=". $in.">" .$val. "</option>";
		}

		return array('checker' => $checker, 'ret' => $ret);
	}

	private function ajax_get_taxrate() {
		$taxaccount = $this->input->post('taxaccount');
		$taxamount = $this->input->post('taxamount');
		$result = $this->accounts_payable->getTaxRate($taxaccount);
		return $result;
	}

	private function ajax_list_jobs() {
		$jobs_tagged = $this->input->post('jobs_tagged');
		$tags = explode(',', $jobs_tagged);
		$pagination = $this->accounts_payable->getJobList();
		$table = '';

		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}

		foreach($pagination->result as $key => $row)
		{
			$table	.= '<tr>';
			$check = in_array($row->job_no, $tags) ? 'checked' : '';
			$table	.= '<td class = "text-center"><input type = "checkbox" name = "jobno[]" id = "jobno" class = "jobno" value = "'.$row->job_no.'" '.$check.'></td>';
			$table	.= '<td>'.$row->job_no.'</td>';
			$table	.= '<td>'.$this->colorStatJob($row->stat).'</td>';
			$table	.= '</tr>';
		}

		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_get_asset_details() {
		$asset = $this->input->post('asset');
		$result = $this->accounts_payable->getAssetDetails($asset);
		$in  = $result->ind;
		return $in;
	}

	private function ajax_check_account() {
		$account = $this->input->post('account');
		$result = $this->accounts_payable->getAccountClasscode($account);
		$check = false;
		foreach($result as $row) {
			if($row->accountclasscode == 'ACCPAY') {
				$check = true;
			}
		}
		return $check;
	}

	private function ajax_check_budget() {
		$budgetcode = $this->input->post('budgetcode');
		$accountcode = $this->input->post('accountcode');
		$amount = 0;
		$budget_check = '';
		$get_amount = $this->accounts_payable->getBudgetAmount($budgetcode, $accountcode);
		if(!empty($get_amount->amount)) {
			$amount = $get_amount->amount;
			$budget_check = $get_amount->budget_check;
		}
		return array('amount' => $amount, 'budget_check' => $budget_check);
	}

	private function checkifacctisinbudget(){
		$accountcode = $this->input->post('accountcode');

		// Check if Account is used in a Budget
		$ret_result = $this->accounts_payable->checkifaccountisinbudget($accountcode);
		$result 	=	!empty($ret_result) ? 1 : 0;

		return $dataArray = array("result"=>$result);
	}

	private function checkifpairexistsinbudget() {
		$accountcode= $this->input->post('accountcode');
		$budget 	= $this->input->post('budgetcode');

		// Check if Budget_Accountcode pair exists
		$ret_result = $this->accounts_payable->checkifpairexistsinbudget($accountcode, $budget);
		$result 	= !empty($ret_result) ? 1 : 0;

		return $dataArray = array("result"=>$result);
	}

	private function ajax_upload_file() {
		$post_data 		= $this->input->post();
		$upload_handler	= new UploadHandler();
		$reference 		= $post_data['reference'];
		if ($reference == '') {
			$post_data['reference'] = $this->accounts_payable->getLatestAPRecord();
		}
		$task 			= $post_data['task'];
		$upload_result 	= false;
		unset($post_data['task']);
		
		if (isset($upload_handler->response) && isset($upload_handler->response['files'])) {
			if(!isset($upload_handler->response['files'][0]->error)){
				
				/**
				 * Generate Attachment Id
				 * @param table
				 * @param group fields
				 * @param custom condition
				 */
				// if ($task=='edit') 
				$attachment_id = $this->accounts_payable->getCurrentId("accountspayable_attachments", $reference);
				if ($attachment_id=='0'){
					$attachment_id = $this->accounts_payable->getNextId("accountspayable_attachments","attachment_id");
				}
				// else
					// $attachment_id = $this->purchase_model->getNextId("purchasereceipt_attachments","attachment_id");

				foreach($upload_handler->response['files'] as $key => $row) {
					$post_data['attachment_id'] 	= $attachment_id;
					$post_data['attachment_name'] 	= $row->name;
					$post_data['attachment_type'] 	= $row->type;
					$post_data['attachment_url']	= $row->url;
				}

				if ($task == 'edit') {
					$upload_result 	= $this->accounts_payable->replaceAttachment($post_data);
					
					}
				else
					$upload_result 	= $this->accounts_payable->uploadAttachment($post_data);

			}else{
				// if($upload_handler->response['files'][0]->name == "Sorry, but file already exists"){
					
				// }
				$upload_result 	= false;
			}
		}
	}
}