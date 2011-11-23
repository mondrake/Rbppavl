# PHP AVL binary search tree - Coming soon!#

A set of PHP classes implementing binary search trees that comply to AVL 
rules. The API exposes methods for tree operations (insert, replace, 
delete, find), and for in-order traversal (find, first, last, prev, 
next, curr). Nodes within the trees are not accessible publicly. Tree 
and traversal operations implement relaxed balance factors, and 
parent-pointer node structures. Hooks for node comparison, error 
handling and diagnostic logging capabilities are provided via a callback 
interface. 


<strong>What are AVL trees?</strong>

In an nutshell, AVL trees are a form of binary search trees that can be 
used to keep a set of data efficiently sorted while adding or removing 
elements. 

For more details, Wikipedia has articles for [binary search trees](http://en.wikipedia.org/wiki/Binary_search_tree) 
and [AVL trees](http://en.wikipedia.org/wiki/AVL_tree). 

<strong>Acknowledgments</strong>

....

<strong>Basic usage</strong>

Define your data object and the callback interface to be used by Rbppavl

	class TestObject {
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
			return null;
		}

		public function errorHandler($id, $text, $params, $className = null) {
			return null;
		}
	}

Create a tree

	$tree = new RbppavlTree("callbackClass");

Insert data objects in the tree

	$data = new TestObject;
	$data->q = 12;
	$t = $tree->insert($data); 
	$data = new TestObject;
	$data->q = 7;
	$t = $tree->insert($data); 
	$data = new TestObject;
	$data->q = 36;
	$t = $tree->insert($data); 

Perform in-order traversal

	$trav = new RbppavlTraverser($tree);
	$r = $trav->first();
	while ($r != NULL)    {
		print($r->q . '<br/>);
	}

<strong>More examples</strong>

See more examples in simple_test.php and full_test.php

