<?php
class controller extends wc_controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->bank 		=	new bank();
		$this->input 		=	new input();
		$this->ui 			= 	new ui();
		$this->url 			=	new url();
		$this->log 			=  	new log();

		$this->view->header_active = 'maintenance/bank/';

		$this->fields = array(
			'gl_code',
			'shortname',
			'bankcode',
			'accountno',
			'currency',
			'checking_account',
			'address1'
		);

		$this->fields2 = array(
			'bank_id',
			'booknumber',
			'firstchequeno',
			'lastchequeno'
		);
	}

	public function listing(){
		$this->view->title = $this->ui->ListLabel('');
		$data['ui'] 	   = $this->ui;
		$this->view->load('bank/bank_list', $data);
	}

	public function create()
	{
		$this->view->title = $this->ui->AddLabel('');

		if ($this->input->isPost) 
		{
			$extracted_data = $this->input->post($data);

			extract($extracted_data);		
		}

		$data 					= $this->input->post($this->fields);
		$data['currencylist']   = $this->bank->retrieveExchangeRateDropdown();
		$data['gllist']   		= $this->bank->retrieveGLDropdown();
		$data['ui'] 			= $this->ui;
		$data['task'] 			= 'add';
		$data['show_input'] 	= true;
		$data['ajax_post'] 		= '';
		$this->view->load('bank/bank', $data);
	}

	public function edit($id)
	{
		$this->view->title = $this->ui->EditLabel('');
		$data 			 	= (array) $this->bank->retrieveExistingBank($this->fields, $id);
		$data['currencylist']   = $this->bank->retrieveExchangeRateDropdown();
		$data['gllist']   		= $this->bank->retrieveGLDropdown();
		$data['ui'] 		= $this->ui;
		$data['task'] 		= 'update';
		$data['show_input'] = true;
		$data['id']			= $id;
		$data['ajax_post'] 	= "&id=$id";
		$this->view->load('bank/bank', $data);
	}

	public function view($id)
	{
		$this->view->title 	= $this->ui->ViewLabel('');
		$data 			 	= (array) $this->bank->retrieveExistingBank($this->fields, $id);
		$data['currencylist']   = $this->bank->retrieveExchangeRateDropdown();
		$data['gllist']   		= $this->bank->retrieveGLDropdown();
		$data['id']			= $id;
		$data['ui'] 		= $this->ui;
		$data['show_input'] = false;
		$data['task'] 		= "";
		$data['ajax_post'] 	= "";

		$this->view->load('bank/bank', $data);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function bank_list() {

		$search = $this->input->post("search");
		$sort 	= $this->input->post('sort');
		$limit  = $this->input->post('limit');
		$list 	= $this->bank->retrieveListing($search, $sort, $limit);

		$table 	= '';

		if( !empty($list->result) ) :
			foreach ($list->result as $key => $row) {

				$dropdown = $this->ui->loadElement('check_task')
				->addView()
				->addEdit()
										// ->addDelete()
				->addOtherTask(
					'Delete',
					'trash',
					$row->nextchequeno == $row->firstchequeno
				)
				->addCheckbox()
				->setValue($row->id)
				->addOtherTask(
					'Manage Check',
					'new-window',
					$row->checking_account == 'yes'
				)
				->addOtherTask(
					'Deactivate',
					'glyphicon glyphicon-arrow-down',
					$row->stat == 'active'
				)
				->addOtherTask(
					'Activate',
					'glyphicon glyphicon-arrow-up',
					$row->stat == 'inactive'
				)
				->draw();

				if($row->stat == 'active'){
					$bank_status = '<span class="label label-success">'.strtoupper($row->stat).'</span>';
				}else if($row->stat == 'inactive'){
					$bank_status = '<span class="label label-warning">'.strtoupper($row->stat).'</span>';
				}

				$table .= '<tr>';
				$table .= ' <td align = "center">' .$dropdown. '</td>';
				$table .= '<td>' . $row->shortname . '</td>';
				$table .= '<td>' . $row->bankcode . '</td>';
				$table .= '<td>' . $row->accountno. '</td>';
				$table .= '<td>' . $bank_status. '</td>';
				$table .= '</tr>';
			}
		else:
			$table .= "<tr>
			<td colspan = '5' class = 'text-center'>No Records Found</td>
			</tr>";
		endif;

		$list->table = $table;
		$list->csv 		= 	utf8_encode($this->export());

		return $list;
	}

	private function add()
	{
		$posted_data 	= $this->input->post($this->fields);
		$result  		= $this->bank->insertBank($posted_data);
		$bankname 		= $posted_data["shortname"];
		$accountno 		= $posted_data["accountno"];

		if( $result )
		{
			$msg = "success";
			$this->log->saveActivity("Added New Bank [$bankname] -  Account Number [$accountno]");
		}
		else
		{
			$msg = $result;
		}

		return $dataArray 		= array( "msg" => $msg, "bankname" => $posted_data["shortname"], "accountno" => $posted_data["accountno"] );
	}

	private function update()
	{
		$posted_data 	= $this->input->post($this->fields);
		$id 		 	= $this->input->post('id');
		$result 		= $this->bank->updateBank($posted_data, $id);
		$bankname 		= $posted_data["shortname"];
		$accountno 		= $posted_data["accountno"];

		if( $result )
		{
			$msg = "success";
			$this->log->saveActivity("Update Bank [$bankname] - Account Number [$accountno]");
		}
		else
		{
			$msg = $result;
		}

		return $dataArray 		= array( "id" => $id, "msg" => $msg );
	}

	private function delete()
	{
		$id_array 		= array('id');
		$id       		= $this->input->post($id_array);
		$info 			= $this->bank->getBank($id);


		foreach ($info as $key => $value) {
			$sname = $value->shortname;
			$code = $value->accountno;
			$this->log->saveActivity("Deleted Bank [$sname] - Account Number [$code]");
		}
		$result 		= $this->bank->deleteBank($id);



		if( empty($result) ) {
			$msg = "success";
		}
		else
		{
			$msg = $result;
		}

		return $dataArray 		= array( "msg" => $msg);
	}

	private function check_duplicate(){
		$current = $this->input->post('curr_code');
		$old 	 = $this->input->post('old_code');
		$count 	 = 0;
		if( $current!='' && $current != $old )
		{
			$result = $this->bank->check_duplicate($current);
			$count = $result[0]->count;
		}

		$msg   = "";

		if( $count > 0 )
		{	
			$msg = "exists";
		}

		return $dataArray = array("msg" => $msg);
	}

	public function manage_check($id){
		$this->view->title 		= 'Manage Check';
		$data2 			 		= (array) $this->bank->retrieveExistingBank($this->fields, $id);
		$data2 					= $this->input->post($this->fields2);
		$data2['id']			= $id;
		$data2['currencylist']  = $this->bank->retrieveExchangeRateDropdown();
		$data2['gllist']   		= $this->bank->retrieveGLDropdown();
		$data2['ui'] 			= $this->ui;
		$data2['task'] 			= 'save_check';
		$data2['show_input'] 	= true;
		$data2['ajax_post'] 	= '';
		$this->view->load('bank/manage_check', $data2);
	}

	public function save_check(){
		$posted_data 	= $this->input->post($this->fields2);
		$result  		= $this->bank->insertCheck($posted_data);
		$firstchequeno 	= $posted_data['firstchequeno'];
		$lastchequeno	= $posted_data['lastchequeno'];
		$id				= $posted_data['bank_id'];
		$accntname 		= $this->bank->getAccountname($id);
		$id = $accntname[0]->shortname;

		if( $result )
		{
			$msg = "success";
			$this->log->saveActivity("Added Check Series On Bank ($id) [$firstchequeno -  $lastchequeno]");
		}

		else
		{
			$msg = $result;
		}

		return $dataArray 		= array( "msg" => $msg );

	}

	private function check_list() {

		$search = $this->input->post("search");
		$id 	= $this->input->post("id");
		$sort 	= $this->input->post('sort');
		$limit  = $this->input->post('limit');
		$list 	= $this->bank->checkListing($search, $sort, $limit, $id);

		$table 	= '';

		if( !empty($list->result) ) :
			foreach ($list->result as $key => $row) {
				$entereddate = explode(' ',$row->entereddate);
				$date = $entereddate[0];
				$book_date = str_replace('-', '', $date);

				if($row->stat == 'open'){
					$next = $row->nextchequeno;
					$check_stat = '<span class="label label-success">'.strtoupper('AVAILABLE').'</span>';
				}else if($row->stat == 'closed'){
					$next = '';
					$check_stat = '<span class="label label-info">'."USED".'</span>';
				}
					// } else {
					// 	$check_stat = '<span class="label label-warning">'."USED".'</span>';
					// }
				$show_edit = ($next == $row->firstchequeno) ? 'editcheck' : '';
				$show_del = ($next == $row->firstchequeno) ? 'deletecheck' : '';
				$show_cancel = ($row->stat == 'closed') ? '' : 'cancel';
				
				if ($show_cancel == ''){
					$dropdown = '';
				} else {
					$dropdown = $this->ui->loadElement('check_task')

					->addOtherTask(
						'Edit Check Series',
						'pencil',
						$show_edit
					)

					->addOtherTask(
						'Delete Check Series',
						'trash',
						$show_del
					)
					
								// ->addOtherTask(
								// 	'Set as Default Check',
								// 	'check',
								// 	'set_default'
								// )
					->addOtherTask(
						'Cancel Check Range',
						'remove-circle',
						$show_cancel
					)
					->draw();
				}

				$table .= '<tr>';
				$table .= ' <td align = "center">' .$dropdown. '</td>';
				$table .= '<td>' . $row->shortname . '</td>';
				$table .= '<td>' . $row->accountno . '</td>';
				$table .= '<td id="booknumber">' . $book_date. ' - ' .$row->booknumber . '</td>';
				$table .= '<td id="start_check" class="start_check" value="' . $row->firstchequeno. '-' .$row->lastchequeno. '">' . $row->firstchequeno. '-' .$row->lastchequeno. '</td>';
				$table .= '<td class="next">' . $next. '</td>';
				$table .= '<td>' . $check_stat. '</td>';
				$table .= '</tr>';


				$cancel_list = $this->bank->cancel_list($row->bank_id,$row->firstchequeno);

				if ($cancel_list){
					$table	.= '<tr>';
					$table	.= '<td></td>';
					$table	.= '<td class="warning" ><strong>First Number</strong></td>';
					$table	.= '<td class="warning" ><strong>Last Number</strong></td>';
					$table	.= '<td class="warning" ><strong>Date</strong></td>';
					$table	.= '<td class="warning" ><strong>Reason</strong></td>';
					$table	.= '</tr>';

					foreach ($cancel_list as $key => $value) {
						$entereddate = explode(' ',$value->entereddate);
						$date = $entereddate[0];
						$book_date = str_replace('-', '', $date);

						$table .= '<tr>';
						$table	.= '<td class="text-center"><span class="label label-danger ">CANCELLED</span></td>';
						$table .= '<td>' . $value->firstcancelled . '</td>';
						$table .= '<td>' . $value->lastcancelled . '</td>';
						$table .= '<td>' . $book_date . '</td>';
						$table .= '<td colspan="2">' . $value->remarks. '</td>';
						$table .= '</tr>';
					}
				} 





			}
		else:
			$table .= "<tr>
			<td colspan = '6' class = 'text-center'>No Records Found</td>
			</tr>";
		endif;

		$list->table 	=	$table;

		return $list;
	}

	public function edit_check(){
		$id 		= $this->input->post("id");
		$bookno 	= $this->input->post("booknumber");
		$data2		= (array) $this->bank->retrieveCheck($id, $bookno);
		$booknumber 	= $data2[0]->booknumber; 
		$firstchequeno 	= $data2[0]->firstchequeno; 
		$lastchequeno 	= $data2[0]->lastchequeno; 
		$data = array("booknumber" => $booknumber,"firstchequeno" => $firstchequeno, 'lastchequeno' => $lastchequeno , 'task' => 'update_check');
		return $data;
	}

	public function update_check(){
		$posted_data 	= $this->input->post($this->fields2);
		$old 			= $this->input->post('oldbooknumber');
		$result  		= $this->bank->insertCheck($posted_data);
		$firstchequeno 	= $posted_data['firstchequeno'];
		$lastchequeno	= $posted_data['lastchequeno'];
		$bank_id		= $posted_data['bank_id'];
		$accntname 		= $this->bank->update_check($bank_id, $posted_data, $old);
		$bankdesc 		= $this->bank->getAccountname($bank_id);
		$isname = $bankdesc[0]->shortname;
		if( $accntname )
		{
			$msg = "success";
			$this->log->saveActivity("Update Check Series On Bank ($isname) [$firstchequeno -  $lastchequeno]");
		}
		else
		{
			$msg = $result;
		}
		return $dataArray 		= array( "msg" => $msg );

	}

	public function ajax_edit_deactivate(){
		$id 	= $this->input->post('id');
		$data['stat'] = 'inactive';
		$info = $this->bank->getInfo($id);
		$result = $this->bank->deactivateBank($id,$data);
		$sname  = $info[0]->shortname;
		$accountno  = $info[0]->accountno;
		if ($result){
			$this->log->saveActivity("Deactivated Bank [$sname] - Account Number [$accountno]");
		}
		return $result;
	}

	public function ajax_edit_activate(){
		$id 	= $this->input->post('id');
		$info = $this->bank->getInfo($id);
		$sname  = $info[0]->shortname;
		$accountno  = $info[0]->accountno;
		$data['stat'] = 'active';
		$result = $this->bank->deactivateBank($id,$data);
		if ($result){
			$this->log->saveActivity("Activated Bank [$sname] - Account Number [$accountno]");
		}
		return $result;
	}

	public function delete_check(){
		$posted_data 	= $this->input->post('booknumber');
		$id 			= $this->input->post('id');

		$bankdesc 		= $this->bank->getAccountname($id);
		$isname 		= $bankdesc[0]->shortname;
		$firstchequeno 	= $bankdesc[0]->firstchequeno;
		$lastchequeno 	= $bankdesc[0]->lastchequeno;
		$accntname 		= $this->bank->deleteCheck($posted_data, $id);

		if( $accntname )
		{
			$msg = "success";
			$this->log->saveActivity("Delete Check Series On Bank ($isname) [$firstchequeno -  $lastchequeno]");
		}

		else
		{
			$msg = $result;
		}

		return $dataArray 		= array( "msg" => $msg );

	}

	public function check_duplicate_booknumber(){
		$current = $this->input->post('curr_code');
		$old 	 = $this->input->post('old_code');
		$count 	 = 0;
		if( $current!='' && $current != $old )
		{
			$result = $this->bank->check_duplicate_booknums($current);
			$count = $result[0]->count;
		}
		$msg   = "";
		if( $count > 0 )
		{	
			$msg = "exists";
		}

		return $dataArray = array("msg" => $msg);

	}

	public function set_check(){
		$fno = $this->input->post('booknumber');
		$fno = explode('-',$fno);
		$first = $fno[0];
		$bank = $this->input->post('id');
		$result = $this->bank->set_check($bank, $first);
		if ($result){
			$msg = 'success';
		}

		return $msg;
	}

	public function check_duplicate_gl_code(){
		$old 	 = $this->input->post('old_gl_code');
		$current 	 = $this->input->post('curr_gl_code');
		$count 	 = 0;
		if( $current!='' && $current != $old )
		{
			$result = $this->bank->check_duplicate_glcode($current);
			$count = $result[0]->count;
		}

		$msg   = "";

		if( $count > 0 )
		{	
			$msg = "exists";
		}

		return $dataArray = array("msg" => $msg);
	}

	public function save_cancelled(){
		$data_array = array(
			'id',
			'start',
			'end',
			'firstcancelled',
			'lastcancelled',
			'remarks'
		);
		$data = $this->input->post($data_array);
		$result = $this->bank->insertCancelledChecks($data);
		if ($result){
			$msg = 'yes';
		} else {
			$msg = '';
		}
		return $dataArray = array("msg" => $msg);
	}

	private function update_multiple_deactivate(){
		$posted_data       =  $this->input->post(array('ids'));
		
		$data['stat']       =  'inactive';

		$posted_ids       =  $posted_data['ids'];
		$id_arr         =  explode(',',$posted_ids);

		foreach($id_arr as $key => $value)
		{
			$result       =   $this->bank->deactivateBank($value, $data);
		}
		
		if($result)
		{
			$msg = "success";
		} else {
			$msg = "Failed to Update.";
		}
		
		return $dataArray = array( "msg" => $msg );
	}

	private function update_multiple_activate(){
		$posted_data 			=	$this->input->post(array('ids'));

		$data['stat'] 			=	'active';

		$posted_ids 			=	$posted_data['ids'];
		$id_arr 				=	explode(',',$posted_ids);

		foreach($id_arr as $key => $value)
		{
			$result 			= 	$this->bank->deactivateBank($value, $data);
		}

		if($result)
		{
			$msg = "success";
		} else {
			$msg = "Failed to Update.";
		}

		return $dataArray = array( "msg" => $msg );
	}

	private function ajax_save_import() {
		$csv_array	= array_map('str_getcsv', file($_FILES['file']['tmp_name']));
		$result		= false;
		$errors		= array();
		$values		= array();
		$header = array("Bank Account GL Code","Bank Code","Bank Name","Bank Account Number","Currency Code","Checking Account (yes/no)", "Bank Address");

		if ($csv_array[0] == $header) {
			unset($csv_array[0]);
			
			if (empty($csv_array)) {
				$errors[] = 'No Data Given';
			} else {
				foreach ($csv_array as $key => $row) {
					$row['row_num'] = $key + 1;
					$checking_account 		= $this->getValueCSV('Checking Account (yes/no)', $row, '', $errors, '');
					$values[] = array(
						'gl_code'			=> $this->getValueCSV('Bank Account GL Code', $row, 'required', $errors),
						'bankcode' 			=> $this->getValueCSV('Bank Code', $row, 'required', $errors),
						'shortname' 		=> $this->getValueCSV('Bank Name', $row, 'required', $errors),
						'accountno' 		=> $this->getValueCSV('Bank Account Number', $row, 'required text', $errors),
						'currency' 			=> $this->getValueCSV('Currency Code', $row, 'required text', $errors),
						// 'checking_account' 	=> $this->getValueCSV('Checking Account (yes/no)', $row, '', $errors),
						'address1' 			=> $this->getValueCSV('Bank Address', $row, 'required', $errors)
					);
					// echo $checking_account;
					if($checking_account != "" && (strtolower($checking_account) != "yes" && strtolower($checking_account) != "no")){
						$errors[0] = "The Checking Account in line " .$row['row_num']. " is invalid. Kindly use 'Yes' for Checking Accounts, otherwise 'No'.<br>";;
					}
				}
				$line = 1;
				$bankcode = $this->bank->checkGL();
				$name = array();
				foreach ($bankcode as $m) {
					$name[] = $m->segment5;
				}
				$currencycode = $this->bank->exchangeRate();
				$currency = array();
				foreach ($currencycode as $m) {
					$currency[] = strtolower($m->currencycode);
				}
				$checkglcode = $this->bank->checkExisting();
				$existing = array();
				foreach ($checkglcode as $m) {
					$existing[] = $m->gl_code;
				}

				foreach ($csv_array as $key => $check_row) {
					if(in_array($check_row[0], $existing)) {
						$errors[0] = "The GL Code you entered in line " .$line. " was being used by another bank<br>";
					}

					if(in_array($check_row[0],$name)){

					}else{
						$errors[0] = "The GL Account Code you entered in " .$line. " doesn't exist<br>";
					}
					if(in_array(strtolower($check_row[4]),$currency)){

					}else{
						$errors[0] = "Currency Code in " .$line. " you entered doesn't exist<br>";
					}
					$line++;	
				}

 				if (empty($errors)) {
					$result = $this->bank->saveUserCSV($values);
				}
			}
			// var_dump($checkglcode);
		} else {
			$errors[] = 'Invalid Import File. Please Use our Template for Uploading CSV';
		}

		$json = array(
			'success'	=> $result,
			'errors'	=> $errors
		);
		return $json;
	}

	private function getValueCSV($field, $array, $checker = '', &$errors = array(), $checker_function = '', $add_args = '') {
		$header = array("Bank Account GL Code","Bank Code","Bank Name","Bank Account Number","Currency Code","Checking Account (yes/no)", "Bank Address");
		$key	= array_search($field, $header);
		$value	= (isset($array[$key])) ? addslashes(implode('', explode("\\", trim(strip_tags($array[$key]))))) : '';
		if ($checker != '') {
			$checker_array = explode(' ', $checker);
			if (in_array('integer', $checker_array)) {
				$value = str_replace(',', '', $value);
				if ( ! preg_match('/^[0-9]*$/', $value)) {
					$errors[$array['row_num']][$field]['Not Integer'] = $value;
				}
			}
			if (in_array('decimal', $checker_array)) {
				$value = str_replace(',', '', $value);
				if ( ! preg_match('/^[0-9.]*$/', $value)) {
					$errors[$array['row_num']][$field]['Not Decimal'] = $value;
				}
			}
			if (in_array('alphanum', $checker_array)) {
				$value = str_replace(',', '', $value);
				if ( ! preg_match('/^[a-zA-Z0-9-_]*$/', $value)) {
					$errors[$array['row_num']][$field]['Not Alpha Numeric'] = $value;
				}
			}
			if (in_array('text', $checker_array)) {
				$value = str_replace(',', '', $value);
				if ( ! preg_match('/^[\\a-zA-Z0-9-_ !@#$%^&*()\/<>?,.{}:;=+\r\n"\'ñÑ]*$/u', $this->utf8ize($value))) {
					$errors[$array['row_num']][$field]['Unsupported Character'] = $value;
				}
			}
			if (in_array('email', $checker_array)) {
				$value = str_replace(',', '', $value);
				if ( ! preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $value)) {
					$errors[$array['row_num']][$field]['Invalid Email Format'] = $value;
				}
			}
			if (in_array('required', $checker_array)) {
				if ($value == '') {
					$errors[$array['row_num']][$field]['Required'] = $value;
				}
			}
		}
		if ($checker_function && $value != '') {
			$args = array($value);
			if ($add_args) {
				$key2		= array_search($add_args, $header);
				$new_arg	= (isset($array[$key2])) ? trim($array[$key2]) : '';
				$args[]		= $new_arg;
			}
			$result = call_user_func_array(array($this->member_model, $checker_function), $args);
			if ($result) {
				$value = $result[0]->ind;
			} else {
				$errors[$array['row_num']][$field]['Invalid Entry'] = $value;
			}
		}
		return $value;
	}

	public function utf8ize($d) {
		if (is_array($d)) {
			foreach ($d as $k => $v) {
				$d[$k] = $this->utf8ize($v);
			}
		} else if (is_string ($d)) {
			return utf8_encode($d);
		}
		return $d;
	}

	private function export()
	{
		$data_post = $this->input->post("search");
		$result = $this->bank->exportBank($this->fields, $data_post);

		$header = array("Bank Account GL Code","Bank Code","Bank Name","Bank Account Number","Currency Code","Checking Account (yes/no)", "Bank Address");

		$csv = '';
		$csv .= '"' . implode('","', $header) . '"';
		$csv .= "\n";

		if(!empty($result)){
			foreach ($result as $key => $row){
				$gl_code 		= $row->gl_code;
				$bankcode 		= $row->bankcode;
				$shortname 	= $row->shortname;
				$accountno		= $row->accountno;
				$currency        	= $row->currency;
				$checking_account = $row->checking_account;
				$address1 = $row->address1;


				$csv .= '"' . $gl_code . '",';
				$csv .= '"' . $bankcode	. '",';
				$csv .= '"' . $shortname	. '",';
				$csv .= '"' . $accountno	. '",';
				$csv .= '"' . $currency	. '",';
				$csv .= '"' . $checking_account	. '",';
				$csv .= '"' . $address1 	. '",';
				$csv .= "\n";
			}
		}
		return $csv;
	}
}
?>