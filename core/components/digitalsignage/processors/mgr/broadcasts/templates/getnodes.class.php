<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageBroadcastTemplateGetNodesProcessor extends modObjectGetListProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'modTemplate';

    /**
     * @access public.
     * @var Array.
     */
    public $languageTopics = ['template', 'category'];

    /**
     * @access public.
     * @var String.
     */
    public $defaultSortField = 'templatename';

    /**
     * @access public.
     * @var String.
     */
    public $defaultSortDirection = 'ASC';

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'digitalsignage.tempate';

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
        $criteria->where([
            'id:IN' => $this->modx->digitalsignage->config['templates']
        ]);

        return $criteria;
    }
}

return 'DigitalSignageBroadcastTemplateGetNodesProcessor';
