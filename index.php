<?php
include_once('lib/functions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

        <title>ITheora, I really broadcast myself - New Itheora fork test</title>
    </head>
    <body>
	<p>
	    Here I'm using createObjectTag() helper function to output the html code.
	    <br />
	    I don't pass any parameters so the output use default video with the width and heigth take from poster image:
	    <br />
	    <code><?php highlight_string('<?php echo createObjectTag(); ?>'); ?></code>
	</p>
	<?php echo createObjectTag(); ?>
	<p>
	    I can use directly html code and set width and height like I want:
	    <br />
	    <code>
		<?php highlight_string('<object id="example" name="example" type="application/xhtml+xml" data="itheora.php?w=640&h=264" style="width: 640px; height: 264px;">
		</object>'); ?>
	    </code>
	</p>
	<object id="example" name="example" type="application/xhtml+xml" data="itheora.php?w=640&h=264" style="width: 640px; height: 264px;">
	</object>
	<p>
	    Or use createObjectTag() function helper and set my preferer width and heigth:
	    <br />
	    <code>
		<?php highlight_string('<?php echo createObjectTag(\'example\', 300, 400); ?>'); ?>
	    </code>
	</p>
	<?php echo createObjectTag('example', 300, 400); ?>
	<p>
	    Or just use createObjectTag() function with just video name, the function retrive alone the width and height to use.
	    <br />
	    <code>
		<?php highlight_string('<?php echo createObjectTag(\'oceans-clip\'); ?>'); ?>
	    </code>
	</p>
	<?php echo createObjectTag('oceans-clip'); ?>
	<p>
	    If no file are found we have the error video:
	    <br />
	    <code>
		<?php highlight_string('<?php echo createObjectTag(\'ocean-clip-not-exist\'); ?>'); ?>
	    </code>
	</p>
	<?php echo createObjectTag('ocean-clip-not-exist'); ?>
    </body>
</html>
