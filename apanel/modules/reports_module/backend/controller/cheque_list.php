<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->report   	    = new cheque_list_model();
		$this->session			= new session();
		$this->data = array();
		$this->view->header_active = 'cheque_list/';
	}

	public function view() {
		$this->view->title = 'Check List';
		$data['ui'] = $this->ui;
		
		$this->report_model = new report_model;
   		$this->report_model->generateBalanceTable();

        $data['partner_list'] 		= $this->report->retrievePartnerList();
		$data['bank_list'] 			= $this->report->retrieveBankList();
		$data['datefilter'] 		= $this->date->datefilterMonth();
		$this->view->load('cheque_list', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function update_cheques(){
		$posted_data 			=	$this->input->post(array('ids','release_remarks','release_date'));

		$releasedate			= 	(isset($posted_data['release_date']) && (!empty($posted_data['release_date']))) ? htmlentities(addslashes(trim($posted_data['release_date']))) : "";
		$remarks  				= 	(isset($posted_data['release_remarks']) && (!empty($posted_data['release_remarks']))) ? htmlentities(addslashes(trim($posted_data['release_remarks']))) : "";
		
		$releasedate 			= 	str_replace(array('%2C', '+'), array(',', ' '), $releasedate);
		
		$releasedate			= 	$this->date->dateDbFormat($releasedate);

		$data['releasedate'] 	=	$releasedate;
		$data['remarks'] 		=	$remarks;
		$data['stat'] 			=	'released';

		$posted_ids 			=	$posted_data['ids'];
		$id_arr 				=	explode(',',$posted_ids);

		foreach($id_arr as $key => $value)
		{
			$exp_ids 	 		=	explode('-',$value);
			$transtype 			=	$exp_ids[0];
			$check_number 		=	$exp_ids[1];
		
			$cond 				=	" chequenumber = '$check_number' ";
			
			$result 			=	0;
			if( $transtype == 'PV' || $transtype == 'DV' )
			{
				$result 			= 	$this->report->updateData($data, "pv_cheques", $cond);
			}
			else if( $transtype == "RV" )
			{
				$result 			= 	$this->report->updateData($data, "rv_cheques", $cond);
			}
		}

		if($result)
		{
			$msg = "success";
		} else {
			$msg = "Failed to Update.";
		}

		return $dataArray = array( "msg" => $msg );
	}

	private function update_void(){
		$posted_data 			=	$this->input->post(array('ids'));

		$data['stat'] 			=	'void';
		
		$posted_ids 			=	$posted_data['ids'];
		$id_arr 				=	explode(',',$posted_ids);

		foreach($id_arr as $key => $value)
		{
			$exp_ids 	 		=	explode('-',$value);
			$transtype 			=	$exp_ids[0];
			$check_number 		=	$exp_ids[1];
			
			$cond 				=	" chequenumber = '$check_number' ";

			$result 			=	0;
			if( $transtype == 'PV' || $transtype == 'DV' )
			{
				$result 			= 	$this->report->updateData($data, "pv_cheques", $cond);
			}
			else if( $transtype == "RV" )
			{
				$result 			= 	$this->report->updateData($data, "rv_cheques", $cond);
			}	
			
		}

		if($result)
		{
			$msg = "success";
		} else {
			$msg = "Failed to Update.";
		}

		return $dataArray = array( "msg" => $msg );
	}

	private function update_cancel(){
		$posted_data 			=	$this->input->post(array('ids'));

		$data['stat'] 			=	'cancelled';
		
		$posted_ids 			=	$posted_data['ids'];
		$id_arr 				=	explode(',',$posted_ids);

		foreach($id_arr as $key => $value)
		{
			$exp_ids 	 		=	explode('-',$value);
			$transtype 			=	$exp_ids[0];
			$check_number 		=	$exp_ids[1];
			
			$cond 				=	" chequenumber = '$check_number' ";

			$result 			=	0;
			if( $transtype == 'PV' || $transtype == 'DV' )
			
			{
				$result 			= 	$this->report->updateData($data, "pv_cheques", $cond);
			}
			else if( $transtype == "RV" )
			{
				$result 			= 	$this->report->updateData($data, "rv_cheques", $cond);
			}	
			
		}

		if($result)
		{
			$msg = "success";
		} else {
			$msg = "Failed to Update.";
		}

		return $dataArray = array( "msg" => $msg );
	}

	private function ajax_list() {
		$data 		= $this->input->post(array('daterangefilter','partner','bank','filter','search','sort'));
		$partner 	= $data['partner'];
		$filter 	= $data['filter'];
		$bank 		= $data['bank'];
		$search 	= $data['search'];
		$sort 		= $data['sort'];
		$datefilter	= $data['daterangefilter'];	
		
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$pagination = $this->report->retrieveChequeList($search, $dates[0], $dates[1], $partner, $filter, $bank, $sort);
		$table 			= 	'';
		$tabledetails	=	'';

		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		else
		{
			$prevcust 			= '';
			$nextcust			= '';

			$grandTotalAmount 	= 0;
			$grandTaxAmount 	= 0;
			$grandAmount 		= 0;

			foreach ($pagination->result as $key => $row) {
				$releasedate 		=	isset($row->releasedate) ?	$this->date->dateFormat($row->releasedate) 	: 	"";
				$chequenumber  		=	$row->chequenumber;
				$invoiceno 			=	$row->invoiceno;
				$voucherno 			=	$row->voucherno;
				$chequedate 		=	$row->chequedate;
				$bankname 	 		=	$row->bank;
				$partnername 		=	$row->partner;
				$cleareddate 		=	isset($row->cleardate) 	? 	$this->date->dateFormat($row->cleardate) 	: 	"";
				$chequeamount 		=	$row->chequeamount;
				$stat 				=	$row->stat;
				$transtype 			= 	$row->transtype;

				$prev_date			=	$chequedate;
				
				$dropdown = $this->ui->loadElement('check_task')
									 ->addCheckbox($stat!='released' && $stat!='cancelled' && $stat!='void')
									 ->setValue($transtype."-".$chequenumber)
									 ->draw();
				
				$status_display 	=	"";

				if( $stat == 'uncleared' ){
					$status_display =	'<span class="label label-primary">'.strtoupper("prepared").'</span>';
				}
				else if( $stat == 'released' ){
					$status_display =	'<span class="label label-info">'.strtoupper($stat).'</span>';
				}
				else if( $stat == 'cleared' ){
					$status_display =	'<span class="label label-success">'.strtoupper($stat).'</span>';
				}
				else if( $stat == 'void' ){
					$status_display =	'<span class="label label-warning">'.strtoupper($stat).'</span>';
				}
				else if( $stat == 'cancelled' ){
					$status_display =	'<span class="label label-danger">'.strtoupper($stat).'</span>';
				}
				
				$table .= '<tr>';
				$table .= '<td style="text-align:center;">' . $dropdown 	. '</td>';
				$table .= '<td>' . $this->date->dateFormat($chequedate) 	. '</td>';
				$table .= '<td>' . $chequenumber 	. '</td>';
				$table .= '<td>' . $invoiceno 		. '</td>';
				$table .= '<td>' . $voucherno 		. '</td>';
				$table .= '<td>' . $bankname 		. '</td>';
				$table .= '<td>' . $partnername 	. '</td>';
				$table .= '<td class="text-right">' . number_format($chequeamount,2) . '</td>';
				$table .= '<td>' . $releasedate 	. '</td>';
				$table .= '<td>' . $cleareddate 	. '</td>';
				$table .= '<td>' . $status_display 	. '</td>';
				$table .= '</tr>';
			}

			$footerdtl 			= 	$this->report->getSalesTotal($search, $dates[0], $dates[1], $partner, $filter, $bank, $sort);
			$grandtotal 		=	isset($footerdtl->totalamount)		?	$footerdtl->totalamount		: 	0;

			if ($pagination->page_limit > 1) {
				$tabledetails .= '<tr class="success">
									<td colspan="10" class="text-center">Page ' . $pagination->page . ' of ' . $pagination->page_limit . '</td>
								</tr>';
			}

			$tabledetails .= '<tr>
								<th colspan="7">Grand Total: </th>
								<th class="text-right">' . number_format($grandtotal,2) . '</th>
							</tr>';
		}

		$pagination->table 			= $table;
		$pagination->tabledetails 	= $tabledetails;
		$pagination->csv 			= $this->export();
		return $pagination;
	}

	private function export()
	{
		$data 		= $this->input->post(array('daterangefilter','partner','bank','filter','search','sort')); 	

		$partner 	= $data['partner'];
		$filter 	= $data['filter'];
		$bank 		= $data['bank'];
		$search 	= $data['search'];
		$sort 		= $data['sort'];
		$strdate	= $data['daterangefilter'];	
		$datefilter = explode('-', $data['daterangefilter']);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$retrieved = $this->report->fileExport($search, $dates[0], $dates[1], $partner, $filter, $bank, $sort);
		
		$header = array("Check Date","Check Number","Invoice No.","Bank","Partner","Release Date","Cleared Date","Amount","Check Status"); 
	
		$csv 	= '';
		$csv 	.= 'Check List';
		$csv 	.= "\n\n";
		$csv 	.= '"Date:","'.$strdate.'"';
		$csv 	.= "\n\n";
		$csv 	.= '"' . implode('","', $header) . '"';
		$csv 	.= "\n";

		$grandtotal 	=	0;
		
		if (!empty($retrieved)){
			$next_date 	=	$prev_date 	=	"";
			$total_per_cheque	=	0;
			$count_per_cheque 	=	0;
			$total 		=	0;

			foreach ($retrieved as $key => $row){
				$chequedate 		=	$row->chequedate;
				$chequenumber 		=	$row->chequenumber;
				$prev_date			=	$chequedate;
				
				if( $prev_date != $next_date ){
					if ($next_date != "") {
						$csv .= '"","","","Total checks for '.$chequedate.'","'.$count_per_cheque.'","' . number_format($total_per_cheque,2) . '"';
						$csv .= "\n\n";
						$total_per_cheque	=	0;
						$count_per_cheque 	=	0;
					}
				} 
					
				$csv .= ($prev_date!=$next_date) 	? 	'"' . $chequedate . '",' 	:	'"",';
				$csv .= '"' . $row->chequenumber . '",';
				$csv .= '"' . $row->invoiceno . '",';
				$csv .= '"' . $row->bank . '",';
				$csv .= '"' . $row->partner . '",';
				$csv .= '"' . number_format($row->chequeamount,2) . '",';
				$csv .= '"' . $row->releasedate . '",';
				$csv .= '"' . $row->cleardate . '",';
				$csv .= '"' . $row->stat . '"';
				$csv .= "\n";


				$next_date 	=	$prev_date;

				$total_per_cheque 	+=	$row->chequeamount;
				$grandtotal 		+=	$row->chequeamount;
				$count_per_cheque 	+=	1;
			}

			$csv .= '"","","","Total checks for '.$chequedate.'","'.$count_per_cheque.'","' . number_format($total_per_cheque,2) . '"';
			$csv .= "\n\n";
			
			$csv .= '"","","","","Grand Total","' . number_format($grandtotal,2) . '"';
			$csv .= "\n";
		}

		return $csv;
	}

	public function check_stat(){
		$ids = $this->input->post('ids');
		$id_arr =	explode(',',$ids);
		$stat = array();
		$vno = array();
		$cno = array();
		$msg = array();
		foreach ($id_arr as $value) {
			$exp_ids 	 		=	explode('-',$value);
			$transtype 			=	$exp_ids[0];
			$check_number 		=	$exp_ids[1];
			$result = $this->report->check_stat($transtype, $check_number );
			foreach ($result as $val) {
				$stat = $val->stat;
				if ($stat != 'posted'){
					$vno = $val->voucherno;
					$cno = $val->chequenumber;
					$msg[] = "Check number $cno in Voucher Number $vno is not yet posted";
				}
			}
		}

		$error_msg = '';
		if ($msg) {
			$error_msg .= 'Unable to Release Cheques.';
			$error_msg .= '<ul>';
			foreach ($msg as $mes) {
				$error_msg .= "<li>$mes</li>";
			}
			$error_msg .= '</ul>';
		}

		return array('msg' => $error_msg );

		
	
		

	}
}