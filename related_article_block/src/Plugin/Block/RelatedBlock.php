<?php

namespace Drupal\related_article_block\Plugin\Block;

use Drupal\node\NodeInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "related_article_block",
 *   admin_label = @Translation("Related article Block"),
 * )
 */
class RelatedBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $relatedServiceObj = \Drupal::service('related_article');
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      // You can get nid and anything else you need from the node object.
      $tid = $node->field_tags->target_id;
      $nid = $node->id();
      $aid = $node->getOwner()->id();
    }
    $nodes = $relatedServiceObj->getRelatedArticle($nid, $tid, $aid);
    $html = "<ul>";
    foreach ($nodes as $node_content) {
      $html .= "<li><a href='/node/" . $node_content->id() . "'>" . $node_content->getTitle() . "</a></li>";;
    }
    $html .= "</ul>";
    return [
      '#markup' => $html,
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['related_block_settings'] = $form_state->getValue('related_block_settings');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
