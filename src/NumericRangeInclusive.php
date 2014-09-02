<?php namespace IntervalTree;

use DateTime;
use DateInterval;

class NumericRangeInclusive extends NumericRangeExclusive {

	public function __construct($start, $end = null, $step = 1) {
		parent::__construct($start, $end, $step);
		$this->end += $this->step;
	}

}


