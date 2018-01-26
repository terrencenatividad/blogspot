<?php
	class period extends wc_model
	{
		public function __construct() {
		parent::__construct();
		$this->log = new log();
		}

		public function retrieveListing($data, $search, $sort)
		{
			$condition = '';
			$data[] = 'stat';
			if (!empty($search)){
				$condition = "(period LIKE '%$search%' OR fiscalyear LIKE '%$search%' OR startdate = '%$search%' OR enddate = '%$search%' OR stat = '%$search%' ) ";
			}
			return 	 $this->db->setTable('period')
							->setFields($data)
							->setWhere(''.$condition)
							->setOrderBy($sort)
							->runPagination();
		}

		public function retrieveExistingPeriod($data,$id)
		{
			$ids	= "'" . implode("','", $data) . "'";
			$condition 		=	" id IN ($ids)";
			return $this->db->setTable('period')
							->setFields($data)
							->runSelect()
							->getRow();
		}

		public function insertPeriod($data)
		{
			$session               = new session();
			$datetime              = date("Y-m-d H:i:s");
			$data["stat"]          = "open";
			$old_date_timestamp    = strtotime($_POST['startdate']);
			$data['startdate']     = date('Y-m-d', $old_date_timestamp);
			$old_date              = strtotime($_POST['enddate']);
			$data['enddate']       = date('Y-m-d', $old_date);
			$result = $this->db->setTable('period')
					->setValues($data)
					->runInsert();

			if ($result) {
			$this->log->saveActivity("Created Period [{$data['period']}/{$data['fiscalyear']}]");
			}

			return $result;
		}


		public function validInsert($data,$startdate,$enddate)
		{
			$old_date_timestamp		= strtotime($startdate);
			$startdate				= date('Y-m-d', $old_date_timestamp);
			$old_date				= strtotime($enddate);
			$enddate				= date('Y-m-d', $old_date);
			$id						= isset($data['id']) ? $data['id'] : '';
			$condition = "((startdate <= '$startdate' AND enddate >= '$startdate') OR (startdate <= '$enddate' AND enddate >= '$enddate')) AND id != '$id'";
			$result =  $this->db->setTable('period')
								->setWhere($condition)
								->setFields('*')
								->setLimit(1)
								->runSelect()
								->getResult();	
								// echo $this->db->getQuery();
			return $result;
		}

		public function updatePeriod($data, $id) 
		{
			$data["stat"]     	   = "open";
			$old_date_timestamp    = strtotime($_POST['startdate']);
			$data['startdate']     = date('Y-m-d', $old_date_timestamp);
			$old_date              = strtotime($_POST['enddate']);
			$data['enddate']       = date('Y-m-d', $old_date);
			$condition 			   = " id='$id'";
			$result 			   = $this->db->setTable('period')
											  ->setValues($data)
											  ->setWhere($condition)
											  ->setLimit(1)
											  ->runUpdate();
			if ($result) {
			$this->log->saveActivity("Updated Period [{$data['period']}/{$data['fiscalyear']}]");
			}
			return $result;
		}

		public function deletePeriod($data, $posted_data) {
				$period			= isset($posted_data['period']) ? $posted_data['period'] : '';
				$fiscalyear		= isset($posted_data['fiscalyear']) ? $posted_data['fiscalyear'] : '';
				$ids	= "'" . implode("','", $data) . "'";
				$condition 		= " id  IN ($ids)";
				$result 		= $this->db->setTable('period')
										->setWhere($condition)
										->runDelete();
			if ($result) {
			$this->log->saveActivity("Deleted Period [$period/$fiscalyear]");
			}
				
		}

		// public function searchPeriod($data, $search)
		// {
			
			
		// 	$list = $this->db->setTable('period')
		// 				->setFields($data)
		// 				->setWhere($condition)
		// 				->runSelect()
		// 				->getResult();
		// 				echo $this->db->getQuery();
		// 	return $list;
		// }
	}
?>