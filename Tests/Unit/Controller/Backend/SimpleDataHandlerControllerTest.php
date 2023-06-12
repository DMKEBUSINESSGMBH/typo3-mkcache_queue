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

namespace DMK\MkcacheQueue\Tests\Controller\Backend;

use DMK\MkcacheQueue\Controller\Backend\SimpleDataHandlerController;
use DMK\MkcacheQueue\Utility\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class SimpleDataHandlerControllerTest.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SimpleDataHandlerControllerTest extends UnitTestCase
{
    /**
     * @test
     */
    public function processRequest()
    {
        $extensionConfiguration = $this->getMockBuilder(ExtensionConfiguration::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isDirectCacheClearDisabledCompletely'])
            ->getMock();
        GeneralUtility::addInstance(ExtensionConfiguration::class, $extensionConfiguration);

        $controller = $this->getAccessibleMock(
            SimpleDataHandlerController::class,
            ['callProcessRequestOnParent'],
            [],
            '',
            false
        );
        $clearCacheQueueEnabled = null;
        $controller->expects(self::once())
            ->method('callProcessRequestOnParent')
            ->willReturnCallback(function () use (&$clearCacheQueueEnabled, $extensionConfiguration) {
                $clearCacheQueueEnabled = $extensionConfiguration->isClearCacheQueueEnabled();
            });

        self::assertNull($clearCacheQueueEnabled);
        self::assertTrue($extensionConfiguration->isClearCacheQueueEnabled());
        $controller->_call('processRequest');
        self::assertFalse($clearCacheQueueEnabled);
        self::assertTrue($extensionConfiguration->isClearCacheQueueEnabled());
    }
}
