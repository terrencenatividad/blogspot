<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui = new ui();
		$this->view->header_active = 'report/';
		$this->landed_cost 	    = new landed_cost();
		$this->input            = new input();
		// $this->log 				= new log();
		// $this->seq				= new seqcontrol();
		// $this->show_input 	    = true;
		// $this->session          = new session();
	}

	public function view() {
		$this->view->title 			= 	'Landed Cost Report';
		$this->report_model 		= 	new report_model;
        // $this->report_model->generateBalanceTable();
        
		$data['ui'] 				= 	$this->ui;
		// $data['show_input'] 		= 	true;
        $data['datefilter'] 		= 	$this->date->datefilterMonth();
        $data['supplier_list']	    =   $this->landed_cost->getSupplierList();
        $data['import_purchase_order_list']	    =   $this->landed_cost->getImportPurchaseOrderList();
        $this->view->load('landed_cost', $data);

		// $login						= 	$this->session->get('login');
		// $groupname 					= 	$login['groupname'];
		// $has_access 				= 	$this->trial_balance->retrieveAccess($groupname);
		// $has_close 					=	isset($has_access[0]->mod_close) 	?	$has_access[0]->mod_close	:	0;
	}

	public function ajax($task)
	{
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list()
	{
		$data = $this->input->post(array('daterangefilter','supplier','import_purchase_order'));
		$import_purchase_order	= $this->input->post('import_purchase_order');
		$supplier			= $this->input->post('supplier');

		// $datefilter			= $this->input->post('datefilter');
		// $datefilter			= $this->date->dateDbFormat($datefilter);
		$daterangefilter	= $data['daterangefilter'];
		// $default_datefilter = date("M d, Y",strtotime('first day of this month')).' - '.date("M d, Y",strtotime('last day of this month'));

		$date_filter = explode('-', $daterangefilter);
		foreach ($date_filter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$datefilterFrom = (!empty($dates[0]))? $dates[0] : "";
		$datefilterTo   = (!empty($dates[1]))? $dates[1] : "";
		$datefilter     = (!empty($daterangefilter))? $daterangefilter : $default_datefilter;
			// echo $datefilterFrom;
            // echo " - ".$datefilterTo; 

		$pagination = $this->landed_cost->getUnitCostLanded($datefilterFrom,$datefilterTo,$import_purchase_order,$supplier);
		
		$table = '';
		$addtl_cost = 0;
		$total_ipo_amt = 0;
		$unit_cost_landed = 0;
		// $foot  = '';

		if (empty($pagination->result)) {
			$table = '<tr><td colspan="12" class="text-center"><b>No Records Found</b></td></tr>';
		} else {
			foreach($pagination->result as $key => $row) {	

				// CALCULATE ADDITIONAL COST
			$freight_cost = $row->freight;
			$insurance_cost = $row->insurance;
			$packaging_cost = $row->packaging;
			$addtl_cost = $freight_cost + $insurance_cost + $packaging_cost;
				//TOTAL COST OF IMPORT PURCHASE ORDER
			$total_ipo_amt = $row->netamount;
				// CALCULATE UNIT COST
			$unit_cost_foreign = ( $total_ipo_amt / ($total_ipo_amt + $addtl_cost) ) * $addtl_cost ;
				// EXCHANGE RATES STAGING
			$exchange_curr = $row->exchangecurrency;
			$exchange_rate = $row->exchangerate;
			$unit_cost_base = $unit_cost_foreign * $exchange_rate;
			$base_curr = $row->basecurrency;

				// IPO FIELDS STAGING
			$item_code = $row->itemcode;
			$item_name = $row->itemname;
			$item_desc = $row->detailparticular;
			$voucher_no = $row->voucherno;
			$transaction_date = $row->transactiondate;
			$item_quantity = $row->receiptqty;
			$uom = $row->receiptuom;
			$job_no = $row->job_no;
			$receipt_date = $row->receiptdate;

			$table .= '<tr>
						<td class="text-right">'.$item_code.'- '.$item_name.'</td>
						<td class="text-right">'.$item_desc.'</td>
						<td class="text-right">'.$voucher_no.'</td>
						<td class="text-center">'.$transaction_date.'</td>
						<td class="text-center">'.$item_quantity.' '.$uom.'</td>
						
						<td class="text-center">'.$receipt_date.'</td>
						<td class="text-right"><span class="pull-left">'.$exchange_curr.'</span>'.number_format($unit_cost_foreign,2).'</td>
						<td class="text-right"><span class="pull-left">'.$base_curr.'</span>'.number_format($unit_cost_base,2).'</td>';

				// JOB FIELDS STAGING
			$job_quantity = $row->qty;
			$query_importation_cost = $this->landed_cost->getSumOfAp($job_no);
			$total_importation_cost = $query_importation_cost->debit;
			$query_job_item_count = $this->landed_cost->getTotalItemsInJob($job_no);
			$job_item_count = $query_job_item_count->qty;
			$importation_cost_unit =  floatval($total_importation_cost) / $job_item_count; //sprintf("%7.2f",$quantity);

			$table .=	'<td class="text-right"><span class="pull-left">'.$base_curr.'</span>'.number_format($importation_cost_unit,2).'</td>';
			
				// LANDED COST CALCS STAGING
			$landed_cost_unit = $unit_cost_base + $importation_cost_unit;
			$total_landed_cost = $landed_cost_unit * $item_quantity;

			$table .=	'<td class="text-right"><span class="pull-left">'.$base_curr.'</span>'.number_format($landed_cost_unit,2).'</td>
						<td class="text-right"><span class="pull-left">'.$base_curr.'</span>'.number_format($total_landed_cost,2).'</td>;

			$table.= </tr>';
			}	
		}

		// return array('table' => $table);
		$pagination->table = $table;
		// $pagination->csv   = $this->export();
		return $pagination;

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


}
?>