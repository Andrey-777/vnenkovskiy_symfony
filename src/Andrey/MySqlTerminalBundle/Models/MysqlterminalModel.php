<?php
/**
 * Created by PhpStorm.
 * User: avnenkovskyi
 * Date: 1/14/14
 * Time: 10:55 AM
 */
namespace Andrey\MySqlTerminalBundle\Models;

class MysqlterminalModel {
    public function buildForm($obj)
    {
        $form = $obj->createFormBuilder()
            ->add('Host:', 'text')
            ->add('User:', 'text')
            ->add('Password:', 'password')
            ->add('Database:', 'text')
            ->add('Query:', 'textarea')
            ->getForm();

        return $form;
    }

    public function isPOST($request)
    {
        return $request->getMethod() == 'POST' ? true : false;
    }

    public function getMyDoctrine()
    {
        return $this->getDoctrine();
    }
} 