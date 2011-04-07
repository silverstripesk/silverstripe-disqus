<?php
class DisqusDecorator extends DataObjectDecorator {
	
	function extraStatics() {
		return array(
			'db' => array('cutomDisqusIdentifier' => 'Varchar(32)')
		);
	}
	
	function updateCMSFields(&$fields) {        
        $fields->addFieldToTab("Root.Behaviour", new TextField("cutomDisqusIdentifier", "cutomDisqusIdentifier"), "ProvideComments");
	}
	
	function updateCMSActions(&$actions) {
		// added button for syncing comments with Disqus server manualy...
		
		if ($this->owner->ProvideComments) {
			$Action = new FormAction(
	           "syncCommentsAction",
	           _t("Disqus.SYNCCOMMENTSBUTTON", "Sync Disqus Comments")
	        );
	    	$actions->push($Action);
		}
	}
			
	function disqusIdentifier() {
		$config = SiteConfig::current_site_config();
		return ($this->owner->customDisqusIdentifier) ? $this->owner->customDisqusIdentifier :  $config->disqus_prefix."_".$this->owner->ID;
	}
	
	/* Dont use it. There is special button for manual syncing + cron job...	
	function onBeforeWrite() {
		parent::onBeforeWrite();
		if ($this->owner->ProvideComments) {
			$this->syncWithDisqusServer();
		}
	}
	 * */
		
	function DisqusPageComments() {
		$dev = (Director::isLive()) ? NULL : "var disqus_developer = 1;";
		$config = SiteConfig::current_site_config();
		if ($config->disqus_shortname && $this->owner->ProvideComments) {
			$script = '
			    var disqus_shortname = \''.$config->disqus_shortname.'\';
				'.$dev.'
			    var disqus_identifier = \''.$this->disqusIdentifier().'\';
			    var disqus_url = \''.$this->owner->absoluteLink().'\';
			
			    (function() {
			        var dsq = document.createElement(\'script\'); dsq.type = \'text/javascript\'; dsq.async = true;
			        dsq.src = \'http://\' + disqus_shortname + \'.disqus.com/embed.js\';
			        (document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(dsq);
			    })();				
			';
			Requirements::customScript($script);
			
			$ti = $this->disqusIdentifier();
			$results = DataObject::get('DisqusComment',"isSynced = 1 AND isApproved = 1 AND threadIdentifier = '$ti'");
			
			$templateData = array(
				'LocalComments' => $results
			); 
				
			return $this->owner
				->customise($templateData)
				->renderWith(array('DisqusComments'));

		} else {
			// how to return default PageComments ?
		}
	}

	function syncWithDisqusServer() {
				
			$config = SiteConfig::current_site_config();
			$config->disqus_secretkey;
			$disqus = new DisqusAPI($config->disqus_secretkey);
							
			$comments = false;
			
			try {
				$comments = $disqus->threads->listPosts(array("forum"=>$config->disqus_shortname,"thread"=>"ident:".$this->owner->disqusIdentifier()));
			} catch (Exception $e) {
			    user_error (  'Caught exception (probably cant get thread by ID, does it exists?): ' . $e->getMessage());
			}
						
			if ($comments) {
								
				// Debug
				//print_r($comments);
				
				$ti = $this->disqusIdentifier();
				
				DB::query("UPDATE DisqusComment SET `isSynced` = 0 WHERE `threadIdentifier` = '$ti'");
				
				foreach ($comments as $comment) {
					
					if ($c = DataObject::get_one('DisqusComment',"disqusId = '$comment->id'")) {
						// Comment is already here, fine ;)
					} else {
						// Comment is new, create it
						$c = new DisqusComment();
					}
					$c->isSynced = 1;
					$c->threadIdentifier = $this->disqusIdentifier();
					$c->disqusId = $comment->id;
					$c->author_username = $comment->author->username;
					$c->forum = $comment->forum;
					$c->parent = $comment->parent;
					$c->thread = $comment->thread;
					$c->isApproved = $comment->isApproved;
					$c->isDeleted = $comment->isDeleted;
					$c->isHighlighted = $comment->isHighlighted;
					$c->isSpam = $comment->isSpam;
					$c->createdAt = $comment->createdAt;
					$c->ipAddress = $comment->ipAddress;
					$c->message = $comment->message;
					
					// finaly, save it to DB
					$c->write();
					
				}
			}
				
	}

	function disqusCountLink() {
		$config = SiteConfig::current_site_config();
		return '<a href="'.$this->owner->absoluteLink().'#disqus_thread" title="'.$this->owner->Title.'" data-disqus-identifier="'.$this->disqusIdentifier().'">Komentáre</a>';
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
		"author_username" => "Varchar",
		"createdAt" => "Datetime",
		"ipAddress" => "Varchar(32)",
		"message" => "HTMLText"
	);
	
}

class DisqusCMSActionDecorator extends LeftAndMainDecorator {
     
    function syncCommentsAction() {	
    	    	
    	$id = (int)$_REQUEST['ID']; 
    	$page = DataObject::get_by_id("Page",$id);
    	$page->syncWithDisqusServer();
    		
        FormResponse::status_message(sprintf('All good!'),'good');
        return FormResponse::respond();
    }  
}

class DisqusTask extends HourlyTask {
	function process() {
		
	}
}
//EOF
