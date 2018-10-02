<?php
class exportCSV {
protected $data;
 
/*
* @params array $columns
* @returns void
*/
public function __construct() {
//$this->data = '"' . trim(implode('","', $columns)) . '"' . "\n";
}
/*
* @params array $row
* @returns void
*/
public function addRow($row) {
$this->data .= '"' . trim(implode('","', $row)) . '"' . "\n";
}
/*
* @returns void
*/
public function export($filename,$type='csv') {
header('Content-type: application/'.$type);
header('Content-Disposition: attachment; filename="' . $filename . '.'.$type.'"');
 
echo $this->data;
die();
}
public function __toString() {
return $this->data;
}
}
