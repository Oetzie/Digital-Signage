<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageFeedProcessor extends modObjectProcessor
{
    /**
     * @access public.
     * @var Array.
     */
    public $languageTopics = ['digitalsignage:default'];

    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->modx->getService('digitalsignage', 'DigitalSignage', $this->modx->getOption('digitalsignage.core_path', null, $this->modx->getOption('core_path') . 'components/digitalsignage/') . 'model/digitalsignage/');

        $this->setDefaultProperties([
            'preview' => false
        ]);

        return parent::initialize();
    }

    /**
     * @access public.
     * @return String.
     */
    public function process()
    {
        $items = [];

        if ($url = $this->getFeedUrl()) {
            $items = $this->modx->digitalsignage->getFeedData($url);
        }

        return json_encode([
            'success'   => true,
            'items'     => $items
        ]);
    }

    /**
     * @access public.
     * @return String.
     */
    public function getFeedUrl()
    {
        $url = $this->getProperty('url');

        if (empty($url)) {
            $broadcast = $this->modx->getObject('DigitalSignageBroadcast', [
                'id' => $this->getProperty('bc')
            ]);

            if ($broadcast) {
                $url = $broadcast->get('ticker_url');
            }
        }

        return $url;
    }
}

return 'DigitalSignageFeedProcessor';
