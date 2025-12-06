<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[When('dev')]
final readonly class DevHostPortMappingEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private ?int $devHostPort)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['onKernelRequest', 0],
        ];
    }

    /**
     * Sylius generates css and image links using the X_FORWARDED_PORT header. The Bref image sets this port to 8000,
     * however if the host port is different from 8000 it will generate the wrong link. This methods sets the port
     * value to match the external host port to ensure the links match.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->devHostPort === null) {
            return;
        }

        $event->getRequest()->headers->set('X_FORWARDED_PORT', (string) $this->devHostPort);
    }
}
