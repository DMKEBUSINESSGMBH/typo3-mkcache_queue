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
     * @var ExtensionConfiguration|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $extensionConfiguration;

    /**
     * @var Queue|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $queueUtility;

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

    /**
     * @test
     */
    public function flushByTagIfClearCacheQueueDisabled()
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

    /**
     * @test
     */
    public function flushByTagIfClearCacheQueueEnabled()
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

    /**
     * @test
     */
    public function flushByTagsIfClearCacheQueueDisabled()
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

    /**
     * @test
     */
    public function flushByTagsIfClearCacheQueueEnabled()
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

    /**
     * @test
     */
    public function removeIfClearCacheQueueDisabled()
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

    /**
     * @test
     */
    public function removeIfClearCacheQueueEnabled()
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

    /**
     * @test
     */
    public function flushIfClearCacheQueueDisabled()
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('flush');

        $this->queueUtility->expects(self::never())
            ->method('addQueueEntryForFlushMethod');

        $queueableFrontend = $this->getQueueableFrontend($cache, false);
        $queueableFrontend->flush();
    }

    /**
     * @test
     */
    public function flushIfClearCacheQueueEnabled()
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::never())
            ->method('flush');

        $this->queueUtility->expects(self::once())
            ->method('addQueueEntryForFlushMethod');

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        $queueableFrontend->flush();
    }

    /**
     * @test
     */
    public function getIdentifier()
    {
        $cache = $this->getNullFrontend();

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        self::assertSame('test_cache', $queueableFrontend->getIdentifier());
    }

    /**
     * @test
     */
    public function getBackend()
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

    /**
     * @test
     */
    public function set()
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('set')
            ->with('test_entry', ['data'], ['tags'], 123);

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        $queueableFrontend->set('test_entry', ['data'], ['tags'], 123);
    }

    /**
     * @test
     */
    public function get()
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('get')
            ->with('test_entry')
            ->willReturn('result');

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        self::assertSame('result', $queueableFrontend->get('test_entry'));
    }

    /**
     * @test
     */
    public function has()
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('has')
            ->with('test_entry')
            ->willReturn(true);

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        self::assertTrue($queueableFrontend->has('test_entry'));
    }

    /**
     * @test
     */
    public function collectGarbage()
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('collectGarbage');

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        $queueableFrontend->collectGarbage();
    }

    /**
     * @test
     */
    public function isValidEntryIdentifier()
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('isValidEntryIdentifier')
            ->with('test_entry')
            ->willReturn(true);

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        self::assertTrue($queueableFrontend->isValidEntryIdentifier('test_entry'));
    }

    /**
     * @test
     */
    public function isValidTag()
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('isValidTag')
            ->with('test_entry')
            ->willReturn(true);

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        self::assertTrue($queueableFrontend->isValidTag('test_entry'));
    }

    /**
     * @test
     */
    public function arbitraryMethodIsForwarded()
    {
        $cache = $this->getNullFrontend();
        $cache->expects(self::once())
            ->method('dummy')
            ->with('test_entry')
            ->willReturn('result');

        $queueableFrontend = $this->getQueueableFrontend($cache, true);
        self::assertSame('result', $queueableFrontend->dummy('test_entry'));
    }

    protected function getQueueableFrontend(
        NullFrontend $actualCache,
        bool $isClearCacheQueueEnabled
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
            ])
            ->addMethods(['dummy'])
            ->getMock();

        $cache->expects(self::any())
            ->method('getIdentifier')
            ->willReturn('test_cache');

        return $cache;
    }
}
