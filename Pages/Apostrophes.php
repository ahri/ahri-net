<?php

NodeTpl::variable('title', 'Five Rules pertaining to Apostrophes');

$content = NodeTpl::hook('content');
Node::stripperHtmlFile($content,
                       'apostrophes.html',
                        array('h1', 'h4', 'ol', 'ul', 'li', 'strong', 'br'),
                        array('b' => 'strong'),
                        false);

foreach ($content as $node)
        if ($node instanceof Node) {
                $tag = $node->_tag();
                if ($tag == 'h1')
                        $heading = $node;
        }

if ($heading)
        $content->removeChild($heading);

?>
