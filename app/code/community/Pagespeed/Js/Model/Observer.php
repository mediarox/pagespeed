<?php
/**
 * @package Pagespeed_Js
 * @copyright Copyright (c) 2015 mediarox UG (haftungsbeschraenkt) (http://www.mediarox.de)
 * @author Steven Fritzsche <sfritzsche@mediarox.de>
 */

/**
 * Standard observer class
 */
class Pagespeed_Js_Model_Observer
{
    /**
     * Move every JS (head & inline) to the bottom. ({stripped_html}{js}</body></html>)
     *
     * Step 1: Load needed data.
     * Step 2: Return if no </body> is found in html.
     * Step 3: Search and replace conditional js units. (example: <!--[if lt IE 7]>{multiple js tags}<![endif]-->)
     * Step 4: Search and replace normal js tags.
     * Step 5: Return if no js is found.
     * Step 6: Remove blank lines from html.
     * Step 7: Recalculating </body> position, insert js groups right before body ends and set response.
     *  Final order:
     *      1. stripped html
     *      2. conditional js tags
     *      3. normal js tags
     *      4. </body></html>
     *
     * @param Varien_Event_Observer $observer
     */
    public function parseJsToBottom(Varien_Event_Observer $observer)
    {
        //$timeStart = microtime(true);

        // Step 1
        $response = $observer->getFront()->getResponse();
        $html = $response->getBody();

        // Step 2
        $closedBodyPosition = strpos($html, '</body>');
        if(false === $closedBodyPosition) return;

        // Step 3
        $conditionalJsTags = '';
        $conditionalJsPattern = '#\<\!--\[if[^\>]*>\s*<script.*</script>\s*<\!\[endif\]-->#isUm';
        $conditionalJsHits = preg_match_all($conditionalJsPattern, $html, $conditionalMatches);

        if((bool)$conditionalJsHits) {
            $conditionalJsTags = implode('', $conditionalMatches[0]);
            $html = preg_replace($conditionalJsPattern, '' , $html);
        }

        // Step 4
        $jsTags = '';
        $jsPattern = '#<script.*</script>#isUm';
        $jsHits = preg_match_all($jsPattern, $html, $matches);

        if((bool)$jsHits) {
            $jsTags = implode('', $matches[0]);
            $html = preg_replace($jsPattern, '' , $html);
        }

        // Step 5
        $overallJsHit = ((bool)$conditionalJsHits || (bool)$jsHits);
        if(!$overallJsHit) return;

        // Step 6
        $html = preg_replace('/^\h*\v+/m', '', $html);

        // Step 7
        $closedBodyPosition = strpos($html, '</body>');
        $html = substr_replace($html, $conditionalJsTags . $jsTags, $closedBodyPosition, 0);
        $response->setBody($html);

        //Mage::log('parseJsToBottom ' . round(((microtime(true) - $timeStart) * 1000)) . ' ms');
    }
}