<?php
declare(strict_types=1);

namespace Micro\BaseComponent\Utils;

use Symfony\Component\HttpFoundation\Response as ResponseService;

/**
 * Trait ResponseTrait
 *
 * @category Micro\BaseComponent
 * @package Setter
 */
trait ResponseTrait
{
    /**
     * Order response service
     * @var ResponseService
     */
    private $responseService;

    /**
     * Set response service
     *
     * @param ResponseService $response
     *
     * @return $this
     */
    public function setResponse(ResponseService $response): self
    {
        $this->responseService = $response;

        return $this;
    }

    /**
     * Return response service object
     *
     * @return ResponseService
     * 
     * @throws Exception
     */
    public function getResponse(): ResponseService
    {
        if (null === $this->responseService) {
            $responseService = new ResponseService();
            $this->setResponse($responseService);
        }

        return $this->responseService;
    }
}