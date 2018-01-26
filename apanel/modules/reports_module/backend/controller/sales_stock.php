<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->sales_stock	    = new sales_stock();
		$this->item_model		= new item_model();
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
		$data['warehouse_list'] = $this->sales_stock->getWarehouseList();
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

	private function ajax_list() 
	{
		$data_post = $this->input->post(array('daterangefilter','warehouse','items','sort'));
		$pagination = $this->sales_stock->stockList($data_post);
		$totalqty = 0;
		$table = "";
		$grandtotal = 0;
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		$totalAmount = 0.00;
		$total_unitprice  = 0.00;
		foreach($pagination->result as $key => $row) {
			$totalAmount += $row->amount;
			$totalqty += $row->issueqty;
			$total_unitprice += $row->unitprice;
			$table .= '<tr>';
			$table .= '<td>' . $row->itemcode . '</td>';
			$table .= '<td>' . $row->label . '</td>';
			$table .= '<td>' . $row->detailparticular . '</td>';
			$table .= '<td>' . $row->warehouse . '</td>';
			$table .= '<td>' . $row->issueqty . '</td>';
			$table .= '<td>' . $row->unitprice . '</td>';
			$table .= '<td>' . number_format($row->amount,2) . '</td>';
			$table .= '</tr>';
		}
		$table .= '<tr>	
				<td colspan= "3"></td>
				<td style="font-weight:bold">Total</td>
				<td style="font-weight:bold"> ' . $totalqty .'</td>
				<td style="font-weight:bold"> ' . $total_unitprice .'</td>
				<td style="font-weight:bold"> '. number_format($totalAmount,2) .'</td></tr>';
			
		$pagination->table  = $table;
		$pagination->csv 	= $this->export();
		return $pagination;
	}

	private function export(){
		$data_post = $this->input->post(array('daterangefilter','warehouse','items','sort'));
		$daterangefilter= $data_post['daterangefilter'];
		$warehouse 		= $data_post['warehouse'];
		$limit 			= $data_post['items'];
		$sort 			= $data_post['sort'];
		
		$daterangefilter = str_replace(array('%2C', '+'), array(',', ' '), $data_post['daterangefilter']);
		$daterangefilter = explode('-', $daterangefilter);
		$dates		= array();
		foreach ($daterangefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}

		$start 	= $dates[0];
		$end 	= $dates[1];
		
		$result = $this->sales_stock->fileExport($start, $end, $warehouse, $limit, $sort);
		
		$totalqty = 0;
		$totalAmount = 0.00;
		$total_unitprice = 0.00;

		$header = array("Item Code","Item Category","Stocks","Warehouse","Unit Price","Quantity","Amount");

		$csv = '';
		$csv = '"' . implode('","', $header) . '"';
		$csv .= "\n";

		if(!empty($result)){
			foreach ($result as $key => $row){

				$totalAmount += $row->amount;
				$totalqty 	 += $row->issueqty;
				$total_unitprice += $row->unitprice;

				$csv .= '"' . $row->itemcode			. '",';
				$csv .= '"' . $row->label 				. '",';
				$csv .= '"' . $row->detailparticular 	. '",';
				$csv .= '"' . $row->warehouse 			. '",';
				$csv .= '"' . $row->issueqty 			. '",';
				$csv .= '"' . $row->unitprice 			. '",';
				$csv .= '"' . number_format($row->amount,2) 				. '"';
				$csv .= "\n";
			}
		}

		$csv .= '"","","","Total"," '.$totalqty.'","'.number_format($total_unitprice,2).'","'.number_format($totalAmount,2).'"';

		$csv .= "\n";
		return $csv;
	}


}