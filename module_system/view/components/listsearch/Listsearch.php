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
 * A list component with a search text field at the top. By default we query the general search endpoint but you can
 * also specify a custom endpoint to implement a specific behaviour
 *
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
     * @var array
     */
    protected $items;

    /**
     * @param string $endpointUrl
     * @param array $items
     */
    public function __construct($endpointUrl = null, array $items = [])
    {
        parent::__construct();

        $this->endpointUrl = $endpointUrl;
        $this->items = $items;
        $this->searchPlaceholder = Lang::getInstance()->getLang("form_search_query", "search");
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
     * @param array $items
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }

    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        $endpointUrl = $this->endpointUrl;
        if (empty($this->endpointUrl)) {
            $endpointUrl = _webpath_."/xml.php?admin=1&module=search&action=searchXml&asJson=1";
        }

        $data = [
            "endpoint_url" => $endpointUrl,
            "search_placeholder" => $this->searchPlaceholder,
            "items" => $this->items,
            "form_id" => generateSystemid(),
        ];

        return $this->renderTemplate($data);
    }
}
