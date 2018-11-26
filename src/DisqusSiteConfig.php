<?php

namespace Silverstripesk\Disqus;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

/**
 * Adds a config values to SiteConfig area
 *
 * @package silverstripe-disqus-module
 * @author Pavol Ondráš <admin_silverstripe.sk>
 * @notice SilverStripe.sk is not affiliated with the company SilverStripe Ltd.
 * @date April 2011
 */
class DisqusSiteConfig extends DataExtension
{
    // add database fields
    private static $db = [
        'disqus_shortname' => 'Varchar',
        'disqus_secretkey' => 'Varchar(64)',
        'disqus_prefix'    => 'Varchar',
        'disqus_synctime'  => 'Int',
        'disqus_syncinbg'  => 'Boolean',
    ];

    // Create CMS fields
    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab(
            'Root.Disqus',
            [
                TextField::create('disqus_shortname', 'Disqus shortname'),
                TextField::create('disqus_secretkey', 'Disqus secret key'),
                TextField::create('disqus_prefix', 'Disqus prefix'),
                TextField::create('disqus_synctime', 'Disqus sync time (seconds)'),
                CheckboxField::create('disqus_syncinbg', 'Disqus - sync as background process?')
            ]
        );
    }

    /**
     * Adds a button the Site Config page of the CMS to sync all disqus comments.
     * @param FieldList $actions
     */
    public function updateCMSActions(FieldList $actions)
    {
        //@todo work out why this throws an error.
        //$actions->push( new InlineFormAction('syncAllCommentsAction', _t('Disqus.syncAllCommentsActionButton', 'Sync all Disqus comments') ) );
    }
}
