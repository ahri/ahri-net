<?php

NodeTpl::variable('title', 'Everything looks perfect from far away');

if (isset($_GET['id']))
        Publish::blog(NodeTpl::hook('content'), TLO::getObject(SSql::instance(), 'Blog', array($_GET['id'])));
else {
        $content = NodeTpl::hook('content');
        $first = true;

        foreach (Blog::published(SSql::instance()) as $blog) {
                if (!$first)
                        $content->addChild(NodeTpl::hook('spacer'));

                Publish::blog($content, $blog);
                $first = false;
        }
}

?>
