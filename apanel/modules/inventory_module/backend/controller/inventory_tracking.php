<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->inventory_model	= new inventory_tracking_model();
		$this->item_model		= new item_model();
		$this->session			= new session();
		$this->data = array();
		$this->view->header_active = 'inventory/tracking/';
	}

	public function listing() {
		$this->view->title = 'Inventory Tracking List';
		$data['ui'] = $this->ui;
		$data['item_list'] = $this->item_model->getItemDropdownList();
		$this->view->load('inventory_tracking/inventory_tracking_list', $data);
	}

	public function list_export($datefilter = '', $itemcode = '') {
		$datefilter		= base64_decode($datefilter);
		$itemcode		= base64_decode($itemcode);
		$itemcode_label = ($itemcode) ? 'Item Code: ' . $itemcode : '';
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="Inventory Tracking - ' . $itemcode_label . ' - Date: ' . $datefilter . '.csv"');
		$result = $this->inventory_model->getInventoryTracking($itemcode, $datefilter);
		$header = array(
			'Date',
			'Item Name',
			'Warehouse',
			'Reference No.',
			'Particulars',
			'In',
			'Out',
			'Current Stock'
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
			$csv .= '"' . number_format($row->currentqty) . '"';
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
		$data		= $this->input->post(array('itemcode', 'daterangefilter'));
		$itemcode	= $data['itemcode'];
		$datefilter	= $data['daterangefilter'];

		$pagination = $this->inventory_model->getInventoryTrackingPagination($itemcode, $datefilter);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$table .= '<td>' . $this->date->datetimeFormat($row->entereddate) . '</td>';
			$table .= '<td>' . $row->itemname . '</td>';
			$table .= '<td>' . $row->warehouse . '</td>';
			$table .= '<td>' . $row->reference . '</td>';
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