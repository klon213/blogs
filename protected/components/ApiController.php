<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 02.09.14
 * Time: 15:55
 */
class ApiController extends CController
{
    /**
     * Constant status
     */
    const STATUS_OK = 200;
    const STATUS_NOT_MODIFIED = 304;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_UNAUTHORIZED = 401;
    const STATUS_FORBIDDEN = 403;
    const STATUS_NOT_FOUND = 404;
    const STATUS_INTERNAL_SERVER_ERROR = 500;

    /**
     * Get a description of the response code
     *
     * @param $code
     * @return bool
     */

    public function getStatusMessage($code)
    {
        $messages = array(
            200 => 'OK',
            304 => 'Not Modified',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            420 => 'API Limitations',
            500 => 'Internal Server Error',
        );

        return !empty($messages[$code]) ? $messages[$code] : false;
    }
    /**
     * Response
     *
     * @param array|int|CActiveRecord $code status or response data
     * @param array|int|CActiveRecord $data
     */

    public function sendResponse($code, $data = array())
    {
        $response = array();

        if (!is_int($code))
        {
            $data = $code;
            $code = 200;
        }

        // data is empty
        if (!is_array($data) && empty($data))
        {
            $code = 404;
        }

        // if data is model and has errors
        if (is_a($data, 'CActiveRecord'))
        {
            if ($data->hasErrors())
            {
                $code = 400;
                $data = $data->getErrors();
            }
        }

        $statusMessage = $this->getStatusMessage($code);

        if ($code >= 200 && $code < 400)
        {

            if (!$isArray = is_array($data)) $data = array($data);

            foreach ($data as &$model)
            {
                if (is_object($model) && method_exists($model, 'toJSON'))
                    $model = $model->toJSON();
            }

            $response['data'] = $isArray ? $data : reset($data);
        }
        else
        {
            if (!empty($data))
            {
                $response['errors'] = array();
                $response['errorMessages'] = array();
                if (is_array($data))
                {
                    $response['errors'] = $data;
                    foreach($data as $field => $errors)
                    {
                        $response['errorMessages'] = array_merge($response['errorMessages'], is_array($errors) ? $errors : array());
                    }
                }
                else
                {
                    $response['errorMessages'][] = $data;
                    $response['errors']['app'][] = $data;
                }
            }
        }

        header("HTTP/1.0 {$code} {$statusMessage}");
        header('Content-Type: application/json');

        echo CJSON::encode($response);
        Yii::app()->end();
    }
}