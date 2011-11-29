<?php
/**
 * PHP AVL binary tree
 *
 * A set of PHP classes implementing management of binary trees according to 
 * AVL rules.
 * The API exposes tree management operations (insert, replace, delete, find),
 * traversal (find, first, last, prev, next, curr). Nodes within the trees are
 * not exposed publicly.
 * Tree and traversal operations implement relaxed balance factors, and
 * parent-pointer node structures.
 * Hooks for node comparison, error handling and logging capabilities are provided
 * via a callback interface. 
 *
 * PHP version 5
 *
 * @category Structures
 * @package  Rbppavl
 * @author   mondrake <mondrake@mondrake.org>
 * @license  http://www.gnu.org/licenses/gpl.html GNU GPLv3
 * @link     http://github.com/mondrake/Rbppavl
 */

/**
 * Rbppavl tree node.
 *
 * Class with node methods.
 *
 * @category Structures
 * @package  Rbppavl
 * @author   mondrake <mondrake@mondrake.org>
 * @license  http://www.gnu.org/licenses/gpl.html GNU GPLv3
 * @link     http://github.com/mondrake/Rbppavl
 * @access   private
 */
class RbppavlNode extends Rbppavl
{
    /**
     * Pointers to the node's left and right subtrees
     *
     * @type array 
     */
    public $link = array( null, null );
    
    /**
     * Pointer to the node's parent node
     *
     * @type RbppavlNode 
     */
    public $parent = null;
    
    /**
     * Pointer to the node's data object (payload)
     *
     * @type object
     */
    public $data;
    
    /**
     * Height of the node's tree
     *
     * @type integer
     */
    public $height;

    /**
     * Constructs a RbppavlNode instance.
     *
     * @param object $tree   the caller tree
     * @param object $data   the object of the node
     * @param object $parent the parent RbppavlNode
     *
     * @access private
     */
    public function __construct(RbppavlTree $tree, $data, $parent)
    {
        // sets node attributes
        $this->link[0] = $this->link[1] = null;
        $this->parent = $parent;
        $this->data = $data;
        $this->height = 0;
    }

    /**
     * Returns the current value of the node's balancing factor.
     *
     * Balance is determined from the difference of the heights of the
     * node's subtrees.
     *
     * @return int value of the balancing factor
     *
     * @access private
     */
    public function balance()
    {
        $lHeight = $this->link[0] ? $this->link[0]->height : -1;
        $rHeight = $this->link[1] ? $this->link[1]->height : -1;
        return $rHeight - $lHeight;
    }

    /**
     * Resets the node's height.
     *
     * Height is determined from the value of the 'height' property
     * of the subtrees.
     *
     * @return null
     *
     * @access private
     */
    public function heightReset()
    {
        $lHeight = $this->link[0] ? $this->link[0]->height : -1;
        $rHeight = $this->link[1] ? $this->link[1]->height : -1;
        $this->height = max($lHeight, $rHeight) + 1;
        return null;
    }

    /**
     * Forces removal of the node and of the associated data from the tree.
     *
     * Recursively remove the node, all the nodes in its subtrees, and all
     * associated data objects without recalculating heights and balancing
     * factors of the rest of the tree.
     * Called by RbppavlTree::_destruct() to orderly free memory when
     * destructuring a tree object.
     *
     * @param object &$ctr counter of nodes wiped from the tree
     *
     * @return int number of nodes wiped from the tree
     *
     * @access private
     */
    public function wipe(&$ctr)
    {
        if ($this->link[0]) {
            $this->link[0]->wipe($ctr);
            $this->link[0] = null;
        }
        if ($this->link[1]) {
            $this->link[1]->wipe($ctr);
            $this->link[1] = null;
        }
        // wipes node
        $this->data = null;
        $this->link[0] = $this->link[1] = null;
        if ($this->parent) {
            $dir = ($this === $this->parent->link[0] ? 0 : 1);
            $this->parent->link[$dir] = null;
        }
        //$this->parent = null;
        $ctr++;
        return($ctr);
    }

    /**
     * Validates the node's compliance to AVL rules.
     *
     * This method should be used only for debugging purposes.
     *
     * Called by RbppavlTree::debugValidate().
     *
     * @param int     $balanceFactor balancing factor
     * @param int     &$status       failure status
     * @param boolean $recurse       if true, recursively validates all the nodes in the node's subtrees
     *
     * @return object pointer to the node failing AVL rules, or null if node is compliant
     *
     * @access private
     */
    public function debugNodeValidate($balanceFactor, &$status, $recurse = true)
    {
        if ($recurse) {
            if ($this->link[0]) {
                $n = $this->link[0]->debugNodeValidate($balanceFactor, $status);
                if ($status > 0) {
                    return $n;
                }
            }
            if ($this->link[1]) {
                $n = $this->link[1]->debugNodeValidate($balanceFactor, $status);
                if ($status > 0) {
                    return $n;
                }
            }
        }
        // validates node
        if ($this->height <> $this->_debugHeightCalculate()) {
            // height failure
            $status = RBPPAVL_VALIDATION_HEIGHT_FAILURE;
            return $this;
        }
        if (abs($this->balance()) > $balanceFactor) {
            // balance failure
            $status = RBPPAVL_VALIDATION_BALANCE_FAILURE;
            return $this;
        }
        // valid node
        $status = RBPPAVL_VALIDATION_OK;
        return null;
    }

    /**
     * Calculate the node's height.
     *
     * This method should be used only for debugging purposes.
     *
     * Differently from heightReset(), the height is calculated by recursively
     * calculating the height of the node's subtrees from the leaf
     * node upwards. This method is used by debugNodeValidate() to ensure
     * the height property of a node corresponds to the actual height.
     * In normal mode, the 'height' property stores the current node's
     * height, and is updated by RbppavlTree methods to keep it consistent
     * across tree balancing operations.
     *
     * @return int the node's height
     *
     * @access private
     */
    private function _debugHeightCalculate()
    {
        if (!$this->link[0] and !$this->link[1]) {
            return 0;
        } else {
            $lHeight = $this->link[0] ?
                       $this->link[0]->_debugHeightCalculate() : -1;
            $rHeight = $this->link[1] ?
                       $this->link[1]->_debugHeightCalculate() : -1;
            return (max($lHeight, $rHeight) + 1);
        }
    }
}
