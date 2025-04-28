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

namespace DMK\MkcacheQueue\Controller\Backend;

use DMK\MkcacheQueue\Utility\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SimpleDataHandlerController.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SimpleDataHandlerController extends \TYPO3\CMS\Backend\Controller\SimpleDataHandlerController
{
    /**
     * Make sure it's still possible to clear the cache directly through manual cache clear in the BE.
     */
    protected function processRequest(): void
    {
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $extensionConfiguration->disableClearCacheQueue();
        $this->callProcessRequestOnParent();
        $extensionConfiguration->enableClearCacheQueue();
    }

    protected function callProcessRequestOnParent(): void
    {
        parent::processRequest();
    }
}
