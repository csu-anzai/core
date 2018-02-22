<?php
/**
 * Created by PhpStorm.
 * User: sidler
 * Date: 15.01.18
 * Time: 16:32
 */

namespace Kajona\System\View\Components;

use Kajona\System\System\Carrier;
use Kajona\System\System\Reflection;


/**
 * Base class for view components
 * @package Kajona\View\Components
 */
abstract class AbstractComponent
{
    /**
     * The template used to render the current component. Please add the folder-name, too.-
     * Example: datatable/template.tpl
     */
    const STR_TEMPLATE_ANNOTATION = "@componentTemplate";

    private $strTemplateName = "";

    /**
     * AbstractComponent constructor.
     */
    public function __construct()
    {
        //parse the template section from the current class' config
        $objReflection = new Reflection($this);
        $arrAnnotationValues = $objReflection->getAnnotationValuesFromClass(self::STR_TEMPLATE_ANNOTATION);
        if (count($arrAnnotationValues) > 0) {
            $this->strTemplateName = trim($arrAnnotationValues[0]);
        }
    }

    protected function renderTemplate(array $arrData, string $strSection): string
    {
        $objTemplate = Carrier::getInstance()->getObjTemplate();
        return $objTemplate->fillTemplateFile($arrData, "/view/components/".$this->strTemplateName, $strSection);
    }

    /**
     * Renders the component itself
     * @return string
     */
    abstract public function renderComponent(): string;

}