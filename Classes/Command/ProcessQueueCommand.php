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

namespace DMK\MkcacheQueue\Command;

use DMK\MkcacheQueue\Utility\ExtensionConfiguration;
use DMK\MkcacheQueue\Utility\Queue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ProcessQueueCommand.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class ProcessQueueCommand extends Command
{
    protected ExtensionConfiguration $extensionConfiguration;

    protected Queue $queueUtility;

    protected CacheManager $cacheManager;

    /**
     * @var array <string, array<string>>
     */
    protected array $flushByTagsCommands = [];

    /**
     * @var array <string, string>
     */
    protected array $flushCommands = [];

    /**
     * @var array <string, array<string>>
     */
    protected array $removeCommands = [];

    public function __construct(
        ?string $name = null,
        ?ExtensionConfiguration $extensionConfiguration = null,
        ?Queue $queueUtility = null,
        ?CacheManager $cacheManager = null,
    ) {
        parent::__construct($name);
        $this->extensionConfiguration = $extensionConfiguration
            ?? GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $this->queueUtility = $queueUtility ?? GeneralUtility::makeInstance(Queue::class);
        $this->cacheManager = $cacheManager ?? GeneralUtility::makeInstance(CacheManager::class);
    }

    protected function configure()
    {
        $this->setDescription('Process the clear cache queue');
    }

    /**
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->extensionConfiguration->disableClearCacheQueue();
        foreach ($this->queueUtility->findAllQueueEntries()->fetchAllAssociative() as $queueEntry) {
            $this->collectClearCacheCommand($queueEntry);
            $this->queueUtility->deleteQueueEntry($queueEntry);
        }

        $this->processFlushCommands();
        $this->processFlushByTagsCommands();
        $this->processRemoveCommands();

        return 0;
    }

    /**
     * @param array<string, string> $queueEntry
     */
    protected function collectClearCacheCommand(array $queueEntry): void
    {
        switch ($queueEntry['clear_cache_method']) {
            case 'flush':
                $this->flushCommands[$queueEntry['cache_identifier']] = $queueEntry['cache_identifier'];
                break;
            case 'remove':
                $this->removeCommands[$queueEntry['cache_identifier']][] = $queueEntry['entry_identifier'];
                break;
            case 'flushByTags':
                $decodedTags = $this->queueUtility->decodeTags($queueEntry['tags']);
                if (is_array($decodedTags)) {
                    $this->flushByTagsCommands[$queueEntry['cache_identifier']] = array_merge(
                        $this->flushByTagsCommands[$queueEntry['cache_identifier']] ?? [],
                        $decodedTags
                    );
                }

                break;
            case 'flushByTag':
                $decodedTags = $this->queueUtility->decodeTags($queueEntry['tags']);
                if (is_string($decodedTags)) {
                    $this->flushByTagsCommands[$queueEntry['cache_identifier']][] = $decodedTags;
                }

                break;
        }
    }

    protected function processFlushCommands(): void
    {
        foreach ($this->flushCommands as $cacheIdentifier) {
            $this->cacheManager->getCache($cacheIdentifier)->flush();
        }
    }

    protected function processFlushByTagsCommands(): void
    {
        foreach ($this->flushByTagsCommands as $cacheIdentifier => $tags) {
            if (!$this->isCacheAlreadyCleared($cacheIdentifier)) {
                $this->cacheManager->getCache($cacheIdentifier)->flushByTags(array_unique($tags));
            }
        }
    }

    protected function processRemoveCommands(): void
    {
        foreach ($this->removeCommands as $cacheIdentifier => $entryIdentifiers) {
            if (!$this->isCacheAlreadyCleared($cacheIdentifier)) {
                $cache = $this->cacheManager->getCache($cacheIdentifier);
                foreach ($entryIdentifiers as $entryIdentifier) {
                    $cache->remove($entryIdentifier);
                }
            }
        }
    }

    protected function isCacheAlreadyCleared(string $cacheIdentifier): bool
    {
        return isset($this->flushCommands[$cacheIdentifier]);
    }
}
