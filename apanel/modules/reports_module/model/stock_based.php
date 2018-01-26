<?php

    class stock_based extends wc_model
    {        
        public function retrieveSalesReport($itemcode, $warehouse, $customer, $startdate, $enddate) {
            $condition = "";

			if ($itemcode && $itemcode != 'none') 
			{
                $condition .= " AND isd.itemcode = '$itemcode' ";
            }

            if ($customer && $customer != 'none') 
			{
                $condition .= " AND inv.customer = '$customer' ";
            }
		
			if ($startdate && $enddate) 
			{
				$condition .= " AND ( inv.transactiondate >= '$startdate' AND inv.transactiondate <= '$enddate') ";
			}

			if ($warehouse && $warehouse != 'none') 
			{
                $condition .= " AND isd.warehouse = '$warehouse'";
            }

            $fields     =   array(
								'inv.transactiondate date',
								'inv.voucherno as invoice',
								'part.partnername as customer',
								'isd.issueqty sales_qty',
								'u.uomdesc sales_uom',
								'isd.convissueqty  base_qty', 
								'u2.uomdesc base_uom',
								'isd.unitprice unitprice',
								'isd.taxamount as tax',
								'isd.itemdiscount as discount',
								'isd.amount amount'
							);

            $result = $this->db->setTable("salesinvoice_details as isd")
								->setFields($fields)
                                ->leftJoin('salesinvoice as inv ON inv.voucherno = isd.voucherno')
                                ->leftJoin('items as itm ON itm.itemcode = isd.itemcode')
								->leftJoin("partners as part ON part.partnercode = inv.customer and partnertype = 'customer' ")
								->leftJoin("uom u ON u.uomcode = isd.issueuom AND u.companycode = isd.companycode")
								->leftJoin("uom u2 ON u2.uomcode = isd.convuom AND u2.companycode = isd.companycode")
								->setWhere(" isd.stat NOT IN ('temporary','cancelled') " .$condition)
								->setGroupBy("isd.voucherno, isd.linenum")
								->setOrderBy("inv.transactiondate, isd.linenum, isd.voucherno DESC")
                                ->runPagination();

            return $result;
        }

		public function fileExport($data) {
			$condition = '';
			$datefilter	= $data['daterangefilter'];
			$itemcode 	= $data['itemcode'];
			$customer 	= $data['customer'];
			$warehouse 	= $data['warehouse'];
			$datefilter = explode('-', $datefilter);
			foreach ($datefilter as $date) {
				$dates[] = date('Y-m-d', strtotime($date));
			}
			$startdate  = 	$dates[0] ;
			$enddate	= 	$dates[1] ;

			if ($itemcode && $itemcode != 'none') 
			{
                $condition .= " AND isd.itemcode = '$itemcode' ";
            }

            if ($customer && $customer != 'none') 
			{
                $condition .= " AND inv.customer = '$customer' ";
            }
		
			if ($startdate && $enddate) 
			{
				$condition .= " AND ( inv.transactiondate >= '$startdate' AND inv.transactiondate <= '$enddate') ";
			}

			if ($warehouse && $warehouse != 'none') 
			{
                $condition .= " AND isd.warehouse = '$warehouse'";
            }

			$fields     =   array(
									'inv.transactiondate date',
									'inv.voucherno as invoice',
									'part.partnername as customer',
									'isd.issueqty sales_qty',
									'u.uomdesc sales_uom',
									'isd.convissueqty  base_qty', 
									'u2.uomdesc base_uom',
									'isd.unitprice unitprice',
									'isd.taxamount as tax',
									'isd.itemdiscount as discount',
									'isd.amount amount'
								);

			$result = $this->db->setTable("salesinvoice_details as isd")
								->setFields($fields)
								->leftJoin('salesinvoice as inv ON inv.voucherno = isd.voucherno')
								->leftJoin('items as itm ON itm.itemcode = isd.itemcode')
								->leftJoin("partners as part ON part.partnercode = inv.customer and partnertype = 'customer' ")
								->leftJoin("uom u ON u.uomcode = isd.issueuom AND u.companycode = isd.companycode")
								->leftJoin("uom u2 ON u2.uomcode = isd.convuom AND u2.companycode = isd.companycode")
								->setWhere(" isd.stat NOT IN ('temporary','cancelled') " .$condition)
								->setGroupBy("isd.voucherno, isd.linenum")
								->setOrderBy("inv.transactiondate, isd.linenum, isd.voucherno DESC")
								->runSelect()
								->getResult();
			return $result;
		}

		public function getWarehouseList() {
			$result = $this->db->setTable('warehouse')
							->setFields("warehousecode ind, description val")
							->setWhere("stat = 'active'")
							->runSelect()
							->getResult();
			return $result;
		}

		public function getInvoiceData($type){
			if($type == 'items'){	
				$result = $this->db->setTable('salesinvoice_details inv')
						->setFields(" itm.itemcode ind, itm.itemname val")
						->leftJoin("items itm ON itm.itemcode = inv.itemcode ")
						->setWhere("inv.voucherno != '' AND (inv.stat = 'open' OR inv.stat = 'posted')")
						->setGroupBy("ind")
						->setOrderBy("val")
						->runSelect()
						->getResult();
			}else if($type == 'customers'){
				$result = $this->db->setTable('salesinvoice inv')
						->setFields(" part.partnercode ind, partnername val")
						->leftJoin("partners part ON part.partnercode = inv.customer ")
						->setWhere("inv.voucherno != '' AND (inv.stat = 'open' OR inv.stat = 'posted') AND part.partnertype = 'customer' ")
						->setGroupBy("ind")
						->setOrderBy("val")
						->runSelect()
						->getResult();
			}else if($type == 'warehouse'){
				$result = $this->db->setTable('salesinvoice_details inv')
						->setFields(" wh.warehousecode ind, wh.description val")
						->leftJoin("warehouse wh ON wh.warehousecode = inv.warehouse ")
						->setWhere("inv.voucherno != '' AND (inv.stat = 'open' OR inv.stat = 'posted')")
						->setGroupBy("ind")
						->setOrderBy("val")
						->runSelect()
						->getResult();
			}

			
			
			return $result;
		}
    }

?>