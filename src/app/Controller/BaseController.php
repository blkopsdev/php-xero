<?php
/**
 * @package    xero-php-sample-app
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace App\Controller;

use League\Plates\Engine;
use Psr\Http\Message\ResponseInterface;
use XeroPHP\Application\PublicApplication;
use App\Helper\Strings;
use XeroPHP\Remote\Collection;
use XeroPHP\Remote\Model;

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

        //This is all a little messy, but does the job
        $response->getBody()->write(json_encode([
            'function_location' => sprintf('%s#L%d-%d in %s::%s()', $filename, $start_line, $end_line, $rc->getName(), $rf->getName()),
            'function_body' => $function_body,
            'payload_response' => Strings::print_r($payload_data)
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($http_code);
    }
}