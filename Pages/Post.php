<?php

Session::check_login();
NodeTpl::variable('title', 'Post some drivel');
$content = NodeTpl::hook('content');
$spacer = NodeTpl::hook('spacer');
Form::postage($content, 'FormPost');
$content->addChild($spacer);
Form::postage($content, 'FormEditUnpublishedBlogs');
Form::postage($content, 'FormEditPublishedBlogs');

?>
