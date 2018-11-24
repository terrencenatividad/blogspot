<?php
class controller extends wc_controller 
{
	public function __construct() {
		parent::__construct();
		$this->url 			= new url();
		$this->bom 	= new bom();
		$this->input        = new input();
		$this->ui 			= new ui();
		$this->logs  		= new log;
		$this->fields 			= array(
			'id',
			'bom_code',
			'bundle_item_code',
			'description',
			'status'
		);

		$this->bomdetails 			= array(
			'id',
			'bom_code',
			'item_code',
			'item_name',
			'detailsdesc',
			'quantity',
			'uom'
		);

		$data = array();
	}

	public function listing() {
		$this->view->title  = MODULE_NAME;
		$data['ui'] = $this->ui;
		$this->view->load('bom/bom_list',$data);
	}

	public function create() {
		$this->view->title  = 'Add Bill of Materials';
		$data 				= $this->input->post($this->fields);
		$data['bundle_list'] = $this->bom->getBundleList();
		$data['item_list'] = $this->bom->getItemList();
		$data['ui'] 		= $this->ui;
		$data['ajax_task'] 	= 'ajax_create';
		$data['ajax_post'] 	= '';
		$data['show_input'] = true;
		$this->view->load('bom/bom',$data);
	}

	public function edit($id) {
		$this->view->title  = 'Edit Bill of Materials';
		$data = (array) $this->bom->getBOMById($this->fields, $id);
		$bomcode = $data['bom_code'];
		$details = $this->bom->getDetails($this->bomdetails, $bomcode);
		$data['bundle_list'] = $this->bom->getBundleList();
		$data['item_list'] = $this->bom->getItemList();
		$data['bomdetails'] = $details;
		$data["ajax_task"] = "ajax_edit";
		$data['ui'] = $this->ui;
		$data['ajax_post'] = "&id=$id";
		$data['show_input'] = true;
		$this->view->load('bom/bom',$data);
	}

	public function view($id) {
		$this->view->title  = 'View Bill of Materials';
		$data = (array) $this->bom->getBOMById($this->fields, $id);
		$bomcode = $data['bom_code'];
		$details = $this->bom->getDetails($this->bomdetails, $bomcode);
		$data['bundle_list'] = $this->bom->getBundleList();
		$data['item_list'] = $this->bom->getItemList();
		$data['bomdetails'] = $details;
		$data["ajax_task"] = "ajax_view";
		$data['ajax_post'] = "&id=$id";
		$data['ui'] = $this->ui;
		$data['show_input'] = false;
		$this->view->load('bom/bom',$data);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_create() {
		$bom = $this->input->post($this->fields);
		$bom_details = $this->input->post($this->bomdetails);
		$this->seq = new seqcontrol();

		$bom['bom_code'] = $this->seq->getValue('BOM');
		$bom['status'] = 'active';
		$bom_details['bom_code'] = $bom['bom_code'];
		$bom_details['quantity'] = str_replace(',', '' ,$bom_details['quantity']);
		$result = $this->bom->saveBOM($bom, $bom_details);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_edit() {
		$id = $this->input->post('id');
		$bom = $this->input->post($this->fields);
		$bom['status'] = 'active';
		$bom_details = $this->input->post($this->bomdetails);
		$bomcode = $bom['bom_code'];
		$result = $this->bom->updateBOM($bom, $id, $bomcode);
		$bom_details['bom_code'] = $bom['bom_code'];
		$bom_details['id'] = '';
		$bom_details['quantity'] = str_replace(',', '' ,$bom_details['quantity']);
		$details = $this->bom->saveDetails($bom_details);

		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $details
		);
	}

	private function colorStat($stat) {
		$color = 'default';
		switch ($stat) {
			case 'active':
			$color = 'success';
			break;
			case 'inactive':
			$color = 'warning';
			break; 
		}
		return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
	}

	private function ajax_list() {
		$data	= $this->input->post(array('search', 'sort', 'filter'));
		extract($data);
		$pagination = $this->bom->getBOMListing($this->fields, $sort, $search, $filter);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach($pagination->result as $row) {
			$show_activate 		= ($row->status != 'inactive');
			$show_deactivate 	= ($row->status != 'active');
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
			->setValue($row->id)
			->draw();
			
			$table .= '<tr>';
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $row->bom_code . '</td>';
			$table .= '<td>' . $row->bundle_item_code . '</td>';
			$table .= '<td>' . $row->description . '</td>';
			$table .= '<td>' . $this->colorStat($row->status) . '</td>';
			$table .= '</tr>';
		}

		$pagination->table = $table;

		return $pagination;
	}

	public function ajax_delete() {
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
			$result = $this->bom->deleteBOM($id);

			$dataArray = array( "msg" => $result );
			return $dataArray;
		}

		private function ajax_get_details() {
			$itemcode = $this->input->post('itemcode');
			$details = $this->bom->getItemDetails($itemcode);
			$name = $details->itemname;
			$desc = $details->itemdesc;
			$uom_base = $details->uom_base;

			return array('itemname' => $name, 'itemdesc' => $desc, 'uom' => $uom_base);
		}

	// activate/deactivate

		private function ajax_edit_activate() {
			$code = $this->input->post('id');
			$data['status'] = 'active';

			$result = $this->bom->updateStat($data,$code);
			return array(
				'redirect'	=> MODULE_URL,
				'success'	=> $result
			);
		}

		private function ajax_edit_deactivate() {
			$code = $this->input->post('id');
			$data['status'] = 'inactive';

			$result = $this->bom->updateStat($data,$code);
			return array(
				'redirect'	=> MODULE_URL,
				'success'	=> $result
			);
		}

		private function update_multiple_deactivate() {
			$posted_data 			=	$this->input->post(array('ids'));

			$data['status'] 			=	'inactive';

			$posted_ids 			=	$posted_data['ids'];
			$id_arr 				=	explode(',',$posted_ids);

			foreach($id_arr as $key => $value)
			{
				$result 			= 	$this->bom->updateStat($data, $value);
			}

			if($result)
			{
				$msg = "success";
			} else {
				$msg = "Failed to Update.";
			}

			return $dataArray = array( "msg" => $msg );
		}

		private function update_multiple_activate() {
			$posted_data 			=	$this->input->post(array('ids'));

			$data['status'] 			=	'active';

			$posted_ids 			=	$posted_data['ids'];
			$id_arr 				=	explode(',',$posted_ids);

			foreach($id_arr as $key => $value)
			{
				$result 			= 	$this->bom->updateStat($data, $value);
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