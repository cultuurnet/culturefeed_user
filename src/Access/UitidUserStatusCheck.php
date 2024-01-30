<?php

namespace Drupal\culturefeed_user\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\culturefeed_user\CultureFeedCurrentUserInterface;
use Symfony\Component\Routing\Route;

/**
 * Determines access to routes based on login status of current user.
 */
class UitidUserStatusCheck implements AccessInterface {

  /**
   * The current culturefeed user.
   *
   * @var \Drupal\culturefeed_user\CultureFeedCurrentUserInterface
   */
  protected $cultureFeedCurrentUser;

  /**
   * CulturefeedUserStatusCheck constructor.
   *
   * @param \Drupal\culturefeed_user\CultureFeedCurrentUserInterface $cultureFeedCurrentUser
   *   The current culturefeed user.
   */
  public function __construct(CultureFeedCurrentUserInterface $cultureFeedCurrentUser) {
    $this->cultureFeedCurrentUser = $cultureFeedCurrentUser;
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The currently logged in account.
   * @param \Symfony\Component\Routing\Route $route
   *   The route to check against.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, Route $route) {
    $required_status = filter_var($route->getRequirement('_is_uitid_user'), FILTER_VALIDATE_BOOLEAN);
    $actual_status = $this->cultureFeedCurrentUser->isCultureFeedUser();
    $access_result = AccessResult::allowedIf($required_status === $actual_status)->addCacheContexts(['user.roles:authenticated']);
    if (!$access_result->isAllowed()) {
      $access_result->setReason($required_status === TRUE ? 'This route can only be accessed by uitid users.' : 'This route can only be accessed by non-uitid users.');
    }
    return $access_result;
  }

}
