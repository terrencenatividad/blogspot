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
	private $value			= array();
	private $result			= array();
	private $query			= '';
	private $num_rows		= 0;
	private $error			= '';
	private $insert_id		= '';

	// ----------------------Database----------------------- //

	public function __construct() {
		$this->conn = new mysqli(WC_HOSTNAME, WC_USERNAME, WC_PASSWORD, WC_DATABASE);
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

	// --------------------Query Builder--------------------- //

	public function buildSelect() {
		$this->result	= array();
		$this->query	= '';
		$this->num_rows = 0;
		$check = $this->runCheck(array('fields', 'table'));
		if ($check) {
			$fields = implode(', ', $this->fields);
			$this->query = "SELECT $fields FROM {$this->table}{$this->join}{$this->where}{$this->groupby}{$this->having}{$this->orderby}{$this->limit}{$this->limit_offset}";
			if ($this->conn->error) {
				$this->showError($this->conn->error);
			}
		}
		return $this->query;
	}

	public function buildInsert() {
		$this->insert_id	= '';
		$this->query		= '';
		$check = $this->runCheck(array('fields', 'table', 'values'));
		if ($check) {
			$fields = implode(', ', $this->fields);
			$query = "INSERT INTO {$this->table} ($fields) VALUES";
			foreach ($this->values as $key => $values) {
				$query .= "('" . implode("', '", $values) . "'), ";
			}
			$this->query = (substr($query, -2) == ', ') ? substr($query, 0, -2) : $query;
		}
		return $this->query;
	}

	public function buildUpdate() {
		$this->query = '';
		$check = $this->runCheck(array('fields', 'table', 'values', 'where'));
		if ($check) {
			$temp = array();
			$values = $this->values[0];
			$query = "UPDATE {$this->table} SET ";
			foreach ($values as $key => $value) {
				$query .= "$key = '$value', ";
			}
			$query = (substr($query, -2) == ', ') ? substr($query, 0, -2) : $query;
			$query .= "{$this->where}{$this->limit}";
			$this->query = $query;
		}
		return $this->query;
	}

	public function buildDelete() {
		$this->query = '';
		$check = $this->runCheck(array('table', 'where'));
		if ($check) {
			$this->query = "DELETE FROM {$this->table}{$this->where}{$this->limit}";
		}
		return $this->query;
	}

	// --------------------Execute Query--------------------- //

	public function runSelect() {
		$this->buildSelect();
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

	public function runInsert() {
		$this->buildInsert();
		$this->cleanProperties();
		if ($this->query) {
			$this->result = $this->conn->query($this->query);
			if ($this->conn->error) {
				$this->showError($this->conn->error);
			}
			$this->insert_id = $this->conn->insert_id;
		}
		return $this->result;
	}

	public function runUpdate() {
		$this->buildUpdate();
		$this->cleanProperties();
		if ($this->query) {
			$this->result = $this->conn->query($this->query);
			if ($this->conn->error) {
				$this->showError($this->conn->error);
			}
		}
		return $this->result;
	}

	public function runDelete() {
		$this->buildDelete();
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

	public function getNumRows() {
		return $this->num_rows();
	}

	public function getInsertId() {
		return $this->insert_id;
	}

	public function setProperties($properties, array $array = array('table', 'fields', 'values', 'where', 'groupby', 'having', 'join')) {
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
		$this->value		= array();
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

	private function showError($error = 'Error') {
		if (DEBUGGING) {
			echo $error . (($this->query) ? '<br>Query: ' . $this->query : '');
		}
	}

	public function close() {
		$this->conn->close();
	}

}