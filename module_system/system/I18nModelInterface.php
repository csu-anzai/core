<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\System\System;

/**
 * Marker interface to detect i18n capabilities automatically
 *
 * @author stefan.idler@artemeon.de
 * @since 7.1
 */
interface I18nModelInterface
{
    public function setI18NEnabled(bool $i18NEnabled);
}
