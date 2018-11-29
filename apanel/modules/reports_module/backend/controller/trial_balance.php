<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui = new ui();
		$this->view->header_active = 'report/';
		$this->trial_balance 	= new trial_balance();
		$this->input            = new input();
		$this->log 				= new log();
		$this->seq				= new seqcontrol();
		$this->show_input 	    = true;
		$this->session          = new session();
	}

	public function view() {
		$this->view->title 			= 	'Trial Balance';
		$this->report_model 		= 	new report_model;
		$this->report_model->generateBalanceTable();

		$account 					=	$this->trial_balance->retrieveAccount("IS");
		$data['is_account']			=	isset($account->salesAccount) 	?	$account->salesAccount 	:	"";
		$data['chart_account_list'] =	$this->trial_balance->getChartAccountList();
		$data['proforma_list'] 		= 	$this->trial_balance->getProformaList();

		$year_result 				=  	$this->trial_balance->getYearforClosing();
		$year_closing 				=	isset($year_result->year)		?	$year_result->year	:	"";	
		$period_result 				=	$this->trial_balance->getClosingMonth($year_closing);
		$ret_year  			 		=	isset($period_result->year) 	?	$period_result->year 	:	"";	 			
		$ret_month 					=	isset($period_result->month) 	?	$period_result->month 	:	"";

		$last_date 					= 	$ret_year."-".$ret_month."-1";
		$complete_date 				= 	$this->trial_balance->getMonthEnd($last_date);
		$complete_date 				=	$this->date->dateFormat($complete_date);

		$data['datafrom'] 			=	$complete_date;

		$data['ui'] 				= 	$this->ui;
		$data['show_input'] 		= 	true;
		$data['datefilter'] 		= 	$this->date->datefilterMonth();

		$login						= 	$this->session->get('login');
		$groupname 					= 	$login['groupname'];
		$has_access 				= 	$this->trial_balance->retrieveAccess($groupname);
		$has_close 					=	isset($has_access[0]->mod_close) 	?	$has_access[0]->mod_close	:	0;

		$balance_table 				= 	$this->trial_balance->getBalanceTableCount();
		$display_button 			=	($balance_table->count > 0 && $has_close) 	? 	1	:	0;
		$data['display_btn'] 		=	$display_button;
		$this->view->load('trial_balance', $data);
	}

	public function ajax($task)
	{
		header('Content-type: application/json');
		$result 	=	"";

		if($task == 'list'){
			$result = $this->ajax_list();
		}else if($task == 'load_account_transactions'){
			$result = $this->load_account_transactions();
		}else if($task == 'export'){
			$result = $this->export();
		}else if($task == 'check_existing_jv'){
			$result = $this->check_existing_jv();
		}else if($task == "temporary_jv_close"){
			$result = $this->temporary_jv_close();
		}else if($task == "preview_listing" ){
			$result = $this->preview_listing();
		}else if($task == "close_jv_status"){
			$result = $this->close_jv_status();
		}else if($task == "eradicate_temporary_jv"){
			$result = $this->eradicate_temporary_jv();
		}

		echo json_encode($result); 
	}

	private function ajax_list()
	{
		$data = $this->input->post(array('daterangefilter','limit'));
		$daterangefilter	= $data['daterangefilter'];
		$default_datefilter = date("M d, Y",strtotime('first day of this month')).' - '.date("M d, Y",strtotime('last day of this month'));

		$date_filter = explode('-', $daterangefilter);
		foreach ($date_filter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$datefilterFrom = (!empty($dates[0]))? $dates[0] : "";
		$datefilterTo   = (!empty($dates[1]))? $dates[1] : "";
		$datefilter     = (!empty($daterangefilter))? $daterangefilter : $default_datefilter;
			// echo $datefilterFrom;
			// echo " - ".$datefilterTo; 
		$currentyear 	= date("Y",strtotime($datefilterTo));
		$prevyear 		= date("Y",strtotime($datefilterFrom." -1 year"));

		$pagination = $this->trial_balance->getTrialBalance($currentyear,$prevyear);
		$table = '';
		$foot  = '';
		$totaldebit 	= 0;
		$totalcredit 	= 0;
		$totalprevcarry = 0;
		$totalbalcarry  = 0;
		$totalperiodbalance = 0;
		$totalaccumulatedbalance = 0;
		
		foreach($pagination as $row){
				$accountid          = $row->accountid;
				$accountcode		= $row->accountcode;
				$accountname		= $row->accountname;	

				$prevcarry 			= $this->trial_balance->getPrevCarry($accountid,$datefilterFrom);
				$balcarry			= $this->trial_balance->getBalanceCarry($accountid,$datefilterFrom,$datefilterTo);
				$amount				= $this->trial_balance->getCurrent($accountid,$datefilterFrom,$datefilterTo);

				$debit 				= ($amount > 0) ? $amount : 0;
				$credit 			= ($amount < 0) ? abs($amount) : 0;
				$periodbalance      = $amount;

				$accumulatedbalance = $balcarry + $periodbalance;

				$totalprevcarry 		+= $prevcarry;
				$totalbalcarry  		+= $balcarry;
				$totaldebit 			+= $debit;
				$totalcredit 			+= $credit;
				$totalperiodbalance 	+= $periodbalance;
				$totalaccumulatedbalance += $accumulatedbalance;

				$periodbalance 		= ($periodbalance < 0) ? '('.number_format(abs($periodbalance),2).')' : number_format(abs($periodbalance),2);
				$balcarry 			= ($balcarry < 0) ? '('.number_format(abs($balcarry),2).')' : number_format(abs($balcarry),2);
				$credit 			= ($credit < 0) ? '('.number_format(abs($credit),2).')' : number_format(abs($credit),2);
				$debit 				= ($debit < 0) ? '('.number_format(abs($debit),2).')' : number_format(abs($debit),2);
				$prevcarry 			= ($prevcarry < 0) ? '('.number_format(abs($prevcarry),2).')' : number_format(abs($prevcarry),2);
				$accumulatedbalance = ($accumulatedbalance < 0) ? '('.number_format(abs($accumulatedbalance),2).')' : number_format(abs($accumulatedbalance),2);

				$debitLink	= ($amount > 0) ? '<a href="javascript:void(0);" onClick="openList(\''.$accountid.'\');" >'.$debit.'</a>' : $debit;
				$creditLink	= ($amount < 0) ? '<a href="javascript:void(0);" onClick="openList(\''.$accountid.'\');" >'.$credit.'</a>' : $credit;
				
				$table .= "<tr>";																					
				$table .= '<td class="left">&nbsp;'.$accountcode.'</td>';
				$table .= '<td class="left">&nbsp;'.$accountname.'</td>';
				$table .= '<td class="text-right" >'.$prevcarry.'</td>';
				$table .= '<td class="text-right" >'.$balcarry.'</td>';
				$table .= '<td class="text-right" >'.$debitLink.'</td>';
				$table .= '<td class="text-right" >'.$creditLink.'</td>';
				$table .= '<td class="text-right" >'.$periodbalance.'</td>';
				$table .= '<td class="text-right" >'.$accumulatedbalance.'</td>';
				$table .= '</tr>';
		}
			$totaldebit 				= ($totaldebit < 0) ? '('.number_format(abs($totaldebit),2).')' : number_format(abs($totaldebit),2);
			$totalcredit 				= ($totalcredit < 0) ? '('.number_format(abs($totalcredit),2).')' : number_format(abs($totalcredit),2);
			$totalperiodbalance 		= ($totalperiodbalance < 0) ? '('.number_format(abs($totalperiodbalance),2).')' : number_format(abs($totalperiodbalance),2);
			$totalaccumulatedbalance 	= ($totalaccumulatedbalance < 0) ? '('.number_format(abs($totalaccumulatedbalance),2).')' : number_format(abs($totalaccumulatedbalance),2);
			$totalprevcarry 			= ($totalprevcarry < 0) ? '('.number_format(abs($totalprevcarry),2).')' : number_format(abs($totalprevcarry),2);
			$totalbalcarry 				= ($totalbalcarry < 0) ? '('.number_format(abs($totalbalcarry),2).')' : number_format(abs($totalbalcarry),2);

			$table .= '<tr class="info">
						<td class="text-right" colspan="2"><strong>TOTAL</strong></td>
						<td class="text-right">'.$totalprevcarry.'</td>
						<td class="text-right">'.$totalbalcarry.'</td>
						<td class="text-right"><strong>'.$totaldebit.'</strong></td>
						<td class="text-right"><strong>'.$totalcredit.'</strong></td>
						<td class="text-right">'.$totalperiodbalance.'</td>
						<td class="text-right">'.$totalaccumulatedbalance.'</td>';

			$table.= '</tr>';
		
		// if(count($pagination->result)>0)
		// {
		// 	for($i=0;$i<count($pagination->result);$i++)
		// 	{	
		// 		$accountid          = $pagination->result[$i]->accountid;
		// 		$accountcode		= $pagination->result[$i]->accountcode;
		// 		$accountname		= $pagination->result[$i]->accountname;	

		// 		$prevcarry 			= $this->trial_balance->getPrevCarry($accountid,$datefilterFrom);
		// 		$balcarry			= $this->trial_balance->getBalanceCarry($accountid,$datefilterFrom,$datefilterTo);
		// 		$amount				= $this->trial_balance->getCurrent($accountid,$datefilterFrom,$datefilterTo);

		// 		$debit 				= ($amount > 0) ? $amount : 0;
		// 		$credit 			= ($amount < 0) ? abs($amount) : 0;
		// 		$periodbalance      = $amount;

		// 		$accumulatedbalance = $balcarry + $periodbalance;

		// 		$totalprevcarry 		+= $prevcarry;
		// 		$totalbalcarry  		+= $balcarry;
		// 		$totaldebit 			+= $debit;
		// 		$totalcredit 			+= $credit;
		// 		$totalperiodbalance 	+= $periodbalance;
		// 		$totalaccumulatedbalance += $accumulatedbalance;

		// 		$periodbalance 		= ($periodbalance < 0) ? '('.number_format(abs($periodbalance),2).')' : number_format(abs($periodbalance),2);
		// 		$balcarry 			= ($balcarry < 0) ? '('.number_format(abs($balcarry),2).')' : number_format(abs($balcarry),2);
		// 		$credit 			= ($credit < 0) ? '('.number_format(abs($credit),2).')' : number_format(abs($credit),2);
		// 		$debit 				= ($debit < 0) ? '('.number_format(abs($debit),2).')' : number_format(abs($debit),2);
		// 		$prevcarry 			= ($prevcarry < 0) ? '('.number_format(abs($prevcarry),2).')' : number_format(abs($prevcarry),2);
		// 		$accumulatedbalance = ($accumulatedbalance < 0) ? '('.number_format(abs($accumulatedbalance),2).')' : number_format(abs($accumulatedbalance),2);

		// 		$debitLink	= ($amount > 0) ? '<a href="javascript:void(0);" onClick="openList(\''.$accountid.'\');" >'.$debit.'</a>' : $debit;
		// 		$creditLink	= ($amount < 0) ? '<a href="javascript:void(0);" onClick="openList(\''.$accountid.'\');" >'.$credit.'</a>' : $credit;
				
		// 		$table .= "<tr>";																					
		// 		$table .= '<td class="left">&nbsp;'.$accountcode.'</td>';
		// 		$table .= '<td class="left">&nbsp;'.$accountname.'</td>';
		// 		$table .= '<td class="text-right" >'.$prevcarry.'</td>';
		// 		$table .= '<td class="text-right" >'.$balcarry.'</td>';
		// 		$table .= '<td class="text-right" >'.$debitLink.'</td>';
		// 		$table .= '<td class="text-right" >'.$creditLink.'</td>';
		// 		$table .= '<td class="text-right" >'.$periodbalance.'</td>';
		// 		$table .= '<td class="text-right" >'.$accumulatedbalance.'</td>';
		// 		$table .= '</tr>';
		// 	}	
			
		// 	$totaldebit 				= ($totaldebit < 0) ? '('.number_format(abs($totaldebit),2).')' : number_format(abs($totaldebit),2);
		// 	$totalcredit 				= ($totalcredit < 0) ? '('.number_format(abs($totalcredit),2).')' : number_format(abs($totalcredit),2);
		// 	$totalperiodbalance 		= ($totalperiodbalance < 0) ? '('.number_format(abs($totalperiodbalance),2).')' : number_format(abs($totalperiodbalance),2);
		// 	$totalaccumulatedbalance 	= ($totalaccumulatedbalance < 0) ? '('.number_format(abs($totalaccumulatedbalance),2).')' : number_format(abs($totalaccumulatedbalance),2);
		// 	$totalprevcarry 			= ($totalprevcarry < 0) ? '('.number_format(abs($totalprevcarry),2).')' : number_format(abs($totalprevcarry),2);
		// 	$totalbalcarry 				= ($totalbalcarry < 0) ? '('.number_format(abs($totalbalcarry),2).')' : number_format(abs($totalbalcarry),2);

		// 	$table .= '<tr class="info">
		// 				<td class="text-right" colspan="2"><strong>TOTAL</strong></td>
		// 				<td class="text-right">'.$totalprevcarry.'</td>
		// 				<td class="text-right">'.$totalbalcarry.'</td>
		// 				<td class="text-right"><strong>'.$totaldebit.'</strong></td>
		// 				<td class="text-right"><strong>'.$totalcredit.'</strong></td>
		// 				<td class="text-right">'.$totalperiodbalance.'</td>
		// 				<td class="text-right">'.$totalaccumulatedbalance.'</td>';

		// 	$table.= '</tr>';

		// }else{
		// 	$table .= '<tr><td colspan="8" class="text-center"><b>No Records Found</b></td></tr>';
		// }

		// $pagination->table = $table;
		// $pagination->csv   = $this->export();
		return array('table' => $table);

	}

	private function export()
	{
		$data 		= $this->input->post(array('daterangefilter'));
		
		$data['daterangefilter'] = str_replace(array('%2C', '+'), array(',', ' '), $data['daterangefilter']);
		
		$datefilter	= $data['daterangefilter'];	
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		
		$default_datefilter = date("M d, Y",strtotime('first day of this month')).' - '.date("M d, Y",strtotime('last day of this month'));		
		$datefilterFrom = (!empty($dates[0]))? $dates[0] : "";
		$datefilterTo   = (!empty($dates[1]))? $dates[1] : "";
		$datefilter     = (!empty($daterangefilter))? $daterangefilter : $default_datefilter;

		$currentyear 	= date("Y",strtotime($datefilterTo));
		$prevyear 		= date("Y",strtotime($datefilterFrom." -1 year"));

		$totaldebit 	= 0;
		$totalcredit 	= 0;
		$totalperiodbalance = 0;
		$totalaccumulatedbalance = 0;
		$retrieved = $this->trial_balance->retrieveCOAdetails($currentyear,$prevyear);
		
		$header		= array('Account Code','Account Name','Prev Carryforward','Balance Carryforward','Total Debit','Total Credit','Balance for the Period','Accumulated Balance');

		$csv 	= '';
		$csv 	.= 'Trial Balance';
		$csv 	.= "\n\n";
		$csv 	.= '"' . implode('","',$header).'"';
		$csv 	.= "\n";

		$filtered 	=	array_filter($retrieved);

		if (!empty($filtered)){
			foreach ($filtered as $key => $row){
				$accountid 			= 	$row->accountid;
				$accountcode  		=	$row->accountcode;
				$accountname  		=	$row->accountname;
				
				$prevcarry 			= $this->trial_balance->getPrevCarry($accountid,$datefilterFrom);
				$balcarry			= $this->trial_balance->getBalanceCarry($accountid,$datefilterFrom,$datefilterTo);
				$amount				= $this->trial_balance->getCurrent($accountid,$datefilterFrom,$datefilterTo);

				$debit 				= ($amount > 0) ? $amount : 0;
				$credit 			= ($amount < 0) ? abs($amount) : 0;
				$periodbalance      = $amount;

				$accumulatedbalance = $balcarry + $periodbalance;
					
				$totaldebit 				+= $debit;
				$totalcredit 				+= $credit;
				$totalperiodbalance 		+= $periodbalance;
				$totalaccumulatedbalance 	+= $accumulatedbalance;

				$periodbalance 		= ($periodbalance < 0) ? '('.number_format(abs($periodbalance),2).')' : number_format(abs($periodbalance),2);
				$balcarry 			= ($balcarry < 0) ? '('.number_format(abs($balcarry),2).')' : number_format(abs($balcarry),2);
				$credit 			= ($credit < 0) ? '('.number_format(abs($credit),2).')' : number_format(abs($credit),2);
				$debit 				= ($debit < 0) ? '('.number_format(abs($debit),2).')' : number_format(abs($debit),2);
				$prevcarry 			= ($prevcarry < 0) ? '('.number_format(abs($prevcarry),2).')' : number_format(abs($prevcarry),2);
				$accumulatedbalance = ($accumulatedbalance < 0) ? '('.number_format(abs($accumulatedbalance),2).')' : number_format(abs($accumulatedbalance),2);

				$debitLink	= $debit;
				$creditLink	= $credit;

					$csv .= '"' . $accountcode . '",';
					$csv .= '"' . $accountname . '",';
					$csv .= '"' . $prevcarry . '",';
					$csv .= '"' . $balcarry . '",';
					$csv .= '"' . $debitLink . '",';
					$csv .= '"' . $creditLink . '",';
					$csv .= '"' . $periodbalance . '",';
					$csv .= '"' . $accumulatedbalance . '"';
					$csv .= "\n";		
				}
				
			$totaldebit 				= ($totaldebit < 0) ? '('.number_format(abs($totaldebit),2).')' : number_format(abs($totaldebit),2);
			$totalcredit 				= ($totalcredit < 0) ? '('.number_format(abs($totalcredit),2).')' : number_format(abs($totalcredit),2);
			$totalperiodbalance 		= ($totalperiodbalance < 0) ? '('.number_format(abs($totalperiodbalance),2).')' : number_format(abs($totalperiodbalance),2);
			$totalaccumulatedbalance 	= ($totalaccumulatedbalance < 0) ? '('.number_format(abs($totalaccumulatedbalance),2).')' : number_format(abs($totalaccumulatedbalance),2);
			
			$csv .= '"","","","","' . $totaldebit . '","' . $totalcredit . '","' . $totalperiodbalance . '","' . $totalaccumulatedbalance . '"';
			$csv .= "\n";
		}

		return $csv;
	}

	private function load_account_transactions(){
		$data = $this->input->post(array('accountcode', 'daterangefilter','items'));
		$result = $this->trial_balance->load_account_transactions($data);
		return array(
			'title' => $result["title"],
			'qtr'   => $result["qtr"],
			'table' => $result["table"],
			'pagination' => $result["pagination"],
			'accountfilter' => $result["accountfilter"]
		);
	}

	private function check_existing_jv(){
		$transaction_date  	=	$this->input->post('trans_date');
		
		$result = $this->trial_balance->check_existing_jv($transaction_date);

		$existing 	=	0;
		if( !empty($result) ){
			$existing 	=	1;
		}

		return array(
			'existing' => $existing
		);
	}

	private function temporary_jv_close(){

		$data 			= 	$this->input->post(array('datefrom','reference','notes','closing_account'));
		$datefrom 		=	$data['datefrom'];
		$datefrom 		=	date("Y-m-d", strtotime($datefrom));

		$account 		=	isset($data['closing_account']) 	?	$data['closing_account'] 	:	"";

		$result 		= 	0;

		$gen_value      = $this->trial_balance->getValue("journalvoucher", "COUNT(*) as count", "voucherno != ''");
		$temporary_id   = (!empty($gen_value[0]->count)) ? 'TMP_'.($gen_value[0]->count + 1) : 'TMP_1';

		$data['datefrom'] 			=	$datefrom;
		$data['voucher'] 			=	$temporary_id; 
		$data['closing_account'] 	=	$account;

		$result 			=	$this->trial_balance->save_journal_voucher($data);
		
		$dataArray 		=	array( "result" =>	$result, 'voucherno' => $temporary_id);

		return $dataArray;
	}

	private function preview_listing(){
		$voucherno 	=	$this->input->post('voucherno');
		$search 	= 	$this->input->post('search');
		$limit 		= 	$this->input->post('limit');

		$header 	=	$this->trial_balance->getJVHeader($voucherno);
		$details 	= 	$this->trial_balance->getJVDetails($voucherno, $search, $limit);

		$totalcredit 	=	0;
		$totaldebit 	=	0;
		$table 			=	"";
		// var_dump($details);
		if(count($details->result)>0){
			for($i=0;$i<count($details->result);$i++){	
				$linenum          	= $details->result[$i]->linenum;
				$accountid          = $details->result[$i]->accountcode;
				$accountname 		= $details->result[$i]->accountname;
				$detailparticulars  = $details->result[$i]->detailparticulars;
				$debit          	= $details->result[$i]->debit;
				$credit				= $details->result[$i]->credit;

				$table .= '<tr>
							<td class="text-left">'.$accountname.'</td>
							<td class="text-left">'.$detailparticulars.'</td>
							<td class="text-right"><strong>'.number_format($debit,2).'</strong></td>
							<td class="text-right"><strong>'.number_format($credit,2).'</strong></td>';
				$table.= '</tr>';

				$totaldebit 		+=	$debit;
				$totalcredit 		+=	$credit;
			}
			
			if($totaldebit != 0 || $totalcredit != 0){
				$table .= '<tr>
							<td colspan="2"></td>
							<td class="text-right"><strong>'.number_format($totaldebit,2).'</strong></td>
							<td class="text-right"><strong>'.number_format($totalcredit,2).'</strong></td>';
				$table.= '</tr>';
			} else {
				$table .= '<tr>
								<td class="text-center" colspan="4"><b>No Entries for this Period.<b></td>';
				$table.= '</tr>';
			}
		}else{
			$table .= '<tr><td colspan="4" class="text-center"><b>No Entries for this Period.</b></td></tr>';
		}

		$dataArray 	=	array( "table" 				=>	$table,
							   "voucherno"			=>	$voucherno,	
							   "transactiondate"	=>	date("M d, Y",strtotime($header->transactiondate)),
							   "proformacode" 		=>	$header->proformacode,
							   "reference" 			=>	$header->referenceno,
							   "remarks" 			=>	$header->remarks,
							   "pagination" 		=> 	$details->pagination
							 );

		return $dataArray;
	}

	private function close_jv_status(){
		$temp 		=	$this->input->post('voucherno');
		$voucherno 	=	$this->seq->getValue("JV");
		// var_dump($reference);
		$result 	=	$this->trial_balance->update_jv_status($temp,$voucherno);

		if( $result ){
			$ret_arr 	=	$this->trial_balance->getReference($voucherno);
			$reference 	=	$ret_arr->referenceno;

			$this->log->saveActivity("Closed Book [$voucherno - $reference] ");
		}

		return $result;
	}

	public function eradicate_temporary_jv(){
		$voucherno 	=	$this->input->post('voucherno');
		
		$result 	=	$this->trial_balance->delete_temporary_jv($voucherno);

		$dataArray 	= 	array("result"=>$result);
		return $dataArray;
	}
}
?>