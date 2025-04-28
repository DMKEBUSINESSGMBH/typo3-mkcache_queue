<?php

declare(strict_types=1);

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

namespace DMK\MkcacheQueue\Utility;

use Doctrine\DBAL\Result;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

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
     * @var string
     */
    public const QUEUE_TABLE = 'tx_mkcache_queue';

    public function __construct(protected ConnectionPool $connectionPool)
    {
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

    /**
     * @param array<string> $tags
     */
    public function addQueueEntryForFlushByTagsMethod(string $cacheIdentifier, array $tags): void
    {
        $this->addQueueEntry(
            [
                'cache_identifier' => $cacheIdentifier,
                'clear_cache_method' => 'flushByTags',
                'tags' => $this->encodeTags($tags),
            ]
        );
    }

    public function addQueueEntryForFlushByTagMethod(string $cacheIdentifier, string $tag): void
    {
        $this->addQueueEntry(
            [
                'cache_identifier' => $cacheIdentifier,
                'clear_cache_method' => 'flushByTag',
                'tags' => $this->encodeTags($tag),
            ]
        );
    }

    /**
     * @param array<string, string|false> $queueEntry
     */
    public function addQueueEntry(array $queueEntry): void
    {
        $queueEntry['hash'] = md5(serialize($queueEntry));
        $this->getDatabaseConnection()->insert(self::QUEUE_TABLE, $queueEntry);
    }

    /**
     * @param string|array<string> $tags
     */
    public function encodeTags(string|array $tags): false|string
    {
        return json_encode($tags);
    }

    /**
     * @return string|array<string>
     */
    public function decodeTags(string $tags): string|array
    {
        return json_decode($tags, true);
    }

    public function findAllQueueEntries(): Result
    {
        return $this->getDatabaseConnection()->select(['*'], self::QUEUE_TABLE);
    }

    /**
     * @param array<string, string> $queueEntry
     */
    public function deleteQueueEntry(array $queueEntry): void
    {
        $this->getDatabaseConnection()->delete(self::QUEUE_TABLE, $queueEntry);
    }

    protected function getDatabaseConnection(): Connection
    {
        return $this->connectionPool->getConnectionForTable(self::QUEUE_TABLE);
    }
}
