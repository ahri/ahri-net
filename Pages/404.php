<?php

$search = 'site:ahri.net ' . urldecode(preg_replace('#^/#', '', $_SERVER['REQUEST_URI']));

NodeTpl::variable('title', 'Oops!');
$content = NodeTpl::hook('content');
$content->h3('To whom it may concern,');
$content->p("I couldn't find what you were looking for and apologise most profusely.");
$p = $content->p("Perhaps I can point you in the direction of a ");
$p->a('Google search')->href = sprintf('http://www.google.com/search?q=%s', urlencode($search));
$p->addText(" that may be able to get us both out of this pickle.");
$content->div(NULL, Node::NOT_SELF_CLOSING)->id = 'result';
$content->p("Best of luck!");

$search = str_replace('"', '\"', $search);

NodeTpl::hook('body')->script(<<<JS
google.load("search", "1");
$(document).ready(function() {
        var searchControl = new google.search.SearchControl(),
            options = new google.search.SearcherOptions();

        options.setExpandMode(google.search.SearchControl.EXPAND_MODE_OPEN);
        options.setRoot(document.getElementById("result"));

        searchControl.addSearcher(new google.search.WebSearch(), options);
        searchControl.draw();
        searchControl.execute("$search");
});
JS
, Node::SCRIPT_EMBEDDED)->type = 'text/javascript';
?>
