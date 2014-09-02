<?php namespace IntervalTree;

interface RangeInterface {

	/**
	 * Return the start of this range.
	 */
	public function rangeStart();

	/**
	 * Return the end of this range.
	 */
	public function rangeEnd();

	/**
	 * Return an iterable (or be a generator) over the
	 * range this represents. Should be inclusive of
	 * the range end.
	 */
	public function rangeIterator();

}


