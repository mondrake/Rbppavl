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
 * Rbppavl callback class interface definition.
 *
 * Defines callback methods needed by instances of RbppavlTree 
 * and RbppavlTraverser. 
 *
 * @category Structures
 * @package  Rbppavl
 * @author   mondrake <mondrake@mondrake.org>
 * @license  http://www.gnu.org/licenses/gpl.html GNU GPLv3
 * @link     http://github.com/mondrake/Rbppavl
 */
interface RbppavlCbInterface
{
    /**
     * Compares two data objects.
     *
     * @param object $a first data object
     * @param object $b second data object
     *
     * @return int 0   if $a == $b
     *             -1  if $a < $b
     *             1   if $a > $b
     *
     * @api
     */
    public function compare($a, $b);
    
    /**
     * Returns a custom formatting of a data object.
     *
     * @param object $data data object
     *
     * @return mixed customised format of the data object's content.
     *
     * @api
     */
    public function dump($data);

    /**
     * Handles a request to log a diagnostic message.
     *
     * @param int    $severity  {RBPPAVL_DEBUG|RBPPAVL_INFO|RBPPAVL_NOTICE|RBPPAVL_WARNING|RBPPAVL_ERROR}
     * @param int    $id        id of the diagnostic message
     * @param string $text      unqualified text of the diagnostic message
     * @param array  $params    parameters to qualify the message
     * @param string $qText     fully qualified text of the diagnostic message
     * @param string $className name of the calling class
     *
     * @return null
     *
     * @api
     */
    public function diagnosticMessage($severity, $id, $text, $params, $qText, $className = null);

    /**
     * Handles an error condition.
     *
     * @param int    $id        id of the diagnostic message
     * @param string $text      unqualified text of the diagnostic message
     * @param array  $params    parameters to qualify the message
     * @param string $qText     fully qualified text of the diagnostic message
     * @param string $className name of the calling class
     *
     * @return null
     *
     * @api
     */
    public function errorHandler($id, $text, $params, $qText, $className = null);
}