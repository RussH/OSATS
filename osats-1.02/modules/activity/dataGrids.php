<?php
/**
 * OSATS
 */

include_once('./lib/ActivityEntries.php');
include_once('./lib/Hooks.php');
include_once('./lib/InfoString.php');
include_once('./lib/i18n.php');


class ActivityDataGrid extends DataGrid
{
    protected $_siteID;
    private   $dateformat;


    // FIXME: Fix ugly indenting - ~400 character lines = bad.
    public function __construct($siteID, $parameters)
    {
        /* Pager configuration. */
        $this->_tableWidth = 915;
        $this->_defaultAlphabeticalSortBy = 'lastName';
        $this->ajaxMode = false;
        $this->showExportCheckboxes = true; //BOXES WILL NOT APPEAR UNLESS SQL ROW exportID IS RETURNED!
        $this->showActionArea = true;
        $this->showChooseColumnsBox = true;
        $this->allowResizing = true;
        $this->dateCriterion = '';
        $this->dateformat = __('DATEFORMAT_SQL_LONG');

        if (isset($parameters['period']) && !empty($parameters['period']))
        {
            $this->dateCriterion .= ' AND activity.date_created >= ' . $parameters['period'] . ' ';
        }
        else
        {
            if (isset($parameters['startDate']) && !empty($parameters['startDate']))
            {
                $this->dateCriterion .= ' AND activity.date_created >= \'' .$parameters['startDate'].'\' ';
            }

            if (isset($parameters['endDate']) && !empty($parameters['endDate']))
            {
                $this->dateCriterion .= ' AND activity.date_created <= \''.$parameters['endDate'].'\' ';
            }
        }

        $this->defaultSortBy = 'dateCreatedSort';
        $this->defaultSortDirection = 'DESC';

        $this->_defaultColumns = array(
            array('name' => __('Date'),       'width' => 110),
            array('name' => __('First Name'), 'width' => 85),
            array('name' => __('Last Name'),  'width' => 75),
            array('name' => __('Regarding'),  'width' => 125),
            array('name' => __('Activity'),   'width' => 65),
            array('name' => __('Notes'),      'width' => 240),
            array('name' => __('Entered By'), 'width' => 60),
        );


        $this->_db = DatabaseConnection::getInstance();
        $this->_siteID = $siteID;
        $this->_assignedCriterion = "";
        $this->_dataItemIDColumn = 'company.company_id';

        $this->_classColumns = array(
            __('Date') =>        array('pagerRender'    => 'return $rsData[\'dateCreated\'];',
                                      'sortableColumn' => 'dateCreatedSort',
                                      'pagerWidth'     => 110,
                                      'pagerOptional'  => true,
                                      'alphaNavigation'=> true,
                                      'filterHaving'   => 'dateCreated'),

            __('First Name') =>  array('pagerRender'    => 'if ($rsData[\'dataItemType\']=='.DATA_ITEM_CANDIDATE.') {$ret = \'<img src="images/mru/candidate.gif" height="12" alt="" />\';} else if ($rsData[\'dataItemType\']=='.DATA_ITEM_CONTACT.') {$ret = \'<img src="images/mru/contact.gif" height="12">\';} else {$ret = \'<img src="images/mru/blank.gif">\';} if ($rsData[\'isHot\'] == 1) $className =  \'jobLinkHot\'; else $className = \'jobLinkCold\'; if ($rsData[\'dataItemType\']=='.DATA_ITEM_CANDIDATE.') {return $ret.\'&nbsp;<a href="'.osatutil::getIndexName().'?m=candidates&amp;a=show&amp;candidateID=\'.$rsData[\'dataItemID\'].\'" class="\'.$className.\'" title="\'.htmlspecialchars(InfoString::make($rsData[\'dataItemType\'],$rsData[\'dataItemID\'],$rsData[\'siteID\'])).\'">\'.htmlspecialchars($rsData[\'firstName\']).\'</a>\';} else {return  $ret.\'&nbsp;<a href="'.osatutil::getIndexName().'?m=contacts&amp;a=show&amp;contactID=\'.$rsData[\'dataItemID\'].\'" class="\'.$className.\'" title="\'.htmlspecialchars(InfoString::make($rsData[\'dataItemType\'],$rsData[\'dataItemID\'],$rsData[\'siteID\'])).\'">\'.htmlspecialchars($rsData[\'firstName\']).\'</a>\';}',
                                     'sortableColumn'  => 'firstName',
                                     'pagerWidth'      => 85,
                                     'pagerOptional'   => false,
                                     'alphaNavigation' => true,
                                     'filterHaving'    => 'firstName'),

            __('Last Name') =>   array('pagerRender'    => 'if ($rsData[\'isHot\'] == 1) $className =  \'jobLinkHot\'; else $className = \'jobLinkCold\'; if ($rsData[\'dataItemType\']=='.DATA_ITEM_CANDIDATE.') {return \'<a href="'.osatutil::getIndexName().'?m=candidates&amp;a=show&amp;candidateID=\'.$rsData[\'dataItemID\'].\'" class="\'.$className.\'" title="\'.htmlspecialchars(InfoString::make($rsData[\'dataItemType\'],$rsData[\'dataItemID\'],$rsData[\'siteID\'])).\'"> \'.htmlspecialchars($rsData[\'lastName\']).\'</a>\';} else {return \'<a href="'.osatutil::getIndexName().'?m=contacts&amp;a=show&amp;contactID=\'.$rsData[\'dataItemID\'].\'" class="\'.$className.\'" title="\'.htmlspecialchars(InfoString::make($rsData[\'dataItemType\'],$rsData[\'dataItemID\'],$rsData[\'siteID\'])).\'"> \'.htmlspecialchars($rsData[\'lastName\']).\'</a>\';}',
                                     'sortableColumn'  => 'lastName',
                                     'pagerWidth'      => 75,
                                     'pagerOptional'   => false,
                                     'alphaNavigation' => true,
                                     'filterHaving'    => 'lastName'),

             __('Regarding') =>  array('pagerRender'    => 'if ($rsData[\'jobIsHot\'] == 1) $className =  \'jobLinkHot\'; else $className = \'jobLinkCold\'; if ($rsData[\'companyIsHot\'] == 1) $companyClassName =  \'jobLinkHot\'; else $companyClassName = \'jobLinkCold\';  if ($rsData[\'regardingJobTitle\'] == \'\') {$ret = \'General\'; } else {$ret = \'<a href="'.osatutil::getIndexName().'?m=joborders&amp;a=show&amp;jobOrderID=\'.$rsData[\'jobOrderID\'].\'" class="\'.$className.\'">\'.htmlspecialchars($rsData[\'regardingJobTitle\']).\'</a>\'; if($rsData[\'regardingCompanyName\'] != \'\') {$ret .= \' <a href="'.osatutil::getIndexName().'?m=companies&amp;a=show&amp;companyID=\'.$rsData[\'companyID\'].\'" class="\'.$companyClassName.\'">(\'.htmlspecialchars($rsData[\'regardingCompanyName\']).\')\';}} return $ret;',
                                     'sortableColumn'  => 'regarding',
                                     'pagerWidth'      => 125,
                                     'pagerOptional'   => true,
                                     'alphaNavigation' => true,
                                     'filterHaving'    => 'regarding'),

             __('Activity') =>   array('pagerRender'    => '$ret = $rsData[\'typeDescription\']; return $ret;',
                                     'sortableColumn'  => 'typeDescription',
                                     'pagerWidth'      => 65,
                                     'pagerOptional'   => true,
                                     'alphaNavigation' => true,
                                     'filter'          => 'activity_type.short_description'),

             __('Notes') =>      array('pagerRender'    => 'return $rsData[\'notes\'];',
                                     'sortableColumn'  => 'notes',
                                     'pagerWidth'      => 240,
                                     'pagerOptional'   => true,
                                     'alphaNavigation' => true,
                                     'filterHaving'    => 'notes'),

            __('Entered By') =>  array(
                                     'pagerRender'      => 'return StringUtility::makeInitialName($rsData[\'enteredByFirstName\'], $rsData[\'enteredByLastName\'], false, LAST_NAME_MAXLEN);',
                                     'exportRender'     => 'return $rsData[\'enteredByFirstName\'] . " " .$rsData[\'enteredByLastName\'];',
                                     'sortableColumn'     => 'enteredBySort',
                                     'pagerWidth'    => 60,
                                     'alphaNavigation' => true,
                                     'filter'         => 'CONCAT(entered_by_user.last_name, entered_by_user.first_name)'),
        );

        parent::__construct("activity:ActivityDataGrid", $parameters);
    }

    /**
     * Returns the sql statment for the pager.
     *
     * @return array clients data
     */
    public function getSQL($selectSQL, $joinSQL, $whereSQL, $havingSQL, $orderSQL, $limitSQL, $distinct = '')
    {
        $sql = sprintf(
            "SELECT SQL_CALC_FOUND_ROWS %s
                activity.activity_id AS activityID,
                activity.data_item_id AS dataItemID,
                activity.data_item_type AS dataItemType,
                activity.site_id AS siteID,
                data_item_type.short_description AS item,
                candidate.first_name AS firstName,
                candidate.last_name AS lastName,
                candidate.is_hot AS isHot,
                joborder.is_hot AS jobIsHot,
                company.is_hot AS companyIsHot,
                company.company_id AS companyID,
                activity.joborder_id AS jobOrderID,
                activity.notes AS notes,
                activity_type.short_description AS typeDescription,
                DATE_FORMAT(
                    activity.date_created, '%s'
                ) AS dateCreated,
                activity.date_created AS dateCreatedSort,
                entered_by_user.first_name AS enteredByFirstName,
                entered_by_user.last_name AS enteredByLastName,
                CONCAT(entered_by_user.last_name, entered_by_user.first_name) AS enteredBySort,
                IF(ISNULL(joborder.title),
                    'General',
                    CONCAT(joborder.title, ' (', company.name, ')'))
                AS regarding,
                joborder.title AS regardingJobTitle,
                company.name AS regardingCompanyName
            FROM
                activity
            JOIN data_item_type
                ON activity.data_item_type = data_item_type.data_item_type_id
            LEFT JOIN user AS entered_by_user
                ON activity.entered_by = entered_by_user.user_id
            LEFT JOIN activity_type
                ON activity.type = activity_type.activity_type_id
            LEFT JOIN joborder
                ON activity.joborder_id = joborder.joborder_id
            LEFT JOIN company
                ON joborder.company_id = company.company_id
            INNER JOIN candidate
                ON activity.data_item_id = candidate.candidate_id
            WHERE
                activity.data_item_type = %s
            AND
                activity.site_id = %s
                %s
                %s
            UNION
            SELECT %s
                activity.activity_id AS activityID,
                activity.data_item_id AS dataItemID,
                activity.data_item_type AS dataItemType,
                activity.site_id AS siteID,
                data_item_type.short_description AS item,
                contact.first_name AS firstName,
                contact.last_name AS lastName,
                contact.is_hot AS isHot,
                joborder.is_hot AS jobIsHot,
                company.is_hot AS companyIsHot,
                company.company_id AS companyID,
                activity.joborder_id AS jobOrderID,
                activity.notes AS notes,
                activity_type.short_description AS typeDescription,
                DATE_FORMAT(
                    activity.date_created, '%s'
                ) AS dateCreated,
                activity.date_created AS dateCreatedSort,
                entered_by_user.first_name AS enteredByFirstName,
                entered_by_user.last_name AS enteredByLastName,
                CONCAT(entered_by_user.last_name, entered_by_user.first_name) AS enteredBySort,
                IF(ISNULL(joborder.title),
                    'General',
                    CONCAT(joborder.title, ' (', company.name, ')'))
                AS regarding,
                joborder.title AS regardingJobTitle,
                company.name AS regardingCompanyName
            FROM
                activity
            JOIN data_item_type
                ON activity.data_item_type = data_item_type.data_item_type_id
            LEFT JOIN user AS entered_by_user
                ON activity.entered_by = entered_by_user.user_id
            LEFT JOIN activity_type
                ON activity.type = activity_type.activity_type_id
            LEFT JOIN joborder
                ON activity.joborder_id = joborder.joborder_id
            LEFT JOIN company
                ON joborder.company_id = company.company_id
            INNER JOIN contact
                ON activity.data_item_id = contact.contact_id
            WHERE
                activity.data_item_type = %s
            AND
                activity.site_id = %s
                %s
                %s
            %s
            %s
            %s",
            $distinct,
            $this->dateformat,
            DATA_ITEM_CANDIDATE,
            $this->_siteID,
            $this->dateCriterion,
            (strlen($whereSQL) > 0) ? ' AND ' . $whereSQL : '',
            $distinct,
            $this->dateformat,
            DATA_ITEM_CONTACT,
            $this->_siteID,
            $this->dateCriterion,
            (strlen($whereSQL) > 0) ? ' AND ' . $whereSQL : '',
            (strlen($havingSQL) > 0) ? ' HAVING ' . $havingSQL : '',
            $orderSQL,
            $limitSQL
        );

        return $sql;
    }
}