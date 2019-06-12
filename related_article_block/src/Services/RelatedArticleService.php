<?php

namespace Drupal\related_article_block\Services;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

/**
 * Service to fetch related article.
 */
class RelatedArticleService extends ControllerBase {

  /**
   * Function to fetch all related article with rules.
   */
  public function getRelatedArticle($nid, $tid, $aid) {

    $resultNodes = [];
    $maxLimit = (int) '5';
    // Query for fetch article having same category and same author.
    $query = \Drupal::database()->select('taxonomy_index', 'ti');
    $query->join('node_field_data', 'nfd', 'ti.nid = nfd.nid');
    $query->fields('ti', ['nid']);
    $query->condition('ti.tid', $tid, 'IN');
    $query->condition('ti.nid', $nid, '<>');
    $query->condition('nfd.uid', $aid, '=');
    $query->condition('nfd.type', 'article', '=');
    $query->distinct(TRUE);
    $query->range(0, $maxLimit);
    $result = $query->execute();

    $nodeIds = $result->fetchCol();
    if (count($nodeIds) == $maxLimit) {
      return Node::loadMultiple($nodeIds);
    }
    else {
      $resultNodes = array_unique(array_merge($resultNodes, $nodeIds));
      $limit = $maxLimit - count($resultNodes);

      // Query to fetch article having diffrent category but same author.
      $nodeIds = $this->fetchArticleDiffAuthor($tid, $nid, $aid, $limit);
      $resultNodes = array_unique(array_merge($resultNodes, $nodeIds));
      if (count($resultNodes) == $maxLimit) {
        return Node::loadMultiple($resultNodes);
      }
      else {
        $limit = $maxLimit - count($resultNodes);

        // Query to fetch article having same category but diffrent author.
        $nodeIds = $this->fetchArticleDiffCat($tid, $aid, $nid, $limit);
        $resultNodes = array_unique(array_merge($resultNodes, $nodeIds));

        if (count($resultNodes) == $maxLimit) {
          return Node::loadMultiple($resultNodes);
        }
        else {
          $limit = $maxLimit - count($resultNodes);

          // Query to fetch article having different category and author.
          $nodeIds = $this->fetchArticleDiffAll($tid, $aid, $nid, $limit);
          $resultNodes = array_unique(array_merge($resultNodes, $nodeIds));
          return Node::loadMultiple($resultNodes);
        }
      }
    }
  }

  /**
   * Function to fetch all related article with diffrent Author.
   */
  public function fetchArticleDiffAuthor($tid, $nid, $aid, $limit) {
    $query = \Drupal::database()->select('taxonomy_index', 'ti');
    $query->join('node_field_data', 'nfd', 'ti.nid = nfd.nid');
    $query->fields('ti', ['nid']);
    $query->condition('ti.tid', $tid, 'IN');
    $query->condition('ti.nid', $nid, '<>');
    $query->condition('nfd.uid', $aid, '<>');
    $query->condition('nfd.type', 'article', '=');
    $query->distinct(TRUE);
    $query->range(0, $limit);
    $result = $query->execute();
    return $result->fetchCol();
  }

  /**
   * Function to fetch all related article with diffrent category.
   */
  public function fetchArticleDiffCat($tid, $aid, $nid, $limit) {
    $query = \Drupal::database()->select('taxonomy_index', 'ti');
    $query->join('node_field_data', 'nfd', 'ti.nid = nfd.nid');
    $query->fields('ti', ['nid']);
    $query->condition('ti.tid', $tid, '<>');
    $query->condition('nfd.uid', $aid, '=');
    $query->condition('ti.nid', $nid, '<>');
    $query->condition('nfd.type', 'article', '=');
    $query->distinct(TRUE);
    $query->range(0, $limit);
    $result = $query->execute();
    return $result->fetchCol();
  }

  /**
   * Function to fetch all related article with diffrent Author and category.
   */
  public function fetchArticleDiffAll($tid, $aid, $nid, $limit) {
    $query = \Drupal::database()->select('taxonomy_index', 'ti');
    $query->join('node_field_data', 'nfd', 'ti.nid = nfd.nid');
    $query->fields('ti', ['nid']);
    $query->condition('ti.tid', $tid, '<>');
    $query->condition('ti.nid', $nid, '<>');
    $query->condition('nfd.uid', $aid, '=');
    $query->condition('nfd.type', 'article', '=');
    $query->distinct(TRUE);
    $query->range(0, $limit);
    $result = $query->execute();
    return $result->fetchCol();
  }

}
