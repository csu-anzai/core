<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\Flow\System\Flow\Action;

use Kajona\Flow\System\FlowActionAbstract;
use Kajona\Flow\System\FlowTransition;
use Kajona\Flow\System\SessionTransitionSkip;
use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\System\Model;

/**
 * Action which simply sets the skipped flag. This action should be mostly used at the backwards transition i.e. when
 * a user comes back from a review step
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class SkipTransitionAction extends FlowActionAbstract
{
    public function getTitle()
    {
        return $this->getLang("action_skip_transition_title", "flow");
    }

    public function getDescription()
    {
        return $this->getLang("action_skip_transition_description", "flow");
    }

    public function executeAction(Model $objObject, FlowTransition $objTransition)
    {
        SessionTransitionSkip::markSkip($objObject, $objTransition->getParentStatus());
    }

    public function configureForm(AdminFormgenerator $objForm, FlowTransition $objTransition)
    {
    }
}
