<?php
/**
 * @package Pagespeed_Js
 * @copyright Copyright (c) 2015 mediarox UG (haftungsbeschraenkt) (http://www.mediarox.de)
 * @author Steven Fritzsche <sfritzsche@mediarox.de>
 * @author Thomas Uhlig <tuhlig@mediarox.de>
 */

/**
 * Standard observer class
 */
class Pagespeed_Js_Model_Observer
{
    /**
     * @const string
     */
    const HTML_TAG_BODY = '</body>';

    /**
     * Will finally contain all js tags to move.
     * @var string
     */
    private $jsTags = '';

    /**
     * Contains all exclude regex patterns.
     * @var array
     */
    private $excludeList = array();

    /**
     * Processes the matched single js tag or the conditional js tag group.
     *
     * Step 1: Return if hit is blacklisted by exclude list.
     * Step 2: Add hit to js tag list and return empty string for the replacement.
     *
     * @param array $hits
     * @return string
     */
    public function processHit($hits)
    {
        // Step 1
        if ($this->isHitExcluded($hits[0])) return $hits[0];

        // Step 2
        $this->jsTags .= $hits[0];
        return '';
    }

    /**
     * Is hit on exclude list?
     *
     * @param string $hit
     * @return bool
     */
    protected function isHitExcluded($hit)
    {
        $c = 0;
        preg_replace($this->excludeList, '', $hit, -1, $c);
        return ($c > 0);
    }

    /**
     * Move Js (head & inline) to the bottom. ({excluded_js}{stripped_html}{js}</body></html>)
     *
     * Step 1: Return if module is disabled.
     * Step 2: Load needed data.
     * Step 3: Return if no </body> is found in html.
     * Step 4: Search and replace conditional js units. (example: <!--[if lt IE 7]>{multiple js tags}<![endif]-->)
     * Step 5: Search and replace normal js tags.
     * Step 6: Return if no js is found.
     * Step 7: Remove blank lines from html.
     * Step 8: Recalculating </body> position, insert js groups right before body ends and set response.
     *  Final order:
     *      1. excluded js
     *      2. stripped html
     *      3. conditional js tags
     *      4. normal js tags
     *      5. </body></html>
     *
     * @param Varien_Event_Observer $observer
     */
    public function parseJsToBottom(Varien_Event_Observer $observer)
    {
        //$timeStart = microtime(true);

        // Step 1
        $helper = Mage::helper('pagespeed_js');
        if (!$helper->isEnabled()) return;

        // Step 2
        $response = $observer->getFront()->getResponse();
        $html = $response->getBody();
        $this->excludeList = $helper->getExcludeList();

        // Step 3
        $closedBodyPosition = strripos($html, self::HTML_TAG_BODY);
        if (false === $closedBodyPosition) return;

        // Step 4
        $html = preg_replace_callback(
            '#\<\!--\[if[^\>]*>\s*<script.*</script>\s*<\!\[endif\]-->#isU',
            'self::processHit',
            $html
        );

        // Step 5
        $html = preg_replace_callback(
            '#<script.*</script>#isU',
            'self::processHit',
            $html
        );

        // Step 6
        if (!$this->jsTags) return;

        // Step 7
        $html = preg_replace('/^\h*\v+/m', '', $html);

        // Step 8
        $closedBodyPosition = strripos($html, self::HTML_TAG_BODY);
        $html = substr_replace($html, $this->jsTags, $closedBodyPosition, 0);
        $response->setBody($html);

        //Mage::log(round(((microtime(true) - $timeStart) * 1000)) . ' ms taken to parse Js to bottom');
    }
}