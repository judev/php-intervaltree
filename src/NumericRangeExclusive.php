<?php namespace IntervalTree;

use DateTime;
use DateInterval;

class NumericRangeExclusive implements RangeInterface {

	protected $start, $end, $step;

	public function __construct($start, $end = null, $step = 1) {
		$this->start = $start;
		$this->end = $end;
		$this->step = $step;
	}

	public function rangeStart() {
		return $this->start;
	}

	public function rangeEnd() {
		return $this->end;
	}

	public function rangeIterator() {
		for ($i = $this->rangeStart(); $i < $this->rangeEnd(); $i += $this->step) {
			yield $i;
		}
	}

	public function __toString() {
		return $this->start.'..'.$this->end;
	}

}


