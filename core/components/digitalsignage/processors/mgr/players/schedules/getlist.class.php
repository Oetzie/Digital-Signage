<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignagePlayerScheduleGetListProcessor extends modObjectGetListProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'DigitalSignagePlayerSchedule';

    /**
     * @access public.
     * @var Array.
     */
    public $languageTopics = ['digitalsignage:default'];

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
    public $objectType = 'digitalsignage.schedule';

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
        $criteria->select($this->modx->getSelectColumns('DigitalSignagePlayerSchedule', 'DigitalSignagePlayerSchedule'));
        $criteria->select($this->modx->getSelectColumns('DigitalSignageBroadcast', 'DigitalSignageBroadcast', 'broadcast_', ['resource_id', 'color']));
        $criteria->select($this->modx->getSelectColumns('modResource', 'modResource', 'resource_', ['pagetitle', 'template']));

        $criteria->innerJoin('DigitalSignageBroadcast', 'DigitalSignageBroadcast', [
            'DigitalSignageBroadcast.id = DigitalSignagePlayerSchedule.broadcast_id'
        ]);
        $criteria->innerJoin('modResource', 'modResource', [
            'modResource.id = DigitalSignageBroadcast.resource_id'
        ]);

        $playerID = $this->getProperty('player_id');

        if (!empty($playerID)) {
            $criteria->where([
                'DigitalSignagePlayerSchedule.player_id' => $playerID
            ]);
        }

        $broadcastID = $this->getProperty('broadcast_id');

        if (!empty($broadcastID)) {
            $criteria->where([
                'DigitalSignagePlayerSchedule.broadcast_id' => $broadcastID
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
        return array_merge($object->toArray(), [
            'start_time'        => date($this->modx->getOption('manager_time_format'), strtotime($object->get('start_time'))),
            'start_date'        => date($this->modx->getOption('manager_date_format'), strtotime($object->get('start_date'))),
            'end_time'          => date($this->modx->getOption('manager_time_format'), strtotime($object->get('end_time'))),
            'end_date'          => date($this->modx->getOption('manager_date_format'), strtotime($object->get('end_date'))),
            'type_formatted'    => $this->modx->lexicon('digitalsignage.schedule_' . $object->get('type')),
            'date_formatted'    => $object->toString(),
            'entire_day'        => $object->isEntireDay()
        ]);
    }
}

return 'DigitalSignagePlayerScheduleGetListProcessor';
