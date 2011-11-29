<link rel="stylesheet" href="style.css" type="text/css">
<?php
/**
 * PHP AVL binary tree
 *
 * A test script for Rbppavl package.
 *
 * PHP version 5
 *
 * @category Structures
 * @package  Rbppavl
 * @author   mondrake <mondrake@mondrake.org>
 * @license  http://www.gnu.org/licenses/gpl.html GNU GPLv3
 * @link     http://github.com/mondrake/Rbppavl
 */

require_once "Rbppavl/Rbppavl.php";

// play around with the variable values below to change behaviour of test sets
$testSets = array(
    // **** movie
    'movie' => array(
        'description'  => 'Shows in detail the tree balancing operations while inserting and deleting a limited set of nodes from a canonical AVL tree.',
        'testChangeDiagnosticMessages'  => false, 
        'testTraverser'                 => array( 
            'atModulo'         => false, 
            'atEndOfInserts'   => false, 
            'atEndOfDeletes'   => false, 
        ),
        'testDeletes'                   => true,
        'testSummary'                   => false,
        'checkMemory'                   => array( 
            'atStart'          => false, 
            'atModulo'         => false,
            'atBeginOfInserts' => false,
            'atEndOfInserts'   => false,
            'atBeginOfDeletes' => false,
            'atEndOfDeletes'   => false,
        ),
        'memoryThreshold'               => '5M',
        'howManyCycles'                 => 1,
        'howManyNodes'                  => 10,
        'nodesGeneration'               => 'random',
        'modulo'                        => 1,
        'fromBalanceFactor'             => 1,
        'toBalanceFactor'               => 1,
        'maxIntValueInNode'             => 200,
        'verbose'                       => true,
        'displayTree'                   => array( 
            'atModulo'         => true, 
            'atEndOfInserts'   => true, 
            'atEndOfDeletes'   => false, 
        ),
        'validateTree'                  => array( 
            'atModulo'         => true, 
            'atEndOfInserts'   => false, 
            'atEndOfDeletes'   => false, 
        ),
        'showStatistics'                => array( 
            'atEndOfInserts'   => true, 
            'atEndOfDeletes'   => true, 
        ),
        'displayHowManyLevels'          => 5,
    ),
    // **** forest
    'forest' => array(
        'description'  => 'Generates a large number of trees of increasing imbalance, with random node values, to get summary statistics on height and rotations.',
        'testChangeDiagnosticMessages'  => false, 
        'testTraverser'                 => array( 
            'atModulo'         => false, 
            'atEndOfInserts'   => false, 
            'atEndOfDeletes'   => false, 
        ),
        'testDeletes'                   => false,
        'testSummary'                   => true,
        'checkMemory'                   => array( 
            'atStart'          => false, 
            'atModulo'         => false,
            'atBeginOfInserts' => false,
            'atEndOfInserts'   => false,
            'atBeginOfDeletes' => false,
            'atEndOfDeletes'   => false,
        ),
        'memoryThreshold'               => '5M',
        'howManyCycles'                 => 100,
        'howManyNodes'                  => 200,
        'nodesGeneration'               => 'random list',
        'modulo'                        => 0,
        'fromBalanceFactor'             => 1,
        'toBalanceFactor'               => 10,
        'maxIntValueInNode'             => 10000000, 
        'verbose'                       => false,
        'displayTree'                   => array( 
            'atModulo'         => false, 
            'atEndOfInserts'   => false, 
            'atEndOfDeletes'   => false, 
        ),
        'validateTree'                  => array( 
            'atModulo'         => false, 
            'atEndOfInserts'   => false, 
            'atEndOfDeletes'   => false, 
        ),
        'showStatistics'                => array( 
            'atEndOfInserts'   => false, 
            'atEndOfDeletes'   => false, 
        ),
        'displayHowManyLevels'          => 4,
    ),
    // **** burp
    'burp' => array(
        'description'  => 'Fill memory with random value node insertions, up to the threshold defined by memoryThreshold, then randomly deletes all the nodes.',
        'testChangeDiagnosticMessages'  => false, 
        'testTraverser'                 => array( 
            'atModulo'         => false, 
            'atEndOfInserts'   => false, 
            'atEndOfDeletes'   => false, 
        ),
        'testDeletes'                   => true,
        'testSummary'                   => false,
        'checkMemory'                   => array( 
            'atStart'          => true, 
            'atModulo'         => true, 
            'atBeginOfInserts' => true, 
            'atEndOfInserts'   => true, 
            'atBeginOfDeletes' => true, 
            'atEndOfDeletes'   => true, 
        ),
        'memoryThreshold'               => '200k',
        'howManyCycles'                 => 1,
        'howManyNodes'                  => 200000,
        'nodesGeneration'               => 'random',
        'modulo'                        => 5000,
        'fromBalanceFactor'             => 1,
        'toBalanceFactor'               => 2,
        'maxIntValueInNode'             => 50000000,
        'verbose'                       => false,
        'displayTree'                   => array( 
            'atModulo'         => false, 
            'atEndOfInserts'   => false, 
            'atEndOfDeletes'   => false, 
        ),
        'validateTree'                  => array( 
            'atModulo'         => false, 
            'atEndOfInserts'   => false,  
            'atEndOfDeletes'   => false, 
        ),
        'showStatistics'                => array( 
            'atEndOfInserts'   => true, 
            'atEndOfDeletes'   => true, 
        ),
        'displayHowManyLevels'          => 5,
    ),
    // **** lorem ipsum
    'lorem ipsum' => array(
        'description'  => 'Always repeats operations on the same set of data, the "Lorem ipsum" standard - showcase for using strings instead of integers.',
        'testChangeDiagnosticMessages'  => false, 
        'testTraverser'                 => array( 
            'atModulo'         => false, 
            'atEndOfInserts'   => true, 
            'atEndOfDeletes'   => false, 
        ),
        'testDeletes'                   => true,
        'testSummary'                   => false,
        'checkMemory'                   => array( 
            'atStart'          => false, 
            'atModulo'         => false,
            'atBeginOfInserts' => false,
            'atEndOfInserts'   => false,
            'atBeginOfDeletes' => false,
            'atEndOfDeletes'   => false,
        ),
        'memoryThreshold'               => '5M',
        'howManyCycles'                 => 1,
        'howManyNodes'                  => 500,
        'nodesGeneration'               => 'list',
        'nodesList'                     => preg_split("/[\s,.]+/", 
'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod 
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, 
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 
Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu 
fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in 
culpa qui officia deserunt mollit anim id est laborum.', null, PREG_SPLIT_NO_EMPTY), 
        'modulo'                        => 0,
        'fromBalanceFactor'             => 1,
        'toBalanceFactor'               => 2,
        'verbose'                       => true,
        'displayTree'                   => array( 
            'atModulo'         => false, 
            'atEndOfInserts'   => true, 
            'atEndOfDeletes'   => false, 
        ),
        'validateTree'                  => array( 
            'atModulo'         => false, 
            'atEndOfInserts'   => true, 
            'atEndOfDeletes'   => false, 
        ),
        'showStatistics'                => array( 
            'atEndOfInserts'   => true, 
            'atEndOfDeletes'   => true, 
        ),
        'displayHowManyLevels'          => 6,
    ),
    // **** mirabilis res
    'mirabilis res' => array(
        'description'  => 'Same as "Lorem ipsum", with latinized (?) diagnostic. Showcase for localization of diagnostic messages.',
        'testChangeDiagnosticMessages'  => true, 
        'testTraverser'                 => array( 
            'atModulo'         => false, 
            'atEndOfInserts'   => true, 
            'atEndOfDeletes'   => false, 
        ),
        'testDeletes'                   => true,
        'testSummary'                   => false,
        'checkMemory'                   => array( 
            'atStart'          => false, 
            'atModulo'         => false,
            'atBeginOfInserts' => false,
            'atEndOfInserts'   => false,
            'atBeginOfDeletes' => false,
            'atEndOfDeletes'   => false,
        ),
        'memoryThreshold'               => '5M',
        'howManyCycles'                 => 1,
        'howManyNodes'                  => 500,
        'nodesGeneration'               => 'list',
        'nodesList'                     => preg_split("/[\s,.]+/", 
'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod 
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, 
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 
Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu 
fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in 
culpa qui officia deserunt mollit anim id est laborum.', null, PREG_SPLIT_NO_EMPTY), 
        'modulo'                        => 0,
        'fromBalanceFactor'             => 1,
        'toBalanceFactor'               => 2,
        'verbose'                       => true,
        'displayTree'                   => array( 
            'atModulo'         => false, 
            'atEndOfInserts'   => true, 
            'atEndOfDeletes'   => false, 
        ),
        'validateTree'                  => array( 
            'atModulo'         => false, 
            'atEndOfInserts'   => true, 
            'atEndOfDeletes'   => false, 
        ),
        'showStatistics'                => array( 
            'atEndOfInserts'   => true, 
            'atEndOfDeletes'   => true, 
        ),
        'displayHowManyLevels'          => 6,
    ),
);

// ------------------------------------------------------------------
// Test classes
// ------------------------------------------------------------------

class TestClass {
    public $q;
}

class TestSummary {
    public $q;
    public $qTrees = 0;
    public $totHeight = 0;
    public $totRotations = 0;
}

class TestCallback implements RbppavlCbInterface    {

    static private $tableSet;

    public function stopTable() {
        if (self::$tableSet) {
            print("</table>");    
            self::$tableSet = false;
        }
    }

    public function compare($a, $b)    {
        return ($a->q == $b->q) ? 0 : (($a->q < $b->q) ? -1 : 1);
    }

    public function dump($a)    {
        return $a->q;
    }

    public function diagnosticMessage($severity, $id, $text, $params, $qText, $className = null) {
		list($tUSec, $tSec) = explode(" ", microtime());
		$tSec = date('Y-m-d H:i:s', (float) $tSec);
		$tUSec = trim(strstr((float) $tUSec, '.'), '.');
		$ftm = $tSec . '.' . $tUSec;
        if (!self::$tableSet) {
            print("<table><tr><th>Class</th><th>Severity</th><th>Id</th><th>Time</th><th>Message</th></tr>");
            self::$tableSet = true;
        }
        switch ($severity) {
            case RBPPAVL_DEBUG;
                $sev = "debug";  
                print('<tr class="debug">');
                break;
            case RBPPAVL_INFO;
                $sev = "info";
                print('<tr class="info">');
                break;
            case RBPPAVL_NOTICE;
                $sev = "notice";
                print('<tr class="notice">');
                break;
            case RBPPAVL_WARNING;
                $sev = "warning";
                print('<tr class="warning">');
                break;
            case RBPPAVL_ERROR;
                $sev = "error";
                print('<tr class="error">');
                break;
            default:
                print("<tr>");
        }
        print("<td>$className</td><td align='center'>$sev</td><td align='center'>$id</td><td>$ftm</td><td>$qText</td></tr>");    
    }

    public function errorHandler($id, $text, $params, $qText, $className = null) {
        throw new exception($qText, $id);
    }
}

class TestCallbackSummary extends TestCallback    {
    public function dump($a)    {
        $avgHeight = ($a->qTrees ? $a->totHeight / $a->qTrees : "no trees");
        $avgRotations = ($a->qTrees ? $a->totRotations / $a->qTrees : "no trees");
        return "Balance factor: $a->q - Trees#: $a->qTrees; Avg height: $avgHeight; Avg rotations: $avgRotations";
    }
}

// ------------------------------------------------------------------
// Functions
// ------------------------------------------------------------------

function return_bytes ($size_str)
{
    switch (substr ($size_str, -1))
    {
        case 'M': case 'm': return (int)$size_str * 1048576;
        case 'K': case 'k': return (int)$size_str * 1024;
        case 'G': case 'g': return (int)$size_str * 1073741824;
        default: return $size_str;
    }
}

function test_traverser($cbc, $tree)    {
    $cbc->stopTable(); 
    print("<br/><b>Inorder traversal</b><br/>");
    $trav = new RbppavlTraverser($tree);
    $r = $trav->first();
    $ctr=0;
    while ($r != NULL)    {
        print("[$ctr] => " . $cbc->dump($r) . "<br/>");
        $r = $trav->next();
        $ctr++;
    }
    print("<br/>");
}

function display_tree_structure($cbc, $tree, $maxLev)    {
    $cbc->stopTable(); 
    print("<table>");

    $a = $tree->debugLevelOrderToArray($maxLev);

    for ($i = 0; $i <= $maxLev; $i++)    {
        print("<tr><td align='center'><b>L$i:</b></td>");
        for ($j = 0; $j < pow(2, $i); $j++)    {
            if (isset($a[$i][$j])) {
                $xx = $cbc->dump($a[$i][$j][0]) . '<br/>h:' . $a[$i][$j][1] . ' (' .  $a[$i][$j][2] . ')';
            } else {
                $xx = "*";
            }
            $span = pow(2, $maxLev - $i);
            print("<td colspan='$span' align='center'>$xx</td>");
        }
        print("</tr>");
    }
    print("</table>");

}        

// ------------------------------------------------------------------
// Main routine
// ------------------------------------------------------------------

// get the test to run from the URL 
$testList = array_keys($testSets);
if (isset($_GET['testSet'])) {
    $testToRun = $_GET['testSet'];
} else {
    $testToRun = $testList[0]; 
}
$testRun = $testSets[$testToRun];

// ********* HEADER *************

print ('<h2>Full Rbppavl tree test</h2>');
print ('Check Rbppavl documentation <a href="Rbppavl_doc">here</a>.<br/><br/>Tests available: ');
foreach ($testList as $testName) {
    $desc = $testSets[$testName]['description'];
    print("  <a href='full_test.php?testSet=$testName' title='$desc'>$testName</a>   |");
}
print("  edit full_test.php to change test parameters.<br/><br/>");

// test to run
print("This test: <b>$testToRun</b> - $testRun[description]<br/><br/>");

$cca = 1;
// test synopsis 
print("Test synopsis:<br/>");
if ($testRun['testChangeDiagnosticMessages']) {
    print("<b>$cca.</b> Changes Rbppavl standard diagnostic messages with a local map<br/>");
    $cca++;
}
print("<b>$cca.</b> Run $testRun[howManyCycles] cycle(s) of:<br/>");
$ccb = 1;
if ($testRun['nodesGeneration'] == 'random list') {
    print("<b>$cca.$ccb</b> generate a list of $testRun[howManyNodes] random integers in the range (0-$testRun[maxIntValueInNode])<br/>");
    $ccb++;
}
print("<b>$cca.$ccb</b> starting with a tree with balance factor = $testRun[fromBalanceFactor], ");
print("and ending with a tree with balance factor = $testRun[toBalanceFactor]:<br/>");
$ccc = 1;
switch($testRun['nodesGeneration']) {
case "random":
    print("<b>$cca.$ccb.$ccc</b> insert $testRun[howManyNodes] node(s) of random integer(s) in the range (0-$testRun[maxIntValueInNode]) in a tree<br/>");
    break;
case "random list":
    print("<b>$cca.$ccb.$ccc</b> insert the generated list of value(s) in a tree<br/>");
    break;
case "list":
    print("<b>$cca.$ccb.$ccc</b> insert a list of value(s) in a tree<br/>");
    break;
}
$ccc++;
if ($testRun['modulo']) {
    $comma = false;
    $x = "<b>$cca.$ccb.$ccc</b> at every $testRun[modulo] insert(s) ";
    if ($testRun['checkMemory']['atModulo']) {
        $x .= $comma ? ", " : null;
        $x .= "display consumed memory";
        $comma = true;
    }
    if ($testRun['validateTree']['atModulo']) {
        $x .= $comma ? ", " : null;
        $x .= "validate the tree";
        $comma = true;
    }
    if ($testRun['displayTree']['atModulo']) {
        $x .= $comma ? ", " : null;
        $x .= "print a representation of the tree";
        $comma = true;
    }
    if ($testRun['testTraverser']['atModulo']) {
        $x .= $comma ? ", " : null;
        $x .=  "print an inorder traversal";
        $comma = true;
    }
    print("$x<br/>");
    $ccc++;
}
if ($testRun['validateTree']['atEndOfInserts'] or
    $testRun['showStatistics']['atEndOfInserts'] or
    $testRun['checkMemory']['atEndOfInserts'] or
    $testRun['displayTree']['atEndOfInserts'] or 
    $testRun['testTraverser']['atEndOfInserts']) {
    $comma = false;
    $x = "<b>$cca.$ccb.$ccc</b> at end of inserts ";
    if ($testRun['validateTree']['atEndOfInserts']) {
        $x .= $comma ? ", " : null;
        $x .= "validate the tree";
        $comma = true;
    }
    if ($testRun['showStatistics']['atEndOfInserts']) {
        $x .= $comma ? ", " : null;
        $x .= "show tree statistics";
        $comma = true;
    }
    if ($testRun['checkMemory']['atEndOfInserts']) {
        $x .= $comma ? ", " : null;
        $x .= "display consumed memory";
        $comma = true;
    }
    if ($testRun['displayTree']['atEndOfInserts']) {
        $x .= $comma ? ", " : null;
        $x .= "print a representation of the tree";
        $comma = true;
    }
    if ($testRun['testTraverser']['atEndOfInserts']) {
        $x .= $comma ? ", " : null;
        $x .=  "print an inorder traversal";
        $comma = true;
    }
    print("$x<br/>");
    $ccc++;
}
if ($testRun['testDeletes']) {
    switch($testRun['nodesGeneration']) {
    case "random":
        print("<b>$cca.$ccb.$ccc</b> delete randomly  $testRun[howManyNodes] node(s) from the tree<br/>");
        break;
    case "random list":
        print("<b>$cca.$ccb.$ccc</b> delete the nodes from the tree in the sequence of the generated list<br/>");
        break;
    case "list":
        print("<b>$cca.$ccb.$ccc</b> delete the nodes from the tree in the sequence of the list<br/>");
        break;
    }
    $ccc++;
    if ($testRun['modulo']) {
        $comma = false;
        $x = "<b>$cca.$ccb.$ccc</b> at every $testRun[modulo] delete(s) ";
        if ($testRun['checkMemory']['atModulo']) {
            $x .= $comma ? ", " : null;
            $x .= "display consumed memory";
            $comma = true;
        }
        if ($testRun['validateTree']['atModulo']) {
            $x .= $comma ? ", " : null;
            $x .= "validate the tree";
            $comma = true;
        }
        if ($testRun['displayTree']['atModulo']) {
            $x .= $comma ? ", " : null;
            $x .= "print a representation of the tree";
            $comma = true;
        }
        if ($testRun['testTraverser']['atModulo']) {
            $x .= $comma ? ", " : null;
            $x .=  "print an inorder traversal";
            $comma = true;
        }
        print("$x<br/>");
        $ccc++;
    }
    if ($testRun['validateTree']['atEndOfDeletes'] or
        $testRun['showStatistics']['atEndOfDeletes'] or
        $testRun['checkMemory']['atEndOfDeletes'] or
        $testRun['displayTree']['atEndOfDeletes'] or 
        $testRun['testTraverser']['atEndOfDeletes']) {
        $comma = false;
        $x = "<b>$cca.$ccb.$ccc</b> at end of deletes ";
        if ($testRun['validateTree']['atEndOfDeletes']) {
            $x .= $comma ? ", " : null;
            $x .= "validate the tree";
            $comma = true;
        }
        if ($testRun['showStatistics']['atEndOfDeletes']) {
            $x .= $comma ? ", " : null;
            $x .= "show tree statistics";
            $comma = true;
        }
        if ($testRun['checkMemory']['atEndOfDeletes']) {
            $x .= $comma ? ", " : null;
            $x .= "display consumed memory";
            $comma = true;
        }
        if ($testRun['displayTree']['atEndOfDeletes']) {
            $x .= $comma ? ", " : null;
            $x .= "print a representation of the tree";
            $comma = true;
        }
        if ($testRun['testTraverser']['atEndOfDeletes']) {
            $x .= $comma ? ", " : null;
            $x .=  "print an inorder traversal";
            $comma = true;
        }
        print("$x<br/>");
        $ccc++;
    }
}
if ($testRun['testSummary']) {
    print("<b>$cca.$ccb.$ccc</b> update summary statistics<br/>");
    $ccc++;
}
$cca++;
if ($testRun['testSummary']) {
    print("<b>$cca.</b> report-out a summary by balance factor (#trees, avg height, avg rotations)<br/>");
    $cca++;
}
print("<br/>");

// ********* START *************

// callback
$myCbc = new TestCallback;

// summary callback and tree
if ($testRun['testSummary']) {
    $mySummaryCbc = new TestCallbackSummary;
    $treeSummary = new RbppavlTree("TestCallbackSummary");
}

// Memory limit
if ($testRun['checkMemory']['atStart']) {
    $myCbc->diagnosticMessage(6, 0, null, null, "Memory limit: " . return_bytes(ini_get('memory_limit'))/1024/1024 . 'M; threshold: ' . $testRun['memoryThreshold'], 'test script'); 
}

// Test change diagnostic messages
if ($testRun['testChangeDiagnosticMessages']) {
    $tree = new RbppavlTree("TestCallback"); 
    $t = array(
        1         => array(RBPPAVL_DEBUG,   '%method \'%node\''),
        2         => array(RBPPAVL_DEBUG,   'nodus \'%node\' iam existit'),
        3         => array(RBPPAVL_DEBUG,   '*caput* nodus \'%node\' insertus est; nodes duco: %count'),
        4         => array(RBPPAVL_DEBUG,   'nodus \'%node\' insertus est %direction quam nodum \'%parent\'; nodes duco: %count'),
        5         => array(RBPPAVL_DEBUG,   'altitudine aucta pro nodo \'%node\'; nova altitudo: %height, novus pondus: %balance'),
        6         => array(RBPPAVL_DEBUG,   'curabitur ipsum nodus \'%node\'; novus pondus est: %balance'),
        7         => array(RBPPAVL_DEBUG,   'circumversio %rotationType ex nodo \'%node\' (pondus: %balance)'),
        8         => array(RBPPAVL_DEBUG,   'nodus \'%node\' non est invenitus'),
        9         => array(RBPPAVL_DEBUG,   'nodus \'%node\' invenitus est'),
        10        => array(RBPPAVL_DEBUG,   'nodus \'%node\' obliteratus est, %nodeType; nodus subpostus: %replaceBy; nodes duco: %count'),
        11        => array(RBPPAVL_DEBUG,   'altitudine diminuita pro nodo \'%node\'; nova altitudo: %height, novus pondus: %balance'),
        12        => array(RBPPAVL_DEBUG,   '%ctr nodi detersi cum arboris destruendis'),
        100       => array(RBPPAVL_INFO,    'Status felix'),
        101       => array(RBPPAVL_INFO,    '%class - Vulgata %version'),
        102       => array(RBPPAVL_WARNING, 'Arbor vacuum est.'),
        103       => array(RBPPAVL_WARNING, 'Parum memoriae faciens insertione nodi \'%node\'.'),
        104       => array(RBPPAVL_ERROR,   'Non dictum vel inaccessibilis %class::%method ordinem advocatus est'),
        105       => array(RBPPAVL_ERROR,   'Non dictam vel inaccessibilis variabilis %class::%property advocata est'),
        106       => array(RBPPAVL_ERROR,   'Nulla classis resonante definita est cum faciens %class'),
        107       => array(RBPPAVL_WARNING, 'Res nefasta vel indefinita commissa prae %class::%method. Non nulla res accepta tantum.'),
        1000      => array(RBPPAVL_NOTICE,  'FELIX exitus convalidationis arboris; nodes duco: %count'),
        1001      => array(RBPPAVL_ERROR,   '*INFAUSTUS* exitus convalidationis arboris, cum defecto in %failureType supra nodem: \'%node\' (altitudo: %height pondus: %balance)'),
        1002      => array(RBPPAVL_INFO,    'Calculi arboris: Libra %balance; Nodes duco: %count; Altitudo arboris: %height'),
        1003      => array(RBPPAVL_INFO,    'Calculi arboris: Nodes inseritis: (%ins/%att_ins) Nodes subpostis: (%repl/%att_repl) Nodes obliteratis: (%del/%att_del)'),
        1004      => array(RBPPAVL_INFO,    'Calculi arboris: Curabitur ipsum operationes: %self; Circumversiones: %rotations (RR: %rr, RL: %rl, LL: %ll, LR: %lr)'),

        'none'       => array(RBPPAVL_TEXT,   '*nullus*'),
        'right'      => array(RBPPAVL_TEXT,   'dextera'),
        'left'       => array(RBPPAVL_TEXT,   'sinistra'),
        'root'       => array(RBPPAVL_TEXT,   '*caput*'),
        'leaf'       => array(RBPPAVL_TEXT,   'folium'),
        'height'     => array(RBPPAVL_TEXT,   'altitudinem'),
        'balance'    => array(RBPPAVL_TEXT,   'pondum'),
        'p-noleft'   => array(RBPPAVL_TEXT,   'non est arbor sinistra'),
        'p-noright'  => array(RBPPAVL_TEXT,   'non est arbor dextera'),
        'r-noleft'   => array(RBPPAVL_TEXT,   'filium sinistrum non est sub arborem dextera \'%node\''),
        'r-left'     => array(RBPPAVL_TEXT,   'filium sinistrum est sub arborem dextera \'%node\''),
        'internal'   => array(RBPPAVL_TEXT,   'internus,'),
    );
    $tree->setMessages($t);
}

// ********* MAIN CYCLE *************

for ($tx = 1; $tx <= $testRun['howManyCycles']; $tx++) {

    set_time_limit(30);
    
    // ********* LIST OF VALUES *************

    if ($testRun['nodesGeneration'] == 'random list') {                 // random
        for ($i = 0; $i < $testRun['howManyNodes']; $i++)    {  
            $qres[$i] = mt_rand(0, $testRun['maxIntValueInNode']);
        }
    } elseif ($testRun['nodesGeneration'] == 'list') {                  // predefined
        $qres = $testRun['nodesList'];
    }

    // ********* BALANCE FACTOR CYCLE *************
    
    for ($i = $testRun['fromBalanceFactor']; $i <= $testRun['toBalanceFactor']; $i++)    {   
        if (!$testRun['testSummary']) {
            $myCbc->diagnosticMessage(6, 0, null, null, "------------------------------------------------------------------", 'test script'); 
            $myCbc->diagnosticMessage(5, 0, null, null, "Cycle: $tx, Balance factor: $i", 'test script'); 
        }
            
        $tree = new RbppavlTree("TestCallback", $i, $testRun['verbose'], $testRun['memoryThreshold']);

        // ********* INSERTS *************

        if ($testRun['checkMemory']['atBeginOfInserts']) {
            $myCbc->diagnosticMessage(6, 0, null, null, "Memory at start inserts (Mb): " . round((memory_get_usage()/1024/1024), 2), 'test script'); 
        }
        for ($ctr = 0; $ctr < $testRun['howManyNodes']; )    {
            $nai = new TestClass;
            if ($testRun['nodesGeneration'] == 'random') {
                $nai->q = mt_rand(0, $testRun['maxIntValueInNode']);
            } else {
                if (isset($qres[$ctr])) {    
                    $nai->q = $qres[$ctr];
                } else {
                    break;
                }
            }
            $t = $tree->insert($nai); 
            if ($tree->getStatusLevel() <= RBPPAVL_WARNING) {
                break;
            }
            $ctr++;
            if ($testRun['modulo'] and fmod($ctr, $testRun['modulo']) == 0 and $ctr <> 0) {
                set_time_limit(30);
                if ($testRun['checkMemory']['atModulo']) {
                    $myCbc->diagnosticMessage(6, 0, null, null, "Memory at $ctr inserts (Mb): " . round((memory_get_usage()/1024/1024), 2), 'test script'); 
                }
                if ($testRun['validateTree']['atModulo']) {
                    $failingNode = $tree->debugValidate($setStatusOnSuccess = true);
                } 
                if ($testRun['displayTree']['atModulo']) {
                    display_tree_structure($myCbc, $tree, $testRun['displayHowManyLevels']); 
                }
                if ($testRun['testTraverser']['atModulo']) {
                    test_traverser($myCbc, $tree);
                }
            }
        }

        // ********* AT END OF INSERTS *************

        if ($testRun['validateTree']['atEndOfInserts']) {
            $failingNode = $tree->debugValidate($setStatusOnSuccess = true);
        }
        if ($testRun['showStatistics']['atEndOfInserts']) {
            $stats = $tree->getStatistics($stat = null, $setStatus = true);
        } else {
            $stats = $tree->getStatistics($stat = null);
        }
        if ($testRun['testSummary']) {
            $summHeight = $stats['height'];
        }
        if ($testRun['checkMemory']['atEndOfInserts']) {
            $myCbc->diagnosticMessage(6, 0, null, null, "Memory at end inserts (Mb): " . round((memory_get_usage()/1024/1024), 2), 'test script'); 
        } 
        if ($testRun['displayTree']['atEndOfInserts']) {
            display_tree_structure($myCbc, $tree, $testRun['displayHowManyLevels']); 
        }
        if ($testRun['testTraverser']['atEndOfInserts']) {
            test_traverser($myCbc, $tree);
        }

        // ********* DELETES *************

        if ($testRun['checkMemory']['atBeginOfDeletes']) {
            $myCbc->diagnosticMessage(6, 0, null, null, "Memory at start deletes (Mb): " . round((memory_get_usage()/1024/1024), 2), 'test script'); 
        }
        if ($testRun['testDeletes']) {
            $ctr = 0;
            while ($tree->getCount() > 0)    {
                $nai = new TestClass;
                if ($testRun['nodesGeneration'] == 'random') {
                    $nai->q = mt_rand(0, $testRun['maxIntValueInNode']);
                    $match = $tree->find($nai, RBPPAVL_FIND_NEXT_MATCH);
                    if (!$match) {
                        $match = $tree->find($nai, RBPPAVL_FIND_PREV_MATCH);
                    }
                    $nai->q = $match->q;
                } else {
                    if (isset($qres[$ctr])) {    
                        $nai->q = $qres[$ctr];
                    } else {
                        break;
                    }
                }
                $t = $tree->delete($nai); 
                if ($tree->getStatusLevel() <= RBPPAVL_WARNING) {
                    break;
                }
                $ctr++;
                if ($testRun['modulo'] and fmod($ctr, $testRun['modulo']) == 0 and $ctr <> 0) {
                    set_time_limit(30);
                    if ($testRun['checkMemory']['atModulo']) {
                        $myCbc->diagnosticMessage(6, 0, null, null, "Memory at $ctr deletes (Mb): " . round((memory_get_usage()/1024/1024), 2), 'test script'); 
                    }
                    if ($testRun['validateTree']['atModulo']) {
                        $failingNode = $tree->debugValidate($setStatusOnSuccess = true);
                    } 
                    if ($testRun['displayTree']['atModulo']) {
                        display_tree_structure($myCbc, $tree, $testRun['displayHowManyLevels']); 
                    }
                    if ($testRun['testTraverser']['atModulo']) {
                        test_traverser($myCbc, $tree);
                    }
                }
            }
        }

        // ********* AT END OF DELETES *************

        if ($testRun['validateTree']['atEndOfDeletes']) {
            $failingNode = $tree->debugValidate($setStatusOnSuccess = true);
        }
        if ($testRun['showStatistics']['atEndOfDeletes']) {
            $stats = $tree->getStatistics($stat = null, $setStatus = true);
        } else {
            $stats = $tree->getStatistics($stat = null);
        }
        if ($testRun['checkMemory']['atEndOfDeletes']) {
            $myCbc->diagnosticMessage(6, 0, null, null, "Memory at end deletes (Mb): " . round((memory_get_usage()/1024/1024), 2), 'test script'); 
        } 
        if ($testRun['displayTree']['atEndOfDeletes']) {
            display_tree_structure($myCbc, $tree, $testRun['displayHowManyLevels']); 
        }
        if ($testRun['testTraverser']['atEndOfDeletes']) {
            test_traverser($myCbc, $tree);
        }

        // ********* SUMMARY *************

        if ($testRun['testSummary']) {
            $summ = new TestSummary;
            $summ->q = $stats['balance_factor'];
            $ins = $treeSummary->insert($summ);
            if ($ins) {
                $summ = $ins;
            }
            $summ->qTrees++;
            $summ->totHeight += $summHeight;       
            $rotations = $stats['ll'] + $stats['lr'] + $stats['rr'] + $stats['rl'];
            $summ->totRotations += $rotations;       
        }

        $tree = null; 
    }
}

if ($testRun['testSummary']) {
    test_traverser($mySummaryCbc, $treeSummary);
    $treeSummary = null;
}

