#Search

Starting from Kajona 4.5, the new, index-based search is fully integrated in Kajona. This means that both, the portal- and the backend search make use of the new engine.

In order to have your own objects being added to the index, too, there are some aspects to be considered.

##General
In general, all properties marked with the annotations
`@addSearchIndex` 
are added to the index. As soon as an object is inserted, updated or deleted, the system analyzes the object for properties marked with `@addSearchIndex`. if found, the value of those properties are read, analyzed and added to the index. 
Therefore it is essential to have valid getters and setters for those properties!

##Backend search
All objects in the index are available to the backend search by default. If you don't want any special behavior, you won't need to implement anything (besides marking the relevant properties with `@addSearchIndex`).
Nevertheless, there are some cases where the default behavior, especially when rendering an object in the autocomplete search result list, should be changed. Therefore the object may optionally implement the interface
`SearchResultobjectInterface`
adding the method `getSearchAdminLinkForObject()`. Use this method to generate and return a different link (see `Link::getLinkAdminHref()`) to be used as soon as a user chooses the object out of the autocomplete list.
