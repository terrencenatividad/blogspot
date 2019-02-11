<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui					= new ui();
		$this->input				= new input();
		$this->parts_and_service			= new parts_and_service();
		$this->report_model			= new report_model();
		$this->view->header_active	= 'report/';
	
	}

	public function view() {
		$this->view->title			= 'Detailed Parts and Service Sales Report';
		$data['ui']					= $this->ui;
		$data['datefilter'] 		= $this->date->datefilterMonth();
		$data['customer_list']		= $this->parts_and_service->getCustomers();	
		$this->view->load('parts_and_service', $data);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data_post = $this->input->post(array('daterangefilter','customer','voucher','sort'));
		
		$pagination		= $this->parts_and_service->getJOList($data_post);
		
		$tablerow = "";
		if(empty($pagination->result)){
			$tablerow .= '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
				if(!empty($pagination->result))
				{
					$partsamount = 0;
					$serviceamount = 0;
					$totalsales = 0;
					foreach ($pagination->result as $key => $row) 
					{		
						if($row->parts == ''){
							$row->parts = 0;
						}
						if($row->service == ''){
							$row->service = 0;
						}
						$tablerow	.= '<tr">';
						$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$row->partnername.'</td>';
						$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.date('M d, Y', strtotime($row->transactiondate)).'</td>';
						$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$row->service_quotation.'</td>';
						$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$row->po_number.'</td>';
						$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$row->si.'</td>';
						$tablerow	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;'.number_format($row->parts, 2).'</td>';
						$tablerow	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;'.number_format($row->service, 2).'</td>';
						$tablerow	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;'.number_format($row->parts + $row->service, 2).'</td>';
						$tablerow	.= '</tr>';
						$partsamount += $row->parts;
						$serviceamount += $row->service;
						$totalsales += $row->parts + $row->service;
					}	
					$tablerow	.= '<tr>';
					$tablerow	.= '<td class="text-right" colspan = "5"><strong> Total</strong></td>';					
					$tablerow	.= '<td class="text-right"><strong>'.number_format($partsamount, 2).'</strong></td>';					
					$tablerow	.= '<td class="text-right"><strong>'.number_format($serviceamount, 2).'</strong></td>';					
					$tablerow	.= '<td class="text-right"><strong>'.number_format($totalsales, 2).'</strong></td>';					
					$tablerow	.= '</tr>';
				}
		
		$pagination->table 	= $tablerow;
		$pagination->csv	= $this->export();
		return $pagination;
	}

	private function export()
	{
		$data 		= $this->input->post(array('daterangefilter','customer','voucher','sort'));
		$strdate	= $data['daterangefilter'];
		$datefilter = explode('-', $data['daterangefilter']);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$retrieved = $this->parts_and_service->fileExport($data);
		
		$header = array("Customer","Transaction Date","Service Quotation","Customer Purchase Order No","Sales Invoice","Parts Total Amount","Service Total Amount","Total Sales");
		
		$csv 	= '';
		$csv 	.= 'Detailed Parts and Service Sales Report';
		$csv 	.= "\n\n";
		$csv 	.= '"Date:","'.$strdate.'"';
		$csv 	.= "\n";
		$csv 	.= '"' . implode('","',$header).'"';
		$csv 	.= "\n";

		$filtered 	=	array_filter($retrieved);
		
		if (!empty($filtered)){

			$partsamount = 0;
			$serviceamount = 0;
			$totalsales = 0;
			foreach ($filtered as $key => $row){
				if($row->parts == ''){
					$row->parts = 0;
				}
				if($row->service == ''){
					$row->service = 0;
				}
				$csv .= '"'	. 	$row->partnername 										. 	'",';
				$csv .= '"'	.	date('M d, Y', strtotime($row->transactiondate))		.	'",';
				$csv .= '"'	. 	$row->service_quotation 								. 	'",';
				$csv .= '"'	. 	$row->po_number		 									. 	'",';
				$csv .= '"' . 	$row->si	 											. 	'",';
				$csv .= '"' .	number_format($row->parts, 2) 	 						. 	'",';
				$csv .= '"'	. 	number_format($row->service, 2) 						. 	'",';
				$csv .= '"'	. 	number_format($row->parts + $row->service, 2)			. 	'"';
				$csv .= "\n";
				$partsamount += $row->parts;
				$serviceamount += $row->service;
				$totalsales += $row->parts + $row->service;
			}
			$csv .= '"'	. '' . 	'",';
			$csv .= '"'	. '' . 	'",';
			$csv .= '"'	. '' . 	'",';
			$csv .= '"'	. '' . 	'",';
			$csv .= '"'	. 'Total' . 	'",';
			$csv .= '"'	. number_format($partsamount, 2) . 	'",';
			$csv .= '"'	. number_format($serviceamount, 2) . 	'",';
			$csv .= '"'	. number_format($totalsales, 2) . 	'",';
			
			}

		return $csv;
	}


}

?>