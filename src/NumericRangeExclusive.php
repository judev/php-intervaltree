<?php namespace IntervalTree;

/**
 * Numeric range excluding intersecting numbers.
 */
class NumericRangeExclusive implements RangeInterface
{
    /**
     * @var int
     */
    protected $start;

    /**
     * @var int
     */
    protected $end;

    /**
     * @var int
     */
    protected $step;

    /**
     * @param int $start
     * @param int end
     * @param int $step
     */
    public function __construct($start, $end = null, $step = 1)
    {
        $this->start = $start;
        $this->end = $end;
        $this->step = $step;
    }

    /**
     * @return int
     *
     * {@inheritDoc}
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Generator
     */
    public function iterable()
    {
        for ($i = $this->getStart(); $i < $this->getEnd(); $i += $this->step) {
            yield $i;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->start.'..'.$this->end;
    }
}
