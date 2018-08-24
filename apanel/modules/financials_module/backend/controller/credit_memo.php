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
			'transactiondate',
			'referenceno',
			'remarks',
			'proformacode',
			'partner',
			'amount',
			'si_no',
			'sr_amount',
			'stat'
		);
		$this->fields2			= array(
			'voucherno',
			'accountcode',
			'detailparticulars',
			'debit',
			'credit'
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
		$data['proforma_list']		= $this->cm_model->getProformaList();
		$data['chartofaccounts']	= $this->cm_model->getChartOfAccountList();
		$data['voucher_details']	= json_encode(array());
		$data['ajax_task']			= 'ajax_create';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		$data['restrict_cm'] 		= true;
		$data['status'] 			= false;
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
		$data['proforma_list']	 	= $this->cm_model->getProformaList();
		$data['partner_list']    	= $this->cm_model->getVendorList();
		$data['chartofaccounts']	= $this->cm_model->getChartOfAccountList();
		$data['voucher_details']	= json_encode($this->cm_model->getJournalVoucherDetails($this->fields2, $voucherno));
		$data['ajax_task']			= 'ajax_edit';
		$data['ajax_post']			= "&voucherno_ref=$voucherno";
		$data['show_input']			= true;
		$data['restrict_cm'] 		= true;
		$data['status'] 			= false;
		$this->view->load('credit_memo/credit_memo', $data);
	}

	public function view($voucherno) {
		$this->view->title			= 'View Credit Memo';
		$data						= (array) $this->cm_model->getJournalVoucherById($this->fields, $voucherno);
		$transactiondate 			= $data['transactiondate'];
		$restrict_cm 				= $this->restrict->setButtonRestriction($data['transactiondate']);
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ui'] = $this->ui;
		$data['partner_list']   	 = $this->cm_model->getVendorList();
		$data['proforma_list']		= $this->cm_model->getProformaList();
		$data['chartofaccounts']	= $this->cm_model->getChartOfAccountList();
		$status						= $data['stat'];
		$data['voucher_details']	= json_encode($this->cm_model->getJournalVoucherDetails($this->fields2, $voucherno));
		$data['show_input']			= false;
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_cm'] 		= $restrict_cm;
		$data['status']				= $status;
		$this->view->load('credit_memo/credit_memo', $data);
	}

	public function print_preview($voucherno) {
		$documentinfo		= $this->cm_model->getDocumentInfo($voucherno);
		$documentdetails	= $this->cm_model->getDocumentDetails($voucherno);
		$documentvendor   	= $this->cm_model->getVendor($voucherno);
		$print = new print_voucher_model('P', 'mm', 'Letter');
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

			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit($restrict_cm && $display_edit_delete && $source != "excess")
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
		$submit						= $this->input->post('submit');
		$data						= $this->input->post($this->fields);
		$data2						= $this->input->post($this->fields2);
		$seq						= new seqcontrol();
		$data['voucherno']			= $seq->getValue('CM');
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
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
		$data['partner_list']   	 = $this->cm_model->getVendorList();
		$data					= $this->input->post($this->fields);
		unset($data['voucherno']);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$voucherno				= $this->input->post('voucherno_ref');
		$data2					= $this->input->post($this->fields2);
		$data2['stat']			= 'posted';
		$result					= $this->cm_model->updateJournalVoucher($data, $data2, $voucherno, 'Update');
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		if ($delete_id) {
			$this->cm_model->deleteJournalVouchers($delete_id);
		}
		if ($delete_id) {
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

}