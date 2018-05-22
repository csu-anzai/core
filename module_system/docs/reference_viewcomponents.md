# View Components

While Kajona and AGP Core used a central toolkit up until version 6.6, all UI components will be moved to single
view components.

View components may and should be located either in a central module (the component is reused system wide) or within a 
dedicated module (the component is used by the module, only).
By convention, components are located in a package below `module/view/components`, e.g. `module_system/view/components/listsearch`.

Each component must follow the structure below, as an example we'll use a component named `datatable`:

    /view/components
        /datatable
            /less/datatable.less (less file is optional)
            /Datatable.php
            /template.twig (filename may differ)
            / ...
            
## Component class            
A components php-class must implement the `\Kajona\System\View\Components\AbstractComponent` class and define the template
to be used by providing a `@componentTemplate` annotation:

```php
/**
 * @componentTemplate template.twig
 */
class Listsearch extends AbstractComponent

```

## Component template
Each component may use a template. The only requirement is to use a single root/container node for a template.

A possible template could be:

```twig
<div class="core-component-datatable">
    <h2> ... </h2>
    <table> ... </table>
</div>
```

whereas multiple root-nodes are **NOT** allowed:

```twig
<h2 class='datatable-heder'> ... </h2>
<table class='datatable'> ... </table>
```

## Component styles
Whenever necessary, every component may define its' own set of css/less styles. To keep impact of inidividual styles as
minimal as possible, all styles used by a component must be fenced by a common class name mapped to the root-node of the template.
The class-fence must be named `core-component-name`.
Using the example above, the class name is defined as `core-component-datatable`. A possible less file could be:

```less
.core-component-datatable {
  h2 {}
  table {}
}
```

Nevertheless, try to use component-based styles as less as possible. Component-based styles reduce the overall unity of the interface and
therefore the recognition value along with the usability.