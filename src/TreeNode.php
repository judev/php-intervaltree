<?php namespace IntervalTree;

class TreeNode
{
    /**
     * @var mixed
     */
    public $x_center;

    /**
     * @var array
     */
    public $s_center;

    /**
     * @var mixed
     */
    public $left_node;

    /**
     * @var mixed
     */
    public $right_node;

    /**
     * @param mixed $x_center
     * @param array $s_center
     * @param mixed $left_node
     * @param mixed $right_node
     */
    public function __construct($x_center, array $s_center, $left_node, $right_node)
    {
        $this->x_center = $x_center;

        usort($s_center, function (RangeInterface $a, RangeInterface $b) {
            $a = $a->getStart();
            $b = $b->getStart();

            return $a < $b ? -1 : ($a > $b ? 1 : 0);
        });

        $this->s_center = $s_center;
        $this->left_node = $left_node;
        $this->right_node = $right_node;
    }
}
