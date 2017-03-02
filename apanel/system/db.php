<?php
class db {

	private $conn = '';
	public $query = '';

	public function __construct() {
		$this->conn = new mysqli(WC_HOSTNAME, WC_USERNAME, WC_PASSWORD, WC_DATABASE);
	}

	public function changeDatabase($database = WC_DATABASE) {

	}

	public function retrieveRecord($fields, $table, $condition, $limit = NULL) {
		$return = array();
		$query_fields = '';
		if (is_array($fields)) {
			$query_fields = implode(', ', $fields);
		} else {
			$query_fields = $fields;
		}
		$this->query = "SELECT $fields FROM $table WHERE $condition" . (($limit > 0) ? " LIMIT $limit" : '');
		$result = $this->conn->query($this->query);
		if ($result) {
			while ($row = $result->fetch_object()) {
				$return[] = $row;
			}
		} else {
			$this->displayError();
		}
		return $return;
	}

	public function retrieveRow($fields, $table, $condition) {
		$return = false;
		$result = $this->retrieveRecord($fields, $table, $condition, 1);
		if ($result) {
			return $result[0];
		}
		return $return;
	}

	public function insertRecord($values, $table) {
		$fields = array();
		$field_values = array();
		if (is_array($values)) {
			foreach ($values as $key => $value) {
				$field[] = $key;
				$field_values[] = $value;
			}
		}
		$fields = implode(', ', $fields);
		$field_values = "'" . implode("', '", $field_values) . "'";
		$query = "INSERT INTO $table ($fields) VALUES ($field_values)";
		$this->conn->query($query);
	}

	public function updateRecord($values, $table, $condition, $limit = NULL) {

	}

	public function deleteRecord($table, $condition, $limit = NULL) {

	}

	public function getValue($field, $table, $condition) {
		$this->query = "SELECT $field FROM $table WHERE $condition LIMIT 1";
		$result = $this->conn->query($this->query);
		if ($result) {
			if ($result->num_rows > 0) {
				$row = $result->fetch_object();
				return $row->{$field};
			} else {
				return '';
			}
		} else {
			$this->displayError();
		}
	}

	public function isExist($table, $condition) {

	}

	public function close() {
		$this->conn->close();
	}

	public function displayError() {
		if (DEBUGGING) {
			echo $this->query . "\n";
			echo $this->conn->error;
		}
	}

}

$db = new db;