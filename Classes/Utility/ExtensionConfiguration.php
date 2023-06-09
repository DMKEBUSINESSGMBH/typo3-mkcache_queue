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

use Symfony\Component\Console\Input\ArgvInput;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * Class ExtensionConfiguration.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class ExtensionConfiguration implements SingletonInterface
{
    /**
     * @var bool
     */
    protected $clearCacheQueueEnabled = true;

    /**
     * @var \TYPO3\CMS\Core\Configuration\ExtensionConfiguration
     */
    protected $extensionConfiguration;

    public const EXTENSION_KEY = 'mkcache_queue';

    public function __construct(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration $extensionConfiguration)
    {
        $this->extensionConfiguration = $extensionConfiguration;
    }

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
