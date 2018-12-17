<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui					= new ui();
		$this->input				= new input();
		$this->asset_depreciation	= new asset_depreciation();
		$this->report_model			= new report_model();
		$this->view->header_active	= 'report/';
		$this->fields				= array(
			'name department',
			'asset_id',
			'serial_number',
			'assetclass',
			'am.description',
			'd.depreciation_date',
			'am.capitalized_cost',
			'd.depreciation_amount'
		);
	}

	public function view() {
		$this->view->title			= 'Asset Depreciation List';
		$data['ui']					= $this->ui;
		$data['datefilter']			= date("M d, Y");
		$data['asset_list']			= $this->asset_depreciation->getAsset();
		$data['assetclass_list']	= $this->asset_depreciation->getAssetClass();
		$data['dept_list']			= $this->asset_depreciation->getAssetDepartment();
		$this->view->load('asset_depreciation', $data);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data 			  = $this->input->post(array('datefilter','asset_number','sort','assetclass','department'));
		$sort      		  = $data['sort'];
		$asset_number     = $data['asset_number'];
		$datefilter       = $data['datefilter'];
		$assetclass       = $data['assetclass'];
		$department       = $data['department'];
		
		$pagination		  = $this->asset_depreciation->getDepreciation($this->fields,$sort,$asset_number,$datefilter,$assetclass,$department);
		$tt = '';
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		$totalcc  = 0;
		$totaldep = 0;
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$table .= '<td>' . $row->department . '</td>';
			$table .= '<td>' . $row->asset_id . '</td>';
			$table .= '<td>' . $row->serial_number . '</td>';
			$table .= '<td>' . $row->assetclass . '</td>';
			$table .= '<td>' . $row->description . '</td>';
			$table .= '<td class="text-right">' . number_format($row->capitalized_cost, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->depreciation_amount, 2) . '</td>';
			$table .= '</tr>';

			$totalcc  +=$row->capitalized_cost;
			$totaldep +=$row->depreciation_amount;
		}

		$footer = '';
		$footer .= '<tr>';
		$footer .= '<td colspan="5" class="text-right"><b>' . 'Total:' . '</b></td>';
		$footer .= '<td colspan="1" class="text-right"><b>' . number_format($totalcc, 2) . '</b></td>';
		$footer .= '<td colspan="1" class="text-right"><b>' . number_format($totaldep, 2) . '</b></td>';
		$footer .= '</tr>';
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
		$data 			  = $this->input->post(array('datefilter','asset_number','sort','assetclass','department'));
		$sort      		  = $data['sort'];
		$asset_number     = $data['asset_number'];
		$datefilter       = $data['datefilter'];
		$assetclass       = $data['assetclass'];
		$department       = $data['department'];

		$result		= $this->asset_depreciation->getDepreciationcsv($this->fields,$sort,$asset_number,$datefilter,$assetclass,$department);


		$header = array(
			'Department',
			'Asset Number',
			'Serial Number / Engine Number',
			'Asset Class',
			'Description',
			'Capitalized Cost',
			'Depreciation Amount'
		);

		$csv = '';
		$csv .= 'Asset Depreciation';
		$csv .= "\n\n";
		$csv .= '"Date:","' . $this->date->dateFormat($datefilter) . '"';
		$csv .= "\n\n";
		$csv .= '"' . implode('","', $header) . '"';
		if (empty($result)) {
			$csv .= 'No Records Found';
		}
		$totalcc  = 0;
		$totaldep = 0;
		foreach ($result as $key => $row) {
			$csv .= "\n";
			$csv .= '"' . $row->department . '",';
			$csv .= '"' . $row->asset_id . '",';
			$csv .= '"' . $row->serial_number . '",';
			$csv .= '"' . $row->assetclass . '",';
			$csv .= '"' . $row->description . '",';
			$csv .= '"' . number_format($row->capitalized_cost, 2) . '",';
			$csv .= '"' . number_format($row->depreciation_amount, 2) . '",';

			$totalcc  +=$row->capitalized_cost;
			$totaldep +=$row->depreciation_amount;
		}
		
		$csv .= "\n";
		$csv .= '"",';
		$csv .= '"",';
		$csv .= '"",';
		$csv .= '"",';
		$csv .= '"Total:",';
		$csv .= '"' . number_format($totalcc, 2) . '",';
		$csv .= '"' . number_format($totaldep, 2) . '",';
		
		return $csv;
	}


}
?>