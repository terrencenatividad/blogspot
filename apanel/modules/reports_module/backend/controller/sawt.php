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

		$csv 	= $this->sawt_csv($date[0], $month, $year);
		return array('table' => $table, 'csv' => $csv);
	}

    public function sawt_csv($full_month, $month, $year) {

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

		$header 	=	array('SEQ NO', 'TAXPAYER IDENTIFICATION NO', 'CORPORATION (Registered Name)', 'INDIVIDUAL', 'ATC CODE', 'NATURE OF PAYMENT', 'AMOUNT OF INCOME PAYMENT', 'TAX RATE', 'AMOUNT OF TAX WITHHELD');
		
		$table = '';
		$table .= '"BIR FORM '.$form.'"';
		$table .= "\n";
		$table .= '"SUMMARY ALPHALIST OF WITHHOLDING TAXES (SAWT)"';
		$table .= "\n";
		$table .= '"FOR THE MONTH OF '.strtoupper($full_month). ' '.$year.'"';
		$table .= "\n\n";
		$table .= '"TIN : ","'.$company->tin.'"';
		$table .= "\n";
		$table .= '"PAYEE\'S NAME : ","'.strtoupper($company->companyname).'"';
		$table .= "\n\n";

		$totalwtaxamount = 0;

		$table .= '"' . implode('","', $header) . '"';
		$table .= "\n";
		$table .= '"(1)","(2)","(3)","(4)","(5)","","(6)","(7)","(8)"';
		$table .= "\n";
		$table .= '"------------------------------","------------------------------","------------------------------","------------------------------","------------------------------","------------------------------","------------------------------","------------------------------","------------------------------"';
		$table .= "\n";
		$count = 1;
		if ($pagination) {
			foreach ($pagination as $key => $row) {
				$businesstype = $row->businesstype;
				$contact_person = $row->last_name. ' ' .$row->first_name;

				if ($businesstype == 'Individual') {
					$table .= '"'.$count.'","'.$row->tinno.'","","'.strtoupper($contact_person).'","'.$row->atc_code.'","'.strtoupper($row->paymenttype).'","'.number_format($row->taxbase_amount,2).'","'.number_format($row->tax_rate,2).'","'.number_format($row->credit,2).'"';
				}
				else {
					$table .= '"'.$count.'","'.$row->tinno.'","'.strtoupper($row->partnername).'","","'.$row->atc_code.'","'.strtoupper($row->paymenttype).'","'.number_format($row->taxbase_amount,2).'","'.number_format($row->tax_rate,2).'","'.number_format($row->credit,2).'"';
				}
				
				$table  .= "\n";
				$totalwtaxamount += $row->credit;
				$count++;
			}
		}

		else {
			$table .= '"NO CREDITABLE WITHHOLDING TAX"';
		}
		
		$table 	.= '"GRAND TOTAL : ","","","","","","","","'.number_format($totalwtaxamount,2).'"';
		$table  .= "\n\n";
		$table  .= '"END OF REPORT"';

		return $table;
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