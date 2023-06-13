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

    /**
     * @test
     */
    public function registerCache()
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

    /**
     * @test
     */
    public function registerCacheIfNotQueueable()
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

    /**
     * @test
     */
    public function registerCacheIfFluidTemplateCache()
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
