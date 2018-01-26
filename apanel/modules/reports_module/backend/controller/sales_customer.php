<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->sales_customer	= new sales_customer();
		$this->item_model		= new item_model();
		$this->session			= new session();
		$this->show_input 	    = true;
		$this->data = array();
		$this->view->header_active  = 'report/';
	}

	public function listing() {
		$this->view->title = 'Sales Report per Customer';
		$data['ui'] = $this->ui;
		$data['customer_list'] = $this->sales_customer->retrieveCustomerList();
		$data['warehouse_list'] = $this->sales_customer->getWarehouseList();
		$data['datefilter'] 	= $this->date->datefilterMonth();
		$data['show_input']  = $this->show_input;
		$this->view->load('sales_customer/sales_customer_list', $data);
	}

	public function view($cust_code, $datefilter){
		$cust_code =  base64_decode($cust_code);
		$datefilter = base64_decode($datefilter);
		$this->view->title = 'Detailed Report per Customer';
		$data = (array) $this->sales_customer->customerDetails($cust_code);
		$data['ui'] = $this->ui;
		$data['cust_code'] = $cust_code;
		$data['datefilter'] = $datefilter;
		$this->view->load('sales_customer/sales_customer', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function get_invoice(){
		$data = $this->input->post(array('customer','datefilter'));
		$cust_code   	= $data['customer'];
		$datefilter 	= $data['datefilter'];
		$pagination = $this->sales_customer->customerInvoices($cust_code,$datefilter);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		$total = 0.00;
		foreach ($pagination->result as $key => $row) {
			$total += $row->amount;
			$table .= '<tr>';
			$table .= '<td></td>';
			$table .= '<td>' . $this->date->dateFormat($row->date) . '</td>';
			$table .= '<td><a href="' . BASE_URL . 'sales/sales_invoice/view/'.$row->voucherno.'">'.$row->voucherno.'</a></td>';
			$table .= '<td class="text-left">' . ($row->ref) . '</td>';
			$table .= '<td class="text-left">' . $this->amount($row->amount) . '</td>';
			$table .= '</tr>';
		}
		$table .= '<tr>	
			<td></td>
			<td></td>
			<td></td>
			<td style="font-weight:bold">Total Amount</td>
			<td style="font-weight:bold">' .$this->amount($total). '</td></tr>';
		

		$pagination->table = $table;
		$pagination->csv   = $this->export2();
		return $pagination;
		
	}

	private function ajax_list() {
		$data = $this->input->post(array('daterangefilter','customer','warehouse','sort'));
		$datefilter	= $data['daterangefilter'];
		$customer	= $data['customer'];
		$warehouse  = $data['warehouse'];
		$sort 		= $data['sort'];
		$search = $this->input->post("search");
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}

		$pagination = $this->sales_customer->customer_list($dates[0], $dates[1], $customer, $search);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		$totalAmount = 0.00;
		foreach ($pagination->result as $key => $row) {
			$totalAmount += $row->amount;
			$table .= '<tr class="clickable" data-id="' . base64_encode($row->partnercode) . '/' . base64_encode($data['daterangefilter']) . '">';
			$table .= '<td></td>';
			$table .= '<td><a>' . $row->name . '</a></td>';
			$table .= '<td class="text-left">' . $this->amount($row->amount) . '</td>';
			$table .= '</tr>';
		}
			$table .= '<tr>	
			<td></td>
			<td style="font-weight:bold">Total Amount</td>
			<td style="font-weight:bold">' .$this->amount($totalAmount). '</td></tr>';
		$pagination->table  = $table;
		$pagination->csv 	= $this->export();
		
		return $pagination;
	}

	private function export(){
		$search = $this->input->post("search");
		$data = $this->input->post(array('customer','warehouse','daterangefilter','search'));
		$data['daterangefilter'] = str_replace(array('%2C', '+'), array(',', ' '), $data['daterangefilter']);
		$result = $this->sales_customer->fileExport($data);
		$header = array("Customer", "Amount");

		$csv = '';
		$csv = '"' . implode('","', $header) . '"';
		$csv .= "\n";
		
		$totalAmount = 0.00;
		if (!empty($result)){
			foreach ($result as $key => $row){
				$totalAmount += $row->amount;
				$csv .= '"' . $row->name . '",';
				$csv .= '"' . $this->amount($row->amount) . '"';
				$csv .= "\n";
			}
		}
		$csv .= '"Total Amount:","' . $this->amount($totalAmount) . '"';
		return $csv;
	}

	private function export2(){
		$data = $this->input->post(array('partnercode','warehouse','datefilter'));
		$data['datefilter'] = str_replace(array('%2C', '+'), array(',', ' '), $data['datefilter']);
		$partnercode = $data['partnercode'];
		$res = $this->sales_customer->customerDetails($partnercode); 
		$result = $this->sales_customer->fileExport2($data);

		$header3 = array("Transaction Date","SI No","Reference No","Amount");

		$csv = '';
		$csv .= '"' . 'Detailed report Per Customer' . '",';
		$csv .= "\n";
		$csv .= "\n";
		$csv .= '"' . 'Customer:' . '",';
		$csv .= '"' . $res->name . '",';
		$csv .= '"' . 'Mobile:' . '",';
		$csv .= '"' . $res->mobile . '",';
		$csv .= "\n";
		$csv .= '"' . 'Email:' . '",';
		$csv .= '"' . $res->email . '",';
		$csv .= '"' . 'Address:' . '",';
		$csv .= '"' . $res->address1 . '",';
		
		$csv .= "\n";
		$csv .= "\n";
		$csv .= "\n";
		$csv .= '"' . implode('","', $header3) . '"';
		$csv .= "\n";
		$totalAmount = 0.00;
		if (!empty($result)){
			foreach ($result as $key => $row){
				$totalAmount += $row->amount;
				$csv .= '"' . $row->date . '",';
				$csv .= '"' . $row->voucherno . '",';
				$csv .= '"' . $row->ref . '",';
				$csv .= '"' . $this->amount($row->amount) . '",';
				$csv .= "\n";
			}
		}
		$csv .= '"","","Total Amount:","' . $this->amount($totalAmount) . '"';
		return $csv;
	}

	private function amount($amount)
	{
		return number_format($amount,2);
	}

}