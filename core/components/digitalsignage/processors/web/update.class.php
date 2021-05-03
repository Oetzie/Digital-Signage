<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageUpdateProcessor extends modObjectProcessor
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
     * @return Mixed.
     */
    public function process()
    {
        if (!$this->getProperty('preview')) {
            $player = $this->modx->getObject('DigitalSignagePlayer', [
                'key' => $this->getProperty('pl')
            ]);

            if ($player) {
                $restart = (int) $player->get('restart') === 1;

                $player->fromArray([
                    'last_online'       => date('Y-m-d H:i:s'),
                    'last_online_time'  => $this->getProperty('time'),
                    'last_broadcast_id' => $this->getProperty('bc'),
                    'restart'           => 0
                ]);

                if ($player->save()) {
                    $redirect = false;

                    if ($broadcast = $player->getBroadcastFor(time())) {
                        if ((int) $broadcast->get('id') !== (int)$this->getProperty('bc')) {
                            $redirect = $this->modx->makeUrl($broadcast->get('resource_id'), null, [
                                'pl' => $player->get('key'),
                                'bc' => $broadcast->get('id')
                            ], 'full');
                        }
                    }

                    return json_encode([
                        'success'   => true,
                        'message'   => 'Player updated.',
                        'restart'   => $restart,
                        'redirect'  => $redirect
                    ]);
                }
            }

            return json_encode([
                'success'   => false,
                'message'   => 'Player not found.'
            ]);
        }

        return json_encode([
            'success'   => true,
            'message'   => 'Player in preview mode.'
        ]);
    }
}

return 'DigitalSignageUpdateProcessor';
