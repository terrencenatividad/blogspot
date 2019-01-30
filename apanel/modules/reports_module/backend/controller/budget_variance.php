<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui					= new ui();
		$this->input				= new input();
		$this->dateDbFormat = new date();
		$this->budget_report				= new budget_variance_model();
		$this->view->header_active	= 'report/';
	}

	public function view() {
		$this->view->title		= 'Budget Variance Report';
		$data['ui']				= $this->ui;
		$data['datefilter']		= date("M d, Y");
		$data['costcenter_list']	= $this->budget_report->getCostCenterList();
		$this->view->load('budget_variance', $data);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data			= $this->input->post(array('costcenter', 'budget_type', 'date'));
		extract($data);

		$pagination		= $this->budget_report->getBudgetList($costcenter, $budget_type, $date);

		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$variance = ($row->variance < 0) ? '('.number_format($row->variance, 2).')' : number_format($row->variance,2);
			$table .= '<tr>';
			$table .= '<td>' . $row->segment5 . '</td>';
			$table .= '<td>' . $row->description . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->effectivity_date, 1) . '</td>';
			$table .= '<td class = "amount text-right">' . number_format($row->amount, 2) . '</td>';
			$table .= '<td class = "actual text-right">' . number_format($row->actual, 2) . '</td>';
			$table .= '<td class = "variance text-right" data-val = '.$row->variance.' >' . str_replace('-','',$variance) . '</td>';
			$table .= '</tr>';
		}

		$pagination->table	= $table;
		$pagination->csv	= $this->get_export();
		return $pagination;
	}

	private function get_export() {
		$data			= $this->input->post(array('costcenter', 'budget_type'));
		extract($data);

		$result			= $this->budget_report->getBudgetReportExport($costcenter, $budget_type);

		$header = array(
			'Account Code',
			'Description',
			'Effectivity Date',
			'Budget',
			'Actual',
			'Variance'
		);

		$csv = '';
		$csv .= '"' . implode('","', $header) . '"';
		if (empty($result)) {
			$csv .= 'No Records Found';
		}
		foreach ($result as $key => $row) {
			$csv .= "\n";
			$csv .= '"' . $row->accountcode . '",';
			$csv .= '"' . $row->description . '",';
			$csv .= '"' . $row->effectivity_date . '",';
			$csv .= '"' . $row->amount . '",';
			$csv .= '"' . $row->actual . '",';
			$csv .= '"' . $row->variance . '",';
		}
		
		return $csv;
	}
}
?>