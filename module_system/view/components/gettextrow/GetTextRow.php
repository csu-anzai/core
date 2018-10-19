<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\View\Components\WarningBox;

use Kajona\System\View\Components\AbstractComponent;

/**
 * Returns a single TextRow
 *
 * @author sascha.broening@artemeon.de
 * @since 7.0
 * @componentTemplate template.twig
 */
class GetTextRow extends AbstractComponent
{

    /**
     * @var string
     */
    protected $strText;

    /**
     * @var string
     */
    protected $strClass;

    /**
     * @param string $strText
     * @param string $strClass
     */
    public function __construct(string $strText, string $strClass)
    {
        parent::__construct();

        $this->strText = $strText;
        $this->strClass = $strClass;
    }

    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        $data = [
            "text" => $this->strText,
            "class" => $this->strClass
        ];

        return $this->renderTemplate($data, "text_row");
    }
}
