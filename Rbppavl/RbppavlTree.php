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
 * AVL tree.
 *
 * Implements relaxed balance factors, and parent-pointer node structures.
 * When instantiating a new tree, a callback interface must have been defined 
 * in compliance to RbppavlCbInterface, and its class name passed as an argument. 
 * Rbppavl will create an instance of the specified class.
 *
 * @category Structures
 * @package  Rbppavl
 * @author   mondrake <mondrake@mondrake.org>
 * @license  http://www.gnu.org/licenses/gpl.html GNU GPLv3
 * @link     http://github.com/mondrake/Rbppavl
 */
class RbppavlTree extends RbppavlCommon
{
    /**
     * Pointer to the root node of the tree
     *
     * @type RbppavlNode 
     */
    public $root;
    
    /**
     * Maximum imbalance allowed for a node
     *
     * @type integer 
     */
    public $balanceFactor;

    /**
     * Number of nodes in tree
     *
     * @type integer 
     */
    private $_count;

    /**
     * Internal tree statistics
     *
     * @type array 
     */
    private $_statistics = array( 'att_ins'  => 0,
                                  'att_repl' => 0,
                                  'att_del'  => 0,
                                  'ins'      => 0,
                                  'repl'     => 0,
                                  'del'      => 0,
                                  'self'     => 0,
                                  'll'       => 0,
                                  'lr'       => 0,
                                  'rr'       => 0,
                                  'rl'       => 0);

    /**
     * Creates a new tree.
     *
     * @param string  $callbackClass callback interface class to be instantiated
     * @param int     $balanceFactor AVL balancing factor of the tree
     * @param boolean $debugMode     if true produces verbose debugging on tree operations
     * @param mixed   $memThreshold  if not null enables available memory checking, with min available memory as
     *                               per value specified
     *
     * @api
     */
    public function __construct($callbackClass, $balanceFactor = 1, $debugMode = false, $memThreshold = null)
    {
        if (empty($callbackClass)) {
            // no callback class specified
            $this->setStatus(106);
            return null;
        }
        $this->cbc = new $callbackClass();
        $this->balanceFactor = $balanceFactor;
        $this->root = null;
        $this->_count = 0;
        $this->debugMode = $debugMode;
        if ($memThreshold) {
            $this->memoryLimit = $this->returnBytes(ini_get('memory_limit'));
            $this->memoryThreshold = $this->returnBytes($memThreshold);
        }
        return $this;
    }

    /**
     * Destroys a tree.
     *
     * @access private
     */
    public function __destruct()
    {
        if ($this->root) {
            $ctr = 0;
            $this->root->wipe($ctr);
            // debug diagnostic - wiped nodes
            if ($this->debugMode) {
                $this->setStatus(12, array('%ctr' => $ctr,));
            }
        }
    }

    /**
      * Inserts a data object in the tree structure.
      *
      * Normally returns null if successful.
      * Use getStatusLevel() and getStatusCode() to check internal error status.
      *
      * @param object $data data object to be inserted
      *
      * @api
      *
      * @return object null if node was created or internal error occurred;
      *                pointer to existing data object if node exists already
      */
    public function insert($data)
    {
        $this->resetStatus();
        if (!$this->checkData($data, __FUNCTION__)) {
            return null;
        }
        $this->_statistics['att_ins']++;
        $p = $this->_nodeProbe($data);
        if ($p == null || $p->data === $data) {
            $this->_statistics['ins']++;
            return null;
        } else {
            return $p->data;
        }
    }

    /**
      * Replaces a data object in the tree structure.
      *
      * Normally returns null if successful, or a pointer to the replaced
      * data object. Note: replaced data object is NOT destructed, calling
      * code should deal with the duplicated data object.
      * Use getStatusLevel() and getStatusCode() to check internal error status.
      *
      * @param object $data data object
      *
      * @return object null if node was created or internal error occurred;
      *                pointer to replaced data object if node was existing already
      *
      * @api
      */
    public function replace($data)
    {
        $this->resetStatus();
        if (!$this->checkData($data, __FUNCTION__)) {
            return null;
        }
        $this->_statistics['att_repl']++;
        $p = $this->_nodeProbe($data);
        if ($p == null || $p->data === $data) {
            $this->_statistics['repl']++;
            return null;
        } else {
            $this->_statistics['repl']++;
            $r = $p;
            $p->data = $data;
            return $r->data;
        }
    }

    /**
     * Finds a data object in the tree structure.
     *
     * @param object $data data object to be searched
     * @param int    $mode match mode - by default exact match
     *                     {RBPPAVL_FIND_EXACT_MATCH|RBPPAVL_FIND_PREV_MATCH|RBPPAVL_FIND_NEXT_MATCH}
     *
     * @return object null if node was not found or internal error occurred;
     *                pointer to data object if node found
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
        if (is_null($this->root)) {
            $this->setStatus(102);
            return null;
        }
        $y = $this->root;
        $q = null;
        $dir = null;
        $p = $this->nodeFind($this, $data, $y, $q, $dir, $mode);
        return $p ? $p->data : null;
    }

    /**
      * Deletes a data object from the tree structure.
      *
      * Normally returns a pointer to the data object deleted from the structure,
      * or null if the data object was not found.
      * Use getStatusLevel() and getStatusCode() to check internal error status.
      *
      * @param object $data data object to be deleted
      *
      * @return object null if node was not found or internal error occurred;
      *                pointer to existing data object if node deleted
      *
      * @api
      */
    public function delete($data)
    {
        $this->resetStatus();
        if (!$this->checkData($data, __FUNCTION__)) {
            return null;
        }
        if ($this->root == null) {
            // Empty tree
            $this->setStatus(102);
            return null;
        }

        $this->_statistics['att_del']++;
        //  <Step 1: Find rbppavl node to delete>
        $y = $this->root;
        $q = null;
        $dir = null;
        $p = $this->nodeFind($this, $data, $y, $q, $dir, RBPPAVL_FIND_EXACT_MATCH);
        if (!$p) {
            return null;
        }
        $data = $p->data;

        $nodeType = null;
        // <Step 2: Delete node from rbppavl tree>
        //$q = $p->parent;
        if (!$q) {
            // debug diagnostic - root node
            if ($this->debugMode) {
                $nodeType = ' ' . $this->txt('root');
            }
            $isRoot = true;
        } else {
            $isRoot = false;
        }
        if ($p->link[0] == null) {
            // no left child
            if ($p->link[1] == null) {
                // debug diagnostic - leaf
                if ($this->debugMode) {
                    $nodeType .= ' ' . $this->txt('leaf');
                }
                $w = null;
                // leaf
                if ($q) {
                    $q->link[$dir] = null;
                }
            } else {
                // debug diagnostic - p with no left subtree
                if ($this->debugMode) {
                    $nodeType .= ' ' . $this->txt('p-noleft');
                }
                $w = $p->link[1];
                if ($q) {
                    $q->link[$dir] = $p->link[1];
                    $q->link[$dir]->parent = $p->parent;
                }
            }
        } else {
            if ($p->link[1] == null) {
                // debug diagnostic - p with no right subtree
                if ($this->debugMode) {
                    $nodeType .= ' ' . $this->txt('p-noright');
                }
                $w = $p->link[0];
                if ($q) {
                    $q->link[$dir] = $p->link[0];
                    $q->link[$dir]->parent = $p->parent;
                }
            } else {
                // debug diagnostic - internal node
                if ($this->debugMode) {
                    $nodeType .= ' ' . $this->txt('internal');
                }
                $r = $p->link[1];
                if ($r->link[0] == null) {
                    // debug diagnostic - r with no left subtree
                    if ($this->debugMode) {
                        $nodeType .= ' ' . $this->txt('r-noleft', array('%node' => $this->cbc->dump($r->data),));
                    }
                    $r->link[0] = $p->link[0];
                    $w = $r;
                    if ($q) {
                        $q->link[$dir] = $r;
                    }
                    $r->parent = $p->parent;
                    if ($r->link[0]) {
                        $r->link[0]->parent = $r;
                    }
                    $r->height = $p->height;
                    $q = $r;
                    $dir = 1;
                } else {
                    // debug diagnostic - r with left subtree
                    if ($this->debugMode) {
                        $nodeType .= ' ' . $this->txt('r-left', array('%node' => $this->cbc->dump($r->data),));
                    }
                    $s = $r->link[0];
                    while ($s->link[0] != null) {
                        $s = $s->link[0];
                    }
                    $r = $s->parent;
                    $r->link[0] = $s->link[1];
                    $s->link[0] = $p->link[0];
                    $s->link[1] = $p->link[1];
                    $w = $s;
                    if ($q) {
                        $q->link[$dir] = $s;
                    }
                    if ($s->link[1]) {
                        $s->link[1]->parent = $s;
                    }
                    $s->link[0]->parent = $s;
                    $s->parent = $p->parent;
                    if ($r->link[0]) {
                        $r->link[0]->parent = $r;
                    }
                    $s->height = $p->height;
                    $q = $r;
                    $dir = 0;
                }
            }
        }
        if ($isRoot) {
            $this->root = $w;
            if ($this->root) {
                $this->root->parent = null;
            }
        }

        $this->_count--;
        $this->_statistics['del']++;
        // debug diagnostic    - deleted
        if ($this->debugMode) {
            $this->setStatus(
                10, array('%nodeType'     => $nodeType,
                          '%node'         => $this->cbc->dump($p->data),
                          '%replaceBy'    => ($w ? "'" . $this->cbc->dump($w->data) . "'" : $this->txt('none')),
                          '%count'        => $this->_count,)
            );
        }
        unset($p);

        // <Steps 3 and 4: Update balance factors and rebalance after
        // rbppavl deletion>
        while ($q !== null) {
            $y = $q;
            $q = $y->parent;
            $h = $y->height;
            if (abs($y->balance()) > $this->balanceFactor) {
                $this->_rotationRebalance($y);
                if ($h == $y->height) {
                    break;
                }
            } else {
                $y->heightReset();
                if ($h == $y->height) {
                    // debug diagnostic    - self balancing
                    $this->_statistics['self']++;
                    if ($this->debugMode) {
                        $this->setStatus(
                            6, array('%node'         => $this->cbc->dump($y->data),
                                     '%balance'      => $y->balance(),)
                        );
                    }
                    break;
                } else {
                    // debug diagnostic    - decrease height
                    if ($this->debugMode) {
                        $this->setStatus(
                            11, array('%node'         => $this->cbc->dump($y->data),
                                      '%height'       => $y->height,
                                      '%balance'      => $y->balance(),)
                        );
                    }
                }
            }
            if ($q) {
                $dir = ($q->link[0] !== $y) ? 1 : 0;
            } else {
                $dir = 0;
            }
        }
        return $data;
    }

    /**
     * Return the number of nodes in the tree.
     *
     * @api
     *
     * @return int number of nodes in the tree
     */
    public function getCount()
    {
        $this->resetStatus();
        return $this->_count;
    }

    /**
      * Inserts a node in the tree and returns a pointer to the node inserted.
      *
      * If a duplicate data object is found in the tree, returns a pointer to the duplicate node
      * without inserting the new data object.
      *
      * @param object $data the data object to be inserted
      *
      * @return object pointer to the node inserted
      */
    private function _nodeProbe($data)
    {
        // <Step 1: Search node tree for insertion point>
        $y = $this->root;
        $q = null;
        $dir = null;
        $p = $this->nodeFind($this, $data, $y, $q, $dir, RBPPAVL_FIND_EXACT_MATCH);
        if ($p) {
            // debug - node exists already
            if ($this->debugMode) {
                $this->setStatus(2, array('%node'   => $this->cbc->dump($data), ));
            }
            return $p;
        }

        //  <Step 2: Insert node>
        // checks for enough memory (if memory check was enabled)
        if ($this->memoryLimit) {
            if (memory_get_usage() >= $this->memoryLimit - $this->memoryThreshold) {
                $this->setStatus(103, array('%node'   => $this->cbc->dump($data), ));
                return null;
            }
        }
        $n = new RbppavlNode($this, $data, $q);
        // increase tree nodes counter
        $this->_count++;
        // set parent's link pointer to new node if parent exists,
        // else set root (first node inserted in tree)
        if ($q) {
            $q->link[$dir] = $n;
            // debug diagnostic    - inserted
            if ($this->debugMode) {
                $this->setStatus(
                    4, array('%node'         => $this->cbc->dump($n->data),
                             '%direction'    => ($dir == 0 ? $this->txt('left') : $this->txt('right')),
                             '%parent'       => $this->cbc->dump($q->data),
                             '%count'        => $this->_count,)
                );
            }
        } else {
            $this->root = $n;
            // debug diagnostic    - inserted root
            if ($this->debugMode) {
                $this->setStatus(
                    3, array('%node'         => $this->cbc->dump($n->data),
                             '%count'        => $this->_count,)
                );
            }
            return $n;
        }

        // <Step 3: Update tree heights>
        // Update balance factors after node insertion, traversing the parents'
        // chain upwards till the lowest node needing rebalance.
        for ($p = $n; $p !== $y; $p = $q) {
            $q = $p->parent;
            $dir = ($q->link[0] === $p) ? 0 : 1;
            if (   ($dir == 0 and $q->balance() >= 0)
                or ($dir == 1 and $q->balance() <= 0)
            ) {
                $this->_statistics['self']++;
                // debug diagnostic    - self balancing
                if ($this->debugMode) {
                    $this->setStatus(
                        6, array('%node'       => $this->cbc->dump($q->data),
                                 '%balance'    => $q->balance(),)
                    );
                }
                return $n;
            } else {
                $q->height++;
                // debug diagnostic    - height increase
                if ($this->debugMode) {
                    $this->setStatus(
                        5, array('%node'         => $this->cbc->dump($q->data),
                                 '%height'       => $q->height,
                                 '%balance'      => $q->balance(),)
                    );
                }
            }
        }

        //  <Step 4: Rebalancing through rotation if balancing factor exceeded>
        if (abs($y->balance()) > $this->balanceFactor) {
            $this->_rotationRebalance($y);
        }

        return $n;
    }

    /**
      * Performs rotation on the node to restore AVL compliance.
      *
      * @param object $y node to be rotated
      *
      * @return null
      */
    private function _rotationRebalance($y)
    {
        if ($y->balance() < 0) {
            $x = $y->link[0];
            if ($x->balance() <= 0) {                    // LL rotation
                // debug diagnostic    - rotation
                $this->_statistics['ll']++;
                if ($this->debugMode) {
                    $this->setStatus(
                        7, array('%node'         => $this->cbc->dump($y->data),
                                 '%rotationType' => 'LL',
                                 '%balance'      => $y->balance(),)
                    );
                }
                // rotation
                $w = $x;
                $y->link[0] = $x->link[1];
                $x->link[1] = $y;
                // reset pointers to parents
                $x->parent = $y->parent;
                $y->parent = $x;
                if ($y->link[0] != null) {
                    $y->link[0]->parent = $y;
                }
                // reset heights
                $y->heightReset();
                $x->heightReset();
            } else {                                        // LR rotation
                // debug diagnostic    - rotation
                $this->_statistics['lr']++;
                if ($this->debugMode) {
                    $this->setStatus(
                        7, array('%node'         => $this->cbc->dump($y->data),
                                 '%rotationType' => 'LR',
                                 '%balance'      => $y->balance(),)
                    );
                }
                // rotation
                $w = $x->link[1];
                $x->link[1] = $w->link[0];
                $w->link[0] = $x;
                $y->link[0] = $w->link[1];
                $w->link[1] = $y;
                // reset pointers to parents
                $w->parent = $y->parent;
                $x->parent = $y->parent = $w;
                if ($x->link[1] != null) {
                    $x->link[1]->parent = $x;
                }
                if ($y->link[0] != null) {
                    $y->link[0]->parent = $y;
                }
                // reset heights
                $y->heightReset();
                $x->heightReset();
                $w->heightReset();
            }
        } else {       
            $x = $y->link[1];
            if ($x->balance() >= 0) {                    // RR rotation
                // debug diagnostic    - rotation
                $this->_statistics['rr']++;
                if ($this->debugMode) {
                    $this->setStatus(
                        7, array('%node'         => $this->cbc->dump($y->data),
                                 '%rotationType' => 'RR',
                                 '%balance'      => $y->balance(),)
                    );
                }
                // rotation
                $w = $x;
                $y->link[1] = $x->link[0];
                $x->link[0] = $y;
                // reset pointers to parents
                $x->parent = $y->parent;
                $y->parent = $x;
                if ($y->link[1] != null) {
                    $y->link[1]->parent = $y;
                }
                // reset heights
                $y->heightReset();
                $x->heightReset();
            } else {                                    // RL rotation
                // debug diagnostic    - rotation
                $this->_statistics['rl']++;
                if ($this->debugMode) {
                    $this->setStatus(
                        7, array('%node'         => $this->cbc->dump($y->data),
                                 '%rotationType' => 'RL',
                                 '%balance'      => $y->balance(),)
                    );
                }
                // rotation
                $w = $x->link[0];
                $x->link[0] = $w->link[1];
                $w->link[1] = $x;
                $y->link[1] = $w->link[0];
                $w->link[0] = $y;
                // reset pointers to parents
                $w->parent = $y->parent;
                $x->parent = $y->parent = $w;
                if ($x->link[0] != null) {
                    $x->link[0]->parent = $x;
                }
                if ($y->link[1] != null) {
                    $y->link[1]->parent = $y;
                }
                // reset heights
                $y->heightReset();
                $x->heightReset();
                $w->heightReset();
            }
        }
        if ($w->parent) {
            $w->parent->link[($y !== $w->parent->link[0]) ? 1 : 0] = $w;
        } else {
            $this->root = $w;
        }
    }

    /**
     * Validates tree's AVL compliance.
     *
     * This method should be used only for debugging purposes.
     *
     * Recursively checks all nodes in tree to see if nodes' height property and 
     * balance factor are computed correctly.
     *
     * @param boolean $setStatusOnSuccess if true and validation is successful, internal
     *                                    status is updated and a diagnostic message broadcast
     *
     * @return object the data object of the first failing node or null if successful
     *
     * @api
     */
    public function debugValidate($setStatusOnSuccess = false)
    {
        $this->resetStatus();
        if ($this->root == null) {
            // Empty tree
            $this->setStatus(102);
            return null;
        }
        $status = 0;
        $failingNode = $this->root->debugNodeValidate($this->balanceFactor, $status);
        if ($failingNode) {
            // validation failure
            $this->setStatus(
                1001, array('%node'          => $this->cbc->dump($failingNode->data),
                            '%failureType'   => ($status == RBPPAVL_VALIDATION_HEIGHT_FAILURE ?
                                                $this->txt('height') :
                                                $this->txt('balance')),
                            '%height'        => $failingNode->height,
                            '%balance'       => $failingNode->balance(),)
            );
            return $failingNode->data;
        } else {
            // validation ok
            if ($setStatusOnSuccess) {
                $this->setStatus(1000, array('%count' => $this->_count,));
            }
            return null;
        }
    }

    /**
     * Returns an array of nodes by level and left-to-right order.
     *
     * This method should be used only for debugging purposes.
     *
     * Basically, performs a level-order traversal of the tree, associating each node
     * to its level and position within the level, and build an array with associated
     * keys.
     *
     * @param int $maxLevel maximum number of levels to be returned
     *
     * @return array an array in the format $arr[$lev][$pos] where $lev is the level
     *               of the node in the tree, and $pos the position of the node
     *               in the level
     *
     * @api
     */
    public function debugLevelOrderToArray($maxLevel = 5)
    {
        $arr = array();
        $this->_debugNodeToArray($this->root, 0, "0", $maxLevel, $arr);
        return $arr;
    }

    /**
     * Inserts a node in a level-order array.
     *
     * This method should be used only for debugging purposes.
     *
     * This method is called by debugLevelOrderToArray(). It works recursively.
     *
     * @param object $node     the node to be inserted in the array
     * @param int    $level    the level at which the node is positioned
     * @param string $pos      the node position in the level in binary notation
     * @param int    $maxLevel maximum level to recurse down to
     * @param array  &$arr     the array to be filled 
     *
     * @return null
     */
    private function _debugNodeToArray($node, $level, $pos, $maxLevel, &$arr)
    {
        if ($node == null or $level > $maxLevel) {
            return;
        }
        $posDec = bindec($pos);
        $xa = $node->data;
        $bal = $node->balance();
        $h = $node->height;
        $entry = array( $node->data,
                        $node->height,
                        $node->balance() );
        $arr[$level][$posDec] = $entry;
        if ($node->link[0] != null or $node->link[1] != null) {
            $pos .= "0";
            $this->_debugNodeToArray($node->link[0], $level + 1, $pos, $maxLevel, $arr);
            if ($node->link[1] != null) {
                $pos = substr($pos, 0, strlen($pos) - 1);
                $pos .= "1";
                $this->_debugNodeToArray($node->link[1], $level + 1, $pos, $maxLevel, $arr);
            }
        }
    }

    /**
     * Gets internal tree statistics.
     *
     * Rbppavl maintains an internal set of statistics on tree operation performed in an 
     * associative array. The keys of the array are the following:
     * - 'balance_factor' tree's balance factor
     * - 'height'         root height
     * - 'count'          nodes in tree
     * - 'ins'            successful inserts
     * - 'ins'            successful inserts
     * - 'att_ins'        attempted inserts
     * - 'rep'            successful replaces
     * - 'att_rep'        attempted replaces
     * - 'del'            successful deletes
     * - 'att_del'        attempted deletes
     * - 'self'           number of self-balances
     * - 'll'             number of LL rotations
     * - 'lr'             number of LR rotations
     * - 'rr'             number of RR rotations
     * - 'rl'             number of RL rotations
     *
     * @param string  $stat      the statistic key to return, or null to return the whole array of statistics
     * @param boolean $setStatus if true, internal status is updated and diagnostic message is broadcast
     *
     * @return int|array value of the statistic key requested, or the whole array of statistics
     *                   if no statistic key specified
     *
     * @api
     */
    public function getStatistics($stat = null, $setStatus = false)
    {
        $this->_statistics['balance_factor'] =  $this->balanceFactor;
        $this->_statistics['height'] = ($this->root ? $this->root->height : -1);
        $this->_statistics['count'] = $this->_count;
        if ($setStatus) {
            // diagnose statistics
            $this->setStatus(
                1002, array('%balance'       => $this->_statistics['balance_factor'],
                            '%count'         => $this->_statistics['count'],
                            '%height'        => $this->_statistics['height'],)
            );
            $this->setStatus(
                1003, array('%ins'           => $this->_statistics['ins'],
                            '%att_ins'       => $this->_statistics['att_ins'],
                            '%repl'          => $this->_statistics['repl'],
                            '%att_repl'      => $this->_statistics['att_repl'],
                            '%del'           => $this->_statistics['del'],
                            '%att_del'       => $this->_statistics['att_del'],)
            );
            $this->setStatus(
                1004, array('%self'          => $this->_statistics['self'],
                            '%rotations'     => $this->_statistics['ll'] +
                                                $this->_statistics['lr'] +
                                                $this->_statistics['rr'] +
                                                $this->_statistics['rl'],
                            '%ll'            => $this->_statistics['ll'],
                            '%lr'            => $this->_statistics['lr'],
                            '%rr'            => $this->_statistics['rr'],
                            '%rl'            => $this->_statistics['rl'],)
            );
        }
        if (!$stat) {
            return $this->_statistics;
        } else {
            return (isset($this->_statistics[$stat]) ? $this->_statistics[$stat] : null);
        }
    }
}