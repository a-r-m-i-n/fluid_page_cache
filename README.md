# Fluid Page Cache *for TYPO3 CMS*

This TYPO3 CMS extension allows you to clear frontend page caches, **automatically** when a displayed
record has been updated. It recognizes which variables have been used in the Fluid templates on current 
page and assigns additional cache_tags to page cache.

This allow the shipped after save-hook, to only delete pages from cache, which actually used the
edited record.

**Once EXT:fluid_page_cache is installed, your page cache is as precise as possible, with zero configuration.** 

## Documentation

This extension provides a ReST documentation, located in [Documentation/](./Documentation/Index.rst) directory.

You can see a rendered version on [docs.typo3.org/p/t3/fluid_page_cache](https://docs.typo3.org/p/t3/fluid_page_cache/master/en-us/).
