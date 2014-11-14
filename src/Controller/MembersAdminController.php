<?php

namespace Bolt\Extension\Bolt\Members\Controller;

use Silex;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bolt\Translation\Translator as Trans;
use Bolt\Extension\Bolt\Members\Extension;
use Bolt\Extension\Bolt\Members\Admin;

/**
 * Members admin area controller
 *
 * Copyright (C) 2014  Gawain Lynch
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    Gawain Lynch <gawain.lynch@gmail.com>
 * @copyright Copyright (c) 2014, Gawain Lynch
 * @license   http://opensource.org/licenses/GPL-3.0 GNU Public License 3.0
 */
class MembersAdminController implements ControllerProviderInterface
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var array
     */
    private $config;

    /**
     * @var Bolt\Extension\Bolt\Members\Admin
     */
    private $admin;

    /**
     *
     * @param Silex\Application $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Silex\Application $app)
    {
        $this->config = $app[Extension::CONTAINER]->config;
        $this->admin = new Admin($app);

        /**
         * @var $ctr \Silex\ControllerCollection
         */
        $ctr = $app['controllers_factory'];

        // Admin page
        $ctr->match('/', array($this, 'admin'))
            ->bind('MembersAdmin')
            ->before(array($this, 'before'))
            ->method('GET');

        // AJAX requests
        $ctr->match('/ajax', array($this, 'ajax'))
            ->method('GET|POST');

        $app[Extension::CONTAINER]->addMenuOption(Trans::__('Members'), $app['paths']['bolt'] . 'extensions/members', "fa:users");

        return $ctr;
    }

    /**
     * Controller before render
     *
     * @param Request           $request
     * @param \Bolt\Application $app
     */
    public function before(Request $request, \Bolt\Application $app)
    {
        // Enable HTML snippets in our routes so that JS & CSS gets inserted
        $app['htmlsnippets'] = true;

        // Add our JS & CSS
        $app[Extension::CONTAINER]->addJavascript('js/members.admin.js', true);

        // Temporary until I fork to it's own extension
        $app[Extension::CONTAINER]->addCss('css/sweet-alert.css', true);
        $app[Extension::CONTAINER]->addJavascript('js/sweet-alert.min.js', true);
    }

    /**
     * The main admin page
     *
     * @param Silex\Application $app
     * @param Request $request
     * @return \Twig_Markup
     */
    public function admin(Silex\Application $app, Request $request)
    {
        $this->addTwigPath($app);

        $html = $app['render']->render('members.twig', array(
            'members' => $this->admin->getMembers(false, false),
            'roles'   => $app['members']->getRoles()
        ));

        return new \Twig_Markup($html, 'UTF-8');
    }

    /**
     * Members Admin AJAX controller
     *
     * @param Silex\Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajax(Silex\Application $app, Request $request)
    {
        if ($request->getMethod() == "POST" && $app['request']->get('task')) {

//             if (!$app['users']->checkAntiCSRFToken()) {
//                $app->abort(400, Trans::__("Something went wrong"));
//             }

            //
            $values = array(
                'job' => $app['request']->get('task'),
                'result' => true
            );

            if ($app['request']->get('task') == 'userAdd') {
                /*
                 * Add a user
                 */
                try {
                    //
                } catch (Exception $e) {
                    return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, array('content-type' => 'text/html'));
                }

                return new JsonResponse($values);
            } elseif ($app['request']->get('task') == 'userDel') {
                /*
                 * Delete a user
                 */
                try {
                    //
                } catch (Exception $e) {
                    return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, array('content-type' => 'text/html'));
                }

                return new JsonResponse($values);
            } elseif ($app['request']->get('task') == 'userEnable') {
                /*
                 * Enable a user
                 */
                try {
                    //
                } catch (Exception $e) {
                    return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, array('content-type' => 'text/html'));
                }

                return new JsonResponse($values);
            } elseif ($app['request']->get('task') == 'userDisable') {
                /*
                 * Disable a user
                 */
                try {
                    //
                } catch (Exception $e) {
                    return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, array('content-type' => 'text/html'));
                }

                return new JsonResponse($values);
            } elseif ($app['request']->get('task') == 'roleAdd') {
                /*
                 * Add a role to user(s)
                 */
                try {
                    //
                } catch (Exception $e) {
                    return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, array('content-type' => 'text/html'));
                }

                return new JsonResponse($values);
            } elseif ($app['request']->get('task') == 'roleDel') {
                /*
                 * Delete a role from user(s)
                 */
                try {
                    //
                } catch (Exception $e) {
                    return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, array('content-type' => 'text/html'));
                }

                return new JsonResponse($values);
            }

        } elseif ($request->getMethod() == "GET" && $app['request']->get('task')) {

        }

        // Yeah, nah
        return new Response('Invalid request parameters', Response::HTTP_BAD_REQUEST);
    }

    private function addTwigPath(Silex\Application $app)
    {
        $app['twig.loader.filesystem']->addPath(dirname(dirname(__DIR__)) . '/assets/admin');
    }

}