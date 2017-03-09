<?php
class db {

	private $conn = '';
	private $table = '';
	private $result = array();
	private $fields = array();
	private $values = array();
	private $num_rows = '';
	private $limit = '';
	private $limit_offset = '';
	private $where_condition = '';
	private $show_query = false;
	private $preview = false;
	private $query = '';
	private $insert_id = '';


	public function __construct() {
		$this->conn = new mysqli(WC_HOSTNAME, WC_USERNAME, WC_PASSWORD, WC_DATABASE);
	}

	public function changeDatabase($database) {
		$this->conn = new mysqli(WC_HOSTNAME, WC_USERNAME, WC_PASSWORD, $database);
	}

	public function setTable($table) {
		$this->limit = '';
		$this->limit_offset = '';
		$this->insert_id = '';
		$this->table = $table;
		return $this;
	}

	public function showQuery($show) {
		if (DEBUGGING) {
			$this->show_query = $show;
		}
		return $this;
	}

	public function setPreview() {
		$this->preview = true;
		return $this;
	}

	public function getQuery() {
		return $this->query;
	}

	public function getNumRows() {
		return $this->num_rows;
	}

	public function getInsertId() {
		return $this->insert_id;
	}

	public function setLimit($limit) {
		$this->limit = $limit;
		return $this;
	}

	public function setLimitOffset($limit_offset) {
		$this->limit_offset = $limit_offset;
		return $this;
	}

	public function setFields($fields) {
		$this->fields = (is_array($fields)) ? $fields : explode(',', $fields);
		$this->values = array();
		return $this;
	}

	public function setValues(array $fields_values) {
		if ( ! empty($fields_values)) {
			$temp = isset($fields_values[0]) ? $fields_values[0] : $fields_values;
			$this->fields = array();
			foreach($temp as $key => $value) {
				$this->fields[] = $key;
			}
			$this->values = (isset($fields_values[0])) ? $fields_values : array($fields_values);
		}
		return $this;
	}

	public function setWhere($value) {
		$this->where_condition = $value;
		return $this;
	}

	public function cleanFieldsAndValues() {
		$this->values = array();
		return $this;
	}

	public function cleanAll() {
		$this->table = '';
		$this->result = array();
		$this->fields = array();
		$this->values = array();
		$this->num_rows = '';
		$this->limit = '';
		$this->limit_offset = '';
		$this->where_condition = '';
		$this->query = '';
		$this->preview = false;
		return $this;
	}

	public function runSelect() {
		$this->result = array();
		$check = $this->runCheck(array('fields', 'table'));
		if ($check) {
			$fields = implode(', ', $this->fields);
			$table = $this->table;
			$where_condition = ( ! empty($this->where_condition)) ? "WHERE " . $this->where_condition : '';
			$limit = ( ! empty($this->limit)) ? "LIMIT " . $this->limit : '';
			$limit_offset = ( ! empty($this->limit) && ! empty($this->limit_offset)) ? ", " . $this->limit_offset : '';
			$this->query = "SELECT $fields FROM $table $where_condition $limit $limit_offset";
			$result = $this->conn->query($this->query);
			if ($result) {
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_object()) {
						$this->result[] = $row;
					}
				}
			}
			if ($this->conn->error) {
				$this->showError($this->conn->error);
			}
		}
		return $this;
	}

	public function getRow() {
		if ($this->show_query || $this->preview) {
			return $this->query;
		} else {
			return (empty($this->result)) ? false : $this->result[0];
		}
	}

	public function getResult() {
		if ($this->show_query || $this->preview) {
			return $this->query;
		} else {
			return $this->result;
		}
	}

	public function runInsert() {
		$check = $this->runCheck(array('fields', 'table', 'values'));
		if ($check) {
			$fields = implode(', ', $this->fields);
			$table = $this->table;
			$query = "INSERT INTO $table ($fields) VALUES";
			foreach ($this->values as $key => $values) {
				$query .= "('" . implode("', '", $values) . "'), ";
			}
			$this->query = (substr($query, -2) == ', ') ? substr($query, 0, -2) : $query;
			if ( ! $this->preview) {
				$this->result = $this->conn->query($this->query);
				$this->insert_id = $this->conn->insert_id;
			}
			if ($this->conn->error) {
				$this->showError($this->conn->error);
			}
		}
		if ($this->show_query || $this->preview) {
			return $this->query;
		} else {
			return $this->result;
		}
	}

	public function runUpdate() {
		$check = $this->runCheck(array('fields', 'table', 'values', 'where'));
		if ($check) {
			$table = $this->table;
			$where_condition = $this->where_condition;
			$limit = ( ! empty($this->limit)) ? "LIMIT " . $this->limit : '';
			$temp = array();
			$values = $this->values[0];
			$query = "UPDATE $table SET ";
			foreach ($values as $key => $value) {
				$query .= "$key = '$value', ";
			}
			$query = (substr($query, -2) == ', ') ? substr($query, 0, -2) : $query;
			$query .= " WHERE $where_condition $limit";
			$this->query = $query;
			if ( ! $this->preview) {
				$this->result = $this->conn->query($this->query);
			}
			if ($this->conn->error) {
				$this->showError($this->conn->error);
			}
		}
		if ($this->show_query || $this->preview) {
			return $this->query;
		} else {
			return $this->result;
		}
	}

	public function runDelete() {
		$check = $this->runCheck(array('table', 'where'));
		if ($check) {
			$table = $this->table;
			$where_condition = $this->where_condition;
			$limit = ( ! empty($this->limit)) ? "LIMIT " . $this->limit : '';
			$this->query = "DELETE FROM $table WHERE $where_condition $limit";
			if ( ! $this->preview) {
				$this->result = $this->conn->query($this->query);
			}
			if ($this->conn->error) {
				$this->showError($this->conn->error);
			}
		}
		if ($this->show_query || $this->preview) {
			return $this->query;
		} else {
			return $this->result;
		}
	}

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
				if (empty($this->where_condition)) {
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
			echo $error . '<br>Query: ' . $this->query;
		}
	}

	public function close() {
		$this->conn->close();
	}

}