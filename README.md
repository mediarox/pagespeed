Magento Google Pagespeed Optimization Extension
-----------------------------------------------

This extension should help, to fulfill the requirements of the tool [Google PageSpeed Insights](https://developers.google.com/speed/pagespeed/insights/).

### Current features

1. Move every Javascript tag (head & inline) to the bottom. ```({stripped_html}{js}</body></html>)```
    * including conditional js units ```(<!--[if lt IE 7]>{multiple js tags}<![endif]-->)```
    * including external js tags
    * including "inline" js tags
2. Move every CSS (head & inline) to the bottom. ```({stripped_html}{css}</body></html>)```
    * including conditional css units ```(<!--[if lt IE 7]>{multiple css tags}<![endif]-->)```
    * including external css tags
    * including inline css tags

### How it works ?

Simple parse the final html stream on the event "controller_front_send_response_before".

### What about performance/paring time ?

On our local hardware the html parsing requires a maximum of 8 milliseconds.

### Requirements from PageSpeed Insights and planned Features

1. ~~[Eliminate render-blocking JavaScript and CSS in above-the-fold content](https://developers.google.com/speed/docs/insights/BlockingJS)~~ (feature 1 & 2)
2. [Prioritize visible content](https://developers.google.com/speed/docs/insights/PrioritizeVisibleContent)

### Requirements from PageSpeed Insights which are covered by 3rd party extensions

1. [Minify CSS](https://developers.google.com/speed/docs/insights/MinifyResources)
2. [Minify JavaScript](https://developers.google.com/speed/docs/insights/MinifyResources)
    * Both are covered by [Speedster Advanced by Fooman](http://www.magentocommerce.com/magento-connect/speedster-advanced-by-fooman.html) (note: that we have no experience with this extension, but Fooman seems to be a good guy.)
3. [Optimize images](https://developers.google.com/speed/docs/insights/OptimizeImages)
    * [Image Optimization](http://www.magentocommerce.com/magento-connect/image-optimization.html)(note: no experience too.)
4. [Minify HTML](https://developers.google.com/speed/docs/insights/MinifyResources)
    * Based primary on [Minify CSS](https://developers.google.com/speed/docs/insights/MinifyResources) and [Minify JavaScript](https://developers.google.com/speed/docs/insights/MinifyResources).

### Requirements from PageSpeed Insights which are covered by your server admin :)

1. [Enable compression](https://developers.google.com/speed/docs/insights/EnableCompression)
2. [Avoid landing page redirects](https://developers.google.com/speed/docs/insights/AvoidRedirects)
3. [Leverage browser caching](https://developers.google.com/speed/docs/insights/LeverageBrowserCaching)
4. [Reduce server response time](https://developers.google.com/speed/docs/insights/Server)

![Goal]: http://www.mediarox.de/goal.png

### Notices

1. There is also a great tool called [PageSpeed Module](https://developers.google.com/speed/pagespeed/module)
for common webservers like apache and nginx. If you have the opportunity: Use it, but read the manual.
2. Test before use. There are also "great" things like multiple "</body>" tags, that will crash the party.
3. Front Page Cache: Test it. Look that our event "controller_front_send_response_before" is called before
your FPC-Extension starts to observe.


### Developer

Steven Fritzsche [@de_mediarox](https://twitter.com/de_mediarox)

### Licence

[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

### Copyright

(c) 2015 Steven Fritzsche <sfritzsche@mediarox.de>

