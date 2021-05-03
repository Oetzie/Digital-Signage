<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageDataProcessor extends modObjectProcessor
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
        $broadcast = $this->modx->getObject('DigitalSignageBroadcast', [
            'id' => $this->getProperty('bc')
        ]);

        if ($broadcast) {
            $slides = [];

            $mediaSourceUrl = $this->modx->digitalsignage->getMediaSourceUrl();

            if (!$this->getProperty('preview')) {
                $slides = $broadcast->fromExport();
            }

            if (count($slides) === 0) {
                foreach ((array) $broadcast->getSlides() as $slide) {
                    if ((int) $slide->get('active') === 1) {
                        if ($data = $slide->getFormatted()) {
                            $slides[] = $data;
                        }
                    }
                }
            }

            if (!$this->getProperty('preview')) {
                if ((bool) $this->modx->getOption('digitalsignage.auto_create_sync', null, true)) {
                    $broadcast->toExport($slides);
                }
            }

            foreach ($slides as $key => $value) {
                foreach ((array) $value as $subKey => $subValue) {
                    if (!empty($subValue) && stripos($subKey, 'extern') === false) {
                        if (stripos($subKey, 'image') !== false) {
                            $slides[$key][$subKey] = $mediaSourceUrl . $subValue;
                        } else if (stripos($subKey, 'video') !== false) {
                            $slides[$key][$subKey] = $mediaSourceUrl . $subValue;
                        }
                    }
                }
            }

            foreach ((array) $broadcast->getFeeds() as $feed) {
                if ((int) $feed->get('active') === 1 && (int) $feed->get('frequency') === 0) {
                    foreach ($feed->getSlides() as $slide) {
                        $slides[] = array_merge($slide, $feed->getFormatted());
                    }
                }
            }

            $total = count($slides);

            if (count($slides) <= 30) {
                $newSlides  = $slides;
                $duplicates = ceil(30 / $total);

                for ($duplicate = 0; $duplicate < $duplicates; $duplicate++) {
                    foreach ($slides as $slide) {
                        $newSlides[] = $slide;
                    }
                }

                $slides     = $newSlides;
            }

            $total = count($slides);

            foreach ((array) $broadcast->getFeeds() as $feed) {
                if ((int) $feed->get('active') === 1 && (int) $feed->get('frequency') > 0) {
                    foreach ($feed->getSlides() as $slice => $slide) {
                        if ($slice < ceil($total / $feed->get('frequency'))) {
                            array_splice($slides, (($slice + 1) * $feed->get('frequency')) + $slice, 0, [array_merge($slide, $feed->getFormatted())]);
                        }
                    }
                }
            }

            return json_encode([
                'success'   => true,
                'slides'   => $slides
            ]);
        }

        return json_encode([
            'success'   => false,
            'message'   => 'Broadcast not found.'
        ]);
    }
}

return 'DigitalSignageDataProcessor';
