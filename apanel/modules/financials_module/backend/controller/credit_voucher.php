<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui					= new ui();
		$this->input				= new input();
		$this->seq 					= new seqcontrol();
		$this->credit_voucher_model	= new credit_voucher_model();
		$this->session				= new session();
		$this->log 					= new log();
		$this->fields 				= array(
			'transactiondate',
			'voucherno',
			'partnername',
            'invoiceno',
			'referenceno',
			'amount',
			'source',
			'c.stat stat'
		);
	}

	public function listing() {
		$this->view->title	= 'Credit Voucher';
		$data['ui']				= $this->ui;
		$data['show_input']     = true;
		$this->view->load('credit_voucher/credit_voucher_list', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data		= $this->input->post(array('search', 'typeid', 'classid', 'daterangefilter', 'sort','source','filter'));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$typeid		= $data['typeid'];
		$classid	= $data['classid'];
		$datefilter	= $data['daterangefilter'];
		$source 	= $data['source'];
		$filter 	= $data['filter'];

		$pagination	= $this->credit_voucher_model->getCreditVoucherPagination($this->fields, $search, $sort, $datefilter, $source, $filter);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$getApplied = $this->credit_voucher_model->getApplied($row->voucherno);
			$balance = $row->amount - $getApplied->amount;
			$dropdown = $this->ui->loadElement('check_task')
									->addView() 
									->addEdit($row->source != 'RV' && $row->stat != 'inactive')
									->addDelete($row->source != 'RV' && $row->stat != 'inactive')
									->addPrint()
									->addCheckbox($row->source != 'RV' && $row->stat != 'inactive')
									->setLabels(array('delete' => 'Cancel'))
									->setValue($row->voucherno)
                                    ->draw();
            $table .= '<tr>';
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $row->partnername . '</td>';
			$table .= '<td>' . $row->invoiceno . '</td>';
			$table .= '<td>' . $row->referenceno . '</td>';
			$table .= '<td>' . number_format($balance, 2) . '</td>';
			$table .= '<td>' . $row->amount . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		if ($delete_id) {
			$this->credit_voucher_model->deleteCreditVoucher($delete_id);
		}
	}


}