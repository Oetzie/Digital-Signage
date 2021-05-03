<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageSlideTypeDataUpdateProcessor extends modObjectUpdateProcessor
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

        if ($this->getProperty('key')) {
            $this->setProperty('key', strtolower(str_replace([' ', '-'], '_', $this->getProperty('key'))));
        }

        return parent::initialize();
    }

    /**
     * @access public.
     * @return String.
     */
    public function process()
    {
        $object = $this->modx->getObject($this->classKey, [
            'id' => $this->getProperty('id')
        ]);

        if ($object) {
            if (!preg_match('/^([a-zA-Z0-9\_\-]+)$/i', $this->getProperty('key'))) {
                $this->addFieldError('key', $this->modx->lexicon('digitalsignage.error_slide_type_data_character'));
            } else {
                $data = $object->getData();

                $object->fromArray([
                    'data' => json_encode(array_merge($data, [
                        $this->getProperty('key') => array_merge($data[$this->getProperty('key')], [
                            'xtype'             => $this->getProperty('xtype'),
                            'required'          => $this->getProperty('required'),
                            'values'            => $this->getProperty('values'),
                            'default_value'     => $this->getProperty('default_value'),
                            'label'             => $this->getProperty('label'),
                            'description'       => $this->getProperty('description')
                        ])
                    ]))
                ]);

                if (!$object->save()) {
                    $this->addFieldError('key', $this->modx->lexicon('digitalsignage.error_slide_type_data'));
                } else {
                    return $this->success('', $object);
                }
            }

            return $this->failure('', $object);
        }

        return $this->failure($this->modx->lexicon('digitalsignage.error_slide_type_not_exists'));
    }
}

return 'DigitalSignageSlideTypeDataUpdateProcessor';
