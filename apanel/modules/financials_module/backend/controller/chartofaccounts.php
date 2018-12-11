<?php
class controller extends wc_controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->url 			= new url();
		$this->coaclass 	= new chartofaccountsclass();
		$this->input        = new input();
		$this->ui 			= new ui();
		$this->logs  		= new log; 
		$this->view->title  = MODULE_NAME;
		$this->show_input 	= true;
		$this->companycode  = COMPANYCODE;

	}

	public function create(){

		/**DEFAULT VALUES**/
		$data["accountcode"] = "";
		$data["accountname"] = "";
		$data["accountclasscode"] = "";
		$data["fspresentation"] = "";
		$data["accounttype"] = "";
		$data["parentaccountcode"] = "";
		$data["accountnature"] = "";
		$data["ajax_post"] = "";
		$data["ajax_task"] = "add";
		$this->view->title  = $this->ui->AddLabel('');
		/**RETRIEVALS OPTIONS**/
		/**ACCOUNT TYPE **/
		$data["accounttype_list"] = $this->coaclass->getValue("wc_option", 
			array("code ind","value val"), " type='accounttype' ", "",false,false);
		/**ACCOUNT CLASS **/
		// RETRIEVE ACCOUNT CODE
		$acc_entry_data     = array("accountclasscode ind","accountclass val");
		$account_entry_list = $this->coaclass->getValue("accountclass", 
			$acc_entry_data, "", "");

		$data['accountclasscode_list'] = $account_entry_list;

		/**FS PRESENTATION**/
		$data["fspresentation_list"] = $this->coaclass->getValue("wc_option", 
			array("code ind","value val"), " type='fspresentation' ", "",false,false);
		/**PARENT ACCOUNT**/
		$parentaccountArray	= $this->coaclass->getValue("chartaccount",
			array('segment5 ind','accountname val'),"accounttype != 'C'","accountname");

		$data["parentaccountcode_list"] = $parentaccountArray;

		/**ACCOUNT NATURE**/
		$data["accountnature_list"] = array(
			"Debit"=>"Debit",
			"Credit"=>"Credit");
		
		$data['ui'] = $this->ui;
		$data["task"] = "create";
		$data["button_name"] = "Save";
		$this->view->load('chartofaccounts/chartofaccounts',$data);
	}

	public function edit($accountcode = ""){
		$this->view->title  = $this->ui->EditLabel('');
		$data["ajax_task"] = "edit";
		$data["task"] = "edit";
		$data['ui'] = $this->ui;
		$data['sid'] = $accountcode;
		
		/**RETRIEVALS OPTIONS**/
		/**ACCOUNT TYPE **/
		$data["accounttype_list"] = $this->coaclass->getValue("wc_option", 
			array("code ind","value val"), " type='accounttype' ", "",false,false);
		/**ACCOUNT CLASS **/
		// RETRIEVE ACCOUNT CODE
		$acc_entry_data     = array("accountclasscode ind","accountclass val");
		$account_entry_list = $this->coaclass->getValue("accountclass", 
			$acc_entry_data, "", "");

		$data['accountclasscode_list'] = $account_entry_list;

		/**FS PRESENTATION**/
		$data["fspresentation_list"] = $this->coaclass->getValue("wc_option", 
			array("code ind","value val"), " type='fspresentation' ", "",false,false);
		/**PARENT ACCOUNT**/
		$parentaccountArray	= $this->coaclass->getValue("chartaccount",
			array('segment5 ind','accountname val'),"accounttype != 'C'","accountname");

		$data["parentaccountcode_list"] = $parentaccountArray;

		/**ACCOUNT NATURE**/
		$data["accountnature_list"] = array(
			"Debit"=>"Debit",
			"Credit"=>"Credit");

		$data["ajax_post"] 	 = "&companycode=".COMPANYCODE."&account_code=".$accountcode;
		$retdata = array('id','segment1','segment2','segment3','segment4','segment5',
			'accountname','accountclasscode','fspresentation','accounttype','parentaccountcode',
			'accountnature');
		$condition = " id = '".$accountcode."' ";

		$list = $this->coaclass->retrieveData($retdata,"","",$condition); 
		//var_dump($list);
		if( !empty($list->result) ) :
			for($i = 0; $i < count($list->result); $i++)
			{	
				$data["id"]          = $list->result[$i]->id;
				$data["accountcode"] = $list->result[$i]->segment5;
				$data["accountname"] = $list->result[$i]->accountname;
				$data["accountclasscode"] = $list->result[$i]->accountclasscode;
				$data["fspresentation"] = $list->result[$i]->fspresentation;
				$data["accounttype"] = $list->result[$i]->accounttype;
				$data["parentaccountcode"] = $list->result[$i]->parentaccountcode;
				$data["accountnature"] = $list->result[$i]->accountnature;
			}
		endif;

		$data["button_name"] = "Save";
		$this->view->load('chartofaccounts/chartofaccounts',$data);

	}

	public function view($accountcode = ""){
		$this->view->title  = $this->ui->ViewLabel('');
		$data["ajax_task"] = "view";
		$data["task"] = "view";
		$data['ui'] = $this->ui;
		$data['sid'] = $accountcode;
		/**RETRIEVALS OPTIONS**/
		/**ACCOUNT TYPE **/
		$data["accounttype_list"] = $this->coaclass->getValue("wc_option", 
			array("code ind","value val"), " type='accounttype' ", "",false,false);
		/**ACCOUNT CLASS **/
		// RETRIEVE ACCOUNT CODE
		$acc_entry_data     = array("accountclasscode ind","accountclass val");
		$account_entry_list = $this->coaclass->getValue("accountclass", 
			$acc_entry_data, "", "");

		$data['accountclasscode_list'] = $account_entry_list;

		/**FS PRESENTATION**/
		$data["fspresentation_list"] = $this->coaclass->getValue("wc_option", 
			array("code ind","value val"), " type='fspresentation' ", "",false,false);
		/**PARENT ACCOUNT**/
		$parentaccountArray	= $this->coaclass->getValue("chartaccount",
			array('segment5 ind','accountname val'),"accounttype != 'C'","accountname");

		$data["parentaccountcode_list"] = $parentaccountArray;

		/**ACCOUNT NATURE**/
		$data["accountnature_list"] = array(
			"Debit"=>"Debit",
			"Credit"=>"Credit");

		$data["ajax_post"] 	 = "&companycode=".COMPANYCODE."&account_code=".$accountcode;
		$retdata = array('id','segment1','segment2','segment3','segment4','segment5',
			'accountname','accountclasscode','fspresentation','accounttype','parentaccountcode',
			'accountnature');
		$condition = " id = '".$accountcode."' ";

		$list = $this->coaclass->retrieveData($retdata,"","",$condition);
		
		if( !empty($list->result) ) :
			for($i = 0; $i < count($list->result); $i++)
			{	
				$data["id"]          = $list->result[$i]->id;
				$data["accountcode"] = $list->result[$i]->segment5;
				$data["accountname"] = $list->result[$i]->accountname;
				$data["accountclasscode"] = $list->result[$i]->accountclasscode;
				$data["fspresentation"] = $list->result[$i]->fspresentation;
				$data["accounttype"] = $list->result[$i]->accounttype;
				$data["parentaccountcode"] = $list->result[$i]->parentaccountcode;
				$data["accountnature"] = $list->result[$i]->accountnature;
			}

		endif;
		$data["button_name"] = "Edit";
		$this->view->load('chartofaccounts/chartofaccounts',$data);

	}

	public function ajax($task)
	{
		header('Content-type: application/json');
		$result = "";
		if ($task == 'add'):
			$result = $this->add();
		elseif($task == 'edit'):
			$result = $this->update();
		elseif($task == "load_list"):
			$result = $this->load_list();
		elseif($task == "delete"):
			$result = $this->delete();
		elseif($task == "deleteMultiple"):
			$result = $this->deleteMultiple();
		elseif($task == 'export'):
			$result = $this->export();
		elseif($task == 'import'):
			$result = $this->import();
		elseif($task == 'save_import'):
			$result = $this->save_import();
		elseif($task == 'ajax_edit_activate'):
			$result = $this->ajax_edit_activate();
		elseif($task == 'ajax_edit_deactivate'):
			$result = $this->ajax_edit_deactivate();
		elseif($task == 'update_multiple_deactivate'):
			$result = $this->update_multiple_deactivate();
		elseif($task == 'update_multiple_activate'):
			$result = $this->update_multiple_activate();
		endif;
		echo json_encode($result);
	}

	private function load_list()
	{
		$search = $this->input->post("search");
		$sort 	= $this->input->post("sort");
		$limit 	= $this->input->post("limit");
		$addCond = stripslashes($this->input->post("addCond"));
		$fields = array("chart.id","chart.segment1","chart.segment2","chart.segment3","chart.segment4","chart.segment5","chart.accountname","chart.accountclasscode","chart.stat");
		$table = "";
		$list = $this->coaclass->retrieveData($fields,$search, $sort, $addCond, $limit); 

		if( !empty($list->result) ) :
			foreach($list->result as $key => $row)
			{
				$sid  = $row->id;
				$stat = $row->stat;
				if($stat == 'active'){
					$status = '<span class="label label-success">ACTIVE</span>';								
				}else{
					$status = '<span class="label label-warning">INACTIVE</span>';
				}

				$show_activate 		= ($stat != 'inactive');
				$show_deactivate 	= ($stat != 'active');

				$dropdown = $this->ui->loadElement('check_task')
				->addView()
				->addEdit()
				->addOtherTask(
					'Activate',
					'arrow-up',
					$show_deactivate
				)
				->addOtherTask(
					'Deactivate',
					'arrow-down',
					$show_activate
				)	
				->addDelete()
				->addCheckbox()
				->setValue($sid)
				->draw();
				$table .= '<tr>
				<td align = "center">'.$dropdown.'</td>
				<td>'.$row->segment5.'</td>
				<td>'.$row->accountname.'</td>
				<td>'.$row->accountclasscode.'</td>
				<td>'.$status.'</td>'; 
				'</tr>';			
			}
		else:
			$table .= "<tr>
			<td colspan = '3' class = 'text-center'>No Records Found</td>
			</tr>";
		endif;

		$dataArray = array( "table" => $table, "pagination" => $list->pagination, "csv" => $this->export());
		return $dataArray;
		
	}

	public function listing()
	{
		// $data['import_error_messages'] = array();
		// $data["file_import_result"]    = "";
		$data['ui'] = $this->ui;
		$this->view->title  = $this->ui->ListLabel('');

		// // For Import
		// $errmsg 			= array();

		// if(isset($_FILES['import_csv']))
		// {
		// 	$headerArray	= array('Account Code','Account Name','Account Class','FS Presentation','Account Type','Parent Account','Account Nature');
		
		// 	$file_types = array( "text/comma-separated-values", "text/csv", "application/csv", 
		// 					"application/excel", "application/vnd.ms-excel", 
		// 					"application/vnd.msexcel", "text/anytext");

		// 	if(!in_array($_FILES['import_csv']['type'],$file_types))
		// 	{
		// 		$errmsg[]	= "Invalid file type, file must be CSV(Comma Separated Values) File.<br/>";
		// 	}

		// 	/**VALIDATE FILE IF CORRUPT**/
		// 	if(!empty($_FILES['import_csv']['error']))
		// 	{
		// 		$errmsg[] = "File being uploaded is corrupted.<br/>";
		// 	}

		// 	$file		= fopen($_FILES['import_csv']['tmp_name'],"r");

		// 	// Validate File Contents
		// 	$docData	= array();
		// 	$i			= 0;
		// 	$row		= 2;

		// 	$accountcodes	= array();

		// 	while (($file_data = fgetcsv($file, 1000, ",")) !== FALSE) 
		// 	{
		// 		if(!array_intersect($file_data, $headerArray))
		// 		{
		// 			$accountcode	= addslashes(htmlentities(trim($file_data[0])));
		// 			$accountname	= addslashes(htmlentities(trim($file_data[1])));
		// 			$accountclass	= addslashes(htmlentities(trim($file_data[2])));
		// 			$fspresentation	= addslashes(htmlentities(trim($file_data[3])));
		// 			$accounttype	= addslashes(htmlentities(trim($file_data[4])));
		// 			$parentaccount	= addslashes(htmlentities(trim($file_data[5])));
		// 			$accountnature	= addslashes(htmlentities(trim($file_data[6])));

		// 			if (in_array($accountcode, $accountcodes)) {
		// 				$errmsg[] 	= "Account Code is already in List. Row $row already Exist.";
		// 			} else {
		// 				$accountcodes[] = $accountcode;
		// 			}

		// 			if(empty($accountcode)){
		// 				$errmsg[] 	= "Account Code is required. Row $row should not be empty.";
		// 			}

		// 			if(empty($accountname)){
		// 				$errmsg[] 	= "Account Name is required. Row $row should not be empty.";
		// 			}

		// 			if(empty($accountclass)){
		// 				$errmsg[] 	= "Account Class is required. Row $row should not be empty.";
		// 			}

		// 			if(empty($fspresentation)){
		// 				$errmsg[] 	= "FS Presentation is required. Row $row should not be empty.";
		// 			}

		// 			if(empty($parentaccount) && ($accounttype != 'Parent' && $accounttype != 'P')){
		// 				$errmsg[] 	= "Parent Account is required for accounts with Child or Both account type. Row $row should not be empty.";
		// 			}

		// 			if(empty($accountnature)){
		// 				$errmsg[] 	= "Account Nature is required. Row $row should not be empty.";
		// 			}

		// 			if(empty($accounttype)){
		// 				$errmsg[] 	= "Account Type is required. Row $row should not be empty.";
		// 			}

		// 			/**VALIDATE ACCOUNT CODE**/
		// 			$isaccountcode	= $this->coaclass->getValue("chartaccount",array("segment5"),
		// 							" segment5 = '$accountcode' ");

		// 			if(isset($isaccountcode[0]->segment5) && !empty($isaccountcode[0]->segment5)){
		// 				$errmsg[] 	= "Account Code [ <strong>$accountcode</strong> ] on row $row already exists.";
		// 			}

		// 			/**VALIDATE ACCOUNT CLASS**/
		// 			$isaccountclass	= $this->coaclass->getValue("accountclass", array("accountclasscode"), " accountclasscode = '$accountclass' ");

		// 			if(empty($isaccountclass)){
		// 				$errmsg[] 	= "Account Class [ <strong>$accountclass</strong> ] on row $row is not a valid value.";
		// 			}

		// 			/**VALIDATE FS PRESENTATION**/
		// 			$isfspresentation	= $this->coaclass->getValue("wc_option",array("code"),
		// 							" code = '$fspresentation' AND type = 'fs_presentation' ","",false);

		// 							if(isset($isfspresentation[0]->code) && empty($isfspresentation[0]->code)){
		// 				$errmsg[] 	= "FS Presentation [ <strong>$fspresentation</strong> ] on row $row is not a valid value.";
		// 			}

		// 			/**VALIDATE ACCOUNT TYPE**/
		// 			$isaccounttype	= $this->coaclass->getValue("wc_option",array("code"),
		// 							" code = '$accounttype' AND type = 'accounttype' ","",false);
		// 			if(isset($isaccounttype[0]->code) && empty($isaccounttype[0]->code)){
		// 				$errmsg[] 	= "Account Type [ <strong>$accounttype</strong> ] on row $row 
		// 							  is not a valid value.";
		// 			}

		// 			/**VALIDATE ACCOUNT NATURE**/
		// 			$natureArray	= array('Debit','debit','Credit','credit');
		// 			if(!in_array($accountnature,$natureArray)){
		// 				$errmsg[] 	= "Account Nature [ <strong>$accountnature</strong> ] on row $row is not a valid value.";
		// 			}

		// 			/**ASSIGN TO NEW ARRAY**/
		// 			$docData[$i]['segment5'] 			= $accountcode;
		// 			$docData[$i]['accountname']     	= $accountname;
		// 			$docData[$i]['accountclasscode']	= $accountclass;
		// 			$docData[$i]['fspresentation']  	= $fspresentation;
		// 			$docData[$i]['accounttype']			= $accounttype;
		// 			$docData[$i]['parentaccountcode']	= $parentaccount;
		// 			$docData[$i]['accountnature']   	= $accountnature;

		// 			$i++;
		// 			$row++;
		// 		}
		// 	}

		// 	$errmsg				           = array_filter($errmsg);
		// 	$data['import_error_messages'] = $errmsg;

		// 	// Insert File Data
		// 	if(!empty($docData) && empty($errmsg))
		// 	{
		// 		 $file_import_result = $this->coaclass->saveImport($docData);
		// 		 $data["file_import_result"] = $file_import_result;
		// 	}
		// }

		$this->view->load('chartofaccounts/chartofaccounts_list',$data);
	}

	private function save_import(){

		$file		= fopen($_FILES['file']['tmp_name'],'r') or exit ("File Unable to upload") ;

		$filedir	= $_FILES["file"]["tmp_name"];

		$file_types = array( "text/x-csv","text/tsv","text/comma-separated-values", "text/csv", "application/csv", "application/excel", "application/vnd.ms-excel", "application/vnd.msexcel", "text/anytext", "application/octet-stream");

		/**VALIDATE FILE IF CORRUPT**/
		if(!empty($_FILES['file']['error'])){
			$errmsg[] = "File being uploaded is corrupted.<br/>";
		}

		/**VALIDATE FILE TYPE**/
		if(!in_array($_FILES['file']['type'],$file_types)){
			$errmsg[]= "Invalid file type, file must be .csv.<br/>";
		}

		$headerArr = array('Account Code','Account Name', 'Account Class', 'FS Presentation (BS or IS)', 'Account Type', 'Parent Account', 'Account Nature');

		if( empty($errmsg) )
		{
			$row_start = 2;
				//$x = file_get_contents($_FILES['file']['tmp_name']);
			$x = array_map('str_getcsv', file($_FILES['file']['tmp_name']));

			for ($n = 0; $n < count($x); $n++) {
				if($n==0)
				{
					$layout = count($headerArr);
					$template = count($x);
					$header = $x[$n];

					for ($m=0; $m< $layout; $m++)
					{
						$template_header = $header[$m];

						$error = (empty($template_header) && !in_array($template_header,$headerArr)) ? "error" : "";
					}	

					$errmsg[]	= (!empty($error) || $error != "" ) ? "Invalid template. Please download template from the system first.<br/>" : "";

					$errmsg		= array_filter($errmsg);

				}

				if ($n > 0) 
				{
					$z[] = $x[$n];
				}
			}

			$line 	=	1;
			$list 	=	array();

			foreach ($z as $b) 
			{
				if ( !empty($b)) 
				{	
					$accountcode 	   	= $b[0];
					$accountname 	   	= $b[1];
					$accountclass 	   	= $b[2];
					$fspresentation   	= $b[3];
					$accounttype		= $b[4];
					$parentaccount		= $b[5];
					$accountnature		= $b[6];

					$exists = $this->coaclass->check_duplicate($accountcode);
					$count = $exists[0]->count;
					
					if( $count > 0 )
					{
						$errmsg[]	= "Account Code [<strong>$accountcode</strong>] on row $line already exists.<br/>";
						$errmsg		= array_filter($errmsg);
					}
					if( !in_array($accountcode, $list) ){
						$list[] 	=	$accountcode;
					}else 
					{
						$errmsg[]	= "Account Code [<strong>$accountcode</strong>] on row $line has a duplicate within the document.<br/>";
					}
					
					if(empty($accountcode)){
						$errmsg[] 	= "Account Code is required. Row $line should not be empty.<br>";
					}

					if(empty($accountname)){
						$errmsg[] 	= "Account Name is required. Row $line should not be empty.<br>";
					}

					if(empty($accountclass)){
						$errmsg[] 	= "Account Class is required. Row $line should not be empty.<br>";
					}
					
					if(empty($fspresentation)){
						$errmsg[] 	= "FS Presentation is required. Row $line should not be empty.<br>";
					}
					if(empty($accounttype)){
						$errmsg[] 	= "Account Type is required. Row $line should not be empty.<br>";
					}
					if(empty($accountnature)){
						$errmsg[] 	= "Account Nature is required. Row $line should not be empty.<br>";
					}		

					if($fspresentation == 'Balance Sheet' || $fspresentation == 'Income Statement') {
						$errmsg[]	= "Invalid FS Presenation on Row $line. Kindly use 'IS' for Income Statement and 'BS' for Balance Sheet<br/>";
					}		
					
					$accountcode_[] 	= $accountcode;
					$accountname_[] 	= addslashes($accountname);
					$accountclass_[] 	= $accountclass;
					$fspresentation_[]	= $fspresentation;
					$accounttype_[] 	= $accounttype;
					$parentaccount_[] 	= $parentaccount;
					$accountnature_[] 	= $accountnature;

					$line++;
				}
			}
			$proceed 	=	false;

			if( empty($errmsg) )
			{
				$post = array(
					'segment5'				=> $accountcode_,
					'accountname'			=> $accountname_,
					'accountclasscode'		=> $accountclass_,
					'fspresentation'		=> $fspresentation_,
					'accounttype'			=> $accounttype_,
					'parentaccountcode'		=> $parentaccount_,
					'accountnature'			=> $accountnature_
				);
				
				$proceed  				= $this->coaclass->importCOA($post);

				if( $proceed )
				{
					$this->logs->saveActivity("Imported Chart of Account.");
				}
			}
		}

		$error_messages		= implode(' ', $errmsg);

		return array("proceed" => $proceed,"errmsg"=>$error_messages);
	}

	private function add()
	{
		$data_var = array(
			'accountcode'=>'segment5',
			'accountname',
			'accountclasscode',
			'fspresentation',
			'accounttype',
			'parentaccountcode',
			'accountnature'
		);

		$data = $this->input->post($data_var);

		$result = $this->coaclass->insertData($data);
		
		if($result)
			$msg = "success";
		else
			$msg = "error_add";

		$dataArray = array("msg" => $msg, "account_code" => $data["segment5"], 
			"account_name" => $data["accountname"]);
		return $dataArray;

	}

	public function update()
	{	
		$data_var = array(
			'accountcode'=>'segment5',
			'accountname',
			'accountclasscode',
			'fspresentation',
			'accounttype',
			'parentaccountcode',
			'accountnature'
		);

		$data_var2 = array(
			'account_code'
		);

		$data = $this->input->post($data_var);
		$data2 = $this->input->post($data_var2);
		extract($data2);

		// /**
		// * Update Database
		// */
		$result = $this->coaclass->editData($data, $account_code);

		if($result)
			$msg = "success";

		$dataArray = array( "msg" => $msg );
		return  $dataArray;
	}

	public function delete()
	{
		$data_var = array(
			'id'
		);
		$data = $this->input->post($data_var);
		extract($data);

		$data_var = array('id');
		$id       = $this->input->post($data_var);

		/**
		* Delete Database
		*/
		$result = $this->coaclass->deleteData($id);

		$dataArray = array( "msg" => $result );
		return $dataArray;
	}

	private function export()
	{
		$data_post = $this->input->post(array("addCond", "search"));

		$result = $this->coaclass->fileExport($data_post);

		$header = array("Account Code","Account Name","Account Class","FS Presentation",
			"Account Type","Parent Account","Account Nature");
		
		$csv = '';
		$csv .= '"' . implode('","', $header) . '"';
		$csv .= "\n";
		
		$retrieved 	=	array_filter($result);

		if(!empty($retrieved)){
			foreach ($retrieved as $key => $row){
				$accountcode 		= $row->segment5;
				$accountname 		= $row->accountname;
				$accountclass       = $row->accountclasscode;
				$fspresentation     = $row->fspresentation;
				$accounttype    	= $row->accounttype;
				$parentaccount      = $row->parentaccountcode;
				$accountnature      = $row->accountnature;	

				$csv .= '"' . $accountcode 		. '",';
				$csv .= '"' . $accountname 		. '",';
				$csv .= '"' . $accountclass 	. '",';
				$csv .= '"' . $fspresentation	. '",';
				$csv .= '"' . $accounttype		. '",';
				$csv .= '"' . $parentaccount 	. '",';
				$csv .= '"' . $accountnature 	. '"';
				$csv .= "\n";
			}
		}

		return $csv;
	}

	private function ajax_edit_activate()
	{
		$code = $this->input->post('id');
		$data['stat'] = 'active';

		$result = $this->coaclass->updateStat($data,$code);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}
	
	private function ajax_edit_deactivate()
	{
		$code = $this->input->post('id');
		$data['stat'] = 'inactive';

		$result = $this->coaclass->updateStat($data,$code);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function update_multiple_deactivate(){
		$posted_data 			=	$this->input->post(array('ids'));

		$data['stat'] 			=	'inactive';
		
		$posted_ids 			=	$posted_data['ids'];
		$id_arr 				=	explode(',',$posted_ids);
		
		foreach($id_arr as $key => $value)
		{
			$result 			= 	$this->coaclass->updateStat($data, $value);
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
			$result 			= 	$this->coaclass->updateStat($data, $value);
		}

		if($result)
		{
			$msg = "success";
		} else {
			$msg = "Failed to Update.";
		}

		return $dataArray = array( "msg" => $msg );
	}

}