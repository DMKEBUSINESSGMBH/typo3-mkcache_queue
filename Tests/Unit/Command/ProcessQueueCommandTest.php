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

namespace DMK\MkcacheQueue\Tests\Command;

use DMK\MkcacheQueue\Command\ProcessQueueCommand;
use DMK\MkcacheQueue\Utility\ExtensionConfiguration;
use DMK\MkcacheQueue\Utility\Queue;
use Doctrine\DBAL\ForwardCompatibility\Result;
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
    /**
     * @test
     */
    public function processRemoveCommands()
    {
        $cache = $this->getMockBuilder(NullFrontend::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cache->expects(self::exactly(4))
            ->method('remove')
            ->withConsecutive(
                ['entry_1'],
                ['entry_2'],
                ['entry_3'],
                ['entry_4'],
            );
        $cacheManager = $this->getMockBuilder(CacheManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheManager->expects(self::exactly(2))
            ->method('getCache')
            ->withConsecutive(
                ['identifier_1'],
                ['identifier_2'],
            )
            ->willReturnOnConsecutiveCalls($cache, $cache);

        $command = $this->getCommandMock(null, null, $cacheManager);

        $command->_call('processRemoveCommands');
    }

    /**
     * @test
     */
    public function processFlushByTagsCommands()
    {
        $cache = $this->getMockBuilder(NullFrontend::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cache->expects(self::exactly(2))
            ->method('flushByTags')
            ->withConsecutive(
                [['tag_1', 'tag_2']],
                [['tag_3', 'tag_4']],
            );
        $cacheManager = $this->getMockBuilder(CacheManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheManager->expects(self::exactly(2))
            ->method('getCache')
            ->withConsecutive(
                ['identifier_1'],
                ['identifier_2'],
            )
            ->willReturnOnConsecutiveCalls($cache, $cache);

        $command = $this->getCommandMock(null, null, $cacheManager);

        $command->_call('processFlushByTagsCommands');
    }

    /**
     * @test
     */
    public function processFlushCommands()
    {
        $cache = $this->getMockBuilder(NullFrontend::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cache->expects(self::exactly(2))
            ->method('flush');
        $cacheManager = $this->getMockBuilder(CacheManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheManager->expects(self::exactly(2))
            ->method('getCache')
            ->withConsecutive(
                ['identifier_3'],
                ['identifier_4'],
            )
            ->willReturnOnConsecutiveCalls($cache, $cache);

        $command = $this->getCommandMock(null, null, $cacheManager);

        $command->_call('processFlushCommands');
    }

    /**
     * @test
     */
    public function collectClearCacheCommand()
    {
        $command = $this->getCommandMock();
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
        self::assertSame(['identifier_1', 'identifier_2'], $command->_get('flushCommands'));
        self::assertSame(
            [
                'identifier_1' => ['tag_1', 'tag_2', 'tag_3', 'tag_4', 'tag_5', 'tag_6'],
                'identifier_2' => ['tag_1', 'tag_2', 'tag_3'],
            ],
            $command->_get('flushByTagsCommands')
        );
    }

    /**
     * @test
     */
    public function execute()
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
            ->method('getIterator')
            ->willReturn(new \ArrayObject([['entry_1'], ['entry_2']]));
        $queue->expects(self::once())
            ->method('findAllQueueEntries')
            ->willReturn($result);
        $queue->expects(self::exactly(2))
            ->method('deleteQueueEntry')
            ->withConsecutive(
                [['entry_1']],
                [['entry_2']],
            );

        $command = $this->getCommandMock(
            $extensionConfiguration,
            $queue,
            null,
            ['collectClearCacheCommand', 'processFlushCommands', 'processFlushByTagsCommands', 'processRemoveCommands']
        );

        $command->expects(self::exactly(2))
            ->method('collectClearCacheCommand')
            ->withConsecutive(
                [['entry_1']],
                [['entry_2']],
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
        ExtensionConfiguration $extensionConfiguration = null,
        Queue $queue = null,
        CacheManager $cacheManager = null,
        array $methods = null
    ): ProcessQueueCommand {
        $extensionConfiguration = $extensionConfiguration ?? $this->getMockBuilder(ExtensionConfiguration::class)
                ->disableOriginalConstructor()
                ->getMock();
        $queue = $queue ?? $this->getMockBuilder(Queue::class)
                ->disableOriginalConstructor()
                ->getMock();
        $cacheManager = $cacheManager ?? $this->getMockBuilder(CacheManager::class)
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
        $command->_set('flushCommands', ['identifier_3' => true, 'identifier_4' => true]);

        return $command;
    }
}
