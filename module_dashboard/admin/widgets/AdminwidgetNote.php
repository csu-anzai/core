<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                        		*
********************************************************************************************************/

namespace Kajona\Dashboard\Admin\Widgets;

use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\Admin\Formentries\FormentryTextarea;

/**
 * @package module_dashboard
 *
 */
class AdminwidgetNote extends Adminwidget implements AdminwidgetInterface
{

    /**
     * Basic constructor, registers the fields to be persisted and loaded
     *
     */
    public function __construct()
    {
        parent::__construct();
        //register the fields to be persisted and loaded
        $this->setPersistenceKeys(array("content"));
    }

    /**
     * @inheritdoc
     */
    public function getEditFormContent(AdminFormgenerator $form)
    {
        $form->addField(new FormentryTextarea("content", ""), "")
        ->setStrValue($this->getFieldValue("content"));
    }

    /**
     * This method is called, when the widget should generate it's content.
     * Return the complete content using the methods provided by the base class.
     * Do NOT use the toolkit right here!
     *
     * @return string
     * @throws \Kajona\System\System\Exception
     */
    public function getWidgetOutput()
    {
        if ($this->getFieldValue("content") == "") {
            return $this->getEditWidgetForm();
        }

        return $this->widgetText(nl2br($this->getFieldValue("content")));
    }

    /**
     * Return a short (!) name of the widget.
     *
     * @return string
     */
    public function getWidgetName()
    {
        return $this->getLang("note_name");
    }

    /**
     * @inheritdoc
     */
    public function getWidgetDescription()
    {
        return $this->getLang("note_description");
    }

    /**
     * @return string
     */
    public function getWidgetImg()
    {
        return "/files/extract/widgets/note.png";
    }

}

