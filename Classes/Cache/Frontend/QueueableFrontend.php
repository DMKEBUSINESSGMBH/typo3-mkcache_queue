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
use TYPO3\CMS\Core\Cache\Backend\BackendInterface;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
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
 * Class QueueableFrontend.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class QueueableFrontend implements FrontendInterface
{
    /**
     * @var FrontendInterface
     */
    protected $actualCache;

    /**
     * @var ExtensionConfiguration
     */
    protected $extensionConfiguration;

    /**
     * @var Queue
     */
    protected $queue;

    public function __construct(
        FrontendInterface $actualCache,
        ExtensionConfiguration $extensionConfiguration = null,
        Queue $queue = null
    ) {
        $this->actualCache = $actualCache;
        $this->extensionConfiguration = $extensionConfiguration
            ?? GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $this->queue = $queue ?? GeneralUtility::makeInstance(Queue::class);
    }

    /**
     * @see FrontendInterface::remove()
     */
    public function remove($entryIdentifier): bool
    {
        if ($this->isClearCacheQueueEnabled()) {
            $this->queue->addQueueEntryForRemoveMethod($this->getIdentifier(), $entryIdentifier);

            return true;
        }

        return $this->actualCache->remove($entryIdentifier);
    }

    /**
     * @see FrontendInterface::flush()
     */
    public function flush(): void
    {
        if ($this->isClearCacheQueueEnabled()) {
            $this->queue->addQueueEntryForFlushMethod($this->getIdentifier());

            return;
        }

        $this->actualCache->flush();
    }

    /**
     * @see FrontendInterface::flushByTags()
     */
    public function flushByTags(array $tags): void
    {
        if ($this->isClearCacheQueueEnabled()) {
            $this->queue->addQueueEntryForFlushByTagsMethod($this->getIdentifier(), $tags);

            return;
        }

        $this->actualCache->flushByTags($tags);
    }

    /**
     * @see FrontendInterface::flushByTag()
     */
    public function flushByTag($tag): void
    {
        if ($this->isClearCacheQueueEnabled()) {
            $this->queue->addQueueEntryForFlushByTagMethod($this->getIdentifier(), $tag);

            return;
        }

        $this->actualCache->flushByTag($tag);
    }

    protected function isClearCacheQueueEnabled(): bool
    {
        return $this->extensionConfiguration->isClearCacheQueueEnabled();
    }

    /**
     * @see FrontendInterface::getIdentifier()
     */
    public function getIdentifier(): string
    {
        return $this->actualCache->getIdentifier();
    }

    /**
     * @see FrontendInterface::getIdentifier()
     */
    public function getBackend(): BackendInterface
    {
        return $this->actualCache->getBackend();
    }

    /**
     * @see FrontendInterface::getIdentifier()
     */
    public function set($entryIdentifier, $data, array $tags = [], $lifetime = null)
    {
        $this->actualCache->set($entryIdentifier, $data, $tags, $lifetime);
    }

    /**
     * @see FrontendInterface::getIdentifier()
     */
    public function get($entryIdentifier)
    {
        return $this->actualCache->get($entryIdentifier);
    }

    /**
     * @see FrontendInterface::getIdentifier()
     */
    public function has($entryIdentifier): bool
    {
        return $this->actualCache->has($entryIdentifier);
    }

    /**
     * @see FrontendInterface::getIdentifier()
     */
    public function collectGarbage(): void
    {
        $this->actualCache->collectGarbage();
    }

    /**
     * @see FrontendInterface::getIdentifier()
     */
    public function isValidEntryIdentifier($identifier): bool
    {
        return $this->actualCache->isValidEntryIdentifier($identifier);
    }

    /**
     * @see FrontendInterface::getIdentifier()
     */
    public function isValidTag($tag): bool
    {
        return $this->actualCache->isValidTag($tag);
    }

    public function __call($name, $arguments)
    {
        return $this->actualCache->$name(...$arguments);
    }
}
