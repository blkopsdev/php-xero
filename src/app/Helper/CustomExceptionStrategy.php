<?php
/**
 * @package    xero-php-sample-app
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace App\Helper;

use App\Controller\ExceptionController;
use League\Route\Strategy\ApplicationStrategy;

class CustomExceptionStrategy extends ApplicationStrategy
{
    public function getExceptionDecorator(\Exception $e){
        return sprintf('%s::displayException', ExceptionController::class);
    }
}