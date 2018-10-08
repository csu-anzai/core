<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\System\Admin;

use Kajona\System\System\LanguagesLanguage;

/**
 * Trait to keep common logic for i18n based formentries.
 * Currently providing helpers to explode / implode values
 * and to detect i18n capabilities
 *
 * @author stefan.idler@artemeon.de
 * @since 7.1
 */
trait FormentryI18nTrait
{
    private $i18nEnabled = false;

    /**
     * Evaluates if a value represents an i18n value
     * @param $value
     * @return bool
     */
    public function is18nValue($value)
    {
        $val = json_decode($value, true);
        return $val !== null;
    }

    /**
     * Converts the passed value (i18n or plain string) to an i18n based array.
     * @param string $i18nString
     * @return array
     */
    protected function toValueArray(string $i18nString = null): array
    {
        $val = json_decode($i18nString ?? "", true);
        $return = [];

        foreach ($this->getPossibleLanguages() as $lang) {
            $return[$lang] = isset($val[$lang]) ? $val[$lang] : ($val !== null ? "" : $i18nString);
        }

        return $return;
    }

    /**
     * Converts post-values back to a storable, single-field string
     * @param array $params
     * @param string $key
     * @return string
     */
    protected function toValueString(array $params, string $key): string
    {
        $return = [];
        foreach ($this->getPossibleLanguages() as $lang) {
            $return[$lang] = $params["{$key}_{$lang}"] ?? "";
        }
        return json_encode($return);
    }

    /**
     * Returns the list of languages currently available
     * @return array
     */
    protected function getPossibleLanguages()
    {
        return array_map(function (LanguagesLanguage $lang) {
            return $lang->getStrName();
        }, LanguagesLanguage::getObjectListFiltered());
    }

    /**
     * @return bool
     */
    public function isI18nEnabled(): bool
    {
        return $this->i18nEnabled;
    }

    /**
     * @param bool $i18nEnabled
     * @return FormentryI18nTrait
     */
    public function setI18nEnabled(bool $i18nEnabled)
    {
        $this->i18nEnabled = $i18nEnabled;
        return $this;
    }
}
