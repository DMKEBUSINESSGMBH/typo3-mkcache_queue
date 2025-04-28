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

namespace DMK\MkcacheQueue\Tests\Controller\Backend;

use DMK\MkcacheQueue\Controller\Backend\ClearPageCacheController;
use DMK\MkcacheQueue\Utility\ExtensionConfiguration;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class ClearPageCacheControllerTest.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class ClearPageCacheControllerTest extends UnitTestCase
{
    public function testMainAction(): void
    {
        $extensionConfiguration = $this->getMockBuilder(ExtensionConfiguration::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isDirectCacheClearDisabledCompletely'])
            ->getMock();
        GeneralUtility::addInstance(ExtensionConfiguration::class, $extensionConfiguration);

        $controller = $this->getMockBuilder(ClearPageCacheController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['callMainActionOnParent'])
            ->getMock();
        $clearCacheQueueEnabled = null;
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $controller->expects(self::once())
            ->method('callMainActionOnParent')
            ->with($request)
            ->willReturnCallback(function () use (&$clearCacheQueueEnabled, $response, $extensionConfiguration): \PHPUnit\Framework\MockObject\MockObject {
                $clearCacheQueueEnabled = $extensionConfiguration->isClearCacheQueueEnabled();

                return $response;
            });

        self::assertNull($clearCacheQueueEnabled);
        self::assertTrue($extensionConfiguration->isClearCacheQueueEnabled());
        $controller->mainAction($request);
        self::assertFalse($clearCacheQueueEnabled);
        self::assertTrue($extensionConfiguration->isClearCacheQueueEnabled());
    }
}
