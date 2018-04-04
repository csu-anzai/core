<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\System\View\Components\Listsearch;

use Kajona\System\System\Lang;
use Kajona\System\View\Components\AbstractComponent;

/**
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 * @componentTemplate template.twig
 */
class Listsearch extends AbstractComponent
{
    /**
     * @var string
     */
    protected $endpointUrl;

    /**
     * @var string
     */
    protected $searchPlaceholder;

    /**
     * @param string $endpointUrl
     */
    public function __construct($endpointUrl)
    {
        parent::__construct();

        $this->endpointUrl = $endpointUrl;
        $this->searchPlaceholder = Lang::getInstance()->getLang("commons_search_field_placeholder", "module_commons");
    }

    /**
     * @param string $endpointUrl
     */
    public function setEndpointUrl(string $endpointUrl)
    {
        $this->endpointUrl = $endpointUrl;
    }

    /**
     * @param string $searchPlaceholder
     */
    public function setSearchPlaceholder(string $searchPlaceholder)
    {
        $this->searchPlaceholder = $searchPlaceholder;
    }

    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        $data = [
            "endpoint" => json_encode($this->endpointUrl),
            "search_placeholder" => $this->searchPlaceholder,
            "form_id" => generateSystemid(),
        ];

        return $this->renderTemplate($data);
    }
}
