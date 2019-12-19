.. include:: ../Includes.txt


.. _usage:


Usage
=====

fluid_page_cache comes with zero configuration and works out of the box, when it is installed.



Info Module
-----------

fluid_page_cache ships an Info Module which allows you to see the records used in view, for each page variation in
page cache.

.. image:: ../Welcome/Images/info_module.png
   :scale: 50%
   :alt: Page cache tags in TYPO3


.. caution::
   The info module only works, when the page cache is stored in database.

When you use e.g. Redis, the info module will not work.

The main functionality of fluid_page_cache is not affected. It supports all cache backends, the Caching Framework is
compatible with.


Register cache tags manually
----------------------------

fluid_page_cache is just able to create cache tags for used entities in view,
when they get passed **into** the template using variables.

View helpers like ``f:cObject`` provide own data, which the variable container in Fluid does not know about.


View helper
~~~~~~~~~~~

To be still able to provide cache tags here, fluid_page_cache ships a view helper, you can use in your templates.

**Example Fluid template:**

.. code-block:: html

    {namespace fpc=T3\FluidPageCache\ViewHelpers}

    <f:cObject typoscriptObjectPath="lib.contentElementRendering">{element.uid}</f:cObject>
    <fpc:registerCacheTag table="tt_content" uid="{element.uid}" />


PageCacheManager class
~~~~~~~~~~~~~~~~~~~~~~

The central class ``\T3\FluidPageCache\PageCacheManager`` is entry point for all parts of this extension.

You can call the static method **registerCacheTag** from everywhere you want.

.. code-block:: php

    PageCacheManager::registerCacheTag($table, $uid);


Keep in mind, that fluid_page_cache only apply:

- when ``TYPO3_MODE`` constant is ``FE``
- ``$GLOBALS['TSFE']`` is set


If you want to register an Extbase entity manually, you can use the static **registerEntity** method:

.. code-block:: php

    $entity = $this->myRepository->findByUid(1);
    PageCacheManager::registerEntity($entity);


``$entity`` must be:

- an instance of ``\TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject``
- persisted (and have an uid available)


All cache tags made with fluid_page_cache got this syntax:
``fpc_{table_name}_uid``


DataHandler Hook
----------------

Creating cache tags does only make sense when you use them to clear specific cache entries.

fluid_page_cache provides an after-save-hook, which is triggered each time you modify a record using the DataHandler
(e.g. editors in backend).

It applies automatically, when fluid_page_cache is installed.
