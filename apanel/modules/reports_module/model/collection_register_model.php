<?php

    class collection_register_model extends wc_model
    {        
		public $condition 	=	"";

        public function retrievePartnerList(){
			$result = $this->db->setTable('partners')
						->setFields("partnercode ind, partnername val")
						->setWhere("partnercode != '' AND ( partnertype = 'customer' ) AND stat = 'active'")
						->setOrderBy("val")
						->runSelect()
						->getResult();
			
			return $result;
		}

		public function retrieveReceiptOptions(){
			$result = $this->db->setTable('wc_option as opt')
						->setFields("opt.code ind, opt.value val")
						->setWhere("opt.type = 'receipt_mode'")
						->setOrderBy("opt.code")
						->runSelect(false)
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

		public function getSalesTotal($search, $startdate, $enddate, $partner, $filter, $bank, $sort, $mode, $type) {
			$fields 	= 	array('COUNT(*) count, 
								   rv.transactiondate transactiondate,
								   pt.partnername partnername,
								   SUM(rv.amount) totalamount,
								   IF(rv.paymenttype = "cash","CASH",
									IF(rv.paymenttype = "cheque", CONCAT(coa.accountname," : ",chq.chequenumber),"BANK TRANSFER")) payment,
								   IFNULL(chq.chequedate, rv.transactiondate) paymentdate,
								   rv.paymenttype type');

			$having_cond 	=	"";
			$group_by 		=	"";

			$db	= $this->getQueryDetails($search, $startdate, $enddate, $partner, $filter, $bank, $sort, $mode)
						->setFields($fields);

			// if( $type == 'dated' ){
			// 	echo $startdate;
			// 	echo $enddate;
			// 	echo $type;
			// }

			if ( (!empty($startdate) && !empty($enddate)) && $type != 'pdc' ) {
				$having_cond .= " transactiondate >= '$startdate' AND transactiondate <= '$enddate' ";
			} 
			if ( (!empty($startdate) && !empty($enddate)) && $type == 'dated' ) {
				// echo "here";
				$this->condition .= " AND (IFNULL(chq.chequedate, rv.transactiondate) <= rv.transactiondate)";
			} 
			if ( (!empty($startdate) && !empty($enddate)) && $type == 'pdc' ) {
				// $having_cond .= " paymentdate > '$enddate' ";
				$this->condition .= " AND (IFNULL(chq.chequedate, rv.transactiondate) > rv.transactiondate)";
			}
				
			if( $type && $type == 'cash' ){
				$having_cond .=	" AND payment = 'cash' ";
				$group_by	 .= "rv.paymenttype";
			} else if($type && $type == 'dated'){
				$this->condition .= " AND rv.paymenttype != 'cash'";
			} else if($type && $type == 'pdc'){
				// $having_cond .=	" AND payment != 'cash' ";
				// $group_by	 .= "rv.voucherno, rv.paymenttype";
				$this->condition .= " AND rv.paymenttype != 'cash'";
			} else {
				$group_by 	.=	"";
			}
			if( $partner && !in_array('none', $partner) ){
				$partner_names = implode( "','", $partner );
				$having_cond .= " AND partnername IN ( '$partner_names' ) ";
			}

			if( $type == 'dated' || $type == 'pdc' ){
				$db->setWhere($this->condition);
			} else {
				$db->setGroupBy($group_by);
				$db->setHaving($having_cond);
			}

			$result = $db->runSelect()
							->getRow();
			if( $type == 'dated' ){
				// echo $this->db->getQuery();
			}
			return $result;
		}
	
		public function retrieveChequeList($search, $startdate, $enddate, $partner, $filter, $bank, $sort, $mode) {
			$result	= $this->getQueryDetails($search, $startdate, $enddate, $partner, $filter, $bank, $sort, $mode)
							->runPagination();
			// echo $this->db->getQuery();
			return $result;
		}

        public function getQueryDetails($search, $startdate, $enddate, $partner, $filter, $bank, $sort, $mode) {
		
			$condition = "(rv.voucherno != '' )  AND  rv.stat != 'cancelled'  "; 
			
			// For Date
			if ( $startdate && $enddate ) {
				$condition .= " AND ( rv.transactiondate >= '$startdate' AND rv.transactiondate <= '$enddate') ";
			}
			// For Partner
			if( !empty($partner) ){
				if( !in_array('none', $partner) ){
					$partner_names = implode( "','", $partner );
					$condition .= " AND pt.partnercode IN ( '$partner_names' ) ";
				}
			}
			// For Mode
			if( !empty($mode) ){
				if( !in_array('none', $mode) ){
					$types = implode( "','", $mode );
					$condition .= " AND rv.paymenttype IN ( '$types' ) ";
				}
			}
			// For Search
			if ( !empty($search) ) {
				$condition .= " AND (chq.chequenumber LIKE '%$search%' OR ar.invoiceno LIKE '%$search%' OR rv.voucherno LIKE '%$search%' OR coa.accountname LIKE '%$search%' OR pt.partnername LIKE '%$search%') ";
			}
			$sort 		=	($sort 	!=	"") 	? 	$sort 	:	"rv.transactiondate DESC, rv.voucherno DESC";

			$this->condition 	=	$condition;

			$fields 	= 	array('rv.transactiondate,
									rv.voucherno,
									pt.partnername,
									coa.accountname bank,
									IF(rv.paymenttype = "cash", "CASH",
									IF(rv.paymenttype = "cheque", CONCAT(coa.accountname," : ",chq.chequenumber),"BANK TRANSFER")) payment,
									IFNULL(chq.chequedate, rv.transactiondate) paymentdate,
									rv.amount');

			$query     =   $this->db->setTable("receiptvoucher as rv")
									->leftJoin("rv_cheques as chq ON rv.voucherno = chq.voucherno AND rv.companycode = chq.companycode  AND rv.paymenttype = 'cheque'")
                                    ->leftJoin("rv_application as app ON app.voucherno = rv.voucherno AND app.companycode = rv.companycode ")
									->leftJoin("accountsreceivable as ar ON ar.voucherno = app.arvoucherno AND ar.companycode = app.companycode ")
									->leftJoin("chartaccount as coa ON coa.id = chq.chequeaccount AND coa.companycode = chq.companycode ")
                                    ->leftJoin("partners as pt ON pt.partnercode = ar.customer AND pt.partnertype = 'customer' AND ar.companycode = pt.companycode ")
                                    ->setFields($fields)
									->setWhere($condition)
									->setOrderBy($sort);
									// ->runPagination();	
									
            return $query;				
        }

        private function generateSearch($search, $array) {
            $temp = array();
            foreach ($array as $arr) {
                $temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
            }
            return '(' . implode(' OR ', $temp) . ')';
        }

		public function fileExport($search, $startdate, $enddate, $partner, $filter, $bank, $sort, $mode){
			$result	= $this->getQueryDetails($search, $startdate, $enddate, $partner, $filter, $bank, $sort, $mode)
							->runSelect()
							->getResult();
				// echo $this->db->getQuery();
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