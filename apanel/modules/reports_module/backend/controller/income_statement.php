<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->url 			   	 	= new url();
		$this->ui 					= new ui();
		$this->input 				= new input();
		$this->income_statement 	= new income_statement;
		$this->report_model			= new report_model;
		$this->view->header_active 	= 'report/';
	}

	public function view($year = false) {
		$this->getHeaders($year);
		$this->report_model->generateBalanceTable();
		$year_now					= $this->income_statement->getYear();
		$year						= ($year) ? $year : $year_now;
		$this->view->title 			= 'Income Statement';
		$data['ui'] 			    = $this->ui;
		$data['year'] 			    = $year;
		$data['year_list'] 		    = $this->income_statement->getYearList($year_now);
		$data['monthly_view'] 	    = $this->monthly_view($year);
		$data['quarterly_view']     = $this->quarterly_view($year);
		$data['year_view'] 		    = $this->year_view($year);
		$data['header_monthly']		= $this->header_monthly;
		$data['header_quarterly']	= $this->header_quarterly;
		$data['header_yearly']		= $this->header_yearly;
		$this->view->load('income_statement', $data);
	}

	public function view_export($year, $tab) {
		$this->getHeaders($year);
		if ($tab == 'Monthly') {
			$list = $this->income_statement->getMonthly($year);
			$this->generateCSV($list, array_merge(array('Account'), $this->header_monthly), 'Monthly');
		} else if ($tab == 'Quarterly') {
			$list = $this->income_statement->getQuarterly($year);
			$this->generateCSV($list, array_merge(array('Account'), $this->header_quarterly), 'Quarterly');
		} else if ($tab == 'Yearly') {
			$list = $this->income_statement->getYearly($year);
			$this->generateCSV($list, array_merge(array('Account'), $this->header_yearly), 'Yearly');
		}
	}

	private function monthly_view($year) {
		$list = $this->income_statement->getMonthly($year);
		$table = $this->generateTable($list, 13);
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
		if (empty($list)) {
			$table = '<tr><td colspan="13" class="text-center"><b>No Records Found</b></td></tr>';
		} else {
			$total = array();
			$total_gross 	= array();
			$total_intax 	= array();
			$prevkey  	= '';
			$nextkey 	= '';
			foreach ($list as $key => $row1) {
				$total_type = array();
				$total_revenue 	= array();
				$total_income 	= array();
				$total_cost 	= array();
				$total_expense 	= array();
				$prevkey 		= $key;
				
				$table .= '<tbody>';
				$table .= '<tr class="danger bold"><td colspan="' . $colspan . '" class="text-left">' . $key . '</td></tr>';
				// foreach ($row1 as $key2 => $row2) {
				// 	if ( ! empty($key2)) {
				// 		$table .= '<tr class="bold"><td colspan="' . $colspan . '" class="text-left">' . $key2 . '</td></tr>';
				// 	}
					foreach ($row1 as $key2 => $row2) {
						$table .= '<tr>';
						$table .= '<td class="text-left" style="padding-left: 40px;">' . $key2 . '</td>';
						foreach ($row2 as $key3 => $row3) {
							if ( ! isset($total_type[$key3])) {
								$total_type[$key3] = 0;
							}
	
							if ( ! isset($total_revenue[$key3])) {
								$total_revenue[$key3] = 0;
							}
	
							if ( ! isset($total_income[$key3])) {
								$total_income[$key3] = 0;
							}
	
							if ( ! isset($total_cost[$key3])) {
								$total_cost[$key3] = 0;
							}
	
							if ( ! isset($total_expense[$key3])) {
								$total_expense[$key3] = 0;
							}
	
							if ( ! isset($total_gross[$key3])) {
								$total_gross[$key3] = 0;
							}

							$total_revenue[$key3] 	+= ($key == 'Revenue' && ($row3)) ? $row3 : 0;
							$total_income[$key3] 	+= ($key == 'Income' && ($row3)) ? $row3 : 0;
							$total_cost[$key3] 		+= ($key == 'Cost' && ($row3)) ? $row3 : 0;
							$total_expense[$key3]  	+= ($key == 'Expense' && ($row3)) ? $row3 : 0;
							
							$total_gross[$key3] 	+= ($key == 'Revenue' && ($row3)) ? $row3 : 0;
							$total_gross[$key3] 	+= ($key == 'Income' && ($row3)) ? $row3 : 0;
							$total_gross[$key3] 	-= ($key == 'Cost' && ($row3)) ? $row3 : 0;

							$total_type[$key3] 		+= (($row3) ? $row3 : 0);
							$table .= '<td>' . (($row3 == 0) ? number_format(0, 2) : number_format($row3, 2)) . '</td>';
						}
						$col_num = (isset($key3)) ? $key3 + 1 : 0;
						for ($num_col = $col_num; $num_col < ($colspan - 1); $num_col++) {
							$table .= '<td>' . number_format(0, 2) . '</td>';
						}
						$table .= '</tr>';
					}
				//}
				$table .= '<tr class="warning bold">';
				$table .= '<td class="text-left">Total ' . $key . '</td>';
				foreach ($total_type as $tot) {
					$table .= '<td>' . number_format($tot, 2) . '</td>';
				}
				for ($num_col = $col_num; $num_col < ($colspan - 1); $num_col++) {
					$table .= '<td>' . number_format(0, 2) . '</td>';
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

				$table .= '</tbody>';
			}
			$table .= '<tbody>';

			$table .= '<tr class="warning bold">';
			$table .= '<td class="text-left">Total Income/(Loss) Before Income Tax</td>';
			foreach ($total_type as $tot => $tot_val) {
				$total_net_income 	= (isset($total_gross[$tot])) ? $total_gross[$tot] - $total_expense[$tot] : - $total_expense[$tot];
				$table 				.= '<td>' . (($total_net_income == 0) ? number_format(0, 2) : number_format($total_net_income, 2)) . '</td>';
			}
			$table .= '</tr>';

			$table .= '<tr><td colspan="'.$colspan.'"></td></tr>';

			$table .= '<tr class="warning bold">';
			$table .= '<td class="text-left">Income Tax</td>';
			if($total_intax){
				foreach ($total_intax as $tot => $tot_val) {
					$table 				.= '<td>' . (($tot_val == 0) ? number_format(0, 2) : number_format($tot_val, 2)) . '</td>';
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
				$table 				.= '<td>' . (($total_gross_income == 0) ? number_format(0, 2) : number_format($total_gross_income, 2)) . '</td>';
			}
			$table .= '</tr>';

			$table .= '<tr><td colspan="'.$colspan.'"></td></tr>';
	
			$table .= '</tbody>';
		}
		return $table;
	}

	private function generateCSV($list, $header, $filename) {
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="Income Statement - ' . $filename . '.csv"');
		$total = array();
		$total_gross 	= array();
		$total_intax 	= array();
		$prevkey  	= '';
		$nextkey 	= '';
		$table = '';
		$table .= 'Income Statement';
		$table .= "\n\n";
		$table .= '"' . implode('","', $header) . '"';
		foreach ($list as $key => $row1) {
			$total_type = array();
			$total_revenue 	= array();
			$total_income 	= array();
			$total_cost 	= array();
			$total_expense 	= array();
			$prevkey 		= $key;
			$table .= "\n";
			$table .= "\"$key\"";
			foreach ($row1 as $key2 => $row2) {
				$table .= "\n";
				$table .= "\"$key2\"";
				foreach ($row2 as $key3 => $row3) {
					if ( ! isset($total_type[$key3])) {
						$total_type[$key3] = 0;
					}

					if ( ! isset($total_revenue[$key3])) {
						$total_revenue[$key3] = 0;
					}

					if ( ! isset($total_income[$key3])) {
						$total_income[$key3] = 0;
					}

					if ( ! isset($total_cost[$key3])) {
						$total_cost[$key3] = 0;
					}

					if ( ! isset($total_expense[$key3])) {
						$total_expense[$key3] = 0;
					}

					if ( ! isset($total_gross[$key3])) {
						$total_gross[$key3] = 0;
					}

					$total_revenue[$key3] 	+= ($key == 'Revenue' && ($row3)) ? $row3 : 0;
					$total_income[$key3] 	+= ($key == 'Income' && ($row3)) ? $row3 : 0;
					$total_cost[$key3] 		+= ($key == 'Cost' && ($row3)) ? $row3 : 0;
					$total_expense[$key3]  	+= ($key == 'Expense' && ($row3)) ? $row3 : 0;
					
					$total_gross[$key3] 	+= ($key == 'Revenue' && ($row3)) ? $row3 : 0;
					$total_gross[$key3] 	+= ($key == 'Income' && ($row3)) ? $row3 : 0;
					$total_gross[$key3] 	-= ($key == 'Cost' && ($row3)) ? $row3 : 0;

					$total_type[$key3] 		+= (($row3) ? $row3 : 0);
					$table .= ",\"" . number_format($row3, 2) . "\"";
				}
			}
			$table .= "\n";
			$table .= "\"Total $key\"";
			foreach ($total_type as $tot) {
				$table .= ",\"" . number_format($tot, 2) . "\"";
			}

			if((($prevkey == 'Cost' && $nextkey == 'Revenue')) && $total_gross){
				$table .= "\n";
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

	private function getHeaders($year) {
		$year = ($year) ? $year : $this->income_statement->getYear();

		$this->header_monthly	= array();
		$this->header_quarterly	= array();
		$this->header_yearly	= array();

		$period_start	= $this->income_statement->getPeriod();
		$period_index	= $period_start - 1;

		$monthly = array(
			'Jan',
			'Feb',
			'Mar',
			'Apr',
			'May',
			'Jun',
			'Jul',
			'Aug',
			'Sep',
			'Oct',
			'Nov',
			'Dec'
		);
		$quarterly = array(
			'1st',
			'2nd',
			'3rd',
			'4th'
		);

		for ($x = 1; $x <= 12; $x++) {
			if ($period_index > 11) {
				$period_index = 0;
			}
			$this->header_monthly[] = $monthly[$period_index];

			$period_index++;
		}

		$period_index_start	= $period_start - 1;
		$period_index_end	= $period_index_start + 2;

		for ($x = 0; $x < 4; $x++) {
			if ($period_index_start > 11) {
				$period_index_start = 0;
			}
			if ($period_index_end > 11) {
				$period_index_end -= 12;
			}
			$this->header_quarterly[] = $quarterly[$x] . ' Quarter (' . $monthly[$period_index_start] . ' - ' . $monthly[$period_index_end] . ')';

			$period_index_start += 3;
			$period_index_end += 3;
		}

		$this->header_yearly = array(
			$year - 1,
			$year
		);
	}
}