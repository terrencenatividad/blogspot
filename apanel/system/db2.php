<?php
class db {

	private $conn = '';
	private $table = '';
	private $result = '';
	private $fields = '';
	private $values = '';
	private $num_rows = '';
	private $limit = '';
	private $condition = '';
	private $limit_offset = '';
	private $query = '';


	public function __construct() {
		$this->conn = new mysqli(WC_HOSTNAME, WC_USERNAME, WC_PASSWORD, WC_DATABASE);
	}

	public function setTable(string $table) {
		$this->table = $table;
		return $this;
	}

	public function setQuery(string $query) {
		$this->query = $query;
		return $this;
	}

	public function getQuery() {
		return $this->query;
	}

	public function getNumRows() {
		return $this->num_rows;
	}

	public function setLimit(int $limit) {
		$this->limit = $limit;
		return $this;
	}

	public function setLimitOffset(int $limit_offset) {
		$this->limit_offset = $limit_offset;
		return $this;
	}

	public function setCondition(string $condition) {
		$this->condition = $condition;
		return $this;
	}

	public function setFields(array $fields) {
		$this->fields = $fields;
		return $this;
	}

	public function setValues(array $values) {
		$this->values = $values;
		return $this;
	}

}