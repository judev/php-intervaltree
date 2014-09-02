<?php

use IntervalTree\IntervalTree;
use IntervalTree\DateRangeInclusive;
use IntervalTree\NumericRangeInclusive;
use IntervalTree\DateRangeExclusive;
use IntervalTree\NumericRangeExclusive;

class IntervalTreeDateRangeTest extends PHPUnit_Framework_TestCase {

	public function testDateRangeInclusiveIteration() {
		$expected = array(
			date_create('2014-09-01T03:00:00+00:00'),
			date_create('2014-09-01T03:15:00+00:00'),
			date_create('2014-09-01T03:30:00+00:00'),
			date_create('2014-09-01T03:45:00+00:00'),
			date_create('2014-09-01T04:00:00+00:00'),
			date_create('2014-09-01T04:15:00+00:00'),
		);
		$range = new DateRangeInclusive($expected[0], $expected[5], new DateInterval('PT15M'));
		foreach ($range->rangeIterator() as $value) {
			$this->assertEquals(array_shift($expected), $value);
		}
		$this->assertEquals(0, count($expected));
	}

	public function testDateRangeExclusiveIteration() {
		$expected = array(
			date_create('2014-09-01T03:00:00+00:00'),
			date_create('2014-09-01T03:15:00+00:00'),
			date_create('2014-09-01T03:30:00+00:00'),
			date_create('2014-09-01T03:45:00+00:00'),
			date_create('2014-09-01T04:00:00+00:00'),
		);
		$end = date_create('2014-09-01T04:15:00+00:00');
		$range = new DateRangeExclusive($expected[0], $end, new DateInterval('PT15M'));
		foreach ($range->rangeIterator() as $value) {
			$this->assertEquals(array_shift($expected), $value);
		}
		$this->assertEquals(0, count($expected));
	}

	public function testDateRangeInclusiveSearch() {
		$intervals = $this->getDateRangeInclusiveIntervals();
		$tree = new IntervalTree($intervals);

		$results = $tree->search(date_create('2014-09-04T00:00:00+00:00'));
		$this->assertCount(3, $results);
		$this->assertSame($intervals[3], $results[0]);
		$this->assertSame($intervals[0], $results[1]);
		$this->assertSame($intervals[1], $results[2]);

		$results = $tree->search(date_create('2014-09-05T00:00:00+00:00'));
		$this->assertCount(2, $results);
		$this->assertSame($intervals[0], $results[0]);
		$this->assertSame($intervals[1], $results[1]);

		$results = $tree->search(date_create('2014-08-05T00:00:00+00:00'));
		$this->assertCount(0, $results);
		$results = $tree->search(date_create('2014-09-25T00:00:00+00:00'));
		$this->assertCount(0, $results);
	}

	public function testDateRangeExclusiveSearch() {
		$intervals = $this->getDateRangeExclusiveIntervals();
		$tree = new IntervalTree($intervals);

		$results = $tree->search(date_create('2014-09-04T00:00:00+00:00'));
		$this->assertCount(2, $results);
		$this->assertSame($intervals[0], $results[0]);
		$this->assertSame($intervals[1], $results[1]);

		$results = $tree->search(date_create('2014-09-05T00:00:00+00:00'));
		$this->assertCount(1, $results);
		$this->assertSame($intervals[1], $results[0]);
	}

	private function getDateRangeInclusiveIntervals() {
		return $this->intervals('IntervalTree\DateRangeInclusive');
	}

	private function getDateRangeExclusiveIntervals() {
		return $this->intervals('IntervalTree\DateRangeExclusive');
	}

	private function intervals($class) {
		$day_interval = new DateInterval('P1D');
		return array(
			new $class(
				date_create('2014-09-01T00:00:00+00:00'),
				date_create('2014-09-05T00:00:00+00:00'),
				$day_interval
			),
			new $class(
				date_create('2014-09-04T00:00:00+00:00'),
				date_create('2014-09-07T00:00:00+00:00'),
				$day_interval
			),
			new $class(
				date_create('2014-09-10T00:00:00+00:00'),
				date_create('2014-09-15T00:00:00+00:00'),
				$day_interval
			),
			new $class(
				date_create('2014-08-20T00:00:00+00:00'),
				date_create('2014-09-04T00:00:00+00:00'),
				$day_interval
			),
		);
	}

}

