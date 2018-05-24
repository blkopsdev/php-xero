<?php
/**
 * @package    xero-php-sample-app
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace App\Controller;

use League\Plates\Engine;
use Psr\Http\Message\ResponseInterface;
use XeroPHP\Application\PublicApplication;
use XeroPHP\Remote\Collection;
use XeroPHP\Remote\Model;
use Zend\Diactoros\Response;

/**
 * This whole class can basically be ignored, it's just a lot of meta-php to return the actual code to the client.
 *
 * Class BaseController
 * @package App\Controller
 */
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


    /**
     * This function is mainly just to use reflection to get the code contents of the calling function
     *
     * @param ResponseInterface $response
     * @param $payload_data
     * @param $http_code
     * @return ResponseInterface
     * @throws \Exception
     */
    protected function jsonCodeResponse(ResponseInterface $response, $payload_data, $http_code)
    {
        //Get calling function
        list(, $caller) = debug_backtrace(false);

        $rc = new \ReflectionClass($caller['class']);
        $rf = $rc->getMethod($caller['function']);

        $filename = $rf->getFileName();

        $start_line = $rf->getStartLine();
        $end_line = $rf->getEndLine() - 3; //Remove the return statement

        $file = new \SplFileObject($filename);
        $file->seek($start_line);

        $function_body = "&lt;?php\n";
        while ($file->key() < $end_line) {
            $function_body .= $file->getCurrentLine();
        }
        //Ensure a consistent ending
        $function_body = rtrim($function_body) . "\n?&gt;";


        $payload_response = self::convertPayloadToString($payload_data);

        //This is all a little messy, but does the job
        $response->getBody()->write(json_encode([
            'function_location' => sprintf('%s#L%d-%d in %s::%s()', $filename, $start_line, $end_line, $rc->getName(), $rf->getName()),
            'function_body' => $function_body,
            'payload_response' => $payload_response
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($http_code);
    }


    /**
     * This restores the original class name int he dump
     *
     * @param $payload_data
     * @return mixed
     */
    private static function convertPayloadToString($payload_data)
    {
        $initial = print_r(self::reduceXeroEntities($payload_data), true);

        if ($payload_data instanceof Collection) {
            if ($payload_data->count()) {
                $class = get_class($payload_data[0]);
            } else {
                $class = null;
            }
        } elseif ($payload_data instanceof Model) {
            $class = get_class($payload_data);
        } else {
            $class = 'unknown';
        }

        return str_replace(\stdClass::class, $class, $initial);
    }


    /**
     * As the Xero objects are containers, they need to have their properties pulled out (recursively)
     *
     * @param $input
     * @return array|\stdClass
     */
    private static function reduceXeroEntities($input)
    {
        if ($input instanceof Model) {
            $temp = new \stdClass();
            foreach ($input::getProperties() as $property => $null) {
                $temp->$property = self::reduceXeroEntities($input->$property);
            }
            return $temp;
        } elseif (is_iterable($input)) {
            $items = [];
            foreach ($input as $key => $item) {
                $items[$key] = self::reduceXeroEntities($item);
            }
            return $items;
        } else {
            return $input;
        }
    }

}