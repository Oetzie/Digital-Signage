<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageSlideGetListProcessor extends modObjectGetListProcessor
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

        $this->setDefaultProperties([
            'dateFormat' => $this->modx->getOption('manager_date_format') . ', ' . $this->modx->getOption('manager_time_format')
        ]);

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

        $broadcastID = $this->getProperty('broadcast_id');

        if (!empty($broadcastID)) {
            $criteria->innerJoin('DigitalSignageBroadcastSlide', 'DigitalSignageBroadcastSlide', [
                'DigitalSignageBroadcastSlide.slide_id = DigitalSignageSlide.id'
            ]);

            $criteria->where([
                'DigitalSignageBroadcastSlide.broadcast_id' => $broadcastID
            ]);
        }

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
            'slide_type_name_formatted' => $object->get('slide_type_name'),
            'time_formatted'            => $object->getTimeFormatted(),
            'data'                      => $object->getData()
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

        if (in_array($object->get('editedon'), ['-001-11-30 00:00:00', '-1-11-30 00:00:00', '0000-00-00 00:00:00', null], true)) {
            $array['editedon'] = '';
        } else {
            $array['editedon'] = date($this->getProperty('dateFormat'), strtotime($object->get('editedon')));
        }

        if ($this->modx->hasPermission('digitalsignage_settings') || (int) $object->get('protected') === 0) {
            return $array;
        }

        return [];
    }
}

return 'DigitalSignageSlideGetListProcessor';
