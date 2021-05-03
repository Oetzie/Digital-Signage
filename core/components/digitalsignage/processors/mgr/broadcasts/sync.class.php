<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageBroadcastSynchronizeProcessor extends modObjectUpdateProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'DigitalSignageBroadcast';

    /**
     * @access public.
     * @var Array.
     */
    public $languageTopics = ['digitalsignage:default'];

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'digitalsignage.broadcast';

    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->modx->getService('digitalsignage', 'DigitalSignage', $this->modx->getOption('digitalsignage.core_path', null, $this->modx->getOption('core_path') . 'components/digitalsignage/') . 'model/digitalsignage/');

        return parent::initialize();
    }

    /**
     * @access public.
     * @return Mixed.
     */
    public function process()
    {
        if (!$this->object->sync()) {
            return $this->failure($this->modx->lexicon('digitalsignage.error_broadcast_sync'));
        }

        return $this->outputArray([]);
    }
}

return 'DigitalSignageBroadcastSynchronizeProcessor';
