<?php
    class job extends wc_model
    {
        public function __construct() {
            parent::__construct();
            $this->log = new log();
        }

        public function getJobListing($data, $sort, $search, $filter){
            $condition = '';
            if ($search) {
                $condition .= $this->generateSearch($search, array('job_no', 'notes'));
            }

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

        public function deleteJob($id)  {
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
        
        public function check_jobStatus($job_no)  {
            return $this->db->setTable('job')
                            ->setFields('COUNT(job_no) count')
                            ->setWhere(" job_no = '$job_no'")
                            ->runSelect()
                            ->getResult();
        }
        
        public function check_importationCost($job_no) {
            return $this->db->setTable('job')
                            ->setFields('COUNT(job_no) count')
                            ->setWhere(" job_no = '$job_no'")
                            ->runSelect()
                            ->getResult();
        }

        public function getIPOPagination() {
            $result = $this->db->setTable("purchaseorder")
                            ->setFields("transactiondate, voucherno, amount")
                            ->setWhere("stat='open'")
                            ->setOrderBy("transactiondate")
                            ->runPagination();
            return $result;
        }

        public function getItemPagination($ipo_number){
            $result = $this->db->setTable("purchaseorder_details")
                            ->setFields("voucherno, itemcode, detailparticular, receiptqty, receiptuom")
                            ->setWhere("voucherno='".$ipo_number."'")
                            ->setOrderBy("voucherno")
                            ->runPagination();
            return $result;
        }

        public function getTaggedItemQty($ipo, $itemcode){
            $result = $this->db->setTable("job_details")
                            ->setFields("COUNT(*) as count")
                            ->setWhere("ipo_no='".$ipo."' AND itemcode='".$itemcode."'")
                            ->runSelect()
                            ->getResult();
            return $result;
        }

        public function retrieveExistingJob($job){
            $result = $this->db->setTable("job_details")
                            ->setFields("ipo_no, itemcode")
                            ->setWhere("job_no='".$job."'")
                            ->runSelect()
                            ->getResult();
            return $result;
        }

        public function getJob($job){
            $result = $this->db->setTable("job")
                            ->setFields("transactiondate,notes")
                            ->setWhere("job_no='".$job."'")
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

        
    }

?>