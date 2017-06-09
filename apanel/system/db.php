<?php
class db {
	
	private $table			= '';
	private $join			= '';
	private $where			= '';
	private $groupby		= '';
	private $having			= '';
	private $orderby		= '';
	private $limit			= '';
	private $limit_offset	= '';

	private $fields			= array();
	private $values			= array();
	private $result			= array();
	private $query			= '';
	private $num_rows		= 0;
	private $error			= '';
	private $insert_select	= '';
	private $insert_id		= '';
	private $errorno		= '';
	private $error_allowed	= array();

	// Pagination
	private $page			= 0;
	private $page_limit		= 0;
	private $result_limit	= 0;
	private $result_count	= 0;

	// ----------------------Database----------------------- //

	public function __construct() {
		$session = new session();
		$this->conn				= new mysqli(WC_HOSTNAME, WC_USERNAME, WC_PASSWORD, WC_DATABASE);
		$this->companycode		= COMPANYCODE;
		$this->datetime			= date('Y-m-d H:i:s');
		$this->username			= USERNAME;
		$this->updateprogram	= '';
		$this->error_allowed	= array('1062', '1451');
		if (defined('MODULE_NAME') && defined('MODULE_TASK')) {
			$this->updateprogram	= MODULE_NAME . '|' . MODULE_TASK;
		}
		$this->initPagination();
	}

	public function changeDatabase($database) {
		$this->conn = new mysqli(WC_HOSTNAME, WC_USERNAME, WC_PASSWORD, $database);
	}

	// ---------------------Properties---------------------- //

	public function setTable($table) {
		$this->cleanProperties();
		$this->table = $table;
		return $this;
	}

	public function innerJoin($join) {
		$this->join .= ($join) ? ' INNER JOIN ' . $join : '';
		return $this;
	}

	public function leftJoin($join) {
		$this->join .= ($join) ? ' LEFT JOIN ' . $join : '';
		return $this;
	}

	public function setWhere($where) {
		$this->where = ($where) ? " WHERE $where" : '';
		return $this;
	}

	public function setGroupBy($groupby) {
		$this->groupby = ($groupby) ? " GROUP BY $groupby" : '';
		return $this;
	}

	public function setHaving($having) {
		$this->having = ($having) ? " HAVING $having" : '';
		return $this;
	}

	public function setOrderBy($orderby) {
		$this->orderby = ($orderby) ? " ORDER BY $orderby" : '';
		return $this;
	}

	public function setLimit($limit) {
		$this->limit = ($limit !== '') ? " LIMIT $limit" : '';
		return $this;
	}

	public function setLimitOffset($limit_offset) {
		$this->limit_offset = ($this->limit) ? ", $limit_offset" : '';
		return $this;
	}

	public function setInsertSelect($select) {
		$this->insert_select = $select;
		return $this;
	}

	// -----------------Fields and Values------------------- //

	public function setFields($fields) {
		$this->fields = (is_array($fields)) ? $fields : explode(',', $fields);
		$this->values = array();
		return $this;
	}

	public function setValues(array $values) {
		if ( ! empty($values)) {
			$temp = isset($values[0]) ? $values[0] : $values;
			$this->fields = array();
			foreach($temp as $key => $value) {
				$this->fields[] = $key;
			}
			$this->values = (isset($values[0])) ? $values : array($values);
		}
		return $this;
	}

	public function setValuesFromPost(array $values) {
		$max			= 0;
		$fields_static	= array();
		$fields_array	= array();
		$static			= array();
		$array			= array();
		$this->values	= array();
		foreach ($values as $key => $value) {
			if (is_array($value)) {
				$fields_array[]	= $key;
				$max				= count($value);
				$array[]			= $key;
			} else {
				$fields_static[]	= $key;
				$static[]			= $key;
			}
		}
		$this->fields = array_merge($fields_static, $fields_array);
		for ($x = 0; $x < $max; $x++) {
			$temp = array();
			foreach ($static as $key) {
				$temp[$key] = $values[$key];
			}
			foreach ($array as $key) {
				$temp[$key] = $values[$key][$x];
			}
			$this->values[] = $temp;
		}
		return $this;
	}

	// --------------------Query Builder--------------------- //

	public function buildSelect($addon = true) {
		$this->result	= array();
		$this->query	= '';
		$this->num_rows = 0;
		$check = $this->runCheck(array('fields', 'table'));
		$where = $this->where;
		if ($addon) {
			$main_table = $this->getMainTable();
			$where .= ((empty($this->where)) ? " WHERE " : " AND ") . " {$main_table}companycode = '{$this->companycode}' ";
		}
		if ($check) {
			$fields = implode(', ', $this->fields);
			$this->query = "SELECT $fields FROM {$this->table}{$this->join}{$where}{$this->groupby}{$this->having}{$this->orderby}{$this->limit}{$this->limit_offset}";
			if ($this->conn->error) {
				$this->showError($this->conn->error);
			}
		}
		return $this->query;
	}

	public function buildInsert($addon = true) {
		$this->insert_id		= '';
		$this->query			= '';
		$check_insert_select	= $this->runCheck(array('insert_select'), false);
		$check_values			= $this->runCheck(array('values'), !$check_insert_select);
		$check 					= $this->runCheck(array('fields', 'table'));
		$temp_fields 			= $this->fields;
		$where 					= $this->where;
		if ($addon) {
			$temp_fields[] = 'enteredby';
			$temp_fields[] = 'entereddate';
			$temp_fields[] = 'companycode';
			$temp_fields[] = 'updateprogram';
		}
		if ($check && ($check_values || $check_insert_select)) {
			$fields = implode(', ', $temp_fields);
			$query = "INSERT INTO {$this->table} ($fields)";
			if ($check_values) {
				$query .= " VALUES";
				foreach ($this->values as $key => $values) {
					if ($addon) {
						$values['enteredby']		= $this->username;
						$values['entereddate']		= $this->datetime;
						$values['companycode']		= $this->companycode;
						$values['updateprogram']	= $this->updateprogram;
					}
					$query .= "('" . implode("', '", $values) . "'), ";
				}
				$this->query = (substr($query, -2) == ', ') ? substr($query, 0, -2) : $query;
			} else if ($check_insert_select) {
				$this->query = "$query {$this->insert_select}";
			}
		}
		return $this->query;
	}

	public function buildUpdate($addon = true) {
		$this->query = '';
		$check = $this->runCheck(array('fields', 'table', 'values', 'where'));
		$where = $this->where;
		if ($addon) {
			$main_table = $this->getMainTable();
			$where .= ((empty($this->where)) ? " WHERE " : " AND ") . " {$main_table}companycode = '{$this->companycode}' ";
		}
		if ($check) {
			$temp = array();
			$values = $this->values[0];
			if ($addon) {
				$values['updateby']		= $this->username;
				$values['updatedate']	= $this->datetime;
				$values['updateprogram']	= $this->updateprogram;
			}
			$query = "UPDATE {$this->table} SET ";
			foreach ($values as $key => $value) {
				$query .= "$key = '$value', ";
			}
			$query = (substr($query, -2) == ', ') ? substr($query, 0, -2) : $query;
			$query .= "{$where}{$this->limit}";
			$this->query = $query;
		}
		return $this->query;
	}

	public function buildDelete($addon = true) {
		$this->query = '';
		$check = $this->runCheck(array('table', 'where'));
		$where = $this->where;
		if ($addon) {
			$main_table = $this->getMainTable();
			$where .= ((empty($this->where)) ? " WHERE " : " AND ") . " {$main_table}companycode = '{$this->companycode}' ";
		}
		if ($check) {
			$this->query = "DELETE FROM {$this->table}{$where}{$this->limit}";
		}
		return $this->query;
	}

	// --------------------Execute Query--------------------- //

	public function runSelect($addon = true) {
		$this->buildSelect($addon);
		$this->cleanProperties();
		$result = $this->conn->query($this->query);
		if ($result) {
			$this->num_rows = $result->num_rows;
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_object()) {
					$this->result[] = $row;
				}
			}
		}
		if ($this->conn->error) {
			$this->showError($this->conn->error);
		}
		return $this;
	}

	public function getRow() {
		return (empty($this->result)) ? false : $this->result[0];
	}

	public function getResult() {
		return $this->result;
	}

	public function runInsert($addon = true) {
		$this->buildInsert($addon);
		$this->cleanProperties();
		if ($this->query) {
			$this->result = $this->conn->query($this->query);
			$this->errorno = $this->conn->errno;
			if ($this->conn->error && ! in_array($this->conn->errno, $this->error_allowed)) {
				$this->showError($this->conn->error);
			}
			$this->insert_id = $this->conn->insert_id;
		}
		return $this->result;
	}

	public function runUpdate($addon = true) {
		$this->buildUpdate($addon);
		$this->cleanProperties();
		if ($this->query) {
			$this->result = $this->conn->query($this->query);
			$this->errorno = $this->conn->errno;
			if ($this->conn->error && ! in_array($this->conn->errno, $this->error_allowed)) {
				$this->showError($this->conn->error);
			}
		}
		return $this->result;
	}

	public function runDelete($addon = true) {
		$this->buildDelete($addon);
		$this->cleanProperties();
		if ($this->query) {
			$this->result = $this->conn->query($this->query);
			$this->errorno = $this->conn->errno;
			if ($this->conn->error && ! in_array($this->conn->errno, $this->error_allowed)) {
				$this->showError($this->conn->error);
			}
		}
		return $this->result;
	}

	// --------------------Properties------------------------ //

	public function getProperties($properties = array('table', 'fields', 'values', 'where', 'groupby', 'having', 'join')) {
		$temp = array();
		if (is_array($properties)) {
			foreach ($properties as $value) {
				if (isset($this->{$value})) {
					$temp[$value] = $this->{$value};
				}
			}
		} else if (isset($this->{$properties})) {
			$temp = $this->{$properties};
		}
		return $temp;
	}

	public function getQuery() {
		return $this->query;
	}

	public function getNumRows() {
		return $this->num_rows();
	}

	public function getInsertId() {
		return $this->insert_id;
	}

	public function getError() {
		switch ($this->errorno) {
			case '1062':
				$error = 'duplicate';
				break;
			case '1451':
				$error = 'locked';
				break;
			default:
				$error = '';
		}

		return $error;
	}

	public function setProperties($properties) {
		$temp = array();
		if (is_array($properties)) {
			foreach ($properties as $key => $value) {
				if (isset($this->{$key})) {
					$this->{$key} = $value;
				}
			}
		}
		return $this;
	}

	public function cleanProperties() {
		$this->table			= '';
		$this->join				= '';
		$this->where			= '';
		$this->groupby			= '';
		$this->having			= '';
		$this->orderby			= '';
		$this->limit			= '';
		$this->limit_offset		= '';

		$this->fields			= array();
		$this->values			= array();
		$this->num_rows			= 0;
		$this->error			= '';
		$this->insert_select	= '';
		$this->insert_id		= '';
		$this->errorno			= '';
	}

	// --------------------Addons---------------------------- //

	private function runCheck(array $args, $show = true) {
		foreach ($args as $arg) {
			if ($arg == 'table') {
				if (empty($this->table)) {
					$this->showError("Table Empty. Please Run: setTable(string < table >)", $show);
					return false;
				}
			} else if ($arg == 'fields') {
				if (empty($this->fields)) {
					$this->showError("Fields Empty. Please Run: setFields(array < fields >)", $show);
					return false;
				}
			} else if ($arg == 'where') {
				if (empty($this->where)) {
					$this->showError("Where Condition Empty. Please Run: setWhere(string < condition >)", $show);
					return false;
				}
			} else if ($arg == 'values') {
				if (empty($this->values)) {
					$this->showError("Values Empty. Please Run: setValues(array < values >)", $show);
					return false;
				}
			} else if ($arg == 'insert_select') {
				if (empty($this->insert_select)) {
					return false;
				}
			}
		}
		return true;
	}

	private function getMainTable() {
		$temp = explode(' ',$this->table);
		if ($temp) {
			return $temp[count($temp) - 1] . '.';
		} else {
			return '';
		}
	}

	// Pagination
	public function initPagination() {
		$this->page			= isset($_POST['page'])		? $_POST['page']	: 1;
		$this->result_limit	= isset($_POST['limit'])	? $_POST['limit']	: 10;
	}

	public function setPageLimit($page_limit) {
		$this->page_limit = $page_limit;
	}

	public function setPage($page) {
		$this->page = $page;
	}

	public function setPaginationCallback($callback) {
		$this->callback = $callback;
	}

	public function runPagination($addon = true) {
		$query_all		= $this->buildPagination($addon);
		$this->setLimit((($this->page - 1) * $this->result_limit));
		$this->setLimitOffset($this->result_limit);
		$query_limit	= $this->buildPagination($addon, false);
		$this->cleanProperties();
		$result = $this->conn->query($query_all);
		if ($result) {
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_object()) {
					$this->result[] = $row;
				}
				$this->result_count = $this->getRow()->count;
				if ($result->num_rows > 1) {
					$this->result_count = $result->num_rows;
				}
				$this->page_limit = ceil($this->result_count / $this->result_limit);
			}
		}
		if ($this->conn->error) {
			$this->showError($this->conn->error);
		}
		$this->result = array();
		$result = $this->conn->query($query_limit);
		if ($result) {
			$this->num_rows = $result->num_rows;
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_object()) {
					$this->result[] = $row;
				}
			}
		}
		if ($this->conn->error) {
			$this->showError($this->conn->error);
		}
		return (object) array(
			'result'		=> $this->getResult(),
			'result_count'	=> $this->result_count,
			'page'			=> $this->page,
			'page_limit'	=> $this->page_limit,
			'pagination'	=> $this->drawPagination()
		);
	}

	private function buildPagination($addon, $limit = true) {
		$this->result	= array();
		$this->query	= '';
		$this->num_rows = 0;
		$check = $this->runCheck(array('fields', 'table'));
		$where = $this->where;
		if ($addon) {
			$main_table = $this->getMainTable();
			$where .= ((empty($this->where)) ? " WHERE " : " AND ") . " {$main_table}companycode = '{$this->companycode}' ";
		}
		if ($check) {
			$fields = implode(', ', $this->fields);
			if ($limit) {
				if (empty($this->having)) {
					$fields = 'COUNT(*) count';
				} else {
					$fields = "COUNT({$this->fields[0]}) count";
				}
			}
			$this->query = "SELECT $fields FROM {$this->table}{$this->join}{$where}{$this->groupby}{$this->having}{$this->orderby}{$this->limit}{$this->limit_offset}";
			if ($this->conn->error) {
				$this->showError($this->conn->error);
			}
		}
		return $this->query;
	}

	private function drawPagination() {
		$inner_page				= (($this->page - 2) > 2) ? ($this->page - 2) : 2;
		$inner_counter			= 0;
		$inner_counter_limit	= 7;
		$pagination = '';

		if ($this->page_limit > 1) {
			$active = '';
			$active_class = 'class="active"';
			if ($this->page == 1) {
				$active = $active_class;
			}
			$pagination .= '<div class="text-center">
								<ul class="pagination">
									<li>
										<a href="#" data-page="' . ((($this->page - 1) > 0) ? $this->page - 1 : 1) . '">
											<span aria-hidden="true">&laquo;</span>
										</a>
									</li>
									<li ' . $active . '><a href="#" data-page="1">1</a></li>';

			if ($inner_page != 2) {
				$pagination .= '<li><a>...</a></li>';
				$inner_counter_limit--;
			}

			if (($inner_page + $inner_counter_limit) > $this->page_limit) {
				$inner_page = $this->page_limit - $inner_counter_limit;
			} else {
				$inner_counter_limit--;
			}

			if ($inner_page < 2) {
				$inner_page = 2;
			}

			for (; $inner_page < $this->page_limit && $inner_counter < $inner_counter_limit; $inner_page++, $inner_counter++) {
				$active = '';
				if ($this->page == $inner_page) {
					$active = $active_class;
				}
				$pagination .= '<li ' . $active . '><a href="#" data-page="' . $inner_page . '">' . $inner_page . '</a></li>';
			}

			if ($inner_page != $this->page_limit) {
				$pagination .= '<li><a>...</a></li>';
			}
			$active = '';
			if ($this->page == $this->page_limit) {
				$active = $active_class;
			}
			$pagination .= '
									<li ' . $active . '><a href="#" data-page="' . $this->page_limit . '">' . $this->page_limit . '</a></li>
									<li>
										<a href="#" data-page="' . ((($this->page + 1) <= $this->page_limit) ? $this->page + 1 : $this->page_limit) . '">
											<span aria-hidden="true">&raquo;</span>
										</a>
									</li>
								</ul>
							</div>';
		}
		return $pagination;
	}

	private function showError($error = 'Error', $show = true) {
		if (DEBUGGING && $show) {
			echo $error . (($this->query) ? '<br>Query: ' . $this->query : '');
		}
	}

	public function close() {
		$this->conn->close();
	}

}