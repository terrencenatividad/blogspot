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
        
        public function getPRPagination() {
            $result = $this->db->setTable("purchasereceipt")
                            ->setFields("voucherno, source_no, transactiondate, amount")
                            ->setWhere("stat='Received' AND transtype='IPO'")
                            ->setOrderBy("voucherno ASC")
                            ->runPagination();
            return $result;
        }

        public function getItemPagination($pr_number){
            $result = $this->db->setTable("purchasereceipt_details")
                            ->setFields("voucherno, itemcode, linenum, detailparticular, receiptqty, receiptuom")
                            ->setWhere("voucherno='".$pr_number."'")
                            ->setOrderBy("voucherno ASC, linenum ASC")
                            ->runPagination();
            return $result;
        }

        public function getTaggedItemQty($ipo, $itemcode, $job=""){
            
            $result = $this->db->setTable("job_details jd")
                            ->setFields("SUM(jd.qty) AS count")
                            ->leftJoin("job j ON j.job_no = jd.job_no")
                            ->setWhere("jd.ipo_no='".$ipo."' AND jd.itemcode='".$itemcode."' AND jd.job_no != '".$job."' AND j.stat='on-going'")
                            ->runSelect()
                            ->getResult();
            return $result;
        }

        public function autoGenerate($prefix, $table){
            $gen_value = $this->db->setTable($table)
                            ->setFields("COUNT(*) AS count")
                            ->setWhere("job_no!=''")
                            ->runSelect(false)
                            ->getResult();
            
            if ($gen_value[0]->count>0) {
                $result = $prefix;
                $number = $gen_value[0]->count + 1;
                $zeros = 10 - strlen($number);
                
                for ($zeros; $zeros > 0; $zeros--) { 
                    $result.=0;
                }
                $result.=$number;
            }
            else{
                $result = $prefix."0000000001";
            }
            
            return $result;
        }

        public function saveFromPost($table, $values){
            $result = $this->db->setTable($table)
                            ->setValuesFromPost($values)
                            ->runInsert();
                           
            return $result;
        }

        public function saveValues($table, $values){
            $result = $this->db->setTable($table)
                            ->setValues($values)
                            ->runInsert();
                            
            return $result;
        }

        public function deleteJobValues($table, $value){
            $result = $this->db->setTable($table)
                            ->setWhere("job_no = '".$value."'")
                            ->runDelete();
                            
            return $result;
        }
        
        public function updateJobValues($values, $job_no){
            $result = $this->db->setTable("job")
                            ->setFields("job_no, notes, transactiondate, stat")
                            ->setValues($values)
                            ->setWhere("job_no = '".$job_no."'")
                            ->runUpdate();
            return $result;
        }

        public function retrieveExistingJob($job){
            $result = $this->db->setTable("job_details")
                            ->setFields("ipo_no, itemcode, linenum, description, qty")
                            ->setWhere("job_no='".$job."'")
                            ->setOrderBy("ipo_no ASC, linenum ASC")
                            ->runSelect()
                            ->getResult();
            return $result;
        }

        public function getJob($job){
            $result = $this->db->setTable("job")
                            ->setFields("transactiondate,notes,stat")
                            ->setWhere("job_no='".$job."'")
                            ->runSelect()
                            ->getResult();
            return $result;
        }

        
        
    }

?>