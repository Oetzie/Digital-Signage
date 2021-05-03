<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignage
{
    /**
     * @access public.
     * @var modX.
     */
    public $modx;

    /**
     * @access public.
     * @var Array.
     */
    public $config = [];

    /**
     * @access public.
     * @param modX $modx.
     * @param Array $config.
     */
    public function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;

        $corePath   = $this->modx->getOption('digitalsignage.core_path', $config, $this->modx->getOption('core_path') . 'components/digitalsignage/');
        $assetsUrl  = $this->modx->getOption('digitalsignage.assets_url', $config, $this->modx->getOption('assets_url') . 'components/digitalsignage/');
        $assetsPath = $this->modx->getOption('digitalsignage.assets_path', $config, $this->modx->getOption('assets_path') . 'components/digitalsignage/');

        $this->config = array_merge([
            'namespace'                 => 'digitalsignage',
            'lexicons'                  => ['digitalsignage:default', 'digitalsignage:slides', 'site:digitalsignage'],
            'base_path'                 => $corePath,
            'core_path'                 => $corePath,
            'model_path'                => $corePath . 'model/',
            'processors_path'           => $corePath . 'processors/',
            'elements_path'             => $corePath . 'elements/',
            'chunks_path'               => $corePath . 'elements/chunks/',
            'plugins_path'              => $corePath . 'elements/plugins/',
            'snippets_path'             => $corePath . 'elements/snippets/',
            'templates_path'            => $corePath . 'templates/',
            'assets_path'               => $assetsPath,
            'js_url'                    => $assetsUrl . 'js/',
            'css_url'                   => $assetsUrl . 'css/',
            'assets_url'                => $assetsUrl,
            'connector_url'             => $assetsUrl . 'connector.php',
            'version'                   => '1.3.0',
            'branding_url'              => $this->modx->getOption('digitalsignage.branding_url', null, ''),
            'branding_help_url'         => $this->modx->getOption('digitalsignage.branding_url_help', null, ''),
            'permissions'               => [
                'admin'                     => $this->modx->hasPermission('digitalsignage_admin')
            ],
            'templates'                 => $this->getTemplates(),
            'rte_config'                => json_decode($this->modx->getOption('digitalsignage.rte_config', null, ''), true)
        ], $config);

        $this->modx->addPackage('digitalsignage', $this->config['model_path']);

        if (is_array($this->config['lexicons'])) {
            foreach ($this->config['lexicons'] as $lexicon) {
                $this->modx->lexicon->load($lexicon);
            }
        } else {
            $this->modx->lexicon->load($this->config['lexicons']);
        }
    }

    /**
     * @access public.
     * @return String|Boolean.
     */
    public function getHelpUrl()
    {
        if (!empty($this->config['branding_help_url'])) {
            return $this->config['branding_help_url'] . '?v=' . $this->config['version'];
        }

        return false;
    }
    /**
     * @access public.
     * @return String|Boolean.
     */
    public function getBrandingUrl()
    {
        if (!empty($this->config['branding_url'])) {
            return $this->config['branding_url'];
        }

        return false;
    }

    /**
     * @access public.
     * @param String $key.
     * @param Array $options.
     * @param Mixed $default.
     * @return Mixed.
     */
    public function getOption($key, array $options = [], $default = null)
    {
        if (isset($options[$key])) {
            return $options[$key];
        }

        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        return $this->modx->getOption($this->config['namespace'] . '.' . $key, $options, $default);
    }

    /**
     * @access public.
     * @return Array.
     */
    public function getTemplates()
    {
        $templates  = [];
        $data       = $this->modx->getCollection('modTemplate', [
            'id:IN' => explode(',', $this->modx->getOption('digitalsignage.templates'))
        ]);

        foreach ($data as $template) {
            $templates[] = $template->get('id');
        }

        return $templates;
    }

    /**
     * @access public.
     * @param String $key.
     * @return Object|Null.
     */
    public function getPlayer($key)
    {
        return $this->modx->getObject('DigitalSignagePlayer', [
            'key' => $key
        ]);
    }

    /**
     * @access public.
     * @param Integer $id.
     * @return Object|Null.
     */
    public function getBroadcast($id)
    {
        return $this->modx->getObject('DigitalSignageBroadcast', [
            'id' => $id
        ]);
    }

    /**
     * @access public,
     * @return String.
     */
    public function getMediaSourceUrl()
    {
        $mediaSource = $this->modx->getObject('modMediaSource', [
            'id' => $this->modx->getOption('digitalsignage.media_source')
        ]);

        if ($mediaSource) {
            $mediaSource = $mediaSource->get('properties');

            if (isset($mediaSource['baseUrl']['value'])) {
                return '/' . ltrim($mediaSource['baseUrl']['value'], '/');
            }
        }

        return '/';
    }

    /**
     * @access public.
     * @param String $url.
     * @return Array|Null.
     */
    public function getFeedData($url)
    {
        $data = [];
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
            if (isset($info['content_type'])) {
                if (strpos($info['content_type'],'xml') !== false) {
                    $response = simplexml_load_string($response, null, LIBXML_NOCDATA);

                    if ($response && isset($response->channel->item)) {
                        foreach ($response->channel->item as $value) {
                            $data[] = $this->toFeedDataArray($value);
                        }
                    }
                } else if (strpos($info['content_type'],'json') !== false) {
                    $response = json_decode($response, true);

                    if ($response && isset($response->items)) {
                        foreach ($response->items as $value) {
                            $data[] = $this->toFeedDataArray($value);
                        }
                    }
                }
            }
        }

        foreach ($data as $key => $value) {
            $value = array_change_key_case($value, CASE_LOWER);

            if (isset($value['enclosure'])) {
                $value['image'] = $value['enclosure'];

                unset($value['enclosure']);
            }

            if (isset($value['description']) && !isset($value['content'])) {
                $value['content'] = $value['description'];
            }

            if (isset($value['content'])) {
                $value['content'] = trim(preg_replace('~[\r\n]+~', ' ', $value['content']));
            }

            if (isset($value['description'])) {
                $value['description'] = trim(preg_replace('~[\r\n]+~', ' ', $value['description']));
            }

            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * @access public.
     * @param Mixed $data.
     * @return Array.
     */
    public function toFeedDataArray($data)
    {
        $array = [];

        foreach ($data as $key => $value) {
            if (get_class($value) === 'SimpleXMLElement') {
                if ($value->attributes()) {
                    if ($value->attributes()->url) {
                        $array[$key] = (string) $value->attributes()->url;
                    }
                } else if ($value->count() === 0) {
                    $array[$key] = (string) $value;
                } else {
                    $array[$key] = (array) $this->toFeedDataArray($value);
                }
            } else {
                $array[$key] = (string) $value;
            }
        }

        return $array;
    }
}
