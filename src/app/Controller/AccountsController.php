<?php
/**
 * @package    xero-php-sample-app
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use XeroPHP\Models\Accounting\Account;
use XeroPHP\Models\Accounting\User;

class AccountsController extends BaseController
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response)
    {
        $accounts = $this->xero->load(User::class)->execute();

        return $this->jsonCodeResponse($response, $accounts, 200);
    }
}