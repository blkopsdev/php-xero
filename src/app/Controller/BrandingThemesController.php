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
use XeroPHP\Models\Accounting\BrandingTheme;

class BrandingThemesController extends BaseController
{
    /**
     * Register the routes for this controller
     *
     * @param RouteCollection $collection
     */
    public static function registerRoutes(RouteCollection $collection)
    {
        $collection->group('branding-themes', function (RouteGroup $group) {
            $controller = self::class;

            $group->post('get', "$controller::get");
            $group->post('get/{guid:uuid}', "$controller::getByGUID");
        });
    }


    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response)
    {
        $brandingThemes = $this->xero->load(BrandingTheme::class)
            ->execute();

        return $this->jsonCodeResponse($response, $brandingThemes);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function getByGUID(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $brandingTheme = $this->xero->loadByGUID(BrandingTheme::class, $args['guid']);

        return $this->jsonCodeResponse($response, $brandingTheme);
    }

}