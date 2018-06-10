<?php
/**
 * @package    xero-php-sample-app
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace App\Helper;

use XeroPHP\Application;
use XeroPHP\Models\Accounting\Account;
use XeroPHP\Models\Accounting\BankTransaction;
use XeroPHP\Models\Accounting\Contact;
use XeroPHP\Models\Accounting\ContactGroup;

class XeroTestObjects
{
    /**
     * @var Application
     */
    private $xero;

    /**
     * @param Application $xero
     */
    public function __construct(Application $xero)
    {
        $this->xero = $xero;
    }

    /**
     * @return Account
     * @throws \XeroPHP\Remote\Exception
     */
    public function getAccount()
    {
        $code = Strings::random_number();

        $account = new Account($this->xero);
        $account->setName('Sales-' . $code)
            ->setCode($code)
            ->setDescription("This is my original description.")
            ->setType(Account::ACCOUNT_TYPE_REVENUE);
        $account->save();

        return $account;
    }

    /**
     * @return BankTransaction
     * @throws \XeroPHP\Remote\Exception
     */
    public function getBankTransaction()
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

        return $bankTransaction;
    }

    /**
     * @return Contact
     * @throws \XeroPHP\Remote\Exception
     */
    public function getContact()
    {
        $code = Strings::random_number();

        $contact = new Contact($this->xero);
        $contact->setName('Sidney-' . $code)
            ->setFirstName('Sid-' . $code)
            ->setLastName("Maestre - " . $code)
            ->setEmailAddress("sidney" . $code . "@maestre.com");
        $contact->save();

        return $contact;
    }

    /**
     * @throws \XeroPHP\Remote\Exception
     */
    public function getContactGroup()
    {
        $code = Strings::random_number();

        $contactGroup = new ContactGroup($this->xero);
        $contactGroup->setName('Rebels-' . $code);
        $contactGroup->save();

        return $contactGroup;
    }

}