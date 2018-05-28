<?php
/**
 * @package    xero-php-sample-app
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace App\Controller;

use App\Helper\Strings;
use League\Route\RouteCollection;
use League\Route\RouteGroup;
use League\Route\Strategy\JsonStrategy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use XeroPHP\Models\Accounting\Account;

class AccountsController extends BaseController
{
    /**
     * Register the routes for this controller
     *
     * @param RouteCollection $collection
     */
    public static function registerRoutes(RouteCollection $collection)
    {
        $collection->group('accounts', function (RouteGroup $group) {
            $controller = self::class;

            $group->post('create', "$controller::create");
            $group->post('get', "$controller::get");
            $group->post('get/{guid}', "$controller::getByGUID");
            $group->post('update', "$controller::update");
            $group->post('delete', "$controller::delete");

        });
    }


    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function create(ServerRequestInterface $request, ResponseInterface $response)
    {
        $code = Strings::random_number();

        $account = new Account($this->xero);
        $account->setName('Sales-' . $code)
            ->setCode($code)
            ->setDescription("This is my original description.")
            ->setType(Account::ACCOUNT_TYPE_REVENUE);
        $account->save();


        return $this->jsonCodeResponse($response, $account, 200);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response)
    {
        $accounts = $this->xero->load(Account::class)
            ->where('Type', Account::ACCOUNT_TYPE_BANK)
            ->execute();

        return $this->jsonCodeResponse($response, $accounts, 200);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function getByGUID(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $accounts = $this->xero->loadByGUID(Account::class, $args['guid']);

        return $this->jsonCodeResponse($response, $accounts, 200);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function update(ServerRequestInterface $request, ResponseInterface $response)
    {
        $accounts = $this->xero->load(Account::class)->execute();

        return $this->jsonCodeResponse($response, $accounts, 200);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response)
    {
        $accounts = $this->xero->load(Account::class)->execute();

        return $this->jsonCodeResponse($response, $accounts, 200);
    }
    
}