<?php
/**
 * @package    xero-php-sample-app
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace App\Controller;

use App\Helper\Strings;
use League\Route\RouteCollection;
use League\Route\RouteGroup;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use XeroPHP\Models\Accounting\Account;
use XeroPHP\Models\Accounting\Attachment;

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
            $group->post('get/{guid:uuid}', "$controller::getByGUID");
            $group->post('update', "$controller::update");
            $group->post('delete', "$controller::delete");
            $group->post('archive', "$controller::archive");
            $group->post('add-attachment', "$controller::addAttachment");
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


        return $this->jsonCodeResponse($response, $account, 201);
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

        return $this->jsonCodeResponse($response, $accounts);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function getByGUID(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $account = $this->xero->loadByGUID(Account::class, $args['guid']);

        return $this->jsonCodeResponse($response, $account);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function update(ServerRequestInterface $request, ResponseInterface $response)
    {
        // In a real-world case, you'd be loading the from Xero
        // or using the ->setGUID() method on a new instance
        $account = $this->xeroTestObjects->getAccount();
        $account->setDescription('My updated description');
        $account->save();

        return $this->jsonCodeResponse($response, $account);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response)
    {
        // In a real-world case, you'd be loading the from Xero
        // or using the ->setGUID() method on a new instance
        $account = $this->xeroTestObjects->getAccount();
        $account->delete();

        return $this->jsonCodeResponse($response, $account);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function archive(ServerRequestInterface $request, ResponseInterface $response)
    {
        // In a real-world case, you'd be loading the from Xero
        // or using the ->setGUID() method on a new instance
        $account = $this->xeroTestObjects->getAccount();
        $account->setStatus(Account::ACCOUNT_STATUS_ARCHIVED);
        $account->save();

        return $this->jsonCodeResponse($response, $account);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function addAttachment(ServerRequestInterface $request, ResponseInterface $response)
    {
        // In a real-world case, you'd be loading the from Xero
        // or using the ->setGUID() method on a new instance
        $account = $this->xeroTestObjects->getAccount();

        $attachment = Attachment::createFromLocalFile(APP_ROOT . '/data/helo-heroes.jpg');
        $account->addAttachment($attachment);

        return $this->jsonCodeResponse($response, ['$account' => $account, '$attachment' => $attachment]);
    }
}