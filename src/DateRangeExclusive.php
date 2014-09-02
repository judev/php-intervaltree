<?php namespace IntervalTree;

use DateTime;
use DateInterval;

class DateRangeExclusive implements RangeInterface {

	protected $start, $end, $step;

	public function __construct(DateTime $start, DateTime $end = null, DateInterval $step = null) {
		$this->start = clone $start;
		$this->end = clone $end;
		$this->step = $step ?: new DateInterval('P1D');
	}

	public function rangeStart() {
		return $this->start;
	}

	public function rangeEnd() {
		return $this->end;
	}

	public function rangeIterator() {
		$idate = clone $this->rangeStart();
		while ($idate < $this->rangeEnd()) {
			yield $idate;
			$idate->add($this->step);
		}
	}

	public function __toString() {
		return $this->start->format('Y-m-d').' .. '.$this->end->format('Y-m-d');
	}

}


