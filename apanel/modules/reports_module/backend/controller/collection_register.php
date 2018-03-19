<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->report   	    = new collection_register_model();
		$this->session			= new session();
		$this->data = array();
		$this->view->header_active = 'collection_register/';
	}

	public function view() {
		$this->view->title = 'Collection Register';
		$data['ui'] = $this->ui;
		
		$this->report_model = new report_model;
   		$this->report_model->generateBalanceTable();

        $data['partner_list'] 		= $this->report->retrievePartnerList();
		$data['payment_list'] 		= $this->report->retrieveReceiptOptions();
		// $data['datefilter'] 		= $this->date->datefilterMonth();

		$current_date 				= date('Y-m-d');
		$data['datefilter'] 		= $this->date->datefilterToday($current_date);
		$this->view->load('collection_register', $data);
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
			if( $transtype == 'PV' )
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
		$data 		= $this->input->post(array('daterangefilter','partner','bank','filter','search','sort','mode'));
		$partner 	= $data['partner'];
		$filter 	= $data['filter'];
		$bank 		= $data['bank'];
		$search 	= $data['search'];
		$sort 		= $data['sort'];
		$mode 		= $data['mode'];
		$datefilter	= $data['daterangefilter'];	
		//var_dump($data);
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$pagination = $this->report->retrieveChequeList($search, $dates[0], $dates[1], $partner, $filter, $bank, $sort, $mode);
		$table = '';
		$tabledetails = '';

		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		} else {
			$grandTotalAmount 	= 0;
			$grandTaxAmount 	= 0;
			$grandAmount 		= 0;
			$subtotal 	 		= 0;

			$prev_date 			= $next_date 	=	"";

			foreach ($pagination->result as $key => $row) {
				$transactiondate 		=	$row->transactiondate;
				$receiptno 				= 	$row->voucherno;
				$partnername 			=	$row->partnername;
				$paymentdetail			=	$row->payment;
				$bank					=	$row->bank;
				$amount 				=	$row->amount;
				$paymentdate	 		=	$row->paymentdate;
				$subtotal 				+=	$amount;
				
				$transactiondate 		=	$this->date->dateFormat($transactiondate);
				$prev_date 				=	$transactiondate;

				$table .= '<tr>';
				if( $prev_date != $next_date ) {
					$table .= '<td>' . $transactiondate	. '</td>';
				} else {
					$table .= '<td></td>';
				}
				$table .= '<td><a target="_blank" href="'.BASE_URL.'financials/receipt_voucher/print_preview/'.$receiptno.'">' . $receiptno 	. '</td>';
				$table .= '<td>' . $partnername . '</td>';
				$table .= '<td>' . $paymentdetail 	. '</td>';
				$table .= '<td>' . $this->date->dateFormat($paymentdate) . '</td>';
				$table .= '<td class="text-right">' . number_format($amount,2) . '</td>';
				$table .= '</tr>';
				
				$next_date 	=	$prev_date;
			}

			$footerdtl 			= 	$this->report->getSalesTotal($search, $dates[0], $dates[1], $partner, $filter, $bank, $sort, $mode, "grand");
			$grandtotal 		=	isset($footerdtl->totalamount)		?	$footerdtl->totalamount		: 	0;

			$footerdtl 			= 	$this->report->getSalesTotal($search, $dates[0], $dates[1], $partner, $filter, $bank, $sort, $mode, "cash");
			$totalcashissued 	=	isset($footerdtl->count) 	 	?	$footerdtl->count 			: 	0;
			$totalcashamount 	=	isset($footerdtl->totalamount) 	? 	$footerdtl->totalamount 	: 	0;
			
			$footerdtl 			= 	$this->report->getSalesTotal($search, $dates[0], $dates[1], $partner, $filter, $bank, $sort, $mode, "pdc");
			$totalpdcissued 	=	isset($footerdtl->count) 		?	$footerdtl->count			:	0;
			$totalpdcamount 	=	isset($footerdtl->totalamount)	?	$footerdtl->totalamount 	: 	0;

			$footerdtl 			= 	$this->report->getSalesTotal($search, $dates[0], $dates[1], $partner, $filter, $bank, $sort, $mode, "dated");
			$totaldatedissued 	=	isset($footerdtl->count) 		?	$footerdtl->count 			:	0;
			$totaldatedamount 	=	isset($footerdtl->totalamount)	?	$footerdtl->totalamount 	: 	0;


			$totalreceiptissued =	0;
			$totalreceiptamt 	=	0;
			$totalreceiptissued =	$totalcashissued 	+	$totaldatedissued 	+	$totalpdcissued;
			$totalreceiptamt 	=	$totalcashamount 	+	$totaldatedamount 	+	$totalpdcamount;

			if ($pagination->page_limit > 1) {
				$tabledetails .= '<tr class="success">
									<td colspan="7" class="text-center">Page ' . $pagination->page . ' of ' . $pagination->page_limit . '</td>
								</tr>';
			}

			$tabledetails .= '<tr>
								<th colspan="5">Grand Total: </th>
								<th class="text-right">' . number_format($grandtotal,2) . '</th>
							</tr>';

			$tabledetails .= '<tr class="danger">
								<th colspan="6">Summary:</th>
							</tr>
							<tr class="warning">
								<th></th>
								<th class="text-right">Total Issued</th>
								<th class="text-right">Total Amount</th>
								<th colspan="3"></th>
							</tr>
							<tr>
								<th>Cash:</th>
								<th class="text-right">' . number_format($totalcashissued) . '</th>
								<th class="text-right">' . number_format($totalcashamount,2) . '</th>
								<th colspan="3"></th>
							</tr>
							<tr>
								<th>Dated Check:</th>
								<th class="text-right">' . number_format($totaldatedissued) . '</th>
								<th class="text-right">' . number_format($totaldatedamount,2) . '</th>
								<th colspan="3"></th>
							</tr>
							<tr>
								<th>PDC:</th>
								<th class="text-right">' . number_format($totalpdcissued) . '</th>
								<th class="text-right">' . number_format($totalpdcamount,2) . '</th>
								<th colspan="3"></th>
							</tr>
							<tr class="warning">
								<th>Total Provisional Receipt Issued:</th>
								<th class="text-right">' . number_format($totalreceiptissued) . '</th>
								<th class="text-right">' . number_format($totalreceiptamt, 2) . '</th>
								<th colspan="3"></th>
							</tr>';
		}

		$pagination->table 			= $table;
		$pagination->tabledetails	= $tabledetails;
		$pagination->csv 			= $this->export();
		return $pagination;
	}

	private function export(){
		$data 		= $this->input->post(array('daterangefilter','partner','bank','filter','search','sort','mode')); 	

		$partner 	= $data['partner'];
		$filter 	= $data['filter'];
		$bank 		= $data['bank'];
		$search 	= $data['search'];
		$sort 		= $data['sort'];
		$mode 		= $data['mode'];
		$strdate	= $data['daterangefilter'];	
		$datefilter = explode('-', $data['daterangefilter']);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$retrieved = $this->report->fileExport($search, $dates[0], $dates[1], $partner, $filter, $bank, $sort, $mode);
		
		$header = array("Date","Voucher No.","Customer","Payment Details","Payment Date","Amount"); 
	
		$grandtotal 		=	0;
		$totalcashissued	=	0;
		$totalcashamount 	=	0;
		$totalreceiptissued =	0;
		$totalreceiptamt 	=	0;

		$prevdate 			=	$nextdate 	=	"";
		
		$csv  = "";
		$csv .= 'Collection Register';
		$csv .= "\n\n";
		$csv .= '"Date:","'.$strdate.'"';
		$csv .= "\n\n";
		$csv .= '"' . implode('","', $header) . '"';
		$csv .= "\n";
		
		if (!empty($retrieved)){
			foreach ($retrieved as $key => $row){

				$grandtotal 		+=	$row->amount;

				if( $row->payment == "CASH" ){
					$totalcashamount	+=	$row->amount;
					$totalcashissued 	+=	1;
				}

				$transactiondate 		=	$row->transactiondate;
				$prevdate 				=	$transactiondate;

				$csv .= ($prevdate != $nextdate) ?	'"' . $transactiondate . '",' : '"",';
				$csv .= '"' . $row->voucherno . '",';
				$csv .= '"' . $row->partnername . '",';
				$csv .= '"' . $row->payment . '",';
				$csv .= '"' . $row->paymentdate . '",';
				$csv .= '"' . number_format($row->amount,2) . '",';
				$csv .= "\n";

				$nextdate 	=	$prevdate;
			}

			$footerdtl 			= 	$this->report->getSalesTotal($search, $dates[0], $dates[1], $partner, $filter, $bank, $sort, $mode, "dated");
			$totaldatedissued 	=	isset($footerdtl->count) 		?	$footerdtl->count		:	0;
			$totaldatedamount 	=	isset($footerdtl->totalamount)	?	$footerdtl->totalamount : 	0;
			// var_dump($footerdtl);
			$footerdtl 			= 	$this->report->getSalesTotal($search, $dates[0], $dates[1], $partner, $filter, $bank, $sort, $mode, "pdc");
			$totalpdcissued 	=	isset($footerdtl->count) 		?	$footerdtl->count		:	0;
			$totalpdcamount 	=	isset($footerdtl->totalamount)	?	$footerdtl->totalamount : 	0;

			$totalreceiptissued =	$totalcashissued 	+	$totaldatedissued 	+	$totalpdcissued;
			$totalreceiptamt 	=	$totalcashamount 	+	$totaldatedamount 	+	$totalpdcamount;
			$grandtotal = array(
				'Grand Total','','','','',number_format($grandtotal, 2)
			);
			$csv .= '"' . implode('","', $grandtotal) . '"';
			$csv .= "\n\n";
			$csv .= "Summary";
			$csv .= "\n";
			$csv .= '"","Total Issued","Total Amount"';
			$csv .= "\n";
			$csv .= '"Cash","'.$totalcashissued.'","'.number_format($totalcashamount,2).'"';
			$csv .= "\n";
			$csv .= '"Dated Check","'.$totaldatedissued.'","'.number_format($totaldatedamount,2).'"';
			$csv .= "\n";
			$csv .= '"PDC","'.$totalpdcissued.'","'.number_format($totalpdcamount,2).'"';
			$csv .= "\n";
			$csv .= '"Total Provisional Receipt Issued","'.$totalreceiptissued.'","'.number_format($totalreceiptamt,2).'"';


			// return $csv;
		}

		return $csv;
	}
}