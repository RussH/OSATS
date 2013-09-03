<?php
/*
   * OSATS
   *
   *
   * Open Source GNU License will apply
*/
include_once('./lib/Candidates.php');
include_once('./lib/EmailTemplates.php');

$interface = new SecureAJAXInterface();

/* Bail out if we don't have a valid candidate ID. */
if (!$interface->isRequiredIDValid('candidateID', $_REQUEST))
{
    $interface->outputXMLErrorPage(-1, __('Invalid candidate ID.'));
    die();
}


/* Bail out if we don't have a valid job order ID. */
if (!$interface->isRequiredIDValid('jobOrderID', $_REQUEST))
{
    $interface->outputXMLErrorPage(-2, __('Invalid job order ID.'));
    die();
}

$siteID = $interface->getSiteID();

$candidateID = $_REQUEST['candidateID'];
$jobOrderID  = $_REQUEST['jobOrderID'];

$candidates = new Candidates($siteID);
$candidateRS = $candidates->get($candidateID);


/* Bail out if we got an empty result set. */
if (empty($candidateRS))
{
    $interface->outputXMLErrorPage(-3, __('The specified candidate ID could not be found.'));
    die();
}

$pipelines = new Pipelines($siteID);
$pipelineData = $pipelines->get($candidateID, $jobOrderID);

/* Bail out if we got an empty result set. */
if (empty($pipelineData))
{
    $interface->outputXMLErrorPage(-4 , __('The specified pipeline entry could not be found.'));
    die();
}

$statusRS = $pipelines->getStatusesForPicking();

$selectedStatusID = $pipelineData['statusID'];

/* Override default send email behavior with site specific send email behavior. */
$mailerSettings = new MailerSettings($siteID);
$mailerSettingsRS = $mailerSettings->getAll();

$candidateJoborderStatusSendsMessage = unserialize($mailerSettingsRS['candidateJoborderStatusSendsMessage']);

foreach ($statusRS as $index => $status)
{
    $statusRS[$index]['triggersEmail'] = $candidateJoborderStatusSendsMessage[$status['statusID']];
}

/* Get the change status email template. */
/*
  Status specific email templates:
  By default, the email template 'EMAIL_TEMPLATE_STATUSCHANGE' is used.
  However, if there exists an (enabled) email template tagged 'EMAIL_TEMPLATE_STATUSCHANGE_%NEWSTATUS_ID%'
  (with '%NEWSTATUS_ID%' replaced with the new status, see pipeline status flags in constants.php),
  this template takes precedence over the default one an will be used. 
*/
$emailTemplates = new EmailTemplates($siteID);

/* First, try to get a status-specific template */
$statusChangeTemplateRS = $emailTemplates->getByTag(
    'EMAIL_TEMPLATE_STATUSCHANGE_' . $_REQUEST['statusID']
);

if (empty($statusChangeTemplateRS)) {
    /* Use default teplate if no specific one can be found */
    $statusChangeTemplateRS = $emailTemplates->getByTag(
        'EMAIL_TEMPLATE_STATUSCHANGE'
    );
}

if (empty($statusChangeTemplateRS) ||
empty($statusChangeTemplateRS['textReplaced']))
{
    $statusChangeTemplateSubject = '';
    $statusChangeTemplate = '';
    $emailDisabled = $statusChangeTemplateRS['disabled'];
}
else
{
    $statusChangeTemplateSubject = $statusChangeTemplateRS['subject'];
    $statusChangeTemplate = $statusChangeTemplateRS['textReplaced'];
    $emailDisabled = $statusChangeTemplateRS['disabled'];
}

/* Replace e-mail template variables. '%CANDSTATUS%', '%JBODTITLE%',
 * '%JBODCLIENT%' are replaced by JavaScript.
 */
$EEOSettings = new EEOSettings($siteID);
$EEOSettingsRS = $EEOSettings->getAll();

$stringsToFind = array(
    '%CANDOWNER%',
    '%CANDFIRSTNAME%',
    '%CANDLASTNAME%',
    '%CANDFULLNAME%',
    '%SALUTATION%',
    '%CANDSTATUS'
);

$replacementStrings = array(
    $candidateRS['ownerFullName'],
    $candidateRS['firstName'],
    $candidateRS['lastName'],
    $candidateRS['firstName'] . ' ' . $candidateRS['lastName'],
    ( $EEOSettingsRS['enabled'] == 1 && $EEOSettingsRS['genderTracking'] == 1 && !empty($candidateRS['eeoGender']) ?  
        ( strtolower($candidateRS['eeoGender']) == 'f' ? __('Dear Mrs.') : __('Dear Mr.') ) :
                __('Hello') 
    ),
    $statusRS[$_REQUEST['statusID']]['short_description'] 
);

/* Treat extra fields */
$extraFields = new ExtraFields($siteID, DATA_ITEM_CANDIDATE);
$tmpArr = $extraFields->getValues($candidateID);
$extraFieldsValues = array();
if(!empty($tmpArr))  
    foreach($tmpArr as $tmp)  
        $extraFieldsValues[$tmp['fieldName']] = $tmp['value'];
else
    $extraFieldsValues = array();

preg_match_all(
    '/%EXTRA_(.+?)%/',
    $statusChangeTemplate,
    $matches,
    PREG_PATTERN_ORDER
);

foreach($matches[1] as $match) {
    $stringsToFind[] = '%EXTRA_' . $match . '%';
    $replacementStrings[] = $extraFieldsValues[$match];
}

$statusChangeTemplate = str_replace(
    $stringsToFind,
    $replacementStrings,
    $statusChangeTemplate
);

/* Workaround for XMLHttpRequest objects changing newlines (i.e. FF 23.0.1) */
/* Newlines are replaced by '<br />', which must be converted back by the AJAX frontend */
$statusChangeTemplate = preg_replace('/\r\n/', '<br />', $statusChangeTemplate);

$output = "<data>\n" . 
              "    <errorcode>0</errorcode>\n" .
              "    <errormessage></errormessage>\n" .
              "    <statusChangeTemplateSubject><![CDATA[" . $statusChangeTemplateSubject . "]]></statusChangeTemplateSubject>\n" . 
              "    <statusChangeTemplate><![CDATA[" . $statusChangeTemplate . "]]></statusChangeTemplate>\n" .
              "</data>\n";

/* Send back the XML data. */
$interface->outputXMLPage($output);
