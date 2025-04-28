<?php

/*
 * Copyright notice
 *
 * (c) DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This file is part of the "mkcache_queue" Extension for TYPO3 CMS.
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GNU Lesser General Public License can be found at
 * www.gnu.org/licenses/lgpl.html
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

defined('TYPO3') || exit;

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Core\Cache\CacheManager::class] = [
    'className' => DMK\MkcacheQueue\Cache\CacheManager::class,
];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Backend\Controller\ClearPageCacheController::class] = [
    'className' => DMK\MkcacheQueue\Controller\Backend\ClearPageCacheController::class,
];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Backend\Controller\SimpleDataHandlerController::class] = [
    'className' => DMK\MkcacheQueue\Controller\Backend\SimpleDataHandlerController::class,
];
TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(DMK\MkcacheQueue\Utility\ExtensionConfiguration::class)
    ->disableClearCacheQueueForCacheFlushCliCommand();
TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(DMK\MkcacheQueue\Utility\Registry::class)
    ->registerCachesToClearThroughQueueByConfiguration();
