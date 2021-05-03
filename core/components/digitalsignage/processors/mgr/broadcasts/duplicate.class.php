<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageBroadcastDuplicateProcessor extends modObjectDuplicateProcessor
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
    public function beforeSave()
    {
        $this->newObject->set('has', time());
        $this->newObject->set('color', mt_rand(1, 32));

        $resourceResponse = $this->modx->runProcessor('resource/duplicate', [
            'id'    => $this->object->get('resource_id'),
            'name'  => $this->newObject->get('name')
        ]);

        if ($resourceResponse->isError()) {
            foreach ((array) $resourceResponse->getFieldErrors() as $error) {
                $this->addFieldError('name', $error->message);
            }
        } else {
            $resource = $resourceResponse->getObject();

            if ($resource) {
                if (isset($resource['id'])) {
                    $this->newObject->set('resource_id', $resource['id']);
                } else {
                    $this->addFieldError('name', $this->modx->lexicon('digitalsignage.error_broadcast_resource_object'));
                }
            } else {
                $this->addFieldError('name', $this->modx->lexicon('digitalsignage.error_broadcast_resource_object'));
            }


            foreach ($this->object->getMany('Slides') as $slide) {
                $object = $this->modx->newObject('DigitalSignageBroadcastSlide');

                if ($object) {
                    $object->fromArray($slide->toArray());

                    $this->newObject->addMany($object);
                }
            }

            foreach ($this->object->getMany('Feeds') as $feed) {
                $object = $this->modx->newObject('DigitalSignageBroadcastFeed');

                if ($object) {
                    $object->fromArray($feed->toArray());

                    $this->newObject->addMany($object);
                }
            }
        }

        return parent::beforeSave();
    }

    /**
     * @access public.
     * @return Mixed.
     */
    public function afterSave()
    {
        $this->modx->cacheManager->refresh([
            'db'                => [],
            'auto_publish'      => [
                'contexts'          => [
                    $this->modx->getOption('digitalsignage.context')
                ]
            ],
            'context_settings'  => [
                'contexts'          => [
                    $this->modx->getOption('digitalsignage.context')
                ]
            ],
            'resource'          => [
                'contexts'          => [
                    $this->modx->getOption('digitalsignage.context')
                ]
            ]
        ]);

        $this->modx->call('modResource', 'refreshURIs', [&$this->modx, 0, [
            'contexts' => $this->modx->getOption('digitalsignage.context')
        ]]);

        return parent::afterSave();
    }
}

return 'DigitalSignageBroadcastDuplicateProcessor';
