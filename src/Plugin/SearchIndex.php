<?php

namespace Drupal\auto_indexer\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\node\NodeInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Render\RendererInterface;

class SearchIndex extends PluginBase{

	public function _indexNode(NodeInterface &$node) {
	  $languages = $node->getTranslationLanguages();
	  $node_render = \Drupal::entityManager()->getViewBuilder('node');
	  foreach ($languages as $language) {
	    $node = $node->getTranslation($language->getId());
	    // Render the node.
	    $build = $node_render->view($node, 'search_index', $language->getId());
	    unset($build['#theme']);
	    // Add the title to text so it is searchable.
	    $build['search_title'] = [
	      '#prefix' => '<h1>',
	      '#plain_text' => $node->label(),
	      '#suffix' => '</h1>',
	      '#weight' => -1000
	    ];
	    $text =  \Drupal::service('renderer')->renderPlain($build);
	    // Fetch extra data normally not visible.
	    $extra = \Drupal::moduleHandler()->invokeAll('node_update_index', [$node]);
	    foreach ($extra as $t) {
	      $text .= $t;
	    }
	    // Update index, using search index "type" equal to the plugin ID.
      // "node_search" is a static plugin ID for all indexNode.
	    search_index(node_search, $node->id(), $language->getId(), $text);
	  }
	}
}