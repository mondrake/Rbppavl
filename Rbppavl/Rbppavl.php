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
 * Rbppavl version number
 */
define('RBPPAVL_VERSION_NUMBER', '0.1.0');

/**
 * Rbppavl version state
 */
define('RBPPAVL_VERSION_STATE', 'beta'); 

/**
 * @defgroup severity_levels Status and diagnostic severity levels
 * @{
 * Status and diagnostic levels as defined in RFC 3164.
 * 
 * @see http://www.faqs.org/rfcs/rfc3164.html
 */

 /**
 * 'Debug' status level or diagnostic
 */
define('RBPPAVL_DEBUG',            7);

/**
 * 'Information' status level or diagnostic
 */
define('RBPPAVL_INFO',             6);

/**
 * 'Notice' status level or diagnostic
 */
define('RBPPAVL_NOTICE',           5);

/**
 * 'Warning' status level or diagnostic - partial failure not preventing further processing
 */
define('RBPPAVL_WARNING',          4);

/**
 * 'Error' status level or diagnostic - failure preventing further processing
 */
define('RBPPAVL_ERROR',            3);

/**
 * @} End of "defgroup severity_levels".
 */

/**
 * Identifier for textual diagnostic strings
 */
define('RBPPAVL_TEXT',             -1);

/**
 * AVL Tree validation - success
 */
define('RBPPAVL_VALIDATION_OK',              0);

/**
 * AVL Tree validation - failure - node's stored tree height not
 * consistent with computed height
 */
define('RBPPAVL_VALIDATION_HEIGHT_FAILURE',  1);

/**
 * AVL Tree validation - failure - actual node's balance factor
 * exceeds limit
 */
define('RBPPAVL_VALIDATION_BALANCE_FAILURE', 2);

require_once 'RbppavlNode.php';
require_once 'RbppavlCommon.php';
require_once 'RbppavlTree.php';
require_once 'RbppavlTraverser.php';
require_once 'RbppavlCbInterface.php';

/**
 * Baseline Rbppavl class.
 *
 * Rbppavl root abstract class. This is empty to set a common parent class for the entire package.
 *
 * @category Structures
 * @package  Rbppavl
 * @author   mondrake <mondrake@mondrake.org>
 * @license  http://www.gnu.org/licenses/gpl.html GNU GPLv3
 * @link     http://github.com/mondrake/Rbppavl
 * @access   private
 */
abstract class Rbppavl
{
}