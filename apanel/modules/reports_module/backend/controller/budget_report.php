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
			$budget_desc = ($row->budgetdesc) ? $row->budgetdesc : '';
			$total = $row->january * 12;
			$table .= '<tr>';
			$table .= '<td>' . $row->budget_code .'</td>';
			$table .= '<td>' . $budget_desc .'</td>';
			$table .= '<td class="text-right">' . number_format($total, 2) . '<input type="hidden" class="year_total" value="'.$total.'"/></td>';
			$table .= '<td class="text-right">' . number_format($row->january, 2) . '<input type="hidden" class="monthly_total" value="'.$row->january.'"/></td>';
			$table .= '<td class="text-right">' . number_format($row->february, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->march, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->april, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->may, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->june, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->july, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->august, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->september, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->october, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->november, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->december, 2) . '</td>';
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
			'Budget Code',
			'Budget Description',
			'Total Budget',
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
		$yearly_total 	= 0;
		$total_month 	= 0;
		$year_total 	= 0;
		foreach ($result as $key => $row) {
			$yearly_total = $row->january * 12;
			$csv .= "\n";
			$csv .= '"' . $row->budget_code . '",';
			$csv .= '"' . $row->budgetdesc . '",';
			$csv .= '"' . $yearly_total . '",';
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

			$year_total 	+= $yearly_total;
			$total_month 	+= $row->january;
		}

		$csv .= "\n";
		$csv .= ',';
		$csv .= 'TOTAL,';
		$csv .= '"' . $year_total . '",';
		$csv .= '"' . $total_month . '",';
		$csv .= '"' . $total_month . '",';
		$csv .= '"' . $total_month . '",';
		$csv .= '"' . $total_month . '",';
		$csv .= '"' . $total_month . '",';
		$csv .= '"' . $total_month . '",';
		$csv .= '"' . $total_month . '",';
		$csv .= '"' . $total_month . '",';
		$csv .= '"' . $total_month . '",';
		$csv .= '"' . $total_month . '",';
		$csv .= '"' . $total_month . '",';
		$csv .= '"' . $total_month . '",';
		
		return $csv;
	}
}
?>