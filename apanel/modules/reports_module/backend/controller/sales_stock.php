<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->sales_stock	    = new sales_stock();
		$this->item_model		= new item_model();
		$this->item_class_model	= new item_class_model();
		$this->show_input 	    = true;
		$this->session			= new session();
		$this->data = array();
		$this->view->header_active  = 'report/';
	}

	public function listing() {
		$this->view->title = 'Sales Report per Stock';
		$data['ui'] 		 = $this->ui;
		$data['show_input']  = $this->show_input;
		$data['warehouse']   = "";
		$data['datefilter']  = $this->date->datefilterMonth();
		$data['category_list']	= $this->item_class_model->getParentClass('');
		$data['warehouse_list'] 	= $this->sales_stock->getWarehouseList();
		$this->view->load('sales_stock', $data);
	}
	
	public function ajax($task)
	{
		header('Content-type: application/json');
		$result 	=	"";

		if($task == 'list'){
			$result = $this->ajax_list();
		}else if($task == 'export'){
			$result = $this->export();
		}

		echo json_encode($result); 
	}

	public function ajax_list() {
		$warehouse      =  	$this->input->post('warehouse');
		$sort 		  	=  	$this->input->post('sort');
		$category 		=  	$this->input->post('category');
		$datefilter 	= 	$this->input->post('daterangefilter');
		$datefilter 	= 	explode('-', $datefilter);
		$dates			= 	array();
		foreach ($datefilter as $date) {
			$dates[] = $this->date->dateDbFormat($date);
		}	

		$pagination = $this->sales_stock->stockList($warehouse, $sort, $category, $dates[0], $dates[1]);
		$totalqty = 0;
		$table = "";
		$grandtotal = 0;
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		$totalAmount = 0.00;
		$total_unitprice  = 0.00;
		if ($pagination->result) {
			foreach($pagination->result as $key => $row) {
				$totalAmount 		+= $row->amount;
				$totalqty 			+= $row->issueqty;
				$total_unitprice 	+= $row->unitprice;
				$table .= '<tr>';
				$table .= '<td>' . $row->itemcode . '</td>';
				$table .= '<td>' . $row->label . '</td>';
				$table .= '<td>' . $row->detailparticular . '</td>';
				// $table .= '<td>' . $row->warehouse . '</td>';
				$table .= '<td class="text-right">' . $row->issueqty . '</td>';
				$table .= '<td class="text-right">' . number_format($row->unitprice,2) . '</td>';
				$table .= '<td class="text-right">' . number_format($row->amount,2) . '</td>';
				$table .= '</tr>';
			}
		}  else {
			$table = '<tr><td colspan="6" class="text-center"><b>No Records Found</b></td></tr>';
		}
		
		$details = $this->sales_stock->getSalesTotal($warehouse, $sort, $category, $dates[0], $dates[1]);
		// var_dump($details);
		$tabledetails = '';

		if ($pagination->page_limit > 1) {
			$tabledetails .= '<tr class="success">
								<td colspan="6" class="text-center">Page ' . $pagination->page . ' of ' . $pagination->page_limit . '</td>
							</tr>';
		}

		$tabledetails .= '<tr>
							<th colspan="3">Grand Total: </th>
							<th class="text-right">' . $details->issueqty . '</th>
							<th class="text-right">' . number_format($details->unitprice,2) . '</th>
							<th class="text-right">' . number_format($details->amount,2) . '</th>
						</tr>';
			
		$pagination->table  		= $table;
		$pagination->tabledetails	= $tabledetails;	

		return $pagination;
	}

	public function export(){
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="' . MODULE_NAME . '.csv"');
		$warehouse      =  	$this->input->get('warehouse');
		$sort 		  	=  	$this->input->get('sort');
		$category 		=  	$this->input->get('category');
		$datefilter 	= 	$this->input->get('daterangefilter');
		$datefilter 	= 	explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = $this->date->dateDbFormat(str_replace(array('+', '%2C'), array(' ', ','), $date));
		}
		$result = $this->sales_stock->fileExport($warehouse, $sort, $category, $dates[0], $dates[1]);

		$totalqty = 0;
		$totalAmount = 0;
		$total_unitprice = 0;

		$header = array("QTY","STOCK","AMOUNT");
		$csv = "";
		$csv .= 'Sales Report Per Stock';
		$csv .= "\n\n";
		$csv .= '"Date","'.$this->date->dateFormat($dates[0]).' - '.$this->date->dateFormat($dates[1]).'"'; 
		$csv .= "\n\n";
		if( $warehouse != "" && !is_null($warehouse) ){
		  $csv .= '"Warehouse","'.$warehouse.'"';
		  $csv .= "\n\n";
		}
		$csv .= '"' . implode('","', $header) . '"';
		$csv .= "\n";
	  
		$prev_cat = '';
		$grandtotal 	=	0;
		$grandQty 		=	0;
		if(!empty($result)){
		  foreach ($result as $key => $row){
	  
			$totalAmount += $row->amount;
			$totalqty    += $row->issueqty;
			$total_unitprice += $row->unitprice;
	  
			if ($prev_cat != $row->label) {
			  $csv .= '"' . $row->label . '"' . "\n";
			  $prev_cat = $row->label;
			}
			
			$csv .= '"' . $row->issueqty       . '",';
			$csv .= '"' . $row->detailparticular   . '",';
			$csv .= '"' . number_format($row->amount,2) . '"';
			$csv .= "\n";
	  
			if ( ! isset($result[$key + 1]) || $result[$key + 1]->label != $row->label) {
			  $csv .= '"'.number_format($totalqty,0).'","Total ' . $row->label . '","' .number_format($totalAmount,2). '"';
			  $csv .= "\n";
			  $grandtotal  	+= $totalAmount;
			  $grandQty  	+= $totalqty;
			  $totalqty 	= 	0;
			  $totalAmount 	=	0;
			}
		  }
		}
		$csv .= "\n";
		$footer = array(
			number_format($grandQty),
			"Grand Total",
			number_format($grandtotal, 2),
		);
		$csv .= '"' . implode('","', $footer) . '"';
		echo $csv;
	}


}