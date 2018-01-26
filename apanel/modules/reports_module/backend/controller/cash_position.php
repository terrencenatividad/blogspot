<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui					= new ui();
		$this->input				= new input();
		$this->cash_position_model	= new cash_position_model();
		$this->view->header_active	= 'report/';
	}

	public function view() {
		$this->view->title = 'Cash Position';
		$data['ui'] = $this->ui;
		$data['date'] = date('M j, Y');
		$this->view->load('cash_position', $data);
	}

	public function view_export() {
		$datefilter		= $this->input->get('datefilter');
		$bank_balance	= $this->input->get('bank_balance');
		$for_deposit	= $this->input->get('for_deposit');
		$datefilter		= str_replace('%2C', ',', $datefilter);
		$dateasof		= str_replace('+', ' ', $datefilter);
		$datefilter		= $this->date->dateDbFormat($dateasof);
		$bank_balance	= ($bank_balance) ? $bank_balance : 0;
		$for_deposit	= ($for_deposit) ? $for_deposit : 0;

		$cash_position	= $this->cash_position_model->getCashPosition($datefilter);

		$cash_position->outstanding_checks = str_replace(',', '', $cash_position->outstanding_checks);
		$cash_position->check_for_release = str_replace(',', '', $cash_position->check_for_release);
		$cash_position->post_dated_checks = str_replace(',', '', $cash_position->post_dated_checks);

		$phpexcel = new PHPExcel();
		$phpexcel->getProperties()
				->setCreator("Versia")
				->setLastModifiedBy("Versia")
				->setTitle("Office 2007 XLSX Test Document")
				->setSubject("Office 2007 XLSX Test Document")
				->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
				->setKeywords("office 2007 openxml php");

		$phpexcel->setActiveSheetIndex(0)
				->setCellValue('A1', 'Versia')
				->setCellValue('A2', 'Cash Position Report')
				->setCellValue('A3', 'As of ' . $dateasof);

		$available_cash	= $bank_balance - $cash_position->outstanding_checks + $for_deposit;
		$cash_blance	= $available_cash - $cash_position->check_for_release - $cash_position->post_dated_checks;

		$phpexcel->setActiveSheetIndex(0)
				->setCellValue('A5', 'Bank Balance as of ' . $dateasof)
				->setCellValue('B5', round($bank_balance, 2))
				->setCellValue('A6', 'Less: Outstanding Checks')
				->setCellValue('B6', round($cash_position->outstanding_checks, 2))
				->setCellValue('A7', 'Add: For Deposit')
				->setCellValue('B7', round($for_deposit, 2))
				->setCellValue('A8', 'Available Cash as of ' . $dateasof)
				->setCellValue('B8', round($available_cash, 2))
				->setCellValue('A9', 'Less: Checks for Release')
				->setCellValue('B9', round($cash_position->check_for_release, 2))
				->setCellValue('A10', 'Less: Other Payments')
				->setCellValue('B10', round($cash_position->post_dated_checks, 2))
				->setCellValue('A11', 'Cash Balance as of ' . $dateasof)
				->setCellValue('B11', round($cash_blance, 2));

		$border_bottom = array(
			'borders' => array(
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				),
			),
		);
		$activesheet = $phpexcel->getActiveSheet();
		$activesheet->getStyle('B7')->applyFromArray($border_bottom);
		$activesheet->getStyle('B10')->applyFromArray($border_bottom);
		$activesheet->getColumnDimension('A')->setAutoSize(true);
		$activesheet->getColumnDimension('B')->setAutoSize(true);
		$activesheet->getDefaultRowDimension()->setRowHeight('15');
		$activesheet->setTitle('Cash Position');

		$released	= $this->cash_position_model->getCashPositionDetails($datefilter, 'released');

		$sheet_num = 1;

		if ($released) {
			$phpexcel->createSheet();
			$phpexcel->setActiveSheetIndex($sheet_num)
					->setCellValue('A1', 'Versia')
					->setCellValue('A2', 'Cash Position Report')
					->setCellValue('A3', 'As of ' . $dateasof);

			$phpexcel->setActiveSheetIndex($sheet_num)
						->setCellValue('A5', 'No.')
						->setCellValue('B5', 'Date of Check')
						->setCellValue('C5', 'Check #')
						->setCellValue('D5', 'Payee')
						->setCellValue('E5', 'Amount');

			foreach ($released as $key => $row) {
				$phpexcel->setActiveSheetIndex($sheet_num)
							->setCellValue('A' . ($key + 6), $key + 1)
							->setCellValue('B' . ($key + 6), $this->date->dateFormat($row->chequedate))
							->setCellValue('C' . ($key + 6), $row->chequenumber)
							->setCellValue('D' . ($key + 6), $row->partner)
							->setCellValue('E' . ($key + 6), round($row->chequeamount, 2));
			}

			$activesheet = $phpexcel->getActiveSheet();
			foreach (range('B', 'E') as $col) {
				$activesheet->getColumnDimension($col)->setAutoSize(true);
			}
			$activesheet->getDefaultRowDimension()->setRowHeight('15');
			$activesheet->setTitle('Outstanding Checks');
			$sheet_num++;
		}

		$uncleared	= $this->cash_position_model->getCashPositionDetails($datefilter, 'uncleared');


		if ($uncleared) {
			$phpexcel->createSheet();
			$phpexcel->setActiveSheetIndex($sheet_num)
					->setCellValue('A1', 'Versia')
					->setCellValue('A2', 'Cash Position Report')
					->setCellValue('A3', 'As of ' . $dateasof);

			$phpexcel->setActiveSheetIndex($sheet_num)
						->setCellValue('A5', 'No.')
						->setCellValue('B5', 'Date of Check')
						->setCellValue('C5', 'Check #')
						->setCellValue('D5', 'Payee')
						->setCellValue('E5', 'Amount');

			foreach ($uncleared as $key => $row) {
				$phpexcel->setActiveSheetIndex($sheet_num)
							->setCellValue('A' . ($key + 6), $key + 1)
							->setCellValue('B' . ($key + 6), $this->date->dateFormat($row->chequedate))
							->setCellValue('C' . ($key + 6), $row->chequenumber)
							->setCellValue('D' . ($key + 6), $row->partner)
							->setCellValue('E' . ($key + 6), round($row->chequeamount, 2));
			}

			$activesheet = $phpexcel->getActiveSheet();
			foreach (range('B', 'E') as $col) {
				$activesheet->getColumnDimension($col)->setAutoSize(true);
			}
			$activesheet->getDefaultRowDimension()->setRowHeight('15');
			$activesheet->setTitle('Check for Release');
			$sheet_num++;
		}

		$postdated	= $this->cash_position_model->getCashPositionDetails($datefilter, 'postdated');


		if ($postdated) {
			$phpexcel->createSheet();
			$phpexcel->setActiveSheetIndex($sheet_num)
					->setCellValue('A1', 'Versia')
					->setCellValue('A2', 'Cash Position Report')
					->setCellValue('A3', 'As of ' . $dateasof);

			$phpexcel->setActiveSheetIndex($sheet_num)
						->setCellValue('A5', 'No.')
						->setCellValue('B5', 'Date of Check')
						->setCellValue('C5', 'Check #')
						->setCellValue('D5', 'Payee')
						->setCellValue('E5', 'Amount');

			foreach ($postdated as $key => $row) {
				$phpexcel->setActiveSheetIndex($sheet_num)
							->setCellValue('A' . ($key + 6), $key + 1)
							->setCellValue('B' . ($key + 6), $this->date->dateFormat($row->chequedate))
							->setCellValue('C' . ($key + 6), $row->chequenumber)
							->setCellValue('D' . ($key + 6), $row->partner)
							->setCellValue('E' . ($key + 6), round($row->chequeamount, 2));
			}

			$activesheet = $phpexcel->getActiveSheet();
			foreach (range('B', 'E') as $col) {
				$activesheet->getColumnDimension($col)->setAutoSize(true);
			}
			$activesheet->getDefaultRowDimension()->setRowHeight('15');
			$activesheet->setTitle('Other Payments');
			$sheet_num++;
		}

		$phpexcel->setActiveSheetIndex(0);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Cash Position.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		
		$objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
		$objWriter->save('php://output');
		exit();

		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="' . MODULE_NAME . '-Breakdown.csv"');
		$csv		= '';
		$header = array(
			'Movement Date',
			'Previous Qty',
			'Movement Qty',
			'Stock Qty',
			'Purchase Price',
			'Price Average'
		);
		

		$csv = '"' . implode('","', $header) . '"';
		$grand_total = 0;
		$result = $this->average_cost_model->getAverageCostBreakdown($itemcode);
		foreach ($result as $key => $row) {
			$csv .= "\n";
			$csv .= '"' . $this->date->datetimeFormat($row->movementdate) . '",';
			$csv .= '"' . $row->documentno . '",';
			$csv .= '"' . number_format($row->movement_quantity) . '",';
			$csv .= '"' . number_format($row->stock_quantity) . '",';
			$csv .= '"' . (($row->movement_quantity > 0) ? number_format($row->purchase_price, 2) : '') . '",';
			$csv .= '"' . number_format($row->price_average, 2) . '"';
		}
		echo $csv;
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_get_data() {
		$datefilter = $this->input->post('datefilter');
		$datefilter = $this->date->dateDbFormat($datefilter);
		return $this->cash_position_model->getCashPosition($datefilter);
	}

	private function ajax_get_details() {
		$datefilter = $this->input->post('datefilter');
		$datefilter = $this->date->dateDbFormat($datefilter);
		$stat = $this->input->post('stat');
		$pagination = $this->cash_position_model->getCashPositionDetailsPagination($datefilter, $stat);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$table .= '<td><a href="' . BASE_URL . 'financials/accounts_payable/view/' . $row->apvoucherno . '" target="_blank">' . $row->voucherno . '</a></td>';
			$table .= '<td>' . $this->date->dateFormat($row->chequedate) . '</td>';
			$table .= '<td>' . $row->chequenumber . '</td>';
			$table .= '<td>' . $row->partner . '</td>';
			$table .= '<td class="text-right">' . number_format($row->chequeamount, 2) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

}