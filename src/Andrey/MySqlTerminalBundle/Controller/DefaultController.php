<?php

namespace Andrey\MySqlTerminalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Andrey\MySqlTerminalBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Andrey\MySqlTerminalBundle\Models\MysqlterminalModel;
class DefaultController extends Controller
{
    protected $model = null;

    public function __construct()
    {
        $this->model = new MysqlterminalModel();
    }

    public function indexAction(Request $request)
    {
        $form = $this->model->buildForm($this);

        if ($this->model->isPOST($request)) {
            $form->bind($request);

            if ($form->isValid()) {
                $arr        = $form->getData();
                $em         = $this->getDoctrine()->getEntityManager();
                $connection = $em->getConnection();
                $statement  = $connection->prepare($arr['Query:']);
                $statement->execute();
                $results    = $statement->fetchAll();

                return $this->render('AndreyMySqlTerminalBundle:Default:result.html.twig', array(
                    'res' => $results));
            }
        }

        return $this->render('AndreyMySqlTerminalBundle:Default:index.html.twig', array('form' => $form->createView(),));
    }

    public function createAction()
    {
        $product = new Product();
        $product->setName('A Foo Bar');
        $product->setPrice('19.99');
        $product->setDescription('Lorem ipsum dolor');
        $product->setBlabla('AAAAAAAAAAAA');

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($product);
        $em->flush();

        return new Response('Created product id '.$product->getId());
    }
}
