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

	// private function ajax_list() 
	// {
	// 	$data_post = $this->input->post(array('daterangefilter','warehouse','limit'));
	
	// 	$pagination = $this->sales_stock->stockList($data_post);
	// 	$totalqty = 0;
	// 	$tablerow = "";
	// 	$grandtotal = 0;
	// 	if(!empty($pagination->result))
	// 	{	
			
	// 		for($i=0;$i < count($pagination->result);$i++)
	// 		{	
				
	// 			$itemcategory 		= $pagination->result[$i]->label;
	// 			$itemcode 		    = $pagination->result[$i]->itemcode;
	// 			$detailparticular   = $pagination->result[$i]->detailparticular;
	// 			$issueqty   		= $pagination->result[$i]->issueqty;
	// 			$warehouse    		= $pagination->result[$i]->warehouse;
	// 			$waredescription    = $pagination->result[$i]->description;
	// 			$unitprice          = number_format($pagination->result[$i]->unitprice,2);
	// 			$amount 			= number_format($pagination->result[$i]->amount,2);
			
	// 			if($itemcode != ""){
	// 				$totalqty      += $pagination->result[$i]->issueqty;	
	// 				$grandtotal    += $pagination->result[$i]->amount;
	// 			$tablerow .= '<tr>';
	// 			$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$itemcode.'</td>';
	// 			$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;<b>'.$itemcategory.'</b></td>';
	// 			$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;<b>'.$waredescription.'</b></td>';
	// 			$tablerow	.= '<td class="text-center" style="vertical-align:middle;">&nbsp;'.$issueqty.'</td>';
	// 			$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$detailparticular.'</td>';
	// 			$tablerow	.= '<td class="text-center" style="vertical-align:middle;">&nbsp;'.$amount .'</td>';
	// 			$tablerow .= '</tr>';
	// 			}
	// 			//subtotal
	// 			else{
					
	// 				$tablerow .= '<tr style="background:#FFF">';
	// 				$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;</td>';
	// 				$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;</td>';
	// 				$tablerow	.= '<td class="text-right " style="vertical-align:middle;">&nbsp;<b>Total Quantity</b></td>';
	// 				$tablerow	.= '<td class="text-center" style="vertical-align:middle;">&nbsp;'.$issueqty.'</td>';
	// 				$tablerow	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;<b>Subtotal:</b></td>';
	// 				$tablerow	.= '<td class="text-center" style="vertical-align:middle;">&nbsp;'.$amount .'</td>';
	// 				$tablerow .= '</tr>';
	// 			}


	// 		}	

	

	// 		/**TOTAL AMOUNTS**/
	// 			$tablerow .= '<tr style="background:#DDD">';
	// 			$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;</td>';
	// 			$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;</td>';
	// 			$tablerow	.= '<td class="text-right " style="vertical-align:middle;">&nbsp;</td>';
	// 			$tablerow	.= '<td class="text-center" style="vertical-align:middle;">&nbsp;
	// 			</td>';
	// 			$tablerow	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;<b>Grand total:</b></td>';
	// 			$tablerow	.= '<td class="text-center" style="vertical-align:middle;">&nbsp;'.number_format($grandtotal,2) .'</td>';
	// 			$tablerow .= '</tr>';
	// 	}else{
	// 		$tablerow	.= '<tr>';
	// 		$tablerow	.= '<td class="center" colspan="3">- No Records Found -</td>';
	// 		$tablerow	.= '</tr>';
	// 	}

	// 	$pagination->table = $tablerow;
	// 	return $pagination;
	// }

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
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			if($row->stat == 'open') {
				$status = '<span class="label label-warning">PENDING</span>';
			}
			else if($row->stat == 'released') {
				$status = '<span class="label label-info">RELEASED</span>';
			}
			else if( $row->stat == 'received' ) {	
				$status = '<span class="label label-success">RECEIVED</span>';
			}
			
			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
						->addView()
						->setModuleURL('inventory/stock_transfer')
						->addPrint()
						->setValue($row->stocktransferno)
						->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td><a href="'.BASE_URL.'inventory/stock_transfer/view/' . $row->stocktransferno . '">' . $row->stocktransferno . '</a></td>';
			$table .= '<td>' . $row->desc1 . '</td>';
			$table .= '<td>' . $row->desc2 . '</td>';
			$table .= '<td>' . $row->qtytoapply . '</td>';
			$table .= '<td>' . $status . '</td>';
			$table .= '</tr>';
		}
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
		$header = array("Transaction Date","Stock TransferNo.","Source","Destination","Quantity","Status");

		$csv = '';
		$csv = '"' . implode('","', $header) . '"';
		$csv .= "\n";


		if(!empty($result)){
			foreach ($result as $key => $row){
				if($row->stat == 'open') {
				$status = 'PENDING';
				}
				else if($row->stat == 'released') {
					$status = 'RELEASED';
				}
				else if( $row->stat == 'received' ) {	
					$status = 'RECEIVED';
				}
				$csv .= '"' . $this->date->dateFormat($row->transactiondate) . '",';
				$csv .= '"' . $row->stocktransferno . '",';
				$csv .= '"' . $row->desc1 . '",';
				$csv .= '"' . $row->desc2 . '",';
				$csv .= '"' . $row->qtytoapply . '",';
				$csv .= '"' . $status . '",';
				$csv .= "\n";
			}
		}
		return $csv;
	}

}