<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

require_once __DIR__ . '/digitalsignage.class.php';

class DigitalSignagePlugins extends DigitalSignage
{
    /**
     * @access public.
     */
    public function onHandleRequest()
    {
        if ($this->modx->context->get('key') !== 'mgr') {
            $base       = '/ds/';
            $context    = $this->modx->getOption('digitalsignage.context', null, 'ds');
            $setting    = $this->modx->getObject('modContextSetting', [
                'context_key'   => $context,
                'key'           => 'base_url'
            ]);

            if ($setting) {
                $base = $setting->get('value');
            }

            if (strpos($_SERVER['REQUEST_URI'], $base) === 0) {
                $this->modx->switchContext($context);

                $this->modx->setOption('site_start', $this->getOption('request_resource'));
                $this->modx->setOption('error_page', $this->getOption('request_resource'));

                if ((int) $this->modx->getOption('friendly_urls') === 1) {
                    $alias = $this->modx->getOption('request_param_alias', null, 'q');

                    if (isset($_REQUEST[$alias])) {
                        $_REQUEST[$alias] = substr('/' . ltrim($_REQUEST[$alias], '/'), strlen($base));
                    }
                }
            }
        }
    }

    /**
     * @access public.
     */
    public function onLoadWebDocument()
    {
        if ($this->modx->context->get('key') === $this->modx->getOption('digitalsignage.context', null, 'ds')) {
            if ((int) $this->modx->resource->get('id') === (int) $this->modx->getOption('digitalsignage.request_resource')) {
                if (isset($_GET['pl'])) {
                    $player = $this->getPlayer($_GET['pl']);

                    if ($player) {
                        $broadcast = $player->getBroadcastFor(time());

                        if ($broadcast) {
                            $redirect = $this->modx->makeUrl($broadcast->get('resource_id'), null, [
                                'pl' => $player->get('key'),
                                'bc' => $broadcast->get('id')
                            ], 'full');

                            $this->modx->sendRedirect($redirect);
                        } else {
                            $this->setError('There is currently no broadcast available for player with key "' . $_GET['pl'] . '".');
                        }
                    } else {
                        $this->setError('There is currently no player available for key "' . $_GET['pl'] . '".');
                    }
                } else {
                    $this->setError('There is currently no player available.');
                }
            } else if (in_array($this->modx->resource->get('template'), $this->config['templates'], false)) {
                if (isset($_GET['pl'], $_GET['bc'])) {
                    $player = $this->getPlayer($_GET['pl']);

                    if ($player) {
                        $broadcast = $this->getBroadcast($_GET['bc']);

                        if ($broadcast) {
                            $this->modx->toPlaceholders([
                                'hash'      => time(),
                                'player'    => array_merge($player->toArray(), [
                                    'mode'      => $player->getMode()
                                ]),
                                'broadcast'    => array_merge($broadcast->toArray(), [
                                    'feed'      => '/assets/components/digitalsignage/connector.php?action=web/data'
                                ]),
                                'callback'  => [
                                    'feed'      => '/assets/components/digitalsignage/connector.php?action=web/update'
                                ],
                                'feed'      => [
                                    'feed'      => '/assets/components/digitalsignage/connector.php?action=web/feed'
                                ],
                                'preview'   => isset($_GET['preview']) ? 1 : 0
                            ], 'digitalsignage');
                        } else {
                            $this->setError('There is currently no broadcast available for ID "' . $_GET['bc'] . '".');
                        }
                    } else {
                        $this->setError('There is currently no player available for key "' . $_GET['pl'] . '".');
                    }
                } else {
                    $this->setError('There is currently no player available.');
                }
            }
        }
    }

    /**
     * @access protected.
     * @param String $errorMessage.
     */
    protected function setError($errorMessage)
    {
        if (file_exists(MODX_CORE_PATH . 'error/unavailable.include.php')) {
            include MODX_CORE_PATH . 'error/unavailable.include.php';
        }

        exit($errorMessage);
    }
}
