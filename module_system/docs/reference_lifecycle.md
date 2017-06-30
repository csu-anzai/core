# Life cycle

In the Kajona 7.0 release we have introduced a new design layer called life cycle. 
Previously the controller has worked directly with model classes to create and update
entries. To reduce the logic inside the model and to make the code more testable we 
have introduced a new service layer.

![life_cycle_design]

The life cycle class can contain complex business logic which is executed i.e. on update.
A life cycle is a simple class which implements the interface `ServiceLifeCycleInterface`:

```php
<?php

interface ServiceLifeCycleInterface
{
    /**
     * Persists all fields of the record to the database and executes additional business logic i.e. sending a message
     * or create a rating
     *
     * @param \Kajona\System\System\Root $objModel
     * @param bool $strPrevId
     */
    public function update(Root $objModel, $strPrevId = false);

    /**
     * Deletes a record and all of its child nodes. This performs a logically delete that means that we set only a flag
     * that the entry is deleted the actual db entry still exists
     *
     * @param \Kajona\System\System\Root $objModel
     */
    public function delete(Root $objModel);

    /**
     * Restores a previously deleted record
     *
     * @param \Kajona\System\System\Root $objModel
     */
    public function restore(Root $objModel);

    /**
     * Creates a copy of the record and all of its child nodes. Returns the new created record
     *
     * @param \Kajona\System\System\Root $objModel
     * @param bool $strNewPrevid
     * @param bool $bitChangeTitle
     * @param bool $bitCopyChilds
     * @return \Kajona\System\System\Root
     */
    public function copy(Root $objModel, $strNewPrevid = false, $bitChangeTitle = true, $bitCopyChilds = true);
}
```

Each model class can contain a `@lifeCycleService` annotation which provides a service name to the 
depending life cycle service. (If you have worked with the doctrine ORM this similar to the 
`@Repository` annotation on an entity). The controller knows through this annotation which life cycle
service should be used. There is also a default implementation `ServiceLifeCycleImpl` which is used 
if no service was specified.

A obvious pitfall with this implementation is: If you call these methods directly on the model you 
bypass the complete business logic of the life cycle service. In the future we may remove these 
methods from a model so that you can only update/delete/restore/copy a model through the life cycle 
service.

It is recommended to develop the life cycle service in a stateless way so that multiple calls to
an update method with different models always result in the same behaviour (i.e. dont store the 
model in a property of the class etc.). If you need to execute extra logic which is by default not 
needed i.e. calculate a score you should add a specific `protected` update method i.e. 
`updateWithCalculation`.

You should use a life cycle service only in a controller or in another life cycle service. It is not
recommended to call a life cycle service from a model. This could create circular references and in
general complicates the program flow.

[life_cycle_design]: img/life_cycle_design.png
