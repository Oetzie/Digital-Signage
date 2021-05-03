<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignagePlayerCreateProcessor extends modObjectCreateProcessor
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
    public $objectType = 'digitalsignage.player';

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
        if (!preg_match('/^(\d+)x(\d+)$/', $this->getProperty('resolution'))) {
            $this->addFieldError('resolution', $this->modx->lexicon('digitalsignage.error_player_resolution'));
        }

        $key = $this->object->get('key');

        if (empty($key)) {
            $unique = false;

            while (!$unique) {
                $key = $this->object->generateKey();

                $object = $this->modx->getObject('DigitalSignagePlayer', [
                    'id:!=' => $this->object->get('id'),
                    'key'   => $key
                ]);

                if (!$object) {
                    $unique = true;
                }
            }
        }

        $this->object->set('key', $key);

        return parent::beforeSave();
    }
}

return 'DigitalSignagePlayerCreateProcessor';
