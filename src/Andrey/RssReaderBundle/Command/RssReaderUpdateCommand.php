<?php
/**
 * Created by PhpStorm.
 * User: avnenkovskyi
 * Date: 1/21/14
 * Time: 3:15 PM
 */

namespace Andrey\RssReaderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RssReaderUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('rssreader:update')
            ->setDescription('Update chanel and news table in symfony database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Successfully');
    }
}