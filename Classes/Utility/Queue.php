<?php

declare(strict_types=1);

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

namespace DMK\MkcacheQueue\Utility;

use Doctrine\DBAL\Result;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 *  Copyright notice.
 *
 *  (c) DMK E-BUSINESS GmbH <dev@dmk-ebusiness.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * Class Queue.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class Queue
{
    /**
     * @var ConnectionPool
     */
    protected $connectionPool;

    /**
     * @var string
     */
    public const QUEUE_TABLE = 'tx_mkcache_queue';

    public function __construct(ConnectionPool $connectionPool)
    {
        $this->connectionPool = $connectionPool;
    }

    public function addQueueEntryForRemoveMethod(string $cacheIdentifier, string $entryIdentifier): void
    {
        $this->addQueueEntry(
            [
                'cache_identifier' => $cacheIdentifier,
                'clear_cache_method' => 'remove',
                'entry_identifier' => $entryIdentifier,
            ]
        );
    }

    public function addQueueEntryForFlushMethod(string $cacheIdentifier): void
    {
        $this->addQueueEntry(
            [
                'cache_identifier' => $cacheIdentifier,
                'clear_cache_method' => 'flush',
            ]
        );
    }

    public function addQueueEntryForFlushByTagsMethod(string $cacheIdentifier, array $tags): void
    {
        $this->addQueueEntry(
            [
                'cache_identifier' => $cacheIdentifier,
                'clear_cache_method' => 'flushByTags',
                'tags' => json_encode($tags),
            ]
        );
    }

    public function addQueueEntryForFlushByTagMethod(string $cacheIdentifier, $tag): void
    {
        $this->addQueueEntry(
            [
                'cache_identifier' => $cacheIdentifier,
                'clear_cache_method' => 'flushByTag',
                'tags' => json_encode($tag),
            ]
        );
    }

    public function addQueueEntry(array $queueEntry): void
    {
        $queueEntry['hash'] = md5(serialize($queueEntry));
        $this->getDatabaseConnection()->insert(self::QUEUE_TABLE, $queueEntry);
    }

    public function findAllQueueEntries(): Result
    {
        return $this->getDatabaseConnection()->select(['*'], self::QUEUE_TABLE);
    }

    public function deleteQueueEntry(array $queueEntry): void
    {
        $this->getDatabaseConnection()->delete(self::QUEUE_TABLE, $queueEntry);
    }

    protected function getDatabaseConnection(): Connection
    {
        return $this->connectionPool->getConnectionForTable(self::QUEUE_TABLE);
    }
}
