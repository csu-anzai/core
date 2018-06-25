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
use Kajona\System\System\ServiceProvider;

/**
 * Base class for view components
 *
 * @package Kajona\View\Components
 */
abstract class AbstractComponent
{
    /**
     * The template used to render the current component. Please add the folder-name, too.-
     * Example: datatable/template.tpl
     *
     * If the template file ends with .twig we use the twig template engine in this case you dont need to specify the
     * folder instead you need to reference the template file relative to the class file
     */
    const STR_TEMPLATE_ANNOTATION = "@componentTemplate";

    /**
     * @var string
     */
    private $strTemplateName;

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

    /**
     * @param array $arrData
     * @param string $strSection
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function renderTemplate(array $arrData, string $strSection = ""): string
    {
        $strExtension = pathinfo($this->strTemplateName, PATHINFO_EXTENSION);

        if ($strExtension == "twig") {
            $reflection = new \ReflectionObject($this);
            $classDir = dirname($reflection->getFileName());
            $classDir = str_replace(_realpath_, "", str_replace("\\", "/", $classDir));

            /** @var \Twig_Environment $twig */
            $twig = Carrier::getInstance()->getContainer()->offsetGet(ServiceProvider::STR_TEMPLATE_ENGINE);
            return $twig->render($classDir . "/" . $this->strTemplateName, $arrData);
        } else {
            $objTemplate = Carrier::getInstance()->getObjTemplate();
            return $objTemplate->fillTemplateFile($arrData, "/view/components/".$this->strTemplateName, $strSection);
        }
    }

    /**
     * Renders the component itself
     *
     * @return string
     */
    abstract public function renderComponent(): string;
}
