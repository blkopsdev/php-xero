<?php
/**
 * @package    xero-php-sample-app
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace App\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ApplicationController extends BaseController
{
    public function index(RequestInterface $request, ResponseInterface $response)
    {
        $response->getBody()->write(
            $this->plates->render('index', [])
        );

        return $response->withStatus(200);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function connect(RequestInterface $request, ResponseInterface $response)
    {
        $response->getBody()->write(
            $this->plates->render('connect', [])
        );

        return $response->withStatus(200);
    }
}