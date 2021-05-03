<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageBroadcastSlideSortProcessor extends modProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'DigitalSignageBroadcastSlide';

    /**
     * @access public.
     * @var Array.
     */
    public $languageTopics = ['digitalsignage:default'];

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'digitalsignage.slide';

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
     * @return String.
     */
    public function process()
    {
        $index = 0;

        foreach (explode(',', $this->getProperty('sort')) as $id) {
            $object = $this->modx->getObject($this->classKey, [
                'id' => $id
            ]);

            if ($object) {
                $object->fromArray([
                    'sortindex' => $index
                ]);

                if ($object->save()) {
                    $broadcast = $object->getBroadcast();

                    if ($broadcast) {
                        $broadcast->fromArray([
                            'hash' => time()
                        ]);

                        $broadcast->save();
                    }

                    $index++;
                }
            }
        }

        return $this->success();
    }
}

return 'DigitalSignageBroadcastSlideSortProcessor';
