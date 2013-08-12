<?php

/**
 * Adds a config values to SiteConfig area
 *
 * @package silverstripe-disqus-module
 * @author Pavol Ondráš <admin_silverstripe.sk>
 * @notice SilverStripe.sk is not affiliated with the company SilverStripe Ltd.
 * @date April 2011
 */

class DisqusSiteConfig extends DataExtension{
// add database fields
	private static $db = array(
        'disqus_shortname' => 'Varchar',
        'disqus_secretkey' => 'Varchar(64)',
        'disqus_prefix' => 'Varchar',
        'disqus_synctime' => 'Int',
        'disqus_syncinbg' => 'Boolean'
    );

  // Create CMS fields
  public function updateCMSFields(FieldList $fields) {
    $fields->addFieldToTab("Root.Disqus",new TextField("disqus_shortname", "Disqus shortname"));
	$fields->addFieldToTab("Root.Disqus",new TextField("disqus_secretkey", "Disqus secret key"));
	$fields->addFieldToTab("Root.Disqus",new TextField("disqus_prefix", "Disqus prefix"));
	if (SYNCDISQUS) {
		$fields->addFieldToTab("Root.Disqus",new TextField("disqus_synctime", "Disqus sync time (seconds)"));
		$fields->addFieldToTab("Root.Disqus",new CheckboxField("disqus_syncinbg", "Disqus - sync as background process?"));
	} else {
		$fields->addFieldToTab("Root.Disqus", new LiteralField (
    		$name = "disqusnote",
    		$content = '<h3>Notice :: Syncing not available!</h3><p>Your php version is lower than needed! Please upgrade your servers php version if you need comments syncing (5.3+). Your version is:'.PHP_VERSION.'</p>'
    		)
		);
	}
  }
  
      /**
     * Adds a button the Site Config page of the CMS to sync all disqus comments.
     */
    public function updateCMSActions(FieldList $actions) {
    	if (SYNCDISQUS) {
    		//@todo work out why this throws an error.
	        //$actions->push( new InlineFormAction('syncAllCommentsAction', _t('Disqus.syncAllCommentsActionButton', 'Sync all Disqus comments') ) );
		}
    }
    
    
    
    
}