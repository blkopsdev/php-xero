<?php
/**
 * @package    xero-php-sample-app
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace App\Controller;

use App\Helper\Strings;
use App\Helper\VariableCollection;
use League\Route\RouteCollection;
use League\Route\RouteGroup;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use XeroPHP\Models\Accounting\Invoice;
use XeroPHP\Models\Accounting\Attachment;

class InvoicesController extends BaseController
{
    /**
     * Register the routes for this controller
     *
     * @param RouteCollection $collection
     */
    public static function registerRoutes(RouteCollection $collection)
    {
        $collection->group('invoices', function (RouteGroup $group) {
            $controller = self::class;

            $group->post('create', "$controller::create");
            $group->post('get', "$controller::get");
            $group->post('get/{guid:uuid}', "$controller::getByGUID");
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

        $invoice = new Invoices($this->xero);
        $invoice->setName('Sales-' . $code)
            ->setCode($code)
            ->setDescription("This is my original description.");
        $invoice->save();


        return $this->jsonCodeResponse($response, $invoice, 201);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response)
    {
        $invoices = $this->xero->load(Invoice::class)
            ->execute();

        return $this->jsonCodeResponse($response, $invoices);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws \Exception
     * @throws \XeroPHP\Exception
     * @throws \XeroPHP\Remote\Exception\NotFoundException
     */
    public function getByGUID(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $invoice = $this->xero->loadByGUID(Invoice::class, $args['guid']);

        return $this->jsonCodeResponse($response, $invoice);
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
        $invoice = $this->xeroTestObjects->getInvoices();
        $invoice->setDescription('My updated description');
        $invoice->save();

        return $this->jsonCodeResponse($response, $invoice);
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
        $invoice = $this->xeroTestObjects->getInvoices();
        $invoice->delete();

        return $this->jsonCodeResponse($response, $invoice);
    }
}