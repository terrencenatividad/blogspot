<?php
class controller extends wc_controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->url 			    = new url();
		$this->at 				= new audit_trail();
		$this->input            = new input();
		$this->ui 			    = new ui();
		$this->view->title      = 'Audit Trail';
		$this->show_input 	    = true;
	}

	public function listing()
	{
		$data['ui'] 				= $this->ui;
		$data['show_input'] 		= true;
		$data['date_today'] 		= date("M d, Y");
		$data['datefilter'] 		= $this->date->datefilterMonth();
		$this->view->load('audit_trail/audit_traillist', $data);
	}


	public function ajax($task)
	{
		header('Content-type: application/json');
		$result 	=	"";

		if ($task == 'at_listing') 
		{
			$result = $this->at_listing();
		}
		echo json_encode($result); 
	}

	private function at_listing()
	{
		$data = $this->input->post(array('search', 'daterangefilter'));
		$search = $data['search'];
		$datefilter	= $data['daterangefilter'];
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		
		$pagination = $this->at->retrieveListing($search, $dates[0], $dates[1]);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
				$table .= '<tr>';
				$table .= '<td>' . $this->date->datetimeFormat($row->timestamps) . '</td>';
				$table .= '<td>' . $row->username. '</td>';
				$table .= '<td>' . $row->activitydone . '</td>';
				$table .= '<td>' . $row->module . '</td>';
				$table .= '<td>' . $row->task . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}	

}
?>