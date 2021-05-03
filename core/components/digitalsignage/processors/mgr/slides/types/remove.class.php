<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageSlideTypeRemoveProcessor extends modObjectRemoveProcessor
{
    /**
     * @acces public.
     * @var String.
     */
    public $classKey = 'DigitalSignageSlideType';

    /**
     * @acces public.
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

        return parent::initialize();
    }
}

return 'DigitalSignageSlideTypeRemoveProcessor';
