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

namespace DMK\MkcacheQueue\Tests\Cache\Frontend;

use DMK\MkcacheQueue\Cache\Frontend\ClearCacheThroughQueueTrait;
use DMK\MkcacheQueue\Cache\Frontend\VariableFrontend;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class VariableFrontendTest.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class VariableFrontendTest extends UnitTestCase
{
    /**
     * @test
     */
    public function clearCacheTroughQueueTraitIsUsed()
    {
        self::assertArrayHasKey(ClearCacheThroughQueueTrait::class, class_uses(VariableFrontend::class));
    }
}
