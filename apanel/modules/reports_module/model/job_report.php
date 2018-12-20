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
            

            $fields         = array('
                                    fj.job_no,
                                    bt.accountcode,
                                    ca.accountname,
                                    ca.segment5,
                                    ca.id,
                                    (SUM(bt.debit) - SUM(bt.credit)) AS amount,
                                    j.stat
                                    ');

            $result         = $this->db->setTable('balance_table bt')
                                        ->setFields($fields)
                                        ->innerJoin('financial_jobs fj on fj.voucherno = bt.voucherno')
                                        ->leftJoin('chartaccount ca on ca.id = bt.accountcode')
                                        ->leftJoin('job j on j.job_no = fj.job_no')
                                        ->setWhere("bt.transtype IN('AP',
                                        'CM','DM') AND j.job_no != '' $add_query")
                                        ->setGroupBy('bt.accountcode, j.job_no')
                                        ->setOrderBy($sort)
                                        ->runPagination();
            //echo $this->db->getQuery();

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
            $sort 		 	 	= isset($data['sort']) ? htmlentities($data['sort']) : "fj.job_no";
            
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
                                    (SUM(bt.debit) - SUM(bt.credit)) AS amount,
                                    j.stat
                                    ');

            $result         = $this->db->setTable('balance_table bt')
                                        ->setFields($fields)
                                        ->innerJoin('financial_jobs fj on fj.voucherno = bt.voucherno')
                                        ->leftJoin('chartaccount ca on ca.id = bt.accountcode')
                                        ->leftJoin('job j on j.job_no = fj.job_no')
                                        ->setWhere("bt.transtype IN('AP',
                                        'CM','DM') AND j.job_no != '' $add_query")
                                        ->setGroupBy('bt.accountcode, j.job_no')
                                        ->setOrderBy($sort)
                                        ->runPagination();
            //echo $this->db->getQuery();

            return $result;
        }

        public function retrieveprocessListing($data)
        {
            $sort 		 	 	= isset($data['sort']) ? htmlentities($data['sort']) : "";
            $code               = $data['account_code'];
            $fields 			= array('Reference, Date, debit, Credit');

            $query = "SELECT
                            balance_table.voucherno,
                            balance_table.transactiondate,
                            balance_table.debit,
                            balance_table.credit
                        FROM
                            balance_table
                        LEFT JOIN
                            financial_jobs AS fj ON balance_table.voucherno = fj.voucherno
                        INNER JOIN
                            job ON fj.job_no = job.job_no
                        WHERE
                            balance_table.accountcode = '$code' AND balance_table.debit != 0.00
                            
                        UNION
                        
                        SELECT
                            balance_table.voucherno,
                            balance_table.transactiondate,
                            balance_table.debit,
                            balance_table.credit
                        FROM
                            balance_table
                        LEFT JOIN
                            financial_jobs AS fj ON balance_table.voucherno = fj.voucherno
                        INNER JOIN
                            job ON fj.job_no = job.job_no
                        WHERE
                            balance_table.accountcode = '$code' AND balance_table.credit != 0.00";

            $result = 	$this->db->setTable("($query) main")
                        ->setFields('main.voucherno AS referenceList,main.transactiondate,main.debit,main.credit')
                        ->setOrderBy($sort)
                        ->runPagination(false);
            return $result;         
        }

        public function get_job_without_closed()
        {
            $result = $this->db->setTable('job')
            ->setFields("distinct job.job_no ind, job.job_no val")
            ->leftJoin('financial_jobs as fj ON fj.job_no = job.job_no')
            ->innerJoin('ap_details ON ap_details.voucherno = fj.voucherno AND ap_details.debit != 0.00')
            ->setWhere("job.job_no != '' AND job.stat != 'closed'")
            ->setOrderBy("val")
            ->runSelect()
            ->getResult();

            return $result;
        }

        public function retrieveclosedjobListing($data)
        {
            $sort 		 	 	= isset($data['sort']) ? htmlentities($data['sort']) : "";
            $job_no               = $data['close_job_number'];
            $fields 			= array('Reference, Date, debit, Credit');

            $query = "SELECT
                            bt.voucherno,
                            ca.accountname,
                            bt.debit,
                            bt.credit
                        FROM
                            balance_table bt 
                        INNER JOIN 
                            chartaccount ca on ca.id = bt.accountcode
                        left join 
                            financial_jobs fj on fj.voucherno = bt.voucherno
                        where
                            fj.job_no = '$job_no' AND bt.debit != '' AND bt.transtype != 'DM'
                            
                        UNION
                        
                        SELECT
                            bt.voucherno,
                            ca.accountname,
                            bt.debit,
                            bt.credit
                        FROM
                            balance_table bt 
                        INNER JOIN 
                            chartaccount ca on ca.id = bt.accountcode
                        left join 
                            financial_jobs fj on fj.voucherno = bt.voucherno
                        where
                            fj.job_no = '$job_no' AND bt.credit != '' AND bt.transtype = 'DM'
            ";

            $result = 	$this->db->setTable("($query) main")
                        ->setFields('main.voucherno,main.accountname,main.debit,main.credit')
                        ->setOrderBy($sort)
                        ->runPagination(false);
            //echo $this->db->getQuery();
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
                                    (SUM(bt.debit) - SUM(bt.credit)) AS amount,
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
            //echo $this->db->getQuery();

            return $result;
        }
        
    }

?>