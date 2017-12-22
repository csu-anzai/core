
# Reference: Architecture

This document should roughly explain the basic concepts of a module with references
to more detailed documentation.

# Module

## Controller

The controller provides methods which handle HTTP requests. These methods must start with 
the name `action*` to be accessible. It is a simple class which extends from the 
`AdminController` class. The `action*` methods can be called through 
`index.php?admin=1&module=system&action=[action]`.

### Model

A model is the basic entity to store data in a database. The model can contain additional
annotations i.e. to describe which formentries should be used.

See: [ORM](reference_orm.md)

### DI

Inside the controller it is possible to inject services through the `@inject` annotation.
It is recommended to move as much logic as possible into separate service classes so
that the controller does not contain any logic. If you have specific create, update, 
delete, copy logic you should place them in a life cycle service.

See: [DI-Container](reference_dicontainer.md)

### Lifecycle

A life cycle service is a class which implements the `ServiceLifeCycleInterface` interface.
By default the class `ServiceLifeCycleImpl` is used for every model. To use another 
implementation you can define a `@lifeCycle` annotation on the model where you can specify
a service name.

See: [LifeCycle](reference_lifecycle.md)

### Formgenerator

The Formgenerator is a class which generates a specific form for a model. Use the 
`@formGenerator` annotation to specify a different implementation for the model.
Always use a separate form generator class instead of overwriting the `getAdminForm` 
in the controller. This simplifies the reuse of this form presentation.

### Objectvalidator

The object validator is coupled to a model and can be used to specify specific validation
logic which can not be handeled through the standard form entry validators. An object
validator should extend the class `ObjectvalidatorBase`.

See: [ObjectValidator](reference_objectvalidator.md)

### Flow

Through the flow module it is possible to dynamically change the status flow of a model.
If you want to use a flow in your model you can need to create a flow handler which
is a class extending from `FlowHandlerAbstract`. Also you controller needs to use the 
trait `FlowControllerTrait`. In you model you can also use the `FlowModelTrait` to
simplify the right handling.

See: [Flow](../../module_flow/docs/manual_flow.md)

## Installer

### Samplecontent

## Event

See: [Events](reference_events.md)

## System

## Tests

