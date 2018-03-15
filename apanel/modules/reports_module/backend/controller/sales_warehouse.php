<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->sales_warehouse	= new sales_warehouse();
		$this->item_model		= new item_model();
		$this->session			= new session();
		$this->show_input 	    = true;
		$this->data = array();
		$this->view->header_active  = 'report/';
	}

	public function listing() {
		$this->view->title = 'Sales Report per Warehouse';
		$data['ui'] = $this->ui;
		$data['customer_list'] = $this->sales_warehouse->retrieveCustomerList();
		$data['warehouse_list'] = $this->sales_warehouse->getWarehouseList();
		$data['datefilter'] 	= $this->date->datefilterMonth();
		$data['show_input']  = $this->show_input;
		$this->view->load('sales_warehouse/sales_warehouse_list', $data);
	}

	public function view($warehouse,$datefilter){
		$datefilter = base64_decode($datefilter);
		$this->view->title = 'Detailed Report per Warehouse';
		$data = (array) $this->sales_warehouse->warehouseDetails($warehouse,$datefilter);
		$data['warehouse'] = $warehouse;
		$data['daterange'] = $datefilter;
		$data['ui'] = $this->ui;
		$this->view->load('sales_warehouse/sales_warehouse', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function getWarehouse(){
		$data = $this->input->post(array('warehouse','date'));
		$warehouse = $data['warehouse'];
		$daterange = $data['date'];
		$pagination = $this->sales_warehouse->warehouseBreakdown($warehouse,$daterange);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		$amount = 0.00;
		foreach ($pagination->result as $key => $row) {
			$amount += $row->amount;
			$table .= '<tr>';
			$table .= '<td>' . $row->itemcode . '</td>';
			$table .= '<td>' . $row->itemname . '</td>';
			$table .= '<td>' . number_format($row->quantity,0) . '</td>';
			$table .= '<td>' . $row->issueuom . '</td>';
			$table .= '<td>' .$this->amount($row->unitprice) . '</td>';
			$table .= '<td>' .$this->amount( $row->amount) . '</td>';
			$table .= '</tr>';
		}
		$total = $amount;
		$table .= '<tr>';
			$table .= '<td></td>';
			$table .= '<td></td>';
			$table .= '<td colspan = "2"></td>';
			$table .= '<td><strong>Total Amount</strong></td>';
			$table .= '<td><strong>' .$this->amount($total). '</strong></td>';
			$table .= '</tr>';

		$pagination->table = $table;
		$pagination->csv   = $this->export2();
		return $pagination;
		
	}

	private function ajax_list() {
		$data = $this->input->post(array('daterangefilter','warehouse','sort'));
		$datefilter	= $data['daterangefilter'];
		$warehouse = $data['warehouse'];
		$sort      = $data['sort'];
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$pagination = $this->sales_warehouse->warehouse_list($dates[0], $dates[1], $warehouse, $sort);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		$quantity = 0.00;
		$total = 0.00;
		$t_amount = 0.00;
		foreach ($pagination->result as $key => $row) {
			$quantity += $row->quantity;
			$total += $row->amount;
			$t_amount += $row->total;
			$table .= '<tr class="clickable" data-id="' .$row->warehousecode . '/' . base64_encode($data['daterangefilter']) . '">';
			$table .= '<td><a>' . $row->warehousecode . '</a></td>';
			$table .= '<td>' . $row->warehouse . '</td>';
			$table .= '<td>' . number_format($row->quantity,0) . '</td>';
			$table .= '</tr>';
		}
			$table .= '<tr>
			<td></td>
			<td style="font-weight:bold" >Total</td>
			<td style="font-weight:bold"> ' .number_format($quantity,0).  '</td></tr>';
		$pagination->table  = $table;
		$pagination->csv 	= $this->export();
		return $pagination;
	}

	private function export(){
		$data = $this->input->post(array('warehouse','daterangefilter'));
		$data['daterangefilter'] = str_replace(array('%2C', '+'), array(',', ' '), $data['daterangefilter']);
		$result = $this->sales_warehouse->fileExport($data);
		$header = array("Warehouse Code","Warehouse","Quantity");
		$warehouse_list = $this->sales_warehouse->warehouses($data['warehouse']);
		// var_dump($warehouse_list);

		$csv = '';
		$csv .= '"' . 'Sales Report per Warehouse ' . '",';
		$csv .= "\n";
		$csv .= "\n";
		$csv .= '"' . 'Date:' . '",';
		$csv .= '"' . $data["daterangefilter"] . '",';
		$csv .= "\n";
		$csv .= '"' . 'Warehouse:' . '",';
		if ($data['warehouse'] == ''){
			$csv .= '"' . 'All'. '"';
			$csv .= "\n";
			$csv .= '"",';
		}  else {
			foreach ($warehouse_list as $key => $value) {
				$csv .= '"' . $value->val. '"';
				$csv .= "\n";
				$csv .= '"",';
			}
		}
		
		$csv .= "\n\n";
		$csv .= '"' . implode('","', $header) . '"';
		$csv .= "\n";
		
		$quantity = 0.00;
		$amount = 0.00;
		$t_amount = 0.00;
		if (!empty($result)){
			foreach ($result as $key => $row){
				$quantity += $row->quantity;
				$amount += $row->amount;
				$t_amount += $row->total;
				$csv .= '"' . $row->warehousecode . '",';
				$csv .= '"' . $row->warehouse . '",';
				$csv .= '"' . number_format($row->quantity,0) . '",';
				$csv .= "\n";
			}
		}
		$csv .= '"","Total","' . number_format($quantity,0) . '","';
		return $csv;
	}

	private function export2(){
		$data = $this->input->post(array('warehouse','date'));
		$data['date'] = str_replace(array('%2C', '+'), array(',', ' '), $data['date']);
		$warehouse = $data['warehouse'];
		$result = $this->sales_warehouse->fileExport2($data);
		$res = $this->sales_warehouse->warehouseDetails($warehouse);
		$header3 = array("Itemcode","Itemname","Quantity","UOM","Unitprice","Amount");

		$csv = '';
		$csv .= '"' . 'Detailed report Per Warehouse' . '",';
		$csv .= "\n";
		$csv .= "\n";
		$csv .= '"' . 'Warehouse Code:' . '",';
		$csv .= '"' . $res->warehousecode . '",';
		$csv .= '"' . 'Description:' . '",';
		$csv .= '"' . $res->description . '",';
		$csv .= "\n";
		
		$csv .= "\n";
		$csv .= "\n";
		$csv .= "\n";
		$csv .= '"' . implode('","', $header3) . '"';
		$csv .= "\n";
		$totalAmount = 0.00;
		if (!empty($result)){
			foreach ($result as $key => $row){
				$totalAmount += $row->amount;
				$csv .= '"' . $row->itemcode . '",';
				$csv .= '"' . $row->itemname . '",';
				$csv .= '"' .number_format( $row->quantity,0) . '",';
				$csv .= '"' . $row->issueuom . '",';
				$csv .= '"' .  $this->amount($row->unitprice) . '",';
				$csv .= '"' . $this->amount($row->amount) . '"';
				$csv .= "\n";
				$csv .= "\n";
			}
		}
		$csv .= '"","","","","Total Amount:","' . $this->amount($totalAmount) . '"';
		return $csv;
	}

	private function amount($amount){
		return number_format($amount,2);
	}

	private function removeamount($amount){
		return number_format($amount,0);
	}

}