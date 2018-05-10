<?php
/**
 * @package    xero-php-sample-app
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace App\Controller;

use League\Plates\Engine;

abstract class BaseController
{
    protected $plates;

    public function __construct(Engine $plates)
    {
        $this->plates = $plates;
    }
}