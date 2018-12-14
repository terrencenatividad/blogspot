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
		$data = $this->input->post(array('daterangefilter','supplier','import_purchase_order','tab'));
		$import_purchase_order	= $this->input->post('import_purchase_order');
		$supplier			= $this->input->post('supplier');
		$tab = $data['tab'];
		// var_dump($data['tab']);
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
		// $datefilter     = (!empty($daterangefilter))? $daterangefilter : $default_datefilter;
			// echo $datefilterFrom;
			// echo " - ".$datefilterTo; 

		$pagination = $this->landed_cost->getUnitCostLanded($datefilterFrom,$datefilterTo,$import_purchase_order,$supplier,$tab);
		
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
			
			$table .=	'<td class="text-right">'.$job_no.'</td>	
						<td class="text-right"><span class="pull-left">'.$base_curr.'</span>'.number_format($importation_cost_unit,2).'</td>';
			
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
		$pagination->csv   = $this->export();
		return $pagination;

	}

	private function export()
	{
		$data = $this->input->post(array('daterangefilter','supplier','import_purchase_order','tab'));
		$import_purchase_order	= $this->input->post('import_purchase_order');
		$supplier			= $this->input->post('supplier');
		$tab = $data['tab'];
		
		$daterangefilter	= $data['daterangefilter'];
		
		$date_filter = explode('-', $daterangefilter);
		foreach ($date_filter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$datefilterFrom = (!empty($dates[0]))? $dates[0] : "";
		$datefilterTo   = (!empty($dates[1]))? $dates[1] : "";

		$retrieved = $this->landed_cost->exportUnitCostLanded($datefilterFrom,$datefilterTo,$import_purchase_order,$supplier,$tab);
		
		$header	= array('Item','Description','IPO Number','IPO Date','Qty/Unit','IPO Receipt Date','Unit Cost Foreign Currency','Unit Cost Base Currency','Job Number','Importation Cost per Unit','Landed Cost per Unit','Total Landed Cost');

		$import_purchase_order_export = "";
		($import_purchase_order == "") ? $import_purchase_order_export = "All" : $import_purchase_order_export = $import_purchase_order;
		$supplier_export = "";
		($supplier == "") ? $supplier_export = "All" : $supplier_export = $supplier;
		$tab_export = "";
		($tab == "") ? $tab_export = "All" : $tab_export = $supplier;

		$csv 	= '';
		$csv 	.= 'Landed Cost Report';
		$csv 	.= "\n\n";
		$csv 	.= 'IPO: ' .$import_purchase_order_export. ',Supplier: ' .$supplier_export. ',Period: ' .$datefilterFrom. '-' .$datefilterTo. '';
		$csv 	.= "\n";
		$csv 	.= 'Job: ' .$tab_export. '';
		$csv 	.= "\n\n";
		$csv 	.= '"' . implode('","',$header).'"';
		$csv 	.= "\n";
		
		$filtered  = array_filter($retrieved);

		if (!empty($filtered)){
			foreach ($filtered as $key => $row){
			
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

			$csv .= '"' .$item_code.'- '.$item_name. '",';
			$csv .= '"' .$item_desc. '",';
			$csv .= '"' .$voucher_no. '",';
			$csv .= '"' .$transaction_date. '",';
			$csv .= '"' .$item_quantity.' '.$uom. '",';
			$csv .= '"' .$receipt_date. '",';
			$csv .= '"' .$exchange_curr.' '.number_format($unit_cost_foreign,2). '",';
			$csv .= '"' .$base_curr.' '.number_format($unit_cost_base,2). '",';

				// JOB FIELDS STAGING
			$job_quantity = $row->qty;
			$query_importation_cost = $this->landed_cost->getSumOfAp($job_no);
			$total_importation_cost = $query_importation_cost->debit;
			$query_job_item_count = $this->landed_cost->getTotalItemsInJob($job_no);
			$job_item_count = $query_job_item_count->qty;
			$importation_cost_unit =  floatval($total_importation_cost) / $job_item_count; //sprintf("%7.2f",$quantity);

			$csv .= '"' .$job_no. '",';
			$csv .= '"' .$base_curr.' '.number_format($importation_cost_unit,2). '",';
			
				// LANDED COST CALCS STAGING
			$landed_cost_unit = $unit_cost_base + $importation_cost_unit;
			$total_landed_cost = $landed_cost_unit * $item_quantity;
			$csv .= '"' .$base_curr.' '.number_format($landed_cost_unit,2). '",';
			$csv .= '"' .$base_curr.' '.number_format($total_landed_cost,2). '",';
			$csv .= "\n";
			}
		}

		return $csv;
	}


}
?>