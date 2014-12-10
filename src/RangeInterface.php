<?php namespace IntervalTree;

/**
 * Base interface for ranges.
 */
interface RangeInterface
{
    /**
     * Return the start of this range.
     */
    public function getStart();

    /**
     * Return the end of this range.
     */
    public function getEnd();

    /**
     * Return an iterable (or be a generator) over the
     * range this represents. Should be inclusive of
     * the range end.
     */
    public function iterable();
}
