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
		$total_amount = 0;
		$total_actual = 0;
		$total_variance = 0;
		$total_available = 0;
		$total_allocated = 0;
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$available = ($row->available < 0) ? '('.number_format($row->available, 2).')' : number_format($row->available,2);
			$variance = ($row->variance < 0) ? '('.number_format($row->variance, 2).')' : number_format($row->variance,2);
			$total_amount += $row->amount;
			$total_actual += $row->actual ;
			$total_available += $row->available;
			$total_allocated += $row->allocated;
			$total_var = ($total_variance < 0) ? '('.number_format($total_variance, 2).')' : number_format($total_variance,2);
			$total_avail = ($total_available < 0) ? '('.number_format($total_available, 2).')' : number_format($total_available,2);
			$table .= '<tr>';
			$table .= '<td>' . $row->segment5 . '</td>';
			$table .= '<td>' . $row->description . '</td>';
			$table .= '<td class = "amount text-right">' . number_format($row->amount, 2) . '</td>';
			$table .= '<td class = "amount text-right">' . str_replace('-','',$available) . '</td>';
			$table .= '<td class = "amount text-right">' . number_format($row->allocated, 2) . '</td>';
			$table .= '<td class = "actual text-right">' . number_format($row->actual, 2) . '</td>';
			$table .= '<td class = "variance text-right" data-val = '.$row->variance.' >' . str_replace('-','',$variance) . '</td>';
			$table .= '</tr>';
		}

		$pagination->table	= $table;
		$pagination->csv	= $this->get_export();
		$pagination->total_amount =  number_format($total_amount, 2);
		$pagination->total_actual = number_format($total_actual, 2);
		$pagination->total_available = number_format($total_available, 2);
		$pagination->total_allocated = number_format($total_allocated, 2);
		$pagination->total_var = str_replace('-','',$total_var);
		$pagination->total_var = str_replace('-','',$total_avail);
		return $pagination;
	}

	private function get_export() {
		$data			= $this->input->post(array('costcenter', 'budget_type', 'date'));
		extract($data);

		$result			= $this->budget_report->getBudgetReportExport($costcenter, $budget_type, $date);

		$header = array(
			'Account Code',
			'Description',
			'Budget',
			'Available',
			'Allocated',
			'Actual',
			'Variance'
		);

		$csv = '';
		$csv .= '"' . implode('","', $header) . '"';
		if (empty($result)) {
			$csv .= 'No Records Found';
		}
		$total_available = 0;
		$total_allocated = 0;
		$total_amount = 0;
		$total_actual = 0;
		$total_variance = 0;
		foreach ($result as $key => $row) {
			$total_available += $row->available;
			$total_allocated += $row->allocated;
			$total_amount += $row->amount;
			$total_actual += $row->actual ;
			$total_variance += $row->variance;
			$csv .= "\n";
			$csv .= '"' . $row->segment5 . '",';
			$csv .= '"' . $row->description . '",';
			$csv .= '"' . $row->amount . '",';
			$csv .= '"' . $row->available . '",';
			$csv .= '"' . $row->allocated . '",';
			$csv .= '"' . $row->actual . '",';
			$csv .= '"' . $row->variance . '",';
		}
		$csv .= "\n";
		$csv .= ',';
		$csv .= 'TOTAL,';
		$csv .= '"' . $total_available . '",';
		$csv .= '"' . $total_allocated . '",';
		$csv .= '"' . $total_amount . '",';
		$csv .= '"' . $total_actual . '",';
		$csv .= '"' . $total_variance . '",';
		
		return $csv;
	}
}
?>