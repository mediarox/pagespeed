<?php
/**
 * @package Pagespeed_Css
 * @copyright Copyright (c) 2015 mediarox UG (haftungsbeschraenkt) (http://www.mediarox.de)
 * @author Steven Fritzsche <sfritzsche@mediarox.de>
 */

/**
 * Standard observer class
 */
class Pagespeed_Css_Model_Observer
{
    /**
     * Move every Css (head & inline) to the bottom. ({stripped_html}{css}</body></html>)
     *
     * Step 1: Load needed data.
     * Step 2: Return if no </body> is found in html.
     * Step 3: Search and replace conditional css units. (example: <!--[if lt IE 7]>{multiple css tags}<![endif]-->)
     * Step 4: Search and replace external css tags. (link-tags must xhtml-compliant closed by "/>")
     * Step 5: Search and replace inline css tags.
     * Step 6: Return if no css is found.
     * Step 7: Remove blank lines from html.
     * Step 8: Recalculating </body> position, insert css groups right before body ends and set response.
     *  Final order:
     *      1. stripped html
     *      2. conditional css tags
     *      3. external css tags
     *      3. inline css tags
     *      4. </body></html>
     *
     * @param Varien_Event_Observer $observer
     */
    public function parseCssToBottom(Varien_Event_Observer $observer)
    {
        //$timeStart = microtime(true);

        // Step 1
        $response = $observer->getFront()->getResponse();
        $html = $response->getBody();

        // Step 2
        $closedBodyPosition = strpos($html, '</body>');
        if (false === $closedBodyPosition) return;

        // Step 3
        $conditionalCssTags = '';
        $conditionalCssPattern = '#\<\!--\[if[^\>]*>\s*<link[^>]*type\="text\/css"[^>]*/>\s*<\!\[endif\]-->#isUm';
        $conditionalCssHits = preg_match_all($conditionalCssPattern, $html, $conditionalMatches);

        if((bool)$conditionalCssHits) {
            $conditionalCssTags = implode('', $conditionalMatches[0]);
            $html = preg_replace($conditionalCssPattern, '' , $html);
        }

        // Step 4
        $externalCssTags = '';
        $externalCssPattern = '#<link[^>]*type\=["\']text\/css["\'][^>]*/>#isUm';
        $externalCssHits = preg_match_all($externalCssPattern, $html, $externalMatches);

        if((bool)$externalCssHits) {
            $externalCssTags = implode('', $externalMatches[0]);
            $html = preg_replace($externalCssPattern, '' , $html);
        }

        // Step 5
        $inlineCssTags = '';
        $inlineCssPattern = '#<style.*</style>#isUm';
        $inlineCssHits = preg_match_all($inlineCssPattern, $html, $inlineMatches);

        if((bool)$inlineCssHits) {
            $inlineCssTags = implode('', $inlineMatches[0]);
            $html = preg_replace($inlineCssPattern, '' , $html);
        }

        // Step 6
        $overallCssHit = ((bool)$conditionalCssHits || (bool)$externalCssHits || (bool)$inlineCssHits);
        if(!$overallCssHit) return;

        // Step 7
        $html = preg_replace('/^\h*\v+/m', '', $html);

        // Step 8
        $closedBodyPosition = strpos($html, '</body>');
        $css = $conditionalCssTags . $externalCssTags . $inlineCssTags;
        $html = substr_replace($html, $css, $closedBodyPosition, 0);
        $response->setBody($html);

        //Mage::log('parseCssToBottom ' . round(((microtime(true) - $timeStart) * 1000)) . ' ms');
    }
}