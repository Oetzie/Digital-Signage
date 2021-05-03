<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageSlideTypeDataGetListProcessor extends modProcessor
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
    public $defaultSortField = 'menuindex';

    /**
     * @access public.
     * @var String.
     */
    public $defaultSortDirection = 'ASC';

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

        $this->setDefaultProperties([
            'start' => 0,
            'limit' => 20,
            'sort'  => $this->defaultSortField,
            'dir'   => $this->defaultSortDirection,
            'combo' => false,
            'query' => ''
        ]);

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
            $output = [];
            $sort   = $this->getProperty('sort');
            $dir    = $this->getProperty('dir');
            $query  = $this->getProperty('query');

            foreach ((array) $object->getData($sort, $dir) as $key => $value) {
                if (!empty($query)) {
                    if (preg_match('/' . $query . '/', $key)) {
                        $output[] = array_merge([
                            'key' => $key
                        ], $value);
                    }
                } else {
                    $output[] = array_merge([
                        'key' => $key
                    ], $value);
                }
            }

            return $this->outputArray(array_slice($output, $this->getProperty('start'), $this->getProperty('limit')), count($output));
        }

        return $this->failure($this->modx->lexicon('digitalsignage.error_slide_type_not_exists'));
    }
}

return 'DigitalSignageSlideTypeDataGetListProcessor';
