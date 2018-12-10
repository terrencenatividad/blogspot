<?php
    class job extends wc_model
    {
        public function __construct() 
        {
            parent::__construct();
            $this->log = new log();
        }

        public function getJobListing($data, $sort, $search, $filter)
        {
            $condition = '';
            if ($search) {
                $condition .= $this->generateSearch($search, array('job_no', 'notes', 'stat'));
            }
            //var_dump($search);
            $result = $this->db->setTable('job')
            ->setFields($data)
            ->setWhere($condition)
            ->setOrderBy($sort)
            ->runPagination();
            return $result;
        }

        private function generateSearch($search, $array) {
            $temp = array();
            foreach ($array as $arr) {
                $temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
            }
            return '(' . implode(' OR ', $temp) . ')';
        }

        public function getAllJob() {

            $result = $this->db->setTable('job')
                                ->runPagination();
            return $result;
        }

        public function deleteJob($id)
        {
            $cond   = "";
            $pieces = explode(',', $id["id"]);
            $errmsg = array();

            for($i = 0; $i < count($pieces); $i++)
            {
                $id     = $pieces[$i];

                $cond = "job_no = '$id'";

                $result = $this->db->setTable('job')
                ->setWhere($cond)
                ->runDelete();
                
                if(!$result)
                    $errmsg[] = "<p class = 'no-margin'>Deleting JOB No.: $id</p>";
                else
                    $this->log->saveActivity("Delete JOB No. [$id]");
            }

            return $errmsg;
        }
        
        public function check_journalDm($job_no)
		{
			return $this->db->setTable('journaldetails')
							->setFields('COUNT(job_no) count')
							->setWhere(" job_no = '$job_no' AND debit !=0.00")
							->runSelect()
							->getResult();
        }

        public function check_journalCm($job_no)
		{
			return $this->db->setTable('journaldetails')
							->setFields('COUNT(job_no) count')
							->setWhere(" job_no = '$job_no' AND credit !=0.00")
							->runSelect()
							->getResult();
        }
        
        public function check_importationCost($job_no)
		{
			return $this->db->setTable('ap_details')
							->setFields('COUNT(voucherno) count')
							->setWhere(" job_no = '$job_no' AND debit !=0.00")
							->runSelect()
							->getResult();
        }

        public function check_usage($job_no)
		{
			return $this->db->setTable('job')
							->setFields('COUNT(brandcode) count')
							->setWhere(" brandcode = '$job_no'")
							->runSelect()
							->getResult();
		}

        public function cancel_job($id)
		{
			$condition   = "";
			$id_array 	 = explode(',', $id['id']);
			$errmsg 	 = array();

			for($i = 0; $i < count($id_array); $i++)
			{
				$job_no    = $id_array[$i];

                $condition 		= " job_no = '$job_no'";
                $data['stat']		           =	'cancelled';

                $checkExisting 	= $this->check_importationCost($job_no);
                //var_dump($checkExisting);

                // checking for debit memo and credit memo? 
				if($checkExisting[0]->count > 0)
				{	
                    $errmsg[]  = "<p class = 'no-margin'> Job no: $job_no is already tagged with importation cost/s. Cannot cancel.</p>";
				}
				else
				{
                    $result 		= $this->db->setTable('job')
                                        ->setValues($data)
										->setWhere($condition)
										->runUpdate();
										// echo $this->db->getQuery();

					$error 			= $this->db->getError();	
				}
			}
			
			return $errmsg;
		}
        
    }

?>