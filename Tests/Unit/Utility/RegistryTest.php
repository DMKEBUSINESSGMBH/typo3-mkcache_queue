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

namespace DMK\MkcacheQueue\Tests\Utility;

use DMK\MkcacheQueue\Utility\ExtensionConfiguration;
use DMK\MkcacheQueue\Utility\Registry;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class RegistryTest.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class RegistryTest extends UnitTestCase
{
    /**
     * @test
     */
    public function registerCacheToClearThroughQueue()
    {
        $registry = $this->getAccessibleMock(Registry::class, null, [], '', false);

        self::assertSame([], $registry->_get('registeredCaches'));
        $registry->registerCacheToClearThroughQueue('dummy_cache');
        self::assertSame(['dummy_cache' => true], $registry->_get('registeredCaches'));
    }

    /**
     * @test
     */
    public function registerCachesToClearThroughQueue()
    {
        $registry = $this->getAccessibleMock(Registry::class, null, [], '', false);

        self::assertSame([], $registry->_get('registeredCaches'));
        $registry->registerCachesToClearThroughQueue(['dummy_cache_1', 'dummy_cache_2']);
        self::assertSame(['dummy_cache_1' => true, 'dummy_cache_2' => true], $registry->_get('registeredCaches'));
    }

    /**
     * @test
     */
    public function registerCachesToClearThroughQueueByConfiguration()
    {
        $extensionConfiguration = $this->getMockBuilder(ExtensionConfiguration::class)
            ->disableOriginalConstructor()
            ->getMock();
        $extensionConfiguration->expects(self::once())
            ->method('getCachesToClearThroughQueue')
            ->willReturn(['dummy_cache_1', 'dummy_cache_2']);
        $registry = $this->getAccessibleMock(Registry::class, null, [$extensionConfiguration]);

        self::assertSame([], $registry->_get('registeredCaches'));
        $registry->registerCachesToClearThroughQueueByConfiguration();
        self::assertSame(['dummy_cache_1' => true, 'dummy_cache_2' => true], $registry->_get('registeredCaches'));
    }

    /**
     * @test
     */
    public function isCacheRegisteredToClearThroughQueue()
    {
        $registry = $this->getAccessibleMock(Registry::class, null, [], '', false);

        self::assertFalse($registry->isCacheRegisteredToClearThroughQueue('dummy_cache'));
        $registry->registerCacheToClearThroughQueue('dummy_cache');
        self::assertTrue($registry->isCacheRegisteredToClearThroughQueue('dummy_cache'));
    }
}
