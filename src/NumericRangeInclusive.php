<?php namespace IntervalTree;

/**
 * Numeric range including intersecting numbers.
 */
class NumericRangeInclusive extends NumericRangeExclusive
{
    /**
     * @param int $start
     * @param int end
     * @param int $step
     */
    public function __construct($start, $end = null, $step = 1)
    {
        parent::__construct($start, $end, $step);
        $this->end += $this->step;
    }
}
