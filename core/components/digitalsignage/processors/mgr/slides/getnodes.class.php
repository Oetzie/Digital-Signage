<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageSlideGetNodesProcessor extends modObjectGetListProcessor
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
    public $defaultSortField = 'DigitalSignageSlide.name';

    /**
     * @access public.
     * @var String.
     */
    public $defaultSortDirection = 'ASC';

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

        return parent::initialize();
    }

    /**
     * @access public.
     * @param xPDOQuery $criteria.
     * @return xPDOQuery.
     */
    public function prepareQueryBeforeCount(xPDOQuery $criteria)
    {
        $criteria->select($this->modx->getSelectColumns('DigitalSignageSlide', 'DigitalSignageSlide'));
        $criteria->select($this->modx->getSelectColumns('DigitalSignageSlideType', 'DigitalSignageSlideType', 'slide_type_', ['key', 'name', 'icon']));

        $criteria->innerJoin('DigitalSignageSlideType', 'DigitalSignageSlideType', [
            '`DigitalSignageSlide`.`slide_type_id` = `DigitalSignageSlideType`.`id`'
        ]);

        $criteria->where([
            'DigitalSignageSlideType.active' => 1
        ]);

        $query = $this->getProperty('query');

        if (!empty($query)) {
            $criteria->where([
                'DigitalSignageSlide.name:LIKE' => '%' . $query . '%'
            ]);
        }

        return $criteria;
    }

    /**
     * @access public.
     * @param xPDOObject $object.
     * @return Array.
     */
    public function prepareRow(xPDOObject $object)
    {
        $array = array_merge($object->toArray(), [
            'slide_type_name_formatted' => $object->get('slide_type_name')
        ]);

        if (empty($object->get('slide_type_icon'))) {
            $array['slide_type_icon'] = 'file';
        }

        if (empty($object->get('slide_type_name'))) {
            $translationKey = 'digitalsignage.slide_' . $object->get('slide_type_key');

            if ($translationKey !== ($translation = $this->modx->lexicon($translationKey))) {
                $array['slide_type_name_formatted'] = $translation;
            }
        }

        return $array;
    }
}

return 'DigitalSignageSlideGetNodesProcessor';
