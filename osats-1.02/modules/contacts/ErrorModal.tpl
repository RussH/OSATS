<?php /* $Id: ErrorModal.tpl 789 2006-09-08 07:41:24Z will $ */ ?>
<?php TemplateUtility::printModalHeader(__('Contacts')); ?>
    <table>
        <tr>
            <td width="3%">
                <img src="images/contact.gif" width="24" height="24" border="0" alt="Contacts" style="margin-top: 3px;" />&nbsp;
            </td>
            <td><h2><?php _e('Contacts')?>: <?php _e('Error')?></h2></td>
        </tr>
    </table>

    <p class="fatalError">
        <?php _e('A fatal error has occurred.')?><br />
        <br />
        <?php echo($this->errorMessage); ?>
    </p>
    </body>
</html>