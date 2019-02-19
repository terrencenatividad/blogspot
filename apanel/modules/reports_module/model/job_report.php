<?php
    class job_report extends wc_model
    {
        public function __construct() 
        {
            parent::__construct();
            $this->log = new log();
        }

        public function retrieveListing($data)
        {
            

            $daterangefilter 	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
            $jobno      	= isset($data['job_number']) ? htmlentities($data['job_number']) : ""; 
            $searchkey 		 	= isset($data['account_search']) ? htmlentities($data['account_search']) : "";
            $sort 		 	 	= isset($data['sort']) ? htmlentities($data['sort']) : "j.job_no ASC";
            
            $datefilterArr		= explode(' - ',$daterangefilter);
            $datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
            $datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
            
            $add_query   = (!empty($searchkey)) ? "AND (ca.segment5 LIKE '%$searchkey%' OR ca.accountname LIKE '%$searchkey%' )" : "";
            $add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND j.entereddate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
            $add_query .= (!empty($jobno) && $jobno != 'none') ? "AND j.job_no = '$jobno' " : "";
            //var_dump($add_query);
            

            // $fields 			= array('j.job_no,cc.id,cc.accountname,
            // ( (SUM(ad.debit) + IFNULL(SUM(jodDm.debit),0)) - IFNULL(SUM(jodCm.credit),0) ) amount,j.stat,cc.segment5');

            // $result = $this->db->setTable('job j')
            // ->setFields($fields)   
            // ->leftJoin('financial_jobs fj ON j.job_no = fj.job_no') 
            // ->innerJoin('ap_details ad ON fj.voucherno = ad.voucherno AND ad.debit != 0.00')
            // ->innerJoin('chartaccount cc ON cc.id = ad.accountcode')
            // ->leftJoin("journaldetails jodDm ON jodDm.job_no = j.job_no AND jodDm.transtype = 'DM' AND jodDm.debit != 0.00 AND jodDm.accountcode = cc.id")
            // ->leftJoin("journaldetails jodCm ON jodCm.job_no = j.job_no AND jodCm.transtype = 'CM' AND jodCm.credit != 0.00 AND jodDm.accountcode = cc.id")
            // ->setWhere(" j.stat != 'deleted' $add_query")
            // ->setGroupBy('ad.accountcode,j.job_no')
            // ->setOrderBy($sort)
            // ->runPagination();	    
            //echo $this->db->getQuery();
 
            // $costAllJobs    = $this->db->setTable('')
            

            $fields         = array('
                                    fj.job_no,
                                    bt.accountcode,
                                    ca.accountname,
                                    ca.segment5,
                                    ca.id,
                                    (SUM(bt.converted_debit) - SUM(bt.converted_credit)) AS amount,
                                    j.stat,
                                    bt.transtype,
                                    bt.voucherno
                                    ');

            $result         = $this->db->setTable('balance_table bt')
                                        ->setFields($fields)
                                        ->innerJoin('financial_jobs fj on fj.voucherno = bt.voucherno')
                                        ->leftJoin('chartaccount ca on ca.id = bt.accountcode')
                                        ->leftJoin('job j on j.job_no = fj.job_no')
                                        ->setWhere("bt.transtype IN('AP','CM','DM') AND j.job_no != '' 
                                        AND ((bt.transtype IN('AP','DM') AND bt.converted_credit = 0) OR (bt.transtype IN('CM') AND bt.converted_debit = 0))
                                        $add_query")
                                        ->setGroupBy('bt.accountcode, j.job_no')
                                        ->setOrderBy($sort)
                                        ->runPagination();
                                        // echo $this->db->getQuery();
            // echo $add_query;

            return $result;
        }

        public function get_allJob()
        {
            $result = $this->db->setTable('job')
            ->setFields("job_no ind, job_no val")
            //->setWhere("job_no != '' AND partnertype = 'supplier' AND stat = 'active'")
            ->setOrderBy("val")
            ->runSelect()
            ->getResult();

            return $result;
        }

        public function export_main($data)
        {
            $daterangefilter 	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
            $jobno      	= isset($data['job_number']) ? htmlentities($data['job_number']) : ""; 
            $searchkey 		 	= isset($data['account_search']) ? htmlentities($data['account_search']) : "";
            $sort 		 	 	= isset($data['sort']) ? htmlentities($data['sort']) : "j.job_no ASC";
            
            $datefilterArr		= explode(' - ',$daterangefilter);
            $datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
            $datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
            
            $add_query   = (!empty($searchkey)) ? "AND (ca.segment5 LIKE '%$searchkey%' OR ca.accountname LIKE '%$searchkey%' )" : "";
            $add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND j.entereddate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
            $add_query .= (!empty($jobno) && $jobno != 'none') ? "AND j.job_no = '$jobno' " : "";
            

            $fields         = array('
                                    fj.job_no,
                                    bt.accountcode,
                                    ca.accountname,
                                    ca.segment5,
                                    ca.id,
                                    (SUM(bt.converted_debit) - SUM(bt.converted_credit)) AS amount,
                                    j.stat,
                                    SUM(bt.converted_debit),
                                    SUM(bt.converted_credit),
                                    bt.transtype,
                                    bt.voucherno
                                    ');

            $result         = $this->db->setTable('balance_table bt')
                                        ->setFields($fields)
                                        ->innerJoin('financial_jobs fj on fj.voucherno = bt.voucherno')
                                        ->leftJoin('chartaccount ca on ca.id = bt.accountcode')
                                        ->leftJoin('job j on j.job_no = fj.job_no')
                                        ->setWhere("bt.transtype IN('AP',
                                        'CM','DM') AND j.job_no != '' 
                                        AND ((bt.transtype IN('AP','DM') AND bt.converted_credit = 0) OR (bt.transtype IN('CM') AND bt.converted_debit = 0))
                                        $add_query")
                                        ->setGroupBy('bt.accountcode, j.job_no')
                                        ->setOrderBy($sort)
                                        ->runPagination();
            // echo $this->db->getQuery();
            
                                    
            return $result;
        }

        public function retrieveprocessListing($sort,$jobno,$code)
        {
            //$sort 		 	 	= isset($data['sort2']) ? htmlentities($data['sort2']) : "";
            //$code               = $data['account_code'];
            //$jobno               = $data['pjobno'];
            $fields 			= array('Reference, Date, debit, Credit');

            $query = "SELECT
                            balance_table.voucherno,
                            balance_table.transactiondate,
                            balance_table.converted_debit,
                            balance_table.converted_credit,
                            balance_table.transtype
                        FROM
                            balance_table
                        LEFT JOIN
                            financial_jobs AS fj ON balance_table.voucherno = fj.voucherno
                        INNER JOIN
                            job ON fj.job_no = job.job_no
                        WHERE
                            balance_table.accountcode = '$code' AND balance_table.converted_debit != 0.00 AND fj.job_no = '$jobno'
                            
                        UNION
                        
                        SELECT
                            balance_table.voucherno,
                            balance_table.transactiondate,
                            balance_table.converted_debit,
                            balance_table.converted_credit,
                            balance_table.transtype
                        FROM
                            balance_table
                        LEFT JOIN
                            financial_jobs AS fj ON balance_table.voucherno = fj.voucherno
                        INNER JOIN
                            job ON fj.job_no = job.job_no
                        WHERE
                            balance_table.accountcode = '$code' AND balance_table.converted_credit != 0.00 AND fj.job_no = '$jobno'";

            $result = 	$this->db->setTable("($query) main")
                        ->setFields('main.voucherno AS referenceList,main.transactiondate,main.converted_debit,main.converted_credit,main.transtype')
                        ->setOrderBy($sort)
                        ->runSelect(false)
                        ->getResult();
                        // echo $this->db->getQuery();
            return $result;         
        }

        public function get_job_without_closed()
        {
            $result = $this->db->setTable('job')
            ->setFields("distinct job.job_no ind, job.job_no val")
            ->leftJoin('financial_jobs as fj ON fj.job_no = job.job_no')
            ->innerJoin('balance_table ON balance_table.voucherno = fj.voucherno AND balance_table.converted_debit != 0.00')
            ->setWhere("job.job_no != '' AND job.stat = 'on-going'")
            ->setOrderBy("val")
            ->runSelect()
            ->getResult();

            return $result;
        }

        public function retrieveclosedjobListing($data)
        {
            $sort 		 	 	= isset($data['sort3']) ? htmlentities($data['sort3']) : "";
            $job_no               = $data['close_job_number'];
            $fields 			= array('Reference, Date, debit, Credit');

            $query = "SELECT
                            bt.voucherno,
                            ca.accountname,
                            bt.converted_debit,
                            bt.converted_credit
                        FROM
                            balance_table bt 
                        INNER JOIN 
                            chartaccount ca on ca.id = bt.accountcode
                        left join 
                            financial_jobs fj on fj.voucherno = bt.voucherno
                        where
                            fj.job_no = '$job_no' AND bt.converted_debit != '' AND bt.transtype IN('AP','DM')
                            
                        UNION
                        
                        SELECT
                            bt.voucherno,
                            ca.accountname,
                            bt.converted_debit,
                            bt.converted_credit
                        FROM
                            balance_table bt 
                        INNER JOIN 
                            chartaccount ca on ca.id = bt.accountcode
                        left join 
                            financial_jobs fj on fj.voucherno = bt.voucherno
                        where
                            fj.job_no = '$job_no' AND bt.converted_credit != '' AND bt.transtype = 'CM'
            ";

            $result = 	$this->db->setTable("($query) main")
                        ->setFields('main.voucherno,main.accountname,main.converted_debit,main.converted_credit')
                        ->setOrderBy($sort)
                        ->runSelect(false)
                        ->getResult();
            // echo $this->db->getQuery();
            return $result;         
        }

        public function updateStat($data,$job_no)
		{
			$condition 			   = " job_no = '$job_no' ";

			$result 			   = $this->db->setTable('job')
												->setValues($data)
												->setWhere($condition)
												->setLimit(1)
												->runUpdate();
			return $result;
        }

        public function retrieveListingTotal($data)
        {
            

            $daterangefilter 	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
            $jobno      	= isset($data['job_number']) ? htmlentities($data['job_number']) : ""; 
            $searchkey 		 	= isset($data['account_search']) ? htmlentities($data['account_search']) : "";
            $sort 		 	 	= isset($data['sort']) ? htmlentities($data['sort']) : "j.job_no ASC";
            
            $datefilterArr		= explode(' - ',$daterangefilter);
            $datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
            $datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
            
            $add_query   = (!empty($searchkey)) ? "AND (ca.segment5 LIKE '%$searchkey%' OR ca.accountname LIKE '%$searchkey%' )" : "";
            $add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND j.entereddate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
            $add_query .= (!empty($jobno) && $jobno != 'none') ? "AND j.job_no = '$jobno' " : "";
            //var_dump($add_query);

            $fields         = array('
                                    fj.job_no,
                                    bt.accountcode,
                                    ca.accountname,
                                    ca.segment5,
                                    ca.id,
                                    (SUM(bt.converted_debit) - SUM(bt.converted_credit)) AS amount,
                                    j.stat
                                    ');

            $result         = $this->db->setTable('balance_table bt')
                                        ->setFields($fields)
                                        ->innerJoin('financial_jobs fj on fj.voucherno = bt.voucherno')
                                        ->leftJoin('chartaccount ca on ca.id = bt.accountcode')
                                        ->leftJoin('job j on j.job_no = fj.job_no')
                                        ->setWhere("bt.transtype IN('AP',
                                        'CM','DM') AND j.job_no != '' $add_query")
                                        ->setOrderBy($sort)
                                        ->setGroupBy('bt.accountcode, j.job_no')
                                        ->runSelect()
                                        ->getResult();
                                        // echo $this->db->getQuery();

            return $result;
        }

        public function getVoucherRatio($transtype,$voucherno,$job_no) {
            if ($transtype == '') {
                $transtype = substr($voucherno,0,2);
            }

            if ($transtype == 'AP') {
                $setTable = 'accountspayable';
            } else {
                $setTable = 'journalvoucher';
            }

            $q                  = $this->db->setTable($setTable)
                                        ->setFields('job_no')
                                        ->setWhere("voucherno = '$voucherno' AND job_no LIKE '%$job_no%'")
                                        ->runSelect()
                                        ->getRow();
                                        // echo $this->db->getQuery();

            // echo $q->job_no;
            $jobs           = explode(",",$q->job_no);
            $all_jobs_cost  = 0;
            for ($i=0; $i<count($jobs); $i++){
                $all_jobs_cost         += $this->getJobCost($jobs[$i]);
            }
            $job_cost = $this->getJobCost($job_no);
            
            $result = $job_cost/$all_jobs_cost;
            // echo  $result;
            return $result;
            
        }

        public function getJobCost($job_no) {
            $query              = $this->db->setTable('job')
                                        ->setFields('job_cost')
                                        ->setWhere("job_no = '$job_no'")
                                        ->runSelect()
                                        ->getRow();
                                        
            $result = $query->job_cost;
            // var_dump((float)$result);
            return $result;
        }
        
    }

?>