<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

abstract class DigitalSignageManagerController extends modExtraManagerController
{
    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->modx->getService('digitalsignage', 'DigitalSignage', $this->modx->getOption('digitalsignage.core_path', null, $this->modx->getOption('core_path') . 'components/digitalsignage/') . 'model/digitalsignage/');

        $this->addCss($this->modx->digitalsignage->config['css_url'] . 'mgr/digitalsignage.css');

        $this->addJavascript($this->modx->digitalsignage->config['js_url'] . 'mgr/digitalsignage.js');

        $this->addJavascript($this->modx->digitalsignage->config['js_url'] . 'mgr/extras/extras.js');

        $this->addHtml('<script type="text/javascript">
            Ext.onReady(function() {
                MODx.config.help_url = "' . $this->modx->digitalsignage->getHelpUrl() . '";
                
                DigitalSignage.config = ' . $this->modx->toJSON(array_merge($this->modx->digitalsignage->config, [
                    'branding_url'      => $this->modx->digitalsignage->getBrandingUrl(),
                    'branding_url_help' => $this->modx->digitalsignage->getHelpUrl()
                ])) . ';
            });
        </script>');

        return parent::initialize();
    }

    /**
     * @access public.
     * @return Array.
     */
    public function getLanguageTopics()
    {
        return $this->modx->digitalsignage->config['lexicons'];
    }

    /**
     * @access public.
     * @returns Boolean.
     */
    public function checkPermissions()
    {
        return $this->modx->hasPermission('digitalsignage');
    }
}

class IndexManagerController extends DigitalSignageManagerController
{
    /**
     * @access public.
     * @return String.
     */
    public static function getDefaultController()
    {
        return 'home';
    }
}
