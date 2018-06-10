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
use XeroPHP\Models\Accounting\Contact;
use XeroPHP\Models\Accounting\ContactGroup;
use XeroPHP\Remote\Request;
use XeroPHP\Remote\URL;

class ContactGroupsController extends BaseController
{
    /**
     * Register the routes for this controller
     *
     * @param RouteCollection $collection
     */
    public static function registerRoutes(RouteCollection $collection)
    {
        $collection->group('contact-groups', function (RouteGroup $group) {
            $controller = self::class;

            $group->post('create', "$controller::create");
            $group->post('get', "$controller::get");
            $group->post('get/{guid:uuid}', "$controller::getByGUID");
            $group->post('update', "$controller::update");
            $group->post('delete', "$controller::delete");
            $group->post('add-contact', "$controller::addContact");
            $group->post('remove-contact', "$controller::removeContact");
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

        $contactGroup = new ContactGroup($this->xero);
        $contactGroup->setName('Rebels-' . $code);
        $contactGroup->save();

        return $this->jsonCodeResponse($response, $contactGroup, 201);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response)
    {
        $contactGroups = $this->xero->load(ContactGroup::class)
            ->where('Status', 'ACTIVE')
            ->execute();

        return $this->jsonCodeResponse($response, $contactGroups);
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
        $contactGroup = $this->xero->loadByGUID(ContactGroup::class, $args['guid']);

        return $this->jsonCodeResponse($response, $contactGroup);
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
        $contactGroup = $this->xeroTestObjects->getContactGroup();

        $contactGroup->setName('First Order-' . Strings::random_number());
        $contactGroup->save();

        return $this->jsonCodeResponse($response, $contactGroup);
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
        $contactGroup = $this->xeroTestObjects->getContactGroup();

        $contactGroup->setStatus('DELETED');
        $contactGroup->save();

        return $this->jsonCodeResponse($response, $contactGroup);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function addContact(ServerRequestInterface $request, ResponseInterface $response)
    {
        // In a real-world case, you'd be loading the from Xero
        // or using the ->setGUID() method on a new instance
        $contactGroup = $this->xeroTestObjects->getContactGroup();
        $contact = $this->xeroTestObjects->getContact();

        $contactGroup->addContact($contact);
        $contactGroup->save();

        return $this->jsonCodeResponse($response, new VariableCollection(compact('contactGroup', 'contact')));
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function removeContact(ServerRequestInterface $request, ResponseInterface $response)
    {
        // In a real-world case, you'd be loading the from Xero
        // or using the ->setGUID() method on a new instance
        $contactGroup = $this->xeroTestObjects->getContactGroup();
        $contact = $this->xeroTestObjects->getContact();

        // Add a contact so we have one to remove
        $contactGroup->addContact($contact);
        $contactGroup->save();

        // This is not yet handled by the SDK, but it is possible
        // to create your own URL to remove contacts
        $url = new URL($this->xero, sprintf('%s/%s/%s/%s',
                ContactGroup::getResourceURI(), $contactGroup->getGUID(),
                Contact::getResourceURI(), $contact->getGUID())
        );

        $request = new Request($this->xero, $url, Request::METHOD_DELETE);
        $request->send();

        return $this->jsonCodeResponse($response, $contactGroup);
    }

}