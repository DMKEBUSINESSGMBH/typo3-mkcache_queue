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
    public function testRegisterCacheToClearThroughQueue(): void
    {
        $registry = $this->getAccessibleMock(Registry::class, null, [], '', false);

        self::assertSame([], $registry->_get('registeredCaches'));
        $registry->registerCacheToClearThroughQueue('dummy_cache');
        self::assertSame(['dummy_cache' => true], $registry->_get('registeredCaches'));
    }

    public function testRegisterCachesToClearThroughQueue(): void
    {
        $registry = $this->getAccessibleMock(Registry::class, null, [], '', false);

        self::assertSame([], $registry->_get('registeredCaches'));
        $registry->registerCachesToClearThroughQueue(['dummy_cache_1', 'dummy_cache_2']);
        self::assertSame(['dummy_cache_1' => true, 'dummy_cache_2' => true], $registry->_get('registeredCaches'));
    }

    public function testRegisterCachesToClearThroughQueueByConfiguration(): void
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

    public function testIsCacheRegisteredToClearThroughQueue(): void
    {
        $registry = $this->getAccessibleMock(Registry::class, null, [], '', false);

        self::assertFalse($registry->isCacheRegisteredToClearThroughQueue('dummy_cache'));
        $registry->registerCacheToClearThroughQueue('dummy_cache');
        self::assertTrue($registry->isCacheRegisteredToClearThroughQueue('dummy_cache'));
    }
}
