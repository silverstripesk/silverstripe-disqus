<?php

/**
 * Adds a disqus comments to any Page
 *
 * @package silverstripe-disqus-module
 * @author Pavol Ondráš <admin_silverstripe.sk>
 * @notice SilverStripe.sk is not affiliated with the company SilverStripe Ltd.
 * @date April 2011
 */

class DisqusDecorator extends DataObjectDecorator {
	
	function extraStatics() {
		return array(
			'db' => array(
				'cutomDisqusIdentifier' => 'Varchar(32)'
				)
		);
	}
	
	function updateCMSFields(&$fields) {        
        $fields->addFieldToTab("Root.Behaviour", new TextField("cutomDisqusIdentifier", "cutomDisqusIdentifier"), "ProvideComments");
	}
	
	function updateCMSActions(&$actions) {
		// added button for syncing comments with Disqus server manualy...
		
		if ($this->owner->ProvideComments && SYNCDISQUS) {
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
			
	function DisqusPageComments() {		
		$dev = (Director::isLive()) ? NULL : "var disqus_developer = 1;";
		$loc = 	explode("_",$this->owner->Locale);	
		$disqusLocale = ($loc[1]) 
			? 'var disqus_config = function () { this.language = "'.$loc[0].'";	};' 
			: NULL;
		$config = SiteConfig::current_site_config();
		$ti = $this->disqusIdentifier();
		if ($config->disqus_shortname && $this->owner->ProvideComments) {
			$script = '
			    var disqus_shortname = \''.$config->disqus_shortname.'\';
				'.$dev.'
			    var disqus_identifier = \''.$ti.'\';
			    var disqus_url = \''.$this->owner->absoluteLink().'\';
				'.$disqusLocale.'
				
			    (function() {
			        var dsq = document.createElement(\'script\'); dsq.type = \'text/javascript\'; dsq.async = true;
			        dsq.src = \'http://\' + disqus_shortname + \'.disqus.com/embed.js\';
			        (document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(dsq);
			    })();				
			';
			Requirements::customScript($script);
			
			$templateData = array();
			
			if (SYNCDISQUS) {
				// Hide Local Comments -> we will use Disqus service
				$hideLocal = "
					function hideLocalComments() {
						document.getElementById('disqus_local').style.display = 'none';
					}
					window.onload = hideLocalComments;
				";
				Requirements::customScript($hideLocal);
				
				// Get comments 
				$results = DataObject::get('DisqusComment',"isSynced = 1 AND isApproved = 1 AND threadIdentifier = '$ti'");
				
				// Prepare data for template
				$templateData = array(
					'LocalComments' => $results
				); 
				
				// Sync comments
				$now = time();
				$synced = strtotime($this->owner->LastEdited);
				if (($now - $synced) > $config->disqus_synctime) {
					if ($config->disqus_syncinbg) {
						// background process
						// from here: http://stackoverflow.com/questions/1993036/run-function-in-background
					    // TODO: Windows check is not fully correct
					    // Debug
					    // echo "trying to sync in BG";
					    $cmd = "php " . Director::baseFolder() . DIRECTORY_SEPARATOR . "sapphire" . DIRECTORY_SEPARATOR . "cli-script.php /disqussync/sync_by_ident/" . $ti . "/";
					    // Debug
					    // echo $cmd;
					    if (substr(php_uname(), 0, 7) == "Windows") {
					        pclose(popen("start /B ". $cmd, "r"));
					    } else {
					        exec($cmd . " > /dev/null &");
					    }
					} else {
						$returnmessage = (Director::isDev()) ? 1 : 0;
						DisqusSync::sync($ti, $returnmessage);
					}
					// updates LastEdited data
					$this->owner->write(); // saves the record
				} else {
					// Debug
					// echo "not needed to sync";
				}

			}
				
			return $this->owner
				->customise($templateData)
				->renderWith(array('DisqusComments'));

		} 
	}

	function disqusCountLink() {
		$config = SiteConfig::current_site_config();
		return '<a href="'.$this->owner->absoluteLink().'#disqus_thread" title="'.$this->owner->Title.'" data-disqus-identifier="'.$this->disqusIdentifier().'">Komentáre</a>';
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

//EOF
