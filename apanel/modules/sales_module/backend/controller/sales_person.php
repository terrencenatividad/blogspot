<?php
	class controller extends wc_controller 
	{
		public function __construct()
		{
			parent::__construct();

			$this->sales_person =	new sales_person();
			$this->input 		=	new input();
			$this->ui 			= 	new ui();
			$this->url 			=	new url();
			$this->log 			= 	new log();

			$this->view->header_active = 'maintenance/sales_person/';

			$this->fields = array(
				'partnercode',
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
			$this->view->title = 'Sales Person Listing';
			$data['ui'] 	   = $this->ui;
			$this->view->load('sales_person/sales_person_list',$data);
		}

		public function create()
		{
			$this->view->title = 'Add Sales Person';
			
			if ($this->input->isPost) 
			{
				$extracted_data = $this->input->post($data);
				extract($extracted_data);		
			}
			$data 				= $this->input->post($this->fields);

			$data['bt_select'] 	= $this->sales_person->retrieveBusinessTypeDropdown();
			$data['ui'] 		= $this->ui;
			$data['task'] 		= 'add';
			$data['show_input'] = true;
			$data['ajax_post'] 	= '';

			$this->view->load('sales_person/sales_person',  $data);
		}

		public function edit($code)
		{
			$this->view->title = 'Edit Sales Person';
			
			$data 			 	= (array) $this->sales_person->retrieveExistingSalesPerson($this->fields, $code);

			$data['bt_select'] 	= $this->sales_person->retrieveBusinessTypeDropdown();
			$data['ui'] 		= $this->ui;
			$data['task'] 		= 'update';
			$data['show_input'] = true;
			$data['ajax_post'] 	= "&code=$code";

			$this->view->load('sales_person/sales_person',  $data);
		}

		public function view($code)
		{	
			$this->view->title  = 'View Sales Person';
			
			$data 			 	= (array) $this->sales_person->retrieveExistingSalesPerson($this->fields, $code);
			$data['bt_select'] 	= $this->sales_person->retrieveBusinessTypeDropdown();
			$data['ui'] 		= $this->ui;
			$data['show_input'] = false;
			$data['task'] 		= 'view';
			$data['ajax_post'] 	= "";

			$this->view->load('sales_person/sales_person',  $data);
		}

		public function tag_customers($code)
		{
			$this->view->title = 'Tag Customers to Sales Person Template';

			$data 			 	= (array) $this->sales_person->retrieveExistingSalesPerson($this->fields, $code);
			$data['discountchoice'] = array('percentage'=>"Percentage", 'amouunt'=>'Amount');
			$data['customer_list']  = $this->sales_person->retrieveCustomerListing($code);
			$data['salespersonname']= $data['first_name']." ".$data['last_name'];

			$data['ui'] 		= $this->ui;
			$data['show_input'] = true;
			$data['task'] 		= 'apply_to_sales';
			$data['ajax_post']  = "&sales_code=$code";

			$this->view->load('sales_person/sales_person_tag',  $data);
		}

		public function ajax($task) {
			$ajax = $this->{$task}();
			if ($ajax) {
				header('Content-type: application/json');
				echo json_encode($ajax);
			}
		}
		
		private function get_duplicate(){
			$current = $this->input->post('curr_code');
			$old 	 = $this->input->post('old_code');
			$count 	 = 0;

			if( $current!='' && $current != $old )
			{
				$result = $this->sales_person->check_duplicate($current);

				$count = $result[0]->count;
			}
			
			$msg   = "";

			if( $count > 0 )
			{	
				$msg = "exists";
			}

			return $dataArray = array("msg" => $msg);
		}

		private function apply_to_sales()
		{
			$sales_code 		= $this->input->post('sales_code');
			$tagged_customers 	= $this->input->post('taggedCustomers');	

			$result 			= $this->sales_person->tagCustomer($sales_code, $tagged_customers);
			
			if( $result )
			{	
				$msg = "success";
				$this->log->saveActivity("Tagged Customer(s) [ ". implode($tagged_customers,',') ." ] to Sales Person [$sales_code] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray = array("msg" => $msg);
		}

		private function sales_person_list() {

			$search = $this->input->post('search');
			$sort 	= $this->input->post('sort');
			$limit 	= $this->input->post('limit');
			$list 	= $this->sales_person->retrieveListing($search,$sort, $limit);

			$table 	= '';

			if( !empty($list->result) ) :
				foreach ($list->result as $key => $row) {

					$dropdown = $this->ui->loadElement('check_task')
										 ->addView()
										 ->addEdit()
										 ->addDelete()
										 ->addCheckbox()
										 ->setValue($row->partnercode)
										 ->draw();

					$table .= '<tr>';
					$table .= '<td>' . $dropdown . '</td>';
					$table .= '<td>' . $row->partnercode . '</td>';
					$table .= '<td>' . $row->contact_person. '</td>';
					$table .= '<td>' . $row->email . '</td>';
					$table .= '<td>' . $row->stat . '</td>';
					$table .= '</tr>';
				}
			else:
				$table .= "<tr>
								<td colspan = '5' class = 'text-center'>No Records Found</td>
						</tr>";
			endif;

			$list->table 	=	$table;
			$list->csv 		=	$this->export();

			return $list;
		}

		private function add()
		{
			$posted_data 	= $this->input->post($this->fields);

			$sp_code 		= $this->input->post('partnercode');

			$result  		= $this->sales_person->insertSalesPerson($posted_data);

			if( $result )
			{	
				$msg = "success";
				$this->log->saveActivity("Added New Sales Person [$sp_code] ");
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

			$result 		= $this->sales_person->updateSalesPerson($posted_data, $code);

			if( $result )
			{	
				$msg = "success";
				$this->log->saveActivity("Updated Sales Person [$code] ");
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
			
			$result 		= $this->sales_person->deleteSalesPerson($id);

			if( empty($result) )
			{
				$msg = "success";
				$this->log->saveActivity("Cancelled Sales Person(s) [". implode($id, ', ') ."] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray = array("msg" => $msg);
		}

		private function export(){
			
			// $this->log->saveActivity("Exported Sales Person.");

			$search = $this->input->post('search');
			$sort 	= $this->input->post('sort');
			
			$header = array("Sales Person Code","First Name","Last Name","Address","E-mail Address","Business Type","Tin No.","Payment Terms","Contact No.");
			
			$csv = '';
			$csv .= '"' . implode('", "', $header) . '"';
			$csv .= "\n";
			
			$result = $this->sales_person->export($search, $sort);

			if (!empty($result)){
				foreach ($result as $key => $row){

					$csv .= '"' . $row->partnercode . '",';
					$csv .= '"' . $row->first_name . '",';
					$csv .= '"' . $row->last_name . '",';
					$csv .= '"' . $row->address1 . '",';
					$csv .= '"' . $row->email . '",';
					$csv .= '"' . $row->businesstype . '",';
					$csv .= '"' . $row->tinno . '",';
					$csv .= '"' . $row->terms . '",';
					$csv .= '"' . $row->mobile . '"';
					$csv .= "\n";
				}
			}

			return $csv;
		}

		public function get_import(){
			header('Content-type: application/csv');
			$header = array('Sales Person Code','First Name','Last Name','Address','Email','Business Type','Tin No.','Payment Terms','Contact Number');

			$return = '';
			$return .= '"' . implode('","',$header) . '"';
			$return .= "\n";
			$return .= '"SP0001","Lumeng","Lim","Makati Avenue, Makati, 1200 Metro Manila","lumeng.lim@cid-systems.com","Individual","000-000-000-000","30","123-4567"';
			
			echo $return;
		}
		
		private function save_import(){
			$file		= fopen($_FILES['file']['tmp_name'],'r') or exit ("File Unable to upload") ;

			$filedir	= $_FILES["file"]["tmp_name"];
	
			$file_types = array( "text/x-csv","text/tsv","text/comma-separated-values", "text/csv", "application/csv", "application/excel", "application/vnd.ms-excel", "application/vnd.msexcel", "text/anytext");

			/**VALIDATE FILE IF CORRUPT**/
			if(!empty($_FILES['file']['error'])){
				$errmsg[] = "File being uploaded is corrupted.<br/>";
			}

			/**VALIDATE FILE TYPE**/
			if(!in_array($_FILES['file']['type'],$file_types)){
				$errmsg[]= "Invalid file type, file must be .csv.<br/>";
			}
			
			$headerArr = array('Sales Person Code','First Name','Last Name','Address','Email','Business Type','Tin No.','Payment Terms','Contact Number');

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
					if ( ! empty($b)) 
					{	
						$sp_code 	   		= $b[0];
						$firstname        	= $b[1];
						$lastname           = $b[2];
						$address            = $b[3];
						$email 				= $b[4];
						$business 			= $b[5];
						$tinno 				= $b[6];
						$terms 				= $b[7];
						$contact 			= $b[8];

						$exists = $this->sales_person->check_duplicate($sp_code);
						$count = $exists[0]->count;

						if( $count > 0 )
						{
							$errmsg[]	= "Sales Person Code [<strong>$sp_code</strong>] on row $line already exists.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						if(!is_numeric($terms)){
							$errmsg[] 	= "Terms [ <strong>$terms</strong> ] on row $line is not a valid amount.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						if( !in_array($sp_code, $list) ){
							$list[] 	=	$sp_code;
						}
						else
						{
							$errmsg[]	= "Sales Person Code [<strong>$sp_code</strong>] on row $line has a duplicate within the document.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						$sp_code_[] 		= $sp_code;
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

				$proceed 	=	false;

				if( empty($errmsg) )
				{
					$post = array(
						'partnercode'		=> $sp_code_,
						'first_name'		=> $firstname_,
						'last_name'			=> $lastname_,
						'address1'			=> $address_,
						'email'	 		    => $email_,
						'businesstype'	 	=> $business_,
						'tinno' 			=> $tinno_,
						'terms' 			=> $terms_,
						'mobile' 			=> $contact_
					);
					
					$proceed  				= $this->sales_person->importSalesPerson($post);

					if( $proceed )
					{
						$this->log->saveActivity("Imported Sales Person.");
					}
				}
			}

			$error_messages		= implode(' ', $errmsg);
			
			return array("proceed" => $proceed,"errmsg"=>$error_messages);
		} 

	}
?>