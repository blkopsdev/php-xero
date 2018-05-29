<?php
class ExampleClass
{
	public $xero;

	function __construct() {

   	}







	public function createBankTransfer($xero)
	{
		$str = '';

		$account = $this->getBankAccount($xero);

		if (count($account) > 1) {

			//[BankTransfers:Create]
			$fromBankAccount = new \XeroPHP\Models\Accounting\BankTransfer\FromBankAccount($xero);
			$fromBankAccount->setCode($account[0]["Code"])
				->setAccountId($account[0]["AccountId"]);

			$toBankAccount = new \XeroPHP\Models\Accounting\BankTransfer\ToBankAccount($xero);
			$toBankAccount->setCode($account[1]["Code"])
				->setAccountId($account[1]["AccountId"]);

			$banktransfer = new \XeroPHP\Models\Accounting\BankTransfer($xero);
			$banktransfer->setDate(new DateTime('2017-01-02'))
				->setToBankAccount($toBankAccount)
				->setFromBankAccount($fromBankAccount)
				->setAmount("50");
			$banktransfer->save();
			//[/BankTransfers:Create]

			$str = $str ."Create Bank Transfer: " . $banktransfer["BankTransferID"] . " -- $" . $banktransfer["Amount"] . "<br>" ;

		} else {
			$str = $str ."Found less than 2 Bank Accounts  - can't work with Bank Transfers without 2. ";
		}

		return $str;
	}


	public function getBrandingTheme($xero)
	{
		$str = '';

		//[BrandingThemes:Read]
		$brandingtheme = $xero->load('Accounting\\BrandingTheme')->execute();
		//[/BrandingThemes:Read]

		$str = $str ."Get BrandingThemes: " . count($brandingtheme) . "<br>";

		return $str;
	}

	public function getContact($xero,$returnObj=false)
	{
		$str = '';

		//[Contacts:Read]
		$contacts = $xero->load('Accounting\\Contact')->execute();
		//[/Contacts:Read]

		$str = $str ."Get Contacts: " . count($contacts) . "<br>";
	
		// GET Contact with WHERE CLAUSE
		$where = $xero->load('Accounting\\Contact')->where('
			    ContactStatus=="' . \XeroPHP\Models\Accounting\Contact::CONTACT_STATUS_ACTIVE . '" 
			')->execute();
		if (count($where)) {
			$str = $str ."Get an Contact where Status is active: " . $where[0]["Name"] . "<br>";
		} else {
			$str = $str ."No Active Contact found";					
		}

		if($returnObj) {
			return $contacts[0];
		} else {
			return $str;
		}
	}

	public function createContact($xero,$returnObj=false)
	{
		$str = '';
		
		//[Contacts:Create]
		$contact = new \XeroPHP\Models\Accounting\Contact($xero);
		$contact->setName('Sidney-' . $this->getRandNum())
			->setFirstName('Sid-' . $this->getRandNum())
			->setLastName("Maestre - " . $this->getRandNum())
		    ->setEmailAddress("sidney" . $this->getRandNum() . "@maestre.com");
		$contact->save();
		//[/Contacts:Create]

		$str = $str ."Create Contact: " . $contact["Name"] . " -- " . $contact["EmailAddress"] . "<br>";

		if($returnObj) {
			return $contact;
		} else {
			return $str;
		}
	}

	public function updateContact($xero)
	{
		$str = '';
		
		$new = $this->getContact($xero,true);
		$guid = $new['ContactID'];

		//[Contacts:Update]
		$contact = $xero->loadByGUID('Accounting\\Contact', $guid);
		$contact->setName('Sid-' . $this->getRandNum())
			->setContactStatus(NULL);
		$contact->save();
		//[/Contacts:Update]

		$str = $str ."Update Contact: " . $contact["Name"] . " -- " . $contact["EmailAddress"] . "<br>";

		return $str;
	}
	
	public function archiveContact($xero)
	{
		$str = '';

		$new = $this->getContact($xero,true);
		$guid = $new['ContactID'];

		//[Contacts:Archive]
		$contact = $xero->loadByGUID('Accounting\\Contact', $guid);		
		$contact->setContactStatus(\XeroPHP\Models\Accounting\Contact::CONTACT_STATUS_ARCHIVED);
		$contact->save();
		//[/Contacts:Archive]
				
		$str = $str ."Archive Contact: " . $contact["Name"] . "<br>";

		return $str;
	}

	public function getContactGroup($xero,$returnObj=false)
	{
		$str = '';

		//[ContactGroups:Read]
		$contactgroup = $xero->load('Accounting\\ContactGroup')->execute();
		//[/ContactGroups:Read]

		$str = $str ."Get Contact Groups: " . count($contactgroup) . "<br>";
	
		$where = $xero->load('Accounting\\ContactGroup')->where('Status=="ACTIVE"')->execute();
		if (count($where)) {
			$str = $str . "Get an ContactGroup where Status is active: " . $where[0]["Name"] . "<br>";
		} else {
			$str = $str . "No Active ContactGroup found";					
		}

		if($returnObj) {
			return $contactgroup[0];
		} else {
			return $str;
		}
	}

	public function createContactGroup($xero,$returnObj=false)
	{
		$str = '';
		
		$contact = $xero->load('Accounting\\Contact')->execute();

		//[ContactGroups:Create]
		$contactgroup = new \XeroPHP\Models\Accounting\ContactGroup($xero);
		$contactgroup->setName('Rebels-' . $this->getRandNum());
		$contactgroup->save();
		$contactgroup->addContact($contact[0]);
		$contactgroup->save();
		//[/ContactGroups:Create]

		$str = $str . "Create ContactGroup: " . $contactgroup["Name"]  .  "<br>" ;

		if($returnObj) {
			return $contactgroup;
		} else {
			return $str;
		}
	}

	public function updateContactGroup($xero)
	{
		$str = '';		

		$new = $this->createContactGroup($xero,true);
		$guid = $new['ContactGroupID'];

		//[ContactGroups:Update]
		$contactgroup = $xero->loadByGUID('Accounting\\ContactGroup', $guid);		
		$contactgroup->setName('First Order-' . $this->getRandNum());
		$contactgroup->save();
		//[/ContactGroups:Update]

		$str = $str . "Update ContactGroup: " . $contactgroup["Name"] .  "<br>" ;

		return $str;
	}

	public function archiveContactGroup($xero)
	{
		$str = '';		

		$new = $this->createContactGroup($xero,true);
		$guid = $new['ContactGroupID'];

		//[ContactGroups:Archive]
		$contactgroup = $xero->loadByGUID('Accounting\\ContactGroup', $guid);		
		$contactgroup->setStatus("DELETED");
		$contactgroup->save();
		//[/ContactGroups:Archive]
		
		$str = $str . "Delete ContactGroup: " . $contactgroup["Name"] . "<br>" ;

		return $str;
	}

	public function removeContactFromContactGroup($xero)
	{
		$str = '';		

		$contact = $xero->load('Accounting\\Contact')->execute();

		$new = $this->createContactGroup($xero,true);
		$guid = $new['ContactGroupID'];

		// DOES NOT Work
		//[ContactGroups:Update]
		//$contactgroup = new \XeroPHP\Models\Accounting\ContactGroup($xero);
		//$contactgroup->setContactGroupID($guid);

		//$contactgroup = $xero->loadByGUID('Accounting\\ContactGroup', $guid);		
		//$contactgroup->addContact($contact[0]);
		//$contactgroup->delete();
		//[/ContactGroups:Update]

		$str = $str . "Remove Contacts ContactGroup: " . $contactgroup["Name"] .  "<br>" ;

		return $str;
	}

	public function getCreditNote($xero,$returnObj=false)
	{
		$str = '';

		//[CreditNotes:Read]
		$creditnotes = $xero->load('Accounting\\CreditNote')->execute();
		//[/CreditNotes:Read]

		$str = $str ."Get CreditNotes: " . count($creditnotes) . "<br>";

		$where = $xero->load('Accounting\\CreditNote')->where('
			    Type=="' . \XeroPHP\Models\Accounting\CreditNote::CREDIT_NOTE_TYPE_ACCPAYCREDIT . '" 
			')->execute();
		if (count($where)) {
			$str = $str ."Get an CreditNote where Type is ACCPAYCREDIT: " .  date_format($where[0]["Date"], 'Y-m-d') . " -- $" . $where[0]["Total"] . "<br>";
		} else {
			$str = $str ."No Active CreditNote found";					
		}

		if($returnObj) {
			return $creditnotes[0];
		} else {
			return $str;
		}
	}

	public function createCreditNote($xero,$returnObj=false)
	{
		$str = '';
		
		$lineitem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
		$lineitem->setDescription('Credit Note-' . $this->getRandNum())
			->setQuantity(2)
			->setUnitAmount(20)
			->setAccountCode("400");

		$contact = $this->getContact($xero,true);

		//[CreditNotes:Create]
		$creditnote = new \XeroPHP\Models\Accounting\CreditNote($xero);
		$creditnote->setDate(new DateTime('2017-01-02'))
			->setContact($contact)
			->addLineItem($lineitem)
			->setType(\XeroPHP\Models\Accounting\CreditNote::CREDIT_NOTE_TYPE_ACCPAYCREDIT);
		$creditnote->save();
		//[/CreditNotes:Create]

		
		$str = $str ."Create CreditNote: " . date_format($creditnote["Date"], 'Y-m-d') . " -- $" . $creditnote["Total"] . "<br>" ;

		if($returnObj) {
			return $creditnote;
		} else {
			return $str;
		}
	}

	public function updateCreditNote($xero)
	{
		$str = '';

		$new = $this->createCreditNote($xero,true);
		$guid = $new['CreditNoteID'];

		//[CreditNotes:Update]
		$creditnote = $xero->loadByGUID('Accounting\\CreditNote', $guid);		
		$creditnote->setDate(new DateTime('2018-01-28'));
		$creditnote->save();
		//[/CreditNotes:Update]
		
		$str = $str ."Update CreditNote: " . date_format($creditnote["Date"], 'Y-m-d') . " -- $" . $creditnote["Total"] .  "<br>" ;

		return $str;
	}

	public function allocateCreditNote($xero)
	{
		$str = '';

		$newInv = $this->createInvoiceAccPay($xero,true);
		$invGuid = $newInv["InvoiceID"];
		$new = $this->createCreditNoteAuthorised($xero,true);
		$guid = $new["CreditNoteID"];

		//[CreditNotes:Allocate]
		$creditnote = $xero->loadByGUID('Accounting\\CreditNote', $guid);
		
		$invoice = new \XeroPHP\Models\Accounting\Invoice($xero);
		$invoice->setInvoiceID($invGuid);

		$allocation = new \XeroPHP\Models\Accounting\CreditNote\Allocation($xero);
		$allocation->setInvoice($invoice)
			->setAppliedAmount("2.00");
		
		$creditnote->addAllocation($allocation);
		$creditnote->save();
		//[/CreditNotes:Allocate]
		
		$str = $str . "Allocate CreditNote: " . $new["CreditNoteID"] . "<br>" ;
		
		return $str;
		
	}

	public function refundCreditNote($xero)
	{
		$str = '';

		$creditnote = $this->createCreditNoteAuthorised($xero);
		$account = $this->getBankAccount($xero);

		//[CreditNotes:Refund]
		$creditnote = new \XeroPHP\Models\Accounting\Payment($xero);
		$creditnote->setCreditNote($creditnote)
			->setAccount($account[0])
			->setAmount("2.00");
		$creditnote->save();
		//[/CreditNotes:Refund]
		
		$str = $str . "Create CreditNote Refund : " . $creditnote["PaymentID"] . "<br>" ;
		
		return $str;
	}	

	public function deleteCreditNote($xero)
	{
		$str = '';

		$new = $this->createCreditNote($xero,true);
		$guid = $new["CreditNoteID"];
		
		//[CreditNotes:Delete]
		$creditnote = new \XeroPHP\Models\Accounting\CreditNote($xero);
		$creditnote->setCreditNoteID($guid)
			->setStatus(\XeroPHP\Models\Accounting\Invoice::INVOICE_STATUS_DELETED);
		$creditnote->save();
		//[/CreditNotes:Delete]

		$str = $str . "Deleted CreditNote: " . $creditnote["CreditNoteID"] . "<br>" ;

		return $str;
	}

	public function voidCreditNote($xero)
	{
		$str = '';

		$new = $this->createCreditNoteAuthorised($xero,true);
		$guid = $new["CreditNoteID"];
		
		//[CreditNotes:Void]
		$creditnote = new \XeroPHP\Models\Accounting\CreditNote($xero);
		$creditnote->setCreditNoteID($guid)
			->setStatus(\XeroPHP\Models\Accounting\Invoice::INVOICE_STATUS_VOIDED);
		$creditnote->save();
		//[/CreditNotes:Void]

		$str = $str . "Void CreditNote: " . $creditnote["CreditNoteID"] . "<br>" ;

		return $str;
	}

	public function getCurrency($xero)
	{
		$str = '';

		//[Currencies:Read]
		$currencies = $xero->load('Accounting\\Currency')->execute();
		//[/Currencies:Read]

		$str = $str . "Get Currency: " . count($currencies) . "<br>";

		if (count($currencies)) {
			$str = $str . "First Currency found: " .  $currencies[0]["Code"] . " -- $" . $currencies[0]["Description"] . "<br>";
		} else {
			$str = $str . "No Currency found";					
		}	

		return $str;
	}	


	public function getEmployee($xero,$returnObj=false)
	{
		$str = '';

		//[Employees:Read]
		$employees = $xero->load('Accounting\\Employee')->execute();
		//[/Employees:Read]

		$str = $str . "Get Employee: " . count($employees) . "<br>";

		$where = $xero->load('Accounting\\Employee')->where('
			    Status=="' . \XeroPHP\Models\Accounting\Contact::CONTACT_STATUS_ACTIVE . '" 
			')->execute();
		if (count($where)) {
			$str = $str . "Get an Employee where Status is active: " . $where[0]["FirstName"] . " " . $where[0]["LastName"] . "<br>";
		} else {
			$str = $str . "No Active Employee found";					
		}

		if($returnObj) {
			return $employees[0];
		} else {
			return $str;
		}
	}	

	public function createEmployee($xero,$returnObj=false)
	{
		$str = '';

		//[Employees:Create]
		$employee = new \XeroPHP\Models\Accounting\Employee($xero);
		$employee->setFirstName('Sid-' . $this->getRandNum())
			->setLastName("Maestre - " . $this->getRandNum());
		$employee->save();
		//[/Employees:Create]
		
		$str = $str . "Create Employee: " . $employee["FirstName"] . "  " . $employee["LastName"]  . "<br>" ;

		if($returnObj) {
			return $employee;
		} else {
			return $str;
		}
	}	

	public function updateEmployee($xero)
	{
		$str = '';

		$new = $this->createEmployee($xero,true);
		$guid = $new['EmployeeID'];	

		//[Employees:Update]
		$employee = $xero->loadByGUID('Accounting\\Employee', $guid);
		$employee->setFirstName('Sid-' . $this->getRandNum());
		$employee->save();
		//[/Employees:Update]
		
		$str = $str . "Update Employee: " . $employee["FirstName"] . "  " . $employee["LastName"]   . "<br>" ;

		return $str;
	}	

	public function getExpenseClaim($xero,$returnObj=false)
	{
		$str = '';

		//[ExpenseClaims:Read]
		$expenseclaims = $xero->load('Accounting\\ExpenseClaim')->execute();
		//[/ExpenseClaims:Read]
		
		$str = $str . "Get ExpenseClaim: " . count($expenseclaims) . "<br>";

		$where = $xero->load('Accounting\\ExpenseClaim')->where('
			    Status=="' . \XeroPHP\Models\Accounting\ExpenseClaim::EXPENSE_CLAIM_STATUS_SUBMITTED . '" 
			')->execute();
		if (count($where)) {
			$str = $str . "Found an ExpenseClaim where status is Submitted: ID is " . $where[0]["ExpenseClaimID"] . " by " . $where[0]["User"]["FirstName"] . " " . $where[0]["User"]["LastName"] . "<br>";
		} else {
			$str = $str . "No ExpenseClaim of Status Submitted found";					
		}

		if($returnObj) {
			return $expenseclaims[0];
		} else {
			return $str;
		}
	}	

	public function createExpenseClaim($xero,$returnObj=false)
	{
		$str = '';

		$all = $xero->load('Accounting\\User')->execute();
		$userGuid = $all[0]["UserID"];
		$lineitem = $this->getLineItemForReceipt($xero);
		$contact = $this->getContact($xero,true);

		if (count($all)) {	
			//[ExpenseClaims:Create]
			$user = new \XeroPHP\Models\Accounting\User($xero);
			$user->setUserID($userGuid);

			$receipt = new \XeroPHP\Models\Accounting\Receipt($xero);
			$receipt->setDate(new DateTime('2017-01-02'))
				->setContact($contact)
				->addLineItem($lineitem)
				->setUser($user);
			$receipt->save();

			$expenseclaim = new \XeroPHP\Models\Accounting\ExpenseClaim($xero);
			$expenseclaim->setUser($user)
				->addReceipt($receipt);
			$expenseclaim->save();
			//[/ExpenseClaims:Create]

			$str = $str ."Created Expense Claim: " . $expenseclaim["ExpenseClaimID"] . "<br>" ;
		}

		if($returnObj) {
			return $expenseclaim;
		} else {
			return $str;
		}
	}	

	public function updateExpenseClaim($xero)
	{
		$str = '';
		
		$all = $xero->load('Accounting\\User')->execute();
		$userGuid = $all[0]["UserID"];

		$new = $this->createExpenseClaim($xero,true);
		$guid = $new["ExpenseClaimID"];
		
		$lineitem = $this->getLineItemForReceipt($xero);
		$contact = $this->getContact($xero,true);

		if (count($all)) {

			//[ExpenseClaims:Update]
			$expenseclaim = $xero->loadByGUID('Accounting\\ExpenseClaim', $guid);

			$user = new \XeroPHP\Models\Accounting\User($xero);
			$user->setUserID($userGuid);
			
			$receipt = new \XeroPHP\Models\Accounting\Receipt($xero);
			$receipt->setDate(new DateTime('2017-01-02'))
				->setContact($contact)
				->addLineItem($lineitem)
				->setUser($user);
			$receipt->save();
		
			$expenseclaim->addReceipt($receipt)
				->setStatus(\XeroPHP\Models\Accounting\ExpenseClaim::EXPENSE_CLAIM_STATUS_AUTHORISED);			
			$expenseclaim->save();
			//[/ExpenseClaims:Update]
			
			$str = $str . "Updated Expense Claim: " . $expenseclaim["ExpenseClaimID"] . "<br>" ;
		}
		
		return $str;
	}	

	public function getInvoice($xero,$returnObj=false)
	{
		$str = '';

		//[Invoices:Read]
		$invoices = $xero->load('Accounting\\Invoice')->execute();
		//[/Invoices:Read]

		$str = $str . "Get Invoices: " . count($invoices) . "<br>";

		$where = $xero->load('Accounting\\Invoice')->where('
			    Status=="' . \XeroPHP\Models\Accounting\Invoice::INVOICE_STATUS_AUTHORISED . '" 
			')->execute();
		if (count($where)) {
			$str = $str . "Found " . count($where) . " Invoice(s) where status is Authorised<br>";
		} else {
			$str = $str . "No Invoice of Status Authorised found";					
		}

		if($returnObj) {
			return $invoices[0];
		} else {
			return $str;
		}
	}	

	public function createInvoice($xero,$returnObj=false)
	{
		$str = '';
		
		$contact = $this->getContact($xero,true);
		$lineitem = $this->getLineItemForInvoice($xero,true);

		//[Invoices:Create]
		$invoice = new \XeroPHP\Models\Accounting\Invoice($xero);
		$invoice->setReference('Ref-' . $this->getRandNum())
			->setDueDate(new DateTime('2017-03-02'))
			->setType(\XeroPHP\Models\Accounting\INVOICE::INVOICE_TYPE_ACCREC)
			->addLineItem($lineitem)
			->setContact($contact)
			->setLineAmountType("Exclusive");
		$invoice->save();
		//[/Invoices:Create]
		
		$str = $str . "Create Invoice: " . $invoice["Reference"] . " -- $" . $invoice["Total"] . "<br>" ;

		if($returnObj) {
			return $invoice;
		} else {
			return $str;
		}
	}	

	public function updateInvoice($xero)
	{
		$str = '';
		$new = $this->createInvoice($xero,true);
		$guid = $new['InvoiceID'];

		//[Invoices:Update]
		$invoice = $xero->loadByGUID('Accounting\\Invoice', $guid);
		$invoice->setReference('Ref-' . $this->getRandNum());
		$invoice->save();
		//[/Invoices:Update]

		$str = $str . "Update Invoice: " . $invoice["Reference"] . " -- " . $invoice["Total"] . "<br>" ;

		return $str;
	}
			
	public function deleteInvoice($xero)
	{
		$str = '';

		$new = $this->createInvoice($xero,true);
		$guid = $new['InvoiceID'];

		//[Invoices:Delete]
		$invoice = $xero->loadByGUID('Accounting\\Invoice', $guid);
		$invoice->setStatus(\XeroPHP\Models\Accounting\Invoice::INVOICE_STATUS_DELETED);
		$invoice->save();
		//[/Invoices:Delete]

		$str = $str . "Delete Invoice";

		return $str;
	}

	public function voidInvoice($xero)
	{
		$str = '';

		$new = $this->createInvoiceAuthorised($xero,true);
		$guid = $new['InvoiceID'];

		//[Invoices:Void]
		$invoice = $xero->loadByGUID('Accounting\\Invoice', $guid);
		$invoice->setStatus(\XeroPHP\Models\Accounting\Invoice::INVOICE_STATUS_VOIDED);
		$invoice->save();
		//[/Invoices:Void]

		$str = $str . "Void Invoice";

		return $str;
	}

	public function getInvoiceReminder($xero)
	{
		$str = '';

		//[InvoiceReminders:Read]
		$invoicereminders = $xero->load('Accounting\\InvoiceReminder')->execute();
		//[/InvoiceReminders:Read]

		$str = $str . "Invoice Reminder Enabled?: ";
		if ($invoicereminders[0]['Enabled'] == 1) {
			$str = $str . "YES";
		} else {
			$str = $str ."NO";
		}

		return $str;
	}

	public function getItem($xero,$returnObj=false)
	{
		$str = '';

		//[Items:Read]
		$items = $xero->load('Accounting\\Item')->execute();
		//[/Items:Read]

		$str = $str . "Get Items: " . count($items) . "<br>";

		$where = $xero->load('Accounting\\Item')->where('IsSold==true')->execute();
		if (count($where)) {
			$str = $str . "Get an Item where Is Sold is True - Name:" . $where[0]["Name"] . " Code: " . $where[0]["Code"] . "<br>";
		} else {
			$str = $str . "No Item of Is Sold flagged as true";					
		}

		if($returnObj) {
			return $items[0];
		} else {
			return $str;
		}
	}	

	public function createItem($xero,$returnObj=false)
	{
		$str = '';

		//[Items:Create]
		$item = new \XeroPHP\Models\Accounting\Item($xero);
		$item->setName('My Item-' . $this->getRandNum())
			->setCode($this->getRandNum())
			->setDescription("This is my Item description.")
			->setIsTrackedAsInventory(false);
		$item->save();
		//[/Items:Create]
		
		$str = $str . "Create item: " . $item["Name"] . " -- " . $item["Description"] . "<br>" ;
		
		if($returnObj) {
			return $item;
		} else {
			return $str;
		}
	}

	public function updateItem($xero)
	{
		$str = '';
	
		$new = $this->createItem($xero,true);
		$guid = $new['ItemID'];
	
		//[Items:Update]
		$item = $xero->loadByGUID('Accounting\\Item', $guid);
		$item->setName('My Item-' . $this->getRandNum());
		$item->save();
		//[/Items:Update]

		$str = $str . "Update item: " . $item["Name"] . " -- " . $item["Description"] . "<br>";
		
		return $str;
	}
	
	public function deleteItem($xero)
	{
		$str = '';
	
		$new = $this->createItem($xero,true);
		$guid = $new['ItemID'];
	
		//[Items:Delete]
		$item = $xero->loadByGUID('Accounting\\Item', $guid);
		$item->delete();
		//[/Items:Delete]

		$str = $str . "Delete item: " . $item["Name"] . "<br>" ;

		return $str;
	}			


	public function getJournal($xero)
	{
		$str = '';

		//[Journals:Read]
		$journals = $xero->load('Accounting\\Journal')->execute();
		//[/Journals:Read]

		$str = $str . "Get first 100 Journals Total: " . count($journals) . "<br>";

		// GET ITEM with WHERE CLAUSE
		$offset = $xero->load('Accounting\\Journal')->offset('100')->execute();
		$str = $str . "Get Offset 100 Journals Total: " . count($offset) . "<br>";
			
		foreach ($offset as $key => $value) {
		    $str = $str . ' Date: ' .  date_format($value["JournalDate"], 'Y-m-d') . ' : ' . $value["JournalNumber"] . '<br>';
		}
		
		return $str;
	}

	public function getLinkedTransaction($xero,$returnObj=false)
	{
		$str = '';

		//[LinkedTransactions:Read]
		$linkedtransactions = $xero->load('Accounting\\LinkedTransaction')->execute();
		//[/LinkedTransactions:Read]

		$str = $str . "Get LinkedTransactions: " . count($linkedtransactions) . "<br>";

		if($returnObj) {
			return $linkedtransactions[0];
		} else {
			return $str;
		}
	}


	public function createLinkedTransaction($xero,$returnObj=false)
	{
		$str = '';

		$new = $this->createInvoiceAccPay($xero,true);
		$guid = $new['InvoiceID'];
	
		// BUG? *****
		// For some reason the LineItemID is NULL in the Object returned from creating a new Invoice
		// So, I'm hitting the Invoice endpoint to GET the invoice data again.
		//[LinkedTransactions:Create]
		$invoice = $xero->loadByGUID('Accounting\\Invoice', $guid);
		$lineitemid = $invoice['LineItems'][0]["LineItemID"];

		$linkedtransaction = new \XeroPHP\Models\Accounting\LinkedTransaction($xero);
		$linkedtransaction->setSourceTransactionID($guid)
			->setSourceLineItemID($lineitemid);
		$linkedtransaction->save();
		//[/LinkedTransactions:Create]

		$str = $str . "Created LinkedTransaction ID: " . $linkedtransaction['LinkedTransactionID'];
		
		if($returnObj) {
			return $linkedtransaction;
		} else {
			return $str;
		}
	}

	public function updateLinkedTransaction($xero)
	{
		$str = '';

		$new = $this->createLinkedTransaction($xero,true);
		$guid = $new['LinkedTransactionID'];
		$newInv = $this->createInvoiceAccRec($xero,true);
		$invGuid = $newInv['InvoiceID'];
		$contactid = $newInv['Contact']['ContactID'];

		//[LinkedTransactions:Update]
		$invoice = $xero->loadByGUID('Accounting\\Invoice', $invGuid);
		$lineitemid = $invoice['LineItems'][0]["LineItemID"];
		$contactid = $invoice['Contact']['ContactID'];

		$linkedtransaction = $xero->loadByGUID('Accounting\\LinkedTransaction', $guid);
		$linkedtransaction->setTargetTransactionID($invGuid)
			->setTargetLineItemID($lineitemid)
			->setContactID($contactid);
		$linkedtransaction->save();
		//[/LinkedTransactions:Update]

		$str = $str . "Updated LinkedTransaction: " . $new['LinkedTransactionID'];
		
		return $str;
	}

	public function deleteLinkedTransaction($xero)
	{
		$str = '';

		$new = $this->createLinkedTransaction($xero,true);
		$guid = $new['LinkedTransactionID'];

		//[LinkedTransactions:Delete]
		$linkedtransaction = $xero->loadByGUID('Accounting\\LinkedTransaction', $guid);
		$linkedtransaction->delete();
		//[/LinkedTransactions:Delete]

		$str = $str . "Deleted LinkedTransaction: " . $linkedtransaction['LinkedTransactionID'];

		return $str;
	}
				
	public function getManualJournal($xero,$returnObj=false)
	{
		$str = '';

		//[ManualJournals:Read]
		$manualjournals = $xero->load('Accounting\\ManualJournal')->execute();
		//[/ManualJournals:Read]

		$str = $str . "Get all ManualJournals Total: " . count($manualjournals) . "<br>";

		if($returnObj) {
			return $manualjournals[0];
		} else {
			return $str;
		}
	}

	public function createManualJournal($xero,$returnObj=false)
	{
		$str = '';

		$credit = $this->getJournalLineCredit($xero);
		$debit = $this->getJournalLineDebit($xero);

		//[ManualJournals:Create]
		$new = new \XeroPHP\Models\Accounting\ManualJournal($xero);
		$new->setNarration('MJ from SDK -' . $this->getRandNum())
			->addJournalLine($credit)
			->addJournalLine($debit);
		$new->save();
		//[/ManualJournals:Create]
		
		$str = $str . "Create ManualJournal: " . $new["Narration"] . "<br>" ;
		
		if($returnObj) {
			return $new;
		} else {
			return $str;
		}
	}

	public function updateManualJournal($xero)
	{
		$str = '';
	
		$new = $this->createManualJournal($xero,true);
		$guid = $new["ManualJournalID"];

		//[ManualJournals:Update]
		$manualjournal = $xero->loadByGUID('Accounting\\ManualJournal', $guid);
		$manualjournal->setNarration('Updated ManualJournal-' . $this->getRandNum());
		$manualjournal->save();
		//[/ManualJournals:Update]

		$str = $str . "Update ManualJournal: " . $manualjournal["Narration"] . "<br>";
		
		return $str;
	}
		
	public function getOrganisation($xero)
	{
		$str = '';

		//[Organisations:Read]
		$organisations = $xero->load('Accounting\\Organisation')->execute();
		//[/Organisations:Read]
		
		$str = $str . "Organisation Name " . $organisations[0]["Name"];

		return $str;
	}

	public function getPayment($xero,$returnObj=false)
	{
		$str = '';

		//[Payments:Read]
		$payments = $xero->load('Accounting\\Payment')->execute();
		//[/Payments:Read]

		$str = $str . "Get Payments: " . count($payments) . "<br>";

		if($returnObj) {
			return $all[0];
		} else {
			return $str;
		}
	}

	public function createPayment($xero,$returnObj=false)
	{
		$str = '';

		$invoice = $this->createInvoiceAccRec($xero,true);
		$account = $this->getBankAccount($xero);
		$bankaccount = $account[0];

		//[Payments:Create]
		$payment = new \XeroPHP\Models\Accounting\Payment($xero);
		$payment->setInvoice($invoice)
			->setAccount($bankaccount)
			->setAmount("2.00");
		$payment->save();
		//[/Payments:Create]
		
		$str = $str . "Create Payment: " . $payment["PaymentID"] . "<br>" ;
		
		if($returnObj) {
			return $payment;
		} else {
			return $str;
		}
	}

	public function deletePayment($xero)
	{
		$str = '';

		$new = $this->createPayment($xero,true);
		$guid = $new["PaymentID"];

		//[Payments:Delete]
		$payment = new \XeroPHP\Models\Accounting\Payment($xero);
		$payment->setPaymentID($guid);
		$payment->setStatus(\XeroPHP\Models\Accounting\PAYMENT::PAYMENT_STATUS_DELETED);
		$payment->save();
		//[/Payments:Delete]
		
		$str = $str . "Delete Payment: " . $payment["PaymentID"] . "<br>" ;
		
		return $str;
	}

	public function getOverpayment($xero,$returnObj=false)
	{
		$str = '';

		//[Overpayments:Read]
		$overpayments = $xero->load('Accounting\\Overpayment')->execute();
		//[/Overpayments:Read]

		$str = $str . "Get Overpayment: " . count($overpayments) . "<br>";

		if($returnObj) {
			return $all[0];
		} else {
			return $str;
		}
	}

	public function createOverpayment($xero,$returnObj=false)
	{
		$str = '';

		$lineitem = $this->getLineItemForOverpayment($xero);
		$account = $this->getBankAccount($xero);
		$contact = $this->getContact($xero,true);

		if (count($account)) {

			//[Overpayments:Create]
			$bankAccount = new \XeroPHP\Models\Accounting\BankTransaction\BankAccount($xero);
			$bankAccount->setCode($account[0]["Code"])
				->setAccountId($account[0]["AccountId"]);

			$overpayment = new \XeroPHP\Models\Accounting\BankTransaction($xero);
			$overpayment->setReference('Ref-' . $this->getRandNum())
				->setDate(new DateTime('2017-01-02'))
				->setType(\XeroPHP\Models\Accounting\BankTransaction::TYPE_RECEIVE_OVERPAYMENT)
				->addLineItem($lineitem)
				->setContact($contact)
				->setLineAmountType("NoTax")
				->setBankAccount($bankAccount);
			$overpayment->save();
			//[/Overpayments:Create]

			$str = $str ."Create Overpayment(Bank Transaction): " . $overpayment["Reference"] . " -- $" . $overpayment["Total"] . "<br>" ;

		} else {
			$str = $str . "No Bank Account exists";	
		}

		if($returnObj) {
			return $overpayment;
		} else {
			return $str;
		}
	}

	public function allocateOverpayment($xero)
	{
		$str = '';

		$invoice = $this->createInvoiceAccRec($xero,true);
		$banktransaction = $this->createOverpayment($xero,true);
		$guid = $banktransaction["OverpaymentID"];

		//[Overpayments:Allocate]
		$overpayment = $xero->loadByGUID('Accounting\\Overpayment', $guid);
		$allocation = new \XeroPHP\Models\Accounting\Overpayment\Allocation($xero);
		$allocation->setInvoice($invoice)
			->setAppliedAmount("2.00");
		$overpayment->addAllocation($allocation);
		$overpayment->save();
		//[/Overpayments:Allocate]
		
		$str = $str . "Allocate Overpayment: " . $overpayment["OverpaymentID"] . "<br>" ;
	
		return $str;
	}

	public function refundOverpayment($xero)
	{
		$str = '';

		$account = $this->getBankAccount($xero);
		$bankaccount = $account[0];
		$banktransaction = $this->createOverpayment($xero,true);
		$guid = $banktransaction["OverpaymentID"];

		//[Overpayments:Refund]
		$overpayment = $xero->loadByGUID('Accounting\\Overpayment', $guid);		
		$payment = new \XeroPHP\Models\Accounting\Payment($xero);
		$payment->setOverpayment($overpayment)
			->setAccount($bankaccount)
			->setAmount("2.00");
		$payment->save();
		//[/Overpayments:Refund]

		$str = $str . "Create Overpayment Refund : " . $payment["PaymentID"] . "<br>" ;
		
		return $str;
	}	

	public function getPrepayment($xero,$returnObj=false)
	{
		$str = '';

		//[Prepayments:Read]
		$prepayments = $xero->load('Accounting\\Prepayment')->execute();
		//[/Prepayments:Read]

		$str = $str . "Get Prepayments: " . count($prepayments) . "<br>";

		if($returnObj) {
			return $all[0];
		} else {
			return $str;
		}
	}

	public function createPrepayment($xero,$returnObj=false)
	{
		$str = '';

		$lineitem = $this->getLineItemForPrepayment($xero);
		$account = $this->getBankAccount($xero);
		$contact = $this->getContact($xero,true);

		if (count($account)) {
			//[Prepayments:Create]
			$bankAccount = new \XeroPHP\Models\Accounting\BankTransaction\BankAccount($xero);
			$bankAccount->setCode($account[0]["Code"])
				->setAccountId($account[0]["AccountId"]);

			$prepayment = new \XeroPHP\Models\Accounting\BankTransaction($xero);
			$prepayment->setReference('Ref-' . $this->getRandNum())
				->setDate(new DateTime('2017-01-02'))
				->setType(\XeroPHP\Models\Accounting\BankTransaction::TYPE_SPEND_PREPAYMENT)
				->addLineItem($lineitem)
				->setContact($contact)
				->setLineAmountType("NoTax")
			    ->setBankAccount($bankAccount);
			$prepayment->save();
			//[/Prepayments:Create]

			$str = $str ."Create Prepayment(Bank Transaction): " . $prepayment["Reference"] . " -- $" . $prepayment["Total"] . "<br>" ;

		} else {
			$str = $str . "No Bank Account exists";	
		}

		if($returnObj) {
			return $prepayment;
		} else {
			return $str;
		}
	}

	public function allocatePrepayment($xero)
	{
		$str = '';

		$invoice = $this->createInvoiceAccPay($xero,true);
		$banktransaction = $this->createPrepayment($xero,true);
		$guid = $banktransaction["PrepaymentID"];

		//[Prepayments:Allocate]
		$prepayment = $xero->loadByGUID('Accounting\\Prepayment', $guid);
		$allocation = new \XeroPHP\Models\Accounting\Prepayment\Allocation($xero);
		$allocation->setInvoice($invoice)
			->setAppliedAmount("2.00");
		$prepayment->addAllocation($allocation);
		$prepayment->save();
		//[/Prepayments:Allocate]
		
		$str = $str . "Allocate Prepayment: " . $prepayment["PrepaymentID"] . "<br>" ;
		
		return $str;
	}

	public function refundPrepayment($xero)
	{
		$str = '';

		$account = $this->getBankAccount($xero);
		$bankaccount = $account[0];
		$banktransaction = $this->createPrepayment($xero,true);
		$guid = $banktransaction["PrepaymentID"];

		//[Prepayments:Refund]
		$prepayment = $xero->loadByGUID('Accounting\\Prepayment', $guid);		
		$payment = new \XeroPHP\Models\Accounting\Payment($xero);
		$payment->setPrepayment($prepayment)
			->setAccount($bankaccount)
			->setAmount("2.00");
		$payment->save();
		//[/Prepayments:Refund]

		$str = $str . "Create Prepayment Refund : " . $payment["PaymentID"] . "<br>" ;
		
		return $str;
	}	

	public function getPurchaseOrder($xero,$returnObj=false)
	{
		$str = '';

		//[PurchaseOrders:Read]
		$purchaseorders = $xero->load('Accounting\\PurchaseOrder')->execute();
		//[/PurchaseOrders:Read]

		$str = $str . "Get PurchaseOrders: " . count($purchaseorders) . "<br>";

		if($returnObj) {
			return $purchaseorders[0];
		} else {
			return $str;
		}
	}

	public function createPurchaseOrder($xero,$returnObj=false)
	{
		$str = '';

		$lineitem = $this->getLineItemForPurchaseOrder($xero);
		$contact = $this->getContact($xero,true);

		//[PurchaseOrders:Create]
		$purchaseorder = new \XeroPHP\Models\Accounting\PurchaseOrder($xero);
		$purchaseorder->setReference('Ref original -' . $this->getRandNum())
			->setContact($contact)
			->addLineItem($lineitem);
		$purchaseorder->save();
		//[/PurchaseOrders:Create]
		
		$str = $str . "Create PurchaseOrder: " . $purchaseorder["Reference"] . "<br>" ;
		
		if($returnObj) {
			return $purchaseorder;
		} else {
			return $str;
		}
	}

	public function updatePurchaseOrder($xero)
	{
		$str = '';
	
		$new = $this->createPurchaseOrder($xero,true);
		$guid = $new["PurchaseOrderID"];

		//[PurchaseOrders:Update]
		$purchaseorder = $xero->loadByGUID('Accounting\\PurchaseOrder', $guid);
		$purchaseorder->setReference('New Ref Num-' . $this->getRandNum());
		$purchaseorder->save();
		//[/PurchaseOrders:Update]

		$str = $str . "Update PurchaseOrder: " . $purchaseorder["Reference"] . "<br>";
		
		return $str;
	}

	public function deletePurchaseOrder($xero)
	{
		$str = '';
	
		$new = $this->createPurchaseOrder($xero,true);
		$guid = $new["PurchaseOrderID"];

		//[PurchaseOrders:Delete]
		$purchaseorder = $xero->loadByGUID('Accounting\\PurchaseOrder', $guid);
		$purchaseorder->setStatus('DELETED');
		$purchaseorder->save();
		//[/PurchaseOrders:Delete]

		$str = $str . "Deleted PurchaseOrder: " . $purchaseorder["Reference"] . "<br>";
		
		return $str;
	}


	public function getReceipt($xero,$returnObj=false)
	{
		$str = '';

		//[Receipts:Read]
		$receipts = $xero->load('Accounting\\Receipt')->execute();
		//[/Receipts:Read]

		$str = $str . "Get Receipts: " . count($receipts) . "<br>";

		if($returnObj) {
			return $receipts[0];
		} else {
			return $str;
		}
	}

	public function createReceipt($xero,$returnObj=false)
	{
		$str = '';

		$user = $this->getUser($xero,true);
		$contact = $this->getContact($xero,true);
		$lineitem = $this->getLineItemForReceipt($xero);

		//[Receipts:Create]
		$receipt = new \XeroPHP\Models\Accounting\Receipt($xero);
		$receipt->setDate(new DateTime('2018-01-02'))
			->setLineAmountType(\XeroPHP\Models\Accounting\INVOICE::LINEAMOUNT_TYPE_INCLUSIVE)
			->setUser($user)
			->setContact($contact)
			->setStatus(\XeroPHP\Models\Accounting\INVOICE::INVOICE_STATUS_DRAFT)
			->addLineItem($lineitem);
		$receipt->save();
		//[/Receipts:Create]
		
		$str = $str . "Create Receipt: " . $receipt["ReceiptID"] . "<br>" ;
		
		if($returnObj) {
			return $receipt;
		} else {
			return $str;
		}
	}

	public function updateReceipt($xero)
	{
		$str = '';
	
		$new = $this->createReceipt($xero,true);
		$guid = $new["ReceiptID"];
		$lineitem = $this->getLineItemForReceipt($xero);

		//[Receipts:Update]
		$receipt = $xero->loadByGUID('Accounting\\Receipt', $guid);
		$receipt->setReference("Add Ref to Receipt");
		$receipt->save();
		//[/Receipts:Update]

		$str = $str . "Update Receipt: " . $receipt["ReceiptID"] . "<br>";
		
		return $str;
	}

	public function deleteReceipt($xero)
	{
		$str = '';
	
		$new = $this->createReceipt($xero,true);
		$guid = $new["ReceiptID"];

		//[Receipts:Delete]
		$receipt = $xero->loadByGUID('Accounting\\Receipt', $guid);
		$receipt->setStatus('DELETED');
		$receipt->save();
		//[/Receipts:Delete]

		$str = $str . "Deleted Receipt: " . $receipt["ReceiptID"] . "<br>";
		
		return $str;
	}



	public function getRepeatingInvoice($xero)
	{
		$str = '';

		//[RepeatingInvoices:Read]
		$repeatinginvoices = $xero->load('Accounting\\RepeatingInvoice')->execute();
		//[/RepeatingInvoices:Read]
		
		$str = $str . "RepeatingInvoice " . count($repeatinginvoices);

		return $str;
	}

	public function getBalanceSheet($xero)
	{
		$str = '';

		//[Reports:BalanceSheet]
		$report = $xero->load('Accounting\\Report\BalanceSheet')->execute();
		//[/Reports:BalanceSheet]
		
		$str = $str . "Report: " . $report[0]['ReportType'] . " Date: " . $report[0]['ReportTitles'][2];

		return $str;
	}

	public function getProfitAndLoss($xero)
	{
		$str = '';

		//[Reports:ProfitAndLoss]
		$report = $xero->load('Accounting\\Report\ProfitLoss')->execute();
		//[/Reports:ProfitAndLoss]
		
		$str = $str . "Report: " . $report[0]['ReportType'] . " Date: " . $report[0]['ReportTitles'][2];

		return $str;
	}

	public function getTenNinetyNine($xero)
	{
		$str = '';

		//[Reports:TenNinetyNine]
		//$report = $xero->load('Accounting\\Report\TenNinetyNine')->execute();
		//[/Reports:TenNinetyNine]
		
		//$str = $str . "Report: " . $report[0]['ReportType'] . " Date: " . $report[0]['ReportTitles'][2];

		$str = $str . "Report: Not supported in SDK yet"; 

		return $str;
	}

	public function getAgedPayablesByContact($xero)
	{
		$str = '';

		//[Reports:AgedPayablesByContact]
		//$report = $xero->load('Accounting\\Report\AgedPayablesByContact')->execute();
		//[/Reports:AgedPayablesByContact]
		
		//$str = $str . "Report: " . $report[0]['ReportType'] . " Date: " . $report[0]['ReportTitles'][2];

		$str = $str . "Report: Not supported in SDK yet"; 

		return $str;
	}

	public function getAgedReceivablesByContact($xero)
	{
		$str = '';

		//[Reports:AgedReceivablesByContact]
		//$report = $xero->load('Accounting\\Report\AgedReceivablesByContact')->execute();
		//[/Reports:AgedReceivablesByContact]
		
		//$str = $str . "Report: " . $report[0]['ReportType'] . " Date: " . $report[0]['ReportTitles'][2];

		$str = $str . "Report: Not supported in SDK yet"; 

		return $str;
	}

	public function getBankSummary($xero)
	{
		$str = '';

		//[Reports:BankSummary]
		//$report = $xero->load('Accounting\\Report\BankSummary')->execute();
		//[/Reports:BankSummary]
		
		//$str = $str . "Report: " . $report[0]['ReportType'] . " Date: " . $report[0]['ReportTitles'][2];

		$str = $str . "Report: Not supported in SDK yet"; 

		return $str;
	}

	public function getBankStatement($xero)
	{
		$str = '';

		//[Reports:BankStatement]
		//$report = $xero->load('Accounting\\Report\BankStatement')->execute();
		//[/Reports:BankStatement]
		
		//$str = $str . "Report: " . $report[0]['ReportType'] . " Date: " . $report[0]['ReportTitles'][2];

		$str = $str . "Report: Not supported in SDK yet"; 

		return $str;
	}

	public function getBudgetSummary($xero)
	{
		$str = '';

		//[Reports:BudgetSummary]
		//$report = $xero->load('Accounting\\Report\BudgetSummary')->execute();
		//[/Reports:BudgetSummary]
		
		//$str = $str . "Report: " . $report[0]['ReportType'] . " Date: " . $report[0]['ReportTitles'][2];

		$str = $str . "Report: Not supported in SDK yet"; 

		return $str;
	}

	public function getExecutiveSummary($xero)
	{
		$str = '';

		//[Reports:ExecutiveSummary]
		//$report = $xero->load('Accounting\\Report\ExecutiveSummary')->execute();
		//[/Reports:ExecutiveSummary]
		
		//$str = $str . "Report: " . $report[0]['ReportType'] . " Date: " . $report[0]['ReportTitles'][2];

		$str = $str . "Report: Not supported in SDK yet"; 

		return $str;
	}

	public function getTrialBalance($xero)
	{
		$str = '';

		//[Reports:TrialBalance]
		//$report = $xero->load('Accounting\\Report\TrialBalance')->execute();
		//[/Reports:TrialBalance]
		
		//$str = $str . "Report: " . $report[0]['ReportType'] . " Date: " . $report[0]['ReportTitles'][2];

		$str = $str . "Report: Not supported in SDK yet"; 

		return $str;
	}

	public function getTrackingCategory($xero,$returnObj=false)
	{
		$str = '';

		//[TrackingCategories:Read]
		$trackingcategories = $xero->load('Accounting\\TrackingCategory')->execute();
		//[/TrackingCategories:Read]

		$str = $str . "Get TrackingCategories: " . count($trackingcategories) . "<br>";

		if($returnObj) {
			return $trackingcategories[0];
		} else {
			return $str;
		}
	}

	public function createTrackingCategory($xero,$returnObj=false)
	{
		$str = '';	

		//[TrackingCategories:Create]
		$trackingcategory = new \XeroPHP\Models\Accounting\TrackingCategory($xero);
		$trackingcategory->setName('Rate -' . $this->getRandNum());
		$trackingcategory->save();
		//[/TrackingCategories:Create]
		
		$str = $str . "Create TrackingCategory: " . $trackingcategory["TrackingCategoryID"] . "<br>" ;
		
		if($returnObj) {
			return $trackingcategory;
		} else {
			return $str;
		}
	}

	public function updateTrackingCategory($xero)
	{
		$str = '';
	
		$trackingcategories = $xero->load('Accounting\\TrackingCategory')->execute();
		$guid = $trackingcategories[0]["TrackingCategoryID"];

		//[TrackingCategories:Update]
		$trackingcategory = new \XeroPHP\Models\Accounting\TrackingCategory($xero);
		$trackingcategory->setTrackingCategoryID($guid)
			->setName('New Category Name-' . $this->getRandNum());
		$trackingcategory->save();
		//[/TrackingCategories:Update]

		$str = $str . "Update TrackingCategory: " . $trackingcategory["Name"] . "<br>";
		
		return $str;
	}

	public function archiveTrackingCategory($xero)
	{
		$str = '';
		
		$trackingcategories = $xero->load('Accounting\\TrackingCategory')->execute();
		$guid = $trackingcategories[0]["TrackingCategoryID"];

		//[TrackingCategories:Archive]
		$trackingcategory = new \XeroPHP\Models\Accounting\TrackingCategory($xero);
		$trackingcategory->setTrackingCategoryID($guid)
			->setStatus('ARCHIVED');
		$trackingcategory->save();
		//[/TrackingCategories:Archive]

		$str = $str . "Archive TrackingCategory: " . $trackingcategory["Name"] . "<br>";
		
		return $str;
	}

	public function deleteTrackingCategory($xero)
	{
		$str = '';
	
		$trackingcategories = $xero->load('Accounting\\TrackingCategory')->execute();
		$guid = $trackingcategories[0]["TrackingCategoryID"];

		//[TrackingCategories:Delete]
		$trackingcategory = $xero->loadByGUID('Accounting\\TrackingCategory', $guid);
		$trackingcategory->delete();
		//[/TrackingCategories:Delete]

		$str = $str . "Delete TrackingCategory: " . $trackingcategory["Name"] . "<br>";
		
		
		return $str;
	}

	public function getTrackingOption($xero)
	{
		$str = '';

		//[TrackingOptions:Read]
		$trackingcategories = $xero->load('Accounting\\TrackingCategory')->execute();
		$options = $trackingcategories[0]['options'];
		//[/TrackingOptions:Read]

		$str = $str . "Get TrackingOptions: " . count($options) . "<br>";

		return $str;
	}

	public function getTaxRate($xero,$returnObj=false)
	{
		$str = '';

		//[TaxRates:Read]
		$taxrates = $xero->load('Accounting\\TaxRate')->execute();
		//[/TaxRates:Read]

		$str = $str . "Get TaxRates: " . count($taxrates) . "<br>";

		if($returnObj) {
			return $taxrates[0];
		} else {
			return $str;
		}
	}

	public function createTaxRate($xero,$returnObj=false)
	{
		$str = '';

		$taxcomponent = $this->getTaxComponent($xero);

		//[TaxRates:Create]
		$taxrate = new \XeroPHP\Models\Accounting\TaxRate($xero);
		$taxrate->setName('Rate -' . $this->getRandNum())
				->addTaxComponent($taxcomponent);
		$taxrate->save();
		//[/TaxRates:Create]
		
		$str = $str . "Create TaxRate: " . $taxrate["Name"] . "<br>" ;
		
		if($returnObj) {
			return $taxrate;
		} else {
			return $str;
		}
	}

	public function updateTaxRate($xero)
	{
		$str = '';
	
		$new = $this->createTaxRate($xero,true);
		$guid = $new["TaxType"];

		//[TaxRates:Update]
		$taxrate = $xero->loadByGUID('Accounting\\TaxRate', $guid);
		$taxrate->setName('New Tax Name-' . $this->getRandNum());
		$taxrate->save();
		//[/TaxRates:Update]

		$str = $str . "Update TaxRate: " . $taxrate["Name"] . "<br>";
		
		return $str;
	}

	public function deleteTaxRate($xero)
	{
		$str = '';
	
		$new = $this->createTaxRate($xero,true);
		$guid = $new["TaxType"];
		$name = $new["Name"];

		//[TaxRates:Delete]
		//$taxrate = new \XeroPHP\Models\Accounting\TaxRate($xero);
		//$taxrate->setTaxType($guid);
		//$taxrate->setStatus('DELETED');
		//$taxrate->save();
		//[/TaxRates:Delete]
		
		$str = $str . "TaxRate Delete: Not supported in SDK yet";

		return $str;
	}


	public function getUser($xero,$returnObj=false)
	{
		$str = '';

		//[Users:Read]
		$users = $xero->load('Accounting\\User')->execute();
		//[/Users:Read]

		$str = $str . "Get Users: " . count($users) . "<br>";

		if($returnObj) {
			return $users[0];
		} else {
			return $str;
		}
	}

	// HELPERS
	public function getRandNum()
	{
		$randNum = strval(rand(1000,100000)); 

		return $randNum;
	}

	public function getLineItem($xero)
	{
		$lineitem = new \XeroPHP\Models\Accounting\BankTransaction\LineItem($xero);
		$lineitem->setDescription('Bank Transaction-' . $this->getRandNum())
			->setQuantity(1)
			->setUnitAmount(20)
			->setAccountCode("400");

		return $lineitem;
	}	

	public function getLineItemForReceipt($xero)
	{
		$lineitem = new \XeroPHP\Models\Accounting\Receipt\LineItem($xero);
		$lineitem->setDescription('My Receipt 1 -' .  $this->getRandNum())
			->setQuantity(1)
			->setUnitAmount(20)
			->setAccountCode("612");

		return $lineitem;
	}	

	public function getLineItemForInvoice($xero)
	{
		$lineitem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
		$lineitem->setDescription('INV-' . $this->getRandNum())
			->setQuantity(1)
			->setUnitAmount(20)
			->setAccountCode("400");
		return $lineitem;
	}

	public function getLineItemWithTracking($xero)
	{
		$tracking = $this->getTrackingCategory($xero,true);

		$lineitem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
		$lineitem->setDescription('INV-' . $this->getRandNum())
			->setQuantity(1)
			->setUnitAmount(20)
			->setAccountCode("400")
			->addTracking($tracking);
		return $lineitem;
	}

	public function getLineItemForOverpayment($xero)
	{
		$account = $this->getAccRecAccount($xero);

		$lineitem = new \XeroPHP\Models\Accounting\BankTransaction\LineItem($xero);
		$lineitem->setDescription('INV-' . $this->getRandNum())
			->setQuantity(1)
			->setUnitAmount(20)
			->setAccountCode($account[0]["Code"]);
		return $lineitem;
	}

	public function getLineItemForPrepayment($xero)
	{
		$account = $this->getAccountExpense($xero);

		$lineitem = new \XeroPHP\Models\Accounting\BankTransaction\LineItem($xero);
		$lineitem->setDescription('INV-' . $this->getRandNum())
			->setQuantity(1)
			->setUnitAmount(20)
			->setAccountCode($account[0]["Code"]);
		return $lineitem;
	}

	public function getLineItemForPurchaseOrder($xero)
	{
		$lineitem = new \XeroPHP\Models\Accounting\PurchaseOrder\LineItem($xero);
		$lineitem->setDescription('PO-' . $this->getRandNum())
			->setQuantity(1)
			->setUnitAmount(20)
			->setAccountCode("400");
		return $lineitem;
	}

	public function getBankAccount($xero)
	{
		$account = $xero->load('Accounting\\Account')->where('
			    Status=="' . \XeroPHP\Models\Accounting\Account::ACCOUNT_STATUS_ACTIVE . '" AND
			    Type=="' . \XeroPHP\Models\Accounting\Account::ACCOUNT_TYPE_BANK . '"
			')->execute();
		
		return $account;
	}	

	public function getAccRecAccount($xero)
	{
		$account = $xero->load('Accounting\\Account')->where('
			    Status=="' . \XeroPHP\Models\Accounting\Account::ACCOUNT_STATUS_ACTIVE . '" AND
			    SystemAccount=="' . \XeroPHP\Models\Accounting\Account::SYSTEM_ACCOUNT_DEBTORS . '"
			')->execute();
		
		return $account;
	}	

	public function getAccountExpense($xero)
	{
		$account = $xero->load('Accounting\\Account')->where('
			    Status=="' . \XeroPHP\Models\Accounting\Account::ACCOUNT_STATUS_ACTIVE . '" AND
			    Type=="' . \XeroPHP\Models\Accounting\Account::ACCOUNT_TYPE_EXPENSE . '"
			')->execute();
		
		return $account;
	}	

	public function createInvoiceAccPay($xero,$returnObj=false)
	{
		$str = '';
		
		$contact = $this->getContact($xero,true);
		$lineitem = $this->getLineItemForInvoice($xero,true);

		$new = new \XeroPHP\Models\Accounting\Invoice($xero);
		$new->setReference('Ref-' . $this->getRandNum())
			->setDueDate(new DateTime('2017-03-02'))
			->setType(\XeroPHP\Models\Accounting\INVOICE::INVOICE_TYPE_ACCPAY)
			->addLineItem($lineitem)
			->setContact($contact)
			->setStatus(\XeroPHP\Models\Accounting\INVOICE::INVOICE_STATUS_AUTHORISED)
			->setLineAmountType("Exclusive");
		$new->save();
		
		$str = $str . "Create a new Invoice: " . $new["Reference"] . " -- $" . $new["Total"] . "<br>" ;

		if($returnObj) {
			return $new;
		} else {
			return $str;
		}
	}

	public function createInvoiceAuthorised($xero,$returnObj=false)
	{
		$str = '';
		
		$contact = $this->getContact($xero,true);
		$lineitem = $this->getLineItemForInvoice($xero,true);

		$new = new \XeroPHP\Models\Accounting\Invoice($xero);
		$new->setReference('Ref-' . $this->getRandNum())
			->setDueDate(new DateTime('2017-03-02'))
			->setType(\XeroPHP\Models\Accounting\INVOICE::INVOICE_TYPE_ACCPAY)
			->addLineItem($lineitem)
			->setContact($contact)
			->setStatus(\XeroPHP\Models\Accounting\INVOICE::INVOICE_STATUS_AUTHORISED)
			->setLineAmountType("Exclusive");
		$new->save();
		
		$str = $str . "Create a new Invoice: " . $new["Reference"] . " -- $" . $new["Total"] . "<br>" ;

		if($returnObj) {
			return $new;
		} else {
			return $str;
		}
	}

	public function createInvoiceAccRec($xero,$returnObj=false)
	{
		$str = '';
		
		$contact = $this->getContact($xero,true);
		$lineitem = $this->getLineItemForInvoice($xero,true);

		$new = new \XeroPHP\Models\Accounting\Invoice($xero);
		$new->setReference('Ref-' . $this->getRandNum())
			->setDueDate(new DateTime('2017-03-02'))
			->setType(\XeroPHP\Models\Accounting\INVOICE::INVOICE_TYPE_ACCREC)
			->addLineItem($lineitem)
			->setContact($contact)
			->setStatus(\XeroPHP\Models\Accounting\INVOICE::INVOICE_STATUS_AUTHORISED)
			->setLineAmountType("Exclusive");
		$new->save();
		
		$str = $str . "Create a new Invoice: " . $new["Reference"] . " -- $" . $new["Total"] . "<br>" ;

		if($returnObj) {
			return $new;
		} else {
			return $str;
		}
	}	

	public function createInvoiceWithTracking($xero,$returnObj=false)
	{
		$str = '';
		
		$contact = $this->getContact($xero,true);
		$lineitem = $this->getLineItemWithTracking($xero,true);

		$new = new \XeroPHP\Models\Accounting\Invoice($xero);
		$new->setReference('Ref-' . $this->getRandNum())
			->setDueDate(new DateTime('2017-03-02'))
			->setType(\XeroPHP\Models\Accounting\INVOICE::INVOICE_TYPE_ACCREC)
			->addLineItem($lineitem)
			->setContact($contact)
			->setStatus(\XeroPHP\Models\Accounting\INVOICE::INVOICE_STATUS_AUTHORISED)
			->setLineAmountType("Exclusive");
		$new->save();
		
		$str = $str . "Create a new Invoice: " . $new["Reference"] . " -- $" . $new["Total"] . "<br>" ;

		if($returnObj) {
			return $new;
		} else {
			return $str;
		}
	}	

	public function getJournalLineCredit($xero)
	{
		$journalline = new \XeroPHP\Models\Accounting\ManualJournal\JournalLine($xero);
		$journalline->setLineAmount("20.00")
			->setAccountCode("400");
		return $journalline;
	}

	public function getJournalLineDebit($xero)
	{
		$journalline = new \XeroPHP\Models\Accounting\ManualJournal\JournalLine($xero);
		$journalline->setLineAmount("-20.00")
			->setAccountCode("620");
		return $journalline;
	}

	public function createCreditNoteAuthorised($xero)
	{
		$lineitem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
		$lineitem->setDescription('Credit Note-' . $this->getRandNum())
			->setQuantity(2)
			->setUnitAmount(20)
			->setAccountCode("400");

		$contact = $this->getContact($xero,true);

		$new = new \XeroPHP\Models\Accounting\CreditNote($xero);
		$new->setDate(new DateTime('2017-01-02'))
			->setContact($contact)
			->addLineItem($lineitem)
			->setStatus(\XeroPHP\Models\Accounting\Invoice::INVOICE_STATUS_AUTHORISED)
		    ->setType(\XeroPHP\Models\Accounting\CreditNote::CREDIT_NOTE_TYPE_ACCPAYCREDIT);
		$new->save();
		
		return $new;
	
	}

	public function getTaxComponent($xero)
	{
		$taxcomponent = new \XeroPHP\Models\Accounting\TaxRate\TaxComponent($xero);
		$taxcomponent->setName('Tax-' . $this->getRandNum())
			->setRate(5);
		return $taxcomponent;
	}
}
?>
