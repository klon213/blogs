<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
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
     * @var array actions that can be performed without a token
     */
    public $allowedActions = array();


    /**
     * @var array Stores the result of the action
     */
    public $actionResponse = array();


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



    public function sendValidationError($field, $message)
    {
        $this->sendResponse(self::STATUS_BAD_REQUEST, $message);
    }

    /**
     * Verification and authorization token before action
     *
     * @param CAction $action
     * @return bool
     */
    protected function beforeAction($action)
    {
        $postData = json_decode(file_get_contents('php://input'), true);
        if ($postData)
            $_POST = array_merge_recursive($_POST, $postData);

        $user = new User();
        header('Content-type: application/json');

        // authorization is necessary for the action and a token, then check them
        $needAuth = !in_array($action->id, $this->allowedActions);

        $headers = apache_request_headers();

        // trying to login
        if (!empty($headers['Authorization']) && empty($_SERVER['PHP_AUTH_USER']))
        {
            if (!$login = $user->login(array('token' => $headers['Authorization'])))
            {
                $this->sendResponse(self::STATUS_UNAUTHORIZED, $user->getErrors());
            }
        }

        if ($needAuth && empty($login))
        {
            if (empty($user) && !empty($userId))
                $this->sendResponse(404);
            else
                $this->sendResponse(401);
        }

        return true;
    }

    /**
     * Code generation response (200/404) from the result of the action
     *
     * @param CAction $action
     * @see $actionResponse
     */
    protected function afterAction($action)
    {
        $this->sendResponse(!empty($this->actionResponse) ? 200 : 404, $this->actionResponse);
    }


    /**
     *  400 generates an error if the action is not found
     */
    public function actionMissing()
    {
        $this->sendResponse(400, 'Action not found');
    }

    /**
     * Upon successful creation of an action, returns CAction, otherwise generate an error 400
     *
     * @param string $actionID
     * @return CAction|CInlineAction|mixed|null
     */
    public function createAction($actionID)
    {
        if ($action = parent::createAction($actionID))
        {
            return $action;
        }
        else
        {
            $this->actionMissing();
        }
    }
}