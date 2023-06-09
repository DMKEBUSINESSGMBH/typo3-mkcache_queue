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

namespace DMK\MkcacheQueue\Controller\Recordlist;

use DMK\MkcacheQueue\Utility\ExtensionConfiguration;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
 * Class ClearPageCacheController.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 *
 * @todo When support for TYPO3 11.5 is dropped and 13.x is added change
 * \TYPO3\CMS\Recordlist\Controller\ClearPageCacheController to \TYPO3\CMS\Backend\Controller\ClearPageCacheController
 */
class ClearPageCacheController extends \TYPO3\CMS\Recordlist\Controller\ClearPageCacheController
{
    /**
     * Make sure it's still possible to clear the cache directly through manual cache clear in the BE.
     */
    public function mainAction(ServerRequestInterface $request): ResponseInterface
    {
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $extensionConfiguration->disableClearCacheQueue();
        $response = $this->callMainActionOnParent($request);
        $extensionConfiguration->enableClearCacheQueue();

        return $response;
    }

    protected function callMainActionOnParent(ServerRequestInterface $request): ResponseInterface
    {
        return parent::mainAction($request);
    }
}
