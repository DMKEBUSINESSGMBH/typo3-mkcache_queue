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

namespace DMK\MkcacheQueue\Tests\Command;

use DMK\MkcacheQueue\Command\ProcessQueueCommand;
use DMK\MkcacheQueue\Utility\ExtensionConfiguration;
use DMK\MkcacheQueue\Utility\Queue;
use Doctrine\DBAL\Result;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class ProcessQueueCommandTest.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class ProcessQueueCommandTest extends UnitTestCase
{
    public function testProcessRemoveCommands(): void
    {
        $cache = $this->getMockBuilder(NullFrontend::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher = self::exactly(4);
        $cache->expects($matcher)
            ->method('remove')
            ->with(
                $this->callback(function (string $identifier) use ($matcher): bool {
                    self::assertSame(
                        match ($matcher->numberOfInvocations()) {
                            1 => 'entry_1',
                            2 => 'entry_2',
                            3 => 'entry_3',
                            4 => 'entry_4',
                        },
                        $identifier
                    );

                    return true;
                }),
            );
        $cacheManager = $this->getMockBuilder(CacheManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher = self::exactly(2);
        $cacheManager->expects($matcher)
            ->method('getCache')
            ->with(
                $this->callback(function (string $identifier) use ($matcher): bool {
                    self::assertSame(
                        match ($matcher->numberOfInvocations()) {
                            1 => 'identifier_1',
                            2 => 'identifier_2',
                        },
                        $identifier
                    );

                    return true;
                }),
            )
            ->willReturnOnConsecutiveCalls($cache, $cache);

        $command = $this->getCommandMock(null, null, $cacheManager);

        $command->_call('processRemoveCommands');
    }

    public function testProcessFlushByTagsCommands(): void
    {
        $cache = $this->getMockBuilder(NullFrontend::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher = self::exactly(2);
        $cache->expects($matcher)
            ->method('flushByTags')
            ->with(
                $this->callback(function (array $tags) use ($matcher): bool {
                    self::assertSame(
                        match ($matcher->numberOfInvocations()) {
                            1 => ['tag_1', 'tag_2'],
                            2 => ['tag_3', 'tag_4'],
                        },
                        $tags
                    );

                    return true;
                }),
            );
        $cacheManager = $this->getMockBuilder(CacheManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher = self::exactly(2);
        $cacheManager->expects($matcher)
            ->method('getCache')
            ->with(
                $this->callback(function (string $identifier) use ($matcher): bool {
                    self::assertSame(
                        match ($matcher->numberOfInvocations()) {
                            1 => 'identifier_1',
                            2 => 'identifier_2',
                        },
                        $identifier
                    );

                    return true;
                }),
            )
            ->willReturnOnConsecutiveCalls($cache, $cache);

        $command = $this->getCommandMock(null, null, $cacheManager);

        $command->_call('processFlushByTagsCommands');
    }

    public function testProcessFlushCommands(): void
    {
        $cache = $this->getMockBuilder(NullFrontend::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cache->expects(self::exactly(2))
            ->method('flush');
        $cacheManager = $this->getMockBuilder(CacheManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher = self::exactly(2);
        $cacheManager->expects($matcher)
            ->method('getCache')
            ->with(
                $this->callback(function (string $identifier) use ($matcher): bool {
                    self::assertSame(
                        match ($matcher->numberOfInvocations()) {
                            1 => 'identifier_3',
                            2 => 'identifier_4',
                        },
                        $identifier
                    );

                    return true;
                }),
            )
            ->willReturnOnConsecutiveCalls($cache, $cache);

        $command = $this->getCommandMock(null, null, $cacheManager);

        $command->_call('processFlushCommands');
    }

    public function testCollectClearCacheCommand(): void
    {
        $queue = $this->getMockBuilder(Queue::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addQueueEntry'])
            ->getMock();
        $command = $this->getCommandMock(null, $queue);
        $command->_set('removeCommands', []);
        $command->_set('flushByTagsCommands', []);
        $command->_set('flushCommands', []);

        $queueEntries = [
            ['clear_cache_method' => 'remove', 'cache_identifier' => 'identifier_1', 'entry_identifier' => 'entry_1'],
            ['clear_cache_method' => 'remove', 'cache_identifier' => 'identifier_1', 'entry_identifier' => 'entry_2'],
            ['clear_cache_method' => 'remove', 'cache_identifier' => 'identifier_2', 'entry_identifier' => 'entry_1'],
            ['clear_cache_method' => 'flush', 'cache_identifier' => 'identifier_1'],
            ['clear_cache_method' => 'flush', 'cache_identifier' => 'identifier_2'],
            ['clear_cache_method' => 'flushByTags', 'cache_identifier' => 'identifier_1', 'tags' => '["tag_1","tag_2"]'],
            ['clear_cache_method' => 'flushByTags', 'cache_identifier' => 'identifier_1', 'tags' => '["tag_3","tag_4"]'],
            ['clear_cache_method' => 'flushByTags', 'cache_identifier' => 'identifier_2', 'tags' => '["tag_1","tag_2"]'],
            ['clear_cache_method' => 'flushByTag', 'cache_identifier' => 'identifier_1', 'tags' => '"tag_5"'],
            ['clear_cache_method' => 'flushByTag', 'cache_identifier' => 'identifier_1', 'tags' => '"tag_6"'],
            ['clear_cache_method' => 'flushByTag', 'cache_identifier' => 'identifier_2', 'tags' => '"tag_3"'],
        ];

        foreach ($queueEntries as $queueEntry) {
            $command->_call('collectClearCacheCommand', $queueEntry);
        }

        self::assertSame(
            [
                'identifier_1' => ['entry_1', 'entry_2'],
                'identifier_2' => ['entry_1'],
            ],
            $command->_get('removeCommands')
        );
        self::assertSame(
            ['identifier_1' => 'identifier_1', 'identifier_2' => 'identifier_2'],
            $command->_get('flushCommands')
        );
        self::assertSame(
            [
                'identifier_1' => ['tag_1', 'tag_2', 'tag_3', 'tag_4', 'tag_5', 'tag_6'],
                'identifier_2' => ['tag_1', 'tag_2', 'tag_3'],
            ],
            $command->_get('flushByTagsCommands')
        );
    }

    public function testExecute(): void
    {
        $extensionConfiguration = $this->getMockBuilder(ExtensionConfiguration::class)
            ->disableOriginalConstructor()
            ->getMock();
        $extensionConfiguration->expects(self::once())
            ->method('disableClearCacheQueue');

        $queue = $this->getMockBuilder(Queue::class)
            ->disableOriginalConstructor()
            ->getMock();
        $result = $this->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->getMock();
        $result->expects(self::once())
            ->method('fetchAllAssociative')
            ->willReturn([['entry_1'], ['entry_2']]);
        $queue->expects(self::once())
            ->method('findAllQueueEntries')
            ->willReturn($result);
        $matcher = self::exactly(2);
        $queue->expects($matcher)
            ->method('deleteQueueEntry')
            ->with(
                $this->callback(function (array $identifiers) use ($matcher): bool {
                    self::assertSame(
                        match ($matcher->numberOfInvocations()) {
                            1 => ['entry_1'],
                            2 => ['entry_2'],
                        },
                        $identifiers
                    );

                    return true;
                }),
            );

        $command = $this->getCommandMock(
            $extensionConfiguration,
            $queue,
            null,
            ['collectClearCacheCommand', 'processFlushCommands', 'processFlushByTagsCommands', 'processRemoveCommands']
        );

        $matcher = self::exactly(2);
        $command->expects($matcher)
            ->method('collectClearCacheCommand')
            ->with(
                $this->callback(function (array $identifiers) use ($matcher): bool {
                    self::assertSame(
                        match ($matcher->numberOfInvocations()) {
                            1 => ['entry_1'],
                            2 => ['entry_2'],
                        },
                        $identifiers
                    );

                    return true;
                }),
            );
        $command->expects(self::once())
            ->method('processFlushCommands');
        $command->expects(self::once())
            ->method('processFlushByTagsCommands');
        $command->expects(self::once())
            ->method('processRemoveCommands');

        $command->_call(
            'execute',
            $this->getMockBuilder(InputInterface::class)->getMock(),
            $this->getMockBuilder(OutputInterface::class)->getMock()
        );
    }

    protected function getCommandMock(
        ?ExtensionConfiguration $extensionConfiguration = null,
        ?Queue $queue = null,
        ?CacheManager $cacheManager = null,
        ?array $methods = null,
    ): ProcessQueueCommand {
        $extensionConfiguration ??= $this->getMockBuilder(ExtensionConfiguration::class)
                ->disableOriginalConstructor()
                ->getMock();
        $queue ??= $this->getMockBuilder(Queue::class)
                ->disableOriginalConstructor()
                ->getMock();
        $cacheManager ??= $this->getMockBuilder(CacheManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = $this->getAccessibleMock(
            ProcessQueueCommand::class,
            $methods,
            ['command_name', $extensionConfiguration, $queue, $cacheManager]
        );

        $command->_set(
            'removeCommands',
            [
                'identifier_1' => ['entry_1', 'entry_2'],
                'identifier_2' => ['entry_3', 'entry_4'],
                'identifier_3' => ['entry_5', 'entry_6'],
            ]
        );
        $command->_set(
            'flushByTagsCommands',
            [
                'identifier_1' => ['tag_1', 'tag_2', 'tag_1'],
                'identifier_2' => ['tag_3', 'tag_4', 'tag_3'],
                'identifier_3' => ['tag_5', 'tag_6'],
            ]
        );
        $command->_set('flushCommands', ['identifier_3' => 'identifier_3', 'identifier_4' => 'identifier_4']);

        return $command;
    }
}
