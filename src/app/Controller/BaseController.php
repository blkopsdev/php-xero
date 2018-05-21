<?php
/**
 * @package    xero-php-sample-app
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace App\Controller;

use League\Plates\Engine;
use XeroPHP\Application\PublicApplication;

abstract class BaseController
{
    /**
     * @var Engine
     */
    protected $plates;

    /**
     * @var PublicApplication
     */
    protected $xero;

    public function __construct(Engine $plates, PublicApplication $xero)
    {
        $this->plates = $plates;
        $this->xero = $xero;
    }
}