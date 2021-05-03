<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageSlideTypeDataRemoveProcessor extends modProcessor
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
    public $objectType = 'digitalsignage.slidestypes';

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
        $object = $this->modx->getObject($this->classKey, [
            'id' => $this->getProperty('id')
        ]);

        if ($object) {
            $data = $object->getData();

            if (isset($data[$this->getProperty('key')])) {
                unset($data[$this->getProperty('key')]);
            }

            $object->fromArray([
                'data' => json_encode($data)
            ]);

            if (!$object->save()) {
                $this->addFieldError('key', $this->modx->lexicon('digitalsignage.error_slide_type_data'));
            } else {
                return $this->success('', $object);
            }
        }

        return $this->failure($this->modx->lexicon('digitalsignage.error_slide_type_not_exists'));
    }
}

return 'DigitalSignageSlideTypeDataRemoveProcessor';
