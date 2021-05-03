<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageSlideType extends xPDOObject
{
    /**
     * @access public.
     * @param String $col;
     * @param String $dir.
     * @return Array.
     */
    public function getData($col = 'menuindex', $dir = 'ASC')
    {
        $data = json_decode($this->get('data'), true);

        if ($data) {
            $sort = [];

            foreach ((array) $data as $key => $row) {
                if (isset($row[$col])) {
                    $sort[$key] = $row[$col];
                } else {
                    $sort[$key] = $row['key'];
                }
            }

            if (strtoupper($dir) === 'DESC') {
                array_multisort($sort, SORT_DESC, $data);
            } else {
                array_multisort($sort, SORT_ASC, $data);
            }

            return $data;
        }

        return [];
    }
}
