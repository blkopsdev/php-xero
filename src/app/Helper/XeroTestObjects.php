<?php
/**
 * @package    xero-php-sample-app
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace App\Helper;


use XeroPHP\Application;

class XeroObjects
{
    /**
     * @var Application
     */
    private $xero;

    public function __construct(Application $xero)
    {
        $this->xero = $xero;
    }

    public function getLineItem(){
        return
    }
}