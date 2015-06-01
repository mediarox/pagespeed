<?php
/**
 * @package Pagespeed_Css
 * @copyright Copyright (c) 2015 mediarox UG (haftungsbeschraenkt) (http://www.mediarox.de)
 * @author Steven Fritzsche <sfritzsche@mediarox.de>
 * @author Thomas Uhlig <tuhlig@mediarox.de>
 */

/**
 * Standard observer class
 */
class Pagespeed_Css_Model_Observer
{
    /**
     * @const string
     */
    const HTML_TAG_BODY = '</body>';

    /**
     * Will finally contain all css tags to move.
     * @var string
     */
    private $cssTags = '';

    /**
     * Contains all exclude regex patterns.
     * @var array
     */
    private $excludeList = array();

    /**
     * Processes the matched single css tag or the conditional css tag group.
     *
     * Step 1: Return if hit is blacklisted by exclude list.
     * Step 2: Add hit to css tag list and return empty string for the replacement.
     *
     * @param array $hits
     * @return string
     */
    public function processHit($hits)
    {
        // Step 1
        if ($this->isHitExcluded($hits[0])) return $hits[0];

        // Step 2
        $this->cssTags .= $hits[0];
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
     * Move Css (head & inline) to the bottom. ({excluded_css}{stripped_html}{css}</body></html>)
     *
     * Step 1: Return if module is disabled.
     * Step 2: Load needed data.
     * Step 3: Return if no </body> is found in html.
     * Step 4: Search and replace conditional css units. (example: <!--[if lt IE 7]>{multiple css tags}<![endif]-->)
     * Step 5: Search and replace external css tags. (link-tags must xhtml-compliant closed by "/>")
     * Step 6: Search and replace inline css tags.
     * Step 7: Return if no css is found.
     * Step 8: Remove blank lines from html.
     * Step 9: Recalculating </body> position, insert css groups right before body ends and set response.
     *  Final order:
     *      1. excluded css
     *      2. stripped html
     *      3. conditional css tags
     *      4. external css tags
     *      5. inline css tags
     *      6. </body></html>
     *
     * @param Varien_Event_Observer $observer
     */
    public function parseCssToBottom(Varien_Event_Observer $observer)
    {
        //$timeStart = microtime(true);

        // Step 1
        $helper = Mage::helper('pagespeed_css');
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
            '#\<\!--\[if[^\>]*>\s*<link[^>]*type\="text\/css"[^>]*/>\s*<\!\[endif\]-->#isU',
            'self::processHit',
            $html
        );

        // Step 5
        $html = preg_replace_callback(
            '#<link[^>]*type\=["\']text\/css["\'][^>]*/>#isU',
            'self::processHit',
            $html
        );

        // Step 6
        $html = preg_replace_callback(
            '#<style.*</style>#isUm',
            'self::processHit',
            $html
        );

        // Step 7
        if (!$this->cssTags) return;

        // Step 8
        $html = preg_replace('/^\h*\v+/m', '', $html);

        // Step 9
        $closedBodyPosition = strripos($html, self::HTML_TAG_BODY);
        $html = substr_replace($html, $this->cssTags, $closedBodyPosition, 0);
        $response->setBody($html);

        //Mage::log(round(((microtime(true) - $timeStart) * 1000)) . ' ms taken to parse Css to bottom');
    }
}