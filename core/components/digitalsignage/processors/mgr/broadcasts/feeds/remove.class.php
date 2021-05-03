<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageBroadcastFeedRemoveProcessor extends modObjectRemoveProcessor
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

        return parent::initialize();
    }

    /**
     * @access public.
     * @return Mixed.
     */
    public function afterRemove()
    {
        $broadcast = $this->object->getBroadcast();

        if ($broadcast) {
            $broadcast->fromArray([
                'hash' => time()
            ]);

            $broadcast->save();
        }

        return parent::afterRemove();
    }
}

return 'DigitalSignageBroadcastFeedRemoveProcessor';
