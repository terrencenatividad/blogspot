<?php

    class purchase_restriction_model extends wc_model  {
        // public function __construct() {
        //     parent::__construct();
        //     $this->db = new log();
        // }

        public function setButtonRestriction($transactiondate) {
            $closed_date     =   $this->getClosedDate();

            if( $closed_date >= $transactiondate ){
                return 0;
            } else {
                return 1;
            }
        }

        public function getClosedDate() {
            $result     =   $this->db->setTable("journalvoucher")
                                     ->setFields("transactiondate")
                                     ->setWhere("stat = 'posted' AND source = 'closing'")
                                     ->setOrderBy("transactiondate DESC")
                                     ->setLimit(1)
                                     ->runSelect()
                                     ->getRow();
            // var_dump($result);
            $resultArr     =   ($result) ?  explode(" ",$result->transactiondate)   :   false;

            $returnvalue    =   "";
            if( $resultArr ){
                // $result->transactiondate    =   $resultArr[0];
                $returnvalue    =   $resultArr[0];
            } else {
                $returnvalue    =   "";
            }

            return $returnvalue;
        }
    }

