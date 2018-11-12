<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui					= new ui();
		$this->input				= new input();
		$this->sales_top_model		= new sales_top_model();
		$this->view->header_active	= 'report/';
	}

	public function view() {
		$this->view->title = 'Sales Analysis - Top Item';
		$data = array();
		$data['ui'] = $this->ui;
		$data['datefilter'] = $this->date->datefilterMonth();
		$data['warehouse_list'] = $this->sales_top_model->getWarehouseList();
		$this->view->load('sales_top', $data);
	}

	public function view_export() {
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="' . MODULE_NAME . '.csv"');
		$csv		= '';
		$sort		= $this->input->get('sort');
		$warehouse	= $this->input->get('warehouse');
		$datefilter	= $this->input->get('daterangefilter');
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = $this->date->dateDbFormat(str_replace(array('+', '%2C'), array(' ', ','), $date));
		}
		$csv = 'Sales Analysis - Top Item';
		$csv .= "\n\n";
		$csv .= '"Date:","'.$this->date->dateFormat($dates[0]).' - '.$this->date->dateFormat($dates[1]).'"';
		$csv .= "\n\n";
		$csv .= '"' . implode('","', array('Rank', 'Item', 'Category', 'UOM', 'Qty Sold', 'Qty Return', 'Net Qty', 'Total Amount')) . '"';
		$result = $this->sales_top_model->getSales($warehouse, $sort, $dates[0], $dates[1]);
		foreach ($result as $key => $row) {
			$csv .= "\n";
			$csv .= '"' . ($key + 1) . '",';
			$csv .= '"' . $row->itemname . '",';
			$csv .= '"' . $row->category . '",';
			$csv .= '"' . strtoupper($row->uom) . '",';
			$csv .= '"' . number_format($row->sales) . '",';
			$csv .= '"' . number_format($row->returns) . '",';
			$csv .= '"' . number_format($row->sales - $row->returns) . '",';
			$csv .= '"' . number_format($row->total_amount, 2) . '"';
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
		$sort		= $this->input->post('sort');
		$warehouse	= $this->input->post('warehouse');
		$datefilter	= $this->input->post('daterangefilter');
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = $this->date->dateDbFormat($date);
		}
		$pagination = $this->sales_top_model->getSalesPagination($warehouse, $sort, $dates[0], $dates[1]);

		if ($pagination->result) {
			foreach ($pagination->result as $key => $row) {
				$net_qty = $row->sales - $row->returns;
				$table .= '<tr>';
				$table .= '<td>' . ((($pagination->page - 1) * 10) + $key + 1) . '</td>';
				$table .= '<td>' . $row->itemname . '</td>';
				$table .= '<td>' . $row->category . '</td>';
				$table .= '<td class="text-right">' . strtoupper($row->uom) . '</td>';
				// $table .= '<td>' . $row->warehouse . '</td>';
				$table .= '<td class="text-right">' . number_format($row->sales) . '</td>';
				$table .= '<td class="text-right">' . number_format($row->returns) . '</td>';
				$table .= '<td class="text-right">' . number_format($net_qty) . '</td>';
				if ($net_qty < 0) {
					$getAmount = $this->sales_top_model->getReturnedAmount($row->itemcode, $dates[0], $dates[1]);
					$table .= '<td class="text-right">- ' . number_format($getAmount->amount) . '</td>';
				}
				else {
					$table .= '<td class="text-right">' . number_format($row->total_amount, 2) . '</td>';
				}
				$table .= '</tr>';
			}
		} else {
			$table = '<tr><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>';
		}

		$pagination->table = $table;

		return $pagination;
	}

}