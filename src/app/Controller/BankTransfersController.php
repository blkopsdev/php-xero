<?php
/**
 * @package    xero-php-sample-app
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace App\Controller;

use League\Route\RouteCollection;
use League\Route\RouteGroup;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use XeroPHP\Models\Accounting\Account;
use XeroPHP\Models\Accounting\Attachment;
use XeroPHP\Models\Accounting\BankTransfer;
use XeroPHP\Models\Accounting\BankTransfer\FromBankAccount;
use XeroPHP\Models\Accounting\BankTransfer\ToBankAccount;

class BankTransfersController extends BaseController
{
    /**
     * Register the routes for this controller
     *
     * @param RouteCollection $collection
     */
    public static function registerRoutes(RouteCollection $collection)
    {
        $collection->group('bank-transfers', function (RouteGroup $group) {
            $controller = self::class;

            $group->post('create', "$controller::create");
            $group->post('get', "$controller::get");
            $group->post('get/{guid:uuid}', "$controller::getByGUID");
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
        $bankAccounts = $this->xero->load(Account::class)
            ->where('Type', Account::ACCOUNT_TYPE_BANK);

        //[BankTransfers:Create]
        $fromBankAccount = new FromBankAccount();
        $fromBankAccount->setAccountId($bankAccounts->first()->AccountID);

        $toBankAccount = new ToBankAccount();
        $toBankAccount->setAccountId($bankAccounts->last()->AccountID);


        $bankTransfer = new BankTransfer($this->xero);
        $bankTransfer->setDate(new \DateTime('2017-01-02'))
            ->setToBankAccount($toBankAccount)
            ->setFromBankAccount($fromBankAccount)
            ->setAmount(50.00);
        $bankTransfer->save();

        return $this->jsonCodeResponse($response, $bankTransfer);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response)
    {
        $bankTransfers = $this->xero->load(BankTransfer::class)
            ->execute();

        return $this->jsonCodeResponse($response, $bankTransfers);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function getByGUID(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $account = $this->xero->loadByGUID(BankTransfer::class, $args['guid']);

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
        $bankTransfer = $this->xero->load(BankTransfer::class)->first();

        $attachment = Attachment::createFromLocalFile(APP_ROOT . '/data/helo-heroes.jpg');
        $bankTransfer->addAttachment($attachment);

        return $this->jsonCodeResponse($response, ['$bankTransfer' => $bankTransfer, '$attachment' => $attachment]);
    }

}