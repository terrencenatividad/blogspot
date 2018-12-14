<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->sales_journal	= new sales_journal();
		$this->item_model		= new item_model();
		$this->session			= new session();
		$this->show_input 	    = true;
		$this->data = array();
		$this->view->header_active  = 'report/';
	}

	public function listing() {
		$this->view->title = 'Sales Journal';
		$data['ui'] = $this->ui;
		$data['customer_list'] = $this->sales_journal->getCustomerList();
		$data['datefilter'] 	= $this->date->datefilterMonth();
		$data['show_input']  = $this->show_input;
		$this->view->load('sales_journal', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data = $this->input->post(array('daterangefilter','customer','sort'));
		$datefilter	= $data['daterangefilter'];
		$customer 	= $data['customer'];
		$sort      = $data['sort'];
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$date1 = $dates[0];
		$date2 = $dates[1];
		$pagination = $this->sales_journal->sales_journalList($date1, $date2, $customer, $sort);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}

		$totalamount  	=	$totaldiscount 	= 	$totalvat 	=	$totalnetpurchases 	= 	$totaldiscount1 =  	$totaldiscount2 =	$t  = $tot_amount = 0 ;
		
		foreach ($pagination->result as $key => $row) {
			$amount 			= $row->taxamount + $row->amount;
			$tot_amount 		+= $amount; 
			$totalamount 		+= $row->amount;
			$totaldiscount1		+= $row->discount;
			$totaldiscount2		+= $row->d_amount;
			$totalvat 			+= $row->taxamount;
			$total_discount 	= $totaldiscount1 + $totaldiscount2;
			$totalnetpurchases 	+= $row->amount;
			$t  += ($row->discounttype == 'perc') ? $row->d_amount : $row->discount;

			$table .= '<tr>';
			$table .= '<td>' . $this->date($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->tinno . '</td>';
			$table .= '<td>' . $row->customer . '</td>';
			$table .= '<td>' . $row->remarks . '</td>';
			$table .= '<td><a href="'.BASE_URL."sales/sales_invoice/view/" . $row->ref_no . '">' .$row->ref_no. '</a></td>';
			$table .= '<td>' . number_format($amount,2) . '</td>';
			if ($row->discounttype == 'amt'){
				$table .= '<td>' . number_format($row->discount,2) . '</td>';
			} else {
				$table .= '<td>' . number_format($row->d_amount,2) . '</td>';
			}
			$table .= '<td>' . number_format($row->taxamount,2) . '</td>';
			$table .= '<td>' . number_format($row->amount,2) . '</td>';
			$table .= '</tr>';
		}

		$table .= '<tr>';
		$table .= '<td colspan = "4"></td>';
		$table .= '<td><strong>Total<strong></td>';
		$table .= '<td>' . number_format($tot_amount,2) . '</td>';
		$table .= '<td>' . number_format($t,2) . '</td>';
		$table .= '<td>' . number_format($totalvat,2) . '</td>';
		$table .= '<td>' . number_format($totalnetpurchases,2) . '</td>';
		$table .= '</tr>';

		$pagination->table  = $table;
		$pagination->csv 	= $this->export();
		return $pagination;
	}

	private function export(){
		$data = $this->input->post(array('daterangefilter','customer','sort'));
		$datefilter	= $data['daterangefilter'];
		$customer 	= $data['customer'];
		$sort      = $data['sort'];
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$result = $this->sales_journal->fileExport($dates[0], $dates[1], $customer, $sort);
		$header = array("Date","Customer Tin","Customer Name","Description","Ref No.","Amount","Discount","VAT amount","Net Sales");

		$csv = 'Sales Journal';
		$csv .= "\n\n";
		$csv .= '"Date:","'.$this->date->dateFormat($dates[0]).' - '.$this->date->dateFormat($dates[1]).'"';
		$csv .= "\n\n";
		$csv .= '"' . implode('","', $header) . '"';
		$csv .= "\n";
		
		$totalamount  	=	$totaldiscount 	= 	$totalvat 	=	$totalnetpurchases 	= 	$tot_amount = 0 ;
		
		if (!empty($result)){
			foreach ($result as $key => $row){
				$amount 			= $row->taxamount + $row->amount;
				$tot_amount 		+= $amount;
				$totalamount 		+= $row->amount;
				$totaldiscount 		+= $row->discount;
				$totalvat 			+= $row->taxamount;
				$totalnetpurchases 	+= $row->amount;

				$csv .= '"' . $this->date($row->transactiondate) . '",';
				$csv .= '"' . $row->tinno . '",';
				$csv .= '"' . $row->customer . '",';
				$csv .= '"' . $row->remarks . '",';
				$csv .= '"' . $row->ref_no . '",';
				$csv .= '"' . number_format($amount,2) . '",';
				$csv .= '"' . $row->discount . '",';
				$csv .= '"' . $row->taxamount . '",';
				$csv .= '"' . number_format($row->amount,2) . '"';
				$csv .= "\n";
			}

			$csv .= '"","","","",';
			$csv .= '"Total",';
			$csv .= '"' . number_format($tot_amount,2) . '",';
			$csv .= '"' . number_format($totaldiscount,2) . '",';
			$csv .= '"' . number_format($totalvat,2) . '",';
			$csv .= '"' . number_format($totalnetpurchases,2) . '"';
			$csv .= "\n";
		}
		return $csv;
	}

	// private function amount($amount){
	// 	return number_format($amount,2);
	// }

	private function date($date)
	{
		return date("M d, Y",strtotime($date));
	}

}