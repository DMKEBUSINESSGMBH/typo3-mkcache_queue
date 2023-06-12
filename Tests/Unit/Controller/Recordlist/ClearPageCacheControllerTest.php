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

namespace DMK\MkcacheQueue\Tests\Controller\Recordlist;

use DMK\MkcacheQueue\Controller\Recordlist\ClearPageCacheController;
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
    /**
     * @test
     */
    public function mainAction()
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
            ->willReturnCallback(function () use (&$clearCacheQueueEnabled, $response, $extensionConfiguration) {
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
