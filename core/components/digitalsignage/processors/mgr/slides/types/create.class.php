<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageSlideTypeCreateProcessor extends modObjectCreateProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'DigitalSignageSlideType';

    /**
     * @access public.
     * @var Array.
     */
    public $languageTopics = ['digitalsignage:default'];

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'digitalsignage.slidetype';

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

        if ($this->getProperty('key') !== null) {
            $this->setProperty('key', strtolower(str_replace([' ', '-'], '_', $this->getProperty('key'))));
        }

        return parent::initialize();
    }

    /**
     * @access public.
     * @return Mixed.
     */
    public function beforeSave()
    {
        $this->object->set('key', $this->getProperty('key'));

        if (!preg_match('/^([a-zA-Z0-9\_\-]+)$/i', $this->getProperty('key'))) {
            $this->addFieldError('key', $this->modx->lexicon('digitalsignage.error_slide_type_character'));
        }

        return parent::beforeSave();
    }
}

return 'DigitalSignageSlideTypeCreateProcessor';
