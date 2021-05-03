<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageSlideCreateProcessor extends modObjectCreateProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'DigitalSignageSlide';

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

        if ($this->getProperty('active') === null) {
            $this->setProperty('active', 0);
        }

        if ($this->modx->hasPermission('digitalsignage_settings')) {
            if ($this->getProperty('protected', null) === null) {
                $this->setProperty('protected', 0);
            } else {
                $this->setProperty('protected', 1);
            }
        } else {
            $this->unsetProperty('protected');
        }

        return parent::initialize();
    }

    /**
     * @access public.
     * @return Mixed.
     */
    public function beforeSave()
    {
        $data = [];

        foreach ($this->getProperties() as $key => $value) {
            if (strpos($key, 'data_') !== false) {
                $data[substr($key, 5, strlen($key))] = $value;
            }
        }

        $this->object->set('data', json_encode($data));

        return parent::beforeSave();
    }
}

return 'DigitalSignageSlideCreateProcessor';
