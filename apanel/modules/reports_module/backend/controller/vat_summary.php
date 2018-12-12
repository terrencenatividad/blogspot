<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->url 			   	 	= new url();
		$this->ui 					= new ui();
		$this->input 				= new input();
		$this->vat_summary 			= new vat_summary;
		$this->view->header_active 	= 'report/';
	}

	public function view() {
		$this->report_model = new report_model;
    	$this->report_model->generateBalanceTable();
		
		$daterange 	= $this->input->post('daterangefilter');
		$tab 		= $this->input->post('tab');

		$daterange 	= ($daterange) ? $daterange : date("M 1, Y")." - ".date("M t, Y");
		$tab 		= ($tab) ? $tab : 'output_tab';
		
		$this->view->title = 'VAT Summary';
		
		$data['ui'] 			= $this->ui;
		$data['daterange'] 		= $daterange;
		$data['tab'] 			= $tab;
		//$data['sales_view'] 	= $this->sales_view($daterange);
		//$data['purchase_view'] 	= $this->purchase_view($daterange);
		//$data['summary_view'] 	= $this->summary_view($daterange);
	
		$this->view->load('vat_summary', $data);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data		= $this->input->post(array('daterangefilter', 'filter'));
		$daterange 	= $data['daterangefilter'];

		$filter 	= $data['filter'];
		$result 		= '';
		
		if($filter == 'output_tab'){
			$result 		= $this->sales_view($daterange);
		}else if($filter == 'input_tab'){
			$result 		= $this->purchase_view($daterange);
		}
		//$pagination->table = $table;
		return $result;
	}

	private function sales_view($range) {
		$list = $this->vat_summary->getSales($range);
		$table = $this->generateTable($list, 7);
		return $table;
	}

	private function purchase_view($range) {
		$list = $this->vat_summary->getPurchase($range);
		$table = $this->generateTable($list, 7);
		return $table;
	}

	private function summary_view($range) {
		$list = $this->vat_summary->getSummary($range);
		$table = $this->generateSummary($list, 2);
		return $table;
	}

	private function generateTable($list, $colspan) {
		$pagination = new stdClass();
		$table = '';
		if (empty($list->rows)) {
			$table = '<tr><td colspan="'.$colspan.'" class="text-center"><b>No Records Found</b></td></tr>';
		} else {
			$total_gross= 0;
			$total_net 	= 0;
			$total_vat 	= 0;
			$prevkey  	= '';
			$nextkey 	= '';

			foreach ($list->rows as $key => $value) {
				$account 		= $value['account'];
				$grossamount 	= $value['amount'];
				$vatamount 		= $value['vatamount'];
				$partner 		= (!empty($value['partner'])) ? $value['partner'] : '-';
				$voucher 		= (!empty($value['voucher'])) ? $value['voucher'] : '-';
				$tin 			= (!empty($value['tin'])) ? $value['tin'] : '-';
				$address 		= (!empty($value['address'])) ? $value['address'] : '-';

				// $vatamount  	= $vat;
				// $netamount 		= $vatamount / 0.12;
				// $grossamount  	= ($vatamount / 0.12) + $vat;

				//$grossamount  	= $vat;
				$netamount 		= $grossamount - $vatamount;
				//$vatamount  	= $grossamount - $netamount;

				$prevkey = $account;
				if($prevkey != $nextkey)
				{
					if($nextkey != '')
					{
						$table .= '<tr class="warning bold">';
						$table .= '<td class="text-right" colspan="4">Total</td>';
						$table .= '<td>' . number_format($total_gross,2) . '</td>';
						$table .= '<td>' . number_format($total_net,2) . '</td>';
						$table .= '<td>';
						$table .= ($total_vat > -1) ? number_format($total_vat,2) : '('.number_format(abs($total_vat),2).')';
						$table .= '</td>';
						$table .= '</tr>';

						$total_gross= 0;
						$total_net 	= 0;
						$total_vat 	= 0;
					}

					$table .= '<tr class="danger bold"><td colspan="' . $colspan . '" class="text-left">' . $account . '</td></tr>';
				}

				$total_vat 		+= $vatamount;
				$total_gross 	+= $grossamount;
				$total_net 		+= $netamount;
				
				$table .= '<tr>';
				$table .= '<td class="text-left">' . $voucher . '</td>';
				$table .= '<td class="text-left">' . $partner . '</td>';
				$table .= '<td class="text-left">' . $tin . '</td>';
				$table .= '<td class="text-left">' . $address . '</td>';
				$table .= '<td class="text-right">' . number_format($grossamount,2) . '</td>';
				$table .= '<td class="text-right">' . number_format($netamount,2) . '</td>';
				$table .= '<td class="text-right">';
				$table .= ($vatamount > -1) ? number_format($vatamount,2) : '('.number_format(abs($vatamount),2).')';
				$table .= '</td>';
				$table .= '</tr>';
				

				$nextkey 		= $prevkey;
			}

			$table .= '<tr class="warning bold">';
			$table .= '<td class="text-right" colspan="4">Total</td>';
			$table .= '<td class="text-right">' . number_format($total_gross,2) . '</td>';
			$table .= '<td class="text-right">' . number_format($total_net,2) . '</td>';
			$table .= '<td class="text-right">';
			$table .= ($total_vat > -1) ? number_format($total_vat,2) : '('.number_format(abs($total_vat),2).')';
			$table .= '</td>';
			$table .= '</tr>';
			
			$table .= '<tr><td colspan="'.$colspan.'"></td></tr>';
		}
		//$table .= ($list->pagination) ? $list->pagination : '';
		$pagination->table = $table;
		$pagination->pagination = $list->pagination;
		return $pagination;
	}

	private function generateSummary($list, $colspan) {
		$table = '<table class="table table-hover table-striped report_table text-right table-bordered">';
		$table .= '<thead>';
		$table .= '	<tr>
						<th class="col-md-4">Account</th>
						<th class="col-md-8">Amount</th>
					</tr>';
		$table .= '</thead>';

		$table .= '<tbody>';
		
		if (empty($list)) {
			$table = '<tr><td colspan="'.$colspan.'" class="text-center"><b>No Records Found</b></td></tr>';
		} else {
			$total_output 	= 0;
			$total_input 	= 0;
			$total_vat 		= 0;

			foreach ($list->rows as $key => $value) {
				$account 		= $value['account'];
				$grossamount 	= $value['amount'];

				$netamount 		= $grossamount / 1.12;
				$vatamount  	= $grossamount - $netamount;

				$table .= '<tr>';
				$table .= '<td class="text-left">' . $account . '</td>';
				$table .= '<td class="text-right">';
				$table .= ($vatamount > -1) ? number_format($vatamount,2) : '('.number_format(abs($vatamount),2).')';
				$table .= '</td>';
				$table .= '</tr>';

				

				if (strpos($account, "Output") === FALSE) {
					$total_input 		+= $vatamount;
				}else{
					$total_output 		+= $vatamount;

					$table .= '<tr class="bold danger">';
					$table .= '<td class="text-right">Total Sales</td>';
					$table .= '<td class="text-right">';
					$table .= ($total_output > -1) ? number_format($total_output,2) : '('.number_format(abs($total_output),2).')';
					$table .= '</td>';
					$table .= '</tr>';
				}
			}

			$table .= '<tr class="bold danger">';
			$table .= '<td class="text-right">Total Purchase / Billing</td>';
			$table .= '<td class="text-right">';
			$table .= ($total_input > -1) ? number_format($total_input,2) : '('.number_format(abs($total_input),2).')';
			$table .= '</td>';
			$table .= '</tr>';

			$total_vat = $total_output - $total_input;
			$table .= '<tr class="warning bold">';
			$table .= '<td class="text-right">Net VAT Payable (Receivable)</td>';
			$table .= '<td>';
			$table .= ($total_vat > -1) ? number_format($total_vat,2) : '('.number_format(abs($total_vat),2).')';
			$table .= '</td>';
			$table .= '</tr>';
			
			$table .= '<tr><td colspan="'.$colspan.'"></td></tr>';
		}
		$table .= '</tbody>';
		$table .= '</table>';
		$table .= ($list->pagination) ? $list->pagination : '';
	
		return $table;
	}

	public function view_export($range) {
		$header = array(
			'Reference',
			'Partner',
			'TIN',
			'Address',
			'Gross Amount',
			'Net Amount',
			'Tax Amount'
		);

		$output_list 	= $this->vat_summary->getSales(urldecode($range));
		$input_list 	= $this->vat_summary->getPurchase(urldecode($range));
		$list = (object) array_merge_recursive((array) $output_list, (array) $input_list);
		
		$this->generateCSV($list, $header, urldecode($range));
	}

	private function generateCSV($list, $header, $range) {
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="VAT Summary.csv"');
		$total = array();
		$table = '';
		$table = 'VAT Summary';
		$table .= "\n\n";
		$table .= '"Date:","'.$range.'"';
		$table .= "\n\n";
		$table .= '"' . implode('", "', $header) . '"';
		$maindata = $list->rows;
		$prevname = '';
		$nextname = '';

		$total_output = 0;
		$total_input  = 0;

		foreach ($maindata as $key => $accounts) {
			$total = 0;
			$accountname 	= $accounts['account'];
			$voucher 		= $accounts['voucher'];
			$partner 		= $accounts['partner'];
			$transtype 		= $accounts['transtype'];
			$tin 			= $accounts['tin'];
			$grossamount 	= $accounts['amount'];
			$address 		= $accounts['address'];
			$vatamount		= $accounts['vatamount'];
			$prevname 		= $accountname;

			// $vatamount  	= $vat;
			// $netamount 		= $vatamount / 0.12;
			// $grossamount  	= ($vatamount / 0.12) + $vat;

			//$netamount 		= $grossamount / 1.12;
			//$vatamount  	= $grossamount - $netamount;
			$netamount  	= $grossamount - $vatamount;

			if (strpos($accountname, "Output") === FALSE) {
				$total_input 		+= $vatamount;
			}else{
				$total_output 		+= $vatamount;

				if($prevname != $nextname && $nextname != '')
				{
					$table .= "\n,,,,,";
					$table .= "\"Total Sales VAT\",";
					$table .= "\"".number_format($total_output,2)."\"";
				}
			}
			
			if($prevname != $nextname)
			{	
				if($nextname != ''){
					if (strpos($nextname, "Output") === FALSE) {
					
					}else{
						$table .= "\n,,,,,";
						$table .= "\"Total Sales VAT\",";
						$table .= "\"".number_format($total_output,2)."\"";
					}

					$table .= "\n";
				}
				$table .= "\n";
				$table .= "\"$accountname\",";
			}

			$table .= "\n";
			$table .= "\"$voucher\",";
			$table .= "\"$partner\",";
			$table .= "\"$tin\",";
			$table .= "\"$address\",";
			$table .= "\"".number_format($grossamount,2)."\",";
			$table .= "\"".number_format($netamount,2)."\",";
			$table .= "\"".number_format($vatamount,2)."\"";

			$nextname = $prevname;
		}

		$table .= "\n,,,,,";
		$table .= "\"Total Purchase VAT\",";
		$table .= "\"".number_format($total_input,2)."\"";
		
		$total 	= $total_output - $total_input;

		$table .= "\n";
		$table .= "\n,,,,,";
		$table .= "\"Net VAT Payable (Receivable)\",";
		$table .= "\"".number_format($total,2)."\"";
		
		echo $table;
	}
}