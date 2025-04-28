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

/**
 * Class Registry.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class Registry
{
    /**
     * @var array<string, bool>
     */
    protected $registeredCaches = [];

    public function __construct(protected ExtensionConfiguration $extensionConfiguration)
    {
    }

    public function registerCacheToClearThroughQueue(string $cacheIdentifier): void
    {
        $this->registeredCaches[$cacheIdentifier] = true;
    }

    /**
     * @param array<string> $cacheIdentifiers
     */
    public function registerCachesToClearThroughQueue(array $cacheIdentifiers): void
    {
        foreach ($cacheIdentifiers as $cacheIdentifier) {
            $this->registerCacheToClearThroughQueue($cacheIdentifier);
        }
    }

    public function registerCachesToClearThroughQueueByConfiguration(): void
    {
        $this->registerCachesToClearThroughQueue($this->extensionConfiguration->getCachesToClearThroughQueue());
    }

    public function isCacheRegisteredToClearThroughQueue(string $cacheIdentifier): bool
    {
        return isset($this->registeredCaches[$cacheIdentifier]);
    }
}
