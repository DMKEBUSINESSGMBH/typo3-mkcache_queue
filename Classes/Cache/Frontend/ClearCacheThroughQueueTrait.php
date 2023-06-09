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

namespace DMK\MkcacheQueue\Cache\Frontend;

use DMK\MkcacheQueue\Utility\ExtensionConfiguration;
use DMK\MkcacheQueue\Utility\Queue;
use TYPO3\CMS\Core\Cache\Frontend\AbstractFrontend;
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
 * Class ClearCacheThroughQueueTrait.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
trait ClearCacheThroughQueueTrait
{
    /**
     * @see AbstractFrontend::remove()
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function remove($entryIdentifier)
    {
        if ($this->getExtensionConfiguration()->isClearCacheQueueEnabled()) {
            $response = true;
            $this->getQueueUtility()->addQueueEntryForRemoveMethod($this->getIdentifier(), $entryIdentifier);
        } else {
            $response = $this->callMethodOnParent(__FUNCTION__, [$entryIdentifier]);
        }

        return $response;
    }

    /**
     * @see AbstractFrontend::flush()
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function flush()
    {
        if ($this->getExtensionConfiguration()->isClearCacheQueueEnabled()) {
            $this->getQueueUtility()->addQueueEntryForFlushMethod($this->getIdentifier());
        } else {
            $this->callMethodOnParent(__FUNCTION__);
        }
    }

    /**
     * @see AbstractFrontend::flushByTags()
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function flushByTags(array $tags)
    {
        if ($this->getExtensionConfiguration()->isClearCacheQueueEnabled()) {
            $this->getQueueUtility()->addQueueEntryForFlushByTagsMethod($this->getIdentifier(), $tags);
        } else {
            $this->callMethodOnParent(__FUNCTION__, [$tags]);
        }
    }

    /**
     * @see AbstractFrontend::flushByTag()
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function flushByTag($tag)
    {
        if ($this->getExtensionConfiguration()->isClearCacheQueueEnabled()) {
            $this->getQueueUtility()->addQueueEntryForFlushByTagMethod($this->getIdentifier(), $tag);
        } else {
            $this->callMethodOnParent(__FUNCTION__, [$tag]);
        }
    }

    protected function callMethodOnParent(string $methodName, array $arguments = [])
    {
        return call_user_func_array(['parent', $methodName], $arguments);
    }

    protected function getExtensionConfiguration(): ExtensionConfiguration
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class);
    }

    protected function getQueueUtility(): Queue
    {
        return GeneralUtility::makeInstance(Queue::class);
    }
}
