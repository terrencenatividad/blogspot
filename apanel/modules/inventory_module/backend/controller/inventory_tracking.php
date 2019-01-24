<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->inventory_model	= new inventory_tracking_model();
		$this->brand_model		= $this->checkoutModel('maintenance_module/brand');
		$this->item_model		= new item_model();
		$this->session			= new session();
		$this->data = array();
		$this->view->header_active = 'inventory/tracking/';
	}

	public function listing() {
		$this->view->title 		= 'Inventory Tracking List';
		$data['ui']				= $this->ui;
		$data['item_list']		= $this->item_model->getItemDropdownList();
		$data['warehouse_list']	= $this->inventory_model->getWarehouseDropdownList();
		$data['brand_list'] = $this->brand_model->getBrandDropdownList();
		$this->view->load('inventory_tracking/inventory_tracking_list', $data);
	}

	public function list_export($datefilter = '', $itemcode = '', $warehouse = '', $sort = '') {
		$datefilter	= base64_decode($datefilter);
		$itemcode	= base64_decode($itemcode);
		$warehouse	= base64_decode($warehouse);
		$sort		= base64_decode($sort);
		$itemcode_label = ($itemcode) ? 'Item Code: ' . $itemcode : '';
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="Inventory Tracking - ' . $itemcode_label . ' - Date: ' . $datefilter . '.csv"');
		$result = $this->inventory_model->getInventoryTracking($itemcode, $datefilter, $warehouse, $sort);
		$header = array(
			'Date',
			'Item Name',
			'Warehouse',
			'Reference No.',
			'Particulars',
			'In',
			'Out',
			'Current Stock',
			'Activity',
			'User'
		);
		$csv = '"' . implode('","', $header) . '"';
		$totalin	= 0;
		$totalout	= 0;
		foreach ($result as $key => $row) {
			$in			= (($row->quantity > 0) ? $row->quantity : '-');
			$out		= (($row->quantity < 0) ? $row->quantity * -1 : '-');
			if ($in != '-') {
				$totalin	+= $in;
				$in			= number_format($in);
			}
			if ($out != '-') {
				$totalout	+= $out;
				$out		= number_format($out);
			}
			$csv .= "\n";
			$csv .= '"' . $this->date->datetimeFormat($row->entereddate) . '",';
			$csv .= '"' . $row->itemname . '",';
			$csv .= '"' . $row->warehouse . '",';
			$csv .= '"' . $row->reference . '",';
			$csv .= '"' . $row->partnername . '",';
			$csv .= '"' . $in . '",';
			$csv .= '"' . $out . '",';
			$csv .= '"' . number_format($row->currentqty) . '",';
			$csv .= '"' . $row->activity . '",';
			$csv .= '"' . $row->name . '"';
		}
		$footer = array(
			'',
			'',
			'',
			'',
			'Total',
			number_format($totalin),
			number_format($totalout),
			''
		);
		$csv .= "\n";
		$csv .= '"' . implode('","', $footer) . '"';
		echo $csv;
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data		= $this->input->post(array('itemcode', 'brandcode', 'daterangefilter', 'warehouse', 'sort'));
		$sort		= $data['sort'];
		$itemcode	= $data['itemcode'];
		$warehouse	= $data['warehouse'];
		$brandcode 	= $data['brandcode'];
		$datefilter	= $data['daterangefilter'];

		$pagination = $this->inventory_model->getInventoryTrackingPagination($itemcode, $datefilter, $warehouse, $sort, $brandcode);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$table .= '<td>' . $this->date->datetimeFormat($row->entereddate) . '</td>';
			$table .= '<td>' . $row->itemcode.' - '.$row->itemname . '</td>';
			$table .= '<td>' . $row->brandname . '</td>';
			$table .= '<td>' . $row->warehouse . '</td>';
			if (stripos($row->reference, 'DR') !== FALSE) {
				$table .= '<td><a href="' . BASE_URL . 'sales/delivery_receipt/view/'.$row->reference.'" target = "_blank">' . $row->reference . '</a></td>';
			}
			else if (stripos($row->reference, 'PR') !== FALSE) {
				$table .= '<td><a href="' . BASE_URL . 'purchase/purchase_receipt/view/'.$row->reference.'" target = "_blank">' . $row->reference . '</a></td>';
			}
			else if (stripos($row->reference, 'STA') !== FALSE || stripos( $row->reference, 'ST') !== FALSE) {
				$table .= '<td><a href="' . BASE_URL . 'inventory/stock_transfer/view/'.$row->reference.'" target = "_blank">' . $row->reference . '</a></td>';
			}
			else if (stripos($row->reference, 'PRTN') !== FALSE) {
				$table .= '<td><a href="' . BASE_URL . 'purchase/return/view/'.$row->reference.'" target = "_blank">' . $row->reference . '</a></td>';
			}
			else if (stripos($row->reference, 'SR') !== FALSE || stripos($row->reference, 'R') !== FALSE) {
				$table .= '<td><a href="' . BASE_URL . 'sales/return/view/'.$row->reference.'" target = "_blank">' . $row->reference . '</a></td>';
			}
			else {
				$table .= '<td>' . $row->reference . '</td>';
			}
			$table .= '<td>' . $row->partnername . '</td>';
			$table .= '<td>' . (($row->quantity > 0) ? number_format($row->quantity) : '-') . '</td>';
			$table .= '<td>' . (($row->quantity < 0) ? number_format($row->quantity * -1) : '-') . '</td>';
			$table .= '<td>' . number_format($row->currentqty) . '</td>';
			$table .= '<td>' . $row->activity . '</td>';
			$table .= '<td>' . $row->name . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

}