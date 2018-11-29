<?php
declare(strict_types=1);

namespace Micro\BaseComponent\Utils;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request as RequestService;

/**
 * Trait RequestTrait
 *
 * @category Micro\BaseComponent
 * @package Setter
 */
trait RequestTrait
{
    /**
     * Order request service
     * @var RequestService
     */
    private $requestService;

    /**
     * Set request service
     *
     * @param RequestStack|RequestService $request
     *
     * @return $this
     */
    public function setRequest($request): self
    {
        if ($request instanceof RequestStack) {
            $this->requestService = $request->getCurrentRequest();
        } elseif ($request instanceof RequestService) {
            $this->requestService = $request;
        } else {
            throw new \Exception('Set not valid request object type');
        }

        return $this;
    }

    /**
     * Return request service object
     *
     * @return RequestService
     * 
     * @throws Exception
     */
    public function getRequest(): RequestService
    {
        if (null === $this->requestService) {
            if ($this->container) {
                $this->setRequest($this->container->get('request_stack'));
            } else {
                throw new Exception('Request service not set');
            }
        }

        return $this->requestService;
    }
}