<?php

    class parts_and_service_model extends wc_model  {

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

            $resultArr     =   ($result)    ?   explode(" ",$result->transactiondate)   :   "";

            $returnvalue    =   "";
            if($resultArr){
                $returnvalue    =   $resultArr[0];
            } else {
                $returnvalue    =   "";
            }

            return $returnvalue;
        }
    }

