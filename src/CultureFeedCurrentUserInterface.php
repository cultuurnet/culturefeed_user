<?php

namespace Drupal\culturefeed_user;

/**
 * Defines an interface for fetching CultureFeed "UiTID" user information.
 */
interface CultureFeedCurrentUserInterface {

  /**
   * Check if the current user is a CultureFeed "UiTID" user.
   *
   * @return bool
   *   Boolean indicating if the user is a CultureFeed "UiTID" user or not.
   */
  public function isCultureFeedUser();

  /**
   * Get the "CultureFeed" user's Id.
   *
   * @return string|null
   *   The "CultureFeed" user's id.
   */
  public function getUserId();

  /**
   * Get the "CultureFeed" user.
   *
   * @param bool $reset
   *   Whether to reset the users cache.
   *
   * @return \CultureFeed_User|null
   *   A fully loaded "CultureFeed" user object or null.
   */
  public function getUser(bool $reset = FALSE);

  /**
   * Get the name of current user.
   *
   * @return string
   *   The name of current user.
   */
  public function getName();

  /**
   * Get the admin pages of the "CultureFeed" user.
   *
   * @param string|null $category
   *   Optionally filter the pages by category.
   * @param bool $reset
   *   Whether to reset the users cache.
   *
   * @return \CultureFeed_Pages_Membership[]
   *   An array of page memberships.
   */
  public function getAdminPages(string $category = NULL, bool $reset = FALSE);

  /**
   * Check if the current user is admin of a given page.
   *
   * @param \CultureFeed_Cdb_Item_Page $page
   *   The page to check.
   *
   * @return bool
   *   TRUE if user is admin.
   */
  public function isAdminOfPage(\CultureFeed_Cdb_Item_Page $page);

  /**
   * Get the cache tags that were last used on api side.
   *
   * @return array
   *   The cache tags.
   */
  public function getLastUsedCacheTags();

}
