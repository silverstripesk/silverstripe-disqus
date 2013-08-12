<?php

/**
 * DisqusComment dataobject.
 *
 * @package silverstripe-disqus-module
 * @author Pavol Ondráš <admin_silverstripe.sk>
 * @notice SilverStripe.sk is not affiliated with the company SilverStripe Ltd.
 * @date April 2011
 */

class DisqusComment extends DataObject {
	private static $db = array(
		"isSynced" => "Boolean",
		"threadIdentifier" => "Varchar(32)",
		"forum" => "Varchar",
		"disqusId" => "Int",
		"parent" => "Int",
		"thread" => "Int",
		"isApproved" => "Boolean",
		"isDeleted" => "Boolean",
		"isFlagged" => "Boolean",
		"isHighlighted" => "Boolean",
		"isSpam" => "Boolean",
		"author_name" => "Varchar",
		"createdAt" => "Datetime",
		//"ipAddress" => "Varchar(32)", - API no longer returns IP address via publick secret key
		"message" => "HTMLText"
	);
	
}

// EOF
