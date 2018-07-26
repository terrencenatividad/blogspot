<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui					= new ui();
		$this->input				= new input();
		$this->ap_aging				= new ap_aging();
		$this->view->header_active	= 'report/';
	}

	public function view() {
		$this->view->title		= 'Accounts Payable Aging';
		$data['ui']				= $this->ui;
		$data['datefilter']		= date("M d, Y");
		$data['supplier_list']	= $this->ap_aging->getSupplierList();
		$this->view->load('ap_aging', $data);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$datefilter			= $this->input->post('datefilter');
		$datefilter			= $this->date->dateDbFormat($datefilter);
		$supplier			= $this->input->post('supplier');

		$pagination		= $this->ap_aging->getArAging($datefilter, $supplier);
		$total_aging	= $this->ap_aging->getArAgingTotal($datefilter, $supplier);

		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$table .= '<td>' . $row->supplier . '</td>';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->terms . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->duedate) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->current, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->thirty, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->sixty, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->oversixty, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->balance, 2) . '</td>';
			$table .= '</tr>';
		}

		$footer = '';

		if ($pagination->page_limit > 1) {
			$footer .= '<tr>';
			$footer .= '<td colspan="9" class="text-center"><b>Page: ' . $pagination->page . ' of ' . $pagination->page_limit . '</b></td>';
			$footer .= '</tr>';
		}

		$footer .= '<tr>';
		$footer .= '<td colspan="5" class="text-right"><b>Totals: </b></td>';
		$footer .= '<td class="text-right"><b>' . number_format($total_aging->current_total, 2) . '</b></td>';
		$footer .= '<td class="text-right"><b>' . number_format($total_aging->thirty_total, 2) . '</b></td>';
		$footer .= '<td class="text-right"><b>' . number_format($total_aging->sixty_total, 2) . '</b></td>';
		$footer .= '<td class="text-right"><b>' . number_format($total_aging->oversixty_total, 2) . '</b></td>';
		$footer .= '<td class="text-right"><b>' . number_format($total_aging->balance_total, 2) . '</b></td>';
		$footer .= '</tr>';

		$pagination->table	= $table;
		$pagination->footer	= $footer;
		$pagination->csv	= $this->get_export();
		return $pagination;
	}

	private function get_export() {
		$datefilter	= $this->input->post('datefilter');
		$datefilter	= $this->date->dateDbFormat($datefilter);
		$supplier	= $this->input->post('supplier');

		$result			= $this->ap_aging->getArAgingExport($datefilter, $supplier);
		$total_aging	= $this->ap_aging->getArAgingTotal($datefilter, $supplier);

		$header = array(
			'Supplier',
			'Reference',
			'Transaction Date',
			'Terms',
			'Due Date',
			'Current',
			'1 - 30',
			'31 -60 Days',
			'Over 60 Days',
			'Balance'
		);

		$csv = '';
		$csv .= 'Accounts Payable Aging';
		$csv .= "\n\n";
		$csv .= '"Date:","' . $this->date->dateFormat($datefilter) . '"';
		$csv .= "\n\n";
		$csv .= '"' . implode('","', $header) . '"';
		if (empty($result)) {
			$csv .= 'No Records Found';
		}
		foreach ($result as $key => $row) {
			$csv .= "\n";
			$csv .= '"' . $row->supplier . '",';
			$csv .= '"' . $row->voucherno . '",';
			$csv .= '"' . $this->date->dateFormat($row->transactiondate) . '",';
			$csv .= '"' . $row->terms . '",';
			$csv .= '"' . $this->date->dateFormat($row->duedate) . '",';
			$csv .= '"' . number_format($row->current, 2) . '",';
			$csv .= '"' . number_format($row->thirty, 2) . '",';
			$csv .= '"' . number_format($row->sixty, 2) . '",';
			$csv .= '"' . number_format($row->oversixty, 2) . '",';
			$csv .= '"' . number_format($row->balance, 2) . '"';
		}
		
		$csv .= "\n";
		$csv .= '"Totals:",';
		$csv .= '"",';
		$csv .= '"",';
		$csv .= '"",';
		$csv .= '"",';
		$csv .= '"' . number_format($total_aging->current_total, 2) . '",';
		$csv .= '"' . number_format($total_aging->thirty_total, 2) . '",';
		$csv .= '"' . number_format($total_aging->sixty_total, 2) . '",';
		$csv .= '"' . number_format($total_aging->oversixty_total, 2) . '",';
		$csv .= '"' . number_format($total_aging->balance_total, 2) . '"';
		
		return $csv;
	}


}
?>