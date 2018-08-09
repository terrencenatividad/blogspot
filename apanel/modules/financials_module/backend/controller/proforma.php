<?php
class controller extends wc_controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->url 				= new url();
		$this->proformaclass 	= new proformaclass();
		$this->input        	= new input();
		$this->ui 				= new ui();
		$this->logs  			= new log;
		$this->view->title  	= MODULE_NAME;
		$this->show_input 	    = true;

		$this->companycode  	= COMPANYCODE;
	}

	public function create()
	{
		/**DEFAULT VALUES**/
		$data["proformacode"] 		= "";
		$data["proformadesc"] 		= "";
		$data["transactiontype"] 	= "";
		$data["companycode"] 		= "";
		$data["button_color"] 		= "btn-info";
		$data["ajax_post"] 			= "";
		$data["ajax_task"] 			= "add";
		$this->view->title  		= $this->ui->AddLabel('');
		$data["cmp"] 				= COMPANYCODE;

		/**RETRIEVALS OPTIONS**/

		/**FINANCIALS TYPE OPTIONS**/
		$data["financialtype_list"] = $this->proformaclass->getValue("wc_option", 
		array("code ind","value val"), " type='financials_type' ", "",false,false);

		/**ACCOUNT CODE OPTIONS **/
		$account_code_options = $this->proformaclass->getValue("chartaccount", 
								  array("id ind","accountname val"), "", "segment5");
		$data['accountcodeoption_list'] = $account_code_options;

		$data['ui'] = $this->ui;
		$data["task"] = "create";
		$this->view->load('proforma/proforma',$data);
	}

	public function edit($accountcode = ""){

		
		$this->view->title  = $this->ui->EditLabel('');
		$data["button_color"] = "btn-warning";
		$data["ajax_task"] = "edit";
		$data["ajax_post"] = "";
		$data["sid"] = $accountcode;
		$data["cmp"] = $this->companycode;

		/**RETRIEVALS OPTIONS**/

		/**FINANCIALS TYPE OPTIONS**/
		$data["financialtype_list"] = $this->proformaclass->getValue("wc_option", 
		array("code ind","value val"), " type='financials_type' ", "",false,false);

		/**ACCOUNT CODE OPTIONS **/
		$account_code_options = $this->proformaclass->getValue("chartaccount", 
								  array("id ind","accountname val"), "", "segment5");
		$data['accountcodeoption_list'] = $account_code_options;

		
		$retdata = array('proformacode','proformadesc','transactiontype','companycode');
		$condition = " proformacode = '$accountcode' ";
		$list = $this->proformaclass->retrieveData($retdata,"","",$condition); 
		
		if( !empty($list->result) ) :
			for($i = 0; $i < count($list->result); $i++)
			{	
				$data["proformacode"] 	  = $list->result[$i]->proformacode;
				$data["proformadesc"] 	  = $list->result[$i]->proformadesc;
				$data["transactiontype"]  = $list->result[$i]->transactiontype;
				$data["companycode"]      = $list->result[$i]->companycode;
			}

		endif;

		//retrieve proforma details
		$retDataDetails		= array("proformacode","accountcodeid","accountname");
		$retDetailsCondition = "proformacode = '$accountcode' ";	
		$data["detailList"] = $this->proformaclass->retrieveProformaDetails($retDataDetails,$retDetailsCondition);

		$data["proformacode_"] = $accountcode;
		$data['ui']   = $this->ui;

		$data['ui']   = $this->ui;
		$data["task"] = "edit";
		$this->view->load('proforma/proforma',$data);

	}

	public function view($accountcode = ""){

		$this->view->title  = $this->ui->ViewLabel('');
		$data["ajax_task"]  = "view";
		$data["sid"]        = $accountcode;
		$data["cmp"] = $this->companycode;
		$data["ajax_post"]  = "";

		/**RETRIEVALS OPTIONS**/

		/**FINANCIALS TYPE OPTIONS**/
		$data["financialtype_list"] = $this->proformaclass->getValue("wc_option", 
		array("code ind","value val"), " type='financials_type' ", "",false,false);

		/**ACCOUNT CODE OPTIONS **/
		$account_code_options = $this->proformaclass->getValue("chartaccount", 
								  array("id ind","accountname val"), "", "segment5");
		$data['accountcodeoption_list'] = $account_code_options;
		
		$retdata = array('proformacode','proformadesc','transactiontype','companycode');
		$condition = " proformacode = '$accountcode' ";
		$list = $this->proformaclass->retrieveData($retdata,"","",$condition); 
		
		if( !empty($list->result) ) :
			for($i = 0; $i < count($list->result); $i++)
			{	
				$data["proformacode"] 	  = $list->result[$i]->proformacode;
				$data["proformadesc"] 	  = $list->result[$i]->proformadesc;
				$data["transactiontype"]  = $list->result[$i]->transactiontype;
				$data["companycode"]      = $list->result[$i]->companycode;
			}

		endif;

		//retrieve proforma details
		$retDataDetails		= array("proformacode","accountcodeid","accountname");
		$retDetailsCondition = "proformacode = '$accountcode' ";	
		$data["detailList"] = $this->proformaclass->retrieveProformaDetails($retDataDetails,$retDetailsCondition);

		$data["proformacode_"] = $accountcode;
		$data['ui']   = $this->ui;
		$data["task"] = "view";
		$this->view->load('proforma/proforma',$data);

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
		elseif($task == 'ajax_edit_activate'):
			$result = $this->ajax_edit_activate();
		elseif($task == 'ajax_edit_deactivate'):
			$result = $this->ajax_edit_deactivate();
		endif;

		echo json_encode($result);
	}

	private function load_list()
	{
		$search = $this->input->post("search");
		$sort 	= $this->input->post("sort");
		$addCond = stripslashes($this->input->post("addCond"));
		$fields = array('proformacode','proformadesc','companycode,stat');
		$table = "";
		$list = $this->proformaclass->retrieveData($fields,$search, $sort, $addCond); 
	
			if( !empty($list->result) ) :
			foreach($list->result as $key => $row)
			{
				$proformacode = $row->proformacode;
				$proformadesc = $row->proformadesc;
				$companycode  = $row->companycode;
				$stat = $row->stat;
				if($stat == 'active'){
					$status = '<span class="label label-success">ACTIVE</span>';								
				}else{
					$status = '<span class="label label-danger">INACTIVE</span>';
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
									->setValue($proformacode)
									->draw();
				$table .= '<tr>
							<td align = "center">'.$dropdown.'</td>
							<td>'.$proformacode.'</td>
							<td>'.$proformadesc.'</td>
							<td align = "center">'.$status.'</td>
						</tr>';			
			}
		else:
			$table .= "<tr>
							<td colspan = '3' class = 'text-center'>No Records Found</td>
					  </tr>";
		endif;

		$dataArray = array( "table" => $table, "pagination" => $list->pagination , "csv" => $this->export());
		return $dataArray;
		
	}

	public function listing()
	{
		$data['import_error_messages'] = array();
		$data["file_import_result"]    = "";
		$data['ui'] = $this->ui;
		$this->view->title  = $this->ui->ListLabel('');

		// For Import
		$errmsg 			= array();
		if(isset($_FILES['import_csv']))
		{
			$headerArray	= array('Proforma Code','Description','Transaction Type','Account Name','Account Code Id');
		
			$file_types = array( "text/comma-separated-values", "text/csv", "application/csv", 
							"application/excel", "application/vnd.ms-excel", 
							"application/vnd.msexcel", "text/anytext");

			// Validate File Type
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
					$proformaCode	= addslashes(htmlentities(trim($file_data[0])));
					$proformaDesc	= addslashes(htmlentities(trim($file_data[1])));
					$transactionType= addslashes(htmlentities(trim($file_data[2])));
					$accountName	= addslashes(htmlentities(trim($file_data[3])));
					$accountCodeid	= addslashes(htmlentities(trim($file_data[4])));

					
					/**CHECK IF DOCUMENT SET IS EMPTY**/
					if(empty($proformaCode)){
						$errmsg[] 	= "Document Set on row $row should not be empty.";
					}
		

					/**VALIDATE ITEM**/
					$proformaCodes	= $this->proformaclass->getValue("proforma",array("proformacode"),
						" proformacode = '$proformaCode' AND stat='active' ");

					if(isset($proformaCodes[0]->proformacode) && !empty($proformaCodes[0]->proformacode) ){
						$errmsg[] 	= "Proforma Code [ <strong>".stripslashes($proformaCode)."</strong> ] 
									  on row $row does already exist.";
					}

					/**ASSIGN TO NEW ARRAY**/
					$docData[$proformaCode][$i]["proformacode"]    = $proformaCode;
					$docData[$proformaCode][$i]["proformadesc"]    = $proformaDesc;
					$docData[$proformaCode][$i]["transactiontype"] = $transactionType;
					$docData[$proformaCode][$i]["accountname"] 	   = $accountName;
					$docData[$proformaCode][$i]["accountcodeid"]   = $accountCodeid;

					$i++;
					$row++;
				}
			}

			$errmsg				           = array_filter($errmsg);
			$data['import_error_messages'] = $errmsg;

			// Insert File Data
			if(!empty($docData) && empty($errmsg))
			{
				 $file_import_result = $this->proformaclass->saveImport($docData);
				 $data["file_import_result"] = $file_import_result;
			}
		}
		// End Import

		$this->view->load('proforma/proforma_list',$data);
	}

	private function add()
	{

		$data_var = array(
					'proformacode',
					'proformadesc',
					'financialtype'=>'transactiontype',
					'accountcodeid',
					'costcentercode'
				);
		$data = $this->input->post($data_var);
	
		$result = $this->proformaclass->insertData($data);
		
		if($result)
			$msg = "success";
		else
			$msg = "error_add";

		$dataArray = array("msg" => $msg, "proforma_code" => $data["proformacode"], 
										  "proforma_desc" => $data["proformadesc"]);
		
		return $dataArray;

	}
	
	private function update()
	{	
		$data_var = array(
					'sid'=>'proformacode',
					'proformadesc',
					'financialtype'=>'transactiontype',
					'accountcodeid'
				);
		$data = $this->input->post($data_var);
		
		// /**
		// * Update Database
		// */
		$result = $this->proformaclass->editData($data);

		 if($result)
			$msg = "success";

		$dataArray = array( "msg" => $msg );
		return  $dataArray;
	}

	private function delete()
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
		$result = $this->proformaclass->deleteData($id);

		$dataArray = array( "msg" => $result );
		return $dataArray;
	}

	private function export()
	{
		$data_post = $this->input->get(array("search"));
		$result = $this->proformaclass->fileExport($data_post);

		$header = array('Proforma Code','Description','Transaction Type','Account Name','Account Code Id');
		
		$csv = '';
		$csv .= '"' . implode('","', $header) . '"';
		$csv .= "\n";
		
		$retrieved 	=	array_filter($result);

		if(!empty($retrieved)){
			foreach ($retrieved as $key => $row){
				$proformacode 		= $row->proformacode;
				$proformadesc 		= $row->proformadesc;
				$transactiontype    = $row->transactiontype;
				$accountname     	= $row->accountname;
				$accountid    	    = $row->accountcodeid;

				$csv .= '"' . $proformacode 	. '",';
				$csv .= '"' . $proformadesc 	. '",';
				$csv .= '"' . $transactiontype 	. '",';
				$csv .= '"' . $accountname		. '",';
				$csv .= '"' . $accountid		. '"';
				$csv .= "\n";
			}
		}

		return $csv;
	}

	private function delete_row()
	{
		$data_var  = array('table', "condition");
		$data_post = $this->input->post($data_var);

		/**
		* Delete Database
		*/
		$result = $this->proformaclass->deleteData($data_post);

		if($result)
			$msg = "success";
		else
			$msg = "The system has encountered an error in deleting. 
			Please contact admin to fix this issue.";

		$dataArray = array( "msg" => $msg );
		return $dataArray;
	}

	private function ajax_edit_activate()
	{
		$code = $this->input->post('id');
		$data['stat'] = 'active';

		$result = $this->proformaclass->updateStat($data,$code);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
			);
	}
	
	private function ajax_edit_deactivate()
	{
		$code = $this->input->post('id');

		$data['stat'] = 'inactive';

		$result = $this->proformaclass->updateStat($data,$code);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
			);
	}

}