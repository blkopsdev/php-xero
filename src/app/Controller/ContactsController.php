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
use XeroPHP\Models\Accounting\Attachment;
use XeroPHP\Models\Accounting\Contact;

class ContactsController extends BaseController
{
    /**
     * Register the routes for this controller
     *
     * @param RouteCollection $collection
     */
    public static function registerRoutes(RouteCollection $collection)
    {
        $collection->group('contacts', function (RouteGroup $group) {
            $controller = self::class;

            $group->post('create', "$controller::create");
            $group->post('get', "$controller::get");
            $group->post('get/{guid:uuid}', "$controller::getByGUID");
            $group->post('update', "$controller::update");
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

        $contact = new Contact($this->xero);
        $contact->setName('Sidney-' . $code)
            ->setFirstName('Sid-' . $code)
            ->setLastName("Maestre - " . $code)
            ->setEmailAddress("sidney" . $code . "@maestre.com");
        $contact->save();

        return $this->jsonCodeResponse($response, $contact, 201);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response)
    {
        $contacts = $this->xero->load(Contact::class)
            ->where('ContactStatus', Contact::CONTACT_STATUS_ACTIVE)
            ->execute();

        return $this->jsonCodeResponse($response, $contacts);
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
        $contact = $this->xero->loadByGUID(Contact::class, $args['guid']);

        return $this->jsonCodeResponse($response, $contact);
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
        $contact = $this->xeroTestObjects->getContact();

        $contact->setName('Sid-' . Strings::random_number());
        $contact->save();

        return $this->jsonCodeResponse($response, $contact);
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
        $contact = $this->xeroTestObjects->getContact();

        $contact->setContactStatus(Contact::CONTACT_STATUS_ARCHIVED);
        $contact->save();

        return $this->jsonCodeResponse($response, $contact);
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
        $contact = $this->xeroTestObjects->getContact();

        $attachment = Attachment::createFromLocalFile(APP_ROOT . '/data/helo-heroes.jpg');
        $contact->addAttachment($attachment);

        return $this->jsonCodeResponse($response, new VariableCollection(compact('contact', 'attachment')));
    }
}