<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageBroadcastFeedGetListProcessor extends modObjectGetListProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'DigitalSignageBroadcastFeed';

    /**
     * @access public.
     * @var Array.
     */
    public $languageTopics = ['digitalsignage:default', 'digitalsignage:slides'];

    /**
     * @access public.
     * @var String.
     */
    public $defaultSortField = 'id';

    /**
     * @access public.
     * @var String.
     */
    public $defaultSortDirection = 'DESC';

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'digitalsignage.feed';

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
        $criteria->select($this->modx->getSelectColumns('DigitalSignageBroadcastFeed', 'DigitalSignageBroadcastFeed'));
        $criteria->select($this->modx->getSelectColumns('DigitalSignageSlideType', 'DigitalSignageSlideType', 'slide_type_', ['key', 'name', 'icon']));

        $criteria->innerJoin('DigitalSignageSlideType', 'DigitalSignageSlideType', [
            '`DigitalSignageBroadcastFeed`.`slide_type_id` = `DigitalSignageSlideType`.`id`'
        ]);

        $broadcastID = $this->getProperty('broadcast_id');

        if (!empty($broadcastID)) {
            $criteria->where([
                'DigitalSignageBroadcastFeed.broadcast_id' => $broadcastID
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
            'time_formatted'            => $object->getTimeFormatted()
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

        return $array;
    }
}

return 'DigitalSignageBroadcastFeedGetListProcessor';
