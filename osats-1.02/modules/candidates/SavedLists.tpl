<?php /* $Id: SavedLists.tpl 2571 2007-06-20 20:39:38Z brian $ */ ?>
<?php TemplateUtility::printHeader(__('Candidates'), array('js/submodal/subModal.js', 'js/highlightrows.js', 'js/export.js', 'js/listEditor.js', 'js/dataGrid.js')); ?>
<?php 
if (MYTABPOS == 'top') {
	osatutil::TabsAtTop();
	TemplateUtility::printTabs($this->active);
}
?>
    <div id="main">
        <?php TemplateUtility::printQuickSearch(); ?>

        <div id="contents">
            <table width="100%">
                <tr>
                    <td width="3%">
                        <img src="images/candidate.gif" width="24" height="24" border="0" alt="Candidates" style="margin-top: 3px;" />&nbsp;
                    </td>
                    <td><h2><?php _e('Candidates');?>: <?php _e('Saved Lists');?></h2></td>
                    <td align="right">
                        <form name="candidatesViewSelectorForm" id="candidatesViewSelectorForm" action="<?php echo(osatutil::getIndexName()); ?>" method="get">
                            <input type="hidden" name="m" value="candidates" />
                            <input type="hidden" name="a" value="savedLists" />

                            <table class="viewSelector">
                                <tr>
                                    <td valign="top" align="right" nowrap>
                                        <?php $this->dataGrid->printNavigation(); ?>
                                    </td>
                                    <td>
                                        <select name="view" id="savedListSelect" onChange=" if (this.value != 'nullline') { <?php echo $this->dataGrid->getJSAddFilter(__('Saved Lists'), '=#', 'this.value'); ?> } else {<?php echo $this->dataGrid->getJSRemoveFilter(__('Saved Lists')); ?> } if (this.value == 'nullline') { this.value = '(none)'; }" >
                                            <option value="nullline"><?php _e('Saved Lists');?> (<?php _e('Select to View');?>):</option>
                                            <option value="nullline">------------------------</option>
                                           <?php foreach ($this->savedListsRS as $row => $rowIndex) : ?>
                                                <option value="<?php echo(htmlspecialchars($this->savedListsRS[$row]['description'])) ?>" <?php if($this->dataGrid->getFilterValue(__('Saved Lists')) == $this->savedListsRS[$row]['description']): ?>selected<?php endif; ?>><?php echo($this->savedListsRS[$row]['description']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td valign="top" align="right" nowrap="nowrap">
                                        <input type="checkbox" name="onlyMyCandidates" id="onlyMyCandidates" <?php if ($this->dataGrid->getFilterValue('OwnerID') ==  $this->userID): ?>checked<?php endif; ?> onclick="<?php echo $this->dataGrid->getJSAddRemoveFilterFromCheckbox('OwnerID', '==',  $this->userID); ?>" />
                                        <?php _e('Only My Candidates');?>&nbsp;
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </td>
               </tr>
            </table>

            <?php TemplateUtility::printPopupContainer(); ?>
            
            <p class="note">
                <span style="float:left;"><?php _e('Candidates');?> <?php _e('Saved Lists');?> - <?php _e('Page');?> <?php echo($this->dataGrid->getCurrentPageHTML()); ?></span>
                <span style="float:right;"><?php $this->dataGrid->drawShowFilterControl(); ?></span>&nbsp;
            </p>

            <?php $this->dataGrid->drawFilterArea(); ?>
            <?php $this->dataGrid->draw();  ?>

            <div style="display:block;">
                <span style="float:left;">
                    <?php $this->dataGrid->printActionArea(); ?>
                </span>
                <span style="float:right;">
                    <?php $this->dataGrid->printNavigation(true); ?>
                </span>&nbsp;
            </div>

        </div>
<?php
if (MYTABPOS == 'bottom') 
{
    
	TemplateUtility::printTabs($this->active);
	?>
	</div>
    <div id="bottomShadow"></div>
    
    <?php 
	osatutil::TabsAtBottom();
}else{
	?>
	</div>
    <div id="bottomShadow"></div>
    <?php 
}
?>
<?php TemplateUtility::printFooter(); 
		
?>
