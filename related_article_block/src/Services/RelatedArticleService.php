<?php

namespace Drupal\related_article_block\Services;

use Drupal\Core\Controller\ControllerBase;
use \Drupal\node\Entity\Node;

/**
 * Description of ProductServices.
 */
class RelatedArticleService extends ControllerBase {

  public function getRelatedArticle($nid, $tid) {
    $query = \Drupal::database()->select('taxonomy_index', 'ti');
    $query->join('node', 'nfd', 'ti.nid = nfd.nid');
    $query->fields('ti', array('nid'));
    $query->condition('ti.tid', $tid, 'IN');
    $query->condition('ti.nid', $nid, '<>');
    $query->condition('nfd.type', 'article', '=');
    $query->distinct(TRUE);
    $query->range(0, 5);
    $result = $query->execute();

    if($nodeIds = $result->fetchCol()){
      return Node::loadMultiple($nodeIds);
    }
  }
}
