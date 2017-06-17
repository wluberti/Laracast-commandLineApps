<?php

namespace Acme;

use GuzzleHttp\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

class NewCommand extends Command
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;

        parent::__construct();
    }

    public function configure()
    {
        $this->setName('new')
             ->setDescription('Create a new Laravel application')
             ->addArgument('name', InputArgument::REQUIRED);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return $this
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // assert that the folder doesn't exist
        $directory = getcwd() . '/' . $input->getArgument('name');
        $this->assertApplicationDoesNotExists($directory, $output);

        // download nightly build &
        // extract zipfile
        $this->download($zipFile = $this->makeFileName())
             ->extract($zipFile, $directory)
             ->cleanUp($zipFile);

        // Notify user
        $output->writeln('<comment>Application ready!</comment>');

        return $this;
    }

    /**
     * Check is the directory/application doesn't already exist
     *
     * @param $directory
     * @param OutputInterface $output
     */
    private function assertApplicationDoesNotExists($directory, OutputInterface $output)
    {
        if (is_dir($directory)) {
            $output->writeln('<error>Application already exists!</error>');
            exit(1);
        }

    }

    /**
     * Make temporary file for the downloaded .zip file
     *
     * @return string
     */
    private function makeFileName()
    {
        return getcwd() . '/laravel_' . md5(time().uniqid()) . '.zip';
    }

    /**
     * Download the nightly build of Laravel
     *
     * @param $zipFile
     *
     * @return $this
     */
    private function download($zipFile) {
        $response = $this->client->get('http://cabinet.laravel.com/latest.zip')->getBody();

        file_put_contents($zipFile, $response);

        return $this;
    }

    /**
     * Extract the downloaded zipfile
     *
     * @param $zipFile
     * @param $directory
     *
     * @return $this
     */
    private function extract($zipFile, $directory) {
        $archive = new ZipArchive();

        $archive->open($zipFile);
        $archive->extractTo($directory);
        $archive->close();

        return $this;
    }

    /**
     * Delete the temporary file (suppresses all output '@')
     *
     * @param $zipFile
     *
     * @return $this
     */
    private function cleanUp($zipFile){
        @chmod($zipFile, 0777);
        @unlink($zipFile);

        return $this;
    }

}
