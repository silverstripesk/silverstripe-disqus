<?php

/**
 * Adds a disqus comments to any Page
 *
 * @package silverstripe-disqus-module
 * @author Pavol Ondráš <admin_silverstripe.sk>
 * @notice SilverStripe.sk is not affiliated with the company SilverStripe Ltd.
 * @date April 2011
 */

class DisqusExtension extends DataExtension {
	
	private static $db = array(
		'customDisqusIdentifier' => 'Varchar(32)'
	);

    public function updateCMSFields(FieldList $fields) {
        $fields->removeByName("Comments");
    }
	
	public function updateSettingsFields(FieldList $fields) {
		$fields->addFieldToTab('Root.Settings', TextField::create("customDisqusIdentifier", _t("Disqus.CUSTOMDISQUSIDENTIFIER", "Custom Disqus identifier"))->setDescription("Current identifier: " . $this->owner->disqusIdentifier()));
	}
	
	function updateCMSActions(FieldList $actions) {
		// added button for syncing comments with Disqus server manualy...
		if ($this->owner->disqusEnabled()) {
			$Action = new FormAction(
	           "syncCommentsAction",
	           _t("Disqus.SYNCCOMMENTSBUTTON", "Sync Disqus Comments")
	        );
	    	$actions->push($Action);
		}
	}

    function disqusEnabled() {
		// comments module installed? Ask Comments module if enabled
        if ($this->owner->hasExtension('CommentsExtension')) {
			return ($this->owner->getCommentsEnabled() && SYNCDISQUS) ? true : false;
		}
		// no comments module - place disqus if template asks for
		return true;
    }
			
	function disqusIdentifier() {
		$config = SiteConfig::current_site_config();
		return ($this->owner->customDisqusIdentifier) ? $this->owner->customDisqusIdentifier :  $config->disqus_prefix."_".$this->owner->ID;
	}
	
	function disqusLocaleJsVar() {
		$l = (isset($this->owner->Locale)) ? $this->owner->Locale : i18n::get_locale();
		$loc = 	explode("_",$l);	
		return ($loc[1]) 
			? 'var disqus_config = function () { this.language = "'.$loc[0].'";	};' 
			: NULL;
	}

	function disqusDeveloperJsVar() {
			return (Director::isLive()) ? NULL : "var disqus_developer = 1;";
	}
			
	function DisqusPageComments() {
		// if the owner DataObject is Versioned, don't display DISQUS until the post is published
		// to avoid identifier / URL conflicts.
		if( $this->owner->hasExtension('Versioned') && Versioned::current_stage() == 'Stage') return '<p class="alert">'._t("Disqus.NOTLIVEALERT","Disqus comments are temporary OFF in Stage mode. Logout or turn in Live mode!").'</p>';

		$config = SiteConfig::current_site_config();
		$ti = $this->disqusIdentifier();
		if ($config->disqus_shortname && $this->owner->disqusEnabled()) {
			$script = '
			    var disqus_shortname = \''.$config->disqus_shortname.'\';
				'.$this->owner->disqusDeveloperJsVar().'
			    var disqus_identifier = \''.$ti.'\';
			    var disqus_url = \''.$this->owner->absoluteLink().'\';
				'.$this->owner->disqusLocaleJsVar().'
				
			    (function() {
			        var dsq = document.createElement(\'script\'); dsq.type = \'text/javascript\'; dsq.async = true;
			        dsq.src = \'https://\' + disqus_shortname + \'.disqus.com/embed.js\';
			        (document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(dsq);
			    })();				
			';
			Requirements::customScript($script);
			
			$templateData = array(
				'SyncDisqus' => SYNCDISQUS
			);
			
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
				//$results = DataObject::get('DisqusComment',"isSynced = 1 AND isApproved = 1 AND threadIdentifier = '$ti'");
				$results = DisqusComment::get()->filter(array(
					'isSynced'=>'1',
					'isApproved'=>'1',
					'threadIdentifier'=>$ti
				));
				
				// Prepare data for template
				$templateData['LocalComments'] = $results; 
				
				// Sync comments
				$now = time();
				$synced = strtotime($this->owner->LastEdited);
				if (($now - $synced) > $config->disqus_synctime) {
					if ($config->disqus_syncinbg) {
						// background process
						// from here: https://stackoverflow.com/questions/1993036/run-function-in-background
					    // TODO: Windows check is not fully correct
					    // Debug
					    // echo "trying to sync in BG";
					    $cmd = "php " . Director::baseFolder() . DIRECTORY_SEPARATOR . "framework" . DIRECTORY_SEPARATOR . "cli-script.php /disqussync/sync_by_ident/" . $ti . "/";
					    // Debug
					    //echo $cmd;
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

		if ($config->disqus_shortname) {
			DisqusCount::addCountJS($config->disqus_shortname, $this->owner->disqusLocaleJsVar().$this->owner->disqusDeveloperJsVar());
		}	
		
		return '<a href="'.$this->owner->absoluteLink().'#disqus_thread" title="'.$this->owner->Title.'" data-disqus-identifier="'.$this->disqusIdentifier().'">'._t("Disqus.COMMENTS","Comments").'</a>';
	}
}

//EOF
