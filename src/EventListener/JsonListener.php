<?php
declare(strict_types=1);

namespace Micro\BaseComponent\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class JsonListener
 *
 * @package Micro\BaseComponent
 * @category EventListener
 */
class JsonListener
{
    /**
     * @param GetResponseEvent $event
     *
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->add(\is_array($data) ? $data : []);
        }
    }
}