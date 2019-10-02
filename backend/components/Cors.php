<?php

namespace steroids\components;

use steroids\base\WebApplication;
use yii\base\BootstrapInterface;
use yii\base\Component;

class Cors extends Component implements BootstrapInterface
{
    /**
     * @var array set '*'
     * to allow all domains
     */
    public $allowDomains = [];

    /**
     * @var array
     * set '*' to allow all headers
     */
    public $allowHeaders = ['Origin', 'X-Requested-With', 'Content-Type', 'Accept', 'Authorization'];

    public $allowMethods = ['POST', 'GET', 'OPTIONS'];

    public $allowCredentials = null;
    public $maxAge = 86400;
    public $exposeHeaders = [];

    public function bootstrap($app)
    {
        if ($this->allowDomains && $app instanceof WebApplication) {
            $cors = new \yii\filters\Cors([
                'cors' => [
                    'Origin' => $this->allowDomains,
                    'Access-Control-Request-Method' => $this->allowMethods,
                    'Access-Control-Request-Headers' => $this->allowHeaders,
                    'Access-Control-Allow-Credentials' => $this->allowCredentials,
                    'Access-Control-Max-Age' => $this->maxAge,
                    'Access-Control-Expose-Headers' => $this->exposeHeaders,
                ],
                'request' => \Yii::$app->getRequest(),
                'response' => \Yii::$app->getResponse(),
            ]);

            $requestCorsHeaders = $cors->extractHeaders();
            $responseCorsHeaders = $cors->prepareHeaders($requestCorsHeaders);
            $cors->addCorsHeaders($cors->response, $responseCorsHeaders);

            if ($cors->request->isOptions && $cors->request->headers->has('Access-Control-Request-Method')) {
                // it is CORS preflight request, respond with 200 OK without further processing
                $cors->response->setStatusCode(200);
                \Yii::$app->end();
            }
        }
    }
}
