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
        $builder->add('Host:', 'text')
                ->add('User:', 'text')
                ->add('Password:', 'password', array('required' => false))
                ->add('Database:', 'text')
                ->add('Query:', 'textarea');
    }

    public function getName()
    {
        return 'mysql_terminal';
    }
} 