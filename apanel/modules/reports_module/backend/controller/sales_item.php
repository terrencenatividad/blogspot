<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui					= new ui();
		$this->input				= new input();
		$this->sales_item_model		= new sales_item_model();
		$this->item_class_model		= $this->checkoutModel('inventory_module/item_class_model');
		$this->view->header_active	= 'report/';
	}

	public function view() {
		$this->view->title		= 'Sales per Item';
		$data					= array();
		$data['ui']				= $this->ui;
		$data['datefilter']		= $this->date->datefilterMonth();
		$data['category_list']	= $this->item_class_model->getParentClass('');
		$data['item_list']		= $this->sales_item_model->getItemList();
		$this->view->load('sales_item', $data);
	}

	public function view_export() {
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="' . MODULE_NAME . '.csv"');
		$csv		= '';
		$sort		= $this->input->get('sort');
		$category	= $this->input->get('category');
		$itemcode	= $this->input->get('itemcode');
		$datefilter	= $this->input->get('daterangefilter');
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = $this->date->dateDbFormat(str_replace(array('+', '%2C'), array(' ', ','), $date));
		}
		$header = array(
			'Item Name',
			'Category',
			'UOM',
			'Qty Sold',
			'Qty Returned',
			'Net Qty',
			'Amount',
		);
		$csv = '"' . implode('","', $header) . '"';
		$totalsold		= 0;
		$totalreturned	= 0;
		$totalamount	= 0;
		$result = $this->sales_item_model->getSales($category, $itemcode, $sort, $dates[0], $dates[1]);
		foreach ($result as $key => $row) {
			$totalsold		+= $row->sales;
			$totalreturned	+= $row->returns;
			$totalamount	+= $row->total_amount;
			$csv .= "\n";
			$csv .= '"' . $row->itemname . '",';
			$csv .= '"' . $row->category . '",';
			$csv .= '"' . strtoupper($row->uom) . '",';
			$csv .= '"' . number_format($row->sales) . '",';
			$csv .= '"' . number_format($row->returns) . '",';
			$csv .= '"' . number_format($row->sales - $row->returns) . '",';
			$csv .= '"' . number_format($row->total_amount, 2) . '"';
		}
		$csv .= "\n";
		$footer = array(
			'',
			'',
			'',
			number_format($totalsold),
			number_format($totalreturned),
			number_format($totalsold - $totalreturned),
			number_format($totalamount, 2),
		);
		$csv .= '"' . implode('","', $footer) . '"';
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
		$category	= $this->input->post('category');
		$itemcode	= $this->input->post('itemcode');
		$datefilter	= $this->input->post('daterangefilter');
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = $this->date->dateDbFormat($date);
		}
		$pagination = $this->sales_item_model->getSalesPagination($category, $itemcode, $sort, $dates[0], $dates[1]);

		if ($pagination->result) {
			foreach ($pagination->result as $key => $row) {
				$table .= '<tr>';
				$table .= '<td>' . $row->itemname . '</td>';
				$table .= '<td>' . $row->category . '</td>';
				$table .= '<td class="text-right">' . strtoupper($row->uom) . '</td>';
				$table .= '<td class="text-right">' . number_format($row->sales) . '</td>';
				$table .= '<td class="text-right">' . number_format($row->returns) . '</td>';
				$table .= '<td class="text-right">' . number_format($row->sales - $row->returns) . '</td>';
				$table .= '<td class="text-right">' . number_format($row->total_amount, 2) . '</td>';
				$table .= '</tr>';
			}
		} else {
			$table = '<tr><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>';
		}

		$details = $this->sales_item_model->getSalesTotal($category, $itemcode, $dates[0], $dates[1]);

		$tabledetails = '';

		if ($pagination->page_limit > 1) {
			$tabledetails .= '<tr class="success">
								<td colspan="6" class="text-center">Page ' . $pagination->page . ' of ' . $pagination->page_limit . '</td>
							</tr>';
		}

		$tabledetails .= '<tr>
							<th colspan="3">Grand Total: </th>
							<th class="text-right">' . number_format($details->sales) . '</th>
							<th class="text-right">' . number_format($details->returns) . '</th>
							<th class="text-right">' . number_format($details->sales - $details->returns) . '</th>
							<th class="text-right">' . number_format($details->total_amount, 2) . '</th>
						</tr>';

		$pagination->table			= $table;
		$pagination->tabledetails	= $tabledetails;	

		return $pagination;
	}

}