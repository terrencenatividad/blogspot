<?php

    class stock_ledger extends wc_model
    {        
        public function getValue($table, $cols = array(), $cond, $orderby = "")
        {
            $result = $this->db->setTable($table)
                        ->setFields($cols)
                        ->setWhere($cond)
                        ->setOrderBy($orderby)
                        ->runSelect()
                        ->getResult();

            return $result;
        }

		public function retrieveBeginningBalance($account,$startdate,$enddate){

			$fields 	=	 array('SUM(bal.debit) as tdebit, SUM(bal.credit) as tcredit');
			
			$result 	=	$this->db->setTable("balance_table bal")
							->leftJoin("chartaccount ca ON ca.id = bal.accountcode")
							->leftJoin("partners p ON p.partnercode = bal.partnercode")
							->setFields($fields)
							->setWhere(" bal.accountcode = '$account' AND ( bal.transactiondate < $startdate ) ")
							->setOrderBy("bal.accountcode, bal.transactiondate")
							->runSelect()
							->getResult();
			//echo $this->db->getQuery();
			return $result;
		}

		public function retrieveAccountTotal($account, $startdate , $enddate){

			$fields 	=	 array('SUM(bal.debit) as tdebit, SUM(bal.credit) as tcredit');
			
			$result 	=	$this->db->setTable("balance_table bal")
							->leftJoin("chartaccount ca ON ca.id = bal.accountcode")
							->leftJoin("partners p ON p.partnercode = bal.partnercode")
							->setFields($fields)
							->setWhere(" bal.accountcode = '$account' AND ( bal.transactiondate >= '$startdate' && bal.transactiondate <= '$enddate' ) ")
							->setOrderBy("bal.accountcode, bal.transactiondate")
							->runSelect()
							->getResult();
			//echo $this->db->getQuery();
			return $result;
		}

		public function retrieveAccounts(){

			$fields 	=	 array('id as ind, CONCAT(segment5," - ",accountname) as val');
			
			$result 	=	$this->db->setTable("chartaccount")
							->setFields($fields)
							->setOrderBy("id")
							->runSelect()
							->getResult();
			//echo $this->db->getQuery();
			return $result;
		}
		
		public function retrieveGLReport($acctcode, $startdate, $enddate, $search) {
			
			$condition 	=	"";

			// For Account
			if ( $acctcode && $acctcode != "none" ) 
			{
 				$accounts 	= implode( "','" ,$acctcode );
				$condition .= " AND bal.accountcode IN ( '$accounts' ) ";
            }
			// For Search
			if ( !empty($search) ) 
			{
				$condition .= " AND (ca.segment5 LIKE '%$search%' OR ca.accountname LIKE '%$search%' OR bal.voucherno LIKE '%$search%' OR ( p.first_name LIKE '%$search%' OR p.last_name LIKE '%$search%' ) ) ";
			}

			$fields 	=	 array('bal.accountcode as accountcode, ca.segment5 as segment5, ca.accountname, bal.transactiondate, bal.period, bal.fiscalyear, bal.voucherno, CONCAT(p.first_name," ",last_name) as partner , bal.transtype, SUM(bal.debit) as debit, SUM(bal.credit) as credit');
			
			$result 	=	$this->db->setTable("balance_table bal")
							->leftJoin("chartaccount ca ON ca.id = bal.accountcode")
							->leftJoin("partners p ON p.partnercode = bal.partnercode")
							->setFields($fields)
							->setWhere(" bal.transactiondate >= '$startdate' AND bal.transactiondate <= '$enddate' $condition ")
							->setGroupBy("bal.accountcode, bal.voucherno")
							->setOrderBy("bal.accountcode, bal.transactiondate")
							->runPagination();
			//echo $this->db->getQuery();
			return $result;
        }

		public function fileExport($acctcode, $startdate, $enddate, $search) {
			$condition 	=	"";

			// For Account
			if ( $acctcode && $acctcode != "none" ) 
			{
 				$accounts 	= implode( "','" ,$acctcode );
				$condition .= " AND bal.accountcode IN ( '$accounts' ) ";
            }
			// For Search
			if ( !empty($search) ) 
			{
				$condition .= " AND (ca.segment5 LIKE '%$search%' OR ca.accountname LIKE '%$search%' OR bal.voucherno LIKE '%$search%' OR ( p.first_name LIKE '%$search%' OR p.last_name LIKE '%$search%' ) ) ";
			}

			$fields 	=	 array('bal.accountcode, bal.transactiondate, bal.voucherno, CONCAT(p.first_name," ",last_name) as partner ,  ca.accountname, ca.segment5 as segment5,bal.period, bal.fiscalyear, bal.transtype, SUM(bal.debit) as debit, SUM(bal.credit) as credit');

			$result 	=	$this->db->setTable("balance_table bal")
								->leftJoin("chartaccount ca ON ca.id = bal.accountcode")
								->leftJoin("partners p ON p.partnercode = bal.partnercode")
								->setFields($fields)
								->setWhere(" bal.transactiondate >= '$startdate' && bal.transactiondate <= '$enddate' $condition ")
								->setGroupBy("bal.accountcode, bal.voucherno")
								->setOrderBy("bal.accountcode, bal.transactiondate")
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
    }

?>