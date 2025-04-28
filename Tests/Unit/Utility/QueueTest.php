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
    public function testAddQueueEntryForRemoveMethod(): void
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

    public function testAddQueueEntryForFlushMethod(): void
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

    public function testAddQueueEntryForFlushByTagsMethod(): void
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

    public function testAddQueueEntryForFlushByTagMethod(): void
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

    public function testFindAllQueueEntries(): void
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

    public function testDeleteQueueEntry(): void
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
