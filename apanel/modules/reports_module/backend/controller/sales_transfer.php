<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->sales_stock	    = new sales_transfer();
		$this->item_model		= new item_model();
		$this->show_input 	    = true;
		$this->session			= new session();
		$this->data = array();
		$this->view->header_active  = 'report/';
	}

	public function listing() {
		$this->view->title = 'Stock Transfer Report';
		$data['ui'] 		 = $this->ui;
		$data['show_input']  = $this->show_input;
		$data['warehouse']   = "";
		$data['datefilter']  = $this->date->datefilterMonth();
		$data['warehouse_list'] = $this->sales_stock->getWarehouseList();
		$this->view->load('sales_transfer', $data);
	}
	
	public function ajax($task)
	{
		header('Content-type: application/json');
		$result 	=	"";
		if($task == 'sales_transferlist'){
			$result = $this->sales_transferlist();
		}else if($task == 'export'){
			$result = $this->export();
		}
		echo json_encode($result); 
	}

	public function sales_transferlist(){
		$data_post = $this->input->post(array('daterangefilter','warehouse1', 'warehouse2','limit', 'filter', 'sort'));
		$daterangefilter = $data_post['daterangefilter'];
		$warehouse1 = $data_post['warehouse1'];
		$warehouse2 = $data_post['warehouse2'];
		$limit = $data_post['limit'];
		$sort = $data_post['sort'];
		$filter = $data_post['filter'];
		$daterangefilter = explode('-', $daterangefilter);
		$dates		= array();
		foreach ($daterangefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$start = $dates[0];
		$end   = $dates[1];
		$pagination = $this->sales_stock->sales_transferlist($start, $end, $warehouse1, $warehouse2, $limit , $filter, $sort);
		$table = '';
		$status 	=	"";
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		$prev = '';
		$next = '';
		$prev1 = '';
		$next1 = '';
		$qty = 0;
		foreach ($pagination->result as $key => $row) {
			
			$prev  = $row->stocktransferno;

			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
			->addPrint();
			$dropdown->setTaskLink(array('print' => 'print_approval'))
			->setValue($row->stocktransferno);
			$dropdown = $dropdown->setModuleURL('inventory/stock_transfer')
			->draw();
			
			if ($prev != $next){
				$table .= '<td align = "center">' . $dropdown . '</td>';
				
				$table .= '<td style="font-weight:bold">' . $row->stocktransferno . '</td>';
				$table .= '<td>' . $this->date($row->date) . '</td>';
				$table .= '<td>' . $row->source . '</td>';
				$table .= '<td>' . $row->destination . '</td>';
				$table .= '<td><a href="'.BASE_URL.'/inventory/stock_transfer/print_preview/'.$row->source_no.'">' . $row->source_no . '</a></td>';
			} else {
				$table .='<td></td>';
				$table .='<td></td>';
				$table .='<td></td>';
				$table .='<td></td>';
				$table .='<td></td>';
				$table .='<td></td>';
			}
			
			$table .= '<td>' . ($row->itemcode) . '</td>';
			$table .= '<td>' . $row->detailparticular . '</td>';
			$table .= '<td>' . strtoupper($row->uom) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->qtytransferred,0) . '</td>';
			$table .= '</tr>';

			$next = $prev; 
			$qty 	+=  $row->qtytransferred;
		}
		$table .= '<tr>';
		$table .= '<td colspan = "8"></td>';
		$table .= '<td><strong>TOTAL<strong></td>';
		$table .= '<td style="font-weight:bold;" class="text-right"><strong>'.$qty.'<strong></td>';
		$table .= '<td></td>';
		$table .= '</tr>' ;
		$pagination->table = $table;
		$pagination->csv 	= $this->export();
		return $pagination;

	}

	private function export(){
		$data_post = $this->input->post(array('daterangefilter','warehouse1', 'warehouse2','limit', 'filter', 'sort'));
		$daterangefilter = $data_post['daterangefilter'];
		$warehouse1 = $data_post['warehouse1'];
		$warehouse2 = $data_post['warehouse2'];
		$limit = $data_post['limit'];
		$filter = $data_post['filter'];
		$daterangefilter = str_replace(array('%2C', '+'), array(',', ' '), $data_post['daterangefilter']);
		$sort = $data_post['sort'];
		$daterangefilter = explode('-', $daterangefilter);
		$dates		= array();
		foreach ($daterangefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$start = $dates[0];
		$end = $dates[1];
		$result = $this->sales_stock->fileExport($start, $end, $warehouse1, $warehouse2, $limit , $filter, $sort);
		$header = array("Transfer No.","Transation Date","Requesting Warehouse","Destination Warehouse","Request No.","Item Code","Item Desc","Uom","No of Items");

		$csv = '';
		$csv = '"' . implode('","', $header) . '"';
		$csv .= "\n";
		$status="";
		$next = '';
		$prev = '';
		$qty = 0;
		if(!empty($result)){
			foreach ($result as $key => $row){
				$prev  = $row->stocktransferno;
				if ($prev != $next){
					$csv .= '"' . $row->stocktransferno . '",';
					$csv .= '"' . $this->date($row->date) . '",';
					$csv .= '"' . $row->source . '",';
					$csv .= '"' . $row->destination . '",';
					$csv .= '"' . $row->source_no . '",';
				} else {
					$csv .= '"",';
					$csv .= '"",';
					$csv .= '"",';
					$csv .= '"",';
					$csv .= '"",';
				}
				
				$csv .= '"' . $row->itemcode . '",';
				$csv .= '"' . $row->detailparticular . '",';
				$csv .= '"' . strtoupper($row->uom). '",';
				$csv .= '"' . number_format($row->qtytransferred,0) . '",';
				$csv .= "\n";
				$next = $prev; 
				$qty += $row->qtytransferred;
			}
			$csv .= '"",';
			$csv .= '"",';
			$csv .= '"",';
			$csv .= '"",';
			$csv .= '"",';
			$csv .= '"",';
			$csv .= '"",';
			$csv .= '"TOTAL",';
			$csv .= '"'.$qty.'",';
			
			
		}
		return $csv;
	}

	private function date($date)
	{
		return date("M d, Y",strtotime($date));
	}

}