<?php namespace Nintendo\Translator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MagentoTranslation extends Command
{
    private $transporter;
    private $builder;
    private $formatter;
    private $merger;
    private $creator;

    /**
     * MagentoTranslation constructor.
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function __construct(ContainerBuilder $container)
    {
        $this->transporter = $container->get('transporter');
        $this->builder = $container->get('builder');
        $this->formatter = $container->get('formatter');
        $this->merger = $container->get('merger');
        $this->creator = $container->get('creator');

        parent::__construct();
    }

    /**
     * Set configuration for this command
     */
    protected function configure()
    {
        $this
            ->setName('nintendo:translation:magento')
            ->setDescription('generate translation files for magento')
            ->setHelp('Given a *.xls filename as input, this command generates various CSV files as output')
            ->addArgument('xlsFilename', InputArgument::REQUIRED, 'XLS filename to use as input');

    }

    /**
     * This execution will generate various CSV files under the folder generated
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->transporter->setData($input->getArgument('xlsFilename'));

        /** @var \Nintendo\Translator\Data $generatedFiles */
        $generatedFiles = $this->builder->execute($this->transporter);

        $output->writeln('The following files were generated');
        $output->writeln($generatedFiles->getData());
    }
}