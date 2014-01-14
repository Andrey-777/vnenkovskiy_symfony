<?php

namespace Andrey\MySqlTerminalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Andrey\MySqlTerminalBundle\Entity\Product;
//use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Andrey\MySqlTerminalBundle\Services\MysqlterminalService;
use Andrey\MySqlTerminalBundle\Form\MysqlterminalForm;
class DefaultController extends Controller
{
    protected $service    = null;
    protected $showResult = false;

    public function __construct()
    {
        $this->service = new MysqlterminalService();
    }

    public function indexAction(Request $request)
    {
        return $this->render('AndreyMySqlTerminalBundle:Default:index.html.twig',
                             $this->service->checkRequest($request,
                                                          $this->createForm(new MysqlterminalForm),
                                                          $this->getDoctrine()->getManager()));
    }


//    public function createAction()
//    {
//        $product = new Product();
//        $product->setName('A Foo Bar');
//        $product->setPrice('19.99');
//        $product->setDescription('Lorem ipsum dolor');
//        $product->setBlabla('AAAAAAAAAAAA');
//
//        $em = $this->getDoctrine()->getEntityManager();
//        $em->persist($product);
//        $em->flush();
//
//        return new Response('Created product id '.$product->getId());
//    }
}
