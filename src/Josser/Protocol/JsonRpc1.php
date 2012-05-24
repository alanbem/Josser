<?php

/*
 * This file is part of the Josser package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josser\Protocol;

use Josser\Client\Response\ResponseInterface;
use Josser\Client\Response\Response;
use Josser\Client\Request\RequestInterface;
use Josser\Client\Request\Request;
use Josser\Exception\InvalidRequestException;
use Josser\Exception\InvalidResponseException;
use Josser\Exception\RpcFaultException;
use Josser\Protocol\JsonRpc;

use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * JSON-RPC 1.0 Protocol.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 */
class JsonRpc1 extends JsonRpc
{
    /**
     * Create response object.
     *
     * @throws \Josser\Exception\RpcFaultException
     * @param mixed $dto
     * @return \Josser\Client\Response\ResponseInterface
     */
    public function createResponse($dto)
    {
        $this->validateResponseDataTransferObject($dto);

        if(isset($dto['error'])) {
            throw new RpcFaultException($dto['error']['message']);
        }

        $result = $dto['result'];
        $id = $dto['id'];
        return new Response($result, $id);
    }

    /**
     * Validate and filter request method name.
     *
     * @throws \Josser\Exception\InvalidRequestException
     * @param string $method
     * @return void
     */
    private function validateRequestMethod($method)
    {
        if(!is_string($method)) {
            $error = sprintf('Invalid method type. Remote method name must be string. %s detected.', gettype($method));
            throw new InvalidRequestException($error);
        }
//        if(substr($method, 0, 4) == 'rpc.') {
//            $error = 'Invalid remote method. Method name cannot start with "rpc.".';
//            throw new InvalidRequestException($error);
//        }
    }

    /**
     * Validate request parameters.
     *
     * @throws \Josser\Exception\InvalidRequestException
     * @param array $params
     * @return void
     */
    private function validateRequestParams($params)
    {
        if(!is_array($params) || (is_array($params) && !$this->isIndexed($params))) {
            $error = 'Invalid parameters structure. Parameters must be hold within indexed-only array.';
            throw new InvalidRequestException($error);
        }
    }

    /**
     * Validate and filter request id.
     *
     * @throws \Josser\Exception\InvalidRequestException
     * @param mixed $id
     * @return void
     */
    private function validateRequestId($id)
    {
        // from spec: The request id. This can be of any type.
        if(is_object($id)) {
            $error = sprintf('Request id as object is not supported.', gettype($id));
            throw new InvalidRequestException($error);
        }
    }

    /**
     * Validate $request object.
     *
     * @throws \Josser\Exception\InvalidRequestException
     * @param \Josser\Client\Request\RequestInterface $request
     * @return \Josser\Client\Request\RequestInterface
     */
    private function validateRequest(RequestInterface $request)
    {
        $this->validateRequestMethod($request->getMethod());
        $this->validateRequestParams($request->getParams());
        if (!$this->isNotification($request)) {
            $this->validateRequestId($request->getId());
        }
        return $request;
    }

    /**
     * Return DTO of a request.
     *
     * todo: implement JSON class hinting
     *
     * @param \Josser\Client\Request\RequestInterface $request
     * @return array
     */
    public function getRequestDataTransferObject(RequestInterface $request)
    {
        $this->validateRequest($request);

        $dto = array();
        $dto['method'] = $request->getMethod();
        $dto['params'] = $request->getParams();

        if(!$this->isNotification($request)) {
            $dto['id'] = $request->getId();
        } else {
            $dto['id'] = null;
        }
        return $dto;
    }

    /**
     * Validate response DTO.
     *
     * @throws \Josser\Exception\InvalidResponseException
     * @param mixed $dto
     * @return void
     */
    private function validateResponseDataTransferObject($dto)
    {
        if(!is_array($dto) && !is_object($dto)) {
            $error = sprintf("Incorrect response type detected. An array or object expected. %s type detected.", gettype($dto));
            throw new InvalidResponseException($error);
        }
        $dto = (array) $dto;

        // id check
        if(!array_key_exists('id', $dto)) {
            $error = 'Response id not defined.';
            throw new InvalidResponseException($error);
        }
        $this->validateResponseDataTransferObjectId($dto['id']);

        if(!array_key_exists('result', $dto) || !array_key_exists('error', $dto)) {
            $error = 'Error object or result not found in response.';
            throw new InvalidResponseException($error);
        }

        if(($dto['result'] === null && $dto['error'] === null) || ($dto['result'] !== null && $dto['error'] !== null)) {
            $error = 'Either error or message must be null in response object. Not both and not neither.';
            throw new InvalidResponseException($error);
        }

        $this->validateResponseDataTransferObjectResult($dto['result']);

        $this->validateResponseDataTransferObjectError($dto['error']);
    }

    /**
     * @param mixed $result
     * @return void
     */
    private function validateResponseDataTransferObjectResult($result)
    {
        // no validation
    }

    /**
     * @throws \Josser\Exception\InvalidResponseException
     * @param mixed $id
     * @return void
     */
    private function validateResponseDataTransferObjectId($id)
    {
        if(is_object($id)) {
            $error = sprintf('Invalid response id type. Response id must be integer, string or null. Response id of %s type detected.', gettype($id));
            throw new InvalidResponseException($error);
        }
    }

    /**
     * @throws \Josser\Exception\InvalidResponseException
     * @param mixed $rpcError
     * @return void
     */
    private function validateResponseDataTransferObjectError($rpcError)
    {
        if(null === $rpcError) { // null is perfectly acceptable in case of valid response
            return;
        }

        if(!is_array($rpcError)) {
            $error = sprintf("Incorrect error object detected. An array or object expected. %s type detected.", gettype($rpcError));
            throw new InvalidResponseException($error);
        }
        $rpcError = (array) $rpcError;

        if(!array_key_exists('code', $rpcError)) {
            $error = 'Response error code is not defined.';
            throw new InvalidResponseException($error);
        }
        if(!is_int($rpcError['code'])) {
            $error = sprintf('Response error code must be an integer. "%s" detected.', gettype($rpcError['code']));
            throw new InvalidResponseException($error);
        }

        if(!array_key_exists('message', $rpcError)) {
            $error = 'Response error message is not defined.';
            throw new InvalidResponseException($error);
        }
        if(!is_string($rpcError['message'])) {
            $error = sprintf('Response error message must be a string. "%s" detected.', gettype($rpcError['message']));
            throw new InvalidResponseException($error);
        }

        // TODO: validate optional 'data' attribute
    }
}
