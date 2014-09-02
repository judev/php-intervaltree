<?php

use IntervalTree\IntervalTree;
use IntervalTree\DateRangeInclusive;
use IntervalTree\NumericRangeInclusive;
use IntervalTree\DateRangeExclusive;
use IntervalTree\NumericRangeExclusive;

class IntervalTreeNumericTest extends PHPUnit_Framework_TestCase {

	public function testNumericRangeInclusiveIteration() {
		$expected = array(10, 12, 14, 16, 18, 20);
		$range = new NumericRangeInclusive(10, 20, 2);
		foreach ($range->rangeIterator() as $value) {
			$this->assertEquals(array_shift($expected), $value);
		}
		$this->assertEquals(0, count($expected));
	}

	public function testNumericRangeExclusiveIteration() {
		$expected = array(10, 12, 14, 16, 18);
		$range = new NumericRangeExclusive(10, 20, 2);
		foreach ($range->rangeIterator() as $value) {
			$this->assertEquals(array_shift($expected), $value);
		}
		$this->assertEquals(0, count($expected));
	}

	public function testNumericInclusiveSearch() {
		$intervals = $this->getNumericInclusiveIntervals();
		$tree = new IntervalTree($intervals);
		$results = $tree->search(4);
		$this->assertCount(3, $results);
		$this->assertSame($intervals[6], $results[0]);
		$this->assertSame($intervals[0], $results[1]);
		$this->assertSame($intervals[1], $results[2]);
	}

	public function testNumericExclusiveSearch() {
		$intervals = $this->getNumericExclusiveIntervals();
		$tree = new IntervalTree($intervals);
		$results = $tree->search(4);
		$this->assertCount(2, $results);
		$this->assertSame($intervals[0], $results[0]);
		$this->assertSame($intervals[1], $results[1]);
	}

	private function getNumericInclusiveIntervals() {
		return $this->intervals('IntervalTree\NumericRangeInclusive');
	}

	private function getNumericExclusiveIntervals() {
		return $this->intervals('IntervalTree\NumericRangeExclusive');
	}

	private function intervals($class) {
		return array(
			new $class(1, 5),
			new $class(4, 7),
			new $class(6, 7),
			new $class(10, 15),
			new $class(13, 16),
			new $class(11, 12),
			new $class(-3, 4),
		);
	}

}

