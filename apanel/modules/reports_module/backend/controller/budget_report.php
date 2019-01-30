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
		$total_jan = 0;
		$total_feb = 0;
		$total_mar = 0;
		$total_april = 0;
		$total_may = 0;
		$total_june = 0;
		$total_july = 0;
		$total_aug = 0;
		$total_sept = 0;
		$total_oct = 0;
		$total_nov = 0;
		$total_dec = 0;
		foreach ($pagination->result as $key => $row) {
			$budget_desc = ($row->budgetdesc) ? $row->budgetdesc : '';
			$january = ($row->january == '-') ? $row->january : number_format($row->january, 2);
			$february = ($row->february == '-') ? $row->february : number_format($row->february, 2);
			$march = ($row->march == '-') ? $row->march : number_format($row->march, 2);
			$april = ($row->april == '-') ? $row->april : number_format($row->april, 2);
			$may = ($row->may == '-') ? $row->may : number_format($row->may, 2);
			$june = ($row->june == '-') ? $row->june : number_format($row->june, 2);
			$july = ($row->july == '-') ? $row->july : number_format($row->july, 2);
			$august = ($row->august == '-') ? $row->august : number_format($row->august, 2);
			$september = ($row->september == '-') ? $row->september : number_format($row->september, 2);
			$october = ($row->october == '-') ? $row->october : number_format($row->october, 2);
			$november = ($row->november == '-') ? $row->november : number_format($row->november, 2);
			$december = ($row->december == '-') ? $row->december : number_format($row->december, 2);
			$total = $row->total;
			$jan_val = ($row->january == '-') ? 0 : $row->january;
			$feb_val = ($row->february == '-') ? 0 : $row->february;
			$mar_val = ($row->march == '-') ? 0 : $row->march;
			$april_val = ($row->april == '-') ? 0 : $row->april;
			$may_val = ($row->may == '-') ? 0 : $row->may;
			$june_val = ($row->june == '-') ? 0 : $row->june;
			$july_val = ($row->july == '-') ? 0 : $row->july;
			$aug_val = ($row->august == '-') ? 0 : $row->august;
			$sept_val = ($row->september == '-') ? 0 : $row->september;
			$oct_val = ($row->october == '-') ? 0 : $row->october;
			$nov_val = ($row->november == '-') ? 0 : $row->november;
			$dec_val = ($row->december == '-') ? 0 : $row->december;
			$total_jan += $jan_val;
			$total_feb += $feb_val;
			$total_mar += $mar_val;
			$total_april += $april_val;
			$total_may += $may_val;
			$total_june += $jan_val;
			$total_july += $july_val;
			$total_aug += $aug_val;
			$total_sept += $sept_val;
			$total_oct += $oct_val;
			$total_nov += $nov_val;
			$total_dec += $dec_val;
			$table .= '<tr>';
			$table .= '<td>' . $row->budget_code .'</td>';
			$table .= '<td>' . $budget_desc .'</td>';
			$table .= '<td class="text-right">' . number_format($total, 2) . '<input type="hidden" class="year_total" value="'.$total.'"/></td>';
			$table .= '<td class="text-right">' . $january . '<input type="hidden" class="monthly_total" value="'.$row->january.'"/></td>';
			$table .= '<td class="text-right">' . $february . '</td>';
			$table .= '<td class="text-right">' . $march . '</td>';
			$table .= '<td class="text-right">' . $april . '</td>';
			$table .= '<td class="text-right">' . $may . '</td>';
			$table .= '<td class="text-right">' . $june . '</td>';
			$table .= '<td class="text-right">' . $july . '</td>';
			$table .= '<td class="text-right">' . $august . '</td>';
			$table .= '<td class="text-right">' . $september . '</td>';
			$table .= '<td class="text-right">' . $october . '</td>';
			$table .= '<td class="text-right">' . $november . '</td>';
			$table .= '<td class="text-right">' . $december . '</td>';
			$table .= '</tr>';
		}
		
		$pagination->table	= $table;
		$pagination->csv	= $this->get_export();
		$pagination->jan	= $total_jan;
		$pagination->feb	= $total_feb;
		$pagination->mar	= $total_mar;
		$pagination->april	= $total_april;
		$pagination->may	= $total_may;
		$pagination->june	= $total_june;
		$pagination->july	= $total_july;
		$pagination->aug	= $total_aug;
		$pagination->sept	= $total_sept;
		$pagination->oct	= $total_oct;
		$pagination->nov	= $total_nov;
		$pagination->dec	= $total_dec;
		return $pagination;
	}

	private function get_export() {
		$data			= $this->input->post(array('budgetcode', 'year'));
		extract($data);

		$result			= $this->budget_report->getBudgetReportExport($budgetcode, $year);

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