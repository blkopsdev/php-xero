<?php
/**
 * @package    xero-php-sample-app
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace App\Helper;


use XeroPHP\Remote\Collection;
use XeroPHP\Remote\Model;

class Strings
{
    /**
     * Generate a random number of x length
     *
     * @param int $length
     * @return string
     */
    public static function random_number($length = 6)
    {
        $min = pow(10, $length);
        $max = $min * 10 - 1;

        return (string)rand($min, $max);
    }


    /**
     * Almost print_r(), but:
     * - Displays Collections like Arrays
     * - Displays only the Xero properties of Models
     * - Shows null as 'null'
     * - Shows scalars how they'd look in PHP-land
     *
     * @param $payload_data
     * @param int $level
     * @return mixed
     */
    public static function print_r($payload_data, $level = 0)
    {
        static $single_indent = '    ';
        $indent = str_repeat($single_indent, $level);

        if (is_null($payload_data)) {
            return 'null';
        } elseif (is_scalar($payload_data)) {
            //Var export makes types clearer
            return var_export($payload_data, true);
        } elseif ($payload_data instanceof VariableCollection) {
            $r = '';
            foreach ($payload_data as $key => $value){
                $r .= sprintf("%s$%s => %s\n", $indent, $key, self::print_r($value, $level + 1));
            }
            return $r;
        } elseif ($payload_data instanceof Collection) {
            $assoc_data = $payload_data->getArrayCopy();
        } elseif ($payload_data instanceof Model) {
            //Map model properties to the assoc data
            $assoc_data = $payload_data::getProperties();
            array_walk($assoc_data, function (&$value, $key) use ($payload_data) {
                $value = $payload_data->$key;
            });
        } else {
            $assoc_data = (array)$payload_data;
        }

        if (is_object($payload_data)) {
            $r = sprintf("%s Object\n", get_class($payload_data));
        } else {
            $r = "Array\n";
        }

        $r .= sprintf("%s(\n", $indent);
        foreach ($assoc_data as $key => $value) {
            $r .= sprintf("%s%s[%s] => %s\n", $indent, $single_indent, $key, self::print_r($value, $level + 2));
        }
        $r .= sprintf("%s)\n", $indent);

        return $r;

    }


}