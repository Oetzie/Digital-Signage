<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageMediaProcessor extends modObjectProcessor
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
        $url = $this->getProperty('url');

        if (!empty($url)) {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL             => $url,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_CONNECTTIMEOUT  => 10,
                CURLOPT_TIMEOUT         => 10,
                CURLOPT_FOLLOWLOCATION  => true
            ]);

            $response   = curl_exec($curl);
            $info       = curl_getinfo($curl);

            curl_close($curl);

            if (isset($info['http_code']) || $info['http_code'] === '200') {
                header('Content-type: ' .  $info['content_type']);

                return $response;
            }
        }

        return '';
    }
}

return 'DigitalSignageMediaProcessor';
