<?php
declare(strict_types=1);

namespace Micro\BaseComponent\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Class ViewListener
 *
 * @package Micro\BaseComponent
 * @category EventListener
 */
class ViewListener
{
    /**
     * Make valid json response object
     *
     * @param GetResponseForControllerResultEvent $event
     *
     * @return void
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $result = $event->getControllerResult();

        if (!$result instanceof Response) {
            $result = new JsonResponse($result, JsonResponse::HTTP_OK);
        }

        $event->setResponse($result);
    }
}