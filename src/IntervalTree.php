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
class IntervalTree
{
    /**
     * @var \IntervalTree\TreeNode
     */
    protected $top_node;

    /**
     * @var callable
     */
    protected $comparator;

    /**
     * IntervalTree constructor.
     *
     * Pass in an array of RangeInterface compatible objects and
     * an optional comparator callable.
     *
     * @param array    $ranges
     * @param callable $comparator
     *
     * @return void
     */
    public function __construct(array $ranges, callable $comparator = null)
    {
        if (is_null($comparator)) {
            $comparator = function ($a, $b) {
                if ($a < $b) {
                    return -1;
                }
                if ($a > $b) {
                    return 1;
                }

                return 0;
            };
        }

        $this->comparator = $comparator;

        $this->top_node = $this->divide_intervals($ranges);
    }

    /**
     * Search for ranges that overlap the specified value or range.
     *
     * @param mixed $interval Either a RangeInterface or a value.
     *
     * @return array
     */
    public function search($interval)
    {
        if (is_null($this->top_node)) {
            return array();
        }

        $result = $this->find_intervals($interval);
        $result = array_values($result);

        usort($result, function (RangeInterface $a, RangeInterface $b) {
            $x = $a->getStart();
            $y = $b->getStart();

            $comparedValue = $this->compare($x, $y);

            if ($comparedValue == 0) {
                $x = $a->getEnd();
                $y = $b->getEnd();
                $comparedValue = $this->compare($x, $y);
            }

            return $comparedValue;
        });

        return $result;
    }

    /**
     * @param array $intervals
     *
     * @return \IntervalTree\TreeNode|null
     */
    protected function divide_intervals(array $intervals)
    {
        if (count($intervals) === 0) {
            return null;
        }

        $x_center = $this->center($intervals);
        $s_center = array();
        $s_left = array();
        $s_right = array();

        foreach ($intervals as $k) {
            if ($k->getStart() > $k->getEnd()) {
                throw new NegativeRangeException(
                    'Range is negative (maybe you entered the range in reverse order?)'
                );
            }
            if ($this->compare($k->getEnd(), $x_center) < 0) {
                $s_left[] = $k;
            } elseif ($this->compare($k->getStart(), $x_center) > 0) {
                $s_right[] = $k;
            } else {
                $s_center[] = $k;
            }
        }

        return new TreeNode(
            $x_center,
            $s_center,
            $this->divide_intervals($s_left),
            $this->divide_intervals($s_right)
        );
    }

    /**
     * @param array $intervals
     *
     * @return mixed
     */
    protected function center(array $intervals)
    {
        usort($intervals, function (RangeInterface $a, RangeInterface $b) {
            return $this->compare(
                $a->getStart(),
                $b->getStart()
            );
        });

        return $intervals[count($intervals) >> 1]->getStart();
    }

    /**
     * @param mixed $interval
     *
     * @return mixed
     */
    protected function find_intervals($interval)
    {
        if ($interval instanceof RangeInterface) {
            $first = $interval->getStart();
            $last = $interval->getEnd();
        } else {
            $first = $interval;
            $last = null;
        }

        if (null === $last) {
            $result = $this->point_search($this->top_node, $first);
        } else {
            $result = array();
            foreach ($interval->iterable() as $j) {
                $result = array_merge($result, $this->find_intervals($j));
            }
        }

        return $result;
    }

    /**
     * @param \IntervalTree\TreeNode $node
     * @param mixed                  $point
     *
     * @return array
     */
    protected function point_search(TreeNode $node, $point)
    {
        $result = array();

        // check whether the node values overlap point.
        foreach ($node->s_center as $k) {
            if ($this->compare($k->getStart(), $point) <= 0 && $this->compare($k->getEnd(), $point) > 0) {
                $result[spl_object_hash($k)] = $k;
            }
        }

        // compare point against node center to determine which child
        // node we should recurse through.
        $comparedValue = $this->compare($point, $node->x_center);

        if ($node->left_node && $comparedValue < 0) {
            $result = array_merge(
                $result,
                $this->point_search($node->left_node, $point, $result)
            );
        }

        if ($node->right_node && $comparedValue > 0) {
            $result = array_merge(
                $result,
                $this->point_search($node->right_node, $point, $result)
            );
        }

        return $result;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     *
     * return int
     */
    protected function compare($a, $b)
    {
        return call_user_func($this->comparator, $a, $b);
    }
}
