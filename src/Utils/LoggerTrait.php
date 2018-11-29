<?php
declare(strict_types=1);

namespace Micro\BaseComponent\Utils;

use Micro\BaseComponent\Exception\ParentExceptionInterface;
use Micro\BaseComponent\Exception\ParentExceptionTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Trait LoggerTrait
 *
 * @category Micro\BaseComponent
 * @package Service
 */
trait LoggerTrait
{
    use ParentExceptionTrait;

    /**
     * Logger service
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ExceptionListener constructor.
     *
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Log an regular message or warning
     *
     * @param string $message
     * @param int $level
     * @param array $context
     *
     * @return $this
     */
    public function logMessage(string $message, int $level, array $context = []): self
    {
        switch ($level) {
            case LOG_WARNING:
                $this->logger->warning($message, $context);
                break;

            case LOG_INFO:
                $this->logger->info($message, $context);
                break;

            case LOG_NOTICE:
                $this->logger->notice($message, $context);
                break;

            default:
                break;
        }

        return $this;
    }

    /**
     * Log an exception.
     *
     * @param \Exception $exception The \Exception instance
     * @param string     $message   The error message to log
     *
     * @return $this
     */
    public function logException(\Exception $exception, string $message): self
    {
        $context = ['exception' => $exception];

        $this->parentException = $exception;

        $context = $this->getParentExceptionContext($context, ParentExceptionInterface::CONTEXT_SELIALIZE_PRINTR);

        if (!$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= 500) {
            $this->logger->critical($message, $context);
        } else {
            $this->logger->error($message, $context);
        }

        return $this;
    }

}