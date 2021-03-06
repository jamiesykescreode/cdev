<?php
namespace Creode\Cdev\Command\Plugin;

use Creode\Cdev\Command\ConfigurationCommand;
use Creode\System\Composer\Composer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallPluginCommand extends ConfigurationCommand
{
    // the name of the package we're installing
    private $_package;

    /**
     * @var Composer
     */
    private $_composer;

    /**
     * Constructor
     * @param Composer $composer 
     * @return void
     */
    public function __construct(
        Composer $composer
    ) {
        $this->_composer = $composer;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('plugin:install');
        $this->setDescription('Installs a cdev plugin');

        $this->addOption(
            'path',
            'p',
            InputOption::VALUE_REQUIRED,
            'Path to run commands on. Defaults to the directory the command is run from',
            getcwd()
        );

        $this->addArgument('packages', InputArgument::IS_ARRAY, 'The packages to install');
    }

    /**
     * Executes the command
     * @param InputInterface $input 
     * @param OutputInterface $output 
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_input = $input;
        $this->_output = $output;

        $this->askQuestions();

        $this->installPlugin();
    }

    /**
     * Asks the user questions
     * @return null
     */
    private function askQuestions()
    {
        $helper = $this->getHelper('question');
        $path = $this->_input->getOption('path');

        $packages = $this->_input->getArgument('packages');

        if (count($packages) > 0) {
            $this->_package = implode(' ', $packages);
        } else {
            $this->askQuestion(
                'Plugin package name (e.g. cdev/framework-drupal7)',
                $this->_package
            );
        }
    }

    /**
     * Installs the specified plugin
     * @param string|null $package 
     * @return type
     */
    private function installPlugin($package = null)
    {
        $packageToInstall = isset($package) ? $package : $this->_package;

        $path = \Creode\Cdev\Plugin\Manager::getPluginDir();

        $this->_composer->require($path, $packageToInstall);
    }

}
