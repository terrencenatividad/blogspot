<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui					= new ui();
		$this->input				= new input();
		$this->asset_depreciation	= new asset_depreciation();
		$this->report_model			= new report_model();
		$this->view->header_active	= 'report/';
		$this->fields				= array(
			'department',
			'asset_id',
			'serial_number',
			'assetclass',
			'am.description',
			'd.depreciation_date',
			'am.capitalized_cost',
			'd.depreciation_amount'
		);
	}

	public function view() {
		$this->view->title		= 'Asset Depreciation List';
		$data['ui']				= $this->ui;
		$data['datefilter']		= date("M d, Y");
		// $data['supplier_list']	= $this->asset_depreciation->getSupplierList();
		$this->view->load('asset_depreciation', $data);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$pagination		= $this->asset_depreciation->getDepreciation($this->fields);
		$tt = '';
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			// $transtype = substr($row->voucherno, 0, 2);
			// if($transtype == 'PO')
			// 	$tt = "purchase/purchase_order/view/";
			// else if($transtype == 'PR')
			// 	$tt = "purchase/purchase_receipt/view/";
			// else if($transtype == 'AP')
			// 	$tt = "financials/accounts_payable/view/";
			$table .= '<tr>';
			$table .= '<td>' . $row->department . '</td>';
			$table .= '<td>' . $row->asset_id . '</td>';
			$table .= '<td>' . $row->serial_number . '</td>';
			$table .= '<td>' . $row->assetclass . '</td>';
			$table .= '<td>' . $row->description . '</td>';
			// $table .= '<td>' . $this->date->dateFormat($row->depreciation_date) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->capitalized_cost, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->depreciation_amount, 2) . '</td>';
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
		// $supplier	= $this->input->post('supplier');

		$result		= $this->asset_depreciation->getDepreciationcsv($this->fields);


		$header = array(
			'Department',
			'Asset Number',
			'Serial Number / Engine Number',
			'Asset Class',
			'Description',
			'Capitalized Cost',
			'Depreciation Amount'
		);

		$csv = '';
		$csv .= 'Asset Depreciation';
		$csv .= "\n\n";
		$csv .= '"Date:","' . $this->date->dateFormat($datefilter) . '"';
		$csv .= "\n\n";
		$csv .= '"' . implode('","', $header) . '"';
		if (empty($result)) {
			$csv .= 'No Records Found';
		}
		foreach ($result as $key => $row) {
			$csv .= "\n";
			$csv .= '"' . $row->department . '",';
			$csv .= '"' . $row->asset_id . '",';
			$csv .= '"' . $row->serial_number . '",';
			$csv .= '"' . $row->assetclass . '",';
			$csv .= '"' . $row->description . '",';
			$csv .= '"' . number_format($row->capitalized_cost, 2) . '",';
			$csv .= '"' . number_format($row->depreciation_amount, 2) . '",';
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