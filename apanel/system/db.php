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
	private $insert_id		= '';

	// ----------------------Database----------------------- //

	public function __construct() {
		$session = new session();
		$this->conn				= new mysqli(WC_HOSTNAME, WC_USERNAME, WC_PASSWORD, WC_DATABASE);
		$this->companycode		= COMPANYCODE;
		$this->datetime			= date('Y-m-d H:i:s');
		$this->username			= USERNAME;
		$this->updateprogram	= '';
		if (defined('MODULE_NAME') && defined('MODULE_TASK')) {
			$this->updateprogram	= MODULE_NAME . '|' . MODULE_TASK;
		}
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
		$this->limit = ($limit) ? " LIMIT $limit" : '';
		return $this;
	}

	public function setLimitOffset($limit_offset) {
		$this->limit_offset = ($this->limit) ? " $limit_offset" : '';
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
		$static			= array();
		$array			= array();
		$this->values	= array();
		foreach ($values as $key => $value) {
			$this->fields[] = $key;
			if (is_array($value)) {
				$max		= count($value);
				$array[]	= $key;
			} else {
				$static[]	= $key;
			}
		}
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
		$this->insert_id	= '';
		$this->query		= '';
		$check = $this->runCheck(array('fields', 'table', 'values'));
		$temp_fields = $this->fields;
		$where = $this->where;
		if ($addon) {
			$temp_fields[] = 'enteredby';
			$temp_fields[] = 'entereddate';
			$temp_fields[] = 'companycode';
			$temp_fields[] = 'updateprogram';
		}
		if ($check) {
			$fields = implode(', ', $temp_fields);
			$query = "INSERT INTO {$this->table} ($fields) VALUES";
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
			if ($this->conn->error && $this->conn->errno != 1062) {
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
			if ($this->conn->error && $this->conn->errno != 1062) {
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
			if ($this->conn->error) {
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
		$this->table		= '';
		$this->join			= '';
		$this->where		= '';
		$this->groupby		= '';
		$this->having		= '';
		$this->orderby		= '';
		$this->limit		= '';
		$this->limit_offset	= '';

		$this->fields		= array();
		$this->values		= array();
		$this->num_rows		= 0;
		$this->error		= '';
		$this->insert_id	= '';
	}

	// --------------------Addons---------------------------- //

	private function runCheck(array $args) {
		foreach ($args as $arg) {
			if ($arg == 'table') {
				if (empty($this->table)) {
					$this->showError("Table Empty. Please Run: setTable(string < table >)");
					return false;
				}
			} else if ($arg == 'fields') {
				if (empty($this->fields)) {
					$this->showError("Fields Empty. Please Run: setFields(array < fields >)");
					return false;
				}
			} else if ($arg == 'where') {
				if (empty($this->where)) {
					$this->showError("Where Condition Empty. Please Run: setWhere(string < condition >)");
					return false;
				}
			} else if ($arg == 'values') {
				if (empty($this->values)) {
					$this->showError("Values Empty. Please Run: setValues(array < values >)");
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

	private function showError($error = 'Error') {
		if (DEBUGGING) {
			echo $error . (($this->query) ? '<br>Query: ' . $this->query : '');
		}
	}

	public function close() {
		$this->conn->close();
	}

}