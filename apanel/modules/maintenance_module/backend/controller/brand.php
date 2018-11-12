<?php
    class controller extends wc_controller 
    {
        public function __construct() 
        {
            parent::__construct();

            $this->brand 		=	new brand();
			$this->input 		=	new input();
			$this->ui 			= 	new ui();
			$this->url 			=	new url();
            $this->log 			=  	new log();
            
            $this->view->header_active = 'maintenance/brand/';

            $this->fields = array(
				'brandcode',
                'brandname'
			);
        }

        public function listing(){
			$this->view->title = $this->ui->ListLabel('');
			$data['ui'] 	   = $this->ui;
			$this->view->load('brand/brand_list', $data);
        }

        public function create()
		{
			$this->view->title = $this->ui->AddLabel('');
			
			if ($this->input->isPost) 
			{
				$extracted_data = $this->input->post($data);
				extract($extracted_data);		
			}

			$data 				= $this->input->post($this->fields);
			$data['ui'] 		= $this->ui;
			$data['task'] 		= 'add';
			$data['show_input'] = true;
			$data['ajax_post'] 	= '';

			$this->view->load('brand/brand', $data);
		}

		private function add()
		{
			$posted_data 	= $this->input->post($this->fields);	

			$result  		= $this->brand->insertBrand($posted_data);

			$b_code 		= $posted_data["brandcode"];

			if( $result )
			{
				$msg = "success";
				$this->log->saveActivity("Added New Brand [$b_code].");
			}
			else
			{
				$msg = $result;
			}
			
			return $dataArray 		= array( "msg" => $msg, "brand_code" => $posted_data["brandcode"], "brand_name" => $posted_data["brandname"] );
		}

		public function view($code)
		{
			$this->view->title 	= $this->ui->ViewLabel('');
			
			$data 			 	= (array) $this->brand->retrieveExistingBrand($this->fields, $code);
			
			$data['ui'] 		= $this->ui;
			$data['show_input'] = false;
			$data['task'] 		= "";
			$data['ajax_post'] 	= "";

			$this->view->load('brand/brand', $data);
		}

		public function edit($code)
		{
			$this->view->title = $this->ui->EditLabel('');
			
			$data 			 	= (array) $this->brand->retrieveExistingBrand($this->fields, $code);
			$data['ui'] 		= $this->ui;
			$data['task'] 		= 'update';
			$data['show_input'] = true;
			$data['ajax_post'] 	= "&code=$code";

			$this->view->load('brand/brand',  $data);
		}

		private function update()
		{
			$posted_data 	= $this->input->post($this->fields);
			$code 		 	= $this->input->post('code');

			$result 		= $this->brand->updateBrand($posted_data, $code);

			if( $result )
			{
				$msg = "success";
				$this->log->saveActivity("Updated brand [$code] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray 		= array( "msg" => $msg, "brand_code" => $posted_data["brandcode"], "brand_name" => $posted_data["brandname"] );
		}

		private function delete()
		{
			$id_array 		= array('id');
			$id       		= $this->input->post($id_array);
			
			$result 		= $this->brand->deleteBrand($id);
			
			if( empty($result) )
			{
				$msg = "success";
				$this->log->saveActivity("Deleted Brand [". implode($id, ', ') ."] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray 		= array( "msg" => $msg);
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
				$result = $this->brand->check_duplicate($current);

				$count = $result[0]->count;
			}
			
			$msg   = "";

			if( $count > 0 )
			{	
				$msg = "exists";
			}

			return $dataArray = array("msg" => $msg);
		}
        
        private function brand_list() {

			$search = $this->input->post("search");
			$sort 	= $this->input->post('sort');
			$limit  = $this->input->post('limit');
			$list 	= $this->brand->retrieveListing($search, $sort, $limit);

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
										->setValue($row->brandcode)
										->draw();

					$table .= '<tr>';
					$table .= ' <td align = "center">' .$dropdown. '</td>';
					$table .= '<td>' . $row->brandcode . '</td>';
					$table .= '<td>' . $row->brandname. '</td>';
					$table .= '<td>' . $status. '</td>';

					$table .= '</tr>';
				}
			else:
				$table .= "<tr>
								<td colspan = '3' class = 'text-center'>No Records Found</td>
						</tr>";
			endif;


			$list->table 	=	$table;
			$list->csv		=	$this->export();
			return $list;
		}

		private function ajax_edit_activate()
		{
			$code = $this->input->post('id');
			$data['stat'] = 'active';

			$result = $this->brand->updateStat($data,$code);
			return array(
				'redirect'	=> MODULE_URL,
				'success'	=> $result
				);
		}

		private function ajax_edit_deactivate()
		{
			$code = $this->input->post('id');
			$data['stat'] = 'inactive';

			$result = $this->brand->updateStat($data,$code);
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
				$result 			= 	$this->brand->updateStat($data, $value);
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
				$result 			= 	$this->brand->updateStat($data, $value);
			}
	
			if($result)
			{
				$msg = "success";
			} else {
				$msg = "Failed to Update.";
			}
	
			return $dataArray = array( "msg" => $msg );
		}

		private function export(){
			
			$search = $this->input->post("search");
			$sort 	= $this->input->post('sort');

			$header = array('brandcode','brandname','stat');
			
			$prev 	= '';
			$next 	= '';

			$csv = '';
			$csv .= '"' . implode('", "', $header) . '"';
			$csv .= "\n";
			
			$result = $this->brand->getExport($search, $sort);
			if (!empty($result)){
				foreach ($result as $key => $row){

					$prev	= 	$row->brandcode;
					
					$csv .= '"' . $row->brandcode . '",';
					$csv .= '"' . $row->brandname . '",';
					$csv .= '"' . $row->stat . '",';
					
					// $csv .= '"' . $row->customercode . '"';
					$csv .= "\n";

					$next 	= $prev;
				}
			}

			return $csv;
		}
	
        
    }

?>