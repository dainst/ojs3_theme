<?php
/**********************************************************************/
/* @file plugins/themes/idaitheme/IDaiTheme.inc.php *******************/
/**********************************************************************/
/* Copyright (c) 2019 German Archaeological Institute *****************/
/* Developed by Dennis Twardy (dennistwardy86@gmail.com) **************/
/* Distributed under the GNU GPL v3. For full terms see LICENSE. ******/
/**********************************************************************/
/* Based on PKP's code (see github.com/pkp/ojs) by John Willinsky and */
/* Simon Fraser University ********************************************/
/**********************************************************************/
/* @class IDaiTheme ***************************************************/
/* @ingroup plugins_themes_idaitheme **********************************/
/**********************************************************************/

import('lib.pkp.classes.plugins.ThemePlugin');
import('classes.file.PublicFileManager');

class IDaiTheme extends ThemePlugin {

  /**
	 * @copydoc ThemePlugin::isActive()
	 */
	public function isActive() {
		if (defined('SESSION_DISABLE_INIT')) return true;
		return parent::isActive();
  }

  public function init() {
    // Register options

     // Journal Description Position Option
    $this->addOption('jourdescription', 'radio', array(
        'label' => 'plugins.themes.idaitheme.option.iDaiTheme.jourdescriptionLabel',
        'description' => 'plugins.themes.idaitheme.option.iDaiTheme.jourdescriptionDescription',
        'options' => array(
            'above' => 'plugins.themes.idaitheme.option.iDaiTheme.jourdescriptionAbove',
            'below' => 'plugins.themes.idaitheme.option.iDaiTheme.jourdescriptionBelow',
            'off' => 'plugins.themes.idaitheme.option.iDaiTheme.jourdescriptionOff'
        )
    ));

    $request = Application::getRequest();

    // Load jQuery from a CDN or, if CDNs are disabled, from a local copy.
		$min = Config::getVar('general', 'enable_minified') ? '.min' : '';
		if (Config::getVar('general', 'enable_cdn')) {
			$jquery = '//ajax.googleapis.com/ajax/libs/jquery/' . CDN_JQUERY_VERSION . '/jquery' . $min . '.js';
			$jqueryUI = '//ajax.googleapis.com/ajax/libs/jqueryui/' . CDN_JQUERY_UI_VERSION . '/jquery-ui' . $min . '.js';
		} else {
			// Use OJS's built-in jQuery files
			$jquery = $request->getBaseUrl() . '/lib/pkp/lib/vendor/components/jquery/jquery' . $min . '.js';
			$jqueryUI = $request->getBaseUrl() . '/lib/pkp/lib/vendor/components/jqueryui/jquery-ui' . $min . '.js';
		}
		// Use an empty `baseUrl` argument to prevent the theme from looking for
		// the files within the theme directory
		$this->addScript('jQuery', $jquery, array('baseUrl' => ''));
		$this->addScript('jQueryUI', $jqueryUI, array('baseUrl' => ''));
    $this->addScript('jQueryTagIt', $request->getBaseUrl() . '/lib/pkp/js/lib/jquery/plugins/jquery.tag-it.js', array('baseUrl' => ''));

		// Load Bootsrap's dropdown
		/*$this->addScript('popper', 'js/lib/popper/popper.js');
		$this->addScript('bsUtil', 'js/lib/bootstrap/util.js');
		$this->addScript('bsDropdown', 'js/lib/bootstrap/dropdown.js');*/
		// Load custom JavaScript for this theme
    //$this->addScript('default', 'js/main.js');

		// Add navigation menu areas for this theme
		$this->addMenuArea(array('primary', 'user'));


    // Load primary stylesheet
    $this->addStyle('stylesheet', 'styles/index.less');

    // Variable to hold Less variables
    $additionalLessVariables = array();


    // READING LESS VARIABLES AND SETTING THEM COMES HERE
    // $additionalLessVariables[] = '@variableName' . $this->getOption('optionName') . ';'

    $pubFilesDir = '';
    $pubFilesUrl = '';
    $SysPubDir = '';
    $SysPubUrl = '';
    $baseUrl = '';

    $baseUrl = $request->getBaseUrl();
    $SysPubDir = Config::getVar('files', 'public_files_dir');
    $SysPubUrl = $baseUrl . '/' . $SysPubDir . '/';
    $pubFileManager = new PublicFileManager();

    //ebugToConsole("SysPubDir + SysPubURL");
    //debugToConsole($SysPubDir);
    //debugToConsole($SysPubUrl);


    if ($request) {
      $currentContext = $request->getContext();
      if (isset($currentContext)) {
        $primLocale = $currentContext->getPrimaryLocale();

        //debugToConsole("currentContext is not null");
        //debugToConsole($primLocale);

        $pubFilesUrl = $baseUrl . '/' . $pubFileManager->getJournalFilesPath($currentContext->getId());
        $pubFilesDir = BASE_SYS_DIR . '/' . $SysPubDir . '/' . 'journals/' . $currentContext->getId();

        //debugToConsole($pubFilesUrl);
        //debugToConsole($pubFilesDir);
      } else {
        //debugToConsole("currentContext is null, go for getSite");
        $currentContext = $request->getSite();
        if (isset($currentContext)) {
          //debugToConsole("currentContext from getSite is not null");
          $primLocale = $currentContext->getPrimaryLocale();
          //debugToConsole($primLocale);
          $pubFilesDir = BASE_SYS_DIR . '/' . $SysPubDir . '/' . 'site';
          //debugToConsole($pubFilesDir);
        }
      }
    }

    // Language Toggle
    $this->getContents($request);

    $additionalLessVariables[] = '@DAIpubfiles: \'' . $SysPubUrl .'\';';

    //debugToConsole("Checking now for Header");
    //debugToConsole($pubFilesDir . '/pageHeaderLogoImage_' . $primLocale . '.png');

    if (file_exists($pubFilesDir . '/pageHeaderLogoImage_' . $primLocale . '.png') ||
        file_exists($pubFilesDir . '/pageHeaderLogoImage_' . $primLocale . '.jpg') ||
        file_exists($pubFilesDir . '/pageHeaderLogoImage_' . $primLocale . '.webp') ||
        file_exists($pubFilesDir . '/pageHeaderLogoImage_' . $primLocale . '.svg')) {
      $additionalLessVariables[] = '@headerHasLogo: true;';
      //debugToConsole("Header image found");
    } else {
      $additionalLessVariables[] = '@headerHasLogo: false;';
      //debugToConsole("Header image not found");
    }

    $descriptionTextPosition = $this->getOption('jourdescription');
    if (empty($descriptionTextPosition) || $descriptionTextPosition === 'above') {
      $additionalLessVariables[] = '@DAIdescriptionTextState: \'above\';';
    } elseif ($descriptionTextPosition === 'below') {
      $additionalLessVariables[] = '@DAIdescriptionTextState: \'below\';';
    } else {
      $additionalLessVariables[] = '@DAIdescriptionTextState: \'off\';';
    }

    // ---
    // Pass additional LESS variables based on options
    // ---
    if (!empty($additionalLessVariables)) {
			$this->modifyStyle('stylesheet', array('addLessVariables' => join($additionalLessVariables)));
		}

	  // Load icon font FontAwesome - http://fontawesome.io/
	  if (Config::getVar('general', 'enable_cdn')) {
		  $url = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css';
	  } else {
		  $url = $request->getBaseUrl() . '/lib/pkp/styles/fontawesome/fontawesome.css';
	  }
	  $this->addStyle(
		  'fontAwesome',
		  $url,
		  array('baseUrl' => '')
	  );

  }
  function getContextSpecificPluginSettingsFile() {
    return $this->getPluginPath() . '/settings.xml';
  }
  function getInstallSitePluginSettingsFile() {
    return $this->getPluginPath() . '/settings.xml';
  }
  function getDisplayName() {
    return __('plugins.themes.idaitheme.name');
  }
  function getDescription() {
    return __('plugins.themes.idaitheme.description');
  }


  function getContents(/*$templateMgr,*/ $request = null) {
    $templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('isPostRequest', $request->isPost());
		if (!defined('SESSION_DISABLE_INIT')) {
			$journal = $request->getJournal();
			if (isset($journal)) {
				$locales = $journal->getSupportedLocaleNames();

			} else {
				$site = $request->getSite();
				$locales = $site->getSupportedLocaleNames();
			}
		} else {
			$locales = AppLocale::getAllLocales();
			$templateMgr->assign('languageToggleNoUser', true);
		}

		if (isset($locales) && count($locales) > 1) {
			$templateMgr->assign('enableLanguageToggle', true);
      $templateMgr->assign('languageToggleLocales', $locales);
		}

		return $templateMgr;
  }

  function debugToConsole($debugData) {
    $outputData = $debugData;
    if (is_array($outputData)) {
      $outputData = implode(',', $outputData);
    } else {
      echo "<script>console.log('DEBUG: " . $outputData . "');</script>";
    }
  }
}
?>
