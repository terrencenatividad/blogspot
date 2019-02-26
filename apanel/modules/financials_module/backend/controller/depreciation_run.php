<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->depreciation_run	= new depreciation_run();
		$this->adjustment		= $this->checkoutModel('inventory_module/inventory_adjustment_model');
		$this->session			= new session();
		$this->seq 				= new seqcontrol();
		$this->year 			= date('Y');
		$this->month			= date('m');
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
		$data['ui']					= $this->ui;
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
		$first_dep = $this->depreciation_run->get_depreciation_start();
		$first_dep = date("Y-m-t", strtotime($first_dep->depreciation_month));
		$first_dep = $this->date->dateFormat($first_dep);
		// if(!empty($first_dep)){
		// 	$a = date("Y-m-t", strtotime($first_dep[0]->fiscalyear.'-'.$first_dep[0]->period));
		// 	$a = $this->date->dateFormat($a);
		// }else{
		// 	$a = '';
		// }
		

		$checkdep		= $this->depreciation_run->checkDepreciation();
		if($checkdep === true){
			if($checkdep->transactiondate > $this->date->dateDbFormat()){
				$checkdep = 'true';
			}else{
				$checkdep = 'false';
			}
		}else{
			$checkdep = 'false';
		}
		
		
		$sort = $this->input->post('sort');
		$pagination		= $this->depreciation_run->getAssetMasterList($this->fields,$sort);

		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="12" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
				$table .= '<tr>';
				$table .= '<td class="text-left">' . date('M d, Y', strtotime($row->depreciation_date)) .'</td>';
				$table .= '<td class="text-left">' . $row->name . '</td>';
				$table .= '<td class="text-left">' . $row->asset_number . '</td>';
				$table .= '<td class="text-left">' . $row->serial_number . '</td>';
				$table .= '<td class="text-left">' . $row->assetclass . '</td>';
				$table .= '<td class="text-left">' . $row->description . '</td>';
				$table .= '<td class="text-right">' . number_format($row->capitalized_cost, 2) . '</td>';
				$table .= '<td class="text-right">' . number_format($row->depreciation_amount, 2) . '</td>';
				$table .= '<td class="text-right">' . number_format($row->accumulated_dep, 2) . '</td>';
				$table .= '<td>' . $row->asset . '</td>';
				$table .= '<td>' . $row->accdep . '</td>';
				$table .= '<td>' . $row->depexp . '</td>';
				$table .= '</tr>';
		}

		$pagination->table 	= $table;
		$pagination->check	= $checkdep;
		// $pagination->closing= $a;
		$pagination->first_dep= $first_dep;
		
		return $pagination;
	}

	private function ajax_list_2() {
		$data		= $this->input->post(array('search', 'sort','checked'));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$checked	= $data['checked'];
		$pagination	= $this->depreciation_run->getAsset2($this->fields, $search, $sort, $checked);
		// var_dump($pagination);
		$table		= '';
		if (empty($pagination)) {
			$table = '<tr><td colspan="12" class="text-center"><b>No Records Found</b></td></tr>';
		}
		$date = $this->date->dateDbFormat();
		foreach ($pagination as $row) {
			$accumulated	= $this->depreciation_run->getAccumulated($checked);
			$time  					= strtotime($row->depreciation_month);
			$depreciation 			= 0;
			$accumulated_amount = $accumulated->depamount;
			$x = 0;
			$a = date("Y-m", strtotime($row->depreciation_month));
			$b = date("Y-m", strtotime($date));
			$date1=date_create($a);
			$date2=date_create($b);
			$diff=date_diff($date1,$date2);
			$x = $diff->m;
			// for($x=1;$x<=$row->useful_life;$x++){
			$depreciation_amount 	= ($row->balance_value - $row->salvage_value) / $row->useful_life;
			// $depreciation += ($row->balance_value - $row->salvage_value) / $row->useful_life;
			$final = date("Y-m-d", strtotime("+$x month", $time));
			$depreciation = $accumulated_amount + $depreciation_amount;
			// }
			$table .= '<tr>';
			$table .= '<td class="text-left">' . date('M d, Y',strtotime($final)) .'</td>';
			$table .= '<td class="text-left">' . $row->name . '</td>';
			$table .= '<td class="text-left">' . $row->asset_number . '</td>';
			$table .= '<td class="text-left">' . $row->serial_number . '</td>';
			$table .= '<td class="text-left">' . $row->assetclass . '</td>';
			$table .= '<td class="text-left">' . $row->description . '</td>';
			$table .= '<td class="text-right">' . number_format($row->capitalized_cost, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format(($depreciation_amount), 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($depreciation, 2) . '</td>';
			$table .= '<td>' . $row->asset . '</td>';
			$table .= '<td>' . $row->accdep . '</td>';
			$table .= '<td>' . $row->depexp . '</td>';
			$table .= '</tr>';
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
			$table = '<tr><td colspan="12" class="text-center"><b>No Records Found</b></td></tr>';
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
		$dep_date = $this->input->post('date');
		$asd = $this->depreciation_run->get_depreciation_start();
		
		$fiscalyear = date("Y", strtotime($dep_date));
		$period		= date("m", strtotime($dep_date));

		$pagination	= $this->depreciation_run->getAsset123($dep_date);
		$date = $this->date->dateDbFormat();
		$year = $this->year;
		$month = $this->month;
		
		$paginations	= $this->depreciation_run->getAsset1234();
		foreach($paginations as $row2){
		$this->depreciation_run->saveJV($dep_date,$row2->depreciation_amount,$row2->gl_asset,$row2->gl_accdep,$row2->gl_depexpense);
		}
		
		$table		= '';
		if (empty($pagination)) {
			$table = '<tr><td colspan="12" class="text-center"><b>No Records Found</b></td></tr>';
		}
		$this->depreciation_run->deleteSched($period,$fiscalyear);

		foreach ($pagination as $row) {
			$accumulated	= $this->depreciation_run->getAccumulated($row->asset_number);
			$time  					= strtotime($row->depreciation_month);
			$depreciation 			= 0;
			$accumulated_amount = $accumulated->depamount;
			$x = 0;
			$a = date("Y-m", strtotime($dep_date));
			$b = date("Y-m", strtotime($date));
			$date1=date_create($a);
			$date2=date_create($b);
			$diff=date_diff($date1,$date2);
			$x = $diff->m;
			$depreciation_amount 	= ($row->balance_value - $row->salvage_value) / $row->useful_life;
			$d = date("d", strtotime($row->depreciation_month));
			// var_dump($d);
			$final = date("Y-m-d", strtotime($fiscalyear.'-'.$period.'-'.$d));
			// $final = date("Y-m-d", strtotime("+$x month", $time));
			$depreciation = $accumulated_amount + $depreciation_amount;
			$sched = $this->depreciation_run->saveAssetMasterSchedule($row->asset_number,$row->itemcode,$final,$depreciation,$depreciation_amount, $row->gl_asset, $row->gl_accdep, $row->gl_depexp,$year,$month,$row->useful_life);
			
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