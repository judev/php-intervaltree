<?php namespace IntervalTree;

use DateTime;
use DateInterval;

/**
 * Date range which includes intersecting dates.
 */
class DateRangeInclusive extends DateRangeExclusive
{
    /**
     * @param \DateTime     $start
     * @param \DateTime     $end
     * @param \DateInterval $step
     */
    public function __construct(DateTime $start, DateTime $end = null, DateInterval $step = null)
    {
        parent::__construct($start, $end, $step);
        $this->end->add($this->step);
    }
}
