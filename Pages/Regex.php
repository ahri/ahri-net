<?php

NodeTpl::variable('title', 'RegEx Introduction and Quick Reference');

$content = NodeTpl::hook('content');
Node::stripperHtmlFile($content,
                       'regex.html',
                       array('h1', 'div', 'p', 'br', 'h3', 'h4', 'em', 'ul', 'li', 'pre', 'a href', 'a name'),
                       array('i' => 'em'),
                       false);

$removals = array();
foreach ($content as $node)
        if ($node instanceof Node) {
                $tag = $node->_tag();
                if ($tag == 'h1' || $tag == 'div' || $tag == 'br')
                        $removals[] = $node;
        }

foreach ($removals as $node)
        $content->removeChild($node);

?>
