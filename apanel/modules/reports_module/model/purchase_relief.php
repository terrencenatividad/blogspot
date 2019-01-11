<?php

    class purchase_relief extends wc_model {        
		public function getCompany($code){
			$fields = "companyname, address, tin, taxyear, periodstart";

			$result = $this->db->setTable('company')
								->setFields($fields)
								->setWhere("companycode = '$code'")
								->setWhere(1)
								->setLimit('1')
								->runSelect()
								->getRow();

			return $result;
		}

        public function getPurchaseReliefPagination($customer, $sort, $start, $end) {
            $result	= $this->getQueryDetails($customer, $sort, $start, $end)
                            ->runPagination();
            // echo $this->db->getQuery();
            return $result;
        }

        public function getPurchaseReliefDetails($customer, $sort, $start, $end) {
            $result	= $this->getQueryDetails($customer, $sort, $start, $end)
                            ->runSelect()
                            ->getResult();
            // echo $this->db->getQuery();
            return $result;
        }

        private function getQueryDetails($vendor, $sort, $start, $end) {
            $condition = '';
            if ($start && $end) {
                $condition .= " AND (rpt.transactiondate >= '$start' AND rpt.transactiondate <= '$end')";
            }
            if($vendor!="" && $vendor != "none"){
                $condition .= " AND rpt.vendor = '$vendor'";
            } 
            
            $service_query  =   $this->db->setTable('purchasereceipt_details dtl')
                                         ->setFields(array('dtl.itemcode, SUM(dtl.amount) amt, dtl.companycode, dtl.linenum'))
                                         ->leftJoin("items as itm ON itm.itemcode = dtl.itemcode AND itm.companycode = dtl.companycode AND itm.expenseType = 'vat_domestic_services'")
                                         ->leftJoin("itemclass as ic ON ic.id = itm.classid AND ic.companycode = itm.companycode AND ic.expenseType = 'vat_domestic_services'")
                                         ->setWhere("dtl.stat = 'posted' AND (ic.expenseType IS NOT NULL OR itm.expenseType IS NOT NULL)")
                                         ->setGroupBy('dtl.itemcode')
                                         ->buildSelect();

            $goods_query  =   $this->db->setTable('purchasereceipt_details dtl')
                                         ->setFields(array('dtl.itemcode, SUM(dtl.amount) amt, dtl.companycode, dtl.linenum'))
                                         ->leftJoin("items as itm ON itm.itemcode = dtl.itemcode AND itm.companycode = dtl.companycode AND itm.expenseType = 'vat_domestic_goods'")
                                         ->leftJoin("itemclass as ic ON ic.id = itm.classid AND ic.companycode = itm.companycode AND ic.expenseType = 'vat_domestic_goods'")
                                         ->setWhere("dtl.stat = 'posted' AND (ic.expenseType IS NOT NULL OR itm.expenseType IS NOT NULL)")
                                         ->setGroupBy('dtl.itemcode')
                                         ->buildSelect();

            $capital_query  =   $this->db->setTable('purchasereceipt_details dtl')
                                         ->setFields(array('dtl.itemcode, SUM(dtl.amount) amt, dtl.companycode, dtl.linenum'))
                                         ->leftJoin("items as itm ON itm.itemcode = dtl.itemcode AND itm.companycode = dtl.companycode AND (itm.expenseType = 'vat_exceed' OR itm.expenseType = 'vat_not_exceed')")
                                         ->leftJoin("itemclass as ic ON ic.id = itm.classid AND ic.companycode = itm.companycode AND (ic.expenseType = 'vat_exceed' OR ic.expenseType = 'vat_not_exceed')")
                                         ->setWhere("dtl.stat = 'posted' AND (ic.expenseType IS NOT NULL OR itm.expenseType IS NOT NULL)")
                                         ->setGroupBy('dtl.itemcode')
                                         ->buildSelect();

            $query = $this->db->setTable("purchasereceipt rpt")
                              ->setFields('rpt.transactiondate, rpt.voucherno, rpt.period, rpt.fiscalyear, p.partnername, p.tinno, rpt.netamount, 0 vat_exempt, 0 vat_zerorated, 0 vat_sales, 
                                            COALESCE(service.amt,0) service, COALESCE(goods.amt,0) goods, COALESCE(capital.amt, 0) capital, rpt.wtaxamount, rpt.amount')
                              ->leftJoin('partners p ON p.partnercode = rpt.vendor AND p.companycode = rpt.companycode AND p.partnertype = "supplier"')
                              ->leftJoin('purchasereceipt_details dtl ON dtl.voucherno = rpt.voucherno AND dtl.companycode = rpt.companycode')
                              ->leftJoin('('.$service_query.') service ON service.itemcode = dtl.itemcode AND service.companycode = dtl.companycode AND service.linenum = dtl.linenum')
                              ->leftJoin('('.$goods_query.') goods ON goods.itemcode = dtl.itemcode AND goods.companycode = dtl.companycode AND goods.linenum = dtl.linenum')
                              ->leftJoin('('.$capital_query.') capital ON capital.itemcode = dtl.itemcode AND capital.companycode = dtl.companycode AND capital.linenum = dtl.linenum')
                              ->setWhere("rpt.stat NOT IN ('cancelled','temporary')".$condition)
                              ->setGroupBy('rpt.voucherno')
                              ->setOrderBy($sort);
    
            return $query;
        }

        public function retrieveCustomerList(){
			$result = $this->db->setTable('partners')
						->setFields("partnercode ind, partnername val")
						->setWhere("partnercode != '' AND partnertype = 'customer' AND stat = 'active'")
						->setOrderBy("val")
						->runSelect()
						->getResult();
			return $result;
        }

        public function getAmountTotal($customer, $sort, $start, $end) {
			$fields 	= 	array('rpt.transactiondate, rpt.period, rpt.fiscalyear, p.partnername, p.tinno, SUM(rpt.netamount) netamount, 0 vat_exempt, 0 vat_zerorated, 0 vat_sales, SUM(service.amt) service, SUM(goods.amt) goods, SUM(capital.amt) capital, SUM(rpt.wtaxamount) wtaxamount, SUM(rpt.amount) amount');

			$having_cond 	=	"";
			$group_by 		=	"";

			$db	= $this->getQueryDetails($customer, $sort, $start, $end) 
						->setFields($fields);

			if (!empty($start) && !empty($end)) {
				$having_cond .= " ( rpt.transactiondate >= '$start' AND rpt.transactiondate <= '$end' ) ";
            } 
            
            $result = $db->setGroupBy($group_by)
                        ->setHaving($having_cond)
                        ->runSelect()
                        ->getRow();
                        // echo  $this->db->getQuery();
			return $result;
		}
    }

?>