<head>     
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<?php
require_once "Rbppavl/Rbppavl.php";

// change value below to increase/decrease the number of nodes to be generated
$howManyNodes = 10;


// ------------------------------------------------------------------
// Test classes
// ------------------------------------------------------------------

class TestClass {
    public $q;
}

class TestCallback implements RbppavlCbInterface    {

    public function compare($a, $b)    {
        return ($a->q == $b->q) ? 0 : (($a->q < $b->q) ? -1 : 1);
    }

    public function dump($a)    {
        return $a->q;
    }

    public function diagnosticMessage($severity, $id, $text, $params, $className = null) {
        if ($params) {
            foreach ($params as $a => $b) {
                $text = str_replace($a, $b, $text);
            }
        }
        print ($text . '<br/>');
    }

    public function errorHandler($id, $text, $params, $className = null) {
        return null;
    }
}

// ------------------------------------------------------------------
// Main routine
// ------------------------------------------------------------------

print ('<h2>Simple Rbppavl tree test - a standard AVL tree</h2>');
print ("This test generates $howManyNodes random integers, inserts them in a standard AVL tree, validates the tree,<br/>");
print ('performs an inorder traversal, then deletes the tree and its content.<br/>');
print ('For more complex scenarios, see full test <a href="full_test.php">here</a>.<br/>');
print ('Check Rbppavl documentation <a href="Rbppavl_doc">here</a>.<br/><br/>');

// generate some random data values
$qres = array();
for ($i = 0; $i < $howManyNodes; $i++)    {  
    $qres[$i] = rand(0, $howManyNodes * 5);
}

// create a new tree   
$tree = new RbppavlTree("TestCallback", $balanceFactor = 1, $verbose = true);

// loop to insert the data
print('<b>Node inserts</b><br/>');
$ctr=0;
foreach ($qres as $r)    {
    $data = new TestClass;
    $data->q = $r;
    $t = $tree->insert($data); 
    if ($tree->getStatusLevel() <= RBPPAVL_WARNING) {
        break;
    }
    $ctr++;
}

// check tree
print('<br/><b>Validation and statistics</b><br/>');
$failingNode = $tree->debugValidate($setStatusOnSuccess = true);
$stats = $tree->getStatistics($stat = null, $setStatus = true);

// loop to print the inorder traversal
print('<br/><b>Inorder traversal</b><br/>');
$trav = new RbppavlTraverser($tree);
$r = $trav->first();
$ctr=1;
while ($r != NULL)    {
    print("$ctr: $r->q<br/>");
    $r = $trav->next();
    $ctr++;
}

// cleanup
print('<br/><b>Cleanup</b><br/>');
