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
 * AVL tree in-order traverser.
 *
 * Implements parent-pointer node structures.
 *
 * @category Structures
 * @package  Rbppavl
 * @author   mondrake <mondrake@mondrake.org>
 * @license  http://www.gnu.org/licenses/gpl.html GNU GPLv3
 * @link     http://github.com/mondrake/Rbppavl
 */
class RbppavlTraverser extends RbppavlCommon
{
    /**
     * Tree being traversed
     *
     * @type RbppavlTree 
     */
    private $_tree;

    /**
     * Cursor to current node in tree
     *
     * @type RbppavlNode 
     */
    private $_node;

    /**
     * Initializes the traverser.
     *
     * @param RbppavlTree $tree      the tree to be traversed
     * @param boolean     $debugMode if true produces verbose debugging on tree operations
     *
     * @return null
     *
     * @api
     */
    public function __construct(RbppavlTree $tree = null, $debugMode = false)
    {
        $this->_tree = $tree;
        $this->cbc = $tree->cbc;
        $this->_node = null;
        $this->debugMode = $debugMode;
    }

    /**
      * Returns the data object with the least value (leftmost).
      *
      * Cursor is set to identified node.
      *
      * @return object the leftmost data object in the tree or null if tree is empty.
      *
      * @api
      */
    public function first()
    {
        $this->resetStatus();
        $this->_node = $this->nodeFirst($this->_tree);
        return ($this->_node) ? $this->_node->data : null;
    }

    /**
      * Returns the data object with the greatest value (rightmost).
      *
      * Cursor is set to identified node.
      *
      * @return object the rightmost data object in the tree or null if tree is empty.
      *
      * @api
      */
    public function last()
    {
        $this->resetStatus();
        $this->_node = $this->nodeLast($this->_tree);
        return ($this->_node) ? $this->_node->data : null;
    }

    /**
     * Searches the tree for the specified data object.
     *
     * If found, cursor is set to identified node, and a pointer to the data
     * object is returned.
     * If there is no matching data object, cursor is set to null and 
     * and returns null.
     *
     * @param object $data data object to be searched
     * @param int    $mode match mode - by default exact match
     *                     {RBPPAVL_FIND_EXACT_MATCH|RBPPAVL_FIND_PREV_MATCH|RBPPAVL_FIND_NEXT_MATCH}
     *
     * @return object pointer to data object found, or null if not found
     *
     * @api
     */
    public function find($data, $mode = RBPPAVL_FIND_EXACT_MATCH)
    {
        $this->resetStatus();
        if (!$this->checkData($data, __FUNCTION__)) {
            return null;
        }
        // empty tree
        if (is_null($this->_tree) or is_null($this->_tree->root)) {
            $this->_node = null;
            $this->setStatus(102);
            return null;
        }
        $y = $this->_tree->root;
        $q = null;
        $dir = null;
        $this->_node = $this->nodeFind($this->_tree, $data, $y, $q, $dir, $mode);
        return ($this->_node) ? $this->_node->data : null;
    }

    /**
     * Returns the next data object in in-order sequence.
     *
     * Updates the cursor. If there are no more data objects returns null.
     * If cursor is not set yet, returns the first data object in in-order sequence.
     *
     * @return object next data object in in-order sequence or null if cursor on rightmost node
     *
     * @api
     */
    public function next()
    {
        $this->resetStatus();
        $this->_node = $this->nodeNext($this->_tree, $this->_node);
        return ($this->_node) ? $this->_node->data : null;
    }

    /**
     * Returns the previous data object in in-order sequence.
     *
     * Updates the cursor. If there are no more data objects returns null.
     * If cursor is not set yet, returns the last data object in in-order sequence.
     *
     * @return object previous data object in in-order sequence or null if cursor on leftmost node
     *
     * @api
     */
    public function prev()
    {
        $this->resetStatus();
        $this->_node = $this->nodePrev($this->_tree, $this->_node);
        return ($this->_node) ? $this->_node->data : null;
    }

    /**
     * Returns the current node's data object.
     *
     * @return object data object of current node or null if cursor not initialized yet
     *
     * @api
     */
    public function curr()
    {
        $this->resetStatus();
        return ($this->_node) ? $this->_node->data : null;
    }
}