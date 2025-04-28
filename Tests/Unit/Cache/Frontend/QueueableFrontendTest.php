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

namespace DMK\MkcacheQueue\Tests\Cache\Frontend;

use DMK\MkcacheQueue\Cache\Frontend\QueueableFrontend;
use DMK\MkcacheQueue\Utility\ExtensionConfiguration;
use DMK\MkcacheQueue\Utility\Queue;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Cache\Backend\BackendInterface;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class QueueableFrontendTest.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class QueueableFrontendTest extends UnitTestCase
{
    /**
     * @var ExtensionConfiguration|MockObject
     */
    protected MockObject $extensionConfiguration;

    /**
     * @var Queue|MockObject
     */
    protected MockObject $queueUtility;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extensionConfiguration = $this->getMockBuilder(ExtensionConfiguration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queueUtility = $this->getMockBuilder(Queue::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    public function testFlushByTagIfClearCacheQueueDisabled(): void
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('flushByTag')
            ->with('test_tag');

        $this->queueUtility->expects(self::never())
            ->method('addQueueEntryForFlushByTagMethod');

        $queueableFrontend = $this->getQueueableFrontend($cache, false);
        $queueableFrontend->flushByTag('test_tag');
    }

    public function testFlushByTagIfClearCacheQueueEnabled(): void
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::never())
            ->method('flushByTag');

        $this->queueUtility->expects(self::once())
            ->method('addQueueEntryForFlushByTagMethod')
            ->with('test_cache', 'test_tag');

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        $queueableFrontend->flushByTag('test_tag');
    }

    public function testFlushByTagsIfClearCacheQueueDisabled(): void
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('flushByTags')
            ->with(['test_tag']);

        $this->queueUtility->expects(self::never())
            ->method('addQueueEntryForFlushByTagsMethod');

        $queueableFrontend = $this->getQueueableFrontend($cache, false);
        $queueableFrontend->flushByTags(['test_tag']);
    }

    public function testFlushByTagsIfClearCacheQueueEnabled(): void
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::never())
            ->method('flushByTags');

        $this->queueUtility->expects(self::once())
            ->method('addQueueEntryForFlushByTagsMethod')
            ->with('test_cache', ['test_tag']);

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        $queueableFrontend->flushByTags(['test_tag']);
    }

    public function testRemoveIfClearCacheQueueDisabled(): void
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('remove')
            ->with('test_tag')
            ->willReturn(false);

        $this->queueUtility->expects(self::never())
            ->method('addQueueEntryForRemoveMethod');

        $queueableFrontend = $this->getQueueableFrontend($cache, false);
        self::assertFalse($queueableFrontend->remove('test_tag'));
    }

    public function testRemoveIfClearCacheQueueEnabled(): void
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::never())
            ->method('remove');

        $this->queueUtility->expects(self::once())
            ->method('addQueueEntryForRemoveMethod')
            ->with('test_cache', 'test_tag');

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        self::assertTrue($queueableFrontend->remove('test_tag'));
    }

    public function testFlushIfClearCacheQueueDisabled(): void
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('flush');

        $this->queueUtility->expects(self::never())
            ->method('addQueueEntryForFlushMethod');

        $queueableFrontend = $this->getQueueableFrontend($cache, false);
        $queueableFrontend->flush();
    }

    public function testFlushIfClearCacheQueueEnabled(): void
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::never())
            ->method('flush');

        $this->queueUtility->expects(self::once())
            ->method('addQueueEntryForFlushMethod');

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        $queueableFrontend->flush();
    }

    public function testGetIdentifier(): void
    {
        $cache = $this->getNullFrontend();

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        self::assertSame('test_cache', $queueableFrontend->getIdentifier());
    }

    public function testGetBackend(): void
    {
        $cache = $this->getNullFrontend();
        $backend = $this->getMockBuilder(BackendInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cache->expects(self::once())
            ->method('getBackend')
            ->willReturn($backend);

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        self::assertSame($backend, $queueableFrontend->getBackend());
    }

    public function testSet(): void
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('set')
            ->with('test_entry', ['data'], ['tags'], 123);

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        $queueableFrontend->set('test_entry', ['data'], ['tags'], 123);
    }

    public function testGet(): void
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('get')
            ->with('test_entry')
            ->willReturn('result');

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        self::assertSame('result', $queueableFrontend->get('test_entry'));
    }

    public function testHas(): void
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('has')
            ->with('test_entry')
            ->willReturn(true);

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        self::assertTrue($queueableFrontend->has('test_entry'));
    }

    public function testCollectGarbage(): void
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('collectGarbage');

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        $queueableFrontend->collectGarbage();
    }

    public function testIsValidEntryIdentifier(): void
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('isValidEntryIdentifier')
            ->with('test_entry')
            ->willReturn(true);

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        self::assertTrue($queueableFrontend->isValidEntryIdentifier('test_entry'));
    }

    public function testIsValidTag(): void
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('isValidTag')
            ->with('test_entry')
            ->willReturn(true);

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        self::assertTrue($queueableFrontend->isValidTag('test_entry'));
    }

    public function testArbitraryMethodIsForwarded(): void
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('requireOnce')
            ->with('test_entry')
            ->willReturn('result');

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        self::assertSame('result', $queueableFrontend->requireOnce('test_entry'));
    }

    protected function getQueueableFrontend(
        NullFrontend $actualCache,
        bool $isClearCacheQueueEnabled,
    ): QueueableFrontend {
        $this->extensionConfiguration->expects(self::any())
            ->method('isClearCacheQueueEnabled')
            ->willReturn($isClearCacheQueueEnabled);

        return new QueueableFrontend($actualCache, $this->extensionConfiguration, $this->queueUtility);
    }

    protected function getNullFrontend(): MockObject
    {
        $cache = $this->getMockBuilder(NullFrontend::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getIdentifier',
                'set',
                'get',
                'has',
                'collectGarbage',
                'isValidEntryIdentifier',
                'isValidTag',
                'flush',
                'getBackend',
                'remove',
                'flushByTags',
                'flushByTag',
                'requireOnce',
            ])
            ->getMock();

        $cache->expects(self::any())
            ->method('getIdentifier')
            ->willReturn('test_cache');

        return $cache;
    }
}
