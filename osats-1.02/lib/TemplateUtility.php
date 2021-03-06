<?php
/**
 * OSATS
 */

include_once('Candidates.php');
include_once('DateUtility.php');
include_once('SystemInfo.php');
include_once('i18n.php');

/**
 *	Template Utility Library
 *	@package    OSATS
 *	@subpackage Library
 */
class TemplateUtility
{
    /* Prevent this class from being instantiated. */
    private function __construct() {}
    private function __clone() {}


    /**
     * Prints the template header HTML for a non-modal window.
     *
     * @param string page title
     * @param array JavaScript / CSS files to load
     * @return void
     */
    public static function printHeader($pageTitle, $headIncludes = array())
    {
        self::_printCommonHeader($pageTitle, $headIncludes);
        //below changes the main background color and body style
        echo '<body style="background: #fff; width: 955px;">', "\n";
        self::_printQuickActionMenuHolder();
        self::printPopupContainer();
        
    }

    /**
     * Prints the template header HTML for a modal window.
     *
     * @param string page title
     * @param array JavaScript / CSS files to load
     * @return void
     */
    public static function printModalHeader($pageTitle, $headIncludes = array(), $title = '')
    {
        self::_printCommonHeader($pageTitle, $headIncludes);
        echo '<body style="background: #eee;">', "\n";
        if ($title != '')
        {
            $title = str_replace('\'', '\\\'', $title);
            echo '<script type="text/javascript">parentSetPopTitle(\''.$title.'\');</script>';
        }
        self::_printQuickActionMenuHolder();
    }

    /**
     * Prints logo and "top-right" header HTML.
     *
     * @return void
     */
     /*remove this function when done with new functions in osatutil called tabsattop and tabsatbottom - Jamin */
    public static function printHeaderBlock($showTopRight = true)
    {
        $username     = $_SESSION['OSATS']->getUsername();
        $siteName     = $_SESSION['OSATS']->getSiteName();
        $fullName     = $_SESSION['OSATS']->getFullName();
        $indexName    = osatutil::getIndexName();

        echo '<div id="headerBlock">', "\n";

        /* OSATS Logo - uncomment if you want the logo on top.. I moved it to the bottom. Jamin*/
        //echo '<table cellspacing="0" cellpadding="0" style="margin: 0px; padding: 0px; float: left;">', "\n";
        //echo '<tr>', "\n";
        //echo '<td rowspan="2"><img src="images/applicationLogo.jpg" border="0" alt="OSATS Open Source Applicant Tracking System" /></td>', "\n";
        //echo '</tr>', "\n";
        //echo '</table>', "\n";

        if (!eval(Hooks::get('TEMPLATE_LIVE_CHAT'))) return;

        if (!eval(Hooks::get('TEMPLATE_LOGIN_INFO_PRE_TOP_RIGHT'))) return;

        if ($showTopRight)
        {
            // FIXME: Use common functions.
            // FIXME: Isn't the UNIX-name stuff ASP specific? Hook?
            if (strpos($username, '@'.$_SESSION['OSATS']->getSiteID()) !== false &&
                substr($username, strpos($username, '@'.$_SESSION['OSATS']->getSiteID())) ==
                '@'.$_SESSION['OSATS']->getSiteID() )
            {
               $username = str_replace('@'.$_SESSION['OSATS']->getSiteID(), '', $username);
            }

            if (!eval(Hooks::get('TEMPLATE_LOGIN_INFO_TOP_RIGHT_1'))) return;

            /* Top Right Corner */
            echo '<div id="topRight">';

            // Begin top-right action block
            echo '<div>';
            if (!eval(Hooks::get('TEMPLATE_LOGIN_INFO_TOP_RIGHT_UPGRADE'))) return;
            echo '<a href="', $indexName, '?m=logout">' . __('Logout') . ' <img src="images/lock.png" alt="" class="ico" /></a>';
            echo '</div>';
            // End top-right action block

            if (!eval(Hooks::get('TEMPLATE_LOGIN_INFO_EXTENDED_SITE_NAME'))) return;

            
                        
   
            
            echo '<span>'.__('You are Currently Logged on as:').' <span style="font-weight:bold;">', $fullName, '</span></span><br />';

        	
            /* Disabled notice */
            if (!$_SESSION['OSATS']->accountActive())
            {
                echo '<span style="font-weight:bold;">'.__('Account Inactive').'</span><br />', "\n";
            }
            else if ($_SESSION['OSATS']->getAccessLevel() == ACCESS_LEVEL_READ)
            {
                echo '<span>'.__('Read Only Access').'</span><br />';
            }
            else
            {
                if (!eval(Hooks::get('TEMPLATE_LOGIN_INFO_TOP_RIGHT_2_ELSE'))) return;
            }
            echo '</div>';
        }
        echo '</div>';
    }
    

    /**
     * Prints the time zone selection dropdown list.
     *
     * @param integer ID and name attributes of the time zone select input
     * @param string style attribute of the time zone select input
     * @param string class attribute of the time zone select input
     * @param integer selected GMT offset
     * @return void
     */
    public static function printTimeZoneSelect($selectID, $selectStyle,
        $selectClass, $selectedTimeZone)
    {
        echo '<select id="', $selectID, '" name="', $selectID, '"';

        if (!empty($selectClass))
        {
            echo ' class="', $selectClass, '"';
        }

        if (!empty($selectStyle))
        {
            echo ' style="', $selectStyle, '"';
        }

        echo '>';

        $currentTimeZone = '';

        foreach ($GLOBALS['timeZones'] as $timeZone)
        {
            echo '<option value="', $timeZone[0], '"';

            if ($timeZone[0] !== $currentTimeZone)
            {
                $currentTimeZone = $timeZone[0];
                if ($timeZone[0] == $selectedTimeZone)
                {
                    echo ' selected="selected"';
                }
            }

            echo '>', htmlspecialchars($timeZone[1]), '</option>';
        }

        echo '</select>';
    }

    /**
     * Prints the Quick Search box and MRU list.
     *
     * @return void
     * IF YOU DONT WANT THIS... REMARK IT OUT as below.
     * IF YOU DO WANT THIS... Take off the REMARKS from the beginning and end of the function
     * the rem starts with  /* and ends with below  */
	public static function printQuickSearch($wildCardString = '')
    {

        $MRU = $_SESSION['OSATS']->getMRU()->getFormatted();
        $indexName = osatutil::getIndexName();
		$fullName     = $_SESSION['OSATS']->getFullName();
		
		//need to change the div id to something else. Jamin
        echo '<div id="LeftofsearchP">', "\n";
        echo '<div id="Leftofsearch">', "\n";
        // get logged in user and logout option
        echo '<a href="', $indexName, '?m=logout"><img src="images/lock.png" alt="" class="ico" /> LOGOUT </a>';
        //if (!eval(Hooks::get('TEMPLATE_LOGIN_INFO_EXTENDED_SITE_NAME'))) return;
        echo '<span>     Hi there <span style="font-weight:bold;">', $fullName, '!</span></span><br />';

            
            
		echo '</div>', "\n\n";

        /* Quick Search */
        echo '<form id="quickSearchForm" action="', $indexName,
             '" method="get" onsubmit="return checkQuickSearchForm(document.quickSearchForm);">', "\n";
        echo '<div id="quickSearchBlock">', "\n";

        //FIXME:  Abstract into a hook.
        if ($_SESSION['OSATS']->hasUserCategory('msa'))
        {
            echo '<input type="hidden" name="m" value="asp" />', "\n";
            echo '<input type="hidden" name="a" value="aspSearch" />', "\n";
            echo '<span class="quickSearchLabel" id="quickSearchLabel">'.__('Search Site').':</span>&nbsp;', "\n";
        }
        else
        {
            echo '<input type="hidden" name="m" value="home" />', "\n";
            echo '<input type="hidden" name="a" value="quickSearch" />', "\n";
            echo '<span class="quickSearchLabel" id="quickSearchLabel">'.__('Search Site').':</span>&nbsp;', "\n";
        }

        echo '<input name="quickSearchFor" id="quickSearchFor" class="quickSearchBox" value="',
             $wildCardString, '" />&nbsp;', "\n";
        echo '<input type="submit" name="quickSearch" class="button" value="'.__('Go').'" />&nbsp;', "\n";
        echo '</div>', "\n";
        echo '</form>', "\n";
        echo '</div>', "\n";
    }

    /**
     * Prints Advanced Search for search pages.
     *
     * @return void
     */
    public static function printAdvancedSearch($considerFields)
    {
        echo '<input type="button" class="button" name="advancedSearch" id="advancedSearch" value="'.__('Advanced').'"',
             ' onclick="document.getElementById(\'advancedSearchField\').style.display=\'block\'; ',
             'advancedSearchReset();" style="display:none;">', "\n";
        echo '<input type="hidden" id="advancedSearchParser" name="advancedSearchParser" value="">', "\n";

        if (isset($_GET['advancedSearchOn']) && isset($_GET['advancedSearchParser']) &&
            $_GET['advancedSearchOn'] != 0 && !empty($_GET['advancedSearchParser']))
        {
            /* Output an active advanced search. */
            echo '<input type="hidden" id="advancedSearchOn" name="advancedSearchOn" value="',
                  $_GET['advancedSearchOn'], '" />', "\n";
            echo '<span id="advancedSearchField" style="display:block;">', "\n";
            echo '</span>', "\n";

            echo '<script type="text/javascript">', "\n";
            echo '    data = [];', "\n";
            echo '    nodes = [];', "\n";

            $stuff = explode('{[+', $_GET['advancedSearchParser']);
            for ($i = 0; $i < sizeof($stuff); $i++)
            {
                $innerStuff = explode('[|]', $stuff[$i]);

                echo '    data[',  $i, '] = "', $innerStuff[0], '";', "\n";
                echo '    nodes[', $i, '] = "', $innerStuff[1], '";', "\n";
            }
            echo '    data[', sizeof($stuff), '] = "";', "\n";
            echo '    advancedSearchDraw();', "\n";
            echo '</script>', "\n";
        }
        else
        {
            /* Output basic framework to start an advanced search; no search visible. */
            echo '<input type="hidden" id="advancedSearchOn" name="advancedSearchOn" value="0">', "\n";
            echo '<span id="advancedSearchField" style="display:none;">', "\n";
            echo '</span>', "\n";
        }

        /* Tell the script what fields have access to advanced search. */
        if (!empty($considerFields))
        {
            $considerFieldsArray = explode(',', $considerFields);

            echo '<script type="text/javascript">';
            echo '    advancedValidFields = ["', implode('","', $considerFieldsArray), '"];';
            echo '    advancedSearchConsider();';
            echo '</script>';
        }
    }

    /**
     * Prints the HTML for a saved search from a response array.
     *
     * @param response array
     * @return void
     */
    public static function printSavedSearch($savedSearchRS)
    {
        $savedSearchRecent = array();
        $savedSearchSaved = array();

        foreach ($savedSearchRS as $savedSearchRow)
        {
            if ($savedSearchRow['isCustom'] == 1)
            {
                $savedSearchSaved[] = $savedSearchRow;
            }
            else
            {
                $savedSearchRecent[] = $savedSearchRow;
            }
        }

        $currentUrlGET = array();
        foreach ($_GET as $key => $value)
        {
            if ($key != 'savedSearchID')
            {
                $currentUrlGET[] = $key . '=' . urlencode($value);
            }
        }

        $currentUrlGETString = urlencode(implode('&', $currentUrlGET));
        $indexName = osatutil::getIndexName();

        echo '<div class="recentSearchResults">';
        echo '<table style="vertical-align: top; border-collapse: collapse;"><tr style="vertical-align: top;"><td>';

        echo __('Recent Searches') . '&nbsp;&nbsp;';
        echo '<img title="To save a recent search, press the + button below."',
             ' src="images/information.gif" alt="" width="16" height="16" />';

        echo '<div id="searchRecent" class="recentSearchResultsHidden">';

        /* Recent Search Results */
        if (count($savedSearchRecent) == 0)
        {
           echo '('.__('_None').')';
        }
        else
        {
            foreach ($savedSearchRecent as $savedSearchRow)
            {
                if (strlen($savedSearchRow['dataItemText']) > 35)
                {
                    $savedSearchRow['dataItemText'] = substr($savedSearchRow['dataItemText'], 0, 35) . '...';
                }

                if (count($savedSearchSaved) >= RECENT_SEARCH_MAX_ITEMS)
                {
                    echo '<a href="javascript:void(0);" onclick="alert(\'The maximum amount of saved searches is ',
                         RECENT_SEARCH_MAX_ITEMS, '. To save this search, delete another saved search.\');">';
                }
                else
                {
                    echo '<a href="', $indexName, '?m=home&amp;a=addSavedSearch&amp;searchID=',
                         $savedSearchRow['searchID'], '&amp;currentURL=', $currentUrlGETString, '">';
                }

                echo '<img src="images/actions/add_small.gif" alt="" style="border: none;" title="Save This Search" /></a>&nbsp;', "\n";

                $escapedURL  = htmlspecialchars($savedSearchRow['URL']);

                /* Remove leading slashes. */
                while (substr($escapedURL, 0, 1) == '/')
                {
                    $escapedURL = substr($escapedURL, 1);
                }
                $escapedURL = '/'.$escapedURL;


                $escapedText = htmlspecialchars($savedSearchRow['dataItemText']);

                echo '<a href="', $escapedURL,
                     '" onclick="gotoSearch(\'', $escapedText, "', '", $escapedURL, '\');"',
                     ' onmouseover="this.className += \'recentSearchResultsHighlight\';" ',
                     ' onmouseout="this.className = this.className.replace(\'recentSearchResultsHighlight\', \'\');">',
                     $escapedText, '</a>', '<br />', "\n";
            }
        }

        echo '</div>';
        echo '</td><td>&nbsp;</td><td>';

        echo __('Saved Searches') . '&nbsp;&nbsp;';
        echo '<img title="'.__('To delete a recent search, press the - button.').'"',
             ' src="images/information.gif" alt="" width="16" height="16" />';

        echo '<div id="searchSaved" class="savedSearchResultsHidden">';

        /* Saved Search Results */
        if (count($savedSearchSaved) == 0)
        {
           echo '('.__('_None').')';
        }
        else
        {
            foreach ($savedSearchSaved as $savedSearchRow)
            {
                if (strlen($savedSearchRow['dataItemText']) > 35)
                {
                    $savedSearchRow['dataItemText'] = substr($savedSearchRow['dataItemText'], 0, 35) . '...';
                }

                $escapedURL  = htmlspecialchars($savedSearchRow['URL']);
                $escapedText = htmlspecialchars($savedSearchRow['dataItemText']);

                /* Remove leading slashes. */
                while (substr($escapedURL, 0, 1) == '/')
                {
                    $escapedURL = substr($escapedURL, 1);
                }
                $escapedURL = '/'.$escapedURL;

                echo '<a href="', $indexName, '?m=home&amp;a=deleteSavedSearch&amp;searchID=',
                     $savedSearchRow['searchID'], '&currentURL=', $currentUrlGETString, '">',
                     '<img src="images/actions/delete_small.gif" style="border: none;" title="Delete This Search" /></a>&nbsp;';

                echo '<a href="', $escapedURL, '&amp;savedSearchID=', $savedSearchRow['searchID'],
                     '" onclick="gotoSearch(\'', $escapedText, "', '", $escapedURL,
                     '&amp;savedSearchID=', $savedSearchRow['searchID'], '\');"',
                     ' onmouseover="this.className += \'recentSearchResultsHighlight\';" ',
                     ' onmouseout="this.className = this.className.replace(\'recentSearchResultsHighlight\', \'\');">',
                     $escapedText,'</a><br />', "\n";
            }
        }

        echo '</div>', "\n";

        echo '</td></tr></table></div>';
        echo '<br /><br />';
        echo '<script type="text/javascript">syncRowHeightsSaved();</script>';
    }

    /**
     * Outputs a tester which checks if cookies are enabled in the user's
     * browser.
     *
     * @return void
     */
    public static function printCookieTester()
    {
        $indexName = osatutil::getIndexName();

        echo '<script type="text/javascript">
            if (navigator.cookieEnabled)
            {
                var cookieEnabled = true;
            }
            else
            {
                var cookieEnabled = false;
            }

            if (typeof(navigator.cookieEnabled) == "undefined" && !cookieEnabled)
            {
                document.cookie = \'testcookie\';
                cookieEnabled = (document.cookie.indexOf(\'testcookie\') != -1) ? true : false;
            }

            if (!cookieEnabled)
            {
                showPopWin(\'' . $indexName . '?m=login&amp;a=noCookiesModal\', 400, 225, null);
            }
            </script>';
    }

    /**
     * Outputs a popup container for use with JavaScript based popups like
     * ListEditor.js and other subModal.js-based dialogs.
     *
     * @return void
     */
    public static function printPopupContainer()
    {
        echo '<div id="popupMask">&nbsp;</div><div id="popupContainer">',
             '<div id="popupInner"><div id="popupTitleBar">',
             '<div id="popupTitle"></div><div id="popupControls">',
             '<img src="js/submodal/close.gif" alt="X" width="16" height="16"',
             ' onclick="hidePopWin(false);" /></div></div>';

        echo '<div style="width: 100%; height: 100%; background-color:',
             ' transparent; display: none;" id="popupFrameDiv"></div>';

        echo '<iframe src="js/submodal/loading.html" style="width: 100%; height: 100%;',
             ' background-color: transparent; display: none;" scrolling="auto"',
             ' frameborder="0" allowtransparency="true" id="popupFrameIFrame"',
             ' width="100%" height="100%"></iframe>';

        echo '</div></div>';
    }

    /**
     * Prints the module tabs.
     *
     * @param UserInterface active module interface
     * @param string active subtab name
     * @param string module name to forcibly highlight
     * @return void
     */
    public static function printTabs($active, $subActive = '', $forceHighlight = '')
    {
        echo '<div id="header">', "\n";
        echo '<ul id="primary">', "\n";

        $indexName = osatutil::getIndexName();
		$modules = ModuleUtility::getModules(); 	
        foreach ($modules as $moduleName => $parameters)
        {
            // get the name of the tab from the array which was saved in the db under moduleinfo - Jamin
			$tabText = $parameters[1];
			$tabVisible = $parameters[5];
		
           //print_r($tabVisible . "<br />");
           // Don't display a module's tab if $tabText is empty.
			if (empty($tabText))
            {
                continue;
            }
            
            /* If name = Companies and HR mode is on, change tab name to My Company. */
            if ($_SESSION['OSATS']->isHrMode() && $tabText == __('Companies'))
            {
                $tabText = 'My Company';
            }

            /* we looked at the db and found parameters[5]. if its a 0 then tab is not visible. Jamin */
            $displayTab = true;
			if ($tabVisible == '0')
			{
				continue;
			}
			
            /* Inactive Tab? need to clean this up. Jamin*/
            
            if ($active === null || $moduleName != $active->getModuleName())
            {
                $className = $moduleName == $forceHighlight ? ' class="active"' : '';

                $alPosition = strpos($tabText, "*al=");
                if ($alPosition === false)
                {
                    echo '<li'.$className.'><a href="', $indexName,
                         '?m=', $moduleName, '">', $tabText, '</a></li>', "\n";
                }
                else
                {
                     $al = substr($tabText, $alPosition + 4);
                     if ($_SESSION['OSATS']->getAccessLevel() >= $al ||
                         $_SESSION['OSATS']->isDemo())
                     {
                        echo '<li'.$className.'><a href="', $indexName, '?m=', $moduleName, '">',
                             substr($tabText, 0, $alPosition), '</a></li>', "\n";
                    }
                }

                continue;
            }

            $alPosition = strpos($tabText, "*al=");
            if ($alPosition !== false)
            {
                $tabText = substr($tabText, 0, $alPosition);
            }

            /* Start the <li> block for the active tab. The secondary <ul>
             * for subtabs MUST be contained within this block. It is
             * closed after subtabs are printed. You can adjust the conversion to uppercase if you want
			 * I set it to make the active tab upper case to stand out more. */
            echo '<li class="active"><a href="'.$indexName.'?m='.$moduleName.'">'.strtoupper($tabText).'</a>';

            $subTabs = $active->getSubTabs($modules);
            if ($subTabs)
            {
                echo '<ul id="secondary">';

                foreach ($subTabs as $subTabText => $link) {

                  $style = $subTabText == $subActive ? ' class="active"' : '';

                  /* Check HR mode for displaying tab. */
                  $hrmodePosition = strpos($link, "*hrmode=");
                  if ($hrmodePosition !== false) {
                    /* Access level restricted subtab. */
                    $hrmode = substr($link, $hrmodePosition + 8);
                    if ((!$_SESSION['OSATS']->isHrMode() && $hrmode == 0) ||
                        ($_SESSION['OSATS']->isHrMode() && $hrmode == 1)) {
                      $link =  substr($link, 0, $hrmodePosition);
                    } else {
                      $link = '';
                    }
                  }

                  /* Check access level for displaying tab. */
                  $alPosition = strpos($link, "*al=");
                  if ($alPosition !== false) {
                    /* Access level restricted subtab. */
                    $al = substr($link, $alPosition + 4);
                    if ($_SESSION['OSATS']->getAccessLevel() >= $al) {
                      $link =  substr($link, 0, $alPosition);
                      
                    } else {
                    
                      $link = '';
                    }
                  }

                  $jsPosition = strpos($link, "*js=");
                  if ($jsPosition !== false) {
                    /* Javascript subtab. */
                    echo '<li><a href="', substr($link, 0, $jsPosition), '" onclick="',
                         substr($link, $jsPosition + 4), '"'.$style.'>', $subTabText, '</a></li>', "\n";
                  }

                  /* A few subtabs have special logic to decide if they display or not.
                  FIXME:  Put the logic for these somewhere else.  Perhaps the definitions of the subtabs
                             themselves should have an eval()uatable rule?
                  */
                  else if (strpos($link, 'a=internalPostings') !== false) {
                    /* Default company subtab. */
                    include_once('./lib/Companies.php');

                    $companies = new Companies($_SESSION['OSATS']->getSiteID());
                    $defaultCompanyID = $companies->getDefaultCompany();
                    if ($defaultCompanyID !== false) {
                      echo '<li'.$style.'><a href="', $link, '">', $subTabText, '</a></li>', "\n";
                    }
                  }
                
                  else if (strpos($link, 'a=customizeEEOReport') !== false) {
                    /* EEO Report subtab.  Shouldn't be visible if EEO tracking is disabled. */
                    $EEOSettings = new EEOSettings($_SESSION['OSATS']->getSiteID());
                    $EEOSettingsRS = $EEOSettings->getAll();

                    if ($EEOSettingsRS['enabled'] == 1) {
                      echo '<li'.$style.'><a href="', $link, '">', $subTabText, '</a></li>', "\n";
                    }
                  }

                  /* Tab is ok to draw. */
                  else if ($link != '') {
                      /* Normal subtab. */
                      echo '<li'.$style.'><a href="', $link, '">', $subTabText, '</a></li>', "\n";
                  }
                }

                if (!eval(Hooks::get('TEMPLATE_UTILITY_DRAW_SUBTABS'))) return;

                echo '</ul>';
            }

            echo '</li>';
        }
        echo '</ul>', "\n";
        echo '</div>', "\n";
    }
        

    /**
     * Prints footer HTML for non-report pages.
     *
     * @return void
     */
    public static function printFooter()
    {
    	//the constants.php has the companyname_html that needs to be defined to show the name in the footer. Jamin
        echo '<div class="footerBlock"><span id="footerCopyright">', COMPANYNAME_HTML, '</span><br /><table cellpadding="0" style="margin: 0px; padding: 0px; float: left;" width="100%"></div></body></html>',"\n"; 
		//echo in logo here.
		echo '<td rowspan="2" width="303"><img src="images/applicationLogo.jpg" border="0" alt="OSATS Open Source Applicant Tracking System" /></td>',"\n";
        
    }

    /**
     * Prints footer HTML for report pages.
     *
     * @return void
     */
    public static function printReportFooter()
    {
        $date  = date('l, F jS, Y \a\t h:i:s A T');
        echo '<div class="footerBlock"><p id="footerText">Report generated on ', $date, '.<br />', "\n";
        echo '<span id="footerCopyright">', COMPANYNAME_HTML, '</span></div></body></html>', "\n";
    }

    /**
     * Prints HTML for pipeline candidate-joborder match rating stars.
     *
     * @param integer rating (0-5)
     * @param integer candidate-joborder ID
     * @param string PHP session cookie
     * @return string HTML
     */
    public static function getRatingObject($rating, $candidateJobOrderID, $sessionCookie)
    {
        static $firstCall = true;

        /* These usually come straight from the database; make sure it's an
         * integer.
         */
        $rating = (int) $rating;

        $ratings = self::_getRatingImages();
        $indexName = osatutil::getIndexName();

        if ($_SESSION['OSATS']->getAccessLevel() < ACCESS_LEVEL_EDIT)
        {
            $HTML = '<img src="' . $ratings[$rating] . '" style="border: none;" alt="" id="moImage' . $candidateJobOrderID . '" />';
            return $HTML;
        }

        $HTML  = '<!--MATCHROW moImageValue' . $candidateJobOrderID . '-->';
        if ($rating >= 0)
        {
            $HTML .= '<img src="' . $ratings[$rating] . '" style="border: none;" alt="" id="moImage' . $candidateJobOrderID . '" usemap="#moImageMapPos' . $candidateJobOrderID . '" />';
        }
        else
        {
            $HTML .= '<img src="' . $ratings[$rating] . '" style="border: none;" alt="" id="moImage' . $candidateJobOrderID . '" usemap="#moImageMapNeg' . $candidateJobOrderID . '" />';
            $HTML .= '<map id ="moImageMapNeg' . $candidateJobOrderID . '" name="moImageMapNeg' . $candidateJobOrderID . '">';
            $HTML .= '<area shape="rect" coords="0,0,3,12"  href="javascript:void(0);" onmouseout="showImage(\'moImage' . $candidateJobOrderID . '\', moImageValue' . $candidateJobOrderID . ');" onmouseover="showImage(\'moImage' . $candidateJobOrderID . '\', 0);" onclick="moImageValue' . $candidateJobOrderID . ' = 0; setRating(' . $candidateJobOrderID . ', 0, \'moImage' . $candidateJobOrderID . '\', \'' . $sessionCookie . '\');" alt="">';
            $HTML .= '<area shape="rect" coords="4,1,12,12"  href="javascript:void(0);" onmouseout="showImage(\'moImage' . $candidateJobOrderID . '\', moImageValue' . $candidateJobOrderID . ');" onmouseover="showImage(\'moImage' . $candidateJobOrderID . '\', 7);" onclick="moImageValue' . $candidateJobOrderID . ' = -2; setRating(' . $candidateJobOrderID . ', -2, \'moImage' . $candidateJobOrderID . '\', \'' . $sessionCookie . '\');" alt="">';
            $HTML .= '<area shape="rect" coords="13,1,23,12" href="javascript:void(0);" onmouseout="showImage(\'moImage' . $candidateJobOrderID . '\', moImageValue' . $candidateJobOrderID . ');" onmouseover="showImage(\'moImage' . $candidateJobOrderID . '\', 8);" onclick="moImageValue' . $candidateJobOrderID . ' = -3; setRating(' . $candidateJobOrderID . ', -3, \'moImage' . $candidateJobOrderID . '\', \'' . $sessionCookie . '\');" alt="">';
            $HTML .= '<area shape="rect" coords="24,1,34,12" href="javascript:void(0);" onmouseout="showImage(\'moImage' . $candidateJobOrderID . '\', moImageValue' . $candidateJobOrderID . ');" onmouseover="showImage(\'moImage' . $candidateJobOrderID . '\', 9);" onclick="moImageValue' . $candidateJobOrderID . ' = -4; setRating(' . $candidateJobOrderID . ', -4, \'moImage' . $candidateJobOrderID . '\', \'' . $sessionCookie . '\');" alt="">';
            $HTML .= '<area shape="rect" coords="35,1,45,12" href="javascript:void(0);" onmouseout="showImage(\'moImage' . $candidateJobOrderID . '\', moImageValue' . $candidateJobOrderID . ');" onmouseover="showImage(\'moImage' . $candidateJobOrderID . '\', 10);" onclick="moImageValue' . $candidateJobOrderID . ' = -5; setRating(' . $candidateJobOrderID . ', -5, \'moImage' . $candidateJobOrderID . '\', \'' . $sessionCookie . '\');" alt="">';
            $HTML .= '<area shape="rect" coords="46,1,56,12" href="javascript:void(0);" onmouseout="showImage(\'moImage' . $candidateJobOrderID . '\', moImageValue' . $candidateJobOrderID . ');" onmouseover="showImage(\'moImage' . $candidateJobOrderID . '\', 11);" onclick="moImageValue' . $candidateJobOrderID . ' = -6; setRating(' . $candidateJobOrderID . ', -6, \'moImage' . $candidateJobOrderID . '\', \'' . $sessionCookie . '\');" alt="">';
            $HTML .= '</map>';
        }
        $HTML .= '<map id ="moImageMapPos' . $candidateJobOrderID . '" name="moImageMapPos' . $candidateJobOrderID . '">';
        $HTML .= '<area shape="rect" coords="0,0,3,12"  href="javascript:void(0);" onmouseout="showImage(\'moImage' . $candidateJobOrderID . '\', moImageValue' . $candidateJobOrderID . ');" onmouseover="showImage(\'moImage' . $candidateJobOrderID . '\', 0);" onclick="moImageValue' . $candidateJobOrderID . ' = 0; setRating(' . $candidateJobOrderID . ', 0, \'moImage' . $candidateJobOrderID . '\', \'' . $sessionCookie . '\');" alt="">';
        $HTML .= '<area shape="rect" coords="4,1,12,12"  href="javascript:void(0);" onmouseout="showImage(\'moImage' . $candidateJobOrderID . '\', moImageValue' . $candidateJobOrderID . ');" onmouseover="showImage(\'moImage' . $candidateJobOrderID . '\', 1);" onclick="moImageValue' . $candidateJobOrderID . ' = 1; setRating(' . $candidateJobOrderID . ', 1, \'moImage' . $candidateJobOrderID . '\', \'' . $sessionCookie . '\');" alt="">';
        $HTML .= '<area shape="rect" coords="13,1,23,12" href="javascript:void(0);" onmouseout="showImage(\'moImage' . $candidateJobOrderID . '\', moImageValue' . $candidateJobOrderID . ');" onmouseover="showImage(\'moImage' . $candidateJobOrderID . '\', 2);" onclick="moImageValue' . $candidateJobOrderID . ' = 2; setRating(' . $candidateJobOrderID . ', 2, \'moImage' . $candidateJobOrderID . '\', \'' . $sessionCookie . '\');" alt="">';
        $HTML .= '<area shape="rect" coords="24,1,34,12" href="javascript:void(0);" onmouseout="showImage(\'moImage' . $candidateJobOrderID . '\', moImageValue' . $candidateJobOrderID . ');" onmouseover="showImage(\'moImage' . $candidateJobOrderID . '\', 3);" onclick="moImageValue' . $candidateJobOrderID . ' = 3; setRating(' . $candidateJobOrderID . ', 3, \'moImage' . $candidateJobOrderID . '\', \'' . $sessionCookie . '\');" alt="">';
        $HTML .= '<area shape="rect" coords="35,1,45,12" href="javascript:void(0);" onmouseout="showImage(\'moImage' . $candidateJobOrderID . '\', moImageValue' . $candidateJobOrderID . ');" onmouseover="showImage(\'moImage' . $candidateJobOrderID . '\', 4);" onclick="moImageValue' . $candidateJobOrderID . ' = 4; setRating(' . $candidateJobOrderID . ', 4, \'moImage' . $candidateJobOrderID . '\', \'' . $sessionCookie . '\');" alt="">';
        $HTML .= '<area shape="rect" coords="46,1,56,12" href="javascript:void(0);" onmouseout="showImage(\'moImage' . $candidateJobOrderID . '\', moImageValue' . $candidateJobOrderID . ');" onmouseover="showImage(\'moImage' . $candidateJobOrderID . '\', 5);" onclick="moImageValue' . $candidateJobOrderID . ' = 5; setRating(' . $candidateJobOrderID . ', 5, \'moImage' . $candidateJobOrderID . '\', \'' . $sessionCookie . '\');" alt="">';
        $HTML .= '</map>';

        $HTML .= '<script type="text/javascript">';
        $HTML .= 'moImageValue' . $candidateJobOrderID . ' = ' . $rating . ';';

        $HTML .= '</script>';

        /* Only on the first call... */
        if ($firstCall)
        {
            $HTML .= self::getRatingsArrayJS();
        }

        return $HTML;
    }

    /**
     * Prints out the image array of ratings for associated JavaScript.
     *
     * @param integer table row number
     * @return void
     */
    public static function getRatingsArrayJS()
    {
        $ratings = self::_getRatingImages();

        $HTML = '<script type="text/javascript">';

        foreach ($ratings as $rating)
        {
            $ratingsQuoted[] = '"' . $rating . '"';
        }

        $ratingsQuotedString = implode(',', $ratingsQuoted);
        $HTML .= "\n" . 'defineImages(new Array(' . $ratingsQuotedString . '));';

        $HTML .= "\n" . '</script>';

        return $HTML;
    }

    // TODO: Document me.
    public static function getDataItemTypeDescription($dataItemType)
    {
        switch ($dataItemType)
        {
            case DATA_ITEM_CANDIDATE:
                return __('Candidate');
                break;

            case DATA_ITEM_COMPANY:
                return __('Company');
                break;

            case DATA_ITEM_CONTACT:
                return __('Contact');
                break;

            case DATA_ITEM_JOBORDER:
                return __('Joborder');
                break;

            default:
                return '';
        }
    }

    /**
     * Prints out the class name for the current row number (for tables where
     * row color alternates). Even row numbers get the 'evenTableRow' class;
     * odd numbers get the 'oddTableRow' class.
     *
     * @param integer table row number
     * @return void
     */
    public static function printAlternatingRowClass($rowNumber)
    {
        /* Is the row number even? */
        if (($rowNumber % 2) == 0)
        {
            echo 'evenTableRow';
            return;
        }

        echo 'oddTableRow';
    }

    /**
     * Prints out the class name for the current row number (for div pairs where
     * row color alternates). Even row numbers get the 'evenTableRow' class;
     * odd numbers get the 'oddTableRow' class.
     *
     * @param integer div row number
     * @return void
     */
    public static function printAlternatingDivClass($rowNumber)
    {
        /* Is the row number even? */
        if (($rowNumber % 2) == 0)
        {
            echo 'evenDivRow';
            return;
        }

        echo 'oddDivRow';
    }

    /**
     * Returns the class name for the current row number (for tables where
     * row color alternates). Even row numbers get the 'evenTableRow' class;
     * odd numbers get the 'oddTableRow' class.
     *
     * @param integer table row number
     * @return void
     */
    public static function getAlternatingRowClass($rowNumber)
    {
        /* Is the row number even? */
        if (($rowNumber % 2) == 0)
        {
            return 'evenTableRow';
        }
        else
        {
            return 'oddTableRow';
        }
    }

    /**
     * Removes from $text everything from starting block through ending block.
     * Optionally also removes a following piece of text indicated by closing
     * tag.
     *
     * For example, lets say you had the following text:
     *
     *   <a href="blah/blah.html?id=55"><b>My Link</b></a>
     *
     * If you wanted to remove the hyperlink from the text for every occurrence
     * of this format of link, you could use:
     *
     *   $HTML = filterRemoveTextBlock(
     *       $HTML, '<a href="blah/blah.html?id=', '>', '</a>'
     *   );
     *
     * and the link would be replaced with '<b>My Link</b>' in the returned
     * text / HTML.
     *
     * @param string output HTML to filter
     * @param string text at start of text to be removed
     * @param string text at end of text to be removed
     * @param string closing tag to be removed
     * @return string filtered HTML output
     */
    public static function filterRemoveTextBlock($text, $startBlock, $endBlock, $closingTag = '')
    {
        $startPos = strpos($text, $startBlock);
        if ($startPos !== false)
        {
            $endPos = strpos(substr($text, $startPos + strlen($startBlock)), $endBlock);
        }
        else
        {
            $endPos = false;
        }

        while ($startPos !== false || $endPos !== false)
        {
            if ($startPos === false)
            {
                $startPos = 0;
            }

            if ($endPos === false)
            {
                $endPos = 0;
            }
            else
            {
                $endPos += strlen($endBlock);
            }

            $text = substr_replace($text, '', $startPos, $endPos + strlen($startBlock));

            if ($closingTag != '')
            {
                $closingPos = strpos(substr($text, $startPos), $closingTag);

                if ($closingPos !== false)
                {
                    $text = substr_replace($text, '', $closingPos + $startPos, strlen($closingTag));
                }
            }

            $startPos = strpos($text, $startBlock);
            if ($startPos !== false)
            {
                $endPos = strpos(substr($text, $startPos + strlen($startBlock)), $endBlock);
            }
            else
            {
                $endPos = false;
            }
        }

        return $text;
    }

    public static function printSingleQuickActionMenu($dataItemType, $dataItemID)
    {
        echo '<a href="javascript:void(0);" onclick="showHideSingleQuickActionMenu('.$dataItemType.', '.$dataItemID.', docjslib_getRealLeft(this), docjslib_getRealTop(this)+6);"><img src="images/downward.gif" border=0></a>';
    }

    public static function _printQuickActionMenuHolder()
    {
        echo '<div class="ajaxSearchResults" id="singleQuickActionMenu" align="left" style="width:200px;">';

        echo '</div>';
    }

    /**
     * Prints template header HTML.
     *
     * @param string page title
     * @param array JavaScript / CSS files to load
     * @return void
     */
    private static function _printCommonHeader($pageTitle, $headIncludes = array())
    {
        if (!is_array($headIncludes))
        {
            $headIncludes = array($headIncludes);
        }

        $siteID = $_SESSION['OSATS']->getSiteID();
        
        

        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"', "\n";
        echo '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">', "\n";
        echo '<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">', "\n";
        echo '<head>', "\n";
        echo '<title>OSATS Open Source Applicant Tracking System ', $pageTitle, '</title>', "\n";
        echo '<meta http-equiv="Content-Type" content="text/html; charset=', HTML_ENCODING, '" />', "\n";
        echo '<link rel="icon" href="images/favicon.ico" type="image/x-icon" />', "\n";
        echo '<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />', "\n";
        echo '<link rel="alternate" type="application/rss+xml" title="RSS" href="',
             osatutil::getIndexName(), '?m=rss" />', "\n";

        /* Core JS files */
        echo '<script type="text/javascript" src="js/lib.js'.$javascriptAntiCache.'"></script>', "\n";
        echo '<script type="text/javascript" src="js/quickAction.js'.$javascriptAntiCache.'"></script>', "\n";
        echo '<script type="text/javascript" src="js/calendarDateInput.js'.$javascriptAntiCache.'"></script>', "\n";
        echo '<script type="text/javascript" src="js/submodal/subModal.js'.$javascriptAntiCache.'"></script>', "\n";
        echo '<script type="text/javascript">OSATSIndexName = "'.osatutil::getIndexName().'";</script>', "\n";

       $headIncludes[] = 'main.css';

        foreach ($headIncludes as $key => $filename)
        {
            /* Done manually to prevent a global dependency on FileUtility. */
            if ($filename == 'tinymce')
            {
                echo ('<script language="javascript" type="text/javascript" src="lib/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>'."\n".
                      '<script language="javascript" type="text/javascript">tinyMCE.init({
                            mode : "specific_textareas",
                            editor_selector : "mceEditor",
                            width : "100%",
                            theme : "advanced",
                            theme_advanced_buttons1 : "bold,italic,strikethrough,separator,bullist,numlist,outdent,indent,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,link,unlink,separator,underline,forecolor,separator,removeformat,cleanup,separator,charmap,separator,undo,redo",
                            theme_advanced_buttons2 : "",
                            theme_advanced_buttons3 : "",
                            language : "'.i18n::getLanguageToken().'",
                            theme_advanced_toolbar_location : "top",
                            theme_advanced_toolbar_align : "left",
                            theme_advanced_resizing : true,
                            browsers : "msie,gecko,opera,safari",
                            dialog_type : "modal",
                            theme_advanced_resize_horizontal : false,
                            convert_urls : false,
                            relative_urls : false,
                            remove_script_host : false,
                            force_p_newlines : false,
                            force_br_newlines : true,
                            convert_newlines_to_brs : false,
                            remove_linebreaks : false,
                            fix_list_elements : true
                        });</script>'."\n");
            }
            else
            {

                $extension = substr($filename, strrpos($filename, '.') + 1);

                $filename .= $javascriptAntiCache;

                if ($extension == 'js')
                {
                    echo '<script type="text/javascript" src="', $filename, '"></script>', "\n";
                }
                else if ($extension == 'css')
                {
                    echo '<style type="text/css" media="all">@import "', $filename, '";</style>', "\n";
                }
            }
        }

        echo '<!--[if IE]><link rel="stylesheet" type="text/css" href="ie.css" /><![endif]-->', "\n";
        echo '</head>', "\n\n";
    }


    /**
     * Returns an array of "star" images for rating values.
     *
     * @return array rating values and associated image paths
     */
    private static function _getRatingImages()
    {
        return array(
            0  => 'images/stars/star0.gif',
            1  => 'images/stars/star1.gif',
            2  => 'images/stars/star2.gif',
            3  => 'images/stars/star3.gif',
            4  => 'images/stars/star4.gif',
            5  => 'images/stars/star5.gif',
            -1 => 'images/stars/starneg1.gif',
            -2 => 'images/stars/starneg2.gif',
            -3 => 'images/stars/starneg3.gif',
            -4 => 'images/stars/starneg4.gif',
            -5 => 'images/stars/starneg5.gif',
            -6 => 'images/stars/starneg6.gif'
        );
    }
}