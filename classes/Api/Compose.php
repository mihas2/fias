<?php
namespace Fias\Api;

use Luracast\Restler\RestException;

class Compose extends \Luracast\Restler\Compose
{
    /**
     * Result of an api call is passed to this method
     * to create a standard structure for the data
     *
     * @param mixed $response
     * @internal param mixed $result can be a primitive or array or object
     * @return array|mixed|null
     */
    public function response($response)
    {
        if (is_object($response) && $response instanceof ResponseValue) {
            /** @var $data ResponseValue */
            $data = $response->getData();
            $params = $response->getParams();
            $formattedResponse = array();
            $formattedResponse['result'] = $data;
            if (isset($params['totalCount'])) {
                $formattedResponse['totalCount'] = $params['totalCount'];
            }

            $formattedResponse['status'] = http_response_code();
        } else {
            $formattedResponse = $response;
        }

        return $formattedResponse;
    }

    /**
     * When the api call results in RestException this method
     * will be called to return the error message
     *
     * @param RestException $exception exception that has reasons for failure
     *
     * @return array
     */
    public function message(RestException $exception)
    {
        $statusCode = $exception->getCode();

        if ($exception->getMessage()) {
            $message = $exception->getMessage();
        } else {
            $message = $exception->getErrorMessage();
        }

        $formattedResponse = array(
            'status' => $statusCode,
            'message' => $message
        );

        return $formattedResponse;
    }
}