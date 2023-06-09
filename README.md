MK Cache Queue
==============

![TYPO3 compatibility](https://img.shields.io/badge/TYPO3-11.5%20%7C%2012.4-orange?maxAge=3600&style=flat-square&logo=typo3)
[![Latest Stable Version](https://img.shields.io/packagist/v/dmk/mkcache_queue.svg?maxAge=3600&style=flat-square&logo=composer)](https://packagist.org/packages/dmk/mkcache_queue)
[![Total Downloads](https://img.shields.io/packagist/dt/dmk/mkcache_queue.svg?maxAge=3600&style=flat-square)](https://packagist.org/packages/dmk/mkcache_queue)
[![Build Status](https://img.shields.io/github/actions/workflow/status/DMKEBUSINESSGMBH/typo3-mkcache_queue/php.yml?branch=12.4&maxAge=3600&style=flat-square&logo=github-actions)](https://github.com/DMKEBUSINESSGMBH/typo3-mkcache_queue/actions?query=workflow%3A%22PHP+Checks%22)
[![License](https://img.shields.io/packagist/l/dmk/mkcache_queue.svg?maxAge=3600&style=flat-square&logo=gnu)](https://packagist.org/packages/dmk/mkcache_queue)

"***MK Cache Queue***" provides a queue for clearing the caches of TYPO3. So instead of clearing the caches
directly, this is done asynchronously via a scheduler job (***command cache:process-queue***). 
This will reduce the frequency
of cache clearing when editors do a lot of work. How often the caches will be cleared depends
only on the frequency of the scheduler job. 

By default it's still
possible to clear the cache directly. This is possible through the CLI command ***cache:flush***,
the clear all caches button in the BE (top bar) and the different buttons to clear the caches of a page
(Page view, context menu in page tree). So basically all cache clears which are triggered directly
by a user will still clear the cache. Actions like saving a content element will clear the caches
in the background and those clears are put into a queue. You can turn off
this behaivour and forbid direct cache clearing completely through the extension configuration
***disableDirectCacheClearCompletely***. 

Furthermore you can configure
the supported caches via the extension configuration ***cachesToClearThroughQueue***. Or you can use
the API function ***DMK\MkcacheQueue\Utility\Registry::registerCacheToClearThroughQueue()*** to add
caches to clear through queue in your own extensions or the AdditionalConfiguration.php

## Installation
- Require/install extension <pre>composer require dmk/mkcache_queue:^12.0</pre>
- create a scheduler task for executing a console command and select the ***cache:process-queue*** command.
- make sure the cronjob for executing the scheduler tasks of TYPO3 is in place
- add your caches to clear through queue in the extension configuration or via the API function
  - default caches to clear through queue: core, extbase, hash, imagesizes, l10n, dashboard_rss, fluid_template, assets, pages, pagesection, rootline
- decide if you want to turn off direct cache clearing completely via extension configuration ***disableDirectCacheClearCompletely*** (default: false)
  
