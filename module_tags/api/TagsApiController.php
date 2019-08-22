<?php

/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/
namespace Kajona\Tags\Api;

use Kajona\Api\System\ApiControllerInterface;
use Kajona\Api\System\Http\JsonResponse;
use Kajona\System\Admin\ToolkitAdmin;
use Kajona\Tags\System\TagsTag;
use PSX\Http\Environment\HttpContext;
use PSX\Http\Environment\HttpResponse;

/**
 * TagsApiController
 *
 * @author dhafer.harrathi@artemeon.de
 * @since 7.1
 */

class TagsApiController  implements ApiControllerInterface
{
    /**
     * @inject system_admintoolkit
     * @var ToolkitAdmin
     */
    protected $objToolkit;


    /**
     * Returns the list of tags assigned to the passed system-record.
     *
     * @param HttpContext $context
     * @return HttpResponse
     * @api
     * @method GET
     * @path /v1/tags/{id}
     * @authorization usertoken
     */
    public function getTags(HttpContext $context) : HttpResponse
    {
        $strSystemid = $context->getUriFragment('id');
        $tags = [] ;
        $arrTags = TagsTag::getTagsForSystemid($strSystemid);
        foreach ($arrTags as $objOneTag) {
            $tags .= $this->objToolkit->getTagEntry($objOneTag, $strSystemid,null);
        }
        return new JsonResponse($tags);
    }
}
