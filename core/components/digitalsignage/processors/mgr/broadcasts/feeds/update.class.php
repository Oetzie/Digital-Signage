<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageBroadcastFeedUpdateProcessor extends modObjectUpdateProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'DigitalSignageBroadcastFeed';

    /**
     * @access public.
     * @var Array.
     */
    public $languageTopics = ['digitalsignage:default'];

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'digitalsignage.feed';

    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->modx->getService('digitalsignage', 'DigitalSignage', $this->modx->getOption('digitalsignage.core_path', null, $this->modx->getOption('core_path') . 'components/digitalsignage/') . 'model/digitalsignage/');

        if ($this->getProperty('active') === null) {
            $this->setProperty('active', 0);
        }

        return parent::initialize();
    }

    /**
     * @access public.
     * @return Mixed.
     */
    public function beforeSave()
    {
        if (!preg_match('/^(http|https)/i', $this->getProperty('url'))) {
            $this->setProperty('url', 'http://' . $this->getProperty('url'));
        }

        return parent::beforeSave();
    }

    /**
     * @access public.
     * @return Mixed.
     */
    public function afterSave()
    {
        $broadcast = $this->object->getBroadcast();

        if ($broadcast) {
            $broadcast->fromArray([
                'hash' => time()
            ]);

            $broadcast->save();
        }

        return parent::afterSave();
    }
}

return 'DigitalSignageBroadcastFeedUpdateProcessor';
