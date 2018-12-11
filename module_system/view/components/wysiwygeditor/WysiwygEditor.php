<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\View\Components\Wysiwygeditor;

use Kajona\System\System\Link;
use Kajona\System\System\Resourceloader;
use Kajona\System\System\Session;
use Kajona\System\System\StringUtil;
use Kajona\System\View\Components\AbstractComponent;

/**
 * Returns a Wysiwyg editor text area
 *
 * @author andrii.konoval@artemeon.de
 * @since 7.0
 * @componentTemplate core/module_system/view/components/wysiwygeditor/template.twig
 */
class WysiwygEditor extends AbstractComponent
{

    /**
     * @var string
     */
    protected $strName;

    /**
     * @var string
     */
    protected $strTitle;

    /**
     * @var string
     */
    protected $strEditorId;

    /**
     * @var bool
     */
    protected $bitReadonly;

    /**
     * @var string
     */
    protected $strContent;

    /**
     * @var string
     */
    protected $strToolbarset;

    /**
     * @var string
     */
    protected $strOpener;

    /**
     * @var string
     */
    protected $strLanguage;

    /**
     * @var string
     */
    protected $strConfigFile;

    /**
     * WysiwygEditor constructor.
     * @param string $strName
     * @param string $strTitle
     * @param string $strContent
     * @param string $strToolbarset
     * @param string $strOpener
     * @param $bitReadonly
     * @throws \Kajona\System\System\Exception
     */
    public function __construct($strName,  $strTitle = "", $strContent = "", $strToolbarset = "standard", $bitReadonly = false, $strOpener = "" )
    {
        parent::__construct();

        $this->strName = $strName;
        $this->strTitle = $strTitle;
        $this->strOpener = $strOpener;
        $this->bitReadonly = $bitReadonly;
        $this->strContent = $strContent;
        $this->strToolbarset = $strToolbarset;
        $this->strEditorId = generateSystemid();

        //set the language the user defined for the admin
        $this->strLanguage = Session::getInstance()->getAdminLanguage();
        if ($this->strLanguage == "") {
            $this->strLanguage = "en";
        }

        //check if a customized editor-config is available
        $this->strConfigFile = "config_kajona_standard.js";

        if (is_file(_realpath_."project/module_system/scripts/admin/ckeditor/config_kajona_standard.js")) {
            $this->strConfigFile = "KAJONA_WEBPATH+'/project/module_system/admin/scripts/ckeditor/config_kajona_standard.js'";
        }

        if (is_file(_realpath_."project/module_system/scripts/ckeditor/config_kajona_standard.js")) {
            $this->strConfigFile = "KAJONA_WEBPATH+'/project/module_system/scripts/ckeditor/config_kajona_standard.js'";
        }

    }

    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        $data = [
            "name" => $this->strName,
            "title" => $this->strTitle,
            "opener" => $this->strOpener,
            "readonly" => $this->bitReadonly,
            "content" => $this->strContent,
            "editorid" => $this->strEditorId,
            "toolbarSet" => $this->strToolbarset,
            "language" => $this->strLanguage,
            "configFile" => $this->strConfigFile,
            "modulepath" => _webpath_.Resourceloader::getInstance()->getWebPathForModule("module_system")."/scripts/ckeditor/ckeditor.js",
            "filebrowserBrowseUrl" => StringUtil::replace("&amp;", "&", Link::getLinkAdminHref("folderview", "browserChooser", ['form_element' => 'ckeditor', 'download' => 1])),
            "filebrowserImageBrowseUrl" => StringUtil::replace("&amp;", "&", Link::getLinkAdminHref("folderview", "browserChooser", ['form_element' => 'ckeditor', 'download' => 1])),
        ];

        return $this->renderTemplate($data);
    }
}