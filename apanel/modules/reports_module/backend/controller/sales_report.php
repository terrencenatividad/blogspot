<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->report   	    = new sales_report();
		$this->report_model 	= new report_model;
		$this->item_model		= new item_model();
		$this->session			= new session();
		$this->data = array();
		$this->view->header_active = 'sales_report/';
	}

	public function listing($year = false) {
		$this->report_model->generateSalesReportsTable();

		$this->view->title = 'Sales Report';
		
		$data['ui'] = $this->ui;
		$year						= ($year) ? $year : date('Y');
		$data['year']				= $year;
		
		$data['year_list']			= $this->report->getYearList();
		$data['customer_list']		= $this->report->retrieveCustomerList();

		$this->view->load('sales_report', $data);
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

	public function main_listing(){
		$data 		= $this->input->post(array('month','customer','year'));
		$year 		= $data['year'];
		$custfilter = $data['customer'];

		$pagination = $this->report->retrieveMainListing($year, $custfilter);
		$table 	=	"";
		$total_jan 	=	$total_feb 	=	$total_march 	=	$total_april 	=	$total_may 		=	$total_june  	=	0;
		$total_july =	$total_aug 	=	$total_sept =	$total_oct 		=	$total_nov 		=	$total_decm 	=	0;
		
		foreach($pagination->result as $key => $row){

			$partnercode 		=	$row->partnercode;
			$customer 			=	$row->partnername;
			$january 			=	$row->jan;
			$february 			=	$row->feb;
			$march 				=	$row->march;
			$april 				=	$row->april;
			$may 				=	$row->may;
			$june 				=	$row->june;
			$july 				=	$row->july;
			$august 			=	$row->aug;
			$september 			=	$row->sept; 		
			$october 			=	$row->oct;
			$november 	 		=	$row->nov;
			$december 			=	$row->decm;

			$total_jan 	 		+=	str_replace(',','',$january);
			$total_feb 	 		+=	str_replace(',','',$february);
			$total_march 		+= 	str_replace(',','',$march);
			$total_april 		+=	str_replace(',','',$april);
			$total_may 			+=	str_replace(',','',$may);
			$total_june 		+=	str_replace(',','',$june);
			$total_july 		+=	str_replace(',','',$july);
			$total_aug			+=	str_replace(',','',$august);
			$total_sept 		+=	str_replace(',','',$september);
			$total_oct 			+=	str_replace(',','',$october); 	
			$total_nov 			+=	str_replace(',','',$november);
			$total_decm 		+=	str_replace(',','',$december);

			$table 			.=	"<tr>";
			$table 			.=	"<td class='text-left'>".$customer."</td>";
			$table 			.=  ($january > 0) 		?  "<td>".$this->display_amount(1, $partnercode, $customer, $january) 	. "</td>" : 	"<td>".number_format($january, 2)	."</td>";
			$table 			.=  ($february > 0) 	?  "<td>".$this->display_amount(2, $partnercode, $customer, $february) 	. "</td>" : 	"<td>".number_format($february, 2)	."</td>";
			$table 			.=  ($march > 0) 		?  "<td>".$this->display_amount(3, $partnercode, $customer, $march) 	. "</td>" : 	"<td>".number_format($march, 2)		."</td>";
			$table 			.=  ($april > 0) 		?  "<td>".$this->display_amount(4, $partnercode, $customer, $april) 	. "</td>" : 	"<td>".number_format($april, 2)		."</td>";			
			$table 			.=  ($may > 0) 			?  "<td>".$this->display_amount(5, $partnercode, $customer, $may) 		. "</td>" : 	"<td>".number_format($may, 2)		."</td>";
			$table 			.=  ($june > 0) 		?  "<td>".$this->display_amount(6, $partnercode, $customer, $june) 		. "</td>" : 	"<td>".number_format($june, 2)		."</td>";
			$table 			.=  ($july > 0) 		?  "<td>".$this->display_amount(7, $partnercode, $customer, $july) 		. "</td>" : 	"<td>".number_format($july, 2)		."</td>";
			$table 			.=  ($august > 0) 		?  "<td>".$this->display_amount(8, $partnercode, $customer, $august) 	. "</td>" : 	"<td>".number_format($august, 2)	."</td>";
			$table 			.=  ($september > 0) 	?  "<td>".$this->display_amount(9, $partnercode, $customer, $september) . "</td>" : 	"<td>".number_format($september, 2)	."</td>";
			$table 			.=  ($october > 0) 		?  "<td>".$this->display_amount(10, $partnercode, $customer, $october) 	. "</td>" : 	"<td>".number_format($october, 2)	."</td>";
			$table 			.=  ($november > 0) 	?  "<td>".$this->display_amount(11, $partnercode, $customer, $november) . "</td>" : 	"<td>".number_format($november, 2)	."</td>";
			$table 			.=  ($december > 0) 	?  "<td>".$this->display_amount(12, $partnercode, $customer, $december) . "</td>" : 	"<td>".number_format($december, 2)	."</td>";
			$table 			.= '</tr>';	
		}

		$table 	.= 	"<tr>";
		$table 	.= 	"<td  class='text-left'><strong>Total</strong></td>";
		$table 	.= 	"<td><strong>".number_format($total_jan,2)."</strong></td>";
		$table 	.= 	"<td><strong>".number_format($total_feb,2)."</strong></td>";
		$table 	.= 	"<td><strong>".number_format($total_march,2)."</strong></td>";
		$table 	.= 	"<td><strong>".number_format($total_april,2)."</strong></td>";
		$table 	.= 	"<td><strong>".number_format($total_may,2)."</strong></td>";
		$table 	.= 	"<td><strong>".number_format($total_june,2)."</strong></td>";
		$table 	.= 	"<td><strong>".number_format($total_july,2)."</strong></td>";
		$table 	.= 	"<td><strong>".number_format($total_aug,2)."</strong></td>";
		$table 	.= 	"<td><strong>".number_format($total_sept,2)."</strong></td>";
		$table 	.= 	"<td><strong>".number_format($total_oct,2)."</strong></td>";
		$table 	.= 	"<td><strong>".number_format($total_nov,2)."</strong></td>";
		$table 	.= 	"<td><strong>".number_format($total_decm,2)."</strong></td>";
		$table 	.= 	"</tr>";

		$pagination->table 	= $table;
		$pagination->csv 	= $this->generateCSV($year, $custfilter);
		return $pagination;
	}

	private function generateCSV($year, $customer) {

		$header = array(
			'Customer',
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

		$total_jan 	=	$total_feb 	=	$total_march 	=	$total_april 	=	$total_may 		=	$total_june  	=	0;
		$total_july =	$total_aug 	=	$total_sept =	$total_oct 		=	$total_nov 		=	$total_decm 	=	0;

		$table = '';
		$table .= 'Sales Report';
		$table .= "\n\n";
		$table .= '"' . implode('","', $header) . '"';
		$table .= "\n";
		
		$list = $this->report->export_main($year, $customer);

		foreach($list as $key => $row){
			$partnercode 		=	$row->partnercode;
			$customer 			=	$row->partnername;
			$january 			=	$row->jan;
			$february 			=	$row->feb;
			$march 				=	$row->march;
			$april 				=	$row->april;
			$may 				=	$row->may;
			$june 				=	$row->june;
			$july 				=	$row->july;
			$august 			=	$row->aug;
			$september 			=	$row->sept; 		
			$october 			=	$row->oct;
			$november 	 		=	$row->nov;
			$december 			=	$row->decm;

			$total_jan 	 		+=	str_replace(',','',$january);
			$total_feb 	 		+=	str_replace(',','',$february);
			$total_march 		+= 	str_replace(',','',$march);
			$total_april 		+=	str_replace(',','',$april);
			$total_may 			+=	str_replace(',','',$may);
			$total_june 		+=	str_replace(',','',$june);
			$total_july 		+=	str_replace(',','',$july);
			$total_aug			+=	str_replace(',','',$august);
			$total_sept 		+=	str_replace(',','',$september);
			$total_oct 			+=	str_replace(',','',$october); 	
			$total_nov 			+=	str_replace(',','',$november);
			$total_decm 		+=	str_replace(',','',$december);

			$table 			.=	'"'.$customer	.'",';
			$table 			.=  ($january > 0) 	? '"'.	$january 	.'",'	: 	'"'.number_format($january,2) 		.'",';
			$table 			.=  ($february > 0) ? '"'.	$february 	.'",' 	:  	'"'.number_format($february,2) 		.'",';
			$table 			.=  ($march > 0) 	? '"'.	$march 		.'",' 	:	'"'.number_format($march,2) 		.'",';
			$table 			.=  ($april > 0) 	? '"'.	$april 		.'",' 	:	'"'.number_format($april,2) 		.'",';			
			$table 			.=  ($may > 0) 	 	? '"'.	$may 		.'",' 	: 	'"'.number_format($may,2) 			.'",';
			$table 			.=  ($june > 0) 	? '"'. 	$june 		.'",' 	: 	'"'.number_format($june,2) 			.'",';
			$table 			.=  ($july > 0 ) 	? '"'.	$july 		.'",' 	: 	'"'.number_format($july,2) 			.'",';
			$table 			.=  ($august > 0) 	? '"'.	$august 	.'",' 	: 	'"'.number_format($august,2) 		.'",';
			$table 			.=  ($september > 0)? '"'.	$september 	.'",' 	: 	'"'.number_format($september,2) 	.'",';
			$table 			.=  ($october > 0) 	? '"'.	$october 	.'",' 	: 	'"'.number_format($october,2) 		.'",';
			$table 			.=  ($november > 0) ? '"'.	$november 	.'",' 	: 	'"'.number_format($november,2) 		.'",';
			$table 			.=  ($december > 0) ? '"'.	$december 	.'",' 	: 	'"'.number_format($december,2) 		.'",';
			$table 			.= "\n";	
		}

		$table 			.=	'"Total",';
		$table 			.=  '"'.number_format($total_jan,2)		.'",';
		$table 			.=  '"'.number_format($total_feb,2)		.'",';
		$table 			.=  '"'.number_format($total_march,2)	.'",';
		$table 			.=  '"'.number_format($total_april,2)	.'",';			
		$table 			.=  '"'.number_format($total_may,2)		.'",';
		$table 			.=  '"'.number_format($total_june,2)	.'",';
		$table 			.=  '"'.number_format($total_july,2)	.'",';
		$table 			.=  '"'.number_format($total_aug,2)		.'",';
		$table 			.=  '"'.number_format($total_sept,2)	.'",';
		$table 			.=  '"'.number_format($total_oct,2)		.'",';
		$table 			.=  '"'.number_format($total_nov,2)		.'",';
		$table 			.=  '"'.number_format($total_decm,2) 	.'"';
		$table 			.= "\n";	

		return $table;
	}

	public function daily_listing(){
		$data = $this->input->post(array('month','customer','year'));
		$pagination = $this->report->getDaily($data);

		$table = '';
		$totalamount 	=	0;
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$totalamount 	+= 	$row->totalamount;
			$table .= '<tr>';
			$table .= '<td>' . $this->date->dateFormat($row->date) . '</td>' ;
			$table .= '<td><a href="'.BASE_URL.'sales/sales_invoice/view/' . $row->invoice . '">' . $row->invoice . '</a></td>';
			$table .= '<td>' . $row->reference . '</td>' ;
			$table .= '<td><a href="'.BASE_URL.'financials/accounts_receivable/view/' . $row->ar . '">'.$row->ar.'</a></td>';
			$table .= '<td class="text-right">' . number_format($row->totalamount,2) . '</td>';
			$table .= '</tr>';
		}
		$table .= '<tr>';
		$table .= '<td colspan="3"></td>';
		$table .= '<td ><strong>Total</strong></td>';
		$table .= '<td class="text-right"><strong>' . number_format($totalamount,2) . '</strong></td>';
		$table .= '</tr>';
		$pagination->table = $table;
		$pagination->csv = $this->generateDailyCSV();
		return $pagination;
	}

	private function generateDailyCSV() {
		$data = $this->input->post(array('month','customer','year'));

		$customer_code 	= $data['customer'];

		$ret_details 	= $this->report->retrieveCustomerDetails($customer_code);

		$list = $this->report->export_daily($data);
		
		$header 	=	array('Date','Sales Invoice No.','Reference','AR No.','Amount');
		
		$totalamount=	0;

		$table = '';
		$table .= '"Customer : ","'.$ret_details->name.'"';
		$table .= "\n";
		$table .= '"Address : ","'.$ret_details->address1.'"';
		$table .= "\n\n";
		$table .= '"' . implode('","', $header) . '"';
		$table .= "\n";
		foreach ($list as $key => $row) {
			$totalamount 	+= 	$row->totalamount;
			$table .= '"'.$this->date->dateFormat($row->date).'",';
			$table .= '"'.$row->invoice.'",';
			$table .= '"'.$row->reference.'",';
			$table .= '"'.$row->ar.'",';
			$table .= '"'.number_format($row->totalamount,2).'"';
			$table .= "\n";
		}

		$table .= '"","","","Total","'.number_format($totalamount,2).'"';

		return $table;
	}
	
	private function getCustomerDetails(){
		$customer_code 	=	$this->input->post('customer');
		// echo $customer_code;
		$result 		= 	$this->report->retrieveCustomerDetails($customer_code);
		
		return $result;
	}
	private function amount($amount)
	{
		return number_format($amount,2);
	}
}