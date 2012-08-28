<?php

/**
 * Control Disqus Count JS
 *
 * @package silverstripe-disqus-module
 * @author Pavol Ondráš <admin_silverstripe.sk>
 * @notice SilverStripe.sk is not affiliated with the company SilverStripe Ltd.
 * @date April 2011
 */

class DisqusCount  {
			
	protected static $addCountJS = true;
	
	public static function addCountJS($shortname,$extraVars = NULL) {
			if (self::$addCountJS) {
				$script = "
			    var disqus_shortname = '".$shortname."'; // required: replace example with your forum shortname
			    ".$extraVars."
			
			    (function () {
			        var s = document.createElement('script'); s.async = true;
			        s.type = 'text/javascript';
			        s.src = 'http://' + disqus_shortname + '.disqus.com/count.js';
			        (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
			    }());
			    ";
				Requirements::customScript($script);
				self::$addCountJS = false;
			}
	}
					
}

// EOF
