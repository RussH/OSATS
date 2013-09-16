<?php /* $Id: AddActivityChangeStatusModal.tpl 3799 2007-12-04 17:54:36Z brian $ */ ?>
<?php if ($this->isJobOrdersMode): ?>
    <?php TemplateUtility::printModalHeader(__('Job Orders'), array('modules/candidates/activityvalidator.js', 'js/activity.js'), __('Job Orders: Log Activity')); ?>
<?php elseif ($this->onlyScheduleEvent): ?>
    <?php TemplateUtility::printModalHeader(__('Candidates'), array('modules/candidates/activityvalidator.js', 'js/activity.js'), __('Candidates: Schedule Event')); ?>
<?php else: ?>
    <?php TemplateUtility::printModalHeader(__('Candidates'), array('modules/candidates/activityvalidator.js', 'js/activity.js'), __('Candidates: Log Activity')); ?>
<?php endif; ?>

<?php if (!$this->isFinishedMode): ?>

<script type="text/javascript">
    <?php if ($this->isJobOrdersMode): ?>
        statusesArray = new Array(1);
        jobOrdersArray = new Array(1);
        statusesArrayString = new Array(1);
        jobOrdersArrayStringTitle = new Array(1);
        jobOrdersArrayStringCompany = new Array(1);
        statusesArray[0] = <?php echo($this->pipelineData['statusID']); ?>;
        statusesArrayString[0] = '<?php echo($this->pipelineData['status']); ?>';
        jobOrdersArray[0] = <?php echo($this->pipelineData['jobOrderID']); ?>;
        jobOrdersArrayStringTitle[0] = '<?php echo(str_replace("'", "\\'", $this->pipelineData['title'])); ?>';
        jobOrdersArrayStringCompany[0] = '<?php echo(str_replace("'", "\\'", $this->pipelineData['companyName'])); ?>';
    <?php else: ?>
        <?php $count = count($this->pipelineRS); ?>
        statusesArray = new Array(<?php echo($count); ?>);
        jobOrdersArray = new Array(<?php echo($count); ?>);
        statusesArrayString = new Array(<?php echo($count); ?>);
        jobOrdersArrayStringTitle = new Array(<?php echo($count); ?>);
        jobOrdersArrayStringCompany = new Array(<?php echo($count); ?>);
        <?php for ($i = 0; $i < $count; ++$i): ?>
            statusesArray[<?php echo($i); ?>] = <?php echo($this->pipelineRS[$i]['statusID']); ?>;
            statusesArrayString[<?php echo($i); ?>] = '<?php echo($this->pipelineRS[$i]['status']); ?>';
            jobOrdersArray[<?php echo($i); ?>] = <?php echo($this->pipelineRS[$i]['jobOrderID']); ?>;
            jobOrdersArrayStringTitle[<?php echo($i); ?>] = '<?php echo(str_replace("'", "\\'", $this->pipelineRS[$i]['title'])); ?>';
            jobOrdersArrayStringCompany[<?php echo($i); ?>] = '<?php echo(str_replace("'", "\\'", $this->pipelineRS[$i]['companyName'])); ?>';
        <?php endfor; ?>
    <?php endif; ?>
    statusTriggersEmailArray = new Array(<?php echo(count($this->statusRS)); ?>);
    <?php foreach ($this->statusRS as $rowNumber => $statusData): ?>
       statusTriggersEmailArray[<?php echo($rowNumber); ?>] = <?php echo($statusData['triggersEmail']); ?>;
    <?php endforeach; ?>
</script>

    <form name="changePipelineStatusForm" id="changePipelineStatusForm" action="<?php echo(osatutil::getIndexName()); ?>?m=<?php if ($this->isJobOrdersMode): ?>joborders<?php else: ?>candidates<?php endif; ?>&amp;a=addActivityChangeStatus<?php if ($this->onlyScheduleEvent): ?>&amp;onlyScheduleEvent=true<?php endif; ?>" method="post" onsubmit="return checkActivityForm(document.changePipelineStatusForm);" autocomplete="off">
        <input type="hidden" name="postback" id="postback" value="postback" />
        <input type="hidden" id="candidateID" name="candidateID" value="<?php echo($this->candidateID); ?>" />
<?php if ($this->isJobOrdersMode): ?>
        <input type="hidden" id="regardingID" name="regardingID" value="<?php echo($this->selectedJobOrderID); ?>" />
<?php endif; ?>

        <table class="editTable" width="560">
            <tr id="visibleTR" <?php if ($this->onlyScheduleEvent): ?>style="display:none;"<?php endif; ?>>
                <td class="tdVertical">
                    <label id="regardingIDLabel" for="regardingID"><?php _e('Regarding');?>:</label>
                </td>
                <td class="tdData">
<?php if ($this->isJobOrdersMode): ?>
                    <span><?php $this->_($this->pipelineData['title']); ?></span>
<?php else: ?>
                    <select id="regardingID" name="regardingID" class="inputbox" style="width: 150px;" onchange="AS_onRegardingChange(statusesArray, jobOrdersArray, 'regardingID', 'statusID', 'statusTR', 'sendEmailCheckTR', 'triggerEmail', 'triggerEmailSpan', 'changeStatus', 'changeStatusSpanA', 'changeStatusSpanB');">
                        <option value="-1"><?php _e('General');?></option>

                        <?php foreach ($this->pipelineRS as $rowNumber => $pipelinesData): ?>
                            <?php if ($this->selectedJobOrderID == $pipelinesData['jobOrderID']): ?>
                                <option selected="selected" value="<?php $this->_($pipelinesData['jobOrderID']) ?>"><?php $this->_($pipelinesData['title']) ?></option>
                            <?php else: ?>
                                <option value="<?php $this->_($pipelinesData['jobOrderID']) ?>"><?php $this->_($pipelinesData['title']) ?> (<?php $this->_($pipelinesData['companyName']) ?>)</option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
<?php endif; ?>
                </td>
            </tr>

            <tr id="statusTR" <?php if ($this->onlyScheduleEvent): ?>style="display:none;"<?php endif; ?>>
                <td class="tdVertical">
                    <label id="statusIDLabel" for="statusID"><?php _e('Status');?>:</label>
                </td>
                <td class="tdData">
                    <input type="checkbox" name="changeStatus" id="changeStatus" style="margin-left: 0px" onclick="AS_onChangeStatusChange('changeStatus', 'statusID', 'changeStatusSpanB');"<?php if ($this->selectedJobOrderID == -1 || $this->onlyScheduleEvent): ?> disabled<?php endif; ?> />
                    <span id="changeStatusSpanA"<?php if ($this->selectedJobOrderID == -1): ?> style="color: #aaaaaa;"<?php endif;?>><?php _e('Change Status');?></span><br />

                    <div id="changeStatusDiv" style="margin-top: 4px;">
                        <select id="statusID" name="statusID" class="inputbox" style="width: 150px;" onchange="AS_onStatusChange(statusesArray, jobOrdersArray, 'regardingID', 'statusID', 'sendEmailCheckTR', 'triggerEmailSpan', 'activityNote', 'activityTypeID', <?php if ($this->isJobOrdersMode): echo $this->selectedJobOrderID; else: ?>null<?php endif; ?>, 'candidateID', 'subject', 'customMessage', 'originalCustomMessage', 'triggerEmail', statusesArrayString, jobOrdersArrayStringTitle, jobOrdersArrayStringCompany, statusTriggersEmailArray, 'emailIsDisabled', '<?php echo($this->sessionCookie); ?>' );" disabled>
                            <option value="-1">(<?php _e('Select a Status');?>)</option>

                            <?php if ($this->selectedStatusID == -1): ?>
                                <?php foreach ($this->statusRS as $rowNumber => $statusData): ?>
                                    <option value="<?php $this->_($statusData['statusID']) ?>"><?php $this->_($statusData['status']) ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php foreach ($this->statusRS as $rowNumber => $statusData): ?>
                                    <option <?php if ($this->selectedStatusID == $statusData['statusID']): ?>selected <?php endif; ?>value="<?php $this->_($statusData['statusID']) ?>"><?php $this->_($statusData['status']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <span id="changeStatusSpanB" style="color: #aaaaaa;">&nbsp;*</span>&nbsp;&nbsp;
                        <span id="triggerEmailSpan" style="display: none;"><input type="checkbox" name="triggerEmail" id="triggerEmail" onclick="AS_onSendEmailChange('triggerEmail', 'sendEmailCheckTR', 'visibleTR');" /><?php _e('Send E-Mail Notification to Candidate');?></span>
                    </div>
                </td>
            </tr>

            <tr id="sendEmailCheckTR" style="display: none;">
                <td class="tdVertical">
                    <label id="triggerEmailLabel" for="triggerEmail"><?php _e('E-Mail');?>:</label>
                </td>
                <td class="tdData">
                    <?php _e('Custom Message');?><br />
                    <input name="subject" id="subject" style="width:375px" value="<?php echo($this->subject); ?>" /><br />
                    <input type="hidden" id="originalCustomMessage" value="<?php $this->_($this->statusChangeTemplate); ?>" />
                    <input type="hidden" id="emailIsDisabled" value="<?php echo($this->emailDisabled); ?>" />
                    <textarea style="height:135px; width:375px;" name="customMessage" id="customMessage" cols="50" class="inputbox"></textarea>
                </td>
            </tr>
           <tr id="addActivityTR" <?php if ($this->onlyScheduleEvent): ?>style="display:none;"<?php endif; ?>>
                <td class="tdVertical">
                    <label id="addActivityLabel" for="addActivity"><?php _e('Activity');?>:</label>
                </td>
                <td class="tdData">
                    <input type="checkbox" name="addActivity" id="addActivity" style="margin-left: 0px;"<?php if (!$this->onlyScheduleEvent): ?> checked="checked"<?php endif; ?> onclick="AS_onAddActivityChange('addActivity', 'activityTypeID', 'activityNote', 'addActivitySpanA', 'addActivitySpanB');" /><?php _e('Log an Activity');?><br />
                    <div id="activityNoteDiv" style="margin-top: 4px;">
                        <span id="addActivitySpanA"><?php _e('Activity Type');?></span><br />
                        <select id="activityTypeID" name="activityTypeID" class="inputbox" style="width: 150px; margin-bottom: 4px;">
                            <option value="<?php echo(ACTIVITY_CALL); ?>"><?php _e('Call');?></option>
                            <option value="<?php echo(ACTIVITY_CALL_TALKED); ?>"><?php _e('Call');?> (<?php _e('Talked');?>)</option>
                            <option value="<?php echo(ACTIVITY_CALL_LVM); ?>"><?php _e('Call');?> (<?php _e('LVM');?>)</option>
                            <option value="<?php echo(ACTIVITY_CALL_MISSED); ?>"><?php _e('Call');?> (<?php _e('Missed');?>)</option>
                            <option value="<?php echo(ACTIVITY_EMAIL); ?>"><?php _e('E-Mail');?></option>
                            <option value="<?php echo(ACTIVITY_MEETING); ?>"><?php _e('Meeting');?></option>
                            <option selected="selected" value="<?php echo(ACTIVITY_OTHER); ?>"><?php _e('Other');?></option>
                        </select><br />
                        <span id="addActivitySpanB"><?php _e('Activity Notes');?></span><br />
                        <textarea name="activityNote" id="activityNote" cols="50" style="margin-bottom: 4px;" class="inputbox"></textarea>
                    </div>
                </td>
            </tr>

            <tr id="scheduleEventTR">
                <td class="tdVertical">
                    <label id="scheduleEventLabel" for="scheduleEvent"><?php _e('Schedule Event');?>:</label>
                </td>
                <td class="tdData">
                    <input type="checkbox" name="scheduleEvent" id="scheduleEvent" style="margin-left: 0px; <?php if ($this->onlyScheduleEvent): ?>display:none;<?php endif; ?>" onclick="AS_onScheduleEventChange('scheduleEvent', 'scheduleEventDiv');"<?php if ($this->onlyScheduleEvent): ?> checked="checked"<?php endif; ?> /><?php if (!$this->onlyScheduleEvent): ?><?php _e('Schedule Event');?><?php endif; ?>
                    <div id="scheduleEventDiv" <?php if (!$this->onlyScheduleEvent): ?>style="display:none;"<?php endif; ?>>
                        <table style="border: none; margin: 0px; padding: 0px;">
                            <tr>
                                <td valign="top">
                                    <div style="margin-bottom: 4px;">
                                        <select id="eventTypeID" name="eventTypeID" class="inputbox" style="width: 150px;">
                                            <?php foreach ($this->calendarEventTypes as $eventType): ?>
                                                <option <?php if ($eventType['typeID'] == CALENDAR_EVENT_INTERVIEW): ?>selected="selected" <?php endif; ?>value="<?php echo($eventType['typeID']); ?>"><?php $this->_($eventType['description']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div style="margin-bottom: 4px;">
                                        <script type="text/javascript">DateInput('dateAdd', true, 'MM-DD-YY', '', -1);</script>
                                    </div>

                                    <div style="margin-bottom: 4px;">
                                        <input type="radio" name="allDay" id="allDay0" value="0" style="margin-left: 0px" checked="checked" onchange="AS_onEventAllDayChange('allDay1');" />
                                        <select id="hour" name="hour" class="inputbox" style="width: 40px;">
                                            <?php for ($i = 1; $i <= 12; ++$i): ?>
                                                <option value="<?php echo($i); ?>"><?php echo(sprintf('%02d', $i)); ?></option>
                                            <?php endfor; ?>
                                        </select>&nbsp;
                                        <select id="minute" name="minute" class="inputbox" style="width: 40px;">
                                            <?php for ($i = 0; $i <= 45; $i = $i + 15): ?>
                                                <option value="<?php echo(sprintf('%02d', $i)); ?>">
                                                    <?php echo(sprintf('%02d', $i)); ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>&nbsp;
                                        <select id="meridiem" name="meridiem" class="inputbox" style="width: 45px;">
                                            <option value="AM">AM</option>
                                            <option value="PM">PM</option>
                                        </select>
                                    </div>

                                    <div style="margin-bottom: 4px;">
                                        <input type="radio" name="allDay" id="allDay1" value="1" style="margin-left: 0px" onchange="AS_onEventAllDayChange('allDay1');" /><?php _e('All Day / No Specific Time');?><br />
                                    </div>

                                    <div style="margin-bottom: 4px;">
                                        <input type="checkBox" name="publicEntry" id="publicEntry" style="margin-left: 0px" /><?php _e('Public Entry');?>
                                    </div>
                                </td>

                                <td valign="top">
                                    <div style="margin-bottom: 4px;">
                                        <label id="titleLabel" for="title"><?php _e('Title');?>&nbsp;*</label><br />
                                        <input type="text" class="inputbox" name="title" id="title" style="width: 180px;" />
                                    </div>

                                    <div style="margin-bottom: 4px;">
                                        <label id="durationLabel" for="duration"><?php _e('Length');?>:</label>
                                        <br />
                                        <select id="duration" name="duration" class="inputbox" style="width: 180px;">
                                            <option value="15">15 <?php _e('minutes');?></option>
                                            <option value="30">30 <?php _e('minutes');?></option>
                                            <option value="45">45 <?php _e('minutes');?></option>
                                            <option value="60" selected="selected">1 <?php _e('hour');?></option>
                                            <option value="90">1.5 <?php _e('hours');?></option>
                                            <option value="120">2 <?php _e('hours');?></option>
                                            <option value="180">3 <?php _e('hours');?></option>
                                            <option value="240">4 <?php _e('hours');?></option>
                                            <option value="300"><?php _e('More than 4 hours');?></option>
                                        </select>
                                    </div>
                                    
                                    <div style="margin-bottom: 4px;">
                                        <label id="descriptionLabel" for="description"><?php _e('Description');?></label><br />
                                        <textarea name="description" id="description" cols="20" class="inputbox" style="width: 180px; height:60px;"></textarea>
                                    </div>

                                    <div <?php if (!$this->allowEventReminders): ?>style="display:none;"<?php endif; ?>>
                                        <input type="checkbox" name="reminderToggle" onclick="if (this.checked) document.getElementById('reminderArea').style.display = ''; else document.getElementById('reminderArea').style.display = '';">&nbsp;<label><?php _e('Set Reminder');?></label><br />
                                    </div>
                                    
                                    <div style="display:none;" id="reminderArea">
                                        <div>
                                            <label><?php _e('E-Mail To');?>:</label><br />
                                            <input type="text" id="sendEmail" name="sendEmail" class="inputbox" style="width: 150px" value="<?php $this->_($this->userEmail); ?>" />
                                        </div>
                                        <div>
                                            <label><?php _e('Time');?>:</label><br />
                                            <select id="reminderTime" name="reminderTime" style="width: 150px">
                                                <option value="15">15 <?php _e('min early');?></option>
                                                <option value="30">30 <?php _e('min early');?></option>
                                                <option value="45">45 <?php _e('min early');?></option>
                                                <option value="60">1 <?php _e('hour early');?></option>
                                                <option value="120">2 <?php _e('hours early');?></option>
                                                <option value="1440">1 <?php _e('day early');?></option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>

        </table>
        <input type="submit" class="button" name="submit" id="submit" value="<?php _e("Save") ?>" />&nbsp;
<?php if ($this->isJobOrdersMode): ?>
        <input type="button" class="button" name="close" value="<?php _e("Cancel") ?>" onclick="parentGoToURL('<?php echo(osatutil::getIndexName()); ?>?m=joborders&amp;a=show&amp;jobOrderID=<?php echo($this->selectedJobOrderID); ?>');" />
<?php else: ?>
        <input type="button" class="button" name="close" value="<?php _e("Cancel") ?>" onclick="parentGoToURL('<?php echo(osatutil::getIndexName()); ?>?m=candidates&amp;a=show&amp;candidateID=<?php echo($this->candidateID); ?>');" />
<?php endif; ?>
    </form>

    <script type="text/javascript">
        document.changePipelineStatusForm.activityNote.focus();
    </script>

<?php else: ?>
    <?php if (!$this->changesMade): ?>
        <p><?php _e('No changes have been made.');?></p>
    <?php else: ?>
         <?php if (!$this->onlyScheduleEvent): ?>
            <?php //FIXME: E-mail stuff. ?>
            <?php if ($this->statusChanged): ?>
                <p><?php _e('The candidate\'s status has been changed from');?> <span class="bold"><?php $this->_($this->oldStatusDescription); ?></span> <?php _e('to');?> <span class="bold"><?php $this->_($this->newStatusDescription); ?></span>.</p>
            <?php else: ?>
                <p><?php _e('The candidate\'s status has not been changed.');?></p>
            <?php endif; ?>

            <?php if ($this->activityAdded): ?>
                <?php if (!empty($this->activityDescription)): ?>
                    <p><?php _e('An activity entry of type');?> <span class="bold"><?php $this->_($this->activityType); ?></span> <?php _e('has been added with the following note');?>: &quot;<?php echo($this->activityDescription); ?>&quot;.</p>
                <?php else: ?>
                    <p><?php _e('An activity entry of type');?> <span class="bold"><?php $this->_($this->activityType); ?></span> <?php _e('has been added with no notes.');?></p>
                <?php endif; ?>
            <?php else: ?>
                <p><?php _e('No activity entries have been added.');?></p>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php echo($this->eventHTML); ?>

    <?php echo($this->notificationHTML); ?>

    <form>
<?php if ($this->isJobOrdersMode): ?>
        <input type="button" name="close" class="button" value="<?php _e("Close"); ?>" onclick="parentGoToURL('<?php echo(osatutil::getIndexName()); ?>?m=joborders&amp;a=show&amp;jobOrderID=<?php echo($this->regardingID); ?>');" />
<?php else: ?>
        <input type="button" name="close" class="button" value="<?php _e("Close"); ?>" onclick="parentGoToURL('<?php echo(osatutil::getIndexName()); ?>?m=candidates&amp;a=show&amp;candidateID=<?php echo($this->candidateID); ?>');" />
<?php endif; ?>
    </form>
<?php endif; ?>

    </body>
</html>
