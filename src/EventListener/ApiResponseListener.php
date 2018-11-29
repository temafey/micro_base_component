<?php
declare(strict_types=1);

namespace Micro\BaseComponent\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Class ApiResponseListener
 *
 * @package Micro\BaseComponent
 * @category EventListener
 */
class ApiResponseListener
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
        $request = $event->getRequest();

        $headers = [
            'token' => $request->headers->get('token'),
            'transaction_id' => $request->headers->get('transaction-id'),
            'message_id' => $request->headers->get('message-id'),
            'message_type' => $request->headers->get('message-type'),
            'channel_closed' => false,
            'channel_id' => $request->headers->get('channel-id')
        ];

        $response = new JsonResponse($result, JsonResponse::HTTP_OK, $headers);
        $event->setResponse($response);
    }
}