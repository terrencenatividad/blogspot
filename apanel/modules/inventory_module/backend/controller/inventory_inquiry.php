<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->inventory_model	= new inventory_inquiry_model();
		$this->item_model		= new item_model();
		$this->session			= new session();
		$this->data = array();
		$this->view->header_active = 'inventory/inquiry/';
	}

	public function listing() {
		$this->view->title = 'Inventory Inquiry List';
		$data['ui'] = $this->ui;
		$data['item_list'] = $this->item_model->getItemDropdownList();
		$data['warehouse_list'] = $this->inventory_model->getWarehouseList();
		$this->view->load('inventory_inquiry/inventory_inquiry_list', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data = $this->input->post(array('itemcode', 'daterangefilter','limit', 'sort', 'warehouse'));
		$limit 		= $data['limit'];
		$itemcode 	= $data['itemcode'];
		$datefilter	= $data['daterangefilter'];
		$sort		= $data['sort'];
		$warehouse	= $data['warehouse'];
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}

		$pagination = $this->inventory_model->getInventoryinquiryList($itemcode, $limit, $sort, $warehouse);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
				$table .= '<td>' . $row->itemname . '</td>' ;
				$table .= '<td>' . $row->des . '</td>';
				$table .= '<td><a class="clickable" data-id="onhand/'. $row->itemcode . '/' .$row->warehouse . '">' . $row->OHQty . '</a></td>';
				$table .= '<td><a class="clickable" data-id="order/'. $row->itemcode . '/' .$row->warehouse . '">' . $row->OrderQty . '</a></td>';
				$table .= '<td><a class="clickable" data-id="allocated/'. $row->itemcode . '/' .$row->warehouse . '">' . $row->AllocQty . '</a></td>';
				$table .= '<td>'. $row->avail .'</td>';
				$table .= '</tr>';									
		}
		$pagination->table  = $table;
		$pagination->csv 	= $this->export_main($data);
		return $pagination;
	}

	private function avail_listing(){
		$data = $this->input->post(array('table','itemcode','warehouse'));
		$pagination = $this->inventory_model->avail_quantity($data['table'], $data['itemcode'], $data['warehouse']);

		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
				$table .= '<td></td>' ;
				$table .= '<td>' . $row->prcqty . '</td>' ;
				$table .= '<td>' . $row->drqty . '</td>';
				$table .= '<td>' . $row->srqty . '</td>';
				$table .= '<td>' . $row->prtqty . '</td>';
				$table .= '<td>' . $row->transqty . '</td>';
				$table .= '<td>' . $row->adqty . '</td>';	
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function order_listing(){
		$data = $this->input->post(array('table','itemcode','warehouse'));
		$pagination = $this->inventory_model->order_quantity($data['table'], $data['itemcode'], $data['warehouse']);

		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
				$table .= '<td></td>' ;
				$table .= '<td>' . $row->poqty . '</td>' ;
				$table .= '<td>' . $row->pretQty . '</td>';
				$table .= '<td>' . $row->prcQty . '</td>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function allocated_listing(){
		$data = $this->input->post(array('table','itemcode','warehouse'));
		$pagination = $this->inventory_model->allocated_quantity($data['table'], $data['itemcode'], $data['warehouse']);

		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
				$table .= '<td></td>' ;
				$table .= '<td>' . $row->soqty . '</td>' ;
				$table .= '<td>' . $row->drqty . '</td>';
				// $table .= '<td>' . $row->srqty . '</td>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function onhand_listing(){
		$data = $this->input->post(array('table','itemcode','warehouse'));
		$pagination = $this->inventory_model->onhand_quantity($data['table'], $data['itemcode'], $data['warehouse']);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
				$table .= '<td></td>' ;
				$table .= '<td>' . $row->bqty . '</td>' ;
				$table .= '<td>' . $row->prcqty . '</td>' ;
				$table .= '<td>' . $row->drqty . '</td>';
				$table .= '<td>' . $row->srqty . '</td>';
				$table .= '<td>' . $row->prtqty . '</td>';
				$table .= '<td>' . $row->transqty . '</td>';
				$table .= '<td>' . $row->adqty . '</td>';	
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function export_main($data){
		$header = array("Item","Warehouse","On Hand Quantity","Order Quantity","Allocated Quantity","Available Quantity");

		$csv = '';
		$csv .= '"' . implode('", "', $header) . '"';
		$csv .= "\n";
		
		$result = $this->inventory_model->export_main($data);

		if (!empty($result)){
			foreach ($result as $key => $row){
				$csv .= '"' . $row->itemname . '",';
				$csv .= '"' . $row->warehousename . '",';
				$csv .= '"' . $row->OHQty . '",';
				$csv .= '"' . $row->OrderQty . '",';
				$csv .= '"' . $row->AllocQty . '",';
				$csv .= '"' . $row->avail . '",';
				$csv .= "\n";
			}
		}
		return $csv;
	}

}