.. include:: ../Includes.txt


.. _welcome:


Welcome to Fluid Page Cache
===========================

The Fluid Page Cache extension for TYPO3 CMS, allows you to create **high precise page cache tags**,
with **zero configuration**.

Once installed, all entities you use in your Fluid templates will **automatically** create a new cache tag
for current page cache variation.

Fluid Page Cache also provides an Info module, which visualizes the page cache.

**The following screenshot, displays a single detail page of EXT:news:**

.. image:: Images/info_module.png
   :scale: 50%
   :alt: Page cache tags in TYPO3

The yellow marked entries are provides by news. All others are generated automatically,
based on the actual used entities, by fluid_page_cache.

Because **all actual used entities** have a **corresponding cache tag** now, you can precisely clear only
those cache entries, without affecting other pages.

.. hint::
   With **fluid_page_cache** installed all your cache problems are a thing of the past.
