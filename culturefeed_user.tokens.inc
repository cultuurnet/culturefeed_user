<?php

/**
 * @file
 * Tokens for culturefeed user module.
 */

use Drupal\Core\Render\BubbleableMetadata;

/**
 * Implements hook_token_info().
 */
function culturefeed_user_token_info() {
  $info = [];

  $info['types']['uitiduser'] = [
    'name' => t('UiTID user'),
    'description' => t('Custom tokens with values from the active UiTID user.'),
  ];

  $info['tokens']['uitiduser']['name'] = [
    'name' => t('UiTID user name'),
    'description' => t('The name of the active UiTID user.'),
  ];

  $info['tokens']['uitiduser']['first_name'] = [
    'name' => t("UiTID user's first name"),
    'description' => t('The first name of the active UiTID user.'),
  ];

  $info['tokens']['uitiduser']['email'] = [
    'name' => t('UiTID user email'),
    'description' => t('The email address of the active UiTID user.'),
  ];

  $info['tokens']['uitiduser']['street'] = [
    'name' => t('UiTID user street'),
    'description' => t('The street (and number) of the active UiTID user.'),
  ];

  $info['tokens']['uitiduser']['zipcode'] = [
    'name' => t('UiTID user zipcode'),
    'description' => t('The zipcode of the active UiTID user.'),
  ];

  $info['tokens']['uitiduser']['city'] = [
    'name' => t('UiTID user city'),
    'description' => t('The city of the active UiTID user.'),
  ];

  $info['tokens']['uitiduser']['country'] = [
    'name' => t('UiTID user country'),
    'description' => t('The country of the active UiTID user.'),
  ];

  return $info;
}

/**
 * Implements hook_tokens().
 */
function culturefeed_user_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $replacements = [];

  if ($type === 'uitiduser') {

    /** @var \Drupal\culturefeed_user\CultureFeedCurrentUserInterface $cultureFeedCurrentUser */
    $cultureFeedCurrentUser = \Drupal::service('culturefeed_user.current_user');

    try {
      if ($cultureFeedCurrentUser->isCultureFeedUser()) {
        /** @var \CultureFeed_User $cultureFeedUser */
        $cultureFeedUser = $cultureFeedCurrentUser->getUser();

        foreach ($tokens as $name => $original) {
          switch ($name) {
            case 'name':
              $replacements[$original] = $cultureFeedUser->familyName;
              break;

            case 'first_name':
              $replacements[$original] = $cultureFeedUser->givenName;
              break;

            case 'email':
              $replacements[$original] = $cultureFeedUser->mbox;
              break;

            case 'street':
              $replacements[$original] = $cultureFeedUser->street;
              break;

            case 'zipcode':
              $replacements[$original] = $cultureFeedUser->zip;
              break;

            case 'city':
              $replacements[$original] = $cultureFeedUser->city;
              break;

            case 'country':
              $replacements[$original] = $cultureFeedUser->country;
              break;

          }
        }
      }
      else {
        foreach ($tokens as $name => $original) {
          $replacements[$original] = '';
        }
      }
    }
    catch (\Exception $e) {
      foreach ($tokens as $name => $original) {
        $replacements[$original] = '';
      }
    }
  }

  return $replacements;
}
