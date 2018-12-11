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
            
            $add_query   = (!empty($searchkey)) ? "AND (cc.segment5 LIKE '%$searchkey%' OR cc.accountname LIKE '%$searchkey%' )" : "";
            $add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND j.entereddate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
            $add_query .= (!empty($jobno) && $jobno != 'none') ? "AND j.job_no = '$jobno' " : "";
            //var_dump($add_query);
            

            $fields 			= array('j.job_no,cc.id,cc.accountname,
            ( (SUM(ad.debit) + IFNULL(SUM(jodDm.debit),0)) - IFNULL(SUM(jodCm.credit),0) ) amount,j.stat,cc.segment5');

            $result = $this->db->setTable('job j')
            ->setFields($fields)    
            ->innerJoin('ap_details ad ON ad.job_no = j.job_no AND ad.debit != 0.00')
            ->innerJoin('chartaccount cc ON cc.id = ad.accountcode')
            ->leftJoin("journaldetails jodDm ON jodDm.job_no = j.job_no AND jodDm.transtype = 'DM' AND jodDm.debit != 0.00 AND jodDm.accountcode = cc.id")
            ->leftJoin("journaldetails jodCm ON jodCm.job_no = j.job_no AND jodCm.transtype = 'CM' AND jodCm.credit != 0.00 AND jodDm.accountcode = cc.id")
            ->setWhere(" j.stat != 'deleted' $add_query")
            ->setGroupBy('ad.accountcode,j.job_no')
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
            $sort 		 	 	= isset($data['sort']) ? htmlentities($data['sort']) : "j.job_no";
            
            $datefilterArr		= explode(' - ',$daterangefilter);
            $datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
            $datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
            
            $add_query   = (!empty($searchkey)) ? "AND (cc.segment5 LIKE '%$searchkey%' OR cc.accountname LIKE '%$searchkey%' )" : "";
            $add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND j.entereddate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
            $add_query .= (!empty($jobno) && $jobno != 'none') ? "AND j.job_no = '$jobno' " : "";
            //var_dump($add_query);
            

            $fields 			= array('j.job_no,cc.id,cc.accountname,
            ( (SUM(ad.debit) + IFNULL(SUM(jodDm.debit),0)) - IFNULL(SUM(jodCm.credit),0) ) amount,j.stat,cc.segment5');

            $result = $this->db->setTable('job j')
            ->setFields($fields)    
            ->innerJoin('ap_details ad ON ad.job_no = j.job_no AND ad.debit != 0.00')
            ->innerJoin('chartaccount cc ON cc.id = ad.accountcode')
            ->leftJoin("journaldetails jodDm ON jodDm.job_no = j.job_no AND jodDm.transtype = 'DM' AND jodDm.debit != 0.00 AND jodDm.accountcode = cc.id")
            ->leftJoin("journaldetails jodCm ON jodCm.job_no = j.job_no AND jodCm.transtype = 'CM' AND jodCm.credit != 0.00 AND jodDm.accountcode = cc.id")
            ->setWhere(" j.stat != 'deleted' $add_query")
            ->setGroupBy('ad.accountcode,j.job_no')
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
                            ap_details.voucherno,
                            ap_details.entereddate,
                            ap_details.debit,
                            ap_details.credit
                        FROM
                            ap_details
                        INNER JOIN
                            job ON ap_details.job_no = job.job_no
                        WHERE
                            ap_details.accountcode = '$code' AND ap_details.debit != 0.00
                        UNION
                        SELECT
                            journaldetails.voucherno,
                            journaldetails.entereddate,
                            journaldetails.debit,
                            journaldetails.credit
                        FROM
                            journaldetails
                        INNER JOIN
                            job ON journaldetails.job_no = job.job_no
                        INNER JOIN
                            ap_details ON ap_details.accountcode = journaldetails.accountcode
                        WHERE
                            journaldetails.accountcode = '$code' AND journaldetails.transtype = 'DM' AND journaldetails.debit != 0.00 AND journaldetails.job_no = job.job_no
                        UNION
                        SELECT
                            journaldetails.voucherno,
                            journaldetails.entereddate,
                            journaldetails.debit,
                            journaldetails.credit
                        FROM
                            journaldetails
                        INNER JOIN
                            job ON journaldetails.job_no = job.job_no
                        INNER JOIN
                            ap_details ON ap_details.accountcode = journaldetails.accountcode
                        WHERE
                            journaldetails.accountcode = '$code' AND journaldetails.transtype = 'CM' AND journaldetails.credit != 0.00 AND journaldetails.job_no = job.job_no
                            ";

            $result = 	$this->db->setTable("($query) main")
                        ->setFields('main.voucherno AS referenceList,main.entereddate,main.debit,main.credit')
                        ->setOrderBy($sort)
                        ->runPagination(false);
            return $result;         
        }

        public function get_job_without_closed()
        {
            $result = $this->db->setTable('job')
            ->setFields("distinct job.job_no ind, job.job_no val")
            ->innerJoin('ap_details ON ap_details.job_no = job.job_no AND ap_details.debit != 0.00')
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
                        ad.voucherno,
                        ca.accountname,
                        ad.debit,
                        ad.credit
                    FROM
                        ap_details ad
                    INNER JOIN chartaccount ca ON ca.id = ad.accountcode
                    WHERE
                        job_no = '$job_no' AND debit != 0.00 AND job_no != ''
                    UNION
                    SELECT
                        jd.voucherno,
                        ca.accountname,
                        jd.debit,
                        jd.credit
                    FROM
                        journaldetails jd
                    INNER JOIN chartaccount ca ON ca.id = jd.accountcode
                    WHERE
                        jd.job_no = '$job_no' AND jd.transtype = 'DM' AND jd.debit != 0.00  AND job_no != ''
                    UNION
                    SELECT
                        jd.voucherno,
                        ca.accountname,
                        jd.debit,
                        jd.credit
                    FROM
                        journaldetails jd
                    INNER JOIN chartaccount ca ON ca.id = jd.accountcode
                    WHERE
                        jd.job_no = '$job_no' AND jd.transtype = 'CM' AND jd.credit != 0.00  AND job_no != ''
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
        
    }

?>