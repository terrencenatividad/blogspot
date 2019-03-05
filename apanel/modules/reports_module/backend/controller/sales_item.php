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
		$data['customer_list']	= $this->sales_item_model->getCustomerList();
		$data['warehouse_list']	= $this->sales_item_model->getWarehouseList();
		$this->view->load('sales_item', $data);
	}

	public function view_export() {
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="' . MODULE_NAME . '.csv"');
		$csv		= '';
		$sort		= $this->input->get('sort');
		$category	= $this->input->get('category');
		$itemcode	= $this->input->get('itemcode');
		$customer	= $this->input->get('customer');
		$warehouse	= $this->input->get('warehouse');
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
		$result = $this->sales_item_model->getSales($category, $itemcode, $customer, $warehouse, $sort, $dates[0], $dates[1]);

		$itemcode 		= 	($itemcode == "" || $itemcode == "none") 	? "All" : $itemcode;
		$customer 		= 	($customer == "" || $customer == "none") 	? "All" : $customer;
		$warehouse 		= 	($warehouse == "" || $warehouse == "none") 	? "All" : $warehouse;
		
		$ret_data1 		=	$this->sales_item_model->getName($customer, "customer");
		$customername 	=	isset($ret_data1->val) ? $ret_data1->val : $customer;

		$csv = 'Sales Per Item';
		$csv .= "\n\n";
		$csv .= '"Date:","'.$this->date->dateFormat($dates[0]).' - '.$this->date->dateFormat($dates[1]).'"';
		$csv .= "\n";
		$csv .= '"Item:","'.$itemcode.'"';
		$csv .= "\n";
		$csv .= '"Customer:","'.$customername.'"';
		$csv .= "\n";
		$csv .= '"Warehouse:","'.$warehouse.'"';
		$csv .= "\n\n";
		$csv .= '"' . implode('","', $header) . '"';
		$csv .= "\n";
		$totalsold		= 0;
		$totalreturned	= 0;
		$totalamount	= 0;
		$grandSold 		= 0;
		$grandReturned 	= 0;
		$grandTotal 	= 0;
		$grandNetofRet 	= 0;
		$prev_category = '';
		if($result) {
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
				$grandSold  	+= $totalsold;
				$grandReturned 	+= $totalreturned;
				$grandTotal  	+= $totalamount;
				$grandNetofRet 	+= ($totalsold - $totalreturned);
			}
		} else {
			$csv .= "\n";
			$csv .= '"","","' . number_format(0, 2) . '","' . number_format(0, 2) . '","' . number_format(0, 2) . '","' . number_format(0, 2) . '"';
		}
		$csv .= "\n";
		$footer = array(
			'',
			'',
			number_format($grandSold),
			number_format($grandReturned),
			number_format($grandNetofRet),
			number_format($grandTotal, 2),
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
		$customer	= $this->input->post('customer');
		$warehouse	= $this->input->post('warehouse');
		$datefilter	= $this->input->post('daterangefilter');
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = $this->date->dateDbFormat($date);
		}
		$pagination = $this->sales_item_model->getSalesPagination($category, $itemcode, $customer, $warehouse, $sort, $dates[0], $dates[1]);

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

		$details = $this->sales_item_model->getSalesTotal($category, $itemcode, $customer, $warehouse, $sort, $dates[0], $dates[1]);
		
		$tabledetails = '';

		if ($pagination->page_limit > 1) {
			$tabledetails .= '<tr class="success">
								<td colspan="7" class="text-center">Page ' . $pagination->page . ' of ' . $pagination->page_limit . '</td>
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