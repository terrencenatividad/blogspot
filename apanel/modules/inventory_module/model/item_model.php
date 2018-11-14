<?php
class item_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function saveItem($data) {
		$result = $this->db->setTable('items')
							->setValues($data)
							->runInsert();
		
		if ($result) {
			$this->log->saveActivity("Create Item [{$data['itemcode']}]");
		}
		
		return $result;
	}

	public function updateItem($data, $itemcode) {
		$result = $this->db->setTable('items')
							->setValues($data)
							->setWhere("itemcode = '$itemcode'")
							->setLimit(1)
							->runUpdate();
		
		if ($result) {
			$this->log->saveActivity("Update Item [{$data['itemcode']}]");
		}

		return $result;
	}

	public function deleteItems($data) {
		$error_id = array();
		foreach ($data as $id) {
			$result =  $this->db->setTable('items')
								->setWhere("itemcode = '$id'")
								->setLimit(1)
								->runDelete();
		
			if ($result) {
				$this->log->saveActivity("Delete Item [$id]");
			} 
			else {
				if ($this->db->getError() == 'locked') {
					$error_id[] = $id;
				}
			}
		}

		return $error_id;
	}

	public function checkItemCode($itemcode, $reference) {
		$result = $this->db->setTable('items')
							->setFields('itemcode')
							->setWhere("itemcode = '$itemcode' AND itemcode != '$reference'")
							->setLimit(1)
							->runSelect()
							->getRow();

		if ($result) {
			return false;
		} else {
			return true;
		}
	}

	public function getItemById($fields, $itemcode) {
		return $this->db->setTable('items')
						->setFields($fields)
						->setWhere("itemcode = '$itemcode'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}

	public function getItemListPagination($fields, $search, $typeid, $classid, $sort) {
		$fields = array(
			'itemcode',
			'itemname',
			'itemdesc',
			'weight',
			'i.stat stat'
		);
		$result = $this->getItemListQuery($fields, $search, $typeid, $classid, $sort)
						->runPagination();
		return $result;
	}

	public function getItemList($fields, $search, $typeid, $classid, $sort) {
		$fields = array(
			'itemcode',
			'barcode',
			'itemname',
			'itemdesc',
			'it.label item_type',
			'itemgroup',
			'ic.label item_class',
			'ic2.label item_class_parent',
			'weight',
			'wt.uomdesc weight_type',
			'bundle',
			'item_ident_flag',
			'brandcode',
			'replacement',
			'replacementcode',
			'buom.uomdesc base_uom',
			'suom.uomdesc selling_uom',
			'selling_conv',
			'puom.uomdesc purchasing_uom',
			'purchasing_conv',
			'ra.accountname receivable_account',
			'rva.accountname revenue_account',
			'ea.accountname expense_account',
			'pa.accountname payable_account',
			'ia.accountname inventory_account',
			'rt.value revenuetype',
			'et.value expensetype'
		);
		$result = $this->getItemListQuery($fields, $search, $typeid, $classid, $sort)
						->leftJoin('itemclass ic2 ON ic2.id = ic.parentid AND ic2.companycode = ic.companycode')
						->leftJoin('uom wt ON wt.uomcode = i.weight_type AND wt.companycode = i.companycode')
						->leftJoin('uom buom ON buom.uomcode = i.uom_base AND buom.companycode = i.companycode')
						->leftJoin('uom suom ON suom.uomcode = i.uom_selling AND suom.companycode = i.companycode')
						->leftJoin('uom puom ON puom.uomcode = i.uom_purchasing AND puom.companycode = i.companycode')
						->leftJoin('chartaccount ra ON ra.id = i.receivable_account AND ra.companycode = i.companycode')
						->leftJoin('chartaccount rva ON rva.id = i.revenue_account AND rva.companycode = i.companycode')
						->leftJoin('chartaccount ea ON ea.id = i.expense_account AND ea.companycode = i.companycode')
						->leftJoin('chartaccount pa ON pa.id = i.payable_account AND pa.companycode = i.companycode')
						->leftJoin('chartaccount ia ON ia.id = i.inventory_account AND ia.companycode = i.companycode')
						->leftJoin('wc_option rt ON rt.code = i.revenuetype')
						->leftJoin('wc_option et ON et.code = i.expensetype')
						->runSelect()
						->getResult();
						
		return $result;
	}

	public function getItemTypeList($search = '') {
		$condition = "stat = 'active'";
		if ($search) {
			$condition = "label = '$search'";
		}
		return $this->db->setTable('itemtype')
						->setFields('id ind, label val')
						->setWhere($condition)
						->runSelect()
						->getResult();
	}

	public function getEditItemTypeList($search = '', $itemtype) {
		$condition = "stat = 'active' OR id = '$itemtype'";
		if ($search) {
			$condition = "label = '$search'";
		}
		return $this->db->setTable('itemtype')
						->setFields('id ind, label val, stat stat')
						->setWhere($condition)
						->runSelect()
						->getResult();
	}

	public function getItemClassList($search = '', $parent = '') {
		$condition = "ic.stat = 'active'";
		if ($search) {
			$condition = "ic.label = '$search'";
			if ($parent) {
				$condition .= " AND ic2.label = '$parent'";
			}
		}
		return $this->db->setTable('itemclass ic')
						->setFields('ic.id ind, ic.label val, ic.stat stat')
						->leftJoin('itemclass ic2 ON ic2.id = ic.parentid AND ic2.companycode = ic.companycode')
						->setWhere($condition)
						->runSelect()
						->getResult();
	}

	public function getWeightTypeList($search = '', $weight) {
		$condition = " AND stat = 'active' OR uomcode = '$weight'";
		if ($search) {
			$condition = " AND uomdesc = '$search'";
		}
		return $this->db->setTable('uom')
						->setFields('uomcode ind, uomdesc val')
						->setWhere("uomtype = 'weight'" . $condition)
						->runSelect()
						->getResult();
	}

	public function getItemDropdownList($search="") {
		$condition = " stat = 'active'";
		if ($search) {
			$condition .= " AND itemcode = '$search'";
		}
		return $this->db->setTable('items')
						->setFields('itemcode ind, itemname val')
						->setWhere($condition)
						->runSelect()
						->getResult();
	}

	public function getEditItemDropdownList($itemcode="", $replacement="") {
		$condition = "( stat = 'active' AND itemcode != '$itemcode') OR itemcode = '$replacement'";
		return $this->db->setTable('items')
						->setFields('itemcode ind, itemname val')
						->setWhere($condition)
						->runSelect()
						->getResult();
	}
	
	public function getBrandDropdownList($search="") {
		$condition = " stat = 'active'";
		if ($search) {
			$condition .= " AND brandcode = '$search'";
		}
		return $this->db->setTable('brands')
						->setFields('brandcode ind, brandname val')
						->setWhere($condition)
						->runSelect()
						->getResult();
	}

	public function getEditUOMList($search = '', $base, $selling, $purchasing) {
		$condition = "stat = 'active' OR uomcode = '$base' OR uomcode = '$selling' OR uomcode = '$purchasing'";
		if ($search) {
			$condition = "uomdesc = '$search'";
		}
		return $this->db->setTable('uom')
						->setFields('uomcode ind, uomdesc val, stat stat')
						->setWhere($condition)
						->runSelect()
						->getResult();
	}

	public function getUOMList($search = '') {
		$condition = "stat = 'active'";
		if ($search) {
			$condition = "uomdesc = '$search'";
		}
		return $this->db->setTable('uom')
						->setFields('uomcode ind, uomdesc val')
						->setWhere($condition)
						->runSelect()
						->getResult();
	}

	public function getGroupsList($search = '') {
		$condition = "type = 'item_group'";
		if ($search) {
			$condition = "code = '$search'";
		}
		return $this->db->setTable('wc_option')
						->setFields('code ind, value val')
						->setWhere($condition)
						->runSelect(false)
						->getResult();
	}

	public function getEditItemClassList($search = '',$classid) {
		$condition = "stat = 'active' OR id = '$classid'";
		if ($search) {
			$condition = "label = '$search'";
		}
		return $this->db->setTable('itemclass')
						->setFields('id ind, label val, stat stat')
						->setWhere($condition)
						->runSelect()
						->getResult();
	}

	public function getUOMCode($itemcode) {
		return $this->db->setTable('items as items')
						->setFields('items.uom_base as uom_base, items.uom_selling as selling, items.uom_purchasing as purchasing')
						->leftJoin('uom as uom ON uom.uomcode = items.uom_base')
						->setWhere("items.itemcode = '$itemcode'")
						->runSelect()
						->getRow();
	}

	public function getReceivableAccountList($search = '') {
		$condition = '';
		if ($search) {
			$condition = " AND accountname = '$search'";
		}
		return $this->db->setTable('chartaccount c')
						->innerJoin('accountclass ac ON c.companycode = ac.companycode AND c.accountclasscode = ac.accountclasscode')
						->setFields('id ind, accountname val, accountclass parent')
						->setWhere("c.accountclasscode = 'ACCREC' AND c.accountnature = 'Debit'" . $condition)
						->setOrderBy('accountclass, accountname')
						->runSelect()
						->getResult();
	}

	public function getRevenueAccountList($search = '') {
		$condition = '';
		if ($search) {
			$condition = " AND accountname = '$search'";
		}
		return $this->db->setTable('chartaccount c')
						->innerJoin('accountclass ac ON c.companycode = ac.companycode AND c.accountclasscode = ac.accountclasscode')
						->setFields('id ind, accountname val,  accountclass parent')
						->setWhere("c.accountclasscode IN('REV','REVENU') AND c.accountnature = 'Credit'" . $condition)
						->setOrderBy('accountclass, accountname')
						->runSelect()
						->getResult();
	}

	public function getExpenseAccountList($search = '') {
		$condition = '';
		if ($search) {
			$condition = " AND accountname = '$search'";
		}
		return $this->db->setTable('chartaccount c')
						->innerJoin('accountclass ac ON c.companycode = ac.companycode AND c.accountclasscode = ac.accountclasscode')
						->setFields('id ind, accountname val, accountclass parent')
						->setWhere("c.accountclasscode IN('EXP','OPSEXP','OTREXP','COST','COSTSA','OTHCA','CUASET','NCASET','OTHCL','CULIAB','NCLIAB','TAX','INCTAX','CASH','INV','NVNTRY','PREPAID','PPE')" . $condition)
						->setOrderBy('accountclass, accountname')
						->runSelect()
						->getResult();
	}

	public function getPayableAccountList($search = '') {
		$condition = '';
		if ($search) {
			$condition = " AND accountname = '$search'";
		}
		return $this->db->setTable('chartaccount c')
						->innerJoin('accountclass ac ON c.companycode = ac.companycode AND c.accountclasscode = ac.accountclasscode')
						->setFields('id ind, accountname val, accountclass parent')
						->setWhere("c.accountclasscode 
						IN('CASH','ACCPAY')" . $condition)
						->setOrderBy('accountclass, accountname')
						->runSelect()
						->getResult();
	}

	public function getRevenueTypeList($search = '') {
		$condition = '';
		if ($search) {
			$condition = " AND value = '$search'";
		}
		return $this->db->setTable('wc_option')
						->setFields('code ind, value val')
						->setWhere("type = 'item_vat_class' AND code IN ('vat_private', 'vat_gov', 'vat_zero', 'vat_exempt')" . $condition)
						->setOrderBy('value')
						->runSelect(false)
						->getResult();
	}

	public function getExpenseTypeList($search = '') {
		$condition = '';
		if ($search) {
			$condition = " AND value = '$search'";
		}
		return $this->db->setTable('wc_option')
						->setFields('code ind, value val')
						->setWhere("type = 'item_vat_class' AND code IN ('vat_not', 'vat_not_exceed', 'vat_exceed', 'vat_domestic_goods', 'vat_import_goods', 'vat_domestic_services', 'vat_non_resident')" . $condition)
						->setOrderBy('value')
						->runSelect(false)
						->getResult();
	}

	public function getChartAccountList($search = '') {
		$condition = '';
		if ($search) {
			$condition = "accountname = '$search'";
		}
		return $this->db->setTable('chartaccount c')
						->innerJoin('accountclass ac ON c.companycode = ac.companycode AND c.accountclasscode = ac.accountclasscode')
						->setFields('id ind, accountname val, accountclass parent')
						->setWhere($condition)
						->setOrderBy('accountclass, accountname')
						->runSelect()
						->getResult();
	}

	public function saveItemCSV($value) {
		$result = $this->db->setTable('items')
							->setValues($value)
							->runInsert();
		
		if ($result) {
			$this->log->saveActivity("Upload Item CSV");
		}

		return $result;
	}

	public function checkExistingItem($data) {
		$item_types = "'" . implode("', '", $data) . "'";

		$result = $this->db->setTable('items')
							->setFields('itemcode')
							->setWhere("itemcode IN ($item_types)")
							->runSelect()
							->getResult();
		
		return $result;
	}

	private function getItemListQuery($fields, $search, $typeid, $classid, $sort) {
		$fields		= array_merge($fields, array('ic.label itemclass', 'it.label itemtype'));
		$sort		= ($sort) ? $sort : 'itemcode';
		$condition	= '';
		if ($search) {
			$condition .= $this->generateSearch($search, array('itemcode', 'itemname' , 'ic.label' , 'it.label'));
		}
		if ($typeid && $typeid != 'null') {
			$condition .= (($condition) ? " AND " : " ") . "i.typeid = '$typeid'";
		}
		if ($classid && $classid != 'null') {
			$condition .= (($condition) ? " AND " : " ") . "i.classid = '$classid'";
		}
		$query = $this->db->setTable("items i")
							->innerJoin("itemclass ic ON ic.id = i.classid AND ic.companycode = i.companycode")
							->leftJoin("itemtype it ON it.id = i.typeid AND it.companycode = i.companycode")
							->setFields($fields)
							->setWhere($condition)
							->setOrderBy($sort);
		return $query;
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

	public function updateStat($data,$id)
	{
		$condition 			   = " itemcode = '$id' ";

		$result 			   = $this->db->setTable('items')
											->setValues($data)
											->setWhere($condition)
											->setLimit(1)
											->runUpdate();

		return $result;
	}

}