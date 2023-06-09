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

defined('TYPO3') || exit;

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\Cache\CacheManager::class] = [
    'className' => \DMK\MkcacheQueue\Cache\CacheManager::class,
];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Recordlist\Controller\ClearPageCacheController::class] = [
    'className' => \DMK\MkcacheQueue\Controller\Recordlist\ClearPageCacheController::class,
];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Controller\SimpleDataHandlerController::class] = [
    'className' => \DMK\MkcacheQueue\Controller\Backend\SimpleDataHandlerController::class,
];
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\DMK\MkcacheQueue\Utility\ExtensionConfiguration::class)
    ->disableClearCacheQueueForCacheFlushCliCommand();
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\DMK\MkcacheQueue\Utility\Registry::class)
    ->registerCachesToClearThroughQueueByConfiguration();
