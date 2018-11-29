<?php
declare(strict_types=1);

namespace Micro\BaseComponent\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class ApiVersionListener
 *
 * @package Micro\BaseComponent
 * @category EventListener
 */
class ApiVersionListener
{
    /**
     * @param GetResponseEvent $event
     *
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $pathInfo = $request->getPathInfo();

        if (preg_match('/\/api\/v([0-9\.]+)\//', $pathInfo, $matches)) {
            $version = ucfirst($matches[1]);
            $request->attributes->set('version', $version);
        }
    }
}