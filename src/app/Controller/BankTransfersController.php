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
use XeroPHP\Models\Accounting\BankTransaction;
use XeroPHP\Models\Accounting\Contact;

class BankTransactionsController extends BaseController
{
    /**
     * Register the routes for this controller
     *
     * @param RouteCollection $collection
     */
    public static function registerRoutes(RouteCollection $collection)
    {
        $collection->group('bank-transactions', function (RouteGroup $group) {
            $controller = self::class;

            $group->post('create', "$controller::create");
            $group->post('get', "$controller::get");
            $group->post('get/{guid:uuid}', "$controller::getByGUID");
            $group->post('update', "$controller::update");
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
        //Fetch a contact and pick the first one
        $contact = $this->xero->load(Contact::class)->first();

        //Fetch an account bank account to get a valid code
        //this would probably be stored in your application
        $account = $this->xero->load(Account::class)
            ->where('Type', Account::ACCOUNT_TYPE_BANK)
            ->first();

        $bankAccount = (new BankTransaction\BankAccount())
            ->setAccountID($account->AccountID);

        $lineItem = new BankTransaction\LineItem($this->xero);
        $lineItem->setDescription('Some item')
            ->setUnitAmount(100)
            ->setAccountCode('400');

        $bankTransaction = new BankTransaction($this->xero);
        $bankTransaction->setReference('Ref-' . Strings::random_number())
            ->setDate(new \DateTime('2017-01-02'))
            ->setType(BankTransaction::TYPE_RECEIVE)
            ->setBankAccount($bankAccount)
            ->setContact($contact)
            ->addLineItem($lineItem)
            ->setLineAmountType('Exclusive');
        $bankTransaction->save();

        return $this->jsonCodeResponse($response, $bankTransaction, 201);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response)
    {
        $bankTransactions = $this->xero->load(BankTransaction::class)
            ->where('Status', BankTransaction::BANK_TRANSACTION_STATUS_AUTHORISED)
            ->where('Type', BankTransaction::TYPE_RECEIVE)
            ->execute();

        return $this->jsonCodeResponse($response, $bankTransactions);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function getByGUID(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $account = $this->xero->loadByGUID(BankTransaction::class, $args['guid']);

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
        $bankTransaction = $this->xeroTestObjects->getBankTransaction();
        $bankTransaction->setReference('My updated reference');
        $bankTransaction->save();

        return $this->jsonCodeResponse($response, $bankTransaction);
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
        $bankTransaction = $this->xeroTestObjects->getBankTransaction();

        $attachment = Attachment::createFromLocalFile(APP_ROOT . '/data/helo-heroes.jpg');
        $bankTransaction->addAttachment($attachment);

        return $this->jsonCodeResponse($response, ['$bankTransaction' => $bankTransaction, '$attachment' => $attachment]);
    }

}