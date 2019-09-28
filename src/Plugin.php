<?php

namespace ProvisionOps\UpdateDependencies;

use Composer\Composer;
use Composer\EventDispatcher\Event;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\IO\IOInterface;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PreFileDownloadEvent;
use Composer\Script\ScriptEvents;
use TQ\Git\Repository\Repository;

class Plugin implements PluginInterface, Capable, EventSubscriberInterface
{

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var IOInterface
     */
    protected $io;

    /**
     * @var String Composer working directory. Same as composer.json
     */
    protected $workingDir;

    /**
     * @var \TQ\Git\Repository\Repository
     */
    protected $gitRepo;

    /**
     * @param \Composer\Composer $composer
     * @param \Composer\IO\IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;

        $this->workingDir = getcwd();
        $this->gitRepo = Repository::open($this->workingDir);

        // @TODO: Load .env from composer w  Subscriber ProvisionOps\UpdateDependencies\Plugin::pluginDemoMethod for event init is not
        //        if (file_exists(dirname(__DIR__))) . '.env';
        print $composer->getConfig()->get('working-dir');
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            'pre-update-cmd' => 'preUpdateCommand',
            'post-update-cmd' => 'postUpdateCommand',
        );
    }

    public function getCapabilities()
    {
        return array(
//            'Composer\Plugin\Capability\CommandProvider' => 'ProvisionOps\UpdateDependencies\CommandProvider',
        );
    }

    /**
     * Before running the update command, ensure composer.lock file is not
     * modified.
     *
     * @param Event $event
     */
    public function preUpdateCommand(\Composer\Script\Event $event)
    {
        // Don't proceed if composer.lock file is not in git.
        $info = $this->gitRepo->getObjectInfo('composer.lock');
        if (empty($info['type'])) {
            throw new \Exception('Composer Update: composer.lock file is not committed to the git repository. Please commit the file and try again.');
        }

        // Don't proceed if composer lock or json is modified.
        $diff = $this->gitRepo->getDiff();
        if (!empty($diff['composer.lock'])) {
            throw new \Exception('Composer Update: composer.lock file is modified. Commit or revert the changes before running composer update command.');
        }

        if (!empty($diff['composer.json'])) {
            throw new \Exception('Composer Update: composer.json file is modified. Commit or revert the changes before running composer update command.');
        }
    }

    /**
     * @param Event $event
     */
    public function postUpdateCommand(\Composer\Script\Event $event)
    {
        if (!empty($this->gitRepo->getDiff(['composer.lock']))) {
            $this->io->write(
              '<comment>Updates to composer.lock detected.</comment>'
            );

            // @TODO:
            // Git checkout new branch
            // Git commit.
            // git push new branch
            // GitHub API Submit PR.
            // Git checkout original branch.

        }
    }
}
