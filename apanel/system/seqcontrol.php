<?php
class seqcontrol extends db {

	public function getValue($code) {
		$properties = $this->setTable('wc_sequence_control')
						->setFields('current, prefix')
						->setWhere("AND code = '$code'")
						->setLimit(1)
						->getProperties();
		$result = $this->setProperties($properties)
						->runSelect()
						->getRow();
		$return = false;
		if ($result) {
			$return = $result->prefix . $result->current;
			$result->current += 1;
			$this->setProperties($properties)
				->setValues((array) $result);
			$this->runUpdate();
		}
		return $return;
	}

}