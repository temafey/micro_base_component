<?php
declare(strict_types=1);

namespace Micro\BaseComponent\EventListener;

use Micro\BaseComponent\Utils\LoggerTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ExceptionListener
 *
 * @package Micro\BaseComponent
 * @category EventListener
 */
class ExceptionListener
{
    use LoggerTrait;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var string
     */
    protected $environment;

    /**
     * ExceptionListener constructor.
     *
     * @param KernelInterface $kernel
     * @param LoggerInterface $logger
     */
    public function __construct(KernelInterface $kernel, LoggerInterface $logger)
    {
        $this->kernel = $kernel;
        $this->setLogger($logger);
    }

    /**
     * Process
     *
     * @param GetResponseForExceptionEvent $event
     *
     * @return void
     *
     * @throws \Exception
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $this->logException($exception, sprintf('%s: "%s" at %s line %s', get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine()));

        if ($this->kernel->getEnvironment() === ENV_DEV) {
            throw $exception;
        }

        $event->setResponse(new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND));
    }
}