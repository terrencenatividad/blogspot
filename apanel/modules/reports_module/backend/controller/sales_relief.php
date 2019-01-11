<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->report   	    = new sales_relief();
		$this->report_model 	= new report_model;
		$this->item_model		= new item_model();
		$this->session			= new session();
		$this->companycode      = COMPANYCODE;
		$this->data             = array();
		$this->view->header_active = 'sales_relief/';
	}

	public function listing($year = false) {
		$this->view->title = 'Sales Relief Report';
		
		$data['ui'] = $this->ui;
		$year						= ($year) ? $year : date('Y');
		$data['year']				= $year;
		
		// $data['year_list']			= $this->report->getYearList();
		$data['customer_list']		= $this->report->retrieveCustomerList();
		// $data['datefilter'] 		= $this->date->datefilterMonth();
		$data['datefilter'] 		= "Nov 1,2018 - Nov 30,2018";
        $getCompany 				= $this->report->getCompany($this->companycode);
		$data['companytin']			= $getCompany->tin;
        $data['companyname']        = $getCompany->companyname;
        $data['companyaddress']     = $getCompany->address;
		$data['taxyear']			= $getCompany->taxyear;
		$data['periodstart']		= $getCompany->periodstart;
		$this->view->load('sales_relief', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function display_amount($month, $customercode, $customer, $amount){
	
		return "<a class='clickable' data-id=\"".$month.'/'. $customercode .'/'. $customer . '/' .$amount . '">'.$amount.'</a>';
	}

	public function ajax_list(){
		$data 		= $this->input->post(array('customer','datefilter','sort'));
		$datefilter 	= 	explode('-', $data['datefilter']);
		$dates			= 	array();
		foreach ($datefilter as $date) {
			$dates[] = $this->date->dateDbFormat($date);
		}	
	
        $custfilter = $data['customer'];
        $sortfilter = $data['sort'];

        $pagination = $this->report->getSalesReliefPagination($custfilter, $sortfilter, $dates[0], $dates[1]);
		
		$table 	=	$tabledetails 	=	"";
        if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		} 

		$totalgross = 0;
		$totalexempt= 0;
		$zerorated  = 0;
		$taxablesale= 0;
		$outputtax 	= 0;
		$grtaxable  = 0;
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->tinno . '</td>';
			$table .= '<td>' . $row->partnername . '</td>';
			$table .= '<td class="text-right">' . number_format($row->netamount,2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->vat_exempt,2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->vat_zerorated,2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->vat_sales,2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->taxamount,2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->amount,2) . '</td>';
			$table .= '</tr>';
		}
		$footerdtl 			= 	$this->report->getAmountTotal($custfilter, $sortfilter, $dates[0], $dates[1]);
		$totalgross 		= 	isset($footerdtl->netamount)		?	$footerdtl->netamount		: 	0;
		$totalexempt		= 	isset($footerdtl->vat_exempt)		?	$footerdtl->vat_exempt		: 	0;
		$zerorated  		= 	isset($footerdtl->vat_zerorated)	?	$footerdtl->vat_zerorated	: 	0;
		$taxablesale		= 	isset($footerdtl->vat_sales)		?	$footerdtl->vat_sales		: 	0;
		$outputtax 			= 	isset($footerdtl->taxamount)		?	$footerdtl->taxamount		: 	0;
		$grtaxable  		= 	isset($footerdtl->amount)			?	$footerdtl->amount			: 	0;

		if ($pagination->page_limit > 1) {
			$tabledetails .= '<tr class="success">
								<td colspan="9" class="text-center">Page ' . $pagination->page . ' of ' . $pagination->page_limit . '</td>
							</tr>';
		}

		$tabledetails .= '<tr class="warning">
							<th colspan="3">Grand Total:</th>
							<th class="text-right">' . number_format($totalgross,2) . '</th>
							<th class="text-right">' . number_format($totalexempt, 2) . '</th>
							<th class="text-right">' . number_format($zerorated, 2) . '</th>
							<th class="text-right">' . number_format($taxablesale, 2) . '</th>
							<th class="text-right">' . number_format($outputtax, 2) . '</th>
							<th class="text-right">' . number_format($grtaxable, 2) . '</th>
						</tr>';

		$pagination->table = $table;
		$pagination->tabledetails	= $tabledetails;
		return $pagination;
	}

	public function get_csv() {
		$data 		= $this->input->get(array('customer','datefilter','sort'));
		$datefilter 	= 	explode('-', urldecode($data['datefilter']));
		$dates			= 	array();
		foreach ($datefilter as $date) {
			$dates[] = $this->date->dateDbFormat($date);
		}	
		
        $custfilter = urldecode($data['customer']);
		$sortfilter = urldecode($data['sort']);

		$details 	= $this->report->getSalesReliefDetails($custfilter, $sortfilter, $dates[0], $dates[1]);
		$company 	= $this->report->getCompany($this->companycode);
		
		$filename = 'Sales Relief';

		$excel = new PHPExcel();
		$excel->getProperties()
				->setCreator('Cid')
				->setLastModifiedBy('Cid')
				->setTitle($filename)
				->setSubject('Sales Relief')
				->setDescription('Sales Relief')
				->setKeywords('Sales Relief')
				->setCategory('Sales Relief');

		$excel->getActiveSheet()->setTitle('Sales Relief');
		$excel->setActiveSheetIndex(0);
		$sheet = $excel->getActiveSheet();

		// $sheet->getCell('A1')->setValue('SALES RELIEF '.$dates[0].' - '.$dates[1]);
		$sheet->getCell('A1')->setValue('SUMMARY LIST OF SALES');

		$sheet->getCell('A3')->setValue('SALES TRANSACTION');
		$sheet->getCell('A4')->setValue('RECONCILIATION OF LISTING FOR ENFORCEMENT');
		$sheet->getCell('A5')->setValue('FOR '.strtoupper($this->date->dateFormat($date[0])). ' to '.strtoupper($this->date->dateFormat($date[1])));

		$sheet->getCell('A7')->setValue('TIN: '.$company->tin);
		$sheet->getCell('A8')->setValue("OWNER'S NAME: ".strtoupper($company->companyname));
		$sheet->getCell('A9')->setValue("OWNER'S TRADE NAME: ".strtoupper($company->companyname));
		$sheet->getCell('A10')->setValue("OWNER'S ADDRESS: ".strtoupper($company->address));

		// HEADER STARTS HERE
		$sheet->getCell('A12')->setValue('TAXABLE MONTH');
		$sheet->getCell('B12')->setValue('TIN');
		$sheet->getCell('C12')->setValue('CUSTOMER');
		$sheet->getCell('D12')->setValue('GROSS SALES');
		$sheet->getCell('E12')->setValue('EXEMPT SALES');
		$sheet->getCell('F12')->setValue('ZERO RATED SALES');
		$sheet->getCell('G12')->setValue('TAXABLE SALES');
		$sheet->getCell('H12')->setValue('OUTPUT TAX');
		$sheet->getCell('I12')->setValue('GROSS TAXABLE SALES');

		$totalgross = 0;
		$totalexempt= 0;
		$zerorated  = 0;
		$taxablesale= 0;
		$outputtax 	= 0;
		$grtaxable  = 0;
		$cell_row   = 13;
		if ($details) {
			foreach ($details as $row) {
				$sheet->getCell('A'.$cell_row)->setValue($row->transactiondate);
				$sheet->getCell('B'.$cell_row)->setValue($row->tinno);
				$sheet->getCell('C'.$cell_row)->setValue(strtoupper($row->partnername));
				$sheet->getCell('D'.$cell_row)->setValue($row->netamount);
				$sheet->getCell('E'.$cell_row)->setValue($row->vat_exempt);
				$sheet->getCell('F'.$cell_row)->setValue($row->vat_zerorated);
				$sheet->getCell('G'.$cell_row)->setValue($row->vat_sales);
				$sheet->getCell('H'.$cell_row)->setValue($row->taxamount);
				$sheet->getCell('I'.$cell_row)->setValue($row->amount);

				$cell_row++;

				// COMPUTING TOTAL
				$totalgross += $row->netamount;
				$totalexempt+= $row->vat_exempt;
				$zerorated  += $row->vat_zerorated;
				$taxablesale+= $row->vat_sales;
				$outputtax 	+= $row->taxamount;
				$grtaxable  += $row->amount;
			}
		}

		$fortotal_row 	= $cell_row + 1;
		$forend_report 	= $cell_row + 3;
		$fortotal_amount= $cell_row + 1;

		$sheet->getCell('A'.$fortotal_row)->setValue('GRAND TOTAL:');
		$sheet->getCell('A'.$forend_report)->setValue('END OF REPORT');
		
		// SETTING FOOTER TOTAL
		$sheet->getCell('D'.$fortotal_amount)->setValue($totalgross);
		$sheet->getCell('E'.$fortotal_amount)->setValue($totalexempt);
		$sheet->getCell('F'.$fortotal_amount)->setValue($zerorated);
		$sheet->getCell('G'.$fortotal_amount)->setValue($taxablesale);
		$sheet->getCell('H'.$fortotal_amount)->setValue($outputtax);
		$sheet->getCell('I'.$fortotal_amount)->setValue($grtaxable);

		$sheet->getStyle('D13:I38')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

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

	public function get_dat() {
		$data 		= $this->input->get(array('customer','datefilter','sort'));
		$datefilter 	= 	explode('-', urldecode($data['datefilter']));
		$dates			= 	array();
		foreach ($datefilter as $date) {
			$dates[] = $this->date->dateDbFormat($date);
		}	
        $custfilter = urldecode($data['customer']);
		$sortfilter = urldecode($data['sort']);

		$details 	= $this->report->getSalesReliefDetails($custfilter, $sortfilter, $dates[0], $dates[1]);
		$company 	= $this->report->getCompany($this->companycode);
		
		$filename = 'Sales Relief';

		$header = array('#','Taxable Month','TIN','Customer','Gross Sales','Exempt Sales','Zero Rated Sales','Taxable Sales','Output Tax','Gross Taxable Sales');

		$csv 		= new exportCSV();

		$count = 1;
		$totalgross = 0;
		$totalexempt= 0;
		$zerorated  = 0;
		$taxablesale= 0;
		$outputtax 	= 0;
		$grttaxable = 0;
		if ($details) {
			$csv->addRow($header);
			foreach ($details as $row) {
				$gross 		=	number_format($row->netamount,2);
				$vat_exempt =	number_format($row->vat_exempt,2);
				$vat_zero 	= 	number_format($row->vat_zerorated,2);
				$vat_sales 	=	number_format($row->vat_sales,2);
				$taxamount 	=	number_format($row->taxamount,2);
				$grtaxable 	=	number_format($row->amount,2);
				$csv->addRow(array($count, $row->transactiondate, $row->tinno, strtoupper($row->partnername), $gross, $vat_exempt, $vat_zero, $vat_sales, $taxamount, $grtaxable));

				// COMPUTING TOTAL
				$totalgross += $row->netamount;
				$totalexempt+= $row->vat_exempt;
				$zerorated  += $row->vat_zerorated;
				$taxablesale+= $row->vat_sales;
				$outputtax 	+= $row->taxamount;
				$grttaxable += $row->amount;				
				
				$count++;
			}
		} else {
			$csv->addRow(array("NO RECORDS FOUND."));
		}

		$csv->addRow(array($count, "GRAND TOTAL: ", " ", " ", $totalgross, $totalexempt, $zerorated, $taxablesale, $outputtax, $grttaxable));

		$csv->export($filename,'DAT');
		
		ob_end_flush();
	
	}
}