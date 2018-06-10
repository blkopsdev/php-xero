<?php
/**
 * @package    xero-php-sample-app
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace App\Helper;

/**
 * This is a wrapper so Strings::print_r() can distinguish from other assoc arrays and print a nice var output
 *
 * Class VariableCollection
 * @package App\Helper
 */
class VariableCollection extends \ArrayObject
{
}