<?php
/**
 * Created by PhpStorm.
 * User: avnenkovskyi
 * Date: 1/14/14
 * Time: 3:15 PM
 */

namespace Andrey\MySqlTerminalBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
class MysqlterminalForm extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('host', 'text')
                ->add('username', 'text')
                ->add('password', 'password', array('required' => false))
                ->add('changePass', 'checkbox', array('required' => false))
                ->add('database', 'text')
                ->add('query', 'textarea');
    }

    public function getName()
    {
            return 'mysql_terminal';
    }
} 