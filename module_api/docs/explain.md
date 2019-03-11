
# Integration

To add a new endpoint to the API you need to create a controller class in your module
which declares an API endpoint by using specific annotations. The name of the method
is not important, it must be only public so that it can be called from the outside.

```
<?php

use Kajona\Api\System\ApiControllerInterface;

class MyController implements ApiControllerInterface
{
    /**
     * @api
     * @method GET
     * @path /my/endpoint
     */
    public function myEndpoint()
    {
        return [
            "Hello" => "World!",
        ];
    }
}

```

