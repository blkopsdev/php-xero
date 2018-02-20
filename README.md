# xero-php-sample-app

This is a sample app for the xero-php SDK. Currently, this app focuses on demonstrating the functionality of all Accounting API endpoints and their related actions.  

You'll be able to connect to a Xero Organisation, make real API calls. The code used to make each API call will be displayed along with the results returned from Xero's API.

### Xero App
This sample app uses a Xero [Public](http://developer.xero.com/documentation/auth-and-limits/public-applications/) App.

Go to [http://app.xero.com](http://app.xero.com) and login with your Xero user account to create a Xero Public app. You'll set the callback URL and get your consumer key & secret.

### Setup App
Download this repo and place in your webroot.

Open your terminal and change to the root of this project and download dependencies with Composer.
`composer init`

### Configure
You'll need to set the `Config` values in the following files.

	* request_token.php
	* callback.php
	* get.php

### What isn't supported in the xero-php SDK?
We couldn't find a way to demonstrate the following actions with the xero-php SDK.

**Attachments**
Allows you to attach files to an account
Allows you to attach files to spend or receive money transactions
Allows you to attach files to bank transfers
Allows you to attach files to a contact
Allows you to attach files to credit notes
Attach files to sales invoices or purchase bills
Allows you to attach files to a manual journal
Allows you to attach images to draft expense claim receipts 

**ContactGroups**
Allows you to remove a contact from a contact group
Allows you to remove all contacts from a contact group

**Currencies**
Add currencies to your organisation

**Invoices**
Retrieve the online invoice Url for sales invoices 

**InvoiceReminder**
PR made to SDK to add support for this

**Reports**
TenNinetyNine
AgedPayablesByContact
AgedReceivablesByContact
BankStatement - missing required bankAccountID
BankSummary
BudgetSummary
ExecutiveSummary
TrialBalance

**TaxRates**
Allows you to Delete a tax rate for a Xero organisation

**Tracking Categories**
Add new  options 
Rename  options 
Update  options 
Delete  options

## Acknowledgement

Special thanks to [Connectifier](https://github.com/connectifier) and [Ben Mccann](https://github.com/benmccann).  Marshalling and Unmarshalling in XeroClient was derived and extended from [Xero-Java-Client](https://github.com/connectifier/xero-java-client)
  

## License

This software is published under the [MIT License](http://en.wikipedia.org/wiki/MIT_License).

	Copyright (c) 2016 Xero Limited

	Permission is hereby granted, free of charge, to any person
	obtaining a copy of this software and associated documentation
	files (the "Software"), to deal in the Software without
	restriction, including without limitation the rights to use,
	copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the
	Software is furnished to do so, subject to the following
	conditions:

	The above copyright notice and this permission notice shall be
	included in all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
	OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
	NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
	HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
	WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
	FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
	OTHER DEALINGS IN THE SOFTWARE.
