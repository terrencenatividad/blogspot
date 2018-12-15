<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui					= new ui();
		$this->input				= new input();
		$this->asset_transaction	= new asset_transaction();
		$this->report_model			= new report_model();
		$this->view->header_active	= 'report/';
		$this->fields				= array(
			'at.id',
			'voucherno',
			'transactiontype',
			'assetclass',
			'asset_number',
			'sub_number',
			'serial_number',
			'transactiondate',
			'amount',
			'transferto'
		);
	}

	public function view() {
		$this->view->title		= 'Asset Transaction';
		$data['ui']				= $this->ui;
		$data['datefilter']		= date("M d, Y");
		$data['supplier_list']	= $this->asset_transaction->getSupplierList();
		$this->view->load('asset_transaction', $data);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$this->report_model->generateAssetActivity();
		$pagination		= $this->asset_transaction->getAssetTransaction($this->fields);
		$tt = '';
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$transtype = substr($row->voucherno, 0, 2);
			if($transtype == 'PO')
				$tt = "purchase/purchase_order/view/";
			else if($transtype == 'PR')
				$tt = "purchase/purchase_receipt/view/";
			else if($transtype == 'AP')
				$tt = "financials/accounts_payable/view/";
			else
				$tt = "maintenance/asset_master/view/";
			$table .= '<tr>';
			$table .= '<td>' . $row->transactiontype . '</td>';
			$table .= '<td>' . $row->assetclass . '</td>';
			$table .= '<td>' . $row->asset_number . '</td>';
			$table .= '<td>' . $row->sub_number . '</td>';
			$table .= '<td>' . $row->serial_number . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td class="text-right""><a data-id="' .  $row->voucherno. '" id="modal" href="'.BASE_URL.$tt.$row->voucherno.'" data-toggle="modal">'. number_format($row->amount, 2) . '</a></td>';
			$table .= '<td>' . $row->transferto . '</td>';
			$table .= '</tr>';
		}

		$footer = '';

		if ($pagination->page_limit > 1) {
			$footer .= '<tr>';
			$footer .= '<td colspan="10" class="text-center"><b>Page: ' . $pagination->page . ' of ' . $pagination->page_limit . '</b></td>';
			$footer .= '</tr>';
		}

		$pagination->table	= $table;
		$pagination->footer	= $footer;
		// $pagination->csv	= $this->get_export();
		return $pagination;
	}

	// private function get_export() {
	// 	$datefilter	= $this->input->post('datefilter');
	// 	$datefilter	= $this->date->dateDbFormat($datefilter);
	// 	$supplier	= $this->input->post('supplier');

	// 	$result			= $this->asset_transaction->getArAgingExport($datefilter, $supplier);
	// 	$total_aging	= $this->asset_transaction->getArAgingTotal($datefilter, $supplier);

	// 	$header = array(
	// 		'Supplier',
	// 		'Reference',
	// 		'Transaction Date',
	// 		'Terms',
	// 		'Due Date',
	// 		'Current',
	// 		'1 - 30',
	// 		'31 -60 Days',
	// 		'Over 60 Days',
	// 		'Balance'
	// 	);

	// 	$csv = '';
	// 	$csv .= 'Accounts Payable Aging';
	// 	$csv .= "\n\n";
	// 	$csv .= '"Date:","' . $this->date->dateFormat($datefilter) . '"';
	// 	$csv .= "\n\n";
	// 	$csv .= '"' . implode('","', $header) . '"';
	// 	if (empty($result)) {
	// 		$csv .= 'No Records Found';
	// 	}
	// 	foreach ($result as $key => $row) {
	// 		$csv .= "\n";
	// 		$csv .= '"' . $row->supplier . '",';
	// 		$csv .= '"' . $row->voucherno . '",';
	// 		$csv .= '"' . $this->date->dateFormat($row->transactiondate) . '",';
	// 		$csv .= '"' . $row->terms . '",';
	// 		$csv .= '"' . $this->date->dateFormat($row->duedate) . '",';
	// 		$csv .= '"' . number_format($row->current, 2) . '",';
	// 		$csv .= '"' . number_format($row->thirty, 2) . '",';
	// 		$csv .= '"' . number_format($row->sixty, 2) . '",';
	// 		$csv .= '"' . number_format($row->oversixty, 2) . '",';
	// 		$csv .= '"' . number_format($row->balance, 2) . '"';
	// 	}
		
	// 	$csv .= "\n";
	// 	$csv .= '"Totals:",';
	// 	$csv .= '"",';
	// 	$csv .= '"",';
	// 	$csv .= '"",';
	// 	$csv .= '"",';
	// 	$csv .= '"' . number_format($total_aging->current_total, 2) . '",';
	// 	$csv .= '"' . number_format($total_aging->thirty_total, 2) . '",';
	// 	$csv .= '"' . number_format($total_aging->sixty_total, 2) . '",';
	// 	$csv .= '"' . number_format($total_aging->oversixty_total, 2) . '",';
	// 	$csv .= '"' . number_format($total_aging->balance_total, 2) . '"';
		
	// 	return $csv;
	// }


}
?>