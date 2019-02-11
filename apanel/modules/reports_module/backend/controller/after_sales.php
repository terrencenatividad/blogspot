<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui					= new ui();
		$this->input				= new input();
		$this->after_sales			= new after_sales();
		$this->report_model			= new report_model();
		$this->view->header_active	= 'report/';
		$this->fields			= array(
			'am.id',
			'itemcode',
			'asset_class',
			'asset_name',
			'asset_number',
			'sub_number',
			'serial_number',
			'am.description',
			'asset_location',
			'department',
			'accountable_person',
			'commissioning_date',
			'retirement_date',
			'am.useful_life',
			'depreciation_month',
			'depreciation_amount',
			'capitalized_cost',
			'purchase_value',
			'balance_value',
			'am.salvage_value',
			'frequency_of_dep',
			'number_of_dep',
			'am.gl_asset',
			'am.gl_accdep',
			'am.gl_depexpense',
			'am.stat',
			'assetclass',
			'CONCAT(c.segment5," - ",c.accountname) asset',
			'CONCAT(o.segment5," - ",o.accountname) accdep',
			'CONCAT(a.segment5," - ",a.accountname) depexp',
			'cc.name'
		);
	}

	public function view() {
		$this->view->title			= 'After Sales Status Report';
		$data['ui']					= $this->ui;
		$data['datefilter'] 		= $this->date->datefilterMonth();
		$data['customer_list']		= $this->after_sales->getCustomers();	
		$this->view->load('after_sales', $data);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data_post = $this->input->post(array('daterangefilter','customer','voucher','status','sort'));
		
		$pagination		= $this->after_sales->getJOList($data_post);
		
		$tablerow = "";
		if(empty($pagination->result)){
			$tablerow .= '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
				if(!empty($pagination->result))
				{
					foreach ($pagination->result as $key => $row) 
					{		
						$stat = '';
					   if($row->stat == 'posted' || $row->stat == 'Paid'){
						   $stat  = 'COMPLETED';
					   }elseif($row->stat == 'Approved'){
							$stat  = 'FOR JO';						
					   }elseif($row->stat == 'Approved'){
						$stat  = 'FOR JO';						
					   }elseif($row->stat == 'Partial'){
						$stat  = 'PARTIAL SALES ORDER';						
				       }elseif($row->stat == 'completed'){
						$stat  = 'FOR INVOICING';						
				       }  
						  

						$tablerow	.= '<tr">';
						$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.date('M d, Y', strtotime($row->transactiondate)).'</td>';
						$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$row->service_quotation.'</td>';
						$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$row->job_order_no.'</td>';
						$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$row->si_goods.'</td>';
						$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$row->si_service.'</td>';
						$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$row->partnername.'</td>';
						$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$row->uom.'</td>';
						$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$row->serialno.'</td>';
						$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$stat.'</td>';
						$tablerow	.= '</tr>';
						
					}	
				}
		
		$pagination->table 	= $tablerow;
		$pagination->csv	= $this->export();
		return $pagination;
	}

	private function export()
	{
		$data 		= $this->input->post(array('daterangefilter','customer','voucher','status','sort'));
		$strdate	= $data['daterangefilter'];
		$datefilter = explode('-', $data['daterangefilter']);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$retrieved = $this->after_sales->fileExport($data);
		
		$header = array("Transaction Date","Service Quotation","Job Order No","Sales Invoice (Parts)","Sales Invoice (Services)","Customer","Unit","Serial No","Status");
		
		$csv 	= '';
		$csv 	.= 'After Sales Status Report';
		$csv 	.= "\n\n";
		$csv 	.= '"Date:","'.$strdate.'"';
		$csv 	.= "\n";
		$csv 	.= '"' . implode('","',$header).'"';
		$csv 	.= "\n";

		$filtered 	=	array_filter($retrieved);
		
		if (!empty($filtered)){
			
			foreach ($filtered as $key => $row){
				$stat = '';
				if($row->stat == 'posted' || $row->stat == 'Paid'){
					$stat  = 'COMPLETED';
				}elseif($row->stat == 'Approved'){
					 $stat  = 'FOR JO';						
				}elseif($row->stat == 'Approved'){
				 $stat  = 'FOR JO';						
				}elseif($row->stat == 'Partial'){
				 $stat  = 'PARTIAL SALES ORDER';						
				}elseif($row->stat == 'completed'){
				 $stat  = 'FOR INVOICING';						
				}  
				
				$csv .= '"'	.	date('M d, Y', strtotime($row->transactiondate))		.	'",';
				$csv .= '"'	. 	$row->service_quotation 								. 	'",';
				$csv .= '"'	. 	$row->job_order_no		 								. 	'",';
				$csv .= '"' . 	$row->si_goods 											. 	'",';
				$csv .= '"' .	$row->si_service 	 									. 	'",';
				$csv .= '"'	. 	$row->partnername 										. 	'",';
				$csv .= '"'	. 	$row->uom 												. 	'",';
				$csv .= '"'	. 	$row->serialno 											. 	'",';
				$csv .= '"' . 	$stat 													. 	'"';
				$csv .= "\n";

			}

			}

		return $csv;
	}


}

?>