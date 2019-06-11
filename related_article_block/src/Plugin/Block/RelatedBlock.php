<?php

namespace Drupal\related_article_block\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "related_article_block,
 *   admin_label = @Translation("Related article Block"),
 * )
 */
class RelatedBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {

    $RelatedServiceObj = \Drupal::service('RelatedArticleService');
    return [
      '#markup' => $RelatedServiceObj->getRelatedArticle(),
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
}
