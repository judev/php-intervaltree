<?php namespace IntervalTree;

/**
 * An Interval Tree implementation.
 * http://en.wikipedia.org/wiki/Interval_tree
 *
 * Based on:
 *  - https://github.com/tylerkahn/intervaltree-python
 *  - https://github.com/misshie/interval-tree
 *
 */
class IntervalTree {

	protected $top_node, $comparator;

	/**
	 * IntervalTree constructor.
	 *
	 * Pass in an array of RangeInterface compatible objects and
	 * an optional comparator callable.
	 *
	 * @param  array $ranges
	 * @param  Callable $comparator (optional)
	 * @return void
	 */
	public function __construct($ranges, $comparator = null) {
		if (is_null($comparator)) {
			$comparator = function($a, $b) {
				if ($a < $b) return -1;
				if ($a > $b) return 1;
				return 0;
			};
		}

		$this->comparator = $comparator;

		$this->top_node = $this->divide_intervals($ranges);
	}

	/**
	 * Search for ranges that overlap the specified value or range.
	 *
	 * @param  mixed $interval Either a RangeInterface or a value.
	 * @return array
	 */
	public function search($interval) {
		if (is_null($this->top_node)) {
			return array();
		}

		$result = $this->find_intervals($interval);
		$result = array_values($result);

		usort($result, function($a, $b) {
			$x = $a->rangeStart();
			$y = $b->rangeStart();
			$cmp = $this->compare($x, $y);
			if ($cmp == 0) {
				$x = $a->rangeEnd();
				$y = $b->rangeEnd();
				$cmp = $this->compare($x, $y);
			}
			return $cmp;
		});
		return $result;
	}

	protected function divide_intervals($intervals) {
		if (count($intervals) === 0) {
			return null;
		}

		$x_center = $this->center($intervals);
		$s_center = array();
		$s_left = array();
		$s_right = array();

		foreach ($intervals as $k) {
			if ($this->compare($k->rangeEnd(), $x_center) < 0) {
				$s_left[] = $k;
			}
			else if ($this->compare($k->rangeStart(), $x_center) > 0) {
				$s_right[] = $k;
			}
			else {
				$s_center[] = $k;
			}
		}

		return new IntervalTreeNode(
			$x_center,
			$s_center,
			$this->divide_intervals($s_left),
			$this->divide_intervals($s_right)
		);
	}

	protected function center($intervals) {
		usort($intervals, function($a, $b) {
			return $this->compare(
				$a->rangeStart(),
				$b->rangeStart()
			);
		});
		return $intervals[count($intervals) >> 1]->rangeStart();
	}

	protected function find_intervals($interval) {
		if ($interval instanceof RangeInterface) {
			$first = $interval->rangeStart();
			$last = $interval->rangeEnd();
		}
		else {
			$first = $interval;
			$last = null;
		}

		if (is_null($last)) {
			$result = $this->point_search($this->top_node, $first);
		}
		else {
			$result = array();
			foreach ($interval->rangeIterator() as $j) {
				$result = array_merge($result, $this->find_intervals($j));
			}
		}
		return $result;
	}

	protected function point_search($node, $point) {
		$result = array();

		// check whether the node values overlap point.
		foreach ($node->s_center as $k) {
			if ($this->compare($k->rangeStart(), $point) <= 0 && $this->compare($k->rangeEnd(), $point) > 0) {
				$result[spl_object_hash($k)] = $k;
			}
		}

		// compare point against node center to determine which child
		// node we should recurse through.
		$cmp = $this->compare($point, $node->x_center);

		if ($node->left_node && $cmp < 0) {
			$result = array_merge(
				$result,
				$this->point_search($node->left_node, $point, $result)
			);
		}

		if ($node->right_node && $cmp > 0) {
			$result = array_merge(
				$result,
				$this->point_search($node->right_node, $point, $result)
			);
		}

		return $result;
	}

	protected function compare($a, $b) {
		return call_user_func($this->comparator, $a, $b);
	}

	public function dump() {
		return $this->top_node->dump();
	}

}

class IntervalTreeNode {

	public $x_center, $s_center, $left_node, $right_node;

	public function __construct($x_center, $s_center, $left_node, $right_node) {
		$this->x_center = $x_center;
		usort($s_center, function($a, $b) {
			$a = $a->rangeStart();
			$b = $b->rangeStart();
			return $a < $b ? -1 : ($a > $b ? 1 : 0);
		});
		$this->s_center = $s_center;
		$this->left_node = $left_node;
		$this->right_node = $right_node;
	}

	public function dump($depth = 0) {
		$pad = str_repeat('  ', $depth);
		$contents = array();
		foreach ($this->s_center as $k) {
			$contents[] = $k;
		}
		$contents = implode(', ', $contents);
		$s = '';
		$s .= $this->x_center.' '.$contents."\n";
		if ($this->left_node) $s .= $pad.' L: '.$this->left_node->dump($depth + 1);
		else $s .= $pad." L: -\n";
		if ($this->right_node) $s .= $pad.' R: '.$this->right_node->dump($depth + 1);
		else $s .= $pad." R: -\n";
		return $s;
	}

}

