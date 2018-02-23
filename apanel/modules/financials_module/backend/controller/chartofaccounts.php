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
		$this->view->title  = 'Account Class';
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
			$this->view->title  = 'Add New Account';

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
		$this->view->title  = 'Edit Account';
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
		$this->view->title  = 'View Account'; 
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
		endif;
		echo json_encode($result);
	}

	private function load_list()
	{
		$search = $this->input->post("search");
		$sort 	= $this->input->post("sort");
		$limit 	= $this->input->post("limit");
		$addCond = stripslashes($this->input->post("addCond"));
		$fields = array("chart.id","chart.segment1","chart.segment2","chart.segment3","chart.segment4","chart.segment5","chart.accountname","chart.accountclasscode");
		$table = "";
		$list = $this->coaclass->retrieveData($fields,$search, $sort, $addCond, $limit); 
	
			if( !empty($list->result) ) :
			foreach($list->result as $key => $row)
			{
				$sid = $row->id;

				$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit()
									->addDelete()
									->addCheckbox()
									->setValue($sid)
									->draw();
				$table .= '<tr>
							<td align = "center">'.$dropdown.'</td>
							<td>'.$row->segment5.'</td>
							<td>'.$row->accountname.'</td>
							<td>'.$row->accountclasscode.'</td>'; 
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
		$data['import_error_messages'] = array();
		$data["file_import_result"]    = "";
		$data['ui'] = $this->ui;
		$this->view->title  = 'Chart of Accounts';

		// For Import
		$errmsg 			= array();

		if(isset($_FILES['import_csv']))
		{
			$headerArray	= array('Account Code','Account Name','Account Class','FS Presentation','Account Type','Parent Account','Account Nature');
		
			$file_types = array( "text/comma-separated-values", "text/csv", "application/csv", 
							"application/excel", "application/vnd.ms-excel", 
							"application/vnd.msexcel", "text/anytext");

			if(!in_array($_FILES['import_csv']['type'],$file_types))
			{
				$errmsg[]	= "Invalid file type, file must be CSV(Comma Separated Values) File.<br/>";
			}

			/**VALIDATE FILE IF CORRUPT**/
			if(!empty($_FILES['import_csv']['error']))
			{
				$errmsg[] = "File being uploaded is corrupted.<br/>";
			}

			$file		= fopen($_FILES['import_csv']['tmp_name'],"r");

			// Validate File Contents
			$docData	= array();
			$i			= 0;
			$row		= 2;

			while (($file_data = fgetcsv($file, 1000, ",")) !== FALSE) 
			{
				if(!array_intersect($file_data, $headerArray))
				{
					$accountcode	= addslashes(htmlentities(trim($file_data[0])));
					$accountname	= addslashes(htmlentities(trim($file_data[1])));
					$accountclass	= addslashes(htmlentities(trim($file_data[2])));
					$fspresentation	= addslashes(htmlentities(trim($file_data[3])));
					$accounttype	= addslashes(htmlentities(trim($file_data[4])));
					$parentaccount	= addslashes(htmlentities(trim($file_data[5])));
					$accountnature	= addslashes(htmlentities(trim($file_data[6])));

					if(empty($accountcode)){
						$errmsg[] 	= "Account Code is required. Row $row should not be empty.";
					}
					
					if(empty($accountname)){
						$errmsg[] 	= "Account Name is required. Row $row should not be empty.";
					}

					if(empty($accountclass)){
						$errmsg[] 	= "Account Class is required. Row $row should not be empty.";
					}

					if(empty($fspresentation)){
						$errmsg[] 	= "FS Presentation is required. Row $row should not be empty.";
					}
					
					if(empty($parentaccount) && ($accounttype != 'Parent' && $accounttype != 'P')){
						$errmsg[] 	= "Parent Account is required for accounts with Child or Both account type. Row $row should not be empty.";
					}

					if(empty($accountnature)){
						$errmsg[] 	= "Account Nature is required. Row $row should not be empty.";
					}

					if(empty($accounttype)){
						$errmsg[] 	= "Account Type is required. Row $row should not be empty.";
					}
			
					/**VALIDATE ACCOUNT CODE**/
					$isaccountcode	= $this->coaclass->getValue("chartaccount",array("segment5"),
									" segment5 = '$accountcode' ");
									
					if(isset($isaccountcode[0]->segment5) && !empty($isaccountcode[0]->segment5)){
						$errmsg[] 	= "Account Code [ <strong>$accountcode</strong> ] on row $row already exists.";
					}

					/**VALIDATE ACCOUNT CLASS**/
					$isaccountclass	= $this->coaclass->getValue("accountclass", array("accountclasscode"), " accountclasscode = '$accountclass' ");

					if(empty($isaccountclass)){
						$errmsg[] 	= "Account Class [ <strong>$accountclass</strong> ] on row $row is not a valid value.";
					}

					/**VALIDATE FS PRESENTATION**/
					$isfspresentation	= $this->coaclass->getValue("wc_option",array("code"),
									" code = '$fspresentation' AND type = 'fs_presentation' ","",false);
					
									if(isset($isfspresentation[0]->code) && empty($isfspresentation[0]->code)){
						$errmsg[] 	= "FS Presentation [ <strong>$fspresentation</strong> ] on row $row is not a valid value.";
					}

					/**VALIDATE ACCOUNT TYPE**/
					$isaccounttype	= $this->coaclass->getValue("wc_option",array("code"),
									" code = '$accounttype' AND type = 'accounttype' ","",false);
					if(isset($isaccounttype[0]->code) && empty($isaccounttype[0]->code)){
						$errmsg[] 	= "Account Type [ <strong>$accounttype</strong> ] on row $row 
									  is not a valid value.";
					}

					/**VALIDATE ACCOUNT NATURE**/
					$natureArray	= array('Debit','debit','Credit','credit');
					if(!in_array($accountnature,$natureArray)){
						$errmsg[] 	= "Account Nature [ <strong>$accountnature</strong> ] on row $row is not a valid value.";
					}

					/**ASSIGN TO NEW ARRAY**/
					$docData[$i]['segment5'] 			= $accountcode;
					$docData[$i]['accountname']     	= $accountname;
					$docData[$i]['accountclasscode']	= $accountclass;
					$docData[$i]['fspresentation']  	= $fspresentation;
					$docData[$i]['accounttype']			= $accounttype;
					$docData[$i]['parentaccountcode']	= $parentaccount;
					$docData[$i]['accountnature']   	= $accountnature;

					$i++;
					$row++;
				}
			}
			
			$errmsg				           = array_filter($errmsg);
			$data['import_error_messages'] = $errmsg;

			// Insert File Data
			if(!empty($docData) && empty($errmsg))
			{
				 $file_import_result = $this->coaclass->saveImport($docData);
				 $data["file_import_result"] = $file_import_result;
			}
		}

		$this->view->load('chartofaccounts/chartofaccounts_list',$data);
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

}