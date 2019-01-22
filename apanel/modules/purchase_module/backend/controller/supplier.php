<?php
	class controller extends wc_controller 
	{
		public function __construct()
		{
			parent::__construct();

			$this->supplier 	=	new supplier();
			$this->input 		=	new input();
			$this->ui 			= 	new ui();
			$this->url 			=	new url();
			$this->log 			= 	new log();
			$this->import 		= 	new import();

			$this->view->header_active = 'maintenance/supplier/';

			$this->fields = array(
				'partnercode',
				'partnername',
				'first_name',
				'last_name',
				'address1',
				'email',
				'businesstype',
				'tinno',
				'terms'	,
				'mobile',
				'stat'
			);
		}

		public function listing()
		{
			$this->view->title = $this->ui->ListLabel('');
			$data['ui'] 	   = $this->ui;
			$this->view->load('supplier/supplier_list', $data);
		}

		public function create()
		{
			$this->view->title = $this->ui->AddLabel('');
			
			$data 				= $this->input->post($this->fields);

			$data['bt_select'] 		= $this->supplier->retrieveBusinessTypeDropdown();
			$data['ui'] 			= $this->ui;
			$data['task'] 			= 'add';
			$data['show_input'] 	= true;
			$data['ajax_post'] 		= '';

			$this->view->load('supplier/supplier_create',  $data);
		}

		public function edit($code)
		{
			$this->view->title 		= $this->ui->EditLabel('');
			
			$data 			 		= (array) $this->supplier->retrieveExistingSupplier($this->fields, $code);

			$data['bt_select'] 		= $this->supplier->retrieveBusinessTypeDropdown();
			$data['ui'] 			= $this->ui;
			$data['task'] 			= 'update';
			$data['show_input'] 	= true;
			$data['ajax_post'] 		= "&code=$code";
			
			$this->view->load('supplier/supplier_create',  $data);
		}

		public function view($code)
		{
			$this->view->title = $this->ui->ViewLabel('');
			
			$data 			 	= (array) $this->supplier->retrieveExistingSupplier($this->fields, $code);
			$data['bt_select'] 	= $this->supplier->retrieveBusinessTypeDropdown();
			$data['ui'] 		= $this->ui;
			$data['show_input'] = false;
			$data['task'] 		= 'view';
			$data['ajax_post'] 	= "";

			$this->view->load('supplier/supplier_create',  $data);
		}

		public function ajax($task) {
			$ajax = $this->{$task}();
			if ($ajax) {
				header('Content-type: application/json');
				echo json_encode($ajax);
			}
		}
		
		private function supplier_list() {

			$search = $this->input->post('search');
			$sort  	= $this->input->post('sort');
			$limit  = $this->input->post('limit');
			$list 	= $this->supplier->retrieveListing($search, $sort, $limit);
			$table 	= '';
			
			if( !empty($list->result) ) :
				foreach ($list->result as $key => $row) {

					$stat = $row->stat;
					if($stat == 'active'){
						$status = '<span class="label label-success">ACTIVE</span>';								
					}else{
						$status = '<span class="label label-warning">INACTIVE</span>';
					}
					// echo $row->partnercode;
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
										 ->setValue($row->partnercode)
										 ->draw();
					$table .= '<tr>';
					$table .= '<td align = "center">' . $dropdown . '</td>';
					$table .= '<td>' . $row->partnercode . '</td>';
					$table .= '<td>' . $row->partnername. '</td>';
					$table .= '<td>' . $row->contact_person. '</td>';
					$table .= '<td>' . $row->email . '</td>';
					$table .= '<td>' . $status . '</td>';
					$table .= '</tr>';
				}
			else:
				$table .= "<tr>
								<td colspan = '6' class = 'text-center'>No Records Found</td>
						</tr>";
			endif;

			$list->table 	=	$table;
			$list->csv 		= 	$this->export();
			
			return $list;
		}

		private function get_duplicate(){
			$current = $this->input->post('curr_code');
			$old 	 = $this->input->post('old_code');
			
			$count 	 = 0;

			if( $current!='' && $current != $old )
			{
				$result = $this->supplier->check_duplicate($current);

				$count = $result[0]->count;
			}
			
			$msg   = "";

			if( $count > 0 )
			{	
				$msg = "exists";
			}

			return $dataArray = array("msg" => $msg);
		}

		private function add()
		{
			$posted_data 	= $this->input->post($this->fields);	
			$result  		= $this->supplier->insertSupplier($posted_data);

			$suppliercode 	= $this->input->post('partnercode');

			if( $result )
			{
				$msg = "success";
				$this->log->saveActivity("Added New Supplier [$suppliercode] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray = array("msg" => $msg);
		}

		private function update()
		{
			$posted_data 	= $this->input->post($this->fields);
			$code 		 	= $this->input->post('code');

			$result 		= $this->supplier->updateSupplier($posted_data, $code);

			if( $result )
			{
				$msg = "success";
				$this->log->saveActivity("Updated Supplier [$code] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray = array("msg" => $msg);
		}

		private function delete()
		{
			$id_array 		= array('id');
			$id       		= $this->input->post($id_array);
			
			$result 		= $this->supplier->deleteSupplier($id);

			if( empty($result) )
			{
				$msg = "success";
				$this->log->saveActivity("Cancelled Supplier(s) [". implode($id, ', ') ."] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray = array("msg" => $msg);
		}
		
		private function export(){
			
			// $this->log->saveActivity("Exported supplier.");
			
			$search = $this->input->post('search');
			$sort 	= $this->input->post('sort');

			$header = array("Supplier Code","Company Name","First Name","Last Name","Address","E-mail Address","Business Type","Tin No.","Payment Terms","Contact No.","Status");
			
			$csv = '';
			$csv .= '"' . implode('", "', $header) . '"';
			$csv .= "\n";
			
			$result = $this->supplier->export($search, $sort);

			if (!empty($result)){
				foreach ($result as $key => $row){

					$csv .= '"' . $row->partnercode . '",';
					$csv .= '"' . $row->partnername . '",';
					$csv .= '"' . $row->first_name . '",';
					$csv .= '"' . $row->last_name . '",';
					$csv .= '"' . $row->address1 . '",';
					$csv .= '"' . $row->email . '",';
					$csv .= '"' . $row->businesstype . '",';
					$csv .= '"' . $row->tinno . '",';
					$csv .= '"' . $row->terms . '",';
					$csv .= '"' . $row->mobile . '",';
					$csv .= '"' . ucfirst($row->stat)		. '"';
					$csv .= "\n";
				}
			}

			return $csv;
		}

		public function get_import(){
			header('Content-type: application/csv');
			$header = array('Supplier Code','Company Name','First Name','Last Name','Address','Email','Business Type','Tin No.','Payment Terms','Contact Number');

			$return = '';
			$return .= '"' . implode('","',$header) . '"';
			$return .= "\n";
			$return .= '"SUP_232322","CID Systems","Lumeng","Lim","Makati Avenue, Makati, 1200 Metro Manila","lumeng.lim@cid-systems.com","Individual","000-000-000-000","30","123-4567"';
			
			echo $return;
		}

		private function save_import(){
			$file		= fopen($_FILES['file']['tmp_name'],'r') or exit ("File Unable to upload") ;

			$filedir	= $_FILES["file"]["tmp_name"];
	
			$file_types = array( "application/octet-stream","text/x-csv","text/tsv","text/comma-separated-values", "text/csv", "application/csv", "application/excel", "application/vnd.ms-excel", "application/vnd.msexcel", "text/anytext");

			/**VALIDATE FILE IF CORRUPT**/
			if(!empty($_FILES['file']['error'])){
				$errmsg[] = "File being uploaded is corrupted.<br/>";
			}

			/**VALIDATE FILE TYPE**/
			if(!in_array($_FILES['file']['type'],$file_types)){
				$errmsg[]= "Invalid file type, file must be .csv.<br/>";
			}
			
			$headerArr = array('Supplier Code','Company Name','First Name','Last Name','Address','Email','Business Type','Tin No.','Payment Terms','Contact Number');

			$proceed 	=	false;

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
				
				$line 	=	2;
				$list 	=	array();

				if(!empty($z)){
					foreach ($z as $key => $b) {
						if( ! empty($b)) {
							// if ($line == 8) {
							// 	echo utf8_encode($b[4]);
							// 	echo $this->import->trim_special_characters(utf8_encode($b[4]));
							// }
							$suppliercode 	   	= isset($b[0]) ? htmlspecialchars(addslashes(trim($b[0])))	: 	"";
							$companyname        = isset($b[1]) ? addslashes(trim($b[1]))		: 	"";
							$firstname        	= isset($b[2]) ? htmlspecialchars(addslashes(trim($b[2])))	: 	"";
							$lastname           = isset($b[3]) ? htmlspecialchars(addslashes(trim($b[3])))	: 	"";
							$address            = isset($b[4]) ? htmlspecialchars(addslashes(trim($b[4])))	: 	"";
							$email 				= isset($b[5]) ? htmlspecialchars(trim($b[5]))	: 	"";
							$business 			= isset($b[6]) ? htmlspecialchars(addslashes(trim($b[6])))	: 	"";
							$tinno 				= isset($b[7]) ? htmlspecialchars(addslashes(trim($b[7])))	: 	"";
							$terms 				= isset($b[8]) ? htmlspecialchars(addslashes(trim($b[8])))	: 	0;
							$contact 			= isset($b[9]) ? htmlspecialchars(addslashes(trim($b[9])))	: 	"";
							

							$headerArr = array('Customer Code','Company Name','Address','Email','Business Type','Contact Number','First Name','Last Name','Payment Terms','Tin No.');
							
							// **** Trim Other Unusual Special Characters***/ 
							$suppliercode 	   	= $this->import->trim_special_characters($suppliercode);
							$companyname        = $this->import->trim_special_characters($companyname);
							$address            = $this->import->trim_special_characters($address);
							$email 				= $this->import->trim_special_characters($email);
							$business 			= $this->import->trim_special_characters($business);
							$contact 			= $this->import->trim_special_characters($contact);
							$firstname        	= $this->import->trim_special_characters($firstname);
							$lastname           = $this->import->trim_special_characters($lastname);
							$terms 				= $this->import->trim_special_characters($terms);
							$tinno 				= $this->import->trim_special_characters($tinno);

					
							// *********Validation Starts here**************
							
							// Check for Empty on first line
							$errmsg[] 	=	$this->import->check_empty("Customer Code", $suppliercode, $line);
							$errmsg[] 	=	$this->import->check_empty("Company Name", $companyname, $line);
							$errmsg[] 	=	$this->import->check_empty("Address", $address, $line);
							$errmsg[] 	=	$this->import->check_empty("Business Type", $business, $line);
						
							// Check for Max Length 
							$errmsg[] 	=	$this->import->check_character_length("Customer Code", $suppliercode, $line, "20", strlen($suppliercode));
							$errmsg[] 	=	$this->import->check_character_length("Company Name", $companyname, $line, "100", strlen($companyname));
							$errmsg[] 	=	$this->import->check_character_length("Address", $address, $line, "105", strlen($address));
							$errmsg[] 	=	$this->import->check_character_length("Email", $email, $line, "150", strlen($email));
							$errmsg[] 	=	$this->import->check_character_length("Contact Number", $contact, $line, "20", strlen($contact));
							$errmsg[] 	=	$this->import->check_character_length("First Name", $firstname, $line, "20", strlen($firstname));
							$errmsg[] 	=	$this->import->check_character_length("Last Name", $lastname, $line, "20", strlen($lastname));
							$errmsg[] 	=	$this->import->check_character_length("Payment Terms", $terms, $line, "5", strlen($terms));
							$errmsg[] 	=	$this->import->check_character_length("Tin No", $tinno, $line, "15", strlen($tinno));

							// Check for Duplicates
							$errmsg[] 	=	$this->check_duplicate_code("Customer Code",$suppliercode,$line);

							// Check for E-mail Format
							if($email != ''){
								$errmsg[] 	=	$this->import->check_email("Email", $email, $line);
							}

							if($terms != ''){
								$errmsg[] 	=	$this->import->check_negative("Payment Terms", $terms, $line);
								$errmsg[] 	=	$this->import->check_numeric("Payment Terms", $terms, $line);
							}

							// Check for Business Content ( Individual or Corporation )
							$errmsg[] 	=	$this->import->check_business_type("Business Type", $business, $line);

							//Check for Character Type
							$errmsg[] 	=	$this->import->check_alpha_num("Customer Code",$suppliercode,$line);
							$errmsg[] 	=	$this->import->check_special_characters("Company Name",$companyname,$line);
							// $errmsg[] 	=	$this->import->check_special_characters("Company Name",$companyname,$line);

							// Check for Duplicate Customer
							if( !in_array($suppliercode, $list) ){
								$list[] 	=	$suppliercode;
							} else {
								$errmsg[]	= "Customer Code [<strong>$suppliercode</strong>] on row $line has a duplicate within the document.<br/>";
							}
		
							$errmsg		= 	array_filter($errmsg);

							$suppliercode_[] 	= $suppliercode;
							$companyname_[]		= $companyname;
							$firstname_[]		= $firstname;
							$lastname_[]		= $lastname;
							$address_[]			= $address;
							$email_[]			= $email;
							$business_[] 		= $business;
							$tinno_[] 			= $tinno;
							$terms_[] 			= $terms;
							$contact_[] 		= $contact;

							$line++;
						}
					}
				} else {
					$errmsg[] 	= "You are importing an empty template.";
					$errmsg		= array_filter($errmsg);
				}
				

				if( empty($errmsg) )
				{
					$post = array(
						'partnercode'		=> $suppliercode_,
						'partnername'		=> $companyname_,
						'first_name'		=> $firstname_,
						'last_name'			=> $lastname_,
						'address1'			=> $address_,
						'email'	 		    => $email_,
						'businesstype'	 	=> $business_,
						'tinno' 			=> $tinno_,
						'terms' 			=> $terms_,
						'mobile' 			=> $contact_
					);
					
					$proceed  				= $this->supplier->importsupplier($post);

					if( $proceed )
					{
						$this->log->saveActivity("Imported Suppliers.");
					}
				}
			}

			$error_messages		= implode(' ', $errmsg);
			
			return array("proceed" => $proceed,"errmsg"=>$error_messages);
		}

		public function check_duplicate_code($field_name,$field_value,$line){
			$error 		=	"";
	
			$exists 	= 	$this->supplier->check_duplicate($field_value);
			$count  	=	isset($exists[0]->count) 	?	$exists[0]->count 	:	0;
	
			if($count > 0){
				$error 	= 	"$field_name [<strong>$field_value</strong>] on row $line already exists.<br/>";
			}
			return $error;
		}	

		private function ajax_edit_activate()
		{
			$code = $this->input->post('partnercode');
			$data['stat'] = 'active';

			$result = $this->supplier->updateStat($data,$code);
			return array(
				'redirect'	=> MODULE_URL,
				'success'	=> $result
				);
		}

		private function ajax_edit_deactivate()
		{
			$code = $this->input->post('partnercode');
			$data['stat'] = 'inactive';

			$result = $this->supplier->updateStat($data,$code);
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
				$result 			= 	$this->supplier->updateStat($data, $value);
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
				$result 			= 	$this->supplier->updateStat($data, $value);
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
?>