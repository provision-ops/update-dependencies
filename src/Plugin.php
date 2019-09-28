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

class Plugin implements PluginInterface, Capable, EventSubscriberInterface
{
    protected $composer;

    /**
     * @var IOInterface
     */
    protected $io;
    
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            PluginEvents::INIT => 'pluginDemoMethod',
            PluginEvents::COMMAND => '',
            'post-update-cmd' => 'postUpdateCommand',
            'post-package-update' => 'postPackageUpdate',
        );
    }

    public function getCapabilities()
    {
        return array(
//            'Composer\Plugin\Capability\CommandProvider' => 'ProvisionOps\UpdateDependencies\CommandProvider',
        );
    }

    /**
     * @param Event $event
     */
    public function postPackageUpdate(PackageEvent $event)
    {

        $this->io->write("ProvisionOps: Update Dependencies!");

        print $event->getName();

    }
}
