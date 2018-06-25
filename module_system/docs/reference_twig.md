
# Reference: Twig

We are using the Twig (https://twig.symfony.com/) template engine to render components. This
reference contains a list with all available custom filters which can be used inside a twig
template:

## `lang`

Through this you can access any lang property inside a twig template.

```twig
<h1>{{ "copy_document_hint"|lang("contracts") }}</h1>
```

## `date_to_string`

Transforms a date object to a string using the current locale of the user.

```twig
<!-- long date -->
<span>{{ date|date_to_string }}</span>

<!-- short date -->
<span>{{ date|date_to_string(false) }}</span>
```

## `number_format`

Formats a number according to the current locale of the user.

```twig
<span>{{ number|number_format }}</span>
```

## `webpath`

Returns the webpath of a module, so either the filesystem path or the extract path.

```twig
<span>{{ "module_name"|webpath }}</span>
```

