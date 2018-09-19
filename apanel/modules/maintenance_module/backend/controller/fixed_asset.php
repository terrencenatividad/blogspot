<?php
	class controller extends wc_controller 
	{
		public function __construct()
		{
			parent::__construct();

			$this->fixed_asset 	=	new fixed_asset();
			$this->input 		=	new input();
			$this->ui 			= 	new ui();
			$this->url 			=	new url();
			$this->log 			=  	new log();

			$this->view->header_active = 'maintenance/fixed_asset/';

			$this->fields = array(
				'itemno',
				'description',
				'category',
				'location',
				'amount'
			);
			$this->fields1 = array(
				'description'
			);
		}

		public function listing(){
			$this->view->title = $this->ui->ListLabel('');
			$data['ui'] 	   = $this->ui;
			$this->view->load('fixed_asset/fixed_asset_list', $data);
		}

		public function create()
		{
			$this->view->title = $this->ui->AddLabel('');
			
			$data 				= $this->input->post($this->fields);
			$data['ui'] 		= $this->ui;
			$data['category_list']	= $this->fixed_asset->getCategory();
			$data['ajax_task'] 		= 'ajax_create';
			$data['show_input'] = true;
			$data['ajax_post'] 	= '';

			$this->view->load('fixed_asset/fixed_asset', $data);
		}

		public function edit($code)
		{
			$this->view->title = $this->ui->EditLabel('');
			
			$data 			 	= (array) $this->fixed_asset->retrieveExistingFixedAsset($this->fields, $code);
			$data['ui'] 		= $this->ui;
			$data['category_list']	= $this->fixed_asset->getCategory();
			$data['ajax_task'] 	= 'ajax_edit';
			$data['show_input'] = true;
			$data['ajax_post'] 	= "&code=$code";

			$this->view->load('fixed_asset/fixed_asset',  $data);
		}

		public function view($code)
		{
			$this->view->title 	= $this->ui->ViewLabel('');
			
			$data 			 	= (array) $this->fixed_asset->retrieveExistingFixedAsset($this->fields, $code);
			
			$data['ui'] 		= $this->ui;
			$data['category_list']	= $this->fixed_asset->getCategory();
			$data['show_input'] = false;
			$data['task'] 		= "";
			$data['ajax_post'] 	= "";

			$this->view->load('fixed_asset/fixed_asset', $data);
		}

		public function listing_category(){
			$this->view->title = 'Fixed Asset Category';
			$data['ui'] 	   = $this->ui;
			$this->view->load('fixed_asset/fixed_asset_category_list', $data);
		}

		public function add_category()
		{
			$this->view->title = 'Add Fixed Asset Category';
			
			$data 				= $this->input->post($this->fields);
			$data['ui'] 		= $this->ui;
			$data['ajax_task'] 	= 'ajax_create_category';
			$data['show_input'] = true;
			$data['ajax_post'] 	= '';

			$this->view->load('fixed_asset/fixed_asset_category', $data);
		}

		public function edit_category($code)
		{
			$this->view->title = $this->ui->EditLabel('');
			
			$data 			 	= (array) $this->fixed_asset->retrieveExistingFixedAssetCategory($this->fields1, $code);
			$data['ui'] 		= $this->ui;
			$data['ajax_task'] 	= 'ajax_edit_category';
			$data['show_input'] = true;
			$data['ajax_post'] 	= "&code=$code";

			$this->view->load('fixed_asset/fixed_asset_category',  $data);
		}

		public function view_category($code)
		{
			$this->view->title 	= $this->ui->ViewLabel('');
			
			$data 			 	= (array) $this->fixed_asset->retrieveExistingFixedAssetCategory($this->fields1, $code);
			
			$data['ui'] 		= $this->ui;
			$data['show_input'] = false;
			$data['task'] 		= "";
			$data['ajax_post'] 	= "";

			$this->view->load('fixed_asset/fixed_asset_category', $data);
		}

		private function ajax_list_category() {

			$search = $this->input->post("search");
			$sort 	= $this->input->post('sort');
			$limit  = $this->input->post('limit');
			$list 	= $this->fixed_asset->retrieveListingCategory($search, $sort, $limit,$this->fields1);

			$table 	= '';

			if( !empty($list->result) ) :
				foreach ($list->result as $key => $row) {

					$dropdown = $this->ui->loadElement('check_task')
										->addOtherTask('View','','view')
										->addOtherTask('Edit','','edit')
										->addOtherTask('Delete','','delete')
										->addCheckbox()
										->setValue($row->id)
										->draw();

					$table .= '<tr>';
					$table .= ' <td align = "center">' .$dropdown. '</td>';
					$table .= '<td>' . $row->description. '</td>';

					$table .= '</tr>';
				}
			else:
				$table .= "<tr>
								<td colspan = '3' class = 'text-center'>No Records Found</td>
						</tr>";
			endif;

			$list->table 	=	$table;

			return $list;
		}

		public function ajax($task) {
			$ajax = $this->{$task}();
			if ($ajax) {
				header('Content-type: application/json');
				echo json_encode($ajax);
			}
		}
		
		private function ajax_list() {

			$search = $this->input->post("search");
			$sort 	= $this->input->post('sort');
			$limit  = $this->input->post('limit');
			$list 	= $this->fixed_asset->retrieveListing($search, $sort, $limit,$this->fields);

			$table 	= '';

			if( !empty($list->result) ) :
				foreach ($list->result as $key => $row) {

					$dropdown = $this->ui->loadElement('check_task')
										->addView()
										->addEdit()
										->addDelete()
										->addCheckbox()
										->setValue($row->itemno)
										->draw();

					$table .= '<tr>';
					$table .= ' <td align = "center">' .$dropdown. '</td>';
					$table .= '<td>' . $row->itemno . '</td>';
					$table .= '<td>' . $row->description. '</td>';
					$table .= '<td>' . $row->category. '</td>';
					$table .= '<td>' . $row->location. '</td>';
					$table .= '<td>' . $row->amount. '</td>';

					$table .= '</tr>';
				}
			else:
				$table .= "<tr>
								<td colspan = '3' class = 'text-center'>No Records Found</td>
						</tr>";
			endif;

			$list->table 	=	$table;

			return $list;
		}

		private function ajax_create()
		{
			$posted_data 	= $this->input->post($this->fields);	
			$result  		= $this->fixed_asset->insertFixedAsset($posted_data);

			$itemno 		= $posted_data["itemno"];

			if( $result )
			{
				$msg = "success";
				$this->log->saveActivity("Added New Fixed Asset [$itemno].");
			}
			else
			{
				$msg = $result;
			}
			
			return $dataArray 		= array( "msg" => $msg, "itemno" => $posted_data["itemno"], "description" => $posted_data["description"] );
		}

		private function ajax_edit()
		{
			$posted_data 	= $this->input->post($this->fields);
			$code 		 	= $this->input->post('code');

			$result 		= $this->fixed_asset->updateFixedAsset($posted_data, $code);

			if( $result )
			{
				$msg = "success";
				$this->log->saveActivity("Updated fixed asset [$code] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray 		= array( "msg" => $msg, "itemno" => $posted_data["itemno"], "description" => $posted_data["description"] );
		}

		private function delete()
		{
			$id_array 		= array('id');
			$id       		= $this->input->post($id_array);
			
			$result 		= $this->fixed_asset->deleteFixedAsset($id);

			if( empty($result) )
			{
				$msg = "success";
				$this->log->saveActivity("Deleted fixed_asset [". implode($id, ', ') ."] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray 		= array( "msg" => $msg);
		}

		private function delete_category()
		{
			$id_array 		= array('id');
			$id       		= $this->input->post($id_array);
			
			$result 		= $this->fixed_asset->deleteFixedAssetCategory($id);

			if( empty($result) )
			{
				$msg = "success";
				$this->log->saveActivity("Deleted Fixed Asset Category [". implode($id, ', ') ."] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray 		= array( "msg" => $msg);
		}

		private function ajax_create_category()
		{
			$posted_data 	= $this->input->post($this->fields1);	
			$result  		= $this->fixed_asset->insertFixedAssetCategory($posted_data);

			if( $result )
			{
				$msg = "success";
			}
			else
			{
				$msg = $result;
			}
			
			return $dataArray 		= array( "msg" => $msg, "description" => $posted_data["description"] );
		}

		private function ajax_edit_category()
		{
			$posted_data 	= $this->input->post($this->fields1);
			$code 		 	= $this->input->post('code');
			
			$result 		= $this->fixed_asset->updateFixedAssetCategory($posted_data, $code);

			if( $result )
			{
				$msg = "success";
				$this->log->saveActivity("Updated Fixed Asset Category [$code] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray 		= array( "msg" => $msg, "description" => $posted_data["description"] );
		}
		
		private function get_duplicate(){
			$current = $this->input->post('curr_code');
			$old 	 = $this->input->post('old_code');
			$count 	 = 0;

			if( $current!='' && $current != $old )
			{
				$result = $this->fixed_asset->check_duplicate($current);

				$count = $result[0]->count;
			}
			
			$msg   = "";

			if( $count > 0 )
			{	
				$msg = "exists";
			}

			return $dataArray = array("msg" => $msg);
		}

		private function ajax_edit_activate()
		{
			$code = $this->input->post('id');
			$data['stat'] = 'active';

			$result = $this->fixed_asset->updateStat($data,$code);
			return array(
				'redirect'	=> MODULE_URL,
				'success'	=> $result
				);
		}
		
		private function ajax_edit_deactivate()
		{
			$code = $this->input->post('id');
			$data['stat'] = 'inactive';

			$result = $this->fixed_asset->updateStat($data,$code);
			return array(
				'redirect'	=> MODULE_URL,
				'success'	=> $result
				);
		}
	}
?>