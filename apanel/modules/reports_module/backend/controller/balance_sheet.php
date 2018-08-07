<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui					= new ui();
		$this->balance_sheet_model	= new balance_sheet_model;
		$this->report_model			= new report_model;
		$this->view->header_active	= 'report/';
	}

	public function view($year = false) {
		$this->getHeaders($year);
		$this->report_model->generateBalanceTable();
		$year_now					= $this->balance_sheet_model->getYear();
		$year						= ($year) ? $year : $year_now;
		$this->view->title			= 'Balance Sheet';
		$data['ui']					= $this->ui;
		$data['year']				= $year;
		$data['year_list']			= $this->balance_sheet_model->getYearList($year_now);
		$data['monthly_view']		= $this->monthly_view($year);
		$data['quarterly_view']		= $this->quarterly_view($year);
		$data['year_view']			= $this->year_view($year);
		$data['header_monthly']		= $this->header_monthly;
		$data['header_quarterly']	= $this->header_quarterly;
		$data['header_yearly']		= $this->header_yearly;
		$this->view->load('balance_sheet', $data);
	}

	public function view_export($year, $tab) {
		$this->getHeaders($year);
		if ($tab == 'Monthly') {
			$list = $this->balance_sheet_model->getMonthly($year);
			$this->generateCSV($list, array_merge(array('Account'), $this->header_monthly), 'Monthly');
		} else if ($tab == 'Quarterly') {
			$list = $this->balance_sheet_model->getQuarterly($year);
			$this->generateCSV($list, array_merge(array('Account'), $this->header_quarterly), 'Quarterly');
		} else if ($tab == 'Yearly') {
			$list = $this->balance_sheet_model->getYearly($year);
			$this->generateCSV($list, array_merge(array('Account'), $this->header_yearly), 'Yearly');
		}
	}

	private function monthly_view($year) {
		$list	= $this->balance_sheet_model->getMonthly($year);
		$table	= $this->generateTable($list, 13);
		return $table;
	}

	private function quarterly_view($year) {
		$list	= $this->balance_sheet_model->getQuarterly($year);
		$table	= $this->generateTable($list, 5);
		return $table;
	}

	private function year_view($year) {
		$list	= $this->balance_sheet_model->getYearly($year);
		$table	= $this->generateTable($list, 3);
		return $table;
	}

	private function generateTable($list, $colspan) {
		$table = '';
		if (empty($list)) {
			$table = '<tr><td colspan="13" class="text-center"><b>No Records Found</b></td></tr>';
		} else {
			$total = array();
			foreach ($list as $key => $row1) {
				$total_type = array();
				$table .= '<tbody>';
				$table .= '<tr class="danger bold"><td colspan="' . $colspan . '" class="text-left">' . $key . '</td></tr>';
				foreach ($row1 as $key2 => $row2) {
					if ( ! empty($key2)) {
						$table .= '<tr class="bold"><td colspan="' . $colspan . '" class="text-left">' . $key2 . '</td></tr>';
					}
					foreach ($row2 as $key3 => $row3) {
						$table .= '<tr>';
						$table .= '<td class="text-left" style="padding-left: 40px;">' . $key3 . '</td>';
						foreach ($row3 as $key4 => $row4) {
							if ( ! isset($total_type[$key4])) {
								$total_type[$key4] = 0;
							}
							if (in_array($key, array('Liabilities', 'Equity'))) {
								if ( ! isset($total[$key4])) {
									$total[$key4] = 0;
								}
								$total[$key4] += (($row4) ? $row4 : 0);
							}
							$total_type[$key4] += (($row4) ? $row4 : 0);
							$table .= '<td>' . number_format($row4, 2) . '</td>';
						}
						$col_num = (isset($key4)) ? $key4 + 1 : 0;
						for ($num_col = $col_num; $num_col < ($colspan - 1); $num_col++) {
							$table .= '<td>' . number_format(0, 2) . '</td>';
						}
						$table .= '</tr>';
					}
				}
				$table .= '<tr class="warning bold">';
				$table .= '<td class="text-left">Total ' . $key . '</td>';
				foreach ($total_type as $tot) {
					$table .= '<td>' . number_format($tot, 2) . '</td>';
				}
				for ($num_col = $col_num; $num_col < ($colspan - 1); $num_col++) {
					$table .= '<td>' . number_format(0, 2) . '</td>';
				}
				$table .= '</tr>';
				$table .= '</tbody>';
			}
			$table .= '<tbody>';
			$table .= '<tr class="warning bold">';
			$table .= '<td class="text-left">Total Liabilities and Equity</td>';
			foreach ($total as $tot) {
				$table .= '<td>' . number_format($tot, 2) . '</td>';
			}
			for ($num_col = $col_num; $num_col < ($colspan - 1); $num_col++) {
				$table .= '<td>' . number_format(0, 2) . '</td>';
			}
			$table .= '</tr>';
			$table .= '</tbody>';
		}
		return $table;
	}

	private function generateCSV($list, $header, $filename) {
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="Balance Sheet - ' . $filename . '.csv"');
		$total = array();
		$table = '';
		$table .= 'Balance Sheet';
		$table .= "\n\n";
		$table .= '"' . implode('","', $header) . '"';
		foreach ($list as $key => $row1) {
			$total_type = array();
			$table .= "\n";
			$table .= "\"$key\"";
			foreach ($row1 as $key2 => $row2) {
				if ( ! empty($key2)) {
					$table .= "\n";
					$table .= "\"$key2\"";
				}
				foreach ($row2 as $key3 => $row3) {
					$table .= "\n";
					$table .= "\"$key3\"";
					foreach ($row3 as $key4 => $row4) {
						if ( ! isset($total_type[$key4])) {
							$total_type[$key4] = 0;
						}
						if (in_array($key, array('Liabilities', 'Equity'))) {
							if ( ! isset($total[$key4])) {
								$total[$key4] = 0;
							}
							$total[$key4] += (($row4) ? $row4 : 0);
						}
						$total_type[$key4] += (($row4) ? $row4 : 0);
						$table .= ",\"" . number_format($row4, 2) . "\"";
					}
				}
			}
			$table .= "\n";
			$table .= "\"Total $key\"";
			foreach ($total_type as $tot) {
				$table .= ",\"" . number_format($tot, 2) . "\"";
			}
		}
		$table .= "\n";
		$table .= "\"Total Liabilities and Equity\"";
		foreach ($total as $tot) {
			$table .= ",\"" . number_format($tot, 2) . "\"";
		}

		echo $table;
	}

	private function getHeaders($year) {
		$year = ($year) ? $year : $this->balance_sheet_model->getYear();

		$this->header_monthly	= array();
		$this->header_quarterly	= array();
		$this->header_yearly	= array();

		$period_start	= $this->balance_sheet_model->getPeriod();
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
				$period_index_end = 0;
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