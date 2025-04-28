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

namespace DMK\MkcacheQueue\Tests\Cache;

use DMK\MkcacheQueue\Cache\CacheManager;
use DMK\MkcacheQueue\Cache\Frontend\QueueableFrontend;
use DMK\MkcacheQueue\Utility\ExtensionConfiguration;
use DMK\MkcacheQueue\Utility\Queue;
use DMK\MkcacheQueue\Utility\Registry;
use TYPO3\CMS\Core\Cache\Backend\PhpCapableBackendInterface;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Cache\FluidTemplateCache;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class SimpleDataHandlerControllerTest.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class CacheManagerTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // nede for DI
        $extensionConfiguration = $this->getMockBuilder(ExtensionConfiguration::class)
            ->disableOriginalConstructor()
            ->getMock();
        GeneralUtility::addInstance(ExtensionConfiguration::class, $extensionConfiguration);

        $queue = $this->getMockBuilder(Queue::class)
            ->disableOriginalConstructor()
            ->getMock();
        GeneralUtility::addInstance(Queue::class, $queue);
    }

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    public function testRegisterCache(): void
    {
        $registry = $this->getMockBuilder(Registry::class)
            ->onlyMethods(['isCacheRegisteredToClearThroughQueue'])
            ->disableOriginalConstructor()
            ->getMock();
        $registry->expects(self::once())
            ->method('isCacheRegisteredToClearThroughQueue')
            ->with('test')
            ->willReturn(true);
        GeneralUtility::addInstance(Registry::class, $registry);

        $cacheManager = new CacheManager();
        $cache = new NullFrontend('test');

        $cacheManager->registerCache($cache);

        $mappedCache = $cacheManager->getCache('test');
        self::assertInstanceOf(QueueableFrontend::class, $mappedCache);

        $property = new \ReflectionProperty($mappedCache, 'actualCache');
        $property->setAccessible(true);
        self::assertSame($cache, $property->getValue($mappedCache));
    }

    public function testRegisterCacheIfNotQueueable(): void
    {
        $registry = $this->getMockBuilder(Registry::class)
            ->onlyMethods(['isCacheRegisteredToClearThroughQueue'])
            ->disableOriginalConstructor()
            ->getMock();
        $registry->expects(self::once())
            ->method('isCacheRegisteredToClearThroughQueue')
            ->with('test')
            ->willReturn(false);
        GeneralUtility::addInstance(Registry::class, $registry);

        $cacheManager = new CacheManager();
        $cache = new NullFrontend('test');

        $cacheManager->registerCache($cache);

        self::assertSame($cache, $cacheManager->getCache('test'));
    }

    public function testRegisterCacheIfFluidTemplateCache(): void
    {
        $registry = $this->getMockBuilder(Registry::class)
            ->onlyMethods(['isCacheRegisteredToClearThroughQueue'])
            ->disableOriginalConstructor()
            ->getMock();
        $registry->expects(self::once())
            ->method('isCacheRegisteredToClearThroughQueue')
            ->with('test')
            ->willReturn(true);
        GeneralUtility::addInstance(Registry::class, $registry);

        $cacheManager = new CacheManager();
        $cache = new FluidTemplateCache('test', $this->getMockBuilder(PhpCapableBackendInterface::class)->getMock());

        $cacheManager->registerCache($cache);

        self::assertSame($cache, $cacheManager->getCache('test'));
    }
}
