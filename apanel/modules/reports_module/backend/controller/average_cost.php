<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui					= new ui();
		$this->input				= new input();
		$this->average_cost_model	= new average_cost_model;
		$this->report_model			= new report_model;
		$this->view->header_active	= 'report/';
	}

	public function view() {
		$this->view->title	= 'Average Cost';
		$data = array();
		$data['ui']			= $this->ui;
		$this->view->load('average_cost', $data);
	}
	
	public function view_export() {
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="' . MODULE_NAME . '.csv"');
		$csv		= '';
		$search		= $this->input->get('search');
		$header = array(
			'Item Code',
			'Item Name',
			'Description',
			'Stock',
			'UOM',
			'Average Cost',
			'Total'
		);

		$csv = 'Average Cost';
		$csv .= "\n\n";
		$csv .= '"' . implode('","', $header) . '"';
		$grand_total = 0;
		$result = $this->average_cost_model->getAverageCost($search);
		foreach ($result as $key => $row) {
			$csv .= "\n";
			$csv .= '"' . $row->itemcode . '",';
			$csv .= '"' . $row->itemname . '",';
			$csv .= '"' . $row->itemdesc . '",';
			$csv .= '"' . number_format($row->stock_quantity) . '",';
			$csv .= '"' . $row->uom_base . '",';
			$csv .= '"' . number_format($row->price_average, 2) . '",';
			$total = round($row->stock_quantity) * round($row->price_average, 2);
			$grand_total += $total;
			$csv .= '"' . number_format($total, 2) . '"';
		}
		$csv .= "\n";
		$csv .= '"Grand Total",';
		$csv .= '"",';
		$csv .= '"",';
		$csv .= '"",';
		$csv .= '"",';
		$csv .= '"",';
		$csv .= '"' . number_format($grand_total, 2) . '"';
		echo $csv;
	}

	public function view_breakdown_export() {
		$itemcode		= $this->input->get('itemcode');
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="' . MODULE_NAME . '-Breakdown(' . $itemcode . ').csv"');
		$csv		= '';
		$header = array(
			'Movement Date',
			'Previous Qty',
			'Movement Qty',
			'Stock Qty',
			'Purchase Price',
			'Price Average'
		);
		

		$csv = '"' . implode('","', $header) . '"';
		$grand_total = 0;
		$result = $this->average_cost_model->getAverageCostBreakdown($itemcode);
		foreach ($result as $key => $row) {
			$csv .= "\n";
			$csv .= '"' . $this->date->datetimeFormat($row->movementdate) . '",';
			$csv .= '"' . $row->documentno . '",';
			$csv .= '"' . (($row->movement_quantity >= 0) ? number_format($row->movement_quantity) : '(' . number_format($row->movement_quantity * -1) . ')') . '",';
			$csv .= '"' . number_format($row->stock_quantity) . '",';
			$csv .= '"' . (($row->movement_quantity > 0) ? number_format($row->purchase_price, 2) : '') . '",';
			$csv .= '"' . number_format($row->price_average, 2) . '"';
		}
		echo $csv;
	}

	public function ajax($task) {
		$json = $this->{$task}();
		header('Content-type: application/json');
		echo json_encode($json);
	}

	private function ajax_view() {
		$table		= '';
		$search		= $this->input->post('search');
		$pagination = $this->average_cost_model->getAverageCostPagination($search);

		if ($pagination->result) {
			foreach ($pagination->result as $key => $row) {
				$table .= '<tr data-itemcode="' . $row->itemcode . '" data-itemname="' . $row->itemname . '" data-itemdesc="' . $row->itemdesc . '">';
				$table .= '<td><a class="clickable show_breakdown">' . $row->itemcode . '</a></td>';
				$table .= '<td>' . $row->itemname . '</td>';
				$table .= '<td>' . $row->itemdesc . '</td>';
				$table .= '<td class="text-right">' . number_format($row->stock_quantity) . '</td>';
				$table .= '<td>' . $row->uom_base . '</td>';
				$table .= '<td class="text-right">' . number_format($row->price_average, 2) . '</td>';
				$table .= '<td class="text-right">' . number_format(round($row->stock_quantity) * round($row->price_average, 2), 2) . '</td>';
				$table .= '</tr>';
			}
		} else {
			$table = '<tr><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>';
		}

		$pagination->table = $table;

		return $pagination;
	}

	private function ajax_view_breakdown() {
		$table		= '';
		$itemcode	= $this->input->post('itemcode');
		$pagination = $this->average_cost_model->getAverageCostBreakdownPagination($itemcode);

		if ($pagination->result) {
			foreach ($pagination->result as $key => $row) {
				$table .= '<tr>';
				$table .= '<td>' . $this->date->datetimeFormat($row->movementdate) . '</td>';
				$table .= '<td class="text-right">' . $this->setLink($row->documentno, $row->doctype) . '</td>';
				$table .= '<td class="text-right">' . (($row->movement_quantity >= 0) ? number_format($row->movement_quantity) : '(' . number_format($row->movement_quantity * -1) . ')') . '</td>';
				$table .= '<td class="text-right">' . number_format($row->stock_quantity) . '</td>';
				$table .= '<td class="text-right">' . (($row->movement_quantity > 0) ? number_format($row->purchase_price, 2) : '') . '</td>';
				$table .= '<td class="text-right">' . number_format($row->price_average, 2) . '</td>';
				$table .= '</tr>';
			}
		} else {
			$table = '<tr><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>';
		}

		$pagination->table = $table;

		return $pagination;
	}

	private function setLink($documentno, $doctype) {
		$link	= '';
		$url	= '';
		if ($doctype == 'DEL_REC') {
			$url = 'sales/delivery_receipt/view/';
		} else if ($doctype == 'INV_RET') {
			$url = 'sales/return/view/';
		} else if ($doctype == 'PUR_REC') {
			$url = 'purchase/purchase_receipt/view/';
		} else if ($doctype == 'PUR_RET') {
			$url = 'purchase/return/view/';
		}
		// BEG_BAL
		// INV_ADJ
		$link .= ($url) ? '<a href="' . BASE_URL . $url . $documentno . '" target="_blank">' : ''; 
		$link .= $documentno;
		$link .= ($url) ? '</a>' : '';

		return $link;
	}

}