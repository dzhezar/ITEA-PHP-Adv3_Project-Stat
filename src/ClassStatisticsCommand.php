<?php

/*
 * This file is part of the "PHP Project Stat" project.
 *
 * (c) Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App;

use App\Author\ClassAnalyzer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClassStatisticsCommand extends Command
{
    private $analyzer;

    /**
     * {@inheritdoc}
     */
    public function __construct(ClassAnalyzer $analyzer, string $name = null)
    {
        $this->analyzer = $analyzer;

        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('stat:class-name')
            ->setDescription('Shows statistic about needed class')
            ->addArgument(
                'Class Name',
                InputArgument::REQUIRED,
                'Name of needed class'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $class = $input->getArgument('Class Name');

        $array = $this->analyzer->analyze($class);

        \var_dump($array);
        /*$output->writeln(
            \sprintf('Properties:
                                Public: %d (%d static)
                                Protected: %d (%d static)
                                Private: %d (%d static)
                              Methods:
                                Public: %d (%d static)
                                Protected: %d (%d static)
                                Private: %d (%d static)
                                ',  $array['properties']['public'],$array['properties']['public']['static'],
                $array['properties']['protected'],$array['properties']['protected']['static'],
                $array['properties']['private'],$array['properties']['private']['static'],
                $array['methods']['public'],$array['methods']['public']['static'],
                $array['methods']['protected'],$array['properties']['protected']['static'],
                $array['methods']['private'],$array['methods']['private']['static'])
        );*/
    }
}
