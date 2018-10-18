<?php
	class controller extends wc_controller 
	{
		public function __construct()
		{
			parent::__construct();
			$this->url 			= new url();
			$this->atc_code 	= new atccode_class();
			$this->input        = new input();
			$this->ui 			= new ui();
			$this->logs  		= new log; 
			$this->view->title  = MODULE_NAME;
			$this->show_input 	= true;
			$this->companycode  = COMPANYCODE;

		}

		public function create(){

			/**DEFAULT VALUES**/
			$data["atc_code"] = "";
			$data["tax_rate"] = "";
			$data["wtaxcode"] = "";
			$data["short_desc"] = "";
			$data["tax_account"] = "";
			$data["ajax_post"] = "";
			$data["ajax_task"] = "add";
			$this->view->title  = $this->ui->AddLabel('');
			$data['ui'] = $this->ui;
			$data["task"] = "create";
			$data["button_name"] = "Save";

			/**TAX ACCOUNT**/
			$data["account_list"] = $this->atc_code->getValue("chartaccount", 
			array("id ind","CONCAT(segment5, ' - ', accountname) val"), " accountclasscode IN('TAX','CULIAB','OTHCL') ", "",false,false);
			
			$data["s_account_list"] = $this->atc_code->getValue("chartaccount", 
			array("id ind","CONCAT(segment5, ' - ', accountname) val"), " accountname = 'Creditable Withholding Tax'", "",false,false);

			$this->view->load('atc_code/atc_code',$data);
		}

		public function edit($atc_code = ""){
			$this->view->title  = $this->ui->EditLabel('');
			$data["ajax_task"] = "edit";
			$data["task"] = "edit";
			$data['ui'] = $this->ui;
			$data['sid'] = $atc_code;
		
		
			$data["ajax_post"] 	 = "&companycode=".COMPANYCODE."&atcId=".$atc_code;
			$retdata = array('atcId','atc_code','tax_rate','wtaxcode','short_desc','tax_account','cwt');
			$condition = "atcId = '".$atc_code."' ";

			/**TAX ACCOUNT**/
			$data["account_list"] = $this->atc_code->getValue("chartaccount", 
			array("id ind","CONCAT(segment5, ' - ', accountname) val"), " accountclasscode IN('TAX','CULIAB','OTHCL') ", "",false,false);

			$data["s_account_list"] = $this->atc_code->getValue("chartaccount", 
			array("id ind","CONCAT(segment5, ' - ', accountname) val"), " accountname = 'Creditable Withholding Tax'", "",false,false);

			$list = $this->atc_code->retrieveData($retdata,"","",$condition);
		
			if( !empty($list->result) ) :
				for($i = 0; $i < count($list->result); $i++)
				{	
					$data["atcId"]          = $list->result[$i]->atcId;
					$data["atc_code"] 		= $list->result[$i]->atc_code;
					$data["tax_rate"] 		= $list->result[$i]->tax_rate * 100;
					$data["wtaxcode"] 		= $list->result[$i]->wtaxcode;
					$data["short_desc"] 	= $list->result[$i]->short_desc;
					$data["tax_account"] 	= $list->result[$i]->tax_account;
					$data["cwt"] 			= $list->result[$i]->cwt;
				}
			endif;
			
			$data["button_name"] = "Save";
			$this->view->load('atc_code/atc_code',$data);

		}

		public function view($atc_code = ""){
			$this->view->title  = $this->ui->ViewLabel('');
			$data["ajax_task"] = "edit";
			$data["ajax_task"] = "view";
			$data["task"] = "view";
			$data['ui'] = $this->ui;
			$data['sid'] = $atc_code;

			$data["ajax_post"] 	 = "&companycode=".COMPANYCODE."&atc_code=".$atc_code;
			$retdata = array('atcId','atc_code','tax_rate','wtaxcode','short_desc','tax_account','cwt');
			$condition = "atcId = '".$atc_code."' ";

			/**TAX ACCOUNT**/
			$data["account_list"] = $this->atc_code->getValue("chartaccount", 
			array("id ind","CONCAT(segment5, ' - ', accountname) val"), " accountclasscode IN('TAX','CULIAB','OTHCL') ", "",false,false);

			$data["s_account_list"] = $this->atc_code->getValue("chartaccount", 
			array("id ind","CONCAT(segment5, ' - ', accountname) val"), " accountname = 'Creditable Withholding Tax'", "",false,false);

			$list = $this->atc_code->retrieveData($retdata,"","",$condition);
		
			if( !empty($list->result) ) :
				for($i = 0; $i < count($list->result); $i++)
				{	
					$data["atcId"]          = $list->result[$i]->atcId;
					$data["atc_code"] 		= $list->result[$i]->atc_code;
					$data["tax_rate"] 		= $list->result[$i]->tax_rate * 100;
					$data["wtaxcode"] 		= $list->result[$i]->wtaxcode;
					$data["short_desc"] 	= $list->result[$i]->short_desc;
					$data["tax_account"] 	= $list->result[$i]->tax_account;
					$data["cwt"] 			= $list->result[$i]->cwt;
				}

			endif;
			$data["button_name"] = "Edit";
			$this->view->load('atc_code/atc_code',$data);

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

			$headerArr = array('ATC Code','Tax Rate', 'Tax Code', 'Description', 'EWT','CWT');
			$cwtclass = $this->atc_code->check_cwt_accountclasscode();
			foreach ($cwtclass as $row) {
				$cwt_code = $row->segment5;
			}
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
						$atccode 	   	= $b[0];
						$taxrate 	   	= $b[1];
						$taxcode 	   	= $b[2];
						$description   	= $b[3];
						$ewt			= $b[4];
						$cwt			= $b[5];

						$exists = $this->atc_code->check_duplicate($atccode);
						$count = $exists[0]->count;
						$tax = $this->atc_code->check_accountclasscode($ewt);
						$code = array();
						foreach ($tax as $m) {
							$code[] = $m->accountclasscode;
						}
						if(in_array('CULIAB',$code) || in_array('TAX',$code) || in_array('OTHCL',$code) ){
							
						}else{
							$errmsg[] 	= "EWT Code on row $line is not valid for EWT.<br>";
						}
						if($cwt != $cwt_code){
							$errmsg[] 	= "CWT Code on row $line is not a CWT code.<br>";
						}
						if( $count > 0 )
							{
								$errmsg[]	= "ATC Code [<strong>$atccode</strong>] on row $line already exists.<br/>";
								$errmsg		= array_filter($errmsg);
							}
						if( !in_array($atccode, $list) ){
							$list[] 	=	$atccode;
						}
						
						if(empty($atccode)){
							$errmsg[] 	= "ATC Code on row $line should not be empty.<br>";
						}
					
						if(empty($taxrate)){
							$errmsg[] 	= "Tax rate on row $line should not be empty.<br>";
						}
		
						if(empty($ewt)){
							$errmsg[] 	= "EWT on row $line should not be empty.<br>";
						}

						if(empty($cwt)){
							$errmsg[] 	= "CWT on row $line should not be empty.<br>";
						}
						
						if(empty($description)){
							$errmsg[] 	= "Tax description on row $line should not be empty.<br>";
						}
					
						
						$atccode_[] 	= $atccode;
						$taxrate_[] 	= $taxrate/100;
						$taxcode_[] 	= $taxcode;
						$description_[]	= addslashes($description);
						$ewt_[] 		= $ewt;
						$cwt_[] 		= $cwt;

						$line++;

						$coa_id_ewt = $this->atc_code->get_coa_id($ewt);
						$coa_id_cwt = $this->atc_code->get_coa_id($cwt);
						$ewt1_[] 		= $coa_id_ewt;
						$cwt1_[] 		= $coa_id_cwt;
						
				}
			}
			$temp = array();
			foreach ($ewt1_ as $x) {
				foreach ($x as $row) {
					$temp[] = $row->id;
				}
			}
			$temp1 = array();
			foreach ($cwt1_ as $x) {
				foreach ($x as $row) {
					$temp1[] = $row->id;
				}
			}
			$proceed 	=	false;
			if( empty($errmsg) )
				{
					$post = array(
						'atc_code'			=> $atccode_,
						'tax_rate'			=> $taxrate_,
						'wtaxcode'			=> $taxcode_,
						'short_desc'		=> $description_,
						'tax_account'		=> $temp,
						'cwt'				=> $temp1,
					);
					
					$proceed  				= $this->atc_code->importATC($post);

					if( $proceed )
					{
						$this->logs->saveActivity("Imported ATC.");
					}
				}
			}

			$error_messages		= implode(' ', $errmsg);
		
			return array("proceed" => $proceed,"errmsg"=>$error_messages);
		}

		private function load_list()
		{
			$search = $this->input->post("search");
			$sort 	= $this->input->post("sort");
			$limit 	= $this->input->post("limit");
			$addCond = stripslashes($this->input->post("addCond"));
			$fields = array("atcId","atc_code","tax_rate","wtaxcode","atc_type","short_desc","tax_account","cwt","accountname","a.stat stat");
			$table = "";
			$list = $this->atc_code->retrieveData($fields,$search, $sort, $addCond, $limit); 
	
				if( !empty($list->result) ) :
				foreach($list->result as $key => $row)
				{
					$getCwt = $this->atc_code->getAtc($row->cwt);
					if ($getCwt) {
						$acctname = $getCwt->accountname;
					}
					else {
						$acctname = '';
					}
					$sid = $row->atcId;
					$rate = ($row->tax_rate * 100).'%';

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
								<td>'.$row->atc_code.'</td>
								<td>'.$rate.'</td>
								<td>'.$row->wtaxcode.'</td> 
								<td>'.$row->short_desc.'</td>
								<td>'.$row->accountname.'</td>
								<td>'.$acctname.'</td>
								<td>'.$status.'</td>'; 
							'</tr>';		
				}
			else:
				$table .= "<tr>
								<td colspan = '3'></td>
								<td  class = 'text-center'>No Records Found</td>
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
			// 	$headerArray	= array('ATC Code','Tax Rate','Tax Code','Description');
		
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

			// 	while (($file_data = fgetcsv($file, 1000, ",")) !== FALSE) 
			// 	{
			// 		if(!array_intersect($file_data, $headerArray))
			// 		{
			// 			$atc_code	= addslashes(htmlentities(trim($file_data[0])));
			// 			$tax_rate	= addslashes(htmlentities(trim($file_data[1])));
			// 			$wtaxcode	= addslashes(htmlentities(trim($file_data[2])));
			// 			$short_desc	= addslashes(htmlentities(trim($file_data[3])));
			// 			$tax_account= addslashes(htmlentities(trim($file_data[4])));

			// 			if(empty($atc_code)){
			// 				$errmsg[] 	= "ATC Code is required. Row $row should not be empty.";
			// 			}
					
			// 			if(empty($tax_rate)){
			// 				$errmsg[] 	= "Tax rate is required. Row $row should not be empty.";
			// 			}

			// 			if(empty($tax_account)){
			// 				$errmsg[] 	= "Tax account is required. Row $row should not be empty.";
			// 			}
						
			// 			if(empty($short_desc)){
			// 				$errmsg[] 	= "Tax description is required. Row $row should not be empty.";
			// 			}

			// 			/**VALIDATE ACCOUNT CODE**/
			// 			$isatc_code	= $this->atc_code->getValue("atccode",array("atc_code"),
			// 							" atc_code = '$atc_code' ");
			// 			if(isset($isatc_code[0]->atc_code) && !empty($isatc_code[0]->atc_code)){
			// 				$errmsg[] 	= "ATC Code [ <strong>$atc_code</strong> ] on row $row already exists.";
			// 			}

			// 			$istax_account	= $this->atc_code->getValue("atccode",array("tax_account"),
			// 			" tax_account = '$tax_account' ");
			// 			if(isset($istax_account[0]->tax_account) && !empty($istax_account[0]->tax_account)){
			// 				$errmsg[] 	= "ATC Code [ <strong>$tax_account</strong> ] on row $row already exists.";
			// 			}

			// 			/**VALIDATE COA**/
			// 			$natureArray	= $this->atc_code->getValue("chartaccount",array("accountname"),
			// 			" accountclasscode = 'TAX' ");
			// 			if(!in_array($tax_account,$natureArray)){
			// 				$errmsg[] 	= "Tax Account [ <strong>$tax_account</strong> ] on row $row cannot be found on Chart of Accounts table.";
			// 			}

			// 			$istax_account_id	= $this->atc_code->getValue("chartaccount",array("id"),
			// 			" accountname = '$tax_account' ");
			// 			$coa_id = '';
			// 			if(isset($istax_account_id[0]->id) && !empty($istax_account_id[0]->id)){
			// 				$coa_id = $istax_account_id[0]->id ;
			// 				$errmsg[] 	= "Tax Account[ <strong>$tax_account</strong> ] on row $row already exists.";
			// 			}

			// 			/**ASSIGN TO NEW ARRAY**/
			// 			$docData[$i]['atc_code'] 			= $atc_code;
			// 			$docData[$i]['tax_rate']    	 	= $tax_rate / 100;
			// 			$docData[$i]['wtaxcode']		    = $wtaxcode;
			// 			$docData[$i]['short_desc']  		= $short_desc;
			// 			$docData[$i]['tax_account']  		= $coa_id;

			// 			$i++;
			// 			$row++;
			// 		}
			// 	}
			
			// 	$errmsg				           = array_filter($errmsg);
			// 	$data['import_error_messages'] = $errmsg;

			// 	// Insert File Data
			// 	if(!empty($docData) && empty($errmsg))
			// 	{
			// 		 $file_import_result = $this->atc_code->saveImport($docData);
			// 		 $data["file_import_result"] = $file_import_result;
			// 	}
			// }

			$this->view->load('atc_code/atc_codelist',$data);
		}

		private function add()
		{
			$data_var = array(
						'atc_code',
						'tax_rate',
						'wtaxcode',
						'short_desc',
						'tax_account',
						'cwt'
					);

			$data = $this->input->post($data_var);

			$result = $this->atc_code->insertData($data);
		
			if($result)
				$msg = "success";
			else
				$msg = "error_add";

			$dataArray = array("msg" => $msg, "atc_code" => $data["atc_code"], "tax_account" => $data["tax_account"], "cwt" => $data["cwt"]);
			return $dataArray;

		}

		public function update()
		{	
			
			$data_var = array(
				'atcId',
				'atc_code',
				'tax_rate',
				'wtaxcode',
				'short_desc',
				'tax_account',
				'cwt'
			);

			$data_var2 = array(
				'atcId'
			);

			$data = $this->input->post($data_var);
			$data2 = $this->input->post($data_var2);
			extract($data2);

			$data['tax_rate'] 	=	$data['tax_rate'] /	100;
			// /**
			// * Update Database
			// */
			$result = $this->atc_code->editData($data, $data2);
			
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
			$result = $this->atc_code->deleteData($id);

			$dataArray = array( "msg" => $result );
			return $dataArray;
		}

		private function export()
		{
			$data_post = $this->input->post(array("addCond", "search"));

			$result = $this->atc_code->fileExport($data_post);

			$header = array("ATC Code","Tax Rate","Tax Code","Description","EWT","CWT");
			
		
			$csv = '';
			$csv .= '"' . implode('","', $header) . '"';
			$csv .= "\n";
		
			$retrieved 	=	array_filter($result);
			if(!empty($retrieved)){
				foreach ($retrieved as $key => $row){
					$getCwt = $this->atc_code->getAtc($row->cwt);
					if ($getCwt) {
						$acctname = $getCwt->accountname;
					}
					else {
						$acctname = '';
					}
					$atc_code 			= $row->atc_code;
					$tax_rate 			= $row->tax_rate * 100 .'%';
					$wtaxcode       	= $row->wtaxcode;
					$short_desc    	 	= $row->short_desc;
					$accountname    	= $row->accountname;    	 	

					$csv .= '"' . $atc_code 		. '",';
					$csv .= '"' . $tax_rate 		. '",';
					$csv .= '"' . $wtaxcode 		. '",';
					$csv .= '"' . $short_desc 		. '",';
					$csv .= '"' . $accountname 		. '",';
					$csv .= '"' . $acctname . '"';
					$csv .= "\n";
				}
			}
			return $csv;
		}

	private function ajax_edit_activate()
	{
		$code = $this->input->post('id');
		$data['stat'] = 'active';

		$result = $this->atc_code->updateStat($data,$code);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
			);
	}
	
	private function ajax_edit_deactivate()
	{
		$code = $this->input->post('id');
		$data['stat'] = 'inactive';

		$result = $this->atc_code->updateStat($data,$code);
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
			$result 			= 	$this->atc_code->updateStat($data, $value);
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
			$result 			= 	$this->atc_code->updateStat($data, $value);
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