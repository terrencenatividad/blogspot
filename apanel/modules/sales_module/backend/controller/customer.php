<?php
	class controller extends wc_controller 
	{
		public function __construct()
		{
			parent::__construct();

			$this->customer 	=	new customer();
			$this->input 		=	new input();
			$this->ui 			= 	new ui();
			$this->url 			=	new url();
			$this->log 			= 	new log();

			$this->view->header_active = 'maintenance/customer/';

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
				'stat',
				'credit_limit'
			);
		}

		public function listing()
		{
			$this->view->title 	= $this->ui->ListLabel('');
			$data['ui'] 		= $this->ui;
			$this->view->load('customer/customer_list' ,$data);
		}
		
		public function create()
		{
			$this->view->title = $this->ui->AddLabel('');
			
			$data 				= $this->input->post($this->fields);

			$data['bt_select'] 	= $this->customer->retrieveBusinessTypeDropdown();
			$data['ui'] 		= $this->ui;
			$data['task'] 		= 'add';
			$data['show_input'] = true;
			$data['ajax_post'] 	= '';

			$this->view->load('customer/customer_create',  $data);
		}

		public function edit($code)
		{
			$this->view->title 		= $this->ui->EditLabel('');
			
			$data 			 		= (array) $this->customer->retrieveExistingCustomer($this->fields, $code);
			
			$data['bt_select'] 		= $this->customer->retrieveBusinessTypeDropdown();
			$data['ui'] 			= $this->ui;
			$data['task'] 			= 'update';
			$data['show_input'] 	= true;
			$data['ajax_post'] 		= "&code=$code";

			$this->view->load('customer/customer_create',  $data);
		}

		public function view($code)
		{
			$this->view->title = $this->ui->ViewLabel('');
			
			$this->fields[] 				= "COALESCE(incurred.receivables,0) receivables";
			$data 			 				= (array) $this->customer->retrieveExistingCustomer($this->fields, $code);
			$data['bt_select'] 				= $this->customer->retrieveBusinessTypeDropdown();
			$data['ui'] 					= $this->ui;
			$data['show_input'] 			= false;
			$data['task'] 					= 'view';
			$data['ajax_post'] 				= "";
			$data['credit_limit'] 			= number_format($data['credit_limit'],2);
			$data['incurred_receivables']	= number_format($data['receivables'],2);

			$this->view->load('customer/customer_create',  $data);
		}

		public function ajax($task) {
			$ajax = $this->{$task}();
			if ($ajax) {
				header('Content-type: application/json');
				echo json_encode($ajax);
			}
		}

		public function get_import(){
			header('Content-type: application/csv');
			$header = array('Customer Code','Company Name','Address','Email','Business Type','Contact Number','First Name','Last Name','Payment Terms','Tin No.',"Credit Limit");

			$return = '';
			$return .= '"' . implode('","',$header) . '"';
			$return .= "\n";
			$return .= '"CUS_232322","CID Systems","Makati Avenue, Makati, 1200 Metro Manila","lumeng.lim@cid-systems.com","Individual","123-4567","Lumeng","Lim","30","000-000-000-000","0.00"';
			
			echo $return;
		}
		
		private function check_character_length($field_name, $field_value, $line, $max_length, $input_length){
			$error 	=	"";
			
			if($input_length > $max_length){
				$error 	= 	"$field_name [<strong>$field_value</strong>] on row $line exceeded the Max Character Length of $max_length.<br/>";
			}

			return $error;
		}

		private function check_empty($field_name, $field_value, $line){
			$error 	=	"";
			
			if($field_value  == ""){
				$error 	= 	"$field_name [<strong>$field_value</strong>] on row $line is empty.<br/>";
			}

			return $error;
		}

		private function check_numeric($field_name, $field_value, $line){
			$error 	=	"";
			
			if(!is_numeric($field_value)){
				$error 	= 	"$field_name [<strong>$field_value</strong>] on row $line is not a valid number.<br/>";
			}

			return $error;
		}

		private function check_negative($field_name, $field_value, $line){
			$error 	=	"";
			
			if($field_value < 0){
				$error 	= 	"$field_name [<strong>$field_value</strong>] on row $line has a negative value.<br/>";
			}

			return $error;
		}

		private function check_email($field_name, $field_value, $line){
			$error 	=	"";
			if (!filter_var($field_value, FILTER_VALIDATE_EMAIL)) {
				$error 	= 	"$field_name [<strong>$field_value</strong>] on row $line is not a valid E-mail.<br/>";
			}
			return $error;
		}

		private function check_business_type($field_name, $field_value, $line){
			$business_type 	=	array('Corporation','Individual');
			$error 			=	"";

			if (!in_array($field_value, $business_type)) {
				$error 	= 	"$field_name [<strong>$field_value</strong>] on row $line is not a valid Business Type.<br/>";
			}
			return $error;
		}

		private function check_duplicate_code($field_name,$field_value,$line){
			$error 		=	"";

			$exists 	= 	$this->customer->check_duplicate($field_value);
			$count  	=	isset($exists[0]->count) 	?	$exists[0]->count 	:	0;

			if($count > 0){
				$error 	= 	"$field_name [<strong>$field_value</strong>] on row $line already exists.<br/>";
			}
			return $error;
		}

		private function save_import(){
			$file		= fopen($_FILES['file']['tmp_name'],'r') or exit ("File Unable to upload") ;

			$filedir	= $_FILES["file"]["tmp_name"];
	
			$file_types = array( "application/octet-stream","text/x-csv","text/tsv","text/comma-separated-values", "text/csv", "application/csv", "application/excel", "application/vnd.ms-excel", "application/vnd.msexcel", "text/anytext");

			$proceed 	=	false;
			$errmsg 	= 	array();

			/**VALIDATE FILE IF CORRUPT**/
			if(!empty($_FILES['file']['error'])){
				$errmsg[] = "File being uploaded is corrupted.<br/>";
			}

			/**VALIDATE FILE TYPE**/
			if(!in_array($_FILES['file']['type'],$file_types)){
				$errmsg[]= "Invalid file type, file must be .csv.<br/>";
			}
		
			$headerArr = array('Customer Code','Company Name','Address','Email','Business Type','Contact Number','First Name','Last Name','Payment Terms','Tin No.','Credit Limit');

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
				$list 	= 	array();
				
				if(!empty($z)){
					foreach ($z as $key => $b) {
						if( ! empty($b)) {	
							$customercode 	   	= isset($b[0]) ? htmlspecialchars(addslashes(trim($b[0])))	: 	"";
							$companyname        = isset($b[1]) ? addslashes(trim($b[1]))	: 	"";
							$address            = isset($b[2]) ? htmlspecialchars(addslashes(trim($b[2])))	: 	"";
							$email 				= isset($b[3]) ? htmlspecialchars(addslashes(trim($b[3])))	: 	"";
							$business 			= isset($b[4]) ? htmlspecialchars(addslashes(trim($b[4])))	: 	"";
							$contact 			= isset($b[5]) ? htmlspecialchars(addslashes(trim($b[5])))	: 	"";
							$firstname        	= isset($b[6]) ? htmlspecialchars(addslashes(trim($b[6])))	: 	"";
							$lastname           = isset($b[7]) ? htmlspecialchars(addslashes(trim($b[7])))	: 	"";
							$terms 				= isset($b[8]) ? htmlspecialchars(addslashes(trim($b[8])))	: 	0;
							$tinno 				= isset($b[9]) ? htmlspecialchars(addslashes(trim($b[9])))	: 	"";
							$credit_limit 		= isset($b[10])? htmlspecialchars(addslashes(trim($b[10])))	: 	0;

							$headerArr = array('Customer Code','Company Name','Address','Email','Business Type','Contact Number','First Name','Last Name','Payment Terms','Tin No.','Credit Limit');

							// *********Validation Starts here**************
							
							// Check for Empty on first line
							$errmsg[] 	=	$this->check_empty("Customer Code", $customercode, $line);
							$errmsg[] 	=	$this->check_empty("Company Name", $companyname, $line);
							$errmsg[] 	=	$this->check_empty("Address", $address, $line);
							$errmsg[] 	=	$this->check_empty("Business Type", $business, $line);
						
							// Check for Max Length 
							$errmsg[] 	=	$this->check_character_length("Customer Code", $customercode, $line, "20", strlen($customercode));
							$errmsg[] 	=	$this->check_character_length("Company Name", $companyname, $line, "30", strlen($companyname));
							$errmsg[] 	=	$this->check_character_length("Address", $address, $line, "105", strlen($address));
							$errmsg[] 	=	$this->check_character_length("Email", $email, $line, "150", strlen($email));
							$errmsg[] 	=	$this->check_character_length("Contact Number", $contact, $line, "20", strlen($contact));
							$errmsg[] 	=	$this->check_character_length("First Name", $firstname, $line, "20", strlen($firstname));
							$errmsg[] 	=	$this->check_character_length("Last Name", $lastname, $line, "20", strlen($lastname));
							$errmsg[] 	=	$this->check_character_length("Payment Terms", $terms, $line, "5", strlen($terms));
							$errmsg[] 	=	$this->check_character_length("Tin No", $tinno, $line, "15", strlen($tinno));
							$errmsg[] 	=	$this->check_character_length("Credit Limit", $credit_limit, $line, "20", strlen($credit_limit));

							// Check for Duplicates
							$errmsg[] 	=	$this->check_duplicate_code("Customer Code",$customercode,$line);

							// Check for Numerical Values
							$errmsg[] 	=	$this->check_numeric("Payment Terms", $terms, $line);
							$errmsg[] 	=	$this->check_numeric("Credit Limit", $credit_limit, $line);

							// Check for Negative Values
							$errmsg[] 	=	$this->check_negative("Payment Terms", $terms, $line);
							$errmsg[] 	=	$this->check_negative("Credit Limit", $credit_limit, $line);

							// Check for E-mail Format
							$errmsg[] 	=	$this->check_email("Email", $email, $line);

							// Check for Business Content ( Individual or Corporation )
							$errmsg[] 	=	$this->check_business_type("Business Type", $business, $line);

							// Check for Duplicate Customer
							if( !in_array($customercode, $list) ){
								$list[] 	=	$customercode;
							} else {
								$errmsg[]	= "Customer Code [<strong>$customercode</strong>] on row $line has a duplicate within the document.<br/>";
							}

							$errmsg		= 	array_filter($errmsg);

							$customercode_[] 	= $customercode;
							$companyname_[]		= $companyname;
							$firstname_[]		= $firstname;
							$lastname_[]		= $lastname;
							$address_[]			= $address;
							$email_[]			= $email;
							$business_[] 		= $business;
							$tinno_[] 			= $tinno;
							$terms_[] 			= $terms;
							$contact_[] 		= $contact;
							$credit_limit_[] 	= $credit_limit;

							$line++;
						}
					}
				} else {
					$errmsg[] 	= "You are importing an empty template.";
					$errmsg		= array_filter($errmsg);
				}

				if( empty($errmsg) ){
					$post = array(
						'partnercode'		=> $customercode_,
						'partnername'		=> $companyname_,
						'first_name'		=> $firstname_,
						'last_name'			=> $lastname_,
						'address1'			=> $address_,
						'email'	 		    => $email_,
						'businesstype'	 	=> $business_,
						'tinno' 			=> $tinno_,
						'terms' 			=> $terms_,
						'mobile' 			=> $contact_,
						'credit_limit'		=> $credit_limit_
					);
					
					$proceed  				= $this->customer->importCustomers($post);

					if( $proceed ) {
						$this->log->saveActivity("Imported Customers.");
					}
				}
			}

			$error_messages		= implode(' ', $errmsg);
			
			return array("proceed" => $proceed,"errmsg"=>$error_messages);
		}

		private function get_duplicate(){
			$current = $this->input->post('curr_code');
			$old 	 = $this->input->post('old_code');
			$count 	 = 0;

			if( $current!='' && $current != $old )
			{
				$result = $this->customer->check_duplicate($current);

				$count = $result[0]->count;
			}
			
			$msg   = "";

			if( $count > 0 )
			{	
				$msg = "exists";
			}

			return $dataArray = array("msg" => $msg);
		}

		private function customer_list(){

			$search = $this->input->post('search');
			$sort 	= $this->input->post('sort');
			$limit 	= $this->input->post('limit');
			//echo $sort;
			$list 	= $this->customer->retrieveListing($search,$sort,$limit);

			$table 	= '';

			if( !empty($list->result) ) :
				foreach ($list->result as $key => $row) {

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
										 ->setValue($row->partnercode)
										 ->draw();
					$table .= '<tr>';
					$table .= '<td align = "center">' . $dropdown . '</td>';
					$table .= '<td>' . $row->partnercode . '</td>';
					$table .= '<td>' . $row->partnername . '</td>';
					$table .= '<td>' . $row->contact_person. '</td>';
					$table .= '<td>' . $row->email . '</td>';
					$table .= '<td>' . number_format($row->credit_limit,2) . '</td>';
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

		private function add(){
			$posted_data 	= $this->input->post($this->fields);	
			$result  		= $this->customer->insertCustomer($posted_data);
			//var_dump($posted_data);
			$customercode 	= $this->input->post('partnercode');

			if( $result )
			{
				$msg = "success";
				$this->log->saveActivity("Added New Customer [$customercode] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray = array("msg" => $msg);
		}

		private function update(){
			$posted_data 	= $this->input->post($this->fields);
			$code 		 	= $this->input->post('code');

			$result 		= $this->customer->updateCustomer($posted_data, $code);

			if( $result )
			{
				$msg = "success";
				$this->log->saveActivity("Updated Customer [$code] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray = array("msg" => $msg);
		}

		private function delete(){
			$id_array 		= array('id');
			$id       		= $this->input->post($id_array);
			
			$result 		= $this->customer->deleteCustomer($id);
			
			if( empty($result) )
			{
				$msg = "success";
				$this->log->saveActivity("Deleted Customer(s) [". implode($id, ', ') ."] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray = array("msg" => $msg);
		}

		private function export(){
			
			// $this->log->saveActivity("Exported Customers.");

			$search = $this->input->post('search');
			$sort 	= $this->input->post('sort');
			
			//echo $sort;

			$header = array('Customer Code','Company Name','Address','Email','Business Type','Contact Number','First Name','Last Name','Payment Terms','Tin No.','Credit Limit');

			$csv = '';
			$csv .= '"' . implode('","', $header) . '"';
			$csv .= "\n";
			
			$result = $this->customer->export($search, $sort);

			if (!empty($result)){
				foreach ($result as $key => $row){

					$csv .= '"' . $row->partnercode . '",';
					$csv .= '"' . $row->partnername . '",';
					$csv .= '"' . $row->address1 . '",';
					$csv .= '"' . $row->email . '",';
					$csv .= '"' . $row->businesstype . '",';
					$csv .= '"' . $row->mobile . '",';
					$csv .= '"' . $row->first_name . '",';
					$csv .= '"' . $row->last_name . '",';
					$csv .= '"' . $row->terms . '",';
					$csv .= '"' . $row->tinno . '",';
					$csv .= '"' . $row->credit_limit . '"';
					$csv .= "\n";
				}
			}

			return $csv;
		}

		private function ajax_edit_activate()
		{
			$code = $this->input->post('id');
			$data['stat'] = 'active';

			$result = $this->customer->updateStat($data,$code);
			return array(
				'redirect'	=> MODULE_URL,
				'success'	=> $result
				);
		}
		
		private function ajax_edit_deactivate()
		{
			$code = $this->input->post('id');
			$data['stat'] = 'inactive';

			$result = $this->customer->updateStat($data,$code);
			return array(
				'redirect'	=> MODULE_URL,
				'success'	=> $result
				);
		}
	}
?>