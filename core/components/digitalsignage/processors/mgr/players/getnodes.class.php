<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignagePlayerGetNodesProcessor extends modObjectGetListProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'DigitalSignagePlayer';

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
    public $objectType = 'digitalsignage.player';

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
        $query = $this->getProperty('query');

        if (!empty($query)) {
            $criteria->where([
                'key:LIKE'      => '%' . $query . '%',
                'OR:name:LIKE'  => '%' . $query . '%'
            ]);
        }

        return $criteria;
    }
}

return 'DigitalSignagePlayerGetNodesProcessor';
