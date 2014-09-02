<?php namespace IntervalTree;

use DateTime;
use DateInterval;

class DateRangeInclusive extends DateRangeExclusive {

	public function __construct(DateTime $start, DateTime $end = null, DateInterval $step = null) {
		parent::__construct($start, $end, $step);
		$this->end->add($this->step);
	}

}


