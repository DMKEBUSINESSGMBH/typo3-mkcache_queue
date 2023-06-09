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
use DMK\MkcacheQueue\Cache\Frontend\PhpFrontend;
use DMK\MkcacheQueue\Cache\Frontend\VariableFrontend;
use DMK\MkcacheQueue\Utility\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
        $this->resetSingletonInstances = true;
        parent::setUp();
    }

    /**
     * @test
     */
    public function cacheFrontendsAreReplaced()
    {
        $registry = $this->getMockBuilder(Registry::class)
            ->onlyMethods(['isCacheRegisteredToClearThroughQueue'])
            ->disableOriginalConstructor()
            ->getMock();
        $registry->expects(self::exactly(4))
            ->method('isCacheRegisteredToClearThroughQueue')
            ->withConsecutive(
                ['dummy_cache_variable'],
                ['dummy_cache_php'],
                ['dummy_cache_null'],
                ['ignored_dummy_cache_variable'],
            )
            ->willReturnOnConsecutiveCalls(true, true, true, false);
        GeneralUtility::setSingletonInstance(Registry::class, $registry);

        $cacheManager = $this->getAccessibleMock(CacheManager::class, ['registerCache']);
        $cacheManager->setCacheConfigurations([
            'dummy_cache_variable' => [
                'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
            ],
            'dummy_cache_php' => [
                'frontend' => \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class,
            ],
            'dummy_cache_null' => [
                'frontend' => \TYPO3\CMS\Core\Cache\Frontend\NullFrontend::class,
            ],
            'ignored_dummy_cache_variable' => [
                'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
            ],
        ]);

        self::assertSame(
            VariableFrontend::class,
            $cacheManager->_get('defaultCacheConfiguration')['frontend']
        );

        self::assertSame(
            [
                'dummy_cache_variable' => [
                    'frontend' => VariableFrontend::class,
                ],
                'dummy_cache_php' => [
                    'frontend' => PhpFrontend::class,
                ],
                'dummy_cache_null' => [
                    'frontend' => \TYPO3\CMS\Core\Cache\Frontend\NullFrontend::class,
                ],
                'ignored_dummy_cache_variable' => [
                    'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
                ],
            ],
            $cacheManager->_get('cacheConfigurations')
        );
    }
}
