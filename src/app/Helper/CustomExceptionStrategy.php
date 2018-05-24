<?php
/**
 * @package    xero-php-sample-app
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace App\Helper;

use League\Plates\Engine;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Strategy\ApplicationStrategy;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CustomExceptionStrategy extends ApplicationStrategy
{

    public function getNotFoundDecorator(NotFoundException $exception)
    {
        return $this->getExceptionResponse($exception, 404);
    }

    public function getExceptionDecorator(\Exception $exception)
    {
        return $this->getExceptionResponse($exception, 500);
    }


    private function getExceptionResponse(\Exception $e, $code)
    {
        // Unfortunately need to do this because the standard callable doesn't
        // allow accessing via container so the exception message can't be displayed
        // nicely. Not ideal code duplication here, not not a lot of options
        return function (RequestInterface $request, ResponseInterface $response) use ($e, $code) {
            $plates = new Engine(APP_ROOT . '/src/templates', 'phtml');

            $response->getBody()->write(
                $plates->render('exception', ['exception' => $e, 'code' => $code])
            );

            return $response->withStatus($code);
        };
    }
}