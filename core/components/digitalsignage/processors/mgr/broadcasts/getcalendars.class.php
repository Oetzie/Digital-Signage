<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageBroadcastGetCalendarProcessor extends modObjectGetListProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'DigitalSignageBroadcast';

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
    public $objectType = 'digitalsignage.broadcast';

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
        $criteria->select($this->modx->getSelectColumns('DigitalSignageBroadcast', 'DigitalSignageBroadcast'));
        $criteria->select($this->modx->getSelectColumns('modResource', 'modResource', null, ['pagetitle']));

        $criteria->innerJoin('modResource', 'modResource', [
            '`modResource`.`id` = `DigitalSignageBroadcast`.`resource_id`'
        ]);

        return $criteria;
    }

    /**
     * @access public.
     * @param xPDOObject $object.
     * @return Array.
     */
    public function prepareRow(xPDOObject $object)
    {
        return [
            'id'    => $object->get('id'),
            'title' => $object->get('pagetitle'),
            'color' => $object->get('color')
        ];
    }
}

return 'DigitalSignageBroadcastGetCalendarProcessor';
