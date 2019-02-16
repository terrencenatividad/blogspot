<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->seq 				= new seqcontrol();
		$this->sawt_model   	= new sawt_model();
		$this->session			= new session();
        $this->log 				= new log();
        $this->fields           = array (
            'companyname',
            'tin',
            'businesstype',
            'contactname'
        );
	}

	public function listing() {
        $this->view->title	= 'SAWT';
        $data               = (array) $this->sawt_model->getCompanyDetails($this->fields);
		$data['ui']			= $this->ui;
		$data['show_input'] = true;
		$this->view->load('sawt', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data		= $this->input->post(array('datepicker'));

		if (!empty($data)) {
			$date = explode("-", $data['datepicker']);
			//$month = $date[0];
			$year = $date[1];
			if($date[0] == 'January'){$month = '01';}
			else if($date[0] == 'February'){$month = '02';}
			else if($date[0] == 'March'){$month = '03';}
			else if($date[0] == 'April'){$month = '04';}
			else if($date[0] == 'May'){$month = '05';}
			else if($date[0] == 'June'){$month = '06';}
			else if($date[0] == 'July'){$month = '07';}
			else if($date[0] == 'August'){$month = '08';}
			else if($date[0] == 'September'){$month = '09';}
			else if($date[0] == 'October'){$month = '10';}
			else if($date[0] == 'November'){$month = '11';}
			else if($date[0] == 'December'){$month = '12';}
		}
		else {
			$month = '';
			$year = '';
		}

        $pagination	= $this->sawt_model->getSawtPagination($month, $year);
		$table		= '';
		if (empty($pagination)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
        }
        $count = 1;
		$totalwtaxamount = 0;
        if ($pagination) {
            foreach ($pagination as $key => $row) {
                $table .= '<tr>';
                $table .= '<td>' . $count . '</td>';
                $table .= '<td>' . $row->tinno . '</td>';
                if ($row->businesstype == 'Corporation') {
                    $table .= '<td>' . $row->partnername . '</td>';
                    $table .= '<td></td>';
                }
                else {
                    $table .= '<td></td>';
                    $table .= '<td>' . $row->last_name.', '.$row->first_name . '</td>';
                }
                $table .= '<td>' . $row->atc_code . '</td>';
                $table .= '<td>' . $row->paymenttype . '</td>';
                $table .= '<td style = "text-align:right">' . $row->taxbase_amount . '</td>';
                $table .= '<td style = "text-align:right">' . number_format($row->tax_rate, 2) . '</td>';
                $table .= '<td style = "text-align:right">' . $row->credit . '</td>';
                $table .= '</tr>';
                
                $count++;
                $totalwtaxamount = $totalwtaxamount + $row->credit;
            }
        }
        $table .= '<tr style = "font-weight:bold; background:#d9edf7">';
        $table .= '<td>Total</td>';
        $table .= '<td colspan = "9" style = "text-align:right">' . number_format($totalwtaxamount, 2) . '</td>';
        $table .= '</tr>';

		return array('table' => $table);
	}

    public function sawt_csv() {
		if (!empty($_GET["datepicker"])) {
			$datefilter = $_GET['datepicker'];
			$date = explode("-", $datefilter);
			if($date[0] == 'January'){$month = '01';}
			else if($date[0] == 'February'){$month = '02';}
			else if($date[0] == 'March'){$month = '03';}
			else if($date[0] == 'April'){$month = '04';}
			else if($date[0] == 'May'){$month = '05';}
			else if($date[0] == 'June'){$month = '06';}
			else if($date[0] == 'July'){$month = '07';}
			else if($date[0] == 'August'){$month = '08';}
			else if($date[0] == 'September'){$month = '09';}
			else if($date[0] == 'October'){$month = '10';}
			else if($date[0] == 'November'){$month = '11';}
			else if($date[0] == 'December'){$month = '12';}

			$year = $date[1];
		}
		else {
			$month = '';
			$year = '';
		}

        $company =  $this->sawt_model->getCompanyDetails($this->fields);
		$pagination	= $this->sawt_model->getSawtPagination($month, $year);
	
		if ($company->businesstype == 'Individual') {
			$filename = 'SAWT 1701';
			$form = '1701';
		}
		else {
			$filename = 'SAWT 1702';
			$form = '1702';
		}

		$excel = new PHPExcel();
		$excel->getProperties()
				->setCreator('Cid')
				->setLastModifiedBy('Cid')
				->setTitle($filename)
				->setSubject('SAWT')
				->setDescription('SAWT')
				->setKeywords('SAWT')
				->setCategory('SAWT');

		$excel->getActiveSheet()->setTitle('SAWT');
		$excel->setActiveSheetIndex(0);
		$sheet = $excel->getActiveSheet();

		$sheet->getCell('A1')->setValue('BIR FORM '.$form);
        $sheet->getCell('A2')->setValue('SUMMARY ALPHALIST OF WITHHOLDING TAXES (SAWT)');
		$sheet->getCell('A3')->setValue('FOR THE MONTH OF '.strtoupper($date[0]). ' '.$year);


		$sheet->getCell('A6')->setValue('TIN : '.$company->tin);
		$sheet->getCell('A7')->setValue("PAYEE'S NAME: ".strtoupper($company->companyname));

		$sheet->getCell('A11')->setValue('SEQ');
		$sheet->getCell('A12')->setValue('NO');
		$sheet->getCell('A14')->setValue('(1)');
		$sheet->getCell('A15')->setValue('------------------------------');

		$sheet->getCell('B11')->setValue('TAXPAYER');
		$sheet->getCell('B12')->setValue('IDENTIFICATION');
		$sheet->getCell('B13')->setValue('NO');
		$sheet->getCell('B14')->setValue('(2)');
		$sheet->getCell('B15')->setValue('------------------------------');

		$sheet->getCell('C11')->setValue('CORPORATION');
		$sheet->getCell('C12')->setValue('(Registered Name)');
		$sheet->getCell('C14')->setValue('(3)');
		$sheet->getCell('C15')->setValue('------------------------------');

		$sheet->getCell('D11')->setValue('INDIVIDUAL');
		$sheet->getCell('D12')->setValue('(Last Name, First Name, Middle Name)');
		$sheet->getCell('D14')->setValue('(4)');
		$sheet->getCell('D15')->setValue('------------------------------');

		$sheet->getCell('E11')->setValue('ATC CODE');
		$sheet->getCell('E14')->setValue('(5)');
		$sheet->getCell('E15')->setValue('------------------------------');

		$sheet->getCell('F11')->setValue('NATURE OF PAYMENT');
		$sheet->getCell('F15')->setValue('------------------------------');

		$sheet->getCell('G11')->setValue('AMOUNT OF');
		$sheet->getCell('G12')->setValue('INCOME PAYMENT');
		$sheet->getCell('G14')->setValue('(6)');
		$sheet->getCell('G15')->setValue('------------------------------');

		$sheet->getCell('H11')->setValue('TAX RATE');
		$sheet->getCell('H14')->setValue('(7)');
		$sheet->getCell('H15')->setValue('------------------------------');

		$sheet->getCell('I11')->setValue('AMOUNT OF');
		$sheet->getCell('I12')->setValue('TAX WITHHELD');
		$sheet->getCell('I14')->setValue('(8)');
		$sheet->getCell('I15')->setValue('------------------------------');

		$count = 1;
		$totalwtaxamount = 0;
		$cell_row = 16;
		if ($pagination) {
			foreach ($pagination as $row) {

				$businesstype = $row->businesstype;
				$contact_person = $row->last_name.', '.$row->first_name;

				$sheet->getCell('A'.$cell_row)->setValue($count);
				$sheet->getCell('B'.$cell_row)->setValue($row->tinno);
				if ($businesstype != 'Individual') {
					$sheet->getCell('C'.$cell_row)->setValue(strtoupper($row->partnername));
				}
				else {
					$sheet->getCell('D'.$cell_row)->setValue(strtoupper($contact_person));
				}
				$sheet->getCell('E'.$cell_row)->setValue($row->atc_code);
				$sheet->getCell('F'.$cell_row)->setValue(strtoupper($row->paymenttype));
				$sheet->getCell('G'.$cell_row)->setValue($row->taxbase_amount);
				$sheet->getCell('H'.$cell_row)->setValue($row->tax_rate);
				$sheet->getCell('I'.$cell_row)->setValue($row->credit);

				$count++;
				$cell_row++;
				$totalwtaxamount = $totalwtaxamount + $row->credit;
			}
		}

		else {
			$sheet->mergeCells('A16:I16');
			$sheet->getCell('A16')->setValue('NO CREDITABLE WITHHOLDING TAX');
			$sheet->getStyle('A16')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$cell_row = $cell_row + 1;
		}

		$fortotal_row = $cell_row + 1;
		$forend_report = $cell_row + 3;
		$fortotal_amount = $cell_row + 1;

		$sheet->getCell('A'.$fortotal_row)->setValue('Grand Total :');
		$sheet->getCell('A'.$forend_report)->setValue('END OF REPORT');

		$sheet->getCell('I'.$cell_row)->setValue('------------------');
		$sheet->getCell('I'.$fortotal_amount)->setValue($totalwtaxamount);
		// $sheet->getCell('I19')->setValue('==================');

		$sheet->getStyle('G15:I18')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		foreach ($excel->getAllSheets() as $sheet) {
			for ($col = 0; $col <= PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++) {
				$sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
			}
		}

		$filename.= '.xlsx';

		header('Content-type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Pragma: no-cache");
		header("Expires: 0");

		flush();

		$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

		$writer->save('php://output');
	}

	public function sawt_dat() {
		if (!empty($_GET["datepicker"])) {
			$datefilter = $_GET['datepicker'];
			$date = explode("-", $datefilter);
			if($date[0] == 'January'){$month = '01';}
			else if($date[0] == 'February'){$month = '02';}
			else if($date[0] == 'March'){$month = '03';}
			else if($date[0] == 'April'){$month = '04';}
			else if($date[0] == 'May'){$month = '05';}
			else if($date[0] == 'June'){$month = '06';}
			else if($date[0] == 'July'){$month = '07';}
			else if($date[0] == 'August'){$month = '08';}
			else if($date[0] == 'September'){$month = '09';}
			else if($date[0] == 'October'){$month = '10';}
			else if($date[0] == 'November'){$month = '11';}
			else if($date[0] == 'December'){$month = '12';}

			$year = $date[1];
		}
		else {
			$month = '';
			$year = '';
		}

        $company =  $this->sawt_model->getCompanyDetails($this->fields);
		$pagination	= $this->sawt_model->getSawtPagination($month, $year);
	
		if ($company->businesstype == 'Individual') {
			$filename = 'SAWT 1701';
			$form = '1701';
		}
		else {
			$filename = 'SAWT 1702';
			$form = '1702';
		}

		$header = array('');

		$csv 		= new exportCSV();

		$count = 1;
		if ($pagination) {
			foreach ($pagination as $row) {
				$businesstype = $row->businesstype;
				$contact_person = $row->last_name.', '.$row->first_name;

				if ($businesstype != 'Individual') {
					$csv->addRow(array($count, $row->tinno, strtoupper($row->partnername), "", $row->atc_code, strtoupper($row->paymenttype), $row->taxbase_amount, $row->tax_rate, $row->credit));
				}
				else {
					$csv->addRow(array($count, $row->tinno, "", strtoupper($contact_person), $row->atc_code, strtoupper($row->paymenttype), $row->taxbase_amount, $row->tax_rate, $row->credit));
				}
				$count++;
			}
		}

		else {
			$csv->addRow(array("NO CREDITABLE WITHHOLDING TAX"));
		}

		$csv->export($filename,'DAT');
		
		ob_end_flush();
	
	}
}