<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui					= new ui();
		$this->input				= new input();
		$this->asset_list			= new asset_list();
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
		$this->view->title			= 'Asset Master List';
		$data['ui']					= $this->ui;
		$data['datefilter']			= date("M d, Y");
		$data['asset_list']			= $this->asset_list->getAsset();	
		$data['assetclass_list']	= $this->asset_list->getAssetClass();
		$data['dept_list']			= $this->asset_list->getAssetDepartment();	
		// $data['asd']				= $this->asset_list->getAssetMasterList($this->fields);
		$this->view->load('asset_list', $data);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$this->report_model->generateAssetActivity();
		$datefilter 	= $this->input->post('datefilter');
		$sort 			= $this->input->post('sort');
		$asset 			= $this->input->post('asset_number');
		$assetclass	= $this->input->post('assetclass');
		$department	= $this->input->post('department');
		$tab		= $this->input->post('tab');
		
		$pagination		= $this->asset_list->getAssetMasterList($this->fields, $sort, $asset, $datefilter, $assetclass, $department);

		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		$status = '';
		$datetoday = $this->date->dateFormat();
		$acc = 0;
		$totalcapitalizedcost = 0;
		$totaldepamount = 0;
		$totalaccdep = 0;
		$totalbookval = 0;
		foreach ($pagination->result as $key => $row) {
			$start =  date('M d, Y', strtotime($row->depreciation_month. '11:59:59'));
			$retdate =  date('M d, Y', strtotime($row->retirement_date. '11:59:59'));
			$date1 = new DateTime($start);
			$date2 = $date1->diff(new DateTime($datetoday));
			$dep   =  $date2->m;
			if($tab == 'Depreciation'){
				if($retdate > $datetoday){
					$status = 'Active';
				}else{
					$status = 'Retired';
				}
				$depreciation_amount = ($row->balance_value - $row->salvage_value)/$row->useful_life;
				$accumuluateddep = $depreciation_amount * $dep;
				$bookvalue 		 = $row->capitalized_cost - $accumuluateddep;
				$table .= '<tr>';
				$table .= '<td class="text-left">' . $row->asset_number . '</td>';
				$table .= '<td class="text-right">' . number_format($row->capitalized_cost, 2) . '</td>';
				$table .= '<td class="text-left">' . $this->date->dateFormat($row->commissioning_date) . '</td>';
				$table .= '<td class="text-left">' . $row->useful_life . '</td>';
				$table .= '<td class="text-right">' . number_format($depreciation_amount, 2) . '</td>';
				$table .= '<td class="text-left">' . $this->date->dateFormat($row->depreciation_month) . '</td>';
				$table .= '<td class="text-left">' . $this->date->dateFormat($row->retirement_date) . '</td>';
				$table .= '<td class="text-left">' . $status . '</td>';
				$table .= '<td class="text-right">' . number_format($accumuluateddep, 2) . '</td>';
				$table .= '<td class="text-right">' . number_format($bookvalue, 2) . '</td>';
				$table .= '</tr>';

				$totalcapitalizedcost += $row->capitalized_cost;
				$totaldepamount		  += $depreciation_amount;
				$totalaccdep		  += $accumuluateddep;
				$totalbookval		  += $bookvalue;
			}else{
				$table .= '<tr>';
				$table .= '<td class="text-left">' . $row->asset_number . '</td>';
				$table .= '<td class="text-left">' . $row->sub_number . '</td>';
				$table .= '<td class="text-left">' . $row->serial_number . '</td>';
				$table .= '<td class="text-left">' . $row->assetclass . '</td>';
				$table .= '<td class="text-left">' . $row->description . '</td>';
				$table .= '<td class="text-left">' . $row->asset_location . '</td>';
				$table .= '<td class="text-left">' . $row->name . '</td>';
				$table .= '<td class="text-left">' . $row->accountable_person . '</td>';
				$table .= '</tr>';
			}
			
			
		}

		$footer = '';
		if($tab == 'Depreciation'){
			$footer .= '<tr>';
			$footer .= '<td colspan="1" class="text-left"><b> Total:' . '</b></td>';
			$footer .= '<td colspan="1" class="text-right"><b> ' . number_format($totalcapitalizedcost, 2) . '</b></td>';
			$footer .= '<td colspan="3" class="text-right"><b> ' . number_format($totaldepamount, 2) . '</b></td>';
			$footer .= '<td colspan="4" class="text-right"><b> ' . number_format($totalaccdep, 2) . '</b></td>';
			$footer .= '<td colspan="1" class="text-right"><b> ' . number_format($totalbookval, 2) . '</b></td>';
			$footer .= '</tr>';
		}

		if ($pagination->page_limit > 1) {
			$footer .= '<tr>';
			$footer .= '<td colspan="10" class="text-center"><b>Page: ' . $pagination->page . ' of ' . $pagination->page_limit . '</b></td>';
			$footer .= '</tr>';
		}

		$pagination->table	= $table;
		$pagination->footer	= $footer;
		$pagination->csv	= $this->get_export();
		return $pagination;
	}

	private function get_export() {
		$datefilter	= $this->input->post('datefilter');
		$datefilter	= $this->date->dateDbFormat($datefilter);
		$sort		= $this->input->post('sort');
		$asset		= $this->input->post('asset_number');
		$assetclass	= $this->input->post('assetclass');
		$department	= $this->input->post('department');

		$result		= $this->asset_list->getAssetMasterListcsv($this->fields, $sort, $asset, $datefilter, $assetclass, $department);


		$header = array(
			'Asset Number',
			'Sub-number',
			'Serial Number/Engine Number',
			'Asset Class',
			'Description',
			'Asset Location',
			'Budget Center',
			'Accountable Person',
			'Capitalized Cost',
			'Commissioning Date',
			'No. of Months Useful Life',
			'Depreciation Amount / Month',
			'Depreciation Month Start',
			'Retirement Date',
			'Status',
			'Accumulated Depreciation',
			'Book Value',
		);

		$csv = '';
		$csv .= 'Asset Master List';
		$csv .= "\n\n";
		$csv .= '"Date:","' . $this->date->dateFormat($datefilter) . '"';
		$csv .= "\n\n";
		$csv .= '"' . implode('","', $header) . '"';
		if (empty($result)) {
			$csv .= 'No Records Found';
		}
		$status = '';
		$datetoday = $this->date->dateFormat();
		$acc = 0;
		$totalcapitalizedcost = 0;
		$totaldepamount = 0;
		$totalaccdep = 0;
		$totalbookval = 0;
		foreach ($result as $key => $row) {
			$start =  date('M d, Y', strtotime($row->depreciation_month. '11:59:59'));
			$retdate =  date('M d, Y', strtotime($row->retirement_date. '11:59:59'));
			$date1 = new DateTime($start);
			$date2 = $date1->diff(new DateTime($datetoday));
			$dep   =  $date2->m;
				if($retdate > $datetoday){
					$status = 'Retired';
				}else{
					$status = 'Active';
				}
				$depreciation_amount = ($row->balance_value - $row->salvage_value)/$row->useful_life;
				$accumuluateddep = $depreciation_amount * $dep;
				$bookvalue 		 = $row->capitalized_cost - $accumuluateddep;
				
				$csv .= "\n";
				$csv .= '"' . $row->asset_number . '",';
				$csv .= '"' . $row->sub_number . '",';
				$csv .= '"' . $row->serial_number . '",';
				$csv .= '"' . $row->assetclass . '",';
				$csv .= '"' . $row->description . '",';
				$csv .= '"' . $row->asset_location . '",';
				$csv .= '"' . $row->name . '",';
				$csv .= '"' . $row->accountable_person . '",';
				$csv .= '"' . $row->capitalized_cost . '",';
				$csv .= '"' . $row->commissioning_date . '",';
				$csv .= '"' . $row->useful_life . '",';
				$csv .= '"' . $depreciation_amount . '",';
				$csv .= '"' . $row->depreciation_month . '",';
				$csv .= '"' . $row->retirement_date . '",';
				$csv .= '"' . $status . '",';
				$csv .= '"' . $accumuluateddep . '",';
				$csv .= '"' . $bookvalue . '",';

				$totalcapitalizedcost += $row->capitalized_cost;
				$totaldepamount		  += $depreciation_amount;
				$totalaccdep		  += $accumuluateddep;
				$totalbookval		  += $bookvalue;
			

		}
		
		$csv .= "\n";
		$csv .= '":",';
		$csv .= '"",';
		$csv .= '"",';
		$csv .= '"",';
		$csv .= '"",';
		$csv .= '"",';
		$csv .= '"",';
		$csv .= '"Total:",';
		$csv .= '"' . number_format($totalcapitalizedcost, 2) . '",';
		$csv .= '"",';
		$csv .= '"",';		
		$csv .= '"' . number_format($totaldepamount, 2) . '",';
		$csv .= '"",';
		$csv .= '"",';
		$csv .= '"",';
		$csv .= '"' . number_format($totalaccdep, 2) . '",';
		$csv .= '"' . number_format($totalbookval, 2) . '",';
		
		return $csv;
	}


}

?>