<?php
/**
 * @package Pagespeed_Js
 * @copyright Copyright (c) 2015 mediarox UG (haftungsbeschraenkt) (http://www.mediarox.de)
 * @author Steven Fritzsche <sfritzsche@mediarox.de>
 * @author Thomas Uhlig <tuhlig@mediarox.de>
 */

/**
 * Standard helper
 */
class Pagespeed_Js_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Configuration paths
     */
    const PAGESPEED_JS_ENABLED = 'pagespeed/js/enabled';
    const PAGESPEED_JS_EXCLUDE_ENABLED = 'pagespeed/js/exclude_enabled';
    const PAGESPEED_JS_EXCLUDE = 'pagespeed/js/exclude';

    /**
     * Is js module enabled ?
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::PAGESPEED_JS_ENABLED);
    }

    /**
     * Is exclude list enabled ?
     *
     * @return bool
     */
    public function isExcludeEnabled()
    {
        return Mage::getStoreConfigFlag(self::PAGESPEED_JS_EXCLUDE_ENABLED);
    }

    /**
     * Retrieve js configuration exclude list
     *
     * @return array of regex patterns
     */
    public function getExcludeList()
    {
        $result = array();
        if ($this->isExcludeEnabled()) {
            $exclude = Mage::getStoreConfig(self::PAGESPEED_JS_EXCLUDE);
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