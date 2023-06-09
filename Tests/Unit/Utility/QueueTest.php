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

use DMK\MkcacheQueue\Utility\Queue;
use Doctrine\DBAL\Result;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class QueueTest.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class QueueTest extends UnitTestCase
{
    /**
     * @test
     */
    public function addQueueEntryForRemoveMethod()
    {
        $databaseConnection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $databaseConnection->expects(self::once())
            ->method('insert')
            ->with(
                'tx_mkcache_queue',
                [
                    'clear_cache_method' => 'remove',
                    'cache_identifier' => 'cacheIdentifier',
                    'entry_identifier' => 'entryIdentifier',
                    'hash' => '254dba77cc165307370d13eb50224ddb',
                ]
            );

        $this->getQueueMock($databaseConnection)->addQueueEntryForRemoveMethod('cacheIdentifier', 'entryIdentifier');
    }

    /**
     * @test
     */
    public function addQueueEntryForFlushMethod()
    {
        $databaseConnection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $databaseConnection->expects(self::once())
            ->method('insert')
            ->with(
                'tx_mkcache_queue',
                [
                    'clear_cache_method' => 'flush',
                    'cache_identifier' => 'cacheIdentifier',
                    'hash' => 'd2d640fe4523d5450513233842139967',
                ]
            );

        $this->getQueueMock($databaseConnection)->addQueueEntryForFlushMethod('cacheIdentifier');
    }

    /**
     * @test
     */
    public function addQueueEntryForFlushByTagsMethod()
    {
        $databaseConnection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $databaseConnection->expects(self::once())
            ->method('insert')
            ->with(
                'tx_mkcache_queue',
                [
                    'clear_cache_method' => 'flushByTags',
                    'cache_identifier' => 'cacheIdentifier',
                    'tags' => '["tag_1","tag_2"]',
                    'hash' => '37ca509088cc4182f5ef869ccb4cbf4a',
                ]
            );

        $this->getQueueMock($databaseConnection)->addQueueEntryForFlushByTagsMethod(
            'cacheIdentifier',
            ['tag_1', 'tag_2']
        );
    }

    /**
     * @test
     */
    public function addQueueEntryForFlushByTagMethod()
    {
        $databaseConnection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $databaseConnection->expects(self::once())
            ->method('insert')
            ->with(
                'tx_mkcache_queue',
                [
                    'clear_cache_method' => 'flushByTag',
                    'cache_identifier' => 'cacheIdentifier',
                    'tags' => '"tag_1"',
                    'hash' => '37ee7fe3593bd30f78202231c65b2445',
                ]
            );

        $this->getQueueMock($databaseConnection)->addQueueEntryForFlushByTagMethod('cacheIdentifier', 'tag_1');
    }

    /**
     * @test
     */
    public function findAllQueueEntries()
    {
        $databaseConnection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $result = $this->getMockBuilder(Result::class)->disableOriginalConstructor()->getMock();
        $databaseConnection->expects(self::once())
            ->method('select')
            ->with(['*'], 'tx_mkcache_queue')
            ->willReturn($result);

        self::assertSame($result, $this->getQueueMock($databaseConnection)->findAllQueueEntries());
    }

    /**
     * @test
     */
    public function deleteQueueEntry()
    {
        $queueEntry = [
            'clear_cache_method' => 'flushByTag',
            'cache_identifier' => 'cacheIdentifier',
            'tags' => '"tag_1"',
            'hash' => '37ee7fe3593bd30f78202231c65b2445',
        ];
        $databaseConnection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $databaseConnection->expects(self::once())
            ->method('delete')
            ->with('tx_mkcache_queue', $queueEntry);

        $this->getQueueMock($databaseConnection)->deleteQueueEntry($queueEntry);
    }

    protected function getQueueMock(Connection $databaseConnection): Queue
    {
        $queue = $this->getMockBuilder(Queue::class)
            ->onlyMethods(['getDatabaseConnection'])
            ->disableOriginalConstructor()
            ->getMock();
        $queue->expects(self::once())
            ->method('getDatabaseConnection')
            ->willReturn($databaseConnection);

        return $queue;
    }
}
