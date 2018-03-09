<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->purchase_journal	= new purchase_journal();
		$this->item_model		= new item_model();
		$this->session			= new session();
		$this->show_input 	    = true;
		$this->data = array();
		$this->view->header_active  = 'report/';
	}

	public function listing() {
		$this->view->title = 'Purchase Journal';
		$data['ui'] = $this->ui;
		$data['vendor_list'] = $this->purchase_journal->getVendorList();
		$data['datefilter'] 	= $this->date->datefilterMonth();
		$data['show_input']  = $this->show_input;
		$this->view->load('purchase_journal', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data = $this->input->post(array('daterangefilter','vendor','sort'));
		$datefilter	= $data['daterangefilter'];
		$vendor 	= $data['vendor'];
		$sort      = $data['sort'];
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$pagination = $this->purchase_journal->purchase_journalList($dates[0], $dates[1], $vendor, $sort);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		
		$totalamount  	=	$totaldiscount 	= 	$totalvat 	=	$totalnetpurchases 	= 	$total_amt = 0;
		
		foreach ($pagination->result as $key => $row) {
			$amount = $row->taxamount + $row->amount;
			$totalamount 		+= $row->amount;
			$total_amt 			+=$amount;
			$totaldiscount 		+= $row->discount;
			$totalvat 			+= $row->taxamount;
			$totalnetpurchases 	+= $row->amount;

			$table .= '<tr>';
			$table .= '<td>' . $this->date($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->tinno . '</td>';
			$table .= '<td>' . $row->vendor . '</td>';
			$table .= '<td>' . $row->remarks . '</td>';
			$table .= '<td>' . $row->ref_no . '</td>';
			$table .= '<td>' . number_format($amount,2) . '</td>';
			$table .= '<td>' . number_format($row->discount,2) . '</td>';
			$table .= '<td>' . number_format($row->taxamount,2) . '</td>';
			$table .= '<td>' . number_format($row->amount,2) . '</td>';
			$table .= '</tr>';
		}
		
		$table .= '<tr>';
		$table .= '<td colspan = "4"></td>';
		$table .= '<td><strong>Total<strong></td>';
		$table .= '<td>' . number_format($total_amt,2) . '</td>';
		$table .= '<td>' . number_format($totaldiscount,2) . '</td>';
		$table .= '<td>' . number_format($totalvat,2) . '</td>';
		$table .= '<td>' . number_format($totalnetpurchases,2) . '</td>';
		$table .= '</tr>';

		$pagination->table  = $table;
		$pagination->csv 	= $this->export();
		return $pagination;
	}

	private function export(){
		$data = $this->input->post(array('daterangefilter','vendor','sort'));
		$datefilter	= $data['daterangefilter'];
		$vendor 	= $data['vendor'];
		$sort      = $data['sort'];
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$result = $this->purchase_journal->fileExport($dates[0], $dates[1], $vendor, $sort);
		$header = array("Date","Supplier Tin","Vendor Name","Description","Reference No.","Amount","Discount","VAT amount","Net Purchases");
		$cus = $this->purchase_journal->vendorInfo($vendor);
		// var_dump($cus);

		$csv = '';
		$csv .= '"' . 'Purchase Journal' . '",';
		$csv .= "\n";
		$csv .= "\n";
		$csv .= '"' . 'Date Range:' . '",';
		$csv .= '"' . $data["daterangefilter"] . '",';
		$csv .= "\n";
		$csv .= '"' . 'Vendor:' . '",';
		foreach ($cus as $key => $value) {
			$csv .= '"' . $value->val. '"';
			$csv .= "\n";
			$csv .= '"",';

			// implode(',', $vendor) 
		}
		// foreach($cus => $row){
		// 	$csv .= '"' . $row->$val. '",';
		// }
		$csv .= "\n\n";
		$csv .= '"' . implode('","', $header) . '"';
		$csv .= "\n";
		
		$totalamount  	=	$totaldiscount 	= 	$totalvat 	=	$totalnetpurchases 	= 	$total_amt = 0;

		if (!empty($result)){
			foreach ($result as $key => $row){
				$amount 		 	=  $row->taxamount + $row->amount;
				$totalamount 		+= $row->amount;
				$total_amt 			+= $amount;
				$totaldiscount 		+= $row->discount;
				$totalvat 			+= $row->taxamount;
				$totalnetpurchases 	+= $row->amount;

				$csv .= '"' . $this->date($row->transactiondate) . '",';
				$csv .= '"' . $row->tinno . '",';
				$csv .= '"' . $row->vendor . '",';
				$csv .= '"' . $row->remarks . '",';
				$csv .= '"' . $row->ref_no . '",';
				$csv .= '"' . number_format($amount,2) . '",';
				$csv .= '"' . number_format($row->discount,2) . '",';
				$csv .= '"' . number_format($row->taxamount,2) . '",';
				$csv .= '"' . number_format($row->amount,2) . '"';
				$csv .= "\n";
			}

			$csv .= '"","","","",';
			$csv .= '"Total",';
			$csv .= '"' . number_format($total_amt,2) . '",';
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