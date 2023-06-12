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

namespace DMK\MkcacheQueue\Cache;

use DMK\MkcacheQueue\Cache\Frontend\PhpFrontend;
use DMK\MkcacheQueue\Cache\Frontend\VariableFrontend;
use DMK\MkcacheQueue\Utility\Registry;
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
 * Class CacheManager.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class CacheManager extends \TYPO3\CMS\Core\Cache\CacheManager
{
    protected $cacheFrontendClassMap = [
        \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class => VariableFrontend::class,
        \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class => PhpFrontend::class,
    ];

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function __construct(bool $disableCaching = false)
    {
        parent::__construct($disableCaching);

        $this->defaultCacheConfiguration['frontend'] = $this->mapCacheFrontend(
            $this->defaultCacheConfiguration['frontend']
        );
    }

    public function setCacheConfigurations(array $cacheConfigurations): void
    {
        parent::setCacheConfigurations($cacheConfigurations);
        $this->replaceDefaultCacheFrontendClasses();
    }

    protected function replaceDefaultCacheFrontendClasses(): void
    {
        $registry = GeneralUtility::makeInstance(Registry::class);
        foreach ($this->cacheConfigurations as $cacheIdentifier => &$cacheConfiguration) {
            if (
                $registry->isCacheRegisteredToClearThroughQueue($cacheIdentifier)
                && ($cacheConfiguration['frontend'] ?? false)
            ) {
                $cacheConfiguration['frontend'] = $this->mapCacheFrontend($cacheConfiguration['frontend']);
            }
        }
    }

    protected function mapCacheFrontend(string $className): string
    {
        return $this->cacheFrontendClassMap[$className] ?? $className;
    }
}
