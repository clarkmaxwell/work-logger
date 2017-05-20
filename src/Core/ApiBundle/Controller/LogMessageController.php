<?php
/**
 * Created by PhpStorm.
 * User: turker
 * Date: 17.12.2016
 * Time: 22:59
 */

namespace Core\ApiBundle\Controller;

use Core\ApiBundle\Entity\LogHeader;
use Core\ApiBundle\Entity\LogMessage;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Monolog\Logger;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Class LogMessageController
 * @package Core\ApiBundle\Controller
 *
 * @RouteResource("logmessage", pluralize=true)
 */
class LogMessageController extends FOSRestController
{
    /**
     * get_logmessages [GET] /api/logmessages
     * @Rest\View
     */
    public function cgetAction(){
        $logMessages = $this->getDoctrine()->getRepository("ApiBundle:LogMessage")->findAll();

        $view = $this->view($logMessages, 200);
        return $this->handleView($view);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction($id){
        $logMessage = $this->getDoctrine()->getRepository("ApiBundle:LogMessage")->find($id);

        $view = $this->view($logMessage, 200);
        return $this->handleView($view);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postAction(Request $request)
    {
        $logMessage = new LogMessage();
        $form = $this->createForm(\Core\ApiBundle\Form\LogMessageType::class, $logMessage);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $this->renderMessageByTemplate($logMessage);
            $em->persist($logMessage);
            $em->flush();
            $this->addLogHeaders($request, $logMessage, $em);
            $view = View::create($logMessage, 200);
        } else {
            $view = View::create($form, 400);
        }
        return $this->handleView($view);
    }

    /**
     * @param LogMessage $logMessage
     * @ParamConverter("logMessage", class="ApiBundle:LogMessage", options={"id" = "logMessage"})
     *
     * @Rest\View
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putAction(LogMessage $logMessage, Request $request){
        $form = $this->createForm(\Core\ApiBundle\Form\LogMessageType::class, $logMessage, ['method' => $request->getMethod()]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($logMessage);
            $em->flush();
            $view = View::create($logMessage, 200);
        }else{
            $view = View::create($form, 400);
        }
        return $this->handleView($view);
    }

    /**
     * @param LogMessage $logMessage
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(LogMessage $logMessage, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($logMessage);
        $em->flush($logMessage);
        $view = View::create($logMessage, 200);
        return $this->handleView($view);
    }

    /**
     * @param LogMessage $logMessage
     */
    public function renderMessageByTemplate($logMessage)
    {
        /** @var \Twig_Environment $twigEnvironment */
        $twigEnvironment = $this->get("twig");
        $template = $twigEnvironment->createTemplate($logMessage->getTemplate()->getRawContent());
        $message = $template->render(["content" => $logMessage->getContent()]);
        $logMessage->setContent($message);
    }

    /**
     * @param Request $request
     * @param LogMessage $logMessage
     * @param EntityManager $em
     */
    public function addLogHeaders(Request $request, $logMessage, $em)
    {
        foreach ($request->headers->all() as $item => $value) {
            $logHeader = new LogHeader();
            $logHeader->setVarKey($item);
            $logHeader->setVarValue($value[0]);
            $logHeader->setLogMessage($logMessage);
            $em->persist($logHeader);
            $em->flush($logHeader);
        }
    }
}