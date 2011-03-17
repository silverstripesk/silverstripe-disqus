<?php
class DisqusDecorator extends DataObjectDecorator {
		
	function onBeforeWrite() {
		parent::onBeforeWrite();
		if ($this->owner->ProvideComments) {
			$config = SiteConfig::current_site_config();
			$disqus = new DisqusAPI($config->disqus_secretkey);
			if ($this->owner->id) {
				//$disqus->threads->create(array("forum"=>$config->disqus_shortname,"identifier"=>$config->disqus_prefix."_".$this->owner->ID,"title"=>$this->owner->Title));
			} else {
				//$disqus->threads->create(array("forum"=>$config->disqus_shortname,"identifier"=>$config->disqus_prefix."_".$this->owner->ID,"title"=>$this->owner->Title));
			}
		}
	}
		
	function DisqusPageComments() {
		$dev = (Director::isLive()) ? NULL : "var disqus_developer = 1;";
		$config = SiteConfig::current_site_config();
		if ($config->disqus_shortname && $this->owner->ProvideComments) {
			$script = '
			    var disqus_shortname = \''.$config->disqus_shortname.'\';
				'.$dev.'
			    var disqus_identifier = \''.$config->disqus_prefix.'_'.$this->owner->ID.'\';
			    var disqus_url = \''.$this->owner->absoluteLink().'\';
			
			    (function() {
			        var dsq = document.createElement(\'script\'); dsq.type = \'text/javascript\'; dsq.async = true;
			        dsq.src = \'http://\' + disqus_shortname + \'.disqus.com/embed.js\';
			        (document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(dsq);
			    })();				
			';
			Requirements::customScript($script);
				
			$config->disqus_secretkey;
			$disqus = new DisqusAPI($config->disqus_secretkey);
				
			$output = new DataObjectSet();
			$thread = $disqus->threads->details(array("forum"=>$config->disqus_shortname,"thread"=>"ident:".$config->disqus_prefix."_".$this->owner->ID));	
			$comments = $disqus->threads->listPosts(array("forum"=>$config->disqus_shortname,"thread"=>$thread->id));
			//print_r($thread);
			//print_r($comments);
			
			if ($comments) {
				
				DB::query("UPDATE DisqusComment SET `isSynced` = 0 WHERE `thread` = '$thread->id'");	
				
				foreach ($comments as $comment) {
					$output->push(new ArrayData($comment));
					
					if ($c = DataObject::get_one('DisqusComment',"disqusId = '$comment->id'")) {
						echo "Comment here";
					} else {
						$c = new DisqusComment();
					}
					$c->isSynced = 1;
					$c->disqusId = $comment->id;
					$c->author_username = $comment->author->username;
					$c->forum = $comment->forum;
					$c->parent = $comment->parent;
					$c->thread = $comment->thread;
					$c->isApproved = $comment->isApproved;
					$c->isDeleted = $comment->isDeleted;
					$c->isDeleted = $comment->isDeleted;
					$c->isHighlighted = $comment->isHighlighted;
					$c->isSpam = $comment->isSpam;
					$c->createdAt = $comment->createdAt;
					$c->ipAddress = $comment->ipAddress;
					$c->message = $comment->message;
					$c->write();
					
				}
			}

			$templateData = array(
				'Results' => $output
			); 
				
			return $this->owner
				->customise($templateData)
				->renderWith(array('DisqusComments'));

		} else {
			// how to return default PageComments ?
		}
	}

	function disqusCountLink() {
		$config = SiteConfig::current_site_config();
		return '<a href="'.$this->owner->absoluteLink().'#disqus_thread" title="'.$this->owner->Title.'" data-disqus-identifier="'.$config->disqus_prefix.'_'.$this->owner->ID.'">Komentáre</a>';
	}
}

class DisqusSiteConfig extends DataObjectDecorator{
// add database fields
  function extraStatics() {
    return array(
      'db' => array(
        'disqus_shortname' => 'Varchar',
        'disqus_secretkey' => 'Varchar(64)',
        'disqus_prefix' => 'Varchar',
      )
    );
  }

  // Create CMS fields
  public function updateCMSFields(&$fields) {
    $fields->addFieldToTab("Root.Disqus",new TextField("disqus_shortname", "Disqus shortname"));
	$fields->addFieldToTab("Root.Disqus",new TextField("disqus_secretkey", "Disqus secret key"));
	$fields->addFieldToTab("Root.Disqus",new TextField("disqus_prefix", "Disqus prefix"));
  }
}

class DisqusCountExtension extends Extension {
			
	function onAfterInit() {
		$config = SiteConfig::current_site_config();
		if ($config->disqus_shortname) {
			$script = "
		    var disqus_shortname = '".$config->disqus_shortname."'; // required: replace example with your forum shortname
		
		    (function () {
		        var s = document.createElement('script'); s.async = true;
		        s.type = 'text/javascript';
		        s.src = 'http://' + disqus_shortname + '.disqus.com/count.js';
		        (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
		    }());
		    ";
			(Director::isLive()) ? Requirements::customScript($script) : false;
		}
	}
	
	
				
}

class DisqusComment extends DataObject {
	static $db = array(
		"isSynced" => "Boolean",
		"forum" => "Varchar",
		"disqusId" => "Int",
		"parent" => "Int",
		"thread" => "Int",
		"isApproved" => "Boolean",
		"isDeleted" => "Boolean",
		"isFlagged" => "Boolean",
		"isHighlighted" => "Boolean",
		"isSpam" => "Boolean",
		"author_username" => "Varchar",
		"createdAt" => "Datetime",
		"ipAddress" => "Varchar(32)",
		"message" => "HTMLText"
	);
}

class DisqusTask extends HourlyTask {
	function process() {
		
	}
}
//EOF
