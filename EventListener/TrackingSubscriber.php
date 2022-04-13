<?php

namespace MauticPlugin\CustomPopUpFieldsBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Mautic\CoreBundle\Event\CustomTemplateEvent;
use Mautic\CoreBundle\CoreEvents;

class TrackingSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::VIEW_INJECT_CUSTOM_TEMPLATE => ['onTemplateRender', 0],
        ];
    }

    public function onTemplateRender(CustomTemplateEvent $event)
    {
        if ($event->getTemplate() === 'MauticFocusBundle:Builder:generate.js.php') {
            $event->setTemplate('CustomPopUpFieldsBundle:Builder:generate.js.php');
        }
    }
}
