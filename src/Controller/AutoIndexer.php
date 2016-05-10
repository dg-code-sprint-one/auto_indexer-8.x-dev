<?php

namespace Drupal\auto_indexer\Controller;

use Drupal\auto_indexer\Plugin\SearchIndex;
use Drupal\node\NodeInterface;
use Drupal\comment\CommentInterface;
use Drupal\Core\Entity\EntityManagerInterface;

class AutoIndexer extends SearchIndex {

	public static function autoIndexNode(NodeInterface &$node) {
  	// Static variable to keep track of any node ids already indexed.
  	static $indexed_nodes = array();
    // Extract the node ID
  	$node_id = $node->id();
  	// Check if the node ID has already been indexed.
		if (array_search($node_id, $indexed_nodes) === false) {  				
			// Ensure we force the cache to be updated so latest content is indexed.
			$node = \Drupal::entityManager()->getStorage('node')->load($node_id);  					
			// Do the indexing of this node only.  			
			parent::_indexNode($node);
			// Update search totals.
			search_update_totals();  		
			// Append to array to ensure node only indexed once per action.
			$indexed_nodes[] = $node_id;  		
	  }
	}

  public static function autoIndexNode_comment(CommentInterface &$comment) {
  	// Static variable to keep track of any node ids already indexed.
  	static $indexed_nodes = array();
  	// Extract the comment using node ID
  	$node_id = $comment->getCommentedEntityId();
  	// Check if the node ID has already been indexed.
		if (array_search($node_id, $indexed_nodes) === false) {
			// Ensure we force the cache to be updated so latest content is indexed.
			$node = \Drupal::entityManager()->getStorage('node')->load($node_id);
			// Do the indexing of this node only.
			parent::_indexNode($node);
			// Update search totals.
			search_update_totals();
			// Append to array to ensure node only indexed once per action.
			$indexed_nodes[] = $node_id;
		}
	}
}