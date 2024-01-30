<?php

namespace Drupal\culturefeed_user;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\culturefeed_api\CultureFeedUserContextManagerInterface;
use Drupal\culturefeed_api\DrupalCultureFeedClient;

/**
 * Intermediate service for fetching user information.
 */
class CultureFeedCurrentUser implements CultureFeedCurrentUserInterface {

  const CULTURE_FEED_PAGE_MEMBERSHIP_MEMBER = 'MEMBER';
  const CULTURE_FEED_PAGE_MEMBERSHIP_ADMIN = 'ADMIN';

  /**
   * CultureFeed "UiTID" user context manager service.
   *
   * @var \Drupal\culturefeed_api\CultureFeedUserContextManagerInterface
   */
  protected $cultureFeedUserContextManager;

  /**
   * CultureFeed client.
   *
   * @var \Drupal\culturefeed_api\DrupalCultureFeedClient
   */
  protected $cultureFeedClient;

  /**
   * The CultureFeed configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $cultureFeedConfig;

  /**
   * CultureFeedCurrentUser constructor.
   *
   * @param \Drupal\culturefeed_api\CultureFeedUserContextManagerInterface $cultureFeedUserContextManager
   *   "CultureFeed" user context manager service.
   * @param \Drupal\culturefeed_api\DrupalCultureFeedClient $cultureFeedClient
   *   The "CultureFeed" client.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   */
  public function __construct(CultureFeedUserContextManagerInterface $cultureFeedUserContextManager, DrupalCultureFeedClient $cultureFeedClient, ConfigFactoryInterface $configFactory) {
    $this->cultureFeedUserContextManager = $cultureFeedUserContextManager;
    $this->cultureFeedClient = $cultureFeedClient;
    $this->cultureFeedConfig = $configFactory->get('culturefeed_api.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function isCultureFeedUser() {
    return (bool) $this->cultureFeedUserContextManager->getUserId();
  }

  /**
   * {@inheritdoc}
   */
  public function getUserId() {
    return $this->cultureFeedUserContextManager->getUserId();
  }

  /**
   * {@inheritdoc}
   */
  public function getUser(bool $reset = FALSE) {
    return $this->getUserId() ? $this->cultureFeedClient->getUser($this->getUserId(), TRUE, TRUE, $reset) : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    $user = $this->getUser();
    return !empty($user->givenName) ? $user->givenName . ' ' . $user->familyName : $user->nick;
  }

  /**
   * {@inheritdoc}
   */
  public function getAdminPages(string $category = NULL, bool $reset = FALSE) {
    /** @var \CultureFeed_User $user */
    $user = $this->getUser($reset);
    $pages = [];

    /** @var \CultureFeed_Pages_Membership $pageMembership */
    if (!empty($user->pageMemberships)) {
      foreach ($user->pageMemberships as $pageMembership) {
        if ($pageMembership->role == $this::CULTURE_FEED_PAGE_MEMBERSHIP_ADMIN) {
          if (empty($category) || in_array($category, $pageMembership->page->getCategories())) {
            $pages[] = $pageMembership;
          }
        }
      }
    }

    return $pages;
  }

  /**
   * {@inheritdoc}
   */
  public function isAdminOfPage(\CultureFeed_Cdb_Item_Page $page) {
    /** @var \CultureFeed_User $user */
    $user = $this->getUser();

    /** @var \CultureFeed_Pages_Membership $pageMembership */
    if (!empty($user->pageMemberships)) {
      foreach ($user->pageMemberships as $pageMembership) {
        if ($page->getId() === $pageMembership->page->getId() && $pageMembership->role == $this::CULTURE_FEED_PAGE_MEMBERSHIP_ADMIN) {
          return TRUE;
        }
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastUsedCacheTags() {
    return $this->cultureFeedClient->getLastUsedCacheTags();
  }

}
