<?php

namespace Silverstripesk\Disqus;

use DisqusAPI;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\ValidationException;
use SilverStripe\SiteConfig\SiteConfig;

/**
 * Common Disqus class. Usable from many other disqus classes
 *
 * @package silverstripe-disqus-module
 * @author Pavol Ondráš <admin_silverstripe.sk>
 * @notice SilverStripe.sk is not affiliated with the company SilverStripe Ltd.
 * @date April 2011
 */
class DisqusSync
{

    /**
     * @param $threadID
     * @param bool $returnmessage
     *
     * @return string
     * @throws ValidationException
     */
    public static function sync($threadID, $returnmessage = false)
    {
        $message = "no comments on disqus server";
        $comments = false;

        $config = SiteConfig::current_site_config();
        $config->disqus_secretkey;

        $disqus = new DisqusAPI($config->disqus_secretkey);

        try {
            $comments = $disqus->threads->listPosts([
                "forum" => $config->disqus_shortname,
                "thread" => "ident:" . $threadID
            ]);

        } catch (\Exception $e) {
            //user_error (  'Caught exception (probably cant get thread by ID, does it exists?): ' . $e->getMessage());
        }


        if ($comments) {
            $message = "There are some comments on disqus server";

            // Debug
            if ($returnmessage) {
                //print_r($comments);
            }

            DB::query("UPDATE DisqusComment SET `isSynced` = 0 WHERE `threadIdentifier` = '$threadID'");

            foreach ($comments as $comment) {
                //if ($c = DataObject::get_one('DisqusComment',"disqusId = '$comment->id'")) {
                if ($c = DisqusComment::get()->filter('disqusId', $comment->id)->First()) {
                    // Comment is already here, fine ;)
                    $message .= " | updating comment id " . $comment->id;
                } else {
                    // Comment is new, create it
                    $c = new DisqusComment();
                    $message .= " | adding comment id " . $comment->id;
                }

                $c->isSynced = 1;
                $c->threadIdentifier = $threadID;
                $c->disqusId = $comment->id;
                $c->author_name = $comment->author->name;
                $c->forum = $comment->forum;
                $c->parent = $comment->parent;
                $c->thread = $comment->thread;
                $c->isApproved = $comment->isApproved;
                $c->isDeleted = $comment->isDeleted;
                $c->isHighlighted = $comment->isHighlighted;
                $c->isSpam = $comment->isSpam;
                $c->createdAt = $comment->createdAt;
                //$c->ipAddress = $comment->ipAddress;
                $c->message = $comment->message;

                // finaly, save it to DB
                $c->write();

            }
        }

        if ($returnmessage) {
            return $message;
        }
    }
}
