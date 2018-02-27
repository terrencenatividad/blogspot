<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->report   	    = new sales_register();
		$this->report_model 	= new report_model;
		$this->item_model		= new item_model();
		$this->session			= new session();
		$this->data = array();
		$this->view->header_active = 'sales_register/';
	}

	public function listing($year = false) {
		$this->report_model->generateSalesReportsTable();

		$this->view->title = 'Sales Register';
		
		$data['ui'] 				= $this->ui;
		$current_date 				= date('Y-m-d');
		$data['datefilter'] 		= $this->date->datefilterToday($current_date);
		$data['customer_list']		= $this->report->retrieveCustomerList();

		$this->view->load('sales_register', $data);
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
		$data 		= $this->input->post(array('customer','datefilter'));
		$datefilter = $data['datefilter'];
		$custfilter = $data['customer'];

		$pagination = $this->report->retrieveMainListing($datefilter, $custfilter);
		$ret_gtotal = $this->report->retrieveGrandTotal($datefilter, $custfilter);
		$retDRStat  = $this->report->countDeliveryReceipt($datefilter, $custfilter);

		$table 		= "";
		$subtotal  	= 0;
		$grandtotal = isset($ret_gtotal[0]->amount) 	? 	$ret_gtotal[0]->amount 	: 	0;
		$issuedDR 	= isset($retDRStat[0]->total)		?	$retDRStat[0]->total 	: 	0;
		$cancelledDR= isset($retDRStat[1]->total) 		?	$retDRStat[1]->total 	:	0;

		if(!empty($pagination->result)){
			foreach($pagination->result as $key => $row){

				$voucherno 			=	$row->voucherno;
				$company 			=	$row->company;
				$amount 			=	$row->amount;
				
				$table 			.=	"<tr>";
				$table 			.=  "<td class='text-left' colspan='2'>".$voucherno 	. "</td>";
				$table 			.=  "<td class='text-left'>".$company 	. "</td>";
				$table 			.=  "<td>".number_format($amount,2) 	. "</td>";
				$table 			.=  '</tr>';	

				$subtotal 		+= 	$amount;
			}
			$table 			.=	"<tr>";
			$table 			.=  "<td class='text-left'>
									<strong>Total Sales Delivery Issued</strong>
								</td>";
			$table 			.=  "<td class='text-right'>".number_format($issuedDR,0)."</td>";
			$table 			.=  "<td class='text-right'><strong>Sub Total</strong></td>";
			$table 			.=  "<td>".number_format($subtotal,2) . "</td>";
			$table 			.=  '</tr>';
			$table 			.=	"<tr>";
			$table 			.=  "<td class='text-left'>
									<strong>Total Sales Delivery Cancelled</strong>
								</td>";
			$table 			.=  "<td class='text-right'>".number_format($cancelledDR,0)."</td>";
			$table 			.=  "<td class='text-right'><strong>Grand Total</strong></td>";
			$table 			.=  "<td>".number_format($grandtotal,2). "</td>";
			$table 			.=  '</tr>';
		} else {
			$table 			.=	"<tr>";
			$table 			.=  "<td colspan='4' class='text-center'>No Records Found</td>";
			$table 			.=  '</tr>';	
		}

		$pagination->table 	= $table;
		$pagination->csv 	= $this->generateCSV($datefilter, $custfilter);
		return $pagination;
	}

	private function generateCSV($datefilter, $customer) {

		$header = array(
			'Sales Invoice',
			'Customer',
			'Amount'
		);

		$table 			= '';
		$total_amount 	= 0;
		$table = '"' . implode('","', $header) . '"';
		$table .= "\n";
		
		$list 		= $this->report->export_main($datefilter, $customer);
		$retDRStat  = $this->report->countDeliveryReceipt($datefilter, $customer);

		$issuedDR 	= isset($retDRStat[0]->total)		?	$retDRStat[0]->total 	: 	0;
		$cancelledDR= isset($retDRStat[1]->total) 		?	$retDRStat[1]->total 	:	0;

		foreach($list as $key => $row){
			$voucherno 			=	$row->voucherno;
			$company 			=	$row->company;
			$amount 			=	$row->amount;

			$table 			.=	'"'.$voucherno	.'",';
			$table 			.=  '"'.$company	.'",';
			$table 			.=  '"'.number_format($amount,2)	.'"';
			$table 			.= "\n";
			
			$total_amount 		+=	$amount;
		}

		$table 			.=	'"Total","","'.number_format($total_amount,2)	.'"';
		$table 			.= "\n\n";	
		$table 			.=	'"Total Sales Delivery Issued","","'.$issuedDR.'"';
		$table 			.= "\n";	
		$table 			.=	'"Total Sales Delivery Cancelled","","'.$cancelledDR.'"';

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