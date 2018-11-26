<?php

namespace Silverstripesk\Disqus;

use Page;
use SilverStripe\Admin\LeftAndMainExtension;

/**
 * Adds a function to LeftAndMain to sync disqus comments.
 *
 * @package silverstripe-disqus-module
 * @author Pavol Ondráš <admin_silverstripe.sk>
 * @notice SilverStripe.sk is not affiliated with the company SilverStripe Ltd.
 * @date April 2011
 */
class DisqusCMSActionExtension extends LeftAndMainExtension
{

    /**
     * @var array
     */
    private static $allowed_actions = [
        'syncAllCommentsAction',
    ];


    public function syncCommentsAction()
    {
        $id = (int)$_REQUEST['ID'];
        $page = Page::get()->byID($id);

        DisqusSync::sync($page->disqusIdentifier());

        $this->owner->response->addHeader('X-Status', sprintf('Synced successfuly'));
        return;
    }
}
