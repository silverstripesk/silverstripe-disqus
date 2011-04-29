<?php

/**
 * Sync disqus coments via cli (or browser)
 *
 * @package silverstripe-disqus-module
 * @author Pavol Ondráš <admin_silverstripe.sk>
 * @notice SilverStripe.sk is not affiliated with the company SilverStripe Ltd.
 * @date April 2011
 */

class Disqus_Controller extends Controller {
	
    function sync_by_ident() {
    	if (SYNCDISQUS) {
	    	$returnmessage = (Director::is_cli() || Director::isDev()) ? 1 : 0;
			return DisqusSync::sync(Director::URLParam("ID"),$returnmessage);
		} else {
			return "You need php 5.3+";
		}
    }
}

// EOF
