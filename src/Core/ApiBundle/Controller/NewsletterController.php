<?php
/**
 * Created by PhpStorm.
 * User: turker
 * Date: 17.12.2016
 * Time: 22:59
 */

namespace Core\ApiBundle\Controller;

use Core\ApiBundle\Entity\Newsletter;
use AppBundle\Form\NewsletterType;
use Core\ApiBundle\ApiBundle;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class NewsletterController
 * @package Core\ApiBundle\Controller
 *
 * @RouteResource("Newsletter", pluralize=false)
 */
class NewsletterController extends FOSRestController
{
    /**
     * get_newsletters [GET] /api/newsletter
     * @Rest\View
     */
    public function cgetAction(){
        $newsletters = $this->getDoctrine()->getRepository("ApiBundle:Newsletter")->findAll();
        $view = $this->view($newsletters, 200);
        return $this->handleView($view);
    }


    public function postAction(Request $request)
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(\Core\ApiBundle\Form\NewsletterType::class, $newsletter);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($newsletter);
            $em->flush();
            $view = View::create($newsletter, 200);
        } else {
            $view = View::create($form, 400);
        }
        return $this->handleView($view);
    }
}