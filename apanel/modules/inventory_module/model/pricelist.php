<?php
class pricelist extends wc_model
{
    public function retrievepricelistListing($search, $sort)
    {
        $add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND (pl.itemPriceCode LIKE '%$search%' OR pl.itemPriceName LIKE '%$search%' OR pl.itemPriceDesc LIKE '%$search%' OR pld.itemDtlCode LIKE '%$search%' OR i.itemdesc LIKE '%$search%' OR sect.partnername LIKE '%$search%' ) " 	: 	"";

        $fields 	=	array("pl.itemPriceCode, pl.itemPriceName, pl.itemPriceDesc, pl.stat");

        $result     =   $this->db->setTable('price_list pl')
                        ->leftJoin('price_list_details pld ON pl.itemPriceCode = pld.itemPriceCode AND pl.companycode = pld.companycode')
                        ->leftJoin('customer_price_list cpl ON cpl.itemPriceCode = pld.itemPriceCode AND cpl.companycode = pld.companycode')
                        ->leftJoin('partners sect ON sect.partnercode = cpl.customerCode AND sect.companycode = cpl.companycode')
                        ->leftJOin('items i ON i.itemcode = pld.itemDtlCode AND i.companycode = pld.companycode')
                        ->setFields($fields)
                        ->setWhere("pl.stat != 'deleted' ".$add_cond)
                        ->setOrderBy($sort)
                        ->setGroupBy('pl.itemPriceCode')
                        ->runPagination();
        //echo $this->db->getQuery();

        return $result;
    }

    public function export_pricelist($search, $sort)
	{
		$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND (pl.itemPriceCode LIKE '%$search%' OR pl.itemPriceName LIKE '%$search%' OR pld.itemDtlCode LIKE '%$search%' OR i.itemdesc LIKE '%$search%' OR sect.partnername LIKE '%$search%' ) " 	: 	"";

        $fields 	=	array("pl.itemPriceCode, pl.itemPriceName, pl.itemPriceDesc, pld.itemDtlCode, pld.sellPrice");

        $result     =   $this->db->setTable('price_list pl')
                        ->leftJoin('price_list_details pld ON pl.itemPriceCode = pld.itemPriceCode AND pl.companycode = pld.companycode')
                        ->leftJoin('customer_price_list cpl ON cpl.itemPriceCode = pld.itemPriceCode AND cpl.companycode = pld.companycode')
                        ->leftJoin('partners sect ON sect.partnercode = cpl.customerCode AND sect.companycode = cpl.companycode')
                        ->leftJOin('items i ON i.itemcode = pld.itemDtlCode AND i.companycode = pld.companycode')
                        ->setFields($fields)
                        ->setWhere(" pl.stat = 'active' $add_cond ")
                        ->setOrderBy($sort)
                        ->setGroupBy('pl.itemPriceCode, pld.itemDtlCode')
                        ->runSelect()
                        ->getResult();
                        // echo $this->db->getQuery();
        return $result;
	}

    public function retrieveitemPriceListing($code, $search, $sort)
    {
        $add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND (itemPriceCode LIKE '%$search%' OR itemPriceName LIKE '%$search%') " 	: 	"";

        $fields 	=	array("pld.itemPriceCode code, pld.itemDtlCode itemcode, i.itemdesc description, ip.itemprice original, pld.sellPrice adjusted_price");

        return $this->db->setTable('price_list_details pld')
                        ->leftJoin('items i ON i.itemcode = pld.itemDtlCode AND i.companycode = pld.companycode')
                        ->leftJoin('items_price ip ON ip.itemcode = i.itemcode AND ip.companycode = i.companycode')
                        ->setFields($fields)
                        ->setWhere(" pld.stat = 'active' AND pld.itemPriceCode = '$code' $add_cond ")
                        ->setOrderBy($sort)
                        ->runPagination();
    }

    public function retrieve_masterlist($search, $sort)
    {
        $add_cond 	=	( !empty($search) || $search != "" )  	? 	"  (i.itemcode LIKE '%$search%' OR i.itemname LIKE '%$search%') " 	: 	"";
        
        $fields     	=	array("i.itemcode code, i.itemname name, ip.itemprice, u.uomcode");
        
        $result         =    $this->db->setTable('items i')
                                    ->leftJoin('items_price ip ON ip.itemcode = i.itemcode AND ip.companycode = i.companycode')
                                    ->leftJoin('uom u ON u.uomcode = i.uom_selling AND i.companycode = u.companycode')
                                    ->setFields($fields) 
                                    ->setWhere($add_cond)
                                    ->setOrderBy($sort)
                                    ->runPagination();

        return $result;
    }           

    public function retrieve_purchase_masterlist($search, $sort)
    {
        $add_cond 	=	( !empty($search) || $search != "" )  	? 	"  (i.itemcode LIKE '%$search%' OR i.itemname LIKE '%$search%') " 	: 	"";
        
        $sub_query      =   " SELECT pa.companycode, pa.itemcode, pa.price_average purchase FROM price_average pa LEFT JOIN price_average pa2 ON (pa.itemcode = pa2.itemcode AND pa.linenum < pa2.linenum) WHERE pa2.itemcode IS NULL ";

        $fields     	=	array("i.itemcode code, i.itemname name, pa.purchase, u.uomcode");
        
        $result         =    $this->db->setTable('items i')
                        ->leftJoin('items_price ip ON ip.itemcode = i.itemcode AND ip.companycode = i.companycode')
                        ->leftJoin('uom u ON u.uomcode = i.uom_purchasing AND i.companycode = u.companycode')
                        ->leftJoin( '('.$sub_query .') pa ON pa.companycode = i.companycode AND pa.itemcode = i.itemcode')
                        ->setFields($fields) 
                        ->setWhere($add_cond)
                        ->setOrderBy($sort)
                        ->runPagination();

        return $result;
    }           

    public function export_masterlist($search, $sort)
	{
		$add_cond 	=	( !empty($search) || $search != "" )  	? 	"  (i.itemcode LIKE '%$search%' OR i.itemname LIKE '%$search%') " 	: 	"";
        
        $sub_query      =   " SELECT companycode, itemcode, MAX(price_average) purchase FROM price_average GROUP BY itemcode ORDER BY price_average DESC ";

        $fields     	=	array("i.itemcode code, i.itemname name, ip.itemprice, u.uomcode");
        
        $result         =    $this->db->setTable('items i')
                                        ->leftJoin('items_price ip ON ip.itemcode = i.itemcode AND ip.companycode = i.companycode')
                                        ->leftJoin('uom u ON u.uomcode = i.uom_selling AND i.companycode = u.companycode')
                                        ->setFields($fields) 
                                        ->setWhere($add_cond)
                                        ->runSelect()
                                        ->getResult();
        return $result;
	}

    public function getImportList()
    {
        $fields     	=	array("i.itemcode code, i.itemname name");
        
        $result         =    $this->db->setTable('items i')
                                      ->setFields($fields) 
                                      ->runSelect()
                                      ->getResult();
        return $result;
    }

    public function getValue($table, $cols = array(), $cond, $orderby = "", $bool = "")
    {
        $result = $this->db->setTable($table)
                    ->setFields($cols)
                    ->setWhere($cond)
                    ->setOrderBy($orderby)
                    ->runSelect($bool)
                    ->getResult();

        return $result;
    }
    
    public function processTransaction($data, $task, $voucher = "")
    {
        $mainInvTable		= "price_list";
        $detailInvTable		= "price_list_details";
    
        $insertResult		= 0;
        $errmsg				= array();

        $pl_code			= (isset($data['pricelistcode']) && (!empty($data['pricelistcode']))) ? htmlentities(addslashes(trim($data['pricelistcode']))) : "";
        $old_plcode         = (isset($data['h_price_code']) && (!empty($data['h_price_code']))) ? htmlentities(addslashes(trim($data['h_price_code']))) : "";
        $pl_name	    	= (isset($data['pricelistname']) && (!empty($data['pricelistname']))) ? htmlentities(addslashes(trim($data['pricelistname']))) : "";
        
        $pl_desc			= (isset($data['pricelistdesc']) && (!empty($data['pricelistdesc']))) ? htmlentities(addslashes(trim($data['pricelistdesc']))) : "";
        
        $_final 			= (isset($data['save']) && (!empty($data['save']))) ? htmlentities(addslashes(trim($data['save']))) : "";	
        
        $status				= ( $task == 'create' && empty($_final) ) ? "temp" : "active";
        
        $post_header['itemPriceCode'] 		=	$pl_code;
        $post_header['itemPriceName']   	=	$pl_name;
        $post_header['itemPriceDesc'] 	   	= 	$pl_desc;
        $post_header['stat'] 			    = 	$status;

        if($status=='temp' && $task == 'create')
        {
            $this->db->setTable($mainInvTable)
                ->setWhere("itemPriceCode = '$pl_code'");
            $insertResult = $this->db->runDelete();

            if($insertResult)
            {
                $this->db->setTable($mainInvTable)
                    ->setValues($post_header);
        
                $insertResult = $this->db->runInsert();
            }
        }
        else if( $task == 'create' )
        {
            $this->db->setTable($mainInvTable)
                ->setValues($post_header)
                ->setWhere("itemPriceCode = '$pl_code'");
            
            $insertResult = $this->db->runUpdate();
        }
        else if( $task == 'edit' )
        {
            $cond 	=	"itemPriceCode = '$old_plcode'";

            $insertResult = $this->db->setTable($mainInvTable)
                            ->setValues($post_header)
                            ->setWhere($cond)
                            ->runUpdate();
        }
        // var_dump($data);
        foreach($data as $postIndex => $postValue)
        {
            if($postIndex == 'itemcode' || $postIndex == 'adjusted_price' ) 
            {
                $a		= '';
                
                foreach($postValue as $postValueIndex => $postValueIndexValue)
                {
                    if($postIndex == 'adjusted_price')
                    {
                        $a = str_replace(',', '', $postValueIndexValue);
                    }
                    else
                    {
                        $a = htmlentities(addslashes($postValueIndexValue));
                    }
                    
                    $arrayData[$postIndex][$postValueIndex] = $a;
                }	
            }
        }

        foreach($arrayData as $arrayDataIndex => $arrayDataValue)
        {
            foreach($arrayDataValue as $postValueIndex => $postValueIndexValue)
            {											
                $tempArray[$postValueIndex][$arrayDataIndex] = $postValueIndexValue;
            }
        }

        $tempArr 			= array();
        foreach($tempArray as $tempArrayIndex => $tempArrayValue)
        {
            $itemcode 			=	$tempArrayValue['itemcode'];
            $selling_price 	    = 	$tempArrayValue['adjusted_price'];

            if( $tempArrayValue['itemcode'] != "" )
            {
                $data_insert['itemPriceCode']	  	= $pl_code;
                $data_insert['itemDtlCode']         = $itemcode;
                $data_insert['sellPrice']	        = $selling_price;
                $data_insert['stat'] 			    = $status;
           
                $tempArr[] 						    = $data_insert;
            }
        }	

        $isDetailExist	= $this->getValue($detailInvTable, array("COUNT(*) as count"),"itemPriceCode = '$pl_code'");

        if($isDetailExist[0]->count == 0 && $task == 'create' )
        {
            $this->db->setTable($detailInvTable)
                        ->setValues($tempArr);

            $insertResult = $this->db->runInsert();
        }
        else if( $isDetailExist[0]->count > 0 && ( $task == 'create' || $task == 'edit' ) )
        {
            $this->db->setTable($detailInvTable)
                ->setWhere("itemPriceCode = '$pl_code'");
            $insertResult    =    $this->db->runDelete();

            if( $insertResult ){
                $this->db->setTable($detailInvTable)
                ->setValues($tempArr);
                $insertResult   =   $this->db->runInsert();
                //echo $this->db->getQuery();
            }
        }
        
        if(!$insertResult)
        {
            $errmsg[] 		= "The system has encountered an error in saving. Our team is currently checking on this.<br/>";
        }

        return $errmsg;
    }

    public function updateData($data, $table, $cond)
    {
        $result = $this->db->setTable($table)
                ->setValues($data)
                ->setWhere($cond)
                ->runUpdate();

        return $result;
    }

    public function insertPrice($data)
	{
		$result = $this->db->setTable('items_price')
				->setValues($data)
				->runInsert();

		return $result;
	}

    public function retrieveItemDetails($itemcode)
    {
        $fields = "i.itemname as itemname, i.itemdesc as itemdesc, i.uom_base, p.itemprice as price,
                    i.receivable_account item_receivable, i.revenue_account item_revenue, i.expense_account item_expense, i.payable_account item_payable, 
                    i.inventory_account item_inventory, class.receivable_account class_receivable, class.revenue_account class_revenue, class.expense_account class_expense, 
                    class.payable_account class_payable, class.inventory_account class_inventory, u.uomcode uomcode";
        $cond 	= "i.itemcode = '$itemcode'";

        $result = $this->db->setTable('items i')
                            ->leftJoin('items_price p ON p.itemcode = i.itemcode AND p.companycode = i.companycode')
                            ->leftJoin('uom u ON u.uomcode = i.uom_selling AND u.companycode = i.companycode')
                            ->leftJoin('itemclass class ON class.id = i.classid AND class.companycode = i.companycode')
                            ->setFields($fields)
                            ->setWhere($cond)
                            ->setLimit('1')
                            ->runSelect()
                            ->getRow();

        return $result;
    }
    
    public function deleteData($table, $cond)
    {	
        $this->db->setTable($table)
                ->setWhere($cond);

        $result = $this->db->runDelete();

        return $result;
    }

    public function retrieveExisting($code)
    {	 
        $retrieved_data =	array();
        
        $header_fields 	= 	"pl.itemPriceCode tpl_code, pl.itemPriceName tpl_name, pl.itemPriceDesc tpl_desc";

        $condition 		=	" pl.itemPriceCode = '$code' ";
        
        $retrieved_data['header'] 	= 	$this->db->setTable('price_list pl')
                                                ->setFields($header_fields)
                                                ->setWhere($condition)
                                                ->setLimit('1')
                                                ->runSelect()
                                                ->getRow();

        $detail_fields 			= "pld.itemDtlCode itemcode, i.itemdesc description, ip.itemprice original, pld.sellPrice adjusted_price, u.uomcode uomcode";
        $condition 		        = " pld.itemPriceCode = '$code' ";
        
        $retrieved_data['details'] = 	$this->db->setTable('price_list_details pld')
                                        ->leftJoin(' items i ON i.itemcode = pld.itemDtlCode AND i.companycode = pld.companycode ')
                                        ->leftJoin(' uom u ON u.uomcode = i.uom_selling AND u.companycode = i.companycode ')
                                        ->leftJoin(' items_price ip ON i.itemcode = ip.itemcode AND i.companycode = ip.companycode ')
                                        ->setFields($detail_fields)
                                        ->setWhere($condition)
                                        ->runSelect()
                                        ->getResult();
    
        return $retrieved_data;
    }

    public function delete_temp_transactions($voucherno, $table, $detailTable)
    {
        $result = $this->db->setTable($table)
                ->setWhere(" itemPriceCode = '$voucherno' AND stat = 'temp' ")
                ->runDelete();
            
        if( $result )
        {
            $result = $this->db->setTable($detailTable)
                ->setWhere(" itemPriceCode = '$voucherno' AND stat = 'temp' ")
                ->runDelete();
        }

        return $result;
    }

    public function retrieveCustomerListing($code, $search)
    {
        $add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND (p.partnercode LIKE '%$search%' OR p.partnername LIKE '%$search%') " 	: 	"";
        
        $result =  $this->db->setTable('partners p')
                            ->setFields(array('p.partnercode as partnercode',"p.partnername as partnername", 'cd.customerCode as tagged'))
                            ->leftJoin("customer_price_list cd ON cd.customerCode = p.partnercode AND cd.companycode = p.companycode AND cd.stat != 'deleted' ")
                            ->setWhere(" p.partnertype = 'customer' AND p.stat = 'active' AND (cd.itemPriceCode = '$code' OR cd.itemPriceCode IS NULL) $add_cond")
                            ->setOrderBy('cd.customerCode DESC, p.partnercode ASC')
                            ->runPagination();
                            
        return $result;
    }
    
    public function retrieveTagged($code)
    {
        $result =  $this->db->setTable('partners p')
                            ->setFields(array('cd.customerCode as tagged'))
                            ->leftJoin("customer_price_list cd ON cd.customerCode = p.partnercode AND cd.companycode = p.companycode AND cd.stat != 'deleted' ")
                            ->setWhere(" p.partnertype = 'customer' AND p.stat = 'active' AND (cd.itemPriceCode = '$code' OR cd.itemPriceCode IS NULL)")
                            // ->setOrderBy('cd.itemPriceCode DESC')
                            ->runSelect()
                            ->getResult();
                            
        return $result;
    }

    public function tagCustomer($code, $tagged)
    {
        $result 	=	0;

        $this->db->setTable('customer_price_list')
            ->setWhere("itemPriceCode = '$code'")
            ->runDelete();
        
        $tagged_array 	=	explode(',',$tagged);

        if(!empty($tagged_array))
        {
            foreach( $tagged_array as $key => $val )
            {
                $insert_tag['itemPriceCode'] 	= 	$code;
                $insert_tag['customerCode'] 	=	$val;
                $insert_tag['stat'] 	        =	'active';

                $result = $this->db->setTable('customer_price_list')
                    ->setValues($insert_tag)
                    ->runInsert();
            }
        }
        return $result;

    }

    public function check_if_exists($column, $table, $condition)
	{
		return $this->db->setTable($table)
						->setFields("COUNT(".$column.") count")
						->setWhere($condition)
						->runSelect()
						->getResult();
	}

    public function check_duplicate($current, $table, $column)
    {
        return $this->db->setTable($table)
                        ->setFields('COUNT('.$column.') count')
                        ->setWhere($column." = '$current' AND stat = 'active'")
                        ->runSelect()
                        ->getResult();
    }

    public function check_duplicate_pair($table, $cond)
    {
        $result = $this->db->setTable($table)
                        ->setFields("COUNT(*) count")
                        ->setWhere($cond)
                        ->runSelect()
                        ->getResult();

        return $result;
        
    }

    public function import($data,$table)
    {
        $result = $this->db->setTable($table)
                ->setValuesFromPost($data)
                ->runInsert();
                
        return $result;
    }
    

    public function importPair($data)
    {
        $result = $this->db->setTable('price_list_details')
                ->setValuesFromPost($data)
                ->runInsert();

        return $result;
    }

    public function updateStat($data,$id)
	{
		$condition 			   = " itemPriceCode = '$id' ";

		$result 			   = $this->db->setTable('price_list')
											->setValues($data)
											->setWhere($condition)
											->setLimit(1)
											->runUpdate();

		return $result;
	}
}