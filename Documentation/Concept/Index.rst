.. include:: ../Includes.txt


.. _concept:


Concept
=======

Before I talk about the concept of **fluid_page_cache** I would like to explain the functionality of the page cache
in TYPO3 CMS.


Page Cache in TYPO3
-------------------

TYPO3 deals with many caches and comes with an own Caching Framework. The **page cache** is one of the most important
and last caches during TYPO3's rendering processes. It caches the rendered HTML content of pages.

Extensions like `staticfilecache`_  take this cached content and put it to static HTML files,
which increases the performance, when accessing the website in frontend by browser, significantly.

.. _staticfilecache: https://extensions.typo3.org/extension/staticfilecache/

This only works, if the page **does not** contain USER_INT objects, which happens e.g. when your plugin has
non-cachable actions configured, in ``ext_localconf.php``.


The cHash
~~~~~~~~~

You've probably already dealt with the cHash. It is used for two things:

- prevent manipulating GET parameters (utilized by typoLink() method in ``\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer``)
- identifying cache for current requested page

Here you can find the full truth about how cHash is calculated: `CacheHashCalculator`_

.. _CacheHashCalculator: https://github.com/TYPO3/TYPO3.CMS/blob/master/typo3/sysext/frontend/Classes/Page/CacheHashCalculator.php

This cHash is used as identifier. Each **page variation** get's its own identifier.


Example with news extension
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Because of how Georg used the page cache in his news extension, I've had got the idea of fluid_page_cache.

The following screenshots shows contents from database table **cf_cache_pages_tags**.

.. image:: Images/cache_pages_tags.png
   :alt: Your first DCE

In this example I've created two news entries and called them separately in frontend (so the page cache is created).

We see two different identifiers, but several tags assigned to them. Basically each variant has three tags:

- pageId_0
- tx_news
- tx_news_uid_0

While pageId is provides by the TYPO3 core, the tx_news tags come from news itself. They are set in detailAction of the
NewsController.

With these page cache tags set, you can easily clear single page variants,
without affecting all other cached news' detail pages.


The problem
-----------

Hopefully you got an idea of how the page cache in TYPO3 works, and how the news extension utilize it.

What news does is great, but it has some pitfalls:

- You need to implement it on your own, for your extensions
- News only set cache tags for news items itself (uid & pid), used children (e.g. sys_file_reference) are not creating
  an individual tag
- Which may lead to cache issues for editors, when they edit a relation or a sys_file out of the scope of current news
  entry (e.g. in Filelist)

The question is: How to identify cache-sensitive objects on current page variation?


The idea
--------

tbw;
