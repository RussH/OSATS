<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo(HTML_ENCODING); ?>">
        <title><?php $this->_($this->siteName); ?> - <?php _e('Careers');?></title>
        <?php global $careerPage; if (isset($careerPage) && $careerPage == true): ?>
            <script type="text/javascript" src="../js/lib.js"></script>
            <script type="text/javascript" src="../js/sorttable.js"></script>
            <script type="text/javascript" src="../js/calendarDateInput.js"></script>
        <?php else: ?>
            <script type="text/javascript" src="js/lib.js"></script>
            <script type="text/javascript" src="js/sorttable.js"></script>
            <script type="text/javascript" src="js/calendarDateInput.js"></script>
        <?php endif; ?>
        <style type="text/css" media="all">
            <?php echo($this->template['CSS']); ?>
        </style>
    </head>
    <body marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" >
    <!-- TOP -->
    <?php echo($this->template[__('Header')]); ?>

    <!-- CONTENT -->
    <?php echo($this->template[__('Content')]); ?>

    <!-- FOOTER -->
    <?php echo($this->template[__('Footer')]); ?>
    <div style="font-size:9px;">
        <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
    </div>
    <div style="text-align:center;">

        <?php /*  */ ?>
        <span style="font-size: 9px;">Powered by</span> <a style="color: #888; position: relative; font-size: 9px; font-weight: normal; text-align: center; left: 0px; top: 0px;" href="http://www.google.com/" target="_blank">OSATS</a>.

    </div>
    <script type="text/javascript">st_init();</script>
    </body>
</html>