<?php
/**
 * @package Pagespeed_Css
 * @copyright Copyright (c) 2015 mediarox UG (haftungsbeschraenkt) (http://www.mediarox.de)
 * @author Steven Fritzsche <sfritzsche@mediarox.de>
 * @author Thomas Uhlig <tuhlig@mediarox.de>
 */

/**
 * Standard helper
 */
class Pagespeed_Css_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Configuration paths
     */
    const PAGESPEED_CSS_ENABLED = 'pagespeed/css/enabled';
    const PAGESPEED_CSS_EXCLUDE_ENABLED = 'pagespeed/css/exclude_enabled';
    const PAGESPEED_CSS_EXCLUDE = 'pagespeed/css/exclude';

    /**
     * Is css module enabled ?
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::PAGESPEED_CSS_ENABLED);
    }

    /**
     * Is exclude list enabled ?
     *
     * @return bool
     */
    public function isExcludeEnabled()
    {
        return Mage::getStoreConfigFlag(self::PAGESPEED_CSS_EXCLUDE_ENABLED);
    }

    /**
     * Retrieve css configuration exclude list
     *
     * @return array of regex patterns
     */
    public function getExcludeList()
    {
        $result = array();
        if ($this->isExcludeEnabled()) {
            $exclude = Mage::getStoreConfig(self::PAGESPEED_CSS_EXCLUDE);
            $exclude = explode(PHP_EOL, $exclude);
            foreach ($exclude as $item) {
                if ($item = trim($item)) {
                    $result[] = $item;
                }
            }
        }
        return $result;
    }
}