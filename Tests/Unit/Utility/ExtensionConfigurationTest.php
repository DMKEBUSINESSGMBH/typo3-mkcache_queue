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

namespace DMK\MkcacheQueue\Tests\Utility;

use DMK\MkcacheQueue\Utility\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class ExtensionConfigurationTest.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class ExtensionConfigurationTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['mkcache_queue'] = [
            'cachesToClearThroughQueue' => 'dummy_cache_1, dummy_cache_2',
            'disableDirectCacheClearCompletely' => '1',
        ];
    }

    public function testGetCachesToClearThroughQueue(): void
    {
        $extensionConfiguration = GeneralUtility::makeInstance(
            ExtensionConfiguration::class,
            GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)
        );

        self::assertSame(
            [
                'dummy_cache_1',
                'dummy_cache_2',
            ],
            $extensionConfiguration->getCachesToClearThroughQueue()
        );
    }

    public function testIsDirectCacheClearDisabledCompletely(): void
    {
        $extensionConfiguration = GeneralUtility::makeInstance(
            ExtensionConfiguration::class,
            GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)
        );

        self::assertTrue($extensionConfiguration->isDirectCacheClearDisabledCompletely());
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['mkcache_queue']['disableDirectCacheClearCompletely'] = '0';
        self::assertFalse($extensionConfiguration->isDirectCacheClearDisabledCompletely());
    }

    public function testDisableAndEnableClearCacheQueue(): void
    {
        $extensionConfiguration = GeneralUtility::makeInstance(
            ExtensionConfiguration::class,
            GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)
        );

        self::assertTrue($extensionConfiguration->isClearCacheQueueEnabled());

        // disable not allowed
        $extensionConfiguration->disableClearCacheQueue();
        self::assertTrue($extensionConfiguration->isClearCacheQueueEnabled());

        // allow disable
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['mkcache_queue']['disableDirectCacheClearCompletely'] = '0';
        $extensionConfiguration->disableClearCacheQueue();
        self::assertFalse($extensionConfiguration->isClearCacheQueueEnabled());

        $extensionConfiguration->enableClearCacheQueue();
        self::assertTrue($extensionConfiguration->isClearCacheQueueEnabled());
    }

    public function testDisableClearCacheQueueForCacheFlushCliCommand(): void
    {
        $extensionConfiguration = GeneralUtility::makeInstance(
            ExtensionConfiguration::class,
            GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)
        );

        // allow disable
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['mkcache_queue']['disableDirectCacheClearCompletely'] = '0';

        self::assertTrue($extensionConfiguration->isClearCacheQueueEnabled());

        $_SERVER['argv'] = ['bin/typo3', 'cache:warmup'];
        $extensionConfiguration->disableClearCacheQueueForCacheFlushCliCommand();
        self::assertTrue($extensionConfiguration->isClearCacheQueueEnabled());

        $_SERVER['argv'] = ['bin/typo3', 'cache:flush'];
        $extensionConfiguration->disableClearCacheQueueForCacheFlushCliCommand();
        self::assertFalse($extensionConfiguration->isClearCacheQueueEnabled());
    }
}
