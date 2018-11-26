<?php

namespace Silverstripesk\Disqus;

use Page;

/**
 * Disqus - hourly task
 *
 * @package silverstripe-disqus-module
 * @author Pavol Ondráš <admin_silverstripe.sk>
 * @notice SilverStripe.sk is not affiliated with the company SilverStripe Ltd.
 * @date April 2011
 */
//class DisqusTask extends HourlyTask
//{
//    public function process()
//    {
//        //$pages = DataObject::get("Page","provideComments = 1 AND status = 'Published'");
//        $pages = Page::get()->filter([
//            'ProvideComments' => '1'
//            //@todo remove this as param no longer returned on ss3.0 sitetree 'isPublished'=>'1'
//        ]);
//
//        if ($pages) {
//            echo "<ul>";
//            foreach ($pages as $page) {
//                // this doesnt sync anything, response from disqus server is empty
//                // dont know the reason for now
//                // setup hourly tasks as cron job: sapphire/cli-script.php /HourlyTask
//                // for testing purposes available via URL http://yoursite/HourlyTask (admin must be logged in)
//                echo "<li>";
//                echo $page->Title . ": ";
//                echo DisqusSync::sync($page->disqusIdentifier(), true);
//                echo "</li>";
//            }
//            echo "</ul>";
//        }
//    }
//}

// EOF
