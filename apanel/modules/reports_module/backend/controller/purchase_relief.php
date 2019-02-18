<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->report   	    = new purchase_relief();
		$this->report_model 	= new report_model;
		$this->item_model		= new item_model();
		$this->session			= new session();
		$this->companycode      = COMPANYCODE;
		$this->data             = array();
		$this->view->header_active = 'purchase_relief/';
	}

	public function listing($year = false) {
		$this->view->title = 'Purchase Relief Report';
		
		$data['ui'] = $this->ui;
		$year						= ($year) ? $year : date('Y');
		$data['year']				= $year;
		
		// $data['year_list']			= $this->report->getYearList();
		$data['vendor_list']		= $this->report->retrieveVendorList();
		$data['datefilter'] 		= $this->date->datefilterMonth();
		// $data['datefilter'] 		= "Nov 1,2018 - Nov 30,2018";
        $getCompany 				= $this->report->getCompany($this->companycode);
		$data['companytin']			= $getCompany->tin;
        $data['companyname']        = $getCompany->companyname;
        $data['companyaddress']     = $getCompany->address;
		$data['taxyear']			= $getCompany->taxyear;
		$data['periodstart']		= $getCompany->periodstart;
		$this->view->load('purchase_relief', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function display_amount($month, $vendorcode, $vendor, $amount){
	
		return "<a class='clickable' data-id=\"".$month.'/'. $vendorcode .'/'. $vendor . '/' .$amount . '">'.$amount.'</a>';
	}

	public function ajax_list(){
		$data 		= $this->input->post(array('vendor','datefilter','sort'));
		$datefilter 	= 	explode('-', $data['datefilter']);
		$dates			= 	array();
		foreach ($datefilter as $date) {
			$dates[] = $this->date->dateDbFormat($date);
		}	
	
        $vendfilter = $data['vendor'];
        $sortfilter = $data['sort'];

        $pagination = $this->report->getPurchaseReliefPagination($vendfilter, $sortfilter, $dates[0], $dates[1]);
		
		$table 	=	$tabledetails 	=	"";
        if (empty($pagination->result)) {
			$table = '<tr><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>';
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
			$table .= '<td class="text-right"><a href="'.BASE_URL.'purchase/purchase_receipt/view/'.$row->voucherno.'">' . number_format($row->netamount,2) . '</a></td>';
			// $table .= '<td class="text-right">' . number_format($row->vat_exempt,2) . '</td>';
			// $table .= '<td class="text-right">' . number_format($row->vat_zerorated,2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->vat_sales,2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->service,2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->goods,2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->capital,2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->totaltax,2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->grosstaxable,2) . '</td>';
			$table .= '</tr>';
		}
		$footerdtl 			= 	$this->report->getAmountTotal($vendfilter, $sortfilter, $dates[0], $dates[1]);
		$totalgross 		= 	isset($footerdtl->netamount)		?	$footerdtl->netamount		: 	0;
		$totalexempt		= 	isset($footerdtl->vat_exempt)		?	$footerdtl->vat_exempt		: 	0;
		$zerorated  		= 	isset($footerdtl->vat_zerorated)	?	$footerdtl->vat_zerorated	: 	0;
		$taxablesale		= 	isset($footerdtl->vat_sales)		?	$footerdtl->vat_sales		: 	0;
		$servicetotal		= 	isset($footerdtl->service)		    ?	$footerdtl->service		    : 	0;
		$goodstotal 		= 	isset($footerdtl->goods)		    ?	$footerdtl->goods		    : 	0;
		$capitaltotal  		= 	isset($footerdtl->capital)			?	$footerdtl->capital			: 	0;
		$outputtax 			= 	isset($footerdtl->totaltax)			?	$footerdtl->totaltax		: 	0;
		$grtaxable  		= 	isset($footerdtl->grosstaxable)		?	$footerdtl->grosstaxable	: 	0;

		if ($pagination->page_limit > 1) {
			$tabledetails .= '<tr class="success">
								<td colspan="10" class="text-center">Page ' . $pagination->page . ' of ' . $pagination->page_limit . '</td>
							</tr>';
		}

		$tabledetails .= '<tr class="warning">
							<th colspan="3">Grand Total:</th>
							<th class="text-right">' . number_format($totalgross,2) . '</th>
							<th class="text-right">' . number_format($taxablesale, 2) . '</th>
							<th class="text-right">' . number_format($servicetotal, 2) . '</th>
							<th class="text-right">' . number_format($goodstotal, 2) . '</th>
							<th class="text-right">' . number_format($capitaltotal, 2) . '</th>
							<th class="text-right">' . number_format($outputtax, 2) . '</th>
							<th class="text-right">' . number_format($grtaxable, 2) . '</th>
						</tr>';
						// <th class="text-right">' . number_format($totalexempt, 2) . '</th>
						// <th class="text-right">' . number_format($zerorated, 2) . '</th>

		$pagination->table = $table;
		$pagination->tabledetails	= $tabledetails;
		$pagination->csv 			= $this->get_csv($vendfilter, $data['datefilter'], $sortfilter);
		return $pagination;
	}

	public function get_csv($vendfilter, $date, $sortfilter) {
		$datefilter 	= 	explode('-', $date);
		$dates			= 	array();
		foreach ($datefilter as $date) {
			$dates[] = $this->date->dateDbFormat($date);
		}	
		
        $vendfilter = urldecode($vendfilter);
		$sortfilter = urldecode($sortfilter);

		$details 	= $this->report->getPurchaseReliefDetails($vendfilter, $sortfilter, $dates[0], $dates[1]);
		$company 	= $this->report->getCompany($this->companycode);
		
		$header 	=	array('TAXABLE MONTH','TIN','VENDOR','GROSS AMOUNT','TAXABLE PURCHASE','PURCHASE OF SERVICES','PURCHASE OF CAPITAL GOODS','PURCHASE OF GOODS OTHER THAN CAPITAL GOODS','INPUT TAX','GROSS TAXABLE PURCHASE');
		
		$totalamount=	0;

		$table = '';
		$table .= '"PURCHASE RELIEF REPORT"';
		$table .= "\n\n";
		$table .= '"SUMMARY LIST OF PURCHASES"';
		$table .= "\n\n";
		$table .= '"PURCHASE TRANSACTION"';
		$table .= "\n";
		$table .= '"RECONCILIATION OF LISTING FOR ENFORCEMENT"';
		$table .= "\n";
		$table .= '"FOR '.strtoupper($this->date->dateFormat($dates[0])) . ' to ' . strtoupper($this->date->dateFormat($dates[1])).'"';
		$table .= "\n\n";
		$table .= '"TIN : ","'.$company->tin.'"';
		$table .= "\n";
		$table .= '"OWNER\'S NAME : ","'.strtoupper($company->companyname).'"';
		$table .= "\n";
		$table .= '"OWNER\'S TRADE NAME : ","'.strtoupper($company->companyname).'"';
		$table .= "\n";
		$table .= '"OWNER\'S ADDRESS : ","'.strtoupper($company->address).'"';
		$table .= "\n\n";

		$totalgross 	= 0;
		$totalexempt	= 0;
		$zerorated  	= 0;
		$taxablesale	= 0;
		$outputtax 		= 0;
		$grtaxable  	= 0;
		$totalservice 	= 0;
		$totalgoods 	= 0;
		$totalcapital 	= 0;
		
		$table .= '"' . implode('","', $header) . '"';
		$table .= "\n";

		foreach ($details as $key => $row) {
			$transactiondate = isset($row->transactiondate)	? strtoupper($this->date->dateFormat($row->transactiondate)) : "";
			$tinno 			 = isset($row->tinno) 			? $row->tinno 					: "";
			$partnername 	 = isset($row->partnername) 	? strtoupper($row->partnername) : "";
			$netamount 		 = isset($row->netamount) 		? $row->netamount 				: 0;
			$vat_sales 		 = isset($row->vat_sales) 		? $row->vat_sales 				: 0;
			$service 		 = isset($row->service) 		? $row->service 				: 0;
			$goods 		 	 = isset($row->goods) 			? $row->goods 					: 0;
			$capital 		 = isset($row->capital) 		? $row->capital 				: 0;
			$totaltax 		 = isset($row->totaltax) 		? $row->totaltax 				: 0;
			$grosstaxable 	 = isset($row->grosstaxable) 	? $row->grosstaxable 			: 0;

			$table 	.= '"'.$transactiondate.'","'.$tinno.'","'.$partnername.'","'.number_format($netamount,2).'","'.number_format($vat_sales,2).'","'.number_format($service,2).'","'.number_format($goods,2).'","'.number_format($capital,2).'","'.number_format($totaltax,2).'","'.number_format($grosstaxable,2).'"';
			$table  .= "\n";

			// COMPUTING TOTAL
			$totalgross 	+= $row->netamount;
			$taxablesale	+= $row->vat_sales;
			$totalservice	+= $row->service;
			$totalgoods		+= $row->goods;
			$totalcapital	+= $row->capital;
			$outputtax 		+= $row->totaltax;
			$grtaxable  	+= $row->grosstaxable;
		}
		
		$table 	.= '"GRAND TOTAL : ","","","'.number_format($totalgross,2).'","'.number_format($taxablesale,2).'","'.number_format($totalservice,2).'","'.number_format($totalgoods,2).'","'.number_format($totalcapital,2).'","'.number_format($outputtax,2).'","'.number_format($grtaxable,2).'"';
		$table  .= "\n\n";
		$table  .= '"","","","","END OF REPORT","","","",""';

		return $table;
	}

	public function get_dat() {
		$data 		= $this->input->get(array('vendor','datefilter','sort'));
		$datefilter 	= 	explode('-', urldecode($data['datefilter']));
		$dates			= 	array();
		foreach ($datefilter as $date) {
			$dates[] = $this->date->dateDbFormat($date);
		}	
        $vendfilter = urldecode($data['vendor']);
		$sortfilter = urldecode($data['sort']);

		$details 		= 	$this->report->getPurchaseReliefDetails($vendfilter, $sortfilter, $dates[0], $dates[1]);
		$company 		= 	$this->report->getCompany($this->companycode);
		$companyname 	=	isset($company->companyname) ? $company->companyname 	:	"";
		$companyaddress =	isset($company->address) 	 ? $company->address 		:	"";
		$companytin 	=	isset($company->tin) 		 ? $company->tin 			:	"";
		$companytaxyr 	=	isset($company->taxyear) 	 ? $company->taxyear 		:	"";
		$companypstart 	=	isset($company->periodstart) ? $company->periodstart 	:	"";
		$companyrdo 	=	isset($company->rdo_code) 	 ? $company->rdo_code 		:	"";

		$filename = str_replace('-','',substr($companytin,0,11)).'P'.date("mY",strtotime($dates[1]));

		// $header = array('#','Taxable Month','TIN','Vendor','Gross Amount','Taxable Purchase','Purchase of Services','Purchase of Capital Goods','Purchase of Goods Other than Capital Goods','Input Tax','Gross Taxable Purchase');

		$csv 		= new exportCSV();

		$count = 1;
		$totalgross = $gross = 0;
		$totalexempt= $vat_exempt = 0;
		$zerorated  = $vat_zero = 0;
		$taxablesale= $vat_sales = 0;
		$outputtax 	= $totaltax = 0;
		$grttaxable = $grtaxable = 0;
		$totalservice= $service = 0;
		$totalgoods  = $goods = 0;
		$totalcapital= $capital = 0;
		$rowtype 	= "";
		$rowpartner = "";
		$rowtin 	= "";
		$rowaddress = "";
		$rowrdo 	= "";
		$rowno 		= "";
		$dattype 	= "P";

		if ($details) {
			// $csv->addRow($header);
			foreach ($details as $row) {
				// COMPUTING TOTAL
				$totalgross 	+= $row->netamount;
				$taxablesale	+= $row->vat_sales;
				$outputtax 		+= $row->totaltax;
				$grttaxable 	+= $row->grosstaxable;		
				$totalservice 	+= $row->service;
				$totalgoods 	+= $row->goods;	
				$totalcapital 	+= $row->capital;		
			}

			// Header;
			$rowtype 	= 	"H";
			$rowpartner =	$companyname;
			$rowaddress = 	$companyaddress;
			$rowtin 	= 	$companytin;
			$rowrdo 	=	"0".$companyrdo;
			$rowno 		=	12;
			$gross 		=	number_format($totalgross,2);
			$vat_exempt =	number_format($totalexempt,2);
			$vat_zero 	= 	number_format($zerorated,2);
			$vat_sales 	=	number_format($taxablesale,2);
			$taxamount 	=	number_format($outputtax,2);
			$grtaxable 	=	number_format($grttaxable,2);
			$goods 		=	number_format($totalgoods,2);
			$service 	=	number_format($totalservice,2);
			$capital 	=	number_format($totalcapital,2);

			$csv->addRow(array(
							$rowtype, 
							$dattype, 
							str_replace('-','',substr($rowtin,0,11)), 
							strtoupper($rowpartner), 
							"", 
							"", 
							"", 
							strtoupper($rowpartner), 
							strtoupper(str_replace(',','',str_replace('.','',$rowaddress))), 
							"", 
							$vat_exempt, 
							$vat_zero, 
							$service, 
							$capital, 
							$goods, 
							$taxamount, 
							$taxamount, 
							"0.00",
							$rowrdo,
							date("m/t/Y",strtotime($dates[1])),
							$rowno
						));

			foreach ($details as $row) {
				$partnername 	=	isset($row->partnername) 	?	$row->partnername 	:	"";
				$address 		=	isset($row->address) 		?	$row->address 		:	"";
				$tin 			= 	isset($row->tinno) 			?	$row->tinno  		: 	"";
				
				$rowtype 		= 	"D";
				$rowpartner 	=	$partnername;
				$rowaddress 	= 	$address;
				$rowtin  		= 	$tin;
				$rowrdo 		=	str_replace('-','',substr($companytin,0,11));
				$rowno 			=	"";
				$gross 			=	number_format($row->netamount,2);
				$vat_sales 		=	number_format($row->vat_sales,2);
				$totaltax 		=	number_format($row->totaltax,2);
				$grtaxable 		=	number_format($row->grosstaxable,2);
				$service 		=	number_format($row->service,2);
				$goods 			=	number_format($row->goods,2);
				$capital		=	number_format($row->capital,2);

				$csv->addRow(array(
					$rowtype, 
					$dattype, 
					str_replace('-','',substr($rowtin,0,11)), 
					strtoupper($rowpartner), 
					"", 
					"", 
					"", 
					strtoupper(str_replace(',','',str_replace('.','',$rowaddress))), 
					"", 
					$vat_exempt, 
					$vat_zero, 
					$service, 
					$capital, 
					$goods, 
					$taxamount, 
					$rowrdo,
					date("m/t/Y",strtotime($row->transactiondate)),
				));
				
				$count++;
			}
		} else {
			$csv->addRow(array("NO RECORDS FOUND."));
		}

		// $csv->addRow(array($count, "GRAND TOTAL: ", " ", " ", $totalgross, $taxablesale, $totalservice, $totalgoods, $totalcapital, $outputtax, $grttaxable));

		$csv->export($filename,'DAT');
		
		ob_end_flush();
	
	}
}