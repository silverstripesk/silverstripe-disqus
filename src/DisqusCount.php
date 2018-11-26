<?php

namespace Silverstripesk\Disqus;

use SilverStripe\View\Requirements;

/**
 * Control Disqus Count JS
 *
 * @package silverstripe-disqus-module
 * @author Pavol Ondráš <admin_silverstripe.sk>
 * @notice SilverStripe.sk is not affiliated with the company SilverStripe Ltd.
 * @date April 2011
 */
class DisqusCount
{
    /**
     * @var bool
     */
    protected static $addCountJS = true;

    /**
     * @param $shortname
     * @param null $extraVars
     */
    public static function addCountJS($shortname, $extraVars = null)
    {
        if (self::$addCountJS) {
            $script = "
			    var disqus_shortname = '" . $shortname . "'; // required: replace example with your forum shortname
			    " . $extraVars . "
			
			    (function () {
			        var s = document.createElement('script'); s.async = true;
			        s.type = 'text/javascript';
			        s.src = 'https://' + disqus_shortname + '.disqus.com/count.js';
			        (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
			    }());
			";
            Requirements::customScript($script);
            self::$addCountJS = false;
        }
    }
}
