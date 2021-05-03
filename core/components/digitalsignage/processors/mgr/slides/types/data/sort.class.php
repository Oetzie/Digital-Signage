<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageSlideTypeDataSortProcessor extends modProcessor
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
    public $languageTopics = ['digitalsignage:default', 'digitalsignage:slides'];

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
        $this->modx->getService('digitalsignage', 'DigitalSignage', $this->modx->getOption('digitalsignage.core_path', null, $this->modx->getOption('core_path') .' components/digitalsignage/') . 'model/digitalsignage/');

        return parent::initialize();
    }

    /**
     * @access public.
     * @return String.
     */
    public function process()
    {
        $sort = json_decode($this->getProperty('sort'), true);

        if ($sort) {
            $newSort = [];

            foreach ((array) $sort as $value) {
                $newSort[$value['key']] = $value['menuindex'];
            }

            $object = $this->modx->getObject($this->classKey, [
                'id' => $this->getProperty('id')
            ]);

            if ($object) {
                $data = $object->getData();

                foreach ($data as $key => $value) {
                    if (isset($newSort[$key])) {
                        $data[$key]['menuindex'] = $newSort[$key];
                    } else {
                        $data[$key]['menuindex'] = 0;
                    }
                }

                $object->fromArray([
                    'data' => json_encode($data)
                ]);

                if (!$object->save()) {
                    return $this->failure($this->modx->lexicon('digitalsignage.error_slide_type_data'));
                }

                return $this->success('', $object);
            }

            return $this->failure($this->modx->lexicon('digitalsignage.error_slide_type_not_exists'));
        }
    }
}

return 'DigitalSignageSlideTypeDataSortProcessor';
