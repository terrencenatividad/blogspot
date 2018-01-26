<?php

    class cheque_list_model extends wc_model
    {        
        public function retrievePartnerList()
		{
			$result = $this->db->setTable('partners')
						->setFields("partnercode ind, CONCAT( first_name, ' ', last_name ) val")
						->setWhere("partnercode != '' AND ( partnertype = 'supplier' OR partnertype = 'customer' ) AND stat = 'active'")
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

        public function retrieveCustomerDetails($customer_code)
		{
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

        public function retrieveChequeList($search, $startdate, $enddate, $partner, $filter, $bank) {
		
			$condition = "(chq.voucherno != '' )  "; 
			
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
			}
			// For Partner
			if( $partner && $partner !='none' )
			{
				$partner_names = implode( "','", $partner );
				$condition .= " AND pt.partnercode IN ( '$partner_names' ) ";
			}
			// For Bank
			if ( $bank && $bank != "none" ) 
			{
 				$bank_names = implode( "','" ,$bank );
				$condition .= " AND coa.id IN ( '$bank_names' ) ";
			}

			// For Search
			if ( !empty($search) ) 
			{
				$condition .= " AND (chq.chequenumber LIKE '%$search%' OR inv.invoiceno LIKE '%$search%') ";
			}

            $fields     =   array('chq.releasedate, chq.chequenumber, ap.invoiceno, chq.voucherno, chq.chequedate, coa.accountname bank, pt.partnername as partner, chq.chequeamount, chq.stat, chq.cleardate, chq.transtype, chq.companycode');

            $result 	= $this->db->setTable("pv_cheques as chq")
							   ->innerJoin("pv_application as pva ON pva.voucherno = chq.voucherno AND pva.companycode = chq.companycode")							   
							   ->innerJoin("accountspayable as ap ON ap.voucherno = pva.apvoucherno AND ap.companycode = pva.companycode ")
                               ->innerJoin("chartaccount as coa ON coa.id = chq.chequeaccount AND coa.companycode = chq.companycode ")
							   ->innerJoin("partners as pt ON pt.partnercode = ap.vendor AND pt.partnertype = 'supplier' AND ap.companycode = pt.companycode ")
							   ->setFields($fields)
							   ->setWhere($condition)
							   ->runPagination();	

			// $query = $pvchq; //. ' UNION ALL ' . $rvchq

			// $fields2 	=	array("a.releasedate, a.chequenumber, a.invoiceno, a.voucherno, a.chequedate, a.bank, a.partner, a.cleardate, a.chequeamount, a.stat, a.transtype");

			// $result = $this->db->setTable("($query) a")
			//  					->setFields($fields2)
			//  					->runPagination();				
			
            return $result;				
        }

        private function generateSearch($search, $array) {
            $temp = array();
            foreach ($array as $arr) {
                $temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
            }
            return '(' . implode(' OR ', $temp) . ')';
        }

		public function fileExport($search, $startdate, $enddate, $partner, $filter, $bank)
		{

			$condition = "(chq.voucherno != '' )  "; 
			
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
			}
			// For Partner
			if( $partner && $partner !='none' )
			{
				$partner_names = implode( "','", $partner );
				$condition .= " AND pt.partnercode IN ( '$partner_names' ) ";
			}
			// For Bank
			if ( $bank && $bank != "none" ) 
			{
 				$bank_names = implode( "','" ,$bank );
				$condition .= " AND coa.id IN ( '$bank_names' ) ";
            }
			// For Search
			if ( !empty($search) ) 
			{
				$condition .= " AND (chq.chequenumber LIKE '%$search%' OR inv.invoiceno LIKE '%$search%') ";
			}

            $fields     =   array('chq.releasedate, chq.chequenumber, ap.invoiceno, chq.voucherno, chq.chequedate, coa.accountname bank, pt.partnername as partner, chq.chequeamount, chq.stat, chq.cleardate, chq.transtype, chq.companycode');
			
            $result 	= $this->db->setTable("pv_cheques as chq")
									->innerJoin("pv_application as pva ON pva.voucherno = chq.voucherno AND pva.companycode = chq.companycode")							   
									->innerJoin("accountspayable as ap ON ap.voucherno = pva.apvoucherno AND ap.companycode = pva.companycode ")
									->innerJoin("chartaccount as coa ON coa.id = chq.chequeaccount AND coa.companycode = chq.companycode ")
									->innerJoin("partners as pt ON pt.partnercode = ap.vendor AND pt.partnertype = 'supplier' AND ap.companycode = pt.companycode ")
									->setFields($fields)
									->setWhere($condition)
									->runSelect()
									->getResult();

			// $rvchq 		= $this->db->setTable("rv_cheques as chq")
			// 				   ->leftJoin("receiptvoucher as rv ON rv.voucherno = chq.voucherno AND rv.companycode = chq.companycode ")
			// 				   ->leftJoin("rv_application as app ON app.voucherno = rv.voucherno AND app.companycode = rv.companycode ")
			// 				   ->leftJoin("accountsreceivable as inv ON inv.voucherno = app.arvoucherno AND inv.companycode = app.companycode ")
			// 				   ->leftJoin("bankdetail as btl ON btl.accountlevel = chq.chequeaccount AND btl.companycode = chq.companycode ")
            //                    ->leftJoin("bank as bnk ON btl.bankcode = bnk.bankcode AND btl.companycode = bnk.companycode ")
			// 				   ->leftJoin("partners as pt ON pt.partnercode = rv.customer AND pt.partnertype = 'customer' AND rv.companycode = pt.companycode ")
			// 				   ->setFields($fields)
			// 				   ->setWhere($condition)
			// 				   ->buildSelect();

			// $query = $pvchq . ' UNION ALL ' . $rvchq;

			// $fields2 	=	array("a.releasedate, a.chequenumber, a.invoiceno, a.voucherno, a.chequedate, a.bank, a.partner, a.cleardate, a.chequeamount, a.stat, a.transtype");

			// $result = $this->db->setTable("($query) a")
			//  					->setFields($fields2)
			// 					->runSelect()
			// 					->getResult();

			//var_dump($result);
			return $result;
		}
		
		public function updateData($data, $table, $cond)
		{
			$result = $this->db->setTable($table)
					->setValues($data)
					->setWhere($cond)
					->runUpdate();
					
			return $result;
		}
	}

?>