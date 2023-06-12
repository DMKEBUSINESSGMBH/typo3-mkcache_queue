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

use DMK\MkcacheQueue\Cache\Frontend\ClearCacheThroughQueueTrait;
use DMK\MkcacheQueue\Utility\ExtensionConfiguration;
use DMK\MkcacheQueue\Utility\Queue;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class ClearCacheThroughQueueTraitTest.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class ClearCacheThroughQueueTraitTest extends UnitTestCase
{
    /**
     * @var ClearCacheThroughQueueTrait|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $trait;

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

        $this->trait = $this->getMockForTrait(
            ClearCacheThroughQueueTrait::class,
            [],
            '',
            true,
            true,
            true,
            ['callMethodOnParent', 'getExtensionConfiguration', 'getQueueUtility', 'getIdentifier', 'validateParent']
        );

        $this->trait->expects(self::any())
            ->method('getIdentifier')
            ->willReturn('test_cache');

        $this->extensionConfiguration = $this->getMockBuilder(ExtensionConfiguration::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->trait->expects(self::any())
            ->method('getExtensionConfiguration')
            ->willReturn($this->extensionConfiguration);

        $this->queueUtility = $this->getMockBuilder(Queue::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     */
    public function flushByTagIfClearCacheDisabled()
    {
        $this->extensionConfiguration->expects(self::once())
            ->method('isClearCacheQueueEnabled')
            ->willReturn(false);
        $this->trait->expects(self::never())
            ->method('getQueueUtility');
        $this->trait->expects(self::once())
            ->method('callMethodOnParent')
            ->with('flushByTag', ['test_tag']);

        $this->trait->flushByTag('test_tag');
    }

    /**
     * @test
     */
    public function flushByTagIfClearCacheEnabled()
    {
        $this->extensionConfiguration->expects(self::once())
            ->method('isClearCacheQueueEnabled')
            ->willReturn(true);
        $this->queueUtility->expects(self::once())
            ->method('addQueueEntryForFlushByTagMethod')
            ->with('test_cache', 'test_tag');
        $this->trait->expects(self::once())
            ->method('getQueueUtility')
            ->willReturn($this->queueUtility);
        $this->trait->expects(self::never())
            ->method('callMethodOnParent');

        $this->trait->flushByTag('test_tag');
    }

    /**
     * @test
     */
    public function flushByTagsIfClearCacheDisabled()
    {
        $this->extensionConfiguration->expects(self::once())
            ->method('isClearCacheQueueEnabled')
            ->willReturn(false);
        $this->trait->expects(self::never())
            ->method('getQueueUtility');
        $this->trait->expects(self::once())
            ->method('callMethodOnParent')
            ->with('flushByTags', [['test_tag']]);

        $this->trait->flushByTags(['test_tag']);
    }

    /**
     * @test
     */
    public function flushByTagsIfClearCacheEnabled()
    {
        $this->extensionConfiguration->expects(self::once())
            ->method('isClearCacheQueueEnabled')
            ->willReturn(true);
        $this->queueUtility->expects(self::once())
            ->method('addQueueEntryForFlushByTagsMethod')
            ->with('test_cache', ['test_tag']);
        $this->trait->expects(self::once())
            ->method('getQueueUtility')
            ->willReturn($this->queueUtility);
        $this->trait->expects(self::never())
            ->method('callMethodOnParent');

        $this->trait->flushByTags(['test_tag']);
    }

    /**
     * @test
     */
    public function removeIfClearCacheDisabled()
    {
        $this->extensionConfiguration->expects(self::once())
            ->method('isClearCacheQueueEnabled')
            ->willReturn(false);
        $this->trait->expects(self::never())
            ->method('getQueueUtility');
        $this->trait->expects(self::once())
            ->method('callMethodOnParent')
            ->with('remove', ['test_tag'])
            ->willReturn(false);

        $this->trait->remove('test_tag');
    }

    /**
     * @test
     */
    public function removeIfClearCacheEnabled()
    {
        $this->extensionConfiguration->expects(self::once())
            ->method('isClearCacheQueueEnabled')
            ->willReturn(true);
        $this->queueUtility->expects(self::once())
            ->method('addQueueEntryForRemoveMethod')
            ->with('test_cache', 'test_tag');
        $this->trait->expects(self::once())
            ->method('getQueueUtility')
            ->willReturn($this->queueUtility);
        $this->trait->expects(self::never())
            ->method('callMethodOnParent');

        $this->trait->remove('test_tag');
    }

    /**
     * @test
     */
    public function flushIfClearCacheDisabled()
    {
        $this->extensionConfiguration->expects(self::once())
            ->method('isClearCacheQueueEnabled')
            ->willReturn(false);
        $this->trait->expects(self::never())
            ->method('getQueueUtility');
        $this->trait->expects(self::once())
            ->method('callMethodOnParent')
            ->with('flush');

        $this->trait->flush();
    }

    /**
     * @test
     */
    public function flushIfClearCacheEnabled()
    {
        $this->extensionConfiguration->expects(self::once())
            ->method('isClearCacheQueueEnabled')
            ->willReturn(true);
        $this->queueUtility->expects(self::once())
            ->method('addQueueEntryForFlushMethod')
            ->with('test_cache');
        $this->trait->expects(self::once())
            ->method('getQueueUtility')
            ->willReturn($this->queueUtility);
        $this->trait->expects(self::never())
            ->method('callMethodOnParent');

        $this->trait->flush();
    }

    /**
     * @test
     */
    public function validateParent()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'This trait can be used only with classes that implement '.FrontendInterface::class
        );

        $trait = $this->getMockForTrait(
            ClearCacheThroughQueueTrait::class,
            [],
            '',
            true,
            true,
            true,
            ['callMethodOnParent', 'getExtensionConfiguration', 'getQueueUtility', 'getIdentifier']
        );

        $trait->flush();
    }
}
