<?php
class item_class_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function saveItemClass($data) {
		$result =  $this->db->setTable('itemclass')
							->setValues($data)
							->runInsert();
		
		if ($result) {
			$insert_id = $this->db->getInsertId();
			$this->log->saveActivity("Create Item Class [$insert_id]");
		}

		return $result;
	}

	public function updateItemClass($data, $id) {
		$result =  $this->db->setTable('itemclass')
							->setValues($data)
							->setWhere("id = '$id'")
							->setLimit(1)
							->runUpdate();
		
		if ($result) {
			$this->log->saveActivity("Update Item Class [$id]");
		}

		return $result;
	}

	public function deleteItemClass($data) {
		$error_id = array();
		foreach ($data as $id) {
			$result =  $this->db->setTable('itemclass')
								->setWhere("id = '$id'")
								->setLimit(1)
								->runDelete();
		
			if ($result) {
				$this->log->saveActivity("Delete Item Class [$id]");
			} else {
				if ($this->db->getError() == 'locked') {
					$error_id[] = $id;
				}
			}
		}

		return $error_id;
	}

	public function getItemClassById($fields, $id) {
		return $this->db->setTable('itemclass')
						->setFields($fields)
						->setWhere("id = '$id'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}

	public function getItemClassList($fields, $search = '') {
		$fields = array(
			'ic.id id',
			'ic.label label',
			'ic.parentid parentid',
			'ra.accountname receivable_account',
			'rva.accountname revenue_account',
			'ea.accountname expense_account',
			'pa.accountname payable_account',
			'ia.accountname inventory_account',
			'rt.value revenuetype',
			'et.value expensetype',
			'ic.stat stat'
		);
		
		$condition = "";
		if ($search) {
			$condition = $this->generateSearch($search, array('ic.label', 'p.label', 'p2.label', 'c.label', 'c2.label'));
		}
		$this->db->setTable('itemclass ic')
				->setFields($fields)
				->leftJoin('itemclass p ON ic.parentid = p.id AND ic.companycode = p.companycode')
				->leftJoin('itemclass p2 ON p.parentid = p2.id AND p.companycode = p2.companycode')
				->leftJoin('itemclass c ON c.parentid = ic.id AND c.companycode = ic.companycode')
				->leftJoin('itemclass c2 ON c2.parentid = c.id AND c2.companycode = c.companycode')
				->leftJoin('chartaccount ra ON ra.id = ic.receivable_account AND ra.companycode = ic.companycode')
				->leftJoin('chartaccount rva ON rva.id = ic.revenue_account AND rva.companycode = ic.companycode')
				->leftJoin('chartaccount ea ON ea.id = ic.expense_account AND ea.companycode = ic.companycode')
				->leftJoin('chartaccount pa ON pa.id = ic.payable_account AND pa.companycode = ic.companycode')
				->leftJoin('chartaccount ia ON ia.id = ic.inventory_account AND ia.companycode = ic.companycode')
				->leftJoin('wc_option rt ON rt.code = ic.revenuetype')
				->leftJoin('wc_option et ON et.code = ic.expensetype')
				->setWhere($condition);

		$result = $this->db->runSelect()
							->getResult();

		return $this->buildTree($result);
	}

	public function getParentClass($id, $wnone = false) {
		$result = $this->getItemClassList(array('id', 'label', 'parentid'));
		$result = $this->getList($result, $id);
		$none = array();
		if ($wnone) {
			$none = array(
				(object) array(
					'ind' => '0',
					'val' => '- None -'
				)
			);
		}
		return array_merge($none, $result);
	}

	public function saveItemClassCSV($data) {
		foreach ($data as $row_data) {
			$row_data['parentid'] = $this->getItemClassParent($row_data['parentid']);
			$result = $this->db->setTable('itemclass')
								->setValues($row_data)
								->runInsert();
		}
			
		if ($result) {
			$this->log->saveActivity("Upload Item Class CSV");
		}

		return $result;
	}

	public function checkItemClass($label, $parentid, $id) {
		$result = $this->db->setTable('itemclass ic')
							->leftJoin('itemclass p ON ic.parentid = p.id AND ic.companycode = p.companycode')
							->setFields('ic.label label, p.label parent')
							->setWhere("ic.label = '$label' AND ic.parentid = '$parentid' AND ic.id != '$id'")
							->setLimit(1)
							->runSelect()
							->getRow();

		return $result;
	}

	public function checkExistingItemClass($data) {
		$item_class = "'" . implode("', '", $data) . "'";

		$result = $this->db->setTable('itemclass ic')
							->leftJoin('itemclass p ON ic.parentid = p.id AND ic.companycode = p.companycode')
							->setFields('ic.label, p.label parent')
							->setWhere("CONCAT(COALESCE(p.label, ''), ' - ', ic.label) IN ($item_class)")
							->runSelect()
							->getResult();
		
		return $result;
	}

	private function getItemClassParent($item_class) {
		if ($item_class == '') {
			return '';
		}
		$result = $this->db->setTable('itemclass')
							->setFields('id')
							->setWhere("label = '$item_class'")
							->setLimit(1)
							->runSelect()
							->getRow();
		
		if ($result) {
			return $result->id;
		} else {
			$values = array(
				'label'		=> $item_class,
				'parentid'	=> ''
			);
			$result = $this->db->setTable('itemclass')
								->setValues($values)
								->runInsert();
			
			if ($result) {
				return $this->db->getInsertId();
			} else {
				return '';
			}
		}
	}

	private function getList($array, $id) {
		$list = array();
		foreach ($array as $key => $value) {
			if ($key != $id) {
				$list[] = (object) array('ind' => $key, 'val' => $value['label']);
				if (isset($value['children'])) {
					$list = array_merge($list, $this->getList($value['children'], $id));
				}
			}
		}
		return $list;
	}

	private function buildTree($list, $pid = 0) {
		$op = array();
		foreach($list as $item) {
			if ($item->parentid == $pid) {
				$op[$item->id] = array(
					'label'					=> $item->label,
					'receivable_account'	=> $item->receivable_account,
					'revenue_account'		=> $item->revenue_account,
					'expense_account'		=> $item->expense_account,
					'payable_account'		=> $item->payable_account,
					'inventory_account'		=> $item->inventory_account,
					'revenuetype'			=> $item->revenuetype,
					'expensetype'			=> $item->expensetype,
					'stat'					=> $item->stat,
				);
				$children = $this->buildTree($list, $item->id);
				if ($children) {
					$op[$item->id]['children'] = $children;
				}
			}
		}
		return $op;
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
		$condition 			   = " id = '$id' ";

		$result 			   = $this->db->setTable('itemclass')
											->setValues($data)
											->setWhere($condition)
											->setLimit(1)
											->runUpdate();

		return $result;
	}


}