<?php

require_once(dirname(dirname(__FILE__)) . '/code/adodb5/adodb.inc.php');


//--------------------------------------------------------------------------------------------------
$db = NewADOConnection('mysql');
$db->Connect("localhost", 
	'root' , '' ,'col_3JAN2011');

// Ensure fields are (only) indexed by column name
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

$names = array();
$node_stack = array();
$packing_type = array();


//--------------------------------------------------------------------------------------------------
function get_root($name)
{
	global $db;
	global $names;
	
	$sql = "SELECT * FROM taxa WHERE name='$name' AND is_accepted_name=1 LIMIT 1";
	$root_id = 0;
	
	$result = $db->Execute($sql);
	if ($result == false) die("failed [" . __LINE__ . "]: " . $sql);
	
	if ($result->NumRows() == 1)
	{
		$root_id = $result->fields['record_id'];
		$names[$root_id] = $result->fields['name'];
//		$names[$root_id] = $result->fields['lsid'];
	}
	
	return $root_id;
}

//--------------------------------------------------------------------------------------------------
// Get immediate children of this node
function get_children($subtree_root_id)
{
	global $db;
	global $names;
	
	$children = array();

	// Get children		
	$sql = "SELECT * FROM taxa WHERE parent_id=" . $subtree_root_id . " AND is_accepted_name=1\n";
	$result = $db->Execute($sql);
	if ($result == false) die("failed [" . __LINE__ . "]: " . $sql);
	while (!$result->EOF) 
	{
		$children[] = $result->fields['record_id'];
		$names[$result->fields['record_id']] = $result->fields['name'];
//		$names[$result->fields['record_id']] = $result->fields['lsid'];
		$result->MoveNext();		
	}
	
	return $children;
}

//--------------------------------------------------------------------------------------------------
// First time we visit this node, store basic details and initialise weight and depth 
function pre_visit($node_id)
{
	global $names;
	global $node_stack;
	
	$node = new stdclass;
	$node->weight = 0;
	$node->depth = 0;
	$node->label  = $names[$node_id];
	$node_stack[$node_id] = $node;
	
//	if (preg_match('/\w+ \w+( \w+)?/', $node->label))
	if (preg_match('/^\w+ \w+$/', $node->label))
	{
		echo "Catalogue of Life\t" . $node->label . "\n";
	}
	
	// names...
}

//--------------------------------------------------------------------------------------------------
// Last time we visit this node
function post_visit($node_id, $stack, $stack_counter)
{
	global $names;
	global $node_stack;
	
	if ($stack_counter > 0)
	{
		// First item in stack array at level below this level is the ancestor
		$ancestor_id = $stack[$stack_counter-1][0];
		
		// Add this node's weight to that of its ancestor
		$node_stack[$ancestor_id]->weight += $node_stack[$node_id]->weight;
		
		// Update depth of ancestor
		$node_stack[$ancestor_id]->depth 
			= max($node_stack[$ancestor]->depth,
					($node_stack[$node_id]->depth + 1));							
	}
	
	// save memory
	unset ($node_stack[$node_id]);
	unset ($names[$node_id]);
}
	

//--------------------------------------------------------------------------------------------------
// traverse tree using database calls and a stack of arrays
function traverse($name)
{
	global $names;
	global $node_stack;
	$stack = array();
	
	$root_id = get_root($name);
			
	// start by getting children of root
	$stack[] = array($root_id);
	
	$stack_counter = 0;
	$done = false;
	while (!$done)
	{
		$node = $stack[$stack_counter][0];

		pre_visit($node);
			
		$children = get_children($node);
		if (count($children) > 0)
		{
			// We have children so add them to stack
			$ancestor = $node;
			$stack[] = $children;
			$stack_counter++;
		}
		else
		{
			// No children, so start to unwind stack
			$node_stack[$node]->weight = 1; // leaf
			$node_stack[$node]->depth = 0; // leaf
			
			post_visit($node, $stack, $stack_counter);
			
			// finished with this node	
			array_shift($stack[$stack_counter]);			
			
			// go back down tree
			while (
				($stack_counter >  0)
				&& (count($stack[$stack_counter]) == 0)
				)
			{		
			
				array_pop($stack);
				$stack_counter--;
								
				$node = $stack[$stack_counter][0];
				post_visit($node, $stack, $stack_counter);
								
				array_shift($stack[$stack_counter]);
			}
			
			if ($stack_counter == 0)
			{
				$done = true;
			}
			
		}
	}
	
}


	
//$name = 'Alpheidae';
//$name = 'Pinnotheridae';
//$name = 'Primates';
//$name = 'Hominidae';
//$name = 'Alpheoidea';
//$name = 'Anura';
//$name = 'Decapoda';
//$name = 'Reptilia';
//$name = 'Squamata';

$name = 'Mammalia';

traverse($name);





?>