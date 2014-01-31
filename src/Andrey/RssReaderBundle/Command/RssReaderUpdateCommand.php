<?php
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
        $resultsUpdate = $this->getContainer()->get('RssReaderService.service')->updateMethod();

        $response = '<info>' . "Update successful\nCount of new chanels: " . $resultsUpdate['chanels']
                  . "\n" . 'Count of new news: ' . $resultsUpdate['news'] . '</info>';

        $output->writeln($response);
    }
}