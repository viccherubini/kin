<?php require_once(__DIR__.'/header.html.php'); ?>

<p>This is the rest of the view.</p>

<p><a href="<?php echo $this->url('abc', 'def', 'blah', array('id' => '10'), true); ?>">click here to continue</a></p>