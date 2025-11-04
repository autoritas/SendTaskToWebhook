<?php

namespace Kanboard\Plugin\SendTaskToWebhook;

use Kanboard\Core\Plugin\Base;
use Kanboard\Plugin\SendTaskToWebhook\Action\SendTaskToWebhook;
use Kanboard\Core\Translator;

/**
 * SendTaskToWebhook Plugin
 *
 * @package  Kanboard\Plugins\SendTaskToWebhook/Plugin.php
 * @author   Autoritas Consulting 
 */

class Plugin extends Base
{
    public function initialize()
    {
        $this->actionManager->register(new SendTaskToWebhook($this->container));
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }

    /**
     * Get plugin name
     *
     * @access public
     * @return string
     */
    public function getPluginName()
    {
        return 'Send Task To Webhook';
    }

    /**
     * Get plugin author
     * 
     * @access public
     * @return string
     */
    public function getPluginAuthor()
    {
        return 'Fran Paredes';
    }

    /**
     * Get plugin version
     * 
     * @access public
     * @return string
     */
    public function getPluginVersion()
    {
        return '1.0.0';
    }

    /**
     * Get plugin description
     * 
     * @access public
     * @return string
     */
    public function getPluginDescription()
    {
        return 'Send task content to a webhook.';
    }

    /**
     * Get plugin homepage
     * 
     * @access public
     * @return string
     */
    public function getPluginHomepage()
    {
        return 'https://github.com/autoritas/SendTaskToWebhook';
    }

    public function getPluginFeatures()
    {
        return [
            'action' => [
                t('Send task to webhook') => 'SendTaskToWebhook',
            ],
        ];
    }
}
