<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\EventSubscriber;

use KejawenLab\ApiSkeleton\Admin\AdminContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

/**
 * @author Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 */
final class LogoutSubscriber implements EventSubscriberInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function redirect(LogoutEvent $event)
    {
        if (!AdminContext::isAdminContext($event->getRequest())) {
            return;
        }

        $event->setResponse(new RedirectResponse($this->urlGenerator->generate(AdminContext::ADMIN_ROUTE)));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'redirect',
        ];
    }
}
