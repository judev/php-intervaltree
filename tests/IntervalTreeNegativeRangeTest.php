<?php

use IntervalTree\IntervalTree;
use IntervalTree\DateRangeExclusive;

class IntervalTreeNegativeRangeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \IntervalTree\NegativeRangeException
     */
    public function testNegativeRangeThrowsException()
    {
        $dateRange = new DateRangeExclusive(
            date_create('2014-09-01T03:00:00+00:00'),
            date_create('2014-01-01T03:15:00+00:00')
        );

        $tree = new IntervalTree(array($dateRange));
    }
}
