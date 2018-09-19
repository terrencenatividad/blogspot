<?php

    class cheque_list_model extends wc_model
    {        
        public function retrievePartnerList()
		{
			$result = $this->db->setTable('partners')
						->setFields("partnercode ind, partnername val")
						->setWhere("partnercode != '' AND partnertype = 'supplier' AND stat = 'active'")
						->setOrderBy("val")
						->runSelect()
						->getResult();
			
			return $result;
		}

		public function retrieveBankList()
		{
			$result = $this->db->setTable('chartaccount as chart')
						->setFields("chart.id ind, chart.accountname val, class.accountclass")
						->leftJoin('accountclass as class USING(accountclasscode)')
						->setWhere("(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'")
						->setOrderBy("class.accountclass")
						->runSelect()
						->getResult();
			
			return $result;
		}

        public function retrieveCustomerDetails($customer_code){
			$fields = "address1, tinno, terms, email, CONCAT( first_name, ' ', last_name ) AS name";
			$cond 	= "partnercode = '$customer_code' AND partnertype = 'customer' ";

			$result = $this->db->setTable('partners')
								->setFields($fields)
								->setWhere($cond)
								->setLimit('1')
								->runSelect()
								->getRow();

			return $result;
		}

		public function getQueryDetails($search, $startdate, $enddate, $partner, $filter, $bank, $sort) {
			$condition = "(chq.voucherno != '' ) "; 
			
			// For Check Date
			if ( $startdate && $enddate ) {
				$condition .= " AND ( chq.chequedate >= '$startdate' AND chq.chequedate <= '$enddate') ";
			}
			// For Check Status
			if (  !empty($filter) && $filter == 'uncleared' ) {
				$condition .= " AND chq.stat = 'uncleared' ";
			} else if (  !empty($filter) && $filter == 'released' ) {
				$condition .= " AND chq.stat = 'released' ";
			} else if (  !empty($filter) && $filter == 'cleared' ) {
				$condition .= " AND chq.stat = 'cleared' ";
			}else if (  !empty($filter) && $filter == 'cancelled' ) {
				$condition .= " AND chq.stat = 'cancelled' ";
			}else if (  !empty($filter) && $filter == 'void' ) {
				$condition .= " AND chq.stat = 'void' ";
			}		
			// For Partner
			if( !empty($partner) ){
				if( !in_array('none', $partner) ){
					$partner_names = implode( "','", $partner );
					$condition .= " AND pt.partnercode IN ( '$partner_names' ) ";
				}
			}
			// For Bank
			if( !empty($bank) ){
				if( !in_array('none', $bank) ){
					$bank_names = implode( "','", $bank );
					$condition .= " AND coa.id IN ( '$bank_names' ) ";
				}
			}
			// For Search
			if ( !empty($search) ) {
				$condition .= " AND (chq.chequenumber LIKE '%$search%' OR ap.invoiceno LIKE '%$search%' OR pt.partnername LIKE '%$search%') ";
			}
			$sort 		=	($sort 	!=	"") 	? 	$sort 	:	"chq.chequedate ASC";

            $fields     =   array('chq.releasedate, chq.chequenumber, ap.invoiceno, chq.voucherno, chq.chequedate, coa.accountname bank, pt.partnername as partner, chq.chequeamount, chq.stat, chq.cleardate, chq.transtype, chq.companycode');

            $query 	= $this->db->setTable("pv_cheques as chq")
									->leftJoin("pv_application as pva ON pva.voucherno = chq.voucherno AND pva.companycode = chq.companycode")							   
									->leftJoin("accountspayable as ap ON ap.voucherno = pva.apvoucherno AND ap.companycode = pva.companycode ")
									->leftJoin("chartaccount as coa ON coa.id = chq.chequeaccount AND coa.companycode = chq.companycode ")
									->leftJoin("partners as pt ON pt.partnercode = ap.vendor AND pt.partnertype = 'supplier' AND ap.companycode = pt.companycode ")
									->setFields($fields)
									->setWhere($condition)
									->setOrderBy($sort);	
									
            return $query;				
		}
		
        public function retrieveChequeList($search, $startdate, $enddate, $partner, $filter, $bank, $sort) {
			$result	= $this->getQueryDetails($search, $startdate, $enddate, $partner, $filter, $bank, $sort)
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

		public function fileExport($search, $startdate, $enddate, $partner, $filter, $bank, $sort){
			$result	= $this->getQueryDetails($search, $startdate, $enddate, $partner, $filter, $bank, $sort)
							->runSelect()
							->getResult();

			return $result;
		}
		
		public function getSalesTotal($search, $startdate, $enddate, $partner, $filter, $bank, $sort) {
			$fields 	= 	array('SUM(chq.chequeamount) totalamount');

			$result	= $this->getQueryDetails($search, $startdate, $enddate, $partner, $filter, $bank, $sort)
						->setFields($fields)
						->runSelect()
						->getRow();
							
			return $result;
		}
		
		public function updateData($data, $table, $cond) {
			$result = $this->db->setTable($table)
					->setValues($data)
					->setWhere($cond)
					->runUpdate();
					
			return $result;
		}
	}	

?>