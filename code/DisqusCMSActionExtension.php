<?php

/**
 * Adds a function to LeftAndMain to sync disqus comments.
 *
 * @package silverstripe-disqus-module
 * @author Pavol Ondráš <admin_silverstripe.sk>
 * @notice SilverStripe.sk is not affiliated with the company SilverStripe Ltd.
 * @date April 2011
 */

class DisqusCMSActionExtension extends LeftAndMainExtension {
	
	private static $allowed_actions = array(
        	'syncAllCommentsAction'
    	);
     
    	function syncCommentsAction() {	
    	    	
    		$id = (int)$_REQUEST['ID']; 
    		$page = Page::get()->byID($id);

		DisqusSync::sync($page->disqusIdentifier());
    		
        	$this->owner->response->addHeader('X-Status', sprintf('Synced successfuly'));
        	return;
    	}  
}

// EOF
