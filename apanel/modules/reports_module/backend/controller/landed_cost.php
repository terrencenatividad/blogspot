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
		$data['jobs']				= 	$this->landed_cost->getJobList();
		$data['item_list']			=	$this->landed_cost->getItemList();
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
		$data = $this->input->post(array('daterangefilter','supplier','import_purchase_order','job','itemcode'));
		$import_purchase_order	= $this->input->post('import_purchase_order');
		$supplier				= $this->input->post('supplier');
		$job 					= $this->input->post('job');
		$item					= $this->input->post('itemcode');
		// $tab 					= $data['tab'];
		// var_dump($data['tab']);
		// $datefilter			= $this->input->post('datefilter');
		// $datefilter			= $this->date->dateDbFormat($datefilter);
		$daterangefilter	= $data['daterangefilter'];
		// $default_datefilter = date("M d, Y",strtotime('first day of this month')).' - '.date("M d, Y",strtotime('last day of this month'));
		$datefilter     = (!empty($daterangefilter))? $daterangefilter : '';

		$date_filter = explode('-', $datefilter);
		foreach ($date_filter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$datefilterFrom = (!empty($dates[0]))? $dates[0] : "";
		$datefilterTo   = (!empty($dates[1]))? $dates[1] : "";
		// $datefilter     = (!empty($daterangefilter))? $daterangefilter : $default_datefilter;
			// echo $datefilterFrom;
			// echo " - ".$datefilterTo; 

		$pagination = $this->landed_cost->getUnitCostLanded($datefilterFrom,$datefilterTo,$import_purchase_order,$supplier,$job,$item);
		
		$table = '';
		$addtl_cost = 0;
		$total_ipo_amt = 0;
		$unit_cost_landed = 0;
		// $foot  = '';

		if (empty($pagination->result)) {
			$table = '<tr><td colspan="13" class="text-center"><b>No Records Found</b></td></tr>';
		} else {
			foreach($pagination->result as $key => $row) {	
			$item_code = $row->itemcode;
			$item_name = $row->itemname;
			$item_desc = $row->detailparticular;
			$ipo_no = $row->ipo_num;


			// GET ITEM COST
			$get_item = $this->landed_cost->getIPODetails($ipo_no, $item_code);
			$total_item_cost = $get_item->amount;
			$item_qty = $get_item->receiptqty;
			// CALCULATE ADDITIONAL COST
			$freight_cost = $row->freight;
			$insurance_cost = $row->insurance;
			$packaging_cost = $row->packaging;
			$addtl_cost = $freight_cost + $insurance_cost + $packaging_cost;

			//TOTAL COST OF IMPORT PURCHASE ORDER
			$total_ipo_amt = $row->netamount;
			$total_purchase_amount = ($total_ipo_amt - $addtl_cost);
			
			// CALCULATE UNIT COST
			$item_ipo_ratio = $total_item_cost / $total_purchase_amount;
			$unit_cost_foreign = ((($item_ipo_ratio*$addtl_cost) + $total_item_cost) / $item_qty );

			// EXCHANGE RATES STAGING
			$exchange_curr = $row->exchangecurrency;
			$exchange_rate = $row->exchangerate;
			$unit_cost_base = $unit_cost_foreign * $exchange_rate;
			$base_curr = $row->basecurrency;

			// OTHER FIELDS STAGING
			$transaction_date = $row->transactiondate;
			$transaction_date_display = (is_null($transaction_date)) ? '' : date('M d, Y',strtotime($transaction_date));
			$ipo_item_quantity = $row->receiptqty;
			$job_item_quantity = $row->qty;
			$uom = $row->receiptuom;
			$job_no = $row->job_no;
			$receipt_date = $row->receiptdate;
			$receipt_date_display = (is_null($receipt_date)) ? '' : date('M d, Y',strtotime($receipt_date));
			
			$table .= '<tr>
						<td class="text-right">'.$item_code.'- '.$item_name.'</td>
						<td class="text-right">'.$item_desc.'</td>
						<td class="text-right"><a href="'.BASE_URL."purchase/import_purchaseorder/view/" . $ipo_no . '" target="_blank"> '.$ipo_no.'</a></td>
						<td class="text-center">'.$transaction_date_display.'</td>
						<td class="text-center">'.$job_item_quantity.' '.$uom.'</td>
						
						<td class="text-center">'.$receipt_date_display.'</td>
						<td class="text-right"><span class="pull-left label label-default">'.$exchange_curr.'</span>'.number_format($unit_cost_foreign,2).'</td>
						<td class="text-right"><span class="pull-left label label-default">'.$base_curr.'</span>'.number_format($unit_cost_base,2).'</td>';

				// IMPORTATION COST CALCULATION
			$item_cost = $row->convertedamount / $ipo_item_quantity;
			$item_cost_total = $item_cost * $job_item_quantity; //total cost of item

			$query_job_item_count = $this->landed_cost->getTotalItemsInJob($job_no);
			$job_item_count = $query_job_item_count->qty; //number of items in job

			// $query_AP_debit = $this->landed_cost->getSumOfAp($job_no);
			// $query_CM_credit = $this->landed_cost->getSumOfCm($job_no);
			// $query_DM_debit = $this->landed_cost->getSumOfDm($job_no);

			// CALCULATE IMPORTATION COSTS
			// $ap_debit = $query_AP_debit->debit;
			// $cm_credit = $query_CM_credit->credit;
			// $dm_debit = $query_DM_debit->debit;
			// $total_importation_cost = $ap_debit + $dm_debit - $cm_credit; //importation cost/fees from AP,CM,DM
			$total_importation_cost_query = $this->landed_cost->getImportCost($job_no);
			$total_importation_cost = $total_importation_cost_query->import_cost;
			
			// CALCULATE TOTAL PURCHASE AMOUNT OF ALL IPO IN JOB
			$query_cost_job = $this->landed_cost->getTotalCostOfJob($job_no);
			$total_cost_job = $query_cost_job->total; //total cost of all items in job

			// CALCULATE RATIO FOR IMPORTATION COST
			$item_job_ratio = $total_item_cost / $total_cost_job; //ratio of item to all items in job

			// CALCULATE IMPORTATION COST
			$importation_cost_unit =  ($item_job_ratio * $total_importation_cost) / $job_item_quantity; //sprintf("%7.2f",$quantity);
			
			$table .=	'<td class="text-right"><a href="'.BASE_URL."purchase/job/view/" . $job_no . '" target="_blank">'.$job_no.'</a></td> 
						<td class="text-right"><span class="pull-left label label-default">'.$base_curr.'</span>'.number_format($importation_cost_unit,2).'</td>';
			
			// LANDED COST CALCS STAGING
			$landed_cost_unit = $unit_cost_base + $importation_cost_unit;
			$total_landed_cost = $landed_cost_unit * $job_item_quantity;
			$job_stat = $row->job_stat;
			
			if ($job_stat == 'closed') {
				$job_stat_display = '<span class="label label-success">'.strtoupper($job_stat).'</span>';
			} elseif ($job_stat == 'on-going') {
				$job_stat_display = '<span class="label label-warning">'.strtoupper($job_stat).'</span>';
			} else {
				$job_stat_display = '<span class="label label-danger">'.strtoupper($job_stat).'</span>';
			}

			$table .=	'<td class="text-right"><span class="pull-left label label-default">'.$base_curr.'</span>'.number_format($landed_cost_unit,2).'</td>
						<td class="text-right"><span class="pull-left label label-default">'.$base_curr.'</span>'.number_format($total_landed_cost,2).'</td>
						<td class="text-right"><span class="pull-left">'.$job_stat_display.'</td>;

			$table.= </tr>';
			}	
		}

		// return array('table' => $table);
		$pagination->table = $table;
		$pagination->csv   = $this->export();
		return $pagination;

	}

	private function export()
	{
		$data = $this->input->post(array('daterangefilter','supplier','import_purchase_order','job','itemcode'));
		$import_purchase_order	= $this->input->post('import_purchase_order');
		$supplier				= $this->input->post('supplier');
		$job 					= $this->input->post('job');
		$item					= $this->input->post('itemcode');
		
		$daterangefilter	= $data['daterangefilter'];
		// var_dump($daterangefilter);
		$datefilter     = (!empty($daterangefilter))? $daterangefilter : '';
		
		$date_filter = explode('-', $datefilter);
		foreach ($date_filter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$datefilterFrom = (!empty($dates[0]))? $dates[0] : "";
		$datefilterTo   = (!empty($dates[1]))? $dates[1] : "";

		$retrieved = $this->landed_cost->exportUnitCostLanded($datefilterFrom,$datefilterTo,$import_purchase_order,$supplier,$job,$item);
		
		$header	= array('Item','Description','IPO Number','IPO Date','Qty/Unit','IPO Receipt Date','Unit Cost Foreign Currency','Unit Cost Base Currency','Job Number','Importation Cost per Unit','Landed Cost per Unit','Total Landed Cost','Job Status');

		$import_purchase_order_export = "";
		($import_purchase_order == "") ? $import_purchase_order_export = "All" : $import_purchase_order_export = $import_purchase_order;
		$supplier_export = "";
		($supplier == "") ? $supplier_export = "All" : $supplier_export = $supplier;
		$job_export = "";
		($job == "") ? $job_export = "All" : $job_export = $job;
		$item_export = "";
		($item == "") ? $item_export = "All" : $item_export = $item;
		$date_export = "";
		($daterangefilter == "") ? $date_export = "" : $date_export = date('M d Y',strtotime($datefilterFrom)). ' - ' .date('M d Y',strtotime($datefilterTo)). '';;

		$csv 	= '';
		$csv 	.= 'Landed Cost Report';
		$csv 	.= "\n\n";
		$csv 	.= 'IPO: ' .$import_purchase_order_export. 
		',Supplier: ' .$supplier_export. 
		',Job: ' .$job_export. 
		',Item: ' .$item_export. 
		',Period: ' .$date_export;
		$csv 	.= "\n\n";
		$csv 	.= '"' . implode('","',$header).'"';
		$csv 	.= "\n";
		
		$filtered  = array_filter($retrieved);

		if (!empty($filtered)){
			foreach ($filtered as $key => $row){
			
				$item_code = $row->itemcode;
			$item_name = $row->itemname;
			$item_desc = $row->detailparticular;
			$ipo_no = $row->ipo_num;


			// GET ITEM COST
			$get_item = $this->landed_cost->getIPODetails($ipo_no, $item_code);
			$total_item_cost = $get_item->amount;
			$item_qty = $get_item->receiptqty;
			// CALCULATE ADDITIONAL COST
			$freight_cost = $row->freight;
			$insurance_cost = $row->insurance;
			$packaging_cost = $row->packaging;
			$addtl_cost = $freight_cost + $insurance_cost + $packaging_cost;

			//TOTAL COST OF IMPORT PURCHASE ORDER
			$total_ipo_amt = $row->netamount;
			$total_purchase_amount = ($total_ipo_amt - $addtl_cost);
			
			// CALCULATE UNIT COST
			$item_ipo_ratio = $total_item_cost / $total_purchase_amount;
			$unit_cost_foreign = ((($item_ipo_ratio*$addtl_cost) + $total_item_cost) / $item_qty ) ;

			// EXCHANGE RATES STAGING
			$exchange_curr = $row->exchangecurrency;
			$exchange_rate = $row->exchangerate;
			$unit_cost_base = $unit_cost_foreign * $exchange_rate;
			$base_curr = $row->basecurrency;

			// OTHER FIELDS STAGING
			$transaction_date = $row->transactiondate;
			$transaction_date_display = (is_null($transaction_date)) ? '' : date('M d, Y',strtotime($transaction_date));
			$ipo_item_quantity = $row->receiptqty;
			$job_item_quantity = $row->qty;
			$uom = $row->receiptuom;
			$job_no = $row->job_no;
			$receipt_date = $row->receiptdate;
			$receipt_date_display = (is_null($receipt_date)) ? '' : date('M d, Y',strtotime($receipt_date));
			

			$csv .= '"' .$item_code.'- '.$item_name. '",';
			$csv .= '"' .$item_desc. '",';
			$csv .= '"' .$ipo_no. '",';
			$csv .= '"' .$transaction_date_display. '",';
			$csv .= '"' .$job_item_quantity.' '.$uom. '",';
			$csv .= '"' .$receipt_date_display. '",';
			$csv .= '"' .$exchange_curr.' '.number_format($unit_cost_foreign,2). '",';
			$csv .= '"' .$base_curr.' '.number_format($unit_cost_base,2). '",';

			// IMPORTATION COST CALCULATION
			$item_cost = $row->convertedamount / $ipo_item_quantity;
			$item_cost_total = $item_cost * $job_item_quantity; //total cost of item

			$query_job_item_count = $this->landed_cost->getTotalItemsInJob($job_no);
			$job_item_count = $query_job_item_count->qty; //number of items in job

			$query_AP_debit = $this->landed_cost->getSumOfAp($job_no);
			$query_CM_credit = $this->landed_cost->getSumOfCm($job_no);
			$query_DM_debit = $this->landed_cost->getSumOfDm($job_no);

			// CALCULATE IMPORTATION COSTS
			$ap_debit = $query_AP_debit->debit;
			$cm_credit = $query_CM_credit->credit;
			$dm_debit = $query_DM_debit->debit;
			$total_importation_cost = $ap_debit + $dm_debit - $cm_credit; //importation cost/fees from AP,CM,DM
			// $total_importation_cost_query = $this->landed_cost->getImportCost($job_no);
			// $total_importation_cost = $total_importation_cost_query->import_cost;
			
			// CALCULATE TOTAL PURCHASE AMOUNT OF ALL IPO IN JOB
			$query_cost_job = $this->landed_cost->getTotalCostOfJob($job_no);
			$total_cost_job = $query_cost_job->total; //total cost of all items in job

			// CALCULATE RATIO FOR IMPORTATION COST
			$item_job_ratio = $total_item_cost / $total_cost_job; //ratio of item to all items in job

			// CALCULATE IMPORTATION COST
			$importation_cost_unit =  ($item_job_ratio * $total_importation_cost) / $job_item_quantity; //sprintf("%7.2f",$quantity);

			$csv .= '"' .$job_no. '",';
			$csv .= '"' .$base_curr.' '.number_format($importation_cost_unit,2). '",';
			
			// LANDED COST CALCS STAGING
			$landed_cost_unit = $unit_cost_base + $importation_cost_unit;
			$total_landed_cost = $landed_cost_unit * $job_item_quantity;
			$job_stat = $row->job_stat;

			$csv .= '"' .$base_curr.' '.number_format($landed_cost_unit,2). '",';
			$csv .= '"' .$base_curr.' '.number_format($total_landed_cost,2). '",';
			$csv .= '"'.$job_stat. '",';
			$csv .= "\n";
			}
		}

		return $csv;
	}


}
?>