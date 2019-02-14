<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->show_input 		= true;
		$this->cm_model			= new credit_memo_model();
		$this->restrict 		= new financials_restriction_model();
		$this->session			= new session();
		$this->fields 			= array(
			'voucherno',
			'job_no',
			'transactiondate',
			'currencycode',
			'referenceno',
			'exchangerate',
			'remarks',
			'proformacode',
			'partner',
			'amount',
			'convertedamount',
			'si_no',
			'sr_amount',
			'stat'
		);
		$this->fields2			= array(
			'voucherno',
			'accountcode',
			'detailparticulars',
			'debit',
			'credit',
			'convertedcredit',
			'converteddebit'
		);
	}

	public function listing() {
		$this->view->title = 'Credit Memo';
		$this->show_input = false;
		$data['ui'] = $this->ui;
		$data['partner_list']    	= $this->cm_model->getVendorList();
		$this->view->load('credit_memo/credit_memo_list', $data);
	}

	public function create() {
		$this->view->title			= 'Create Credit Memo';
		$data						= $this->input->post($this->fields);
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$data['ui'] = $this->ui;
		$data['partner_list']    	= $this->cm_model->getVendorList();
		$data['chartofaccounts']	= $this->cm_model->getChartOfAccountList();
		$data['voucher_details']	= json_encode(array());
		$data['ajax_task']			= 'ajax_create';
		$data['proforma_list']		= $this->cm_model->getProformaList($data);
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		$data['restrict_cm'] 		= true;
		$data['status'] 			= false;
		$data['currencycodes'] = $this->cm_model->getCurrencyCode();
		$data['currency'] = 'PHP';
		//$data["exchangerate"]       = "1.00";
		$this->view->load('credit_memo/credit_memo', $data);
	}

	public function edit($voucherno) {
		$this->view->title			= 'Edit Credit Memo';
		$data						= (array) $this->cm_model->getJournalVoucherById($this->fields, $voucherno);

		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$data['ui'] = $this->ui;
		$data['partner_list']    	= $this->cm_model->getVendorList();
		$coa_array	= array();
		$data['voucher_details']	= json_encode($this->cm_model->getJournalVoucherDetails($this->fields2, $voucherno));
		$coa_array	= array();
		$hey = json_decode($data['voucher_details']);
			foreach ($hey as $index => $dtl){
				$coa			= $dtl->accountcode;
				$coa_array[]	= $coa;
			}
		$data['chartofaccounts']	= $this->cm_model->getEditChartOfAccountList($data,$coa_array);		
		$data['ajax_task']			= 'ajax_edit';
		$data['proforma_list']	 	= $this->cm_model->getProformaList($data);
		$data['ajax_post']			= "&voucherno_ref=$voucherno";
		$data['show_input']			= true;
		$data['restrict_cm'] 		= true;
		$data['status'] 			= false;
		$data['currencycodes'] = $this->cm_model->getCurrencyCode();
		$data['currency'] = $data['currencycode'];
		//$data['details'] = $details;
		
		// $tag = 0;
		// if($data['job_no'] != '') {
		// 	$jobs = explode(',', $data['job_no']);
		// 	foreach($jobs as $i =>$key) {
		// 		$tag = $tag + 1;
		// 	}
		// }else {
		// 	$tag = '';
		// }
		// $data['tags'] = $tag;

		$this->view->load('credit_memo/credit_memo', $data);
	}

	public function view($voucherno) {
		$this->view->title			= 'View Credit Memo';
		$data						= (array) $this->cm_model->getJournalVoucherById($this->fields, $voucherno);
		$transactiondate 			= $data['transactiondate'];
		$restrict_cm 				= $this->restrict->setButtonRestriction($data['transactiondate']);
		$display_edit 				= $this->cm_model->getSource($voucherno);
		$display_edit 				= ($display_edit[0]->source == "excess") ? 0 : 1;
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ui']					= $this->ui;
		$data['partner_list']   	= $this->cm_model->getVendorList();
		$status						= $data['stat'];
		$data['voucher_details']	= json_encode($this->cm_model->getJournalVoucherDetails								  ($this->fields2, $voucherno));
		$data['show_input']			= false;
		$checker_prtn 				= $this->cm_model->checkRefNo($data['referenceno']);
		$checker 					= ($checker_prtn) ? true : false;
		$data['checker'] 			= $checker;
		$coa_array	= array();
		$hey = json_decode($data['voucher_details']);
			foreach ($hey as $index => $dtl){
				$coa			= $dtl->accountcode;
				$coa_array[]	= $coa;
			}
		$data['chartofaccounts']	= $this->cm_model->getEditChartOfAccountList($data,$coa_array);		
		$data['ajax_task']			= 'ajax_view';
		//$data['ajax_post'] 			= "&id=$id";
		$data['proforma_list']		= $this->cm_model->getProformaList($data);
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_cm'] 		= $restrict_cm;
		$data['status']				= $status;
		$data['display_edit'] 		= $display_edit;
		$data['currencycodes'] = $this->cm_model->getCurrencyCode();
		$data['currency'] = 		$data['currencycode'];
		// $data['partner']			= $this->cm_model->getPartnerOrVendor($voucherno);

		$this->view->load('credit_memo/credit_memo', $data);
	}
	// old print for cm
	// public function print_preview2($voucherno) {
	// 	$documentinfo		= $this->cm_model->getDocumentInfo($voucherno);
	// 	$documentdetails	= $this->cm_model->getDocumentDetails($voucherno);
	// 	$documentvendor   	= $this->cm_model->getVendor($voucherno);
	// 	$print = new print_voucher_model('P', 'mm', 'Letter');
	// 	$print->setDocumentType('Credit Memo')
	// 			->setDocumentInfo($documentinfo)
	// 			->setDocumentDetails($documentdetails)
	// 			->setVendor($documentvendor[0]->partnername)
	// 			->drawPDF('cm_voucher_' . $voucherno);
	// }

	public function print_preview($voucherno) {
		$documentinfo		= $this->cm_model->getDocumentInfo($voucherno);
		$documentdetails	= $this->cm_model->getDocumentDetails($voucherno);
		$documentvendor   	= $this->cm_model->getVendor($voucherno);
		$print = new print_payables_model('P', 'mm', 'Letter');
		$print->setDocumentType('Credit Memo')
				->setDocumentInfo($documentinfo)
				->setDocumentDetails($documentdetails)
				->setVendor($documentvendor[0]->partnername)
				->drawPDF('cm_voucher_' . $voucherno);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data['partner_list'] = $this->cm_model->getVendorList();
		$data		= $this->input->post(array('search', 'typeid', 'classid', 'daterangefilter','partner','limit','sort'));
		$search		= $data['search'];
		$limit		= $data['limit'];
		$sort		= $data['sort'];
		$typeid		= $data['typeid'];
		$classid	= $data['classid'];
		$partner	= $data['partner'];
		$datefilter	= $data['daterangefilter'];
		$pagination	= $this->cm_model->getJournalVoucherPagination($this->fields, $search, $typeid, $classid, $datefilter, $partner, $limit, $sort);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$transactiondate 	=	$row->transactiondate;
			$restrict_cm= $this->restrict->setButtonRestriction($transactiondate);
			$status				=   $row->stat;
			$source 			= 	isset($row->source) 	?	$row->source 	:	"";
			$display_edit_delete=  	($status != 'cancelled') 	?	1	:	0;
			$voucher_status = '<span class="label label-danger">'.strtoupper($status).'</span>';
				if($status == 'open'){
					$voucher_status = '<span class="label label-info">'.strtoupper($status).'</span>';
				}else if($status == 'posted'){
					$voucher_status = '<span class="label label-success">'.strtoupper($status).'</span>';
				}
			$checker_prtn = $this->cm_model->checkRefNo($row->referenceno);
			$prtn = ($checker_prtn == true);

			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit($restrict_cm && $display_edit_delete && $source != "excess" && !$prtn)
									->addDelete($restrict_cm && $display_edit_delete && $source != "excess")
									->addPrint()
									->addCheckbox($restrict_cm  && $display_edit_delete && $source != "excess")
									->setLabels(array('delete'=>'Cancel'))
									->setValue($row->voucherno)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $this->date->dateFormat($transactiondate) . '</td>';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $row->partnername.'</td>';
			$table .= '<td>' . $row->referenceno . '</td>';
			$table .= '<td>' . $row->amount . '</td>';
			$table .= '<td>' . $voucher_status . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_get_proforma() {
		$proformacode	= $this->input->post('proformacode');
		$proforma		= $this->cm_model->getProforma($proformacode);
		return array(
			'proforma' => $proforma
		);
	}

	private function ajax_create() {
		// $data		= $this->input->post($this->fields);
		// $data2		= $this->input->post($this->fields2);
		// $finalized	= $this->input->post('finalized');
		// unset($data['voucherno']);
		// if ($finalized) {
		// 	$seq				= new seqcontrol();
		// 	$data['voucherno']	= $seq->getValue('CM');
		// 	$data['stat']		= 'posted';
		// 	$data2['stat']		= 'posted';
		// }
		//$ap['job_no'] = $post['job'];
		$result						= '';
		$job_no						= $this->input->post('job');
		$submit						= $this->input->post('submit');
		$data						= $this->input->post($this->fields);
		$data2						= $this->input->post($this->fields2);
		
		$seq						= new seqcontrol();
		$data['job_no']				= $job_no;
		$data['voucherno']			= $seq->getValue('CM');
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		
		$data['amount'] 			= str_replace(',', '', $this->input->post('total_debit'));
		$data['convertedamount'] 	= $this->input->post('total_currency');
		//$data2['converteddebit'] 	= str_replace(',', '', $this->input->post('currencydebit'));	

		$jobs = explode(',', $data['job_no']);

		if(!empty($jobs[0])) {
			$finjobs['voucherno'] = $data['voucherno'];
			$finjobs['job_no'] = $jobs;
			$fin_job = $this->cm_model->saveFinancialsJob($finjobs);
			//var_dump($finjobs);
		}

		$rate = str_replace(',', '', $data['exchangerate']);
		$debit = str_replace(',', '', $data2['debit']);
		$credit = str_replace(',', '', $data2['credit']);

		$convdebit = [];
		$convcredit = [];
		foreach($debit as $row) {
			$convdebit[] = $rate * $row;
		}
		foreach($credit as $row) {
			$convcredit[] = $rate * $row;
		}

		$data2['debit'] = str_replace(',', '', $data2['debit']);
		$data2['credit'] = str_replace(',', '', $data2['credit']);
		$data2['converteddebit'] = $convdebit;
		$data2['convertedcredit'] = $convcredit;
		
		// $result					= $this->cm_model->updateJournalVoucher($data, $data2, $this->temp, (($finalized) ? 'Create' : false));

		$result						= $this->cm_model->saveJournalVoucher($data, $data2);
		

		$redirect_url = MODULE_URL;
		if ($submit == 'save_new') {
			$redirect_url = MODULE_URL . 'create';
		} else if ($submit == 'save') {
			$redirect_url = MODULE_URL . 'view/' . $data['voucherno'];
		} else if ($submit == 'save_exit') {
			$redirect_url = MODULE_URL;
		}
		
		return array(
			'redirect'	=> $redirect_url,
			'success'	=> $result
		);
	}

	private function ajax_edit() {
		$data['partner_list']   	= $this->cm_model->getVendorList();
		$data						= $this->input->post($this->fields);
		unset($data['voucherno']);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$voucherno					= $this->input->post('voucherno_ref');
		$data2						= $this->input->post($this->fields2);
		$data2['stat']				= 'posted';
		$data['job_no']				= $this->input->post('job');
		$jobs_tagged				= $this->input->post('jobs_tagged');

		$rate = str_replace(',', '', $data['exchangerate']);
		$debit = str_replace(',', '', $data2['debit']);
		$credit = str_replace(',', '', $data2['credit']);

		$job_no						= $this->input->post('job');
		$data['job_no']				= $job_no;

		if(empty($this->input->post('job'))) {
			$data['job_no'] = $jobs_tagged;
		} else {
			$data['job_no'] = $data['job_no'];
		}

		$jobs = explode(',', $data['job_no']);
		$check_voucher = $this->cm_model->checkVoucherOnFinancialsJob($voucherno);
		$bool = (!empty($check_voucher)) ? true : $check_voucher;
		$finjobs = array();
		$finArr = array();
		if(!empty($jobs[0])) {
			foreach ($jobs as $row) {
				if($bool) {
					$finjobs['voucherno']= $voucherno;
					$finjobs['job_no']= $row;
				} else {
					$finjobs['voucherno']= $voucherno;
					$finjobs['job_no'] = $row;
					$fin_job = $this->cm_model->saveFinancialsJob($finjobs);
				}
				$finArr[] 						= $finjobs;
			}
			$fin_job = $this->cm_model->updateFinancialsJobs($finArr, $voucherno);
		}

		$convdebit = [];
		$convcredit = [];
		foreach($debit as $row) {
			$convdebit[] = $rate * $row;
		}
		foreach($credit as $row) {
			$convcredit[] = $rate * $row;
		}

		$data2['debit'] = str_replace(',', '', $data2['debit']);
		$data2['credit'] = str_replace(',', '', $data2['credit']);
		$data2['converteddebit'] = $convdebit;
		$data2['convertedcredit'] = $convcredit;
		
		$result						= $this->cm_model->updateJournalVoucher($data, $data2, $voucherno, 'Update');
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		if ($delete_id) {
			$this->cm_model->deleteJournalVouchers($delete_id);
			$delete_fin = $this->cm_model->deleteEntry($delete_id);
			$this->cm_model->reverseEntries($delete_id);
		}
	}

	private function ajax_load_ordered_list() {
		$customer		= $this->input->post('customer');
		$search			= $this->input->post('search');
		$pagination		= $this->cm_model->getSalesOrderPagination($customer, $search);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr data-id="' . "$row->voucherno," . "$row->source_no," . "$row->netamount" . '">';
			$table .= '<td ><a data-id="' .  $row->voucherno. '" data-toggle="modal">' . $row->voucherno . '</a></td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->source_no . '</td>';
			$table .= '<td class="text-right">' . number_format($row->netamount, 2) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_load_return_details() {
		$sr_no		= $this->input->post('sr_no');
		$list		= $this->cm_model->getReturnDetails($sr_no);
		$details 	= $this->cm_model->getReturnHeader($sr_no);
		$table		= '';
		if (empty($list)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($list as $key => $row) {
			$table .= '<tr>';
			$table .= '<td>' . $row->itemcode. '</td>';
			$table .= '<td>' . $row->detailparticular. '</td>';
			$table .= '<td>' . number_format($row->unitprice,2) . '</td>';
			$table .= '<td>' . ($row->issueqty) . '</td>';
			$table .= '<td>' . $row->issueuom . '</td>';
			$table .= '<td class="text-right">' . number_format($row->amount, 2) . '</td>';
			$table .= '</tr>';
		}
		return array(
			'table' => $table,
			'voucherno' 		=> $details[0]->voucherno,
			'transactiondate' 	=> $this->date->dateFormat($details[0]->transactiondate),
			'source_no' 		=> $details[0]->source_no,
			'partnername' 		=> $details[0]->partnername

		);
	}

	private function ajax_list_jobs() {
		$jobs_tagged = $this->input->post('jobs_tagged');
		$tags = explode(',', $jobs_tagged);
		$pagination = $this->cm_model->getJobList();
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
			$table	.= '<td>'.$this->colorStat($row->stat).'</td>';
			$table	.= '</tr>';
		}

		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_get_currency_val() {
		$currencycode = $this->input->post('currencycode');
		$result = $this->cm_model->getExchangeRate($currencycode);
		return array("exchangerate" => $result->exchangerate);
	}

	private function colorStat($stat) {
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


}