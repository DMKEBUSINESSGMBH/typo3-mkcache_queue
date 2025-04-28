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

use Symfony\Component\Console\Input\ArgvInput;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ExtensionConfiguration.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class ExtensionConfiguration
{
    /**
     * @var bool
     */
    protected $clearCacheQueueEnabled = true;

    public const EXTENSION_KEY = 'mkcache_queue';

    public function __construct(protected \TYPO3\CMS\Core\Configuration\ExtensionConfiguration $extensionConfiguration)
    {
    }

    /**
     * @return array<string>
     *
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getCachesToClearThroughQueue(): array
    {
        return GeneralUtility::trimExplode(
            ',',
            $this->extensionConfiguration->get(self::EXTENSION_KEY, 'cachesToClearThroughQueue')
        );
    }

    public function isDirectCacheClearDisabledCompletely(): bool
    {
        return (bool) $this->extensionConfiguration->get(self::EXTENSION_KEY, 'disableDirectCacheClearCompletely');
    }

    public function isClearCacheQueueEnabled(): bool
    {
        return $this->clearCacheQueueEnabled;
    }

    public function enableClearCacheQueue(): void
    {
        $this->clearCacheQueueEnabled = true;
    }

    public function disableClearCacheQueue(): void
    {
        if (!$this->isDirectCacheClearDisabledCompletely()) {
            $this->clearCacheQueueEnabled = false;
        }
    }

    /**
     * Make sure it's still possible to clear the cache directly through the cache:flush CLI command.
     * There seems to be no better way to influence the cache:flush command.
     */
    public function disableClearCacheQueueForCacheFlushCliCommand(): void
    {
        if (
            Environment::isCli()
            && ('cache:flush' === GeneralUtility::makeInstance(ArgvInput::class)->getFirstArgument())
        ) {
            $this->disableClearCacheQueue();
        }
    }
}
