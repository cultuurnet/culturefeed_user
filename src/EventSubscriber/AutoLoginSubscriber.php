<?php

namespace Drupal\culturefeed_user\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Provides a subscriber that redirects users to the auth connect route.
 */
class AutoLoginSubscriber implements EventSubscriberInterface {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * AutoLoginSubscriber constructor.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   The current account.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match service.
   */
  public function __construct(AccountProxyInterface $account, RouteMatchInterface $routeMatch) {
    $this->currentUser = $account;
    $this->currentRouteMatch = $routeMatch;
  }

  /**
   * Check for a uid parameter in the request.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   The get response event.
   */
  public function onRequest(RequestEvent $event) {
    if ($this->currentUser->isAnonymous() && $event->getRequest()->query->has('uid')) {
      $queryParams = $event->getRequest()->query->all();

      // Unset 'uid' parameter.
      unset($queryParams['uid']);

      // Add destination and 'skipConfirmation' parameter.
      $destinationUrl = $event->getRequest()->getPathInfo();

      if (!empty($queryParams)) {
        $destinationUrl .= '?' . http_build_query($queryParams);
      }

      $queryParams['destination'] = $destinationUrl;
      $queryParams['skipConfirmation'] = TRUE;

      $connectUrl = Url::fromRoute('culturefeed_user.connect', [], [
        'query' => $queryParams,
      ]);

      $response = new RedirectResponse($connectUrl->toString());
      $event->setResponse($response);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => [
        ['onRequest', 300],
      ],
    ];
  }

}
