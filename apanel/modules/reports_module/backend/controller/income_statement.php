<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->url 			   	 	= new url();
		$this->ui 					= new ui();
		$this->input 				= new input();
		$this->income_statement 	= new income_statement;
		$this->view->header_active 	= 'report/';
	}

	public function view() {
		$this->report_model = new report_model;
    	$this->report_model->generateBalanceTable();

		$year 	= $this->input->post('year_filter');
		$month 	= $this->input->post('month_filter');

		$year 	= ($year) ? $year : date('Y');
		$month 	= ($month) ? $month : date('F');
		
		$this->view->title = 'Income Statement';

		$data['ui'] 			= $this->ui;
		$data['year'] 			= $year;
		$data['month'] 			= $month;
		$data['year_list'] 		= $this->income_statement->getYearList();
		$data['month_list'] 	= $this->income_statement->getMonthList();
		$data['monthly_view'] 	= $this->monthly_view($year,$month);
		$data['quarterly_view'] = $this->quarterly_view($year);
		$data['year_view'] 		= $this->year_view($year);
		
		$this->view->load('income_statement', $data);
	}

	private function monthly_view($year,$month) {
		$list = $this->income_statement->getMonthly($year,$month);
		$table = $this->generateTable($list, 2);
		return $table;
	}

	private function quarterly_view($year) {
		$list = $this->income_statement->getQuarterly($year);
		$table = $this->generateTable($list, 5);
		return $table;
	}

	private function year_view($year) {
		$list = $this->income_statement->getYearly($year);
		$table = $this->generateTable($list, 3);
		return $table;
	}

	private function generateTable($list, $colspan) {
		$table = '';
		$table .= '<tbody>';
		if (empty($list)) {
			$table = '<tr><td colspan="'.$colspan.'" class="text-center"><b>No Records Found</b></td></tr>';
		} else {
			$total 		= array();
			$prevkey  	= '';
			$nextkey 	= '';
			$total_gross 	= array();
			$total_intax 	= array();
			foreach ($list as $key => $row1) {
				$total_type 	= array();
				$total_revenue 	= array();
				$total_income 	= array();
				$total_cost 	= array();
				$total_expense 	= array();
				
				$prevkey 		= $key;
				$table .= '<tr class="danger bold"><td colspan="' . $colspan . '" class="text-left">' . $key . '</td></tr>';
				
				foreach ($row1 as $key3 => $row3) {
					$table .= '<tr>';
					$table .= '<td class="text-left" style="padding-left: 40px;">' . $key3 . '</td>';
					foreach ($row3 as $key4 => $row4) {
						if ( ! isset($total_type[$key4])) {
							$total_type[$key4] = 0;
						}

						if ( ! isset($total_revenue[$key4])) {
							$total_revenue[$key4] = 0;
						}

						if ( ! isset($total_income[$key4])) {
							$total_income[$key4] = 0;
						}

						if ( ! isset($total_cost[$key4])) {
							$total_cost[$key4] = 0;
						}

						if ( ! isset($total_expense[$key4])) {
							$total_expense[$key4] = 0;
						}

						if ( ! isset($total_gross[$key4])) {
							$total_gross[$key4] = 0;
						}
						
						$total_revenue[$key4] 	+= ($key == 'Revenue' && ($row4)) ? $row4 : 0;
						$total_income[$key4] 	+= ($key == 'Income' && ($row4)) ? $row4 : 0;
						$total_cost[$key4] 		+= ($key == 'Cost' && ($row4)) ? $row4 : 0;
						$total_expense[$key4]  	+= ($key == 'Expense' && ($row4)) ? $row4 : 0;
						
						$total_gross[$key4] 	+= ($key == 'Revenue' && ($row4)) ? $row4 : 0;
						$total_gross[$key4] 	+= ($key == 'Income' && ($row4)) ? $row4 : 0;
						$total_gross[$key4] 	-= ($key == 'Cost' && ($row4)) ? $row4 : 0;

						$total_type[$key4] 		+= (($row4) ? $row4 : 0);
						$table .= '<td>' . number_format($row4, 2) . '</td>';
						
					}
					$table .= '</tr>';
				}
				$table .= '<tr class="warning bold">';
				$table .= '<td class="text-left">Total ' . $key . '</td>';
				foreach ($total_type as $tot => $tol_val) {
					$table .= '<td>' . number_format($tol_val, 2) . '</td>';
				}
				$table .= '</tr>';

				if((($prevkey == 'Cost' && $nextkey == 'Revenue')) && $total_gross)
				{
					$table .= '<tr><td colspan="'.$colspan.'"></td></tr>';	
					$table .= '<tr class="warning bold">';
					$table .= '<td class="text-left">Gross Income</td>';
					foreach ($total_gross as $tot => $tot_val) {
						$table 	.= '<td>' . number_format($total_gross[$tot], 2) . '</td>';
					}
					$table .= '</tr>';
				}
				$table .= '<tr><td colspan="'.$colspan.'"></td></tr>';
				$nextkey 		= $prevkey;
			}
			
			$table .= '<tr class="warning bold">';
			$table .= '<td class="text-left">Total Income/(Loss) Before Income Tax</td>';

			foreach ($total_type as $tot => $tot_val) {
				$total_net_income 	= (isset($total_gross[$tot])) ? $total_gross[$tot] - $total_expense[$tot] : - $total_expense[$tot];
				$table 				.= '<td>' . number_format($total_net_income, 2) . '</td>';
			}

			$table .= '</tr>';

			$table .= '<tr><td colspan="'.$colspan.'"></td></tr>';
			$table .= '<tr class="warning bold">';
			$table .= '<td class="text-left">Income Tax</td>';
			if($total_intax){
				foreach ($total_intax as $tot => $tot_val) {
					$table 				.= '<td>' . number_format($tot_val, 2) . '</td>';
				}
			}else{
				foreach ($total_type as $tot => $tot_val) {
					$table 				.= '<td>' . number_format(0, 2) . '</td>';
				}
			}
			
			$table .= '</tr>';

			$table .= '<tr><td colspan="'.$colspan.'"></td></tr>';
			$table .= '<tr class="warning bold">';
			$table .= '<td class="text-left">Total Income/(Loss) After Income Tax</td>';
			foreach ($total_type as $tot => $tot_val) {
				$total_gross_income 	= (isset($total_gross[$tot])) ? $total_gross[$tot] - $total_expense[$tot] : - $total_expense[$tot];
				$table 				.= '<td>' . number_format($total_gross_income, 2) . '</td>';
			}
			$table .= '</tr>';

			$table .= '<tr><td colspan="'.$colspan.'"></td></tr>';
		}
		$table .= '</tbody>';
		return $table;
	}

	public function view_export($year, $tab, $month) {
		if ($tab == 'Monthly') {
			$header = array(
				ucfirst($month)
			);
			$list = $this->income_statement->getMonthly($year ,$month);
			$this->generateCSV($list, $header, 'Monthly');
		} else if ($tab == 'Quarterly') {
			$header = array(
				'Account',
				'1st Quarter (Jan - Mar)',
				'2nd Quarter (Apr - Jun)',
				'3rd Quarter (Jul - Sep)',
				'4th Quarter (Oct - Dec)'
			);
			$list = $this->income_statement->getQuarterly($year);
			$this->generateCSV($list, $header, 'Quarterly');
		} else if ($tab == 'Yearly') {
			$header = array(
				'Account',
				$year,
				$year - 1
			);
			$list = $this->income_statement->getYearly($year);
			$this->generateCSV($list, $header, 'Yearly');
		}
	}

	private function generateCSV($list, $header, $filename) {
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="Income Statement - ' . $filename . '.csv"');
		$total 			= array();
		$table 			= '';
		$table 			.= 'Income Statement';
		$table 			.= "\n\n";
		$table 			.= '"' . implode('","', $header) . '"';
		$total 			= array();
		$prevkey  		= '';
		$nextkey 		= '';
		$total_gross 	= array();
		$total_intax 	= array();

		foreach ($list as $key => $row1) {
			$total_type 	= array();
			$total_revenue 	= array();
			$total_income 	= array();
			$total_cost 	= array();
			$total_expense 	= array();

			$prevkey 		= $key;
			$table 			.= "\n\n";
			$table 			.= "\"$key\"";

			foreach ($row1 as $key3 => $row3) {
				$table 		.= "\n";
				$table 		.= "\"$key3\"";
				foreach ($row3 as $key4 => $row4) {
					if ( ! isset($total_type[$key4])) {
						$total_type[$key4] = 0;
					}

					if ( ! isset($total_revenue[$key4])) {
						$total_revenue[$key4] = 0;
					}

					if ( ! isset($total_income[$key4])) {
						$total_income[$key4] = 0;
					}

					if ( ! isset($total_cost[$key4])) {
						$total_cost[$key4] = 0;
					}

					if ( ! isset($total_expense[$key4])) {
						$total_expense[$key4] = 0;
					}

					if ( ! isset($total_gross[$key4])) {
						$total_gross[$key4] = 0;
					}
						
					$total_revenue[$key4] 	+= ($key == 'Revenue' && ($row4)) ? $row4 : 0;
					$total_income[$key4] 	+= ($key == 'Income' && ($row4)) ? $row4 : 0;
					$total_cost[$key4] 		+= ($key == 'Cost' && ($row4)) ? $row4 : 0;
					$total_expense[$key4]  	+= ($key == 'Expense' && ($row4)) ? $row4 : 0;
					
					$total_gross[$key4] 	+= ($key == 'Revenue' && ($row4)) ? $row4 : 0;
					$total_gross[$key4] 	+= ($key == 'Income' && ($row4)) ? $row4 : 0;
					$total_gross[$key4] 	-= ($key == 'Cost' && ($row4)) ? $row4 : 0;

					$total_type[$key4] 		+= (($row4) ? $row4 : 0);
					$table .= ",\"" . number_format($row4, 2) . "\"";
				}
			}

			$table .= "\n\n";
			$table .= "\"Total $key\"";
			foreach ($total_type as $tot) {
				$table .= ",\"" . number_format($tot, 2) . "\"";
			}

			if((($prevkey == 'Cost' && $nextkey == 'Revenue')) && $total_gross){
				$table .= "\n\n";
				$table .= "\"Gross Income\"";
				foreach ($total_gross as $tot => $tot_val) {
					$table .= ",\"" . number_format($total_gross[$tot], 2) . "\"";
				}
			}
			$nextkey 		= $prevkey;
		}

		$table .= "\n\n";
		$table .= "\"Total Income/(Loss) Before Income Tax\"";
		foreach ($total_type as $tot => $tot_val) {
			$total_net_income 	= (isset($total_gross[$tot])) ? $total_gross[$tot] - $total_expense[$tot] : - $total_expense[$tot];
			$table .= ",\"" . number_format($total_net_income, 2) . "\"";
		}

		$table .= "\n\n";
		$table .= "\"Income Tax\"";
		if($total_intax){
			foreach ($total_intax as $tot => $tot_val) {
				$table .= ",\"" . number_format($tot_val, 2) . "\"";
			}
		}else{
			foreach ($total_type as $tot => $tot_val) {
				$table .= ",\"" . number_format($tot_val, 2) . "\"";
			}
		}

		$table .= "\n\n";
		$table .= "\"Total Income/(Loss) After Income Tax\"";
		foreach ($total_type as $tot => $tot_val) {
			$total_gross_income 	= (isset($total_gross[$tot])) ? $total_gross[$tot] - $total_expense[$tot] : - $total_expense[$tot];
			$table .= ",\"" . number_format($total_gross_income, 2) . "\"";
		}

		echo $table;	
	}
}