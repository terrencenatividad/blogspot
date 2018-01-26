<?php
	class audit_trail extends wc_model
	{
	
		public function retrieveListing($search, $startdate, $enddate){
			$condition = '';
			$fields 			= array('timestamps','username','activitydone','module','task');
			if ($startdate && $enddate) {
			$condition .= "timestamps >= '$startdate 00:00:00' AND timestamps <= '$enddate 23:59:59' ";
			}
			if ($search) {
			$condition .=" AND (timestamps LIKE '%$search%' OR username LIKE '%$search%' OR activitydone LIKE '%$search%' OR module LIKE '%$search%' OR task LIKE '%$search%') ";
			}
			return $this->db->setTable('wc_admin_logs')
							->setFields($fields)
							->leftJoin('')
							->setWhere("$condition")
							->setOrderBy('wc_admin_logsid DESC')
							->runPagination();
							// echo $this->db->getQuery();

		}
	}
?>