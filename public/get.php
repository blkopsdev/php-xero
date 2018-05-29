<?php
	require __DIR__ . '/vendor/autoload.php';

	use XeroPHP\Application\PublicApplication;
	use XeroPHP\Remote\Request;
	use XeroPHP\Remote\URL;
	require_once('storage.php');
	require_once('example.php');

	// Storage Classe uses sessions for storing token > extend to your DB of choice
	$storage = new StorageClass();
	$ex = new ExampleClass();

	$config = [
	    'oauth' => [
	        'callback'        => 'http://localhost/myapp/callback.php',
	        'consumer_key'    => 'YOUR_CONSUMER_KEY',
	        'consumer_secret' => 'YOUR_CONSUMER_SECRET',
	        'signature_location'  => \XeroPHP\Remote\OAuth\Client::SIGN_LOCATION_QUERY,
		    ],
	    'curl' => [
	        CURLOPT_USERAGENT   => 'xero-php sample app',
	        CURLOPT_CAINFO => __DIR__ . '/certs/ca-bundle.crt',
	    ],
	];
	
	$xero = new PublicApplication($config);
	
	//Get session data
	$oauth_session = $storage->getSession();

	$xero->getOAuthClient()
		->setToken($oauth_session['token'])
		->setTokenSecret($oauth_session['token_secret']);

	if (isset($_POST["endpoint"]) ) {
		$endpoint = htmlspecialchars($_POST["endpoint"]);
	} else {
		$endpoint = "Accounts";
	}

	if (isset($_POST["action"]) ) {
		$action = htmlspecialchars($_POST["action"]);
	} else {
		$action = "none";
	}

	// If expired - rediect
	if($storage->checkToken($xero))
	{
		header("Location: index.php");
	}

	
	if (!isset( $oauth_session['token'])) {
		// No token - redirect to connect button
	    header("Location: index.php");
	}

	$file = file_get_contents('./example.php', true);
	$parsed = get_string_between($file, '//[' . $endpoint . ':' . $action . ']', '//[/' . $endpoint . ':' . $action . ']');
	$parsed = str_replace(["\r\n", "\r", "\n"], "<br/>", $parsed);
	
	function get_string_between($string, $start, $end){
	    $string = ' ' . $string;
	    $ini = strpos($string, $start);
	    if ($ini == 0) return '';
	    $ini += strlen($start);
	    $len = strpos($string, $end, $ini) - $ini;
	    return substr($string, $ini, $len);
	}

?>
<html>
<head>
	<title>xero-php sample app</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<script src="http://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.11/handlebars.min.js"  crossorigin="anonymous"></script>
	<script src="public/xero-sdk-ui/xero.js" crossorigin="anonymous"></script>
	<script type="text/javascript">
	   	document.addEventListener("DOMContentLoaded", function() {
			loadGet("xero-php sample app","disconnect.php","<?php echo($endpoint) ?>", "<?php echo($action) ?>");
		});
   	</script>
</head>
<body>
	<div id="req" class="container"></div>
	<div id="res" class="container">	
		<h3><?php echo($endpoint);?></h3>
		<hr>
		<strong>Code</strong><br>
		<pre><?php echo($parsed);?></pre>
		<hr>
		<strong>Result</strong><br>

		<?php
			switch($endpoint)
			{

				 case "BrandingThemes":
				    switch($action)
					{
				        case "Read":
				        echo $ex->getBrandingTheme($xero);
				        break;
				        default:
					    echo $action . " action not supported in API";
				    }
				 break;
				 
				 case "Contacts":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createContact($xero);
				        break;
				        case "Read":
				        echo $ex->getContact($xero);
				        break;
				        case "Update":
				        echo $ex->updateContact($xero);
				    	break;
				    	case "Archive":
				        echo $ex->archiveContact($xero);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "ContactGroups":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createContactGroup($xero);
				        break;
				        case "Read":
				        echo $ex->getContactGroup($xero);
				        break;
				        case "Update":
				        echo $ex->updateContactGroup($xero);
				    	break;
				    	case "Archive":
				        echo $ex->archiveContactGroup($xero);
				    	break;
				    	case "RemoveContact":
				        echo $ex->removeContactFromContactGroup($xero);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "CreditNotes":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createCreditNote($xero);
				        break;
				        case "Read":
				        echo $ex->getCreditNote($xero);
				        break;
				        case "Update":
				        echo $ex->updateCreditNote($xero);
				    	break;
				    	case "Allocate":
				        echo $ex->allocateCreditNote($xero);
				    	break;
				    	case "Refund":
				        echo $ex->refundCreditNote($xero);
				    	break;
				    	case "Delete":
				        echo $ex->deleteCreditNote($xero);
				    	break;
				    	case "Void":
				        echo $ex->voidCreditNote($xero);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Currencies":
				    switch($action)
					{
				        case "Read":
				        echo $ex->getCurrency($xero);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Employees":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createEmployee($xero);
				        break;
				        case "Read":
				        echo $ex->getEmployee($xero);
				        break;
				        case "Update":
				        echo $ex->updateEmployee($xero);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "ExpenseClaims":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createExpenseClaim($xero);
				        break;
				        case "Read":
				        echo $ex->getExpenseClaim($xero);
				        break;
				        case "Update":
				        echo $ex->updateExpenseClaim($xero);
				        //echo $action . " action is supported in API but not SDK (no setStatus)";
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Invoices":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createInvoice($xero);
				        break;
				        case "Read":
				        echo $ex->getInvoice($xero);
				        break;
				        case "Update":
				        echo $ex->updateInvoice($xero);
				    	break;
				    	case "Delete":
				        echo $ex->deleteInvoice($xero);
				    	break;
				    	case "Void":
				        echo $ex->voidInvoice($xero);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "InvoiceReminders":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getInvoiceReminder($xero);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Items":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createItem($xero);
				        break;
				        case "Read":
				        echo $ex->getItem($xero);
				        break;
				        case "Update":
				        echo $ex->updateItem($xero);
				    	break;
				    	case "Delete":
				        echo $ex->deleteItem($xero);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Journals":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getJournal($xero);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "LinkedTransactions":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createLinkedTransaction($xero);
				        break;
				        case "Read":
				        echo $ex->getLinkedTransaction($xero);
				        break;
				        case "Update":
				        echo $ex->updateLinkedTransaction($xero);
				    	break;
				    	case "Delete":
				        echo $ex->deleteLinkedTransaction($xero);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "ManualJournals":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createManualJournal($xero);
				        break;
				        case "Read":
				        echo $ex->getManualJournal($xero);
				        break;
				        case "Update":
				        echo $ex->updateManualJournal($xero);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Organisations":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getOrganisation($xero);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Overpayments":
				    switch($action)
					{
				        case "Read":
				        echo $ex->getOverpayment($xero);
				        break;
				        case "Create":
				        echo $ex->createOverpayment($xero);
				        break;
				        case "Allocate":
				        echo $ex->allocateOverpayment($xero);
				    	break;
				    	case "Refund":
				        echo $ex->refundOverpayment($xero);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Payments":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createPayment($xero);
				        break;
				        case "Read":
				        echo $ex->getPayment($xero);
				        break;
				        case "Delete":
				        echo $ex->deletePayment($xero);
				        break;
				        default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Prepayments":
				    switch($action)
					{
				        case "Read":
				        echo $ex->getPrepayment($xero);
				        break;
				        case "Create":
				        echo $ex->createPrepayment($xero);
				        break;
				        case "Allocate":
				        echo $ex->allocatePrepayment($xero);
				    	break;
				    	case "Refund":
				        echo $ex->refundPrepayment($xero);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "PurchaseOrders":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createPurchaseOrder($xero);
				        break;
				        case "Read":
				        echo $ex->getPurchaseOrder($xero);
				        break;
				        case "Update":
				        echo $ex->updatePurchaseOrder($xero);
				    	break;
				    	case "Delete":
				        echo $ex->deletePurchaseOrder($xero);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Receipts":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createReceipt($xero);
				        break;
				        case "Read":
				        echo $ex->getReceipt($xero);
				        break;
				        case "Update":
				        echo $ex->updateReceipt($xero);
				    	break;
				    	case "Delete":
				        echo $ex->deleteReceipt($xero);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				  case "RepeatingInvoices":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getRepeatingInvoice($xero);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Reports":
				    switch($action)
					{
				    	case "TenNinetyNine":
				        echo $ex->getTenNinetyNine($xero);
				        break;
				        case "AgedPayablesByContact":
				        echo $ex->getAgedPayablesByContact($xero);
				        break;
				        case "AgedReceivablesByContact":
				        echo $ex->getAgedReceivablesByContact($xero);
				        break;
				        case "BalanceSheet":
				        echo $ex->getBalanceSheet($xero);
				        break;
				        case "BankStatement":
				        echo $ex->getBankStatement($xero);
				        break;
				        case "BankSummary":
				        echo $ex->getBankSummary($xero);
				        break;
				        case "BudgetSummary":
				        echo $ex->getBudgetSummary($xero);
				        break;
				        case "ExecutiveSummary":
				        echo $ex->getExecutiveSummary($xero);
				        break;
				        case "ProfitAndLoss":
				        echo $ex->getProfitAndLoss($xero);
				        break;
				        case "TrialBalance":
				        echo $ex->getTrialBalance($xero);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "TaxRates":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createTaxRate($xero);
				        break;
				        case "Read":
				        echo $ex->getTaxRate($xero);
				        break;
				        case "Update":
				        echo $ex->updateTaxRate($xero);
				    	break;
				    	case "Delete":
				        echo $ex->deleteTaxRate($xero);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "TrackingCategories":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createTrackingCategory($xero);
				        break;
				        case "Read":
				        echo $ex->getTrackingCategory($xero);
				        break;
				        case "Update":
				        echo $ex->updateTrackingCategory($xero);
				    	break;
				    	case "Delete":
				        echo $ex->deleteTrackingCategory($xero);
				    	break;
				    	case "Archive":
				        echo $ex->archiveTrackingCategory($xero);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "TrackingOptions":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createTrackingOption($xero);
				        break;
				        case "Read":
				        echo $ex->getTrackingOption($xero);
				        break;
				        case "Update":
				        echo $ex->updateTrackingOption($xero);
				    	break;
				    	case "Delete":
				        echo $ex->deleteTrackingOption($xero);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Users":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getUser($xero);
				        break;
				        default:
					    echo $action . " action not supported in API";
				    }
				 break;
			}

		?>
	</div>
	</body>
</html>