<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui					= new ui();
		$this->input				= new input();
		$this->budget_report				= new budget_report_model();
		$this->view->header_active	= 'report/';

	}

	public function view() {
		$this->view->title		= 'Budget Report';
		$data['ui']				= $this->ui;
		$data['datefilter']		= date("M d, Y");
		$data['budgetcenter_list']	= $this->budget_report->getBudgetCodeList();
		$data['year_list']	= $this->budget_report->getYearList();
		$ret = '';
		$this->view->load('budget_report', $data);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data			= $this->input->post(array('budgetcode', 'year'));
		extract($data);

		$pagination		= $this->budget_report->getBudgetReportList($budgetcode, $year);

		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$total = $row->january * 12;
			$table .= '<tr>';
			$table .= '<td>' . $row->accountname . '</td>';
			$table .= '<td>' . $row->budget_code . '</td>';
			$table .= '<td>' . date('Y', strtotime($row->date_approved)) . '</td>';
			$table .= '<td>' . number_format($row->january, 2) . '</td>';
			$table .= '<td>' . number_format($row->february, 2) . '</td>';
			$table .= '<td>' . number_format($row->march, 2) . '</td>';
			$table .= '<td>' . number_format($row->april, 2) . '</td>';
			$table .= '<td>' . number_format($row->may, 2) . '</td>';
			$table .= '<td>' . number_format($row->june, 2) . '</td>';
			$table .= '<td>' . number_format($row->july, 2) . '</td>';
			$table .= '<td>' . number_format($row->august, 2) . '</td>';
			$table .= '<td>' . number_format($row->september, 2) . '</td>';
			$table .= '<td>' . number_format($row->october, 2) . '</td>';
			$table .= '<td>' . number_format($row->november, 2) . '</td>';
			$table .= '<td>' . number_format($row->december, 2) . '</td>';
			$table .= '</tr>';
			$table .= '<tr>';
			$table .= '<td colspan = "12"></td>';
			$table .= '<td><b>Total</b></td>';
			$table .= '<td><b>' .number_format($total,2). '</b></td>';
			$table .= '</tr>';
		}

		$pagination->table	= $table;
		$pagination->csv	= $this->get_export();
		return $pagination;
	}

	private function get_export() {
		$data			= $this->input->post(array('budgetcode'));
		extract($data);

		$result			= $this->budget_report->getBudgetReportExport($budgetcode);

		$header = array(
			'Account Name',
			'Budget Code',
			'January',
			'February',
			'March',
			'April',
			'May',
			'June',
			'July',
			'August',
			'September',	
			'October',
			'November',
			'December'
		);

		$csv = '';
		$csv .= '"' . implode('","', $header) . '"';
		if (empty($result)) {
			$csv .= 'No Records Found';
		}
		foreach ($result as $key => $row) {
			$csv .= "\n";
			$csv .= '"' . $row->accountname . '",';
			$csv .= '"' . $row->budget_code . '",';
			$csv .= '"' . $row->january . '",';
			$csv .= '"' . $row->february . '",';
			$csv .= '"' . $row->march . '",';
			$csv .= '"' . $row->april . '",';
			$csv .= '"' . $row->may . '",';
			$csv .= '"' . $row->june . '",';
			$csv .= '"' . $row->july . '",';
			$csv .= '"' . $row->august . '",';
			$csv .= '"' . $row->september . '",';
			$csv .= '"' . $row->october . '",';
			$csv .= '"' . $row->november . '",';
			$csv .= '"' . $row->december . '",';
		}
		
		return $csv;
	}
}
?>