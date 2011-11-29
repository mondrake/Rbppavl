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
 * Common Rbppavl class.
 *
 * Abstract class introducing properties common to RbppavlTree and
 * RbppavlTraverser.
 *
 * @category Structures
 * @package  Rbppavl
 * @author   mondrake <mondrake@mondrake.org>
 * @license  http://www.gnu.org/licenses/gpl.html GNU GPLv3
 * @link     http://github.com/mondrake/Rbppavl
 * @internal
 */
abstract class RbppavlCommon extends Rbppavl
{
    /**
     * Callback interface instance
     *
     * @type RbppavlCbInterface 
     */
    protected $cbc = null;

    /**
     * Debug mode - if true, tree management operation are verbosely diagnosed
     *
     * @type boolean 
     */
    protected $debugMode = false;

    /**
     * If different from 0, insertions of new nodes are preliminary checked 
     * vs remaining memory available
     *
     * @type integer
     */
    protected $memoryLimit = 0;

    /**
     * In combination with $memoryLimit, defines the minimum memory to be kept
     * free during tree insertion operations
     *
     * @type integer
     */
    protected $memoryThreshold = 0;

    /**
     * Internal status of tree management classes (RbppavlTree, RbppavlTraverser)
     *
     * Array keys:
     * - 'level'         status level complying to RFC 3164 
     * - 'code'          status code as per _message()
     * - 'messageParams' array of parameters to qualify the message text
     *
     * @type array 
     */
    protected $status = array('level'         => RBPPAVL_NOTICE,
                              'code'          => 100,
                              'messageParams' => null);

                              
    /**
     * Handles calls to inaccessible methods
     *
     * @param string $name the method invoked
     * @param array  $args the array representing the arguments passed to the method
     *
     * @return null
     *
     * @internal
     */
    public function __call($name, $args)
    {
        // undefined method called
        $this->setStatus(104, array('%method' => $name,));
    }

    /**
     * Handles reads to inaccessible properties
     *
     * @param string $name the property invoked
     *
     * @return null
     *
     * @internal
     */
    public function __get($name)
    {
        // inaccessible property 
        $this->setStatus(105, array('%property' => $name,));
    }

    /**
     * Handles writes to inaccessible properties
     *
     * @param string $name  the property invoked
     * @param array  $value the value attempted to be written
     *
     * @return null
     *
     * @internal
     */
    public function __set($name, $value)
    {
        // inaccessible property 
        $this->setStatus(105, array('%property' => $name,));
    }

    /**
     * Gets Rbppavl version and state.
     *
     * @param boolean $setStatus if true, internal status is updated and 
     *                           a diagnostic message broadcast
     *
     * @return array an array with [0] = Rbppavl version number and [1] = Rbppavl version state
     *
     * @api
     */
    public function getVersion($setStatus = false)
    {
        $this->resetStatus();
        if ($setStatus) {
            // Rbppavl version and state
            $this->setStatus(
                101, array('%version'       => RBPPAVL_VERSION_NUMBER . RBPPAVL_VERSION_STATE,)
            );
        }
        return array(RBPPAVL_VERSION_NUMBER, RBPPAVL_VERSION_STATE);
    }

    /**
      * Finds a node in the tree.
      *
      * @param RbppavlTree $tree the tree to be searched
      * @param object      $data the data object to be found
      * @param object      &$y   top node to start searching from
      * @param object      &$q   parent node of the found node
      * @param int         &$dir last direction descended
      * @param int         $mode match mode {RBPPAVL_FIND_EXACT_MATCH|RBPPAVL_FIND_PREV_MATCH|RBPPAVL_FIND_NEXT_MATCH}
      *
      * @return object pointer to the found node or null if not found
      */
    protected function nodeFind($tree, $data, &$y, &$q, &$dir, $mode)
    {
        for ($q = null, $p = $y; $p; $q = $p, $p = $p->link[$dir]) {
            $cmp = $tree->cbc->compare($data, $p->data);
            // returns node if data is in tree
            if ($cmp == 0) {
                // debug diagnostic - found
                if ($this->debugMode) {
                    $this->setStatus(9, array('%node' => $this->cbc->dump($p->data),));
                }
                return $p;
            }
            // sets direction of next iteration
            $dir = $cmp > 0 ? 1 : 0;
            // pushes down the top node pointer to the lowest node to be
            // rebalanced while nodes are not balanced
            if (abs($p->balance()) == $tree->balanceFactor) {
                $y = $p;
            }
        }
        switch ($mode) {
        case RBPPAVL_FIND_EXACT_MATCH:
            // debug diagnostic - not found
            if ($this->debugMode) {
                $this->setStatus(8, array('%node' => $this->cbc->dump($data),));
            }
            return null;
        case RBPPAVL_FIND_PREV_MATCH:
            if ($dir == 0) {
                $p = $this->nodePrev($tree, $q);
            } else {
                $p = $q;
            }
            if ($this->debugMode) {
                if ($p) {
                    // debug diagnostic - prev match found
                    $this->setStatus(
                        13, array('%node' => $this->cbc->dump($data),
                                  '%prev' => $this->cbc->dump($p->data),)
                    );
                } else {
                    // debug diagnostic - prev match not found
                    $this->setStatus(14, array('%node' => $this->cbc->dump($data),));
                }
            }
            return $p;
        case RBPPAVL_FIND_NEXT_MATCH:
            if ($dir == 1) {
                $p = $this->nodeNext($tree, $q);
            } else {
                $p = $q;
            }
            if ($this->debugMode) {
                if ($p) {
                    // debug diagnostic - next match found
                    $this->setStatus(
                        15, array('%node' => $this->cbc->dump($data),
                                  '%prev' => $this->cbc->dump($p->data),)
                    );
                } else {
                    // debug diagnostic - next match not found
                    $this->setStatus(16, array('%node' => $this->cbc->dump($data),));
                }
            }
            return $p;
        }    
    }

    /**
     * Returns the previous node in in-order sequence.
     *
     * If there are no more nodes returns null.
     *
     * @param RbppavlTree $tree the tree to be searched
     * @param object      $node node to start searching from
     *
     * @return object previous node in in-order sequence or null if leftmost node input
     */
    protected function nodePrev($tree, $node)
    {
        if ($node == null) {
            return $this->nodeLast($tree);
        } elseif ($node->link[0] == null) {
            for ($p = $node, $q = $p->parent; ; $p = $q, $q = $q->parent) {
                if ($q == null or $p === $q->link[1]) {
                    return $q;
                }
            }
        } else {
            $q = $node->link[0];
            while ($q->link[1] != null) {
                $q = $q->link[1];
            }
            return $q;
        }
    }

    /**
     * Returns the next node in in-order sequence.
     *
     * If there are no more nodes returns null.
     *
     * @param RbppavlTree $tree the tree to be searched
     * @param object      $node node to start searching from
     *
     * @return object next node in in-order sequence or null if rightmost node input
     */
    protected function nodeNext($tree, $node)
    {
        if ($node == null) {
            return $this->nodeFirst($tree);
        } elseif ($node->link[1] == null) {
            for ($p = $node, $q = $p->parent; ; $p = $q, $q = $q->parent) {
                if ($q == null or $p === $q->link[0]) {
                    return $q;
                }
            }
        } else {
            $q = $node->link[1];
            while ($q->link[0] != null) {
                $q = $q->link[0];
            }
            return $q;
        }
    }

    /**
      * Returns the node with the least value (leftmost).
      *
      * @param RbppavlTree $tree the tree to be searched
      *
      * @return object the leftmost node in the tree or null if tree is empty.
      */
    protected function nodeFirst($tree)
    {
        // empty tree
        if (is_null($tree) or is_null($tree->root)) {
            $this->setStatus(102);
            return null;
        }
        $p = $tree->root;
        while ($p->link[0] != null) {
            $p = $p->link[0];
        }
        return $p;
    }

    /**
      * Returns the node with the greatest value (rightmost).
      *
      * @param RbppavlTree $tree the tree to be searched
      *
      * @return object the rightmost node in the tree or null if tree is empty.
      */
    protected function nodeLast($tree)
    {
        // empty tree
        if (is_null($tree) or is_null($tree->root)) {
            $this->setStatus(102);
            return null;
        }
        $p = $tree->root;
        while ($p->link[1] != null) {
            $p = $p->link[1];
        }
        return $p;
    }

    /**
     * Checks if the data object passed to a method is valid.
     *
     * @param object $data   the data object
     * @param string $method the class method calling the function
     *
     * @return boolean true if $data is a valid object, false elsewhere
     */
    protected function checkData($data, $method)
    {
        if (empty($data) or !is_object($data)) {
            // incorrect data input
            $this->setStatus(107, array('%method' => $method,));
            return false;
        } else {
            // debug log method called
            if ($this->debugMode) {
                $this->setStatus(
                    1, array('%method' => $method,
                             '%node'   => $this->cbc->dump($data), )
                );
            }
            return true;
        }
    }

    /**
     * Sets internal status
     *
     * Internal status is updated with the severity level as identified by the message id.
     * Diagnostic message is passed over to the callback interface.
     * In case of RBPPAVL_ERROR, error handling is passed over to the callback interface; 
     * if callback interface is not instantiated yet, a standard exception is thrown.
     *
     * @param int     $id     the id of the diagnostic message
     * @param array   $params the parameters to qualify the message
     * @param boolean $reset  if true, it is a reset call
     *
     * @return null
     */
    protected function setStatus($id, $params = null, $reset = false)
    {
        // get severity and text
        list($severity, $text) = $this->_message($id);
        // update internal status
        if ($severity <= $this->status['level'] or $reset) {
            $this->status['level']         = $severity;
            $this->status['code']          = $id;
            $this->status['messageParams'] = $params;
        }
        // call callback interface with diagnostic message and hands over to error
        // handler in case of error level RBPPAVL_ERROR; if callback not set yet then
        // throws an exception
        $className = get_class($this);
        $params['%class'] = $className;
        $qText = $this->txt($id, $params);
        if (isset($this->cbc)) {
            if (!$reset) {
                $this->cbc->diagnosticMessage($severity, $id, $text, $params, $qText, $className);
            }
            if ($severity <= RBPPAVL_ERROR) {
                $this->cbc->errorHandler($id, $text, $params, $qText, $className);
            }
        } else {
            if ($severity <= RBPPAVL_ERROR) {
                throw new Exception($qText, $id);
            }
        }
        return null;
    }

    /**
     * Resets internal status.
     *
     * @return null
     */
    protected function resetStatus()
    {
        $this->setStatus(100, null, true);
        return null;
    }

    /**
     * Gets current status level.
     *
     * @return int Current status level
     *             {RBPPAVL_DEBUG|RBPPAVL_INFO|RBPPAVL_NOTICE|RBPPAVL_WARNING|RBPPAVL_ERROR}
     *
     * @api
     */
    public function getStatusLevel()
    {
        return $this->status['level'];
    }

    /**
     * Gets current status code.
     *
     * @return int Current status code
     *
     * @api
     */
    public function getStatusCode()
    {
        return $this->status['code'];
    }

    /**
     * Gets current status message.
     *
     * @return string Current status message
     *
     * @api
     */
    public function getStatusMessage()
    {
        return $this->txt($this->status['code'], $this->status['messageParams']);
    }

    /**
     * Gets an array with Rbppavl diagnostic messages.
     *
     * Each array item is itself an array, with first element being the severity
     * {RBPPAVL_DEBUG|RBPPAVL_INFO|RBPPAVL_NOTICE|RBPPAVL_WARNING|RBPPAVL_ERROR},
     * and the second the unqualified diagnostic text. 
     * A % sign precedes in the text a parameter identifier; getStatusMessage()
     * will replace at run-time the parameters with actual data to qualify the 
     * message.     
     *
     * @return array current Rbppavl diagnostic messages
     *
     * @api
     */
    public function getMessages()
    {
        return $this->_message();
    }

    /**
     * Sets Rbppavl diagnostic messages.
     *
     * Each array item of the input arrayis itself an array, with first 
     * element being the severity {RBPPAVL_DEBUG|RBPPAVL_INFO|RBPPAVL_NOTICE|RBPPAVL_WARNING|RBPPAVL_ERROR},
     * and the second the unqualified diagnostic text. 
     *
     * @param array $table Rbppavl diagnostic messages
     *
     * @return the new complete diagnostic messages map
     *
     * @api
     */
    public function setMessages($table)
    {
        return $this->_message($table);
    }

    /**
     * Converts a short for memory size into number of bytes.
     *
     * @param int|string $size a size number in bytes, or KBytes (nnnK), or MBytes (nnnM), or GBytes (nnnG)
     *
     * @return int number of bytes
     */
    protected function returnBytes($size)
    {
        switch (substr($size, -1)) {
        case 'K':
        case 'k':
            return (int)$size * 1024;
        case 'M':
        case 'm':
            return (int)$size * 1048576;
        case 'G':
        case 'g':
            return (int)$size * 1073741824;
        default:
            return $size;
        }
    }

    /**
     * Returns a fully qualified diagnostic message.
     *
     * @param int   $id     id of the diagnostic message
     * @param array $params parameters to qualify the message
     *
     * @return string fully qualified diagnostic text
     */
    protected function txt($id, $params = null)
    {
        list($severity, $text) = $this->_message($id);
        if ($params) {
            foreach ($params as $a => $b) {
                $text = str_replace($a, $b, $text);
            }
        }
        return $text;
    }

    /**
     * Manages Rbppavl diagnostic messages.
     *
     * @param mixed $id 1) diagnostic message code (int|string), or
     *                  2) null to get current diagnostic message map, or
     *                  3) array to merge a new diagnostic message map
     *
     * @return mixed 1) an array with diagnostic message severity and unqualified text,
     *                  or null if not found 
     *               2) the entire map
     *               3) the resulting merged array 
     */
    private function _message($id = null)
    {
        static $t;
        // initializes the message table
        if (!isset($t)) {
            $t = array(
                1         => array(RBPPAVL_DEBUG,   '%method \'%node\''),
                2         => array(RBPPAVL_DEBUG,   'node \'%node\' exists already'),
                3         => array(RBPPAVL_DEBUG,   'inserted *root* node \'%node\'; count: %count'),
                4         => array(RBPPAVL_DEBUG,   'inserted node \'%node\' %direction of node \'%parent\'; count: %count'),
                5         => array(RBPPAVL_DEBUG,   'height increase in node \'%node\'; new height: %height new balance: %balance'),
                6         => array(RBPPAVL_DEBUG,   'self-balancing in node \'%node\'; new balance: %balance'),
                7         => array(RBPPAVL_DEBUG,   '%rotationType rotation on node \'%node\' (balance: %balance)'),
                8         => array(RBPPAVL_DEBUG,   'node \'%node\' not found'),
                9         => array(RBPPAVL_DEBUG,   'node \'%node\' found'),
                10        => array(RBPPAVL_DEBUG,   'deleted node \'%node\',%nodeType; replacing node: %replaceBy; count: %count'),
                11        => array(RBPPAVL_DEBUG,   'height decrease in node \'%node\'; new height: %height new balance: %balance'),
                12        => array(RBPPAVL_DEBUG,   'Wiped %ctr nodes while destroying tree object'),
                13        => array(RBPPAVL_DEBUG,   'node \'%node\' not found, closest previous \'%prev\''),
                14        => array(RBPPAVL_DEBUG,   'node \'%node\' not found, no closest previous'),
                15        => array(RBPPAVL_DEBUG,   'node \'%node\' not found, closest next \'%prev\''),
                16        => array(RBPPAVL_DEBUG,   'node \'%node\' not found, no closest next'),
                100       => array(RBPPAVL_INFO,    'OK'),
                101       => array(RBPPAVL_INFO,    '%class - Version %version'),
                102       => array(RBPPAVL_WARNING, 'Empty tree.'),
                103       => array(RBPPAVL_WARNING, 'Not enough memory while inserting node \'%node\'.'),
                104       => array(RBPPAVL_ERROR,   'Undefined or inaccessible method %class::%method invoked'),
                105       => array(RBPPAVL_ERROR,   'Undefined or inaccessible property %class::%property invoked'),
                106       => array(RBPPAVL_ERROR,   'No callback class specified when instantiating %class'),
                107       => array(RBPPAVL_WARNING, 'Wrong or undefined data object passed to %class::%method. Only non-null objects accepted.'),
                1000      => array(RBPPAVL_NOTICE,  'Tree validation OK; nodes count: %count'),
                1001      => array(RBPPAVL_ERROR,   'Tree validation *failed* on node: \'%node\' (%failureType failure; height: %height balance: %balance)'),
                1002      => array(RBPPAVL_INFO,    'Tree statistics: Balance factor %balance; Node count: %count; Tree height: %height'),
                1003      => array(RBPPAVL_INFO,    'Tree statistics: Inserts: (%ins/%att_ins) Replaces: (%repl/%att_repl) Deletes: (%del/%att_del)'),
                1004      => array(RBPPAVL_INFO,    'Tree statistics: Self-balances: %self; Rotations: %rotations (RR: %rr, RL: %rl, LL: %ll, LR: %lr)'),

                'none'       => array(RBPPAVL_TEXT,   '*none*'),
                'right'      => array(RBPPAVL_TEXT,   'right'),
                'left'       => array(RBPPAVL_TEXT,   'left'),
                'root'       => array(RBPPAVL_TEXT,   '*root*'),
                'leaf'       => array(RBPPAVL_TEXT,   'leaf'),
                'height'     => array(RBPPAVL_TEXT,   'height'),
                'balance'    => array(RBPPAVL_TEXT,   'balance'),
                'p-noleft'   => array(RBPPAVL_TEXT,   'no left subtree'),
                'p-noright'  => array(RBPPAVL_TEXT,   'no right subtree'),
                'r-noleft'   => array(RBPPAVL_TEXT,   'no left child on right subtree \'%node\''),
                'r-left'     => array(RBPPAVL_TEXT,   'left child on right subtree \'%node\''),
                'internal'   => array(RBPPAVL_TEXT,   'internal,'),
            );
        }
        // if $id input is an array, replaces the default massage table with the input
        if (is_array($id)) {
            foreach ($id as $k => $e) {
                if (isset($t[$k])) {
                    $t[$k] = $e;
                }
            }
            return $t;
        }
        // if $id is null, returns the full message table
        if (is_null($id)) {
            return $t;
        }
        // returns the table element specified by $id
        return isset($t[$id]) ? $t[$id] : null;
    }
}