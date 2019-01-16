<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->sales_category   = new sales_category();
		$this->item_class_model	= new item_class_model();
		$this->session			= new session();
		$this->data = array();
		$this->view->header_active = 'sales_report/';
	}

	public function listing() {
		$this->view->title = 'Sales per Category';
		$data['ui'] = $this->ui;
        $data['category_list'] 		= $this->item_class_model->getParentClass('');
		$data['warehouse_list'] 	= $this->sales_category->getWarehouseList();
		$data['datefilter'] 		= 	$this->date->datefilterMonth();
		$this->view->load('sales_category', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data 		= $this->input->post(array('category','warehouse','daterangefilter'));
		$warehouse 	= $data['warehouse'];
		$category 	= $data['category'];
		$datefilter	= $data['daterangefilter'];
		$datefilter = explode('-', $datefilter);

		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}

		$list = $this->sales_category->retrieveSalesReport($category, $dates[0], $dates[1],$warehouse);
		$table = '';

		if (empty($list->result)) {
			$table = '<tr><td colspan="4" class="text-center"><b>No Records Found</b></td></tr>';
		}
		else
		{
			$grandTotalAmount 	= 0;
			$grandAmount 		= 0;

			foreach ($list->result as $key => $row) 
			{
				$category 			= $row->category;
				$sales_qty 			= $row->sales_qty;
				$sales_uom 			= $row->sales_uom;
				$base_qty 			= $row->base_qty;
				$base_uom 			= $row->base_uom;
				$totalamount 		= $row->amount;
				
				$table .= '<tr>';
				$table .= '<td>' . $category . '</td>';
				$table .= '<td class = "text-right">' . number_format($sales_qty,2) .' '. $sales_uom .'</td>';
				$table .= '<td class = "text-right">' . number_format($base_qty,2) .' '. $base_uom .'</td>';
				$table .= '<td class = "text-right">' . number_format($totalamount,2) . '</td>';
				$table .= '</tr>';

				$grandAmount	 += $totalamount;
			}

			$table .='<tr>';
			$table .='<td align="right" colspan="3"><b>Total Amount</b></td>';
			$table .='<td align="right">'.number_format($grandAmount,2).'</td>';
			$table .='</tr>'; 
		}

		$list->table = $table;
		$list->csv   = $this->export();
		return $list;
	}

	private function export(){
		$data = $this->input->post(array('category','warehouse','daterangefilter'));
		$data['daterangefilter'] = str_replace(array('%2C', '+'), array(',', ' '), $data['daterangefilter']);
		$result = $this->sales_category->fileExport($data);
		$header = array("Item Category","Sales Qty","Base Qty","Amount");

		$csv = 'Sales Per Category';
		$csv .= "\n\n";
		$csv .= '"Date:","'.$data['daterangefilter'].'"';
		$csv .= "\n\n";
		$csv .= '"' . implode('","', $header) . '"';
		$csv .= "\n";
		
		$totalAmount = 0.00;
		if (!empty($result)){
			foreach ($result as $key => $row){
				$totalAmount += $row->amount;
				$csv .= '"' . $row->category . '",';
				$csv .= '"' . $this->amount($row->sales_qty) . ' '.$row->sales_uom.'",';
				$csv .= '"' . $this->amount($row->base_qty) . ' '.$row->base_uom.'",';
				$csv .= '"' . $this->amount($row->amount) . '"';
				$csv .= "\n";
			}
		}
		$csv .= ',,"Total Amount:","' . $this->amount($totalAmount) . '"';
		return $csv;
	}

	private function amount($amount)
	{
		return number_format($amount,2);
	}

}