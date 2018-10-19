<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\View\Components\WarningBox;

use Kajona\System\View\Components\AbstractComponent;

/**
 * Returns a warning box, e.g. shown before deleting a record
 *
 * @author sascha.broening@artemeon.de
 * @since 7.0
 * @componentTemplate template.twig
 */
class WarningBox extends AbstractComponent
{
    
    /**
     * @var string
     */
    protected $strContent;

    /**
     * @var string
     */
    protected $strClass;

    /**
     * @param string $strContent
     * @param string $strClass
     */
    public function __construct(string $strContent, string $strClass)
    {
        parent::__construct();

        $this->strContent = $strContent;
        $this->strClass = $strClass;
    }

    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        $data = [
            "content" => $this->strContent,
            "class" => $this->strClass
        ];

        return $this->renderTemplate($data);
    }
}
