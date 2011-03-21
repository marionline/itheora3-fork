<?php
include_once('lib/functions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

	<style type="text/css">
	    .itheora3-fork {
		padding: 5px;
		background-color: #EAD9BC;
	    }
	</style>

        <title>ITheora, I really broadcast myself - New Itheora fork test</title>
    </head>
    <body>
	<p>
	    Here I'm using createObjectTag() helper function to output the html code.
	    <br />
	    I don't pass any parameters so the output use default video with the width and heigth take from poster image:
	    <br />
	    <code><?php highlight_string('<?php echo createObjectTag(array(\'useFilesInCloud\' => true)); ?>'); ?></code>
	    <br />
	    <?php echo createObjectTag(array('useFilesInCloud' => true)); ?>
	</p>
	<p>
	    I can use directly html code and set width and height as I want:
	    <br />
	    <code>
		<?php highlight_string('<object id="example" name="example" class="itheora3-fork" type="application/xhtml+xml" data="itheora.php?w=640&amp;h=264" style="width: 640px; height: 264px;">
		</object>'); ?>
	    </code>
	    <br />
	    <object id="example" name="example" class="itheora3-fork" type="application/xhtml+xml" data="itheora.php?w=640&amp;h=264" style="width: 640px; height: 264px;">
	    </object>
	</p>
	<p>
	    Or use createObjectTag() function helper and set my preferer width and heigth:
	    <br />
	    <code>
		<?php highlight_string('<?php echo createObjectTag(array(\'video\' => \'example\', \'width\' => 300, \'height\' => 400, \'useFilesInCloud\' => true)); ?>'); ?>
	    </code>
	    <br />
	    <?php echo createObjectTag(array('video' => 'example', 'width' => 300, 'height' => 400, 'useFilesInCloud' => true)); ?>
	</p>
	<p>
	    Or just use createObjectTag() function with just video name, the function retrive alone the width and height to use.
	    <br />
	    <code>
		<?php highlight_string('<?php echo createObjectTag(array(\'video\' => \'oceans-clip\', \'useFilesInCloud\' => true)); ?>'); ?>
	    </code>
	    <br />
	    <?php echo createObjectTag(array('video' => 'oceans-clip', 'useFilesInCloud' => true)); ?>
	</p>
	<p>
	    If no file are found we have the error video:
	    <br />
	    <code>
		<?php highlight_string('<?php echo createObjectTag(array(\'video\' => \'ocean-clip-not-exist\', \'useFilesInCloud\' => true)); ?>'); ?>
	    </code>
	    <br />
	    <?php echo createObjectTag(array('video' => 'ocean-clip-not-exist', 'useFilesInCloud' => true)); ?>
	</p>
    </body>
</html>
