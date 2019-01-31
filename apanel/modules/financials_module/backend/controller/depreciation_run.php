<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->depreciation_run	= new depreciation_run();
		$this->adjustment		= $this->checkoutModel('inventory_module/inventory_adjustment_model');
		$this->session			= new session();
		$this->fields 			= array(
			'asset_number',
			'asset_name',
			'asset_class'
		);
		$this->fields1 			= array(
			'asset_id'
			// 'asset_name',
			// 'asset_class'
		);
	}

	public function listing() {
		$this->view->title	= $this->ui->ListLabel('');
		$data['ui']			= $this->ui;
		$this->view->load('depreciation_run/depreciation_run', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data		= $this->input->post(array('search', 'sort','checked'));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$checked	= $data['checked'];
		$pagination	= $this->depreciation_run->getAsset($this->fields);
		
		$table		= '';
		if (empty($pagination)) {
			$table = '<tr><td colspan="2" class="text-center"><b>No Records Found</b></td></tr>';
		}

		foreach ($pagination as $key => $row) {
			$table .= '<tr class="info">';
			$table .= '<td class = "col-md-2"><b>Asset Class</b></td><td colspan = "5">'.$row->assetclass.'</td>';
			$table .= '</tr>'; 
			
			$asset = $this->depreciation_run->getAssetDetails($row->id);
			foreach ($asset as $row) {
				$table .= '<tr class="success"><td class = "col-md-2"><b>' . 'Asset Number' . '</b></td><td colspan = "5">' . $row->asset_number . '</td></tr>';
				$table .= '<tr class="warning"><td class = "col-md-2"><b>' . 'Budget Center' . '</b></td><td colspan = "5">' . $row->name . '</td></tr>';
				$table .= '<tr class="warning"><td class = "col-md-2"><b>' . 'Capitalized Cost' . '</b></td><td colspan = "5">' . number_format($row->capitalized_cost, 2) . '</td></tr>';
				$table .= '<tr class="warning"><td class = "col-md-2"><b>' . 'Depreciation / month' . '</b></td><td colspan = "5">' . number_format(($row->balance_value - $row->salvage_value) / $row->useful_life, 2) . '</td></tr>';
				
				$comp = $this->depreciation_run->getDepreciationDetails($row->asset_number);
				if (!empty($comp)) {
					$table .= '<tr class="warning">';
					$table .= '<th class="text-left">' . 'Date' . '</td>';
					$table .= '<th class="text-left">' . 'Depreciation Amount' . '</td>';
					$table .= '<th class="text-left">' . 'Accumulated Depreciation Amount' . '</td>';
					$table .= '<th class="text-left">' . 'GL Account(Asset)' . '</td>';
					$table .= '<th class="text-left">' . 'GL Account(AccDep)' . '</td>';
					$table .= '<th class="text-left">' . 'GL Account(DepExp)' . '</td>';
					$table .= '</tr>';
				}
				foreach ($comp as $row) {
					$table .= '<tr>';
					$table .= '<td class="text-left">'.date("M d, Y", strtotime($row->depreciation_date)).'</td>';
					$table .= '<td class="text-right">'.number_format($row->depreciation_amount, 2).'</td>';
					$table .= '<td class="text-right">'.number_format($row->accumulated_dep, 2).'</td>';
					$table .= '<td class="text-left">'.$row->a_segment5 . ' - '. $row->asset.'</td>';
					$table .= '<td class="text-left">'.$row->b_segment5 . ' - '. $row->accdep.'</td>';
					$table .= '<td class="text-left">'.$row->c_segment5 . ' - '. $row->depexp.'</td>';
					$table .= '</tr>';
				}
			}
		}

		return array('table' => $table);
	}

	private function ajax_list_2() {
		$data		= $this->input->post(array('search', 'sort','checked'));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$checked	= $data['checked'];
		$pagination	= $this->depreciation_run->getAsset2($this->fields, $search, $sort, $checked);
		$table		= '';
		if (empty($pagination)) {
			$table = '<tr><td colspan="2" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination as $row) {
			$table .= '<tr class="info">';
			$dropdown = $this->ui->loadElement('check_task')
								->addView()
								->addEdit()
								->draw();
			$table .= '<td><b>' . 'Asset Class' . '</b></td>';
			$table .= '<td colspan="5">' . $row->assetclass . '</td>';
			$table .= '</tr>';

			$table .= '<tr class="success"><td class="col-md-2"><b>' . 'Asset Number' . '</b></td><td colspan="5">' . $row->asset_number . '</td></tr>';
				$table .= '<tr class="warning"><td class="col-md-2"><b>' . 'Budget Center' . '</b></td><td colspan="5">' . $row->name . '</td></tr>';
				$table .= '<tr class="warning"><td class="col-md-2"><b>' . 'Capitalized Cost' . '</b></td><td colspan="5">' . number_format($row->capitalized_cost, 2) . '</td></tr>';
				$table .= '<tr class="warning"><td class="col-md-2"><b>' . 'Depreciation / month' . '</b></td><td colspan="5">' . number_format(($row->balance_value - $row->salvage_value) / $row->useful_life, 2) . '</td></tr>';
				
			$table .= '<tr class="warning">';
			$table .= '<th>' . 'Date' . '</td>';
			$table .= '<th>' . 'Depreciation Amount' . '</td>';
			$table .= '<th>' . 'Accumulated Depreciation Amount' . '</td>';
			$table .= '<th>' . 'GL Account(Asset)' . '</td>';
			$table .= '<th>' . 'GL Account(AccDep)' . '</td>';
			$table .= '<th>' . 'GL Account(DepExp)' . '</td>';
			$table .= '</tr>';
			$table .= '<tr>';
			$depreciation = 0;  
			$time  = strtotime($row->depreciation_month);
			for($x=1;$x<=$row->useful_life;$x++){
			$depreciation += ($row->balance_value - $row->salvage_value) / $row->useful_life;
			$final = date("M d, Y", strtotime("+$x month", $time));
			$table .= '<tr>';
			$table .= '<td>' . $final . '</td>';
			$table .= '<td>' . number_format(($row->balance_value - $row->salvage_value) / $row->useful_life, 2) . '</td>';
			$table .= '<td>' . number_format($depreciation, 2) . '</td>';
			$table .= '<td>' . $row->a_segment5 .' - '. $row->asset . '</td>';
			$table .= '<td>' . $row->b_segment5 .' - '. $row->accdep . '</td>';
			$table .= '<td>' . $row->c_segment5 .' - '. $row->depexp . '</td>';
			$table .= '</tr>';
	

			}
		}

			$table .= '</tr>';

		return array('table' => $table);
	}

	private function ajax_load_asset() {
		$data		= $this->input->post(array('search', 'sort', ''));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$pagination	= $this->depreciation_run->getAssetList($this->fields, $search, $sort);

		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="2" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
								->addView()
								->addEdit()
								->draw();
			
			$table .= '<td align = "center">' . '<input type="checkbox" class="check" value="0" data-id="'.$row->id.'">' . '</td>';
			$table .= '<td>' . $row->assetclass . '</td>';
			$table .= '<td>' . $row->asset_number . '</td>';
			$table .= '<td>' . $row->name . '</td>';
			$table .= '</tr>';
			
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_load_depreciation() {
		$data		= $this->input->post(array('search', 'sort', ''));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$pagination	= $this->depreciation_run->getAsset123();

		$table		= '';
		if (empty($pagination)) {
			$table = '<tr><td colspan="2" class="text-center"><b>No Records Found</b></td></tr>';
		}
		$this->depreciation_run->deleteSched();

		foreach ($pagination as $row) {
			$dropdown = $this->ui->loadElement('check_task')
								->addView()
								->addEdit()
								->draw();
			$time  					= strtotime($row->depreciation_month);
			$depreciation 			= 0;
			for($x=1;$x<=$row->useful_life;$x++){
			$depreciation_amount 	= ($row->balance_value - $row->salvage_value) / $row->useful_life;
			$depreciation += ($row->balance_value - $row->salvage_value) / $row->useful_life;
			$final = date("Y-m-d", strtotime("+$x month", $time));
			$sched = $this->depreciation_run->saveAssetMasterSchedule($row->asset_number,$row->itemcode,$final,$depreciation,$depreciation_amount, $row->gl_asset, $row->gl_accdep, $row->gl_depexp);
			}
			
		}

		return array('table' => $table);
	}

	private function update_locktime(){
		$curr_user 	=	USERNAME;
		$result 	=	$this->adjustment->update_locktime($curr_user);

		if( $result )
		{
			$msg = "success";
			$this->log->saveActivity("Current User [$curr_user]. Locked Other Users for Adjustment.");
		}
		else
		{
			$msg = $result;
		}

		return $dataArray = array("msg" => $msg);
	}

	private function retrieve_users(){
		$curr_user 	=	USERNAME;
		$temp 		=	array();

		$result 	=	$this->adjustment->getLoggedInUsers($curr_user);
		
		foreach ($result as $key => $row) {
			$temp[] 	=	$row->name."<br>";
		}

		$lists		= implode(' ', $temp);

		return $dataArray = array("user_lists" => $lists);
	}

}