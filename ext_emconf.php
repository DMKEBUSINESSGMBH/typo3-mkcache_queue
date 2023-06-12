<?php

/*
 * (c) DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This file is part of TYPO3 CMS-based extension "mkcache_queue" by DMK E-BUSINESS GmbH.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

$EM_CONF['mkcache_queue'] = [
    'title' => 'Clear the cache via a queue instead of directly',
    'category' => 'misc',
    'author' => 'Hannes Bochmann',
    'author_email' => 'dev@dmk-ebusiness.com',
    'author_company' => 'DMK E-Business GmbH',
    'shy' => 0,
    'version' => '12.0.1',
    'priority' => '',
    'module' => '',
    'state' => 'stable',
    'internal' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'lockType' => '',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.7-12.5.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    '_md5_values_when_last_written' => '',
];
