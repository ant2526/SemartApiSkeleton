<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\EventSubscriber;

use KejawenLab\ApiSkeleton\Admin\Controller\LoginController;
use KejawenLab\ApiSkeleton\Controller\SecurityController;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;

/**
 * @author Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 */
final class LoginFailSubscriber implements EventSubscriberInterface
{
    private const FAILURE_KEY = 'FAILURE_KEY';

    private const MAX_LOGIN_FAILURE = 9;

    private const FAILURE_TTL = 300;

    private KernelInterface $kernel;

    private RequestStack $requestStack;

    private LoggerInterface $logger;

    private \Redis $redis;

    public function __construct(KernelInterface $kernel, RequestStack $requestStack, LoggerInterface $logger, \Redis $redis)
    {
        $this->kernel = $kernel;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        $this->redis = $redis;
    }

    public function fail(AuthenticationFailureEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request instanceof Request) {
            return;
        }

        $key = sprintf('%s_%s_%s', static::FAILURE_KEY, $event->getAuthenticationToken()->getUsername(), $request->getClientIp());

        $this->redis->incr($key);
        $this->redis->expire($key, static::FAILURE_TTL);
    }

    public function ban(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!($event->isMasterRequest() && $request->isMethod(Request::METHOD_POST))) {
            return;
        }

        if (!in_array($request->attributes->get('_route'), [SecurityController::ROUTE_NAME, LoginController::ROUTE_NAME])) {
            return;
        }

        $clientIp = $request->getClientIp();
        $key = sprintf('%s_%s_%s', static::FAILURE_KEY, $request->request->get('username'), $clientIp);
        if ((int) $this->redis->get($key) >= static::MAX_LOGIN_FAILURE && !$this->kernel->isDebug()) {
            $this->logger->critical(sprintf('IP "%s" banned due to %d login attempts failures.', $clientIp, static::MAX_LOGIN_FAILURE));

            throw new HttpException(Response::HTTP_TOO_MANY_REQUESTS);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationFailureEvent::class => 'fail',
            RequestEvent::class => [['ban', 9]],
        ];
    }
}
