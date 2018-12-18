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
		$data['asd']				= $this->asset_list->getAssetMasterListPagination($this->fields);
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
		
		$pagination		= $this->asset_list->getAssetMasterListPagination($this->fields);
		$tt = '';
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			
			$table .= '<tr>';
			$table .= '<td>' . $row->itemcode . '</td>';
			$table .= '<td>' . $row->assetclass . '</td>';
			$table .= '<td>' . $row->asset_name . '</td>';
			$table .= '<td>' . $row->asset_number . '</td>';
			$table .= '<td>' . $row->sub_number . '</td>';
			$table .= '<td>' . $row->serial_number . '</td>';
			$table .= '<td>' . $row->description . '</td>';
			$table .= '<td>' . $row->asset_location . '</td>';
			$table .= '<td>' . $row->name . '</td>';
			$table .= '<td>' . $row->accountable_person . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->retirement_date) . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->commissioning_date) . '</td>';
			$table .= '</tr>';
		}

		$footer = '';

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

		$result		= $this->asset_list->getAssetMasterListPaginationcsv($this->fields);


		$header = array(
			'Item Code',
			'Asset Class',
			'Asset Name',
			'Asset Number',
			'Sub-number',
			'Serial Number/Engine Number',
			'Description',
			'Asset Location',
			'Department',
			'Accountable Person',
			'Commissioning Date',
			'Retirement Date',
			'No. of Months Useful Life',
			'Depreciation Month',
			'Depreciation Amount',
			'Capitalized Cost',
			'Purchase Value',
			'Balance Value',
			'Salvage Value',
			'number_of_dep',
			'GL Account(Asset)',
			'GL Account(Accdep)',
			'GL Account(Dep Expense)'
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
		foreach ($result as $key => $row) {
			$csv .= "\n";
			$csv .= '"' . $row->itemcode;
			$csv .= '"' . $row->assetclass;
			$csv .= '"' . $row->asset_name;
			$csv .= '"' . $row->sub_number;
			$csv .= '"' . $row->description;
			$csv .= '"' . $row->asset_location;
			$csv .= '"' . $row->department;
			$csv .= '"' . $row->accountable_person;
			$csv .= '"' . $row->retirement_date;
			$csv .= '"' . $row->commissioning_date;
			$csv .= '"' . $row->useful_life;
			$csv .= '"' . $row->depreciation_month;
			$csv .= '"' . $row->capitalized_cost;
			$csv .= '"' . $row->purchase_value;
			$csv .= '"' . $row->balance_value;
			$csv .= '"' . $row->salvage_value;
			$csv .= '"' . $row->asset;
			$csv .= '"' . $row->accdep;
			$csv .= '"' . $row->depexp;
			

		}
		
		// $csv .= "\n";
		// $csv .= '"Totals:",';
		// $csv .= '"",';
		// $csv .= '"",';
		// $csv .= '"",';
		// $csv .= '"",';
		// $csv .= '"' . number_format($total_aging->current_total, 2) . '",';
		// $csv .= '"' . number_format($total_aging->thirty_total, 2) . '",';
		// $csv .= '"' . number_format($total_aging->sixty_total, 2) . '",';
		// $csv .= '"' . number_format($total_aging->oversixty_total, 2) . '",';
		// $csv .= '"' . number_format($total_aging->balance_total, 2) . '"';
		
		return $csv;
	}


}
?>