<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 ********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\Flow\System;

use AGP\Contracts\System\ContractsQuickcheck;
use AGP\Contracts\System\OutsourcingScoringInterface;
use AGP\Contracts\System\QuickcheckResultEnum;
use AGP\Prozessverwaltung\System\ProzessverwaltungCommons;
use Kajona\System\System\Model;

/**
 * FlowEBAConditionAbstract
  */
abstract class FlowEBAConditionAbstract extends FlowConditionAbstract
{
    /**
     * Checks basing on the MaRisk Quick Check if object is "EBA Relevance"
     *
     * @param Model $objObject
     * @return bool
     */
    protected function isEBARelevance(Model $objObject): bool
    {
        $objLastRating = ProzessverwaltungCommons::getLatestEvaluation($objObject, ContractsQuickcheck::class, true);

        if (!$objLastRating instanceof OutsourcingScoringInterface) {
            return false;
        }

        if (!$objLastRating->getOutsourcingScoring()->equals(QuickcheckResultEnum::EBARELEVANCE())) {
            return false;
        }

        return true;
    }
}
