<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageBroadcastPreviewProcessor extends modObjectProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'DigitalSignagePlayer';

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
     * @return String.
     */
    public function process()
    {
        $criteria = $this->modx->newQuery('DigitalSignageBroadcast');

        $criteria->select($this->modx->getSelectColumns('DigitalSignageBroadcast', 'DigitalSignageBroadcast'));
        $criteria->select($this->modx->getSelectColumns('modResource', 'modResource', 'resource_', ['pagetitle']));

        $criteria->innerJoin('modResource', 'modResource', [
            '`modResource`.`id` = `DigitalSignageBroadcast`.`resource_id`'
        ]);

        $criteria->where([
            'DigitalSignageBroadcast.id' => $this->getProperty('id')
        ]);

        $broadcast = $this->modx->getObject('DigitalSignageBroadcast', $criteria);

        if ($broadcast) {
            $player = $this->modx->getObject('DigitalSignagePlayer', [
                'id' => $this->getProperty('player')
            ]);

            if ($player) {
                list($width, $height) = explode('x', $player->get('resolution'));

                return $this->success(null, array_merge($player->toArray(), [
                    'url'       => $this->modx->makeUrl($broadcast->get('resource_id'), null, [
                        'pl'        => $player->get('key'),
                        'bc'        => $broadcast->get('id'),
                        'preview'   => true
                    ], 'full'),
                    'width'     => $width,
                    'height'    => $height
                ]));
            }
        }

        return $this->failure();
    }
}

return 'DigitalSignageBroadcastPreviewProcessor';
