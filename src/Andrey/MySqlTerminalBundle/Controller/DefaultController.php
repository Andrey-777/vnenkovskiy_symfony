<?php

namespace Andrey\MySqlTerminalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Andrey\MySqlTerminalBundle\Form\MysqlterminalForm;
class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $session = $this->get('request')->getSession();
        $service = $this->get('service.service');

        return $this->render('AndreyMySqlTerminalBundle:Default:index.html.twig',
            $service->getFormData($request,
                $this->createForm(new MysqlterminalForm),
                $session, $this->get('model.service')));
    }
}
