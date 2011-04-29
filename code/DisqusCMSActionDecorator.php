<?php

/**
 * Adds a function to LeftAndMain to sync disqus comments.
 *
 * @package silverstripe-disqus-module
 * @author Pavol Ondráš <admin_silverstripe.sk>
 * @notice SilverStripe.sk is not affiliated with the company SilverStripe Ltd.
 * @date April 2011
 */

class DisqusCMSActionDecorator extends LeftAndMainDecorator {
	
	public static $allowed_actions = array(
        'syncAllCommentsAction'
    );
     
    function syncCommentsAction() {	
    	    	
    	$id = (int)$_REQUEST['ID']; 
    	$page = DataObject::get_by_id("Page",$id);

		DisqusSync::sync($page->disqusIdentifier());
    		
        FormResponse::status_message(sprintf('Synced successfuly'),'good');
        return FormResponse::respond();
    }  
	
	function syncAllCommentsAction() {	
    	    	
    	$pages = DataObject::get("Page","provideComments = 1 AND status = 'Published'");
		if ($pages) {
			foreach ($pages as $page) {
				// TODO: this doesnt work too (sameas HourlyTask). What's the reason?
 				DisqusSync::sync($page->disqusIdentifier());
			}
		}
    		
        FormResponse::status_message(sprintf('Synced successfuly'),'good');
        return FormResponse::respond();
    }  
}

// EOF
