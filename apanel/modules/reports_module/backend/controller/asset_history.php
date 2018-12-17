<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui					= new ui();
		$this->input				= new input();
		$this->asset_history		= new asset_history();
		$this->report_model			= new report_model();
		$this->view->header_active	= 'report/';
		$this->fields				= array(
			'at.id',
			'voucherno',
			'assetclass',
			'asset_number',
			'serial_number',
			'transactiondate',
			'transactiontype',
			'amount',
			'transferto'
		);
	}

	public function view() {
		$this->view->title		= 'Asset History';
		$data['ui']				= $this->ui;
		$data['datefilter']		= date("M d, Y");
		$data['asset_list']	= $this->asset_history->getAsset();				
		$this->view->load('asset_history', $data);
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

		$datefilter			= $this->input->post('datefilter');
		$sort				= $this->input->post('sort');
		$asset			= $this->input->post('asset_number');

		$pagination		= $this->asset_history->getAssetHistory($this->fields, $sort, $asset, $datefilter);
		// $total_aging	= $this->asset_history->getArAgingTotal($datefilter, $supplier);
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
			$table .= '<tr>';
			$table .= '<td>' . $row->assetclass . '</td>';
			$table .= '<td>' . $row->asset_number . '</td>';
			$table .= '<td>' . $row->serial_number . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->transactiontype . '</td>';
			$table .= '<td class="text-right"><a data-id="' .  $row->voucherno. '" id="modal" href="'.BASE_URL.$tt.$row->voucherno.'" data-toggle="modal">'. number_format($row->amount, 2) . '</a></td>';
			$table .= '<td class="text-right">' . $row->transferto . '</td>';
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
		$pagination->csv	= $this->get_export();
		return $pagination;
	}

	private function get_export() {
		$datefilter	= $this->input->post('datefilter');
		$datefilter	= $this->date->dateDbFormat($datefilter);
		$sort	= $this->input->post('sort');
		$asset	= $this->input->post('asset_number');

		$result		= $this->asset_history->getAssetHistorycsv($this->fields, $sort, $asset, $datefilter);


		$header = array(
			'Asset Class',
			'Asset Number',
			'Serial Number / Engine Number',
			'Transaction Date',
			'Transaction Type',
			'Transaction Amount',
			'Transfer To'
		);

		$csv = '';
		$csv .= 'Asset History';
		$csv .= "\n\n";
		$csv .= '"Date:","' . $this->date->dateFormat($datefilter) . '"';
		$csv .= "\n\n";
		$csv .= '"' . implode('","', $header) . '"';
		if (empty($result)) {
			$csv .= 'No Records Found';
		}
		foreach ($result as $key => $row) {
			$csv .= "\n";
			$csv .= '"' . $row->assetclass . '",';
			$csv .= '"' . $row->asset_number . '",';
			$csv .= '"' . $row->serial_number . '",';
			$csv .= '"' . $this->date->dateFormat($row->transactiondate) . '",';
			$csv .= '"' . $row->transactiontype . '",';
			$csv .= '"' . number_format($row->amount, 2) . '",';
			$csv .= '"' . $row->transferto . '",';
		}
		
		// $csv .= "\n";
		// $csv .= '"Totals:",';
		// $csv .= '"",';
		// $csv .= '"",';
		// $csv .= '"",';
		// $csv .= '"",';
		// $csv .= '"' . number_format($total_aging->current_total, 2) . '",';
		// $csv .= '"' . number_format($total_aging->thirty_total, 2) . '",';
		// $csv .= '"' . number_format($total_aging->sixty_total, 2) . '",';
		// $csv .= '"' . number_format($total_aging->oversixty_total, 2) . '",';
		// $csv .= '"' . number_format($total_aging->balance_total, 2) . '"';
		
		return $csv;
	}


}
?>