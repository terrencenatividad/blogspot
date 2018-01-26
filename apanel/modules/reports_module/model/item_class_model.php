<?php
class item_class_model extends wc_model {

	public function saveItemClass($data) {
		return $this->db->setTable('itemclass')
			->setValues($data)
			->runInsert();
	}

	public function updateItemClass($data, $id) {
		return $this->db->setTable('itemclass')
				->setValues($data)
				->setWhere("id = '$id'")
				->setLimit(1)
				->runUpdate();
	}

	public function deleteItemClass($data) {
		$ids = "'" . implode("','", $data) . "'";
		return $this->db->setTable('itemclass')
				->setWhere("id IN ($ids)")
				->setLimit(count($data))
				->runDelete();
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
		$condition = '';
		if ($search) {
			$condition = $this->generateSearch($search, array('ic.label', 'p.label'));
		}
		$this->db->setTable('itemclass ic')
				->leftJoin('itemclass p ON ic.parentid = p.id AND ic.companycode = p.companycode')
				->setFields('ic.id id, ic.label label, ic.parentid parentid')
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
					'label' => $item->label
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

}