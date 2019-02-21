<?php
    class job extends wc_model
    {
        public function __construct() 
        {
            parent::__construct();
            $this->log = new log();
        }

        public function getJobListing($data, $sort, $search, $filter, $daterange)
        {
            $condition = "stat != 'temporary'" ;
            $daterangefilter    = isset($daterange) ? htmlentities($daterange) : ""; 
            $datefilterArr      = explode(' - ',$daterangefilter);
            $datefilterFrom     = (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
            $datefilterTo       = (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
            if ($search) {
                $condition .= "AND " . $this->generateSearch($search, array('job_no', 'notes', 'stat'));
            }
            $condition .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
            //var_dump($search);
            $result = $this->db->setTable('job')
            ->setFields($data)
            ->setWhere($condition)
            ->setOrderBy($sort)
            ->runPagination();
            
            return $result;
        }

        public function getJobVouchers($job=''){
            $result = $this->db->setTable('job')
                                ->setFields('job_no')
                                ->setWhere("job_no!='$job'")
                                ->runSelect()
                                ->getResult();
            return $result;
        }

        public function checkUsedJob($job){
            $result = $this->db->setTable('accountspayable, journalvoucher')
                                ->setFields('job_no')
                                ->setWhere("stat='posted' AND job_no='$job'")
                                ->runSelect()
                                ->getResult();
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
        
        public function check_importationCost($job_no)
		{
			return $this->db->setTable('financial_jobs')
							->setFields('COUNT(voucherno) count')
							->setWhere(" job_no = '$job_no'")
							->runSelect(false)
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
                    $result         = $this->db->setTable('job_details')
                                        ->setValues($data)
                                        ->setWhere($condition)
                                        ->runUpdate();
										// echo $this->db->getQuery();

					$error 			= $this->db->getError();	
				}
			}
			
			return $errmsg;
        }
        
        public function getIPOPagination($search='', $job) {
            $addcond = '';
            if ($search != '') {
                $addcond = "AND ipo.voucherno LIKE '%$search%'";
            }
            $pagination = $this->db->setTable("import_purchaseorder ipo")
                            ->setFields("ipo.voucherno, ipo.transactiondate")
                            ->leftJoin("import_purchaseorder_details ipod ON ipod.voucherno=ipo.voucherno")
                            
                            ->leftJoin("job_details jd ON jd.ipo_no=ipod.voucherno AND jd.linenum=ipod.linenum AND jd.stat!='cancelled'")
                            ->setWhere("ipo.stat IN ('open', 'partial', 'posted') AND ipod.receiptqty - COALESCE((SELECT SUM(qty) FROM job_details WHERE ipo_no=ipod.voucherno AND stat!='cancelled'),0) > 0 $addcond ")
                            ->setGroupBy("ipo.voucherno")
                            ->setOrderBy("ipo.transactiondate DESC, ipo.voucherno DESC")
                            ->runPagination();
                            // $query = $this->db->getQuery();
                            // var_dump($query);
            return $pagination;
        }

        public function getItemPagination($ipo_number){
            $result = $this->db->setTable("import_purchaseorder_details")
                            ->setFields("voucherno, itemcode, linenum, detailparticular, receiptqty, receiptuom")
                            ->setWhere("voucherno='".$ipo_number."'")
                            ->setOrderBy("voucherno DESC, linenum ASC")
                            ->runPagination();
            return $result;
        }

        public function getTaggedItemQty($ipo, $linenum, $job="", $task="") {
            if ($task == 'save') {
                $condition = "jd.ipo_no='".$ipo."' AND jd.linenum='".$linenum."' AND j.stat='on-going'";
            }
            else {
                $condition = "jd.ipo_no='".$ipo."' AND jd.linenum='".$linenum."' AND jd.job_no != '".$job."' AND j.stat='on-going'";
            }
            $result = $this->db->setTable("job_details jd")
                            ->setFields("COALESCE(SUM(jd.qty), 0) AS count")
                            ->leftJoin("job j ON j.job_no = jd.job_no")
                            ->setWhere($condition)
                            ->runSelect()
                            ->getRow();

            return $result;
        }

        public function getThisQty($ipo, $linenum, $job) {
            $result = $this->db->setTable("job_details jd")
                            ->setFields("SUM(jd.qty) AS count")
                            ->leftJoin("job j ON j.job_no = jd.job_no")
                            ->setWhere("jd.ipo_no='".$ipo."' AND jd.linenum='".$linenum."' AND jd.job_no = '".$job."'")
                            ->runSelect()
                            ->getRow();
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
            $result = $this->db->setTable("job_details jd")
                            ->setFields("jd.ipo_no, jd.itemcode, jd.linenum, jd.description, jd.qty, ipod.receiptqty, jd.uom")
                            ->leftJoin('import_purchaseorder_details ipod ON ipod.voucherno = jd.ipo_no AND ipod.itemcode = jd.itemcode')
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

        public function getItemCost($ipo,$itemcode,$linenum) {
            $select = $this->db->setTable('import_purchaseorder_details')
                                ->setFields('(convertedamount / receiptqty) AS itemcost')
                                ->setWhere("voucherno = '$ipo' AND itemcode = '$itemcode' AND linenum = '$linenum'")
                                ->runSelect()
                                ->getRow();
                                // echo $this->db->getQuery();
            
            $result = $select->itemcost;

            return $result;
        }
        
    }

?>