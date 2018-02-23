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
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			if($row->status == 'partial') {
				$status = '<span class="label label-info">PARTIAL</span>';
			}
			else if( $row->status == 'open' && $row->source_no != "" ) {	
				$status = '<span class="label label-success">TRANSFERRED</span>';
			}
			else if($row->status == 'open') {
				$status = '<span class="label bg-purple">PENDING</span>';
			}
			else if( $row->status == 'rejected' ) {	
				$status = '<span class="label label-danger">REJECTED</span>';
			} else if( $row->status == 'approved' ) {	
				$status = '<span class="label label-warning">APPROVED</span>';
			}
			
			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
						->addView()
						->addPrint();
						if ($row->source_no != ""){
							$dropdown->setTaskLink(array('view' => 'view_approval','print' => 'print_approval'));
						}else{
							$dropdown->setTaskLink(array('view' => 'view','print' => 'print_preview'));
						}
						$dropdown = $dropdown->setModuleURL('inventory/stock_transfer')
						->setValue($row->st_no)
						->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->date) . '</td>';
			$link 	= '';
			if ($row->source_no != ""){
				$link .= 'view_approval';
			}else{
				$link .= 'view';
			}
			$table .= '<td><a href="'.BASE_URL.'inventory/stock_transfer/'.$link.'/' . $row->st_no . '">' . $row->st_no . '</a></td>';

			$table .= '<td>' . $row->source_no . '</td>';
			$table .= '<td>' . $row->source . '</td>';
			$table .= '<td>' . $row->destination . '</td>';
			// $table .= '<td>' . number_format($row->qty,0) . '</td>';
			$table .= '<td>' . $status. '</td>';
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
		$header = array("Transaction Date","Request No.","Approval No.","Requesting Warehouse","Source Warehouse","Status");

		$csv = '';
		$csv = '"' . implode('","', $header) . '"';
		$csv .= "\n";

		if(!empty($result)){
			foreach ($result as $key => $row){
				if($row->status == 'partial') {
					$status = 'PARTIAL';
				}
				else if( $row->status == 'open' && $row->source_no != "" ) {	
					$status = 'TRANSFERRED';
				}
				else if($row->status == 'open') {
					$status = 'PENDING';
				}
				else if( $row->status == 'rejected' ) {	
					$status = 'REJECTED';
				} else if( $row->status == 'approved' ) {	
					$status = 'APPROVED';
				}
				$csv .= '"' . $this->date->dateFormat($row->date) . '",';
				$csv .= '"' . $row->st_no . '",';
				$csv .= '"' . $row->source_no . '",';
				$csv .= '"' . $row->source . '",';
				$csv .= '"' . $row->destination . '",';
				// $csv .= '"' . number_format($row->qty,0) . '",';
				$csv .= '"' . $status . '",';
				$csv .= "\n";
			}
		}
		return $csv;
	}

}