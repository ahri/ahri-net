<?php

NodeTpl::root($html = new Node('html'));

NodeTpl::hook('head', $head = $html->head());
NodeTpl::hook('body', $body = $html->body());

NodeTpl::hook('html_title', $head->title(NULL, true));

$charset = $head->meta();
$http_equiv = 'http-equiv';
$charset->$http_equiv = 'Content-Type';
$charset->content = 'text/html; charset=UTF-8';

# stylesheets
if (!preg_match('#Android#', $_SERVER['HTTP_USER_AGENT'])) {
        foreach (array('http://fonts.googleapis.com/css?family=Crimson+Text',
                       'http://fonts.googleapis.com/css?family=Droid+Sans+Mono',
                       'http://fonts.googleapis.com/css?family=Lobster',
                       'http://static.ahri.net/css/ahri.net.css') as $css_file) {
                $link = $head->link();
                $link->rel = 'stylesheet';
                $link->type = 'text/css';
                $link->href = $css_file;
                $link->media = 'screen';
        }
}

$header = $body->div(NULL, Node::NOT_SELF_CLOSING);
$header->id = 'header';

$site_title = $header->div();
$site_title->id = 'site-title';
/*$site-title->pre(<<<EOF
                                                .
                      .uef^"                   @88>
                    :d88E          .u    .     %8P
              u     `888E        .d88B :@8c     .
           us888u.   888E .z8k  ="8888f8888r  .@88u
        .@88 "8888"  888E~?888L   4888>'88"  ''888E`
        9888  9888   888E  888E   4888> '      888E
        9888  9888   888E  888E   4888>        888E
        9888  9888   888E  888E  .d888L .+     888E
        9888  9888   888E  888E  ^"8888*"      888&
        "888*""888" m888N= 888>    "Y"         R888"           :8
         ^Y"   ^Y'   `Y"   888        u.    u.  ""            .88
                          J88"      x@88k u@88c.      .u      :888ooo
                          @%       ^"8888""8888"   ud8888.  -*8888888
                        :"           8888  888R  :888'8888.   8888
                                     8888  888R  d888 '88%"   8888
                                     8888  888R  8888.+"      8888
                             .       8888  888R  8888L       .8888Lu=
                             .@8c   "*88*" 8888" '8888c. .+  ^%888*
                            '%888"    ""   'Y"    "88888%      'Y"
                              ^*                    "YP'
EOF
, Node::UNMANGLED)->id = 'site-title-ascii';*/

if (preg_match('#Links#', $_SERVER['HTTP_USER_AGENT'])) {
        $site_title->pre(<<<EOF
                       .__          .__                  __
                _____  |  |_________|__|    ____   _____/  |_
                \__  \ |  |  \_  __ \  |   /    \_/ __ \   __\
                 / __ \|   Y  \  | \/  |  |   |  \  ___/|  |
                (____  /___|  /__|  |__| /\___|  /\___  >__|
                     \/     \/           \/    \/     \/
EOF
, Node::UNMANGLED)->id = 'site-title-ascii';
} else {
        $site_title->a('Ahri.net')->href = 'http://www.ahri.net';
}

NodeTpl::hook('links', $links = $header->div());
$links->id = 'links';
$links->a('[Blog]')->href = 'http://www.ahri.net';
$links->a('[Photos]')->href = 'http://photos.ahri.net';
$links->a('[Code Repos]')->href = 'http://github.com/ahri';
$links->a('[Regex Guide]')->href = '/regex';
$links->a('[Apostrophe Guide]')->href = '/apostrophes';
$links->a('[Music]')->href = '/music';
$links->a('[Login]')->href = '/login';

NodeTpl::hook('page_title', $title = $body->h1());
$title->id = 'page-title';

NodeTpl::hook('content', $content = $body->div(NULL, Node::NOT_SELF_CLOSING));
$content->id = 'content';

NodeTpl::hook('footer', $footer = $body->div(NULL, Node::NOT_SELF_CLOSING));
$footer->id = 'footer';
$footer->hr();

$feeds = $footer->p();
$feeds->id = 'feeds';
#$feeds->a('[Atom]')->href = '/feeds/atom.php';
#$feeds->a(' [RSS]', Node::UNSTRIPPED)->href = '/feeds/rss.php';
$feed = $feeds->a('[Feed]');
$feed->href = 'http://www.ahri.net/feed';
$feed->rel = 'alternate';
$feed->type = 'application/rss+xml';

$feed = $head->link();
$feed->href = 'http://www.ahri.net/feed';
$feed->rel = 'alternate';
$feed->type = 'application/rss+xml';
$feed->title = 'ahri.net rss feed';

NodeTpl::hook('stats', $stats = $footer->p());
$stats->id = 'stats';

$p = $footer->p('Copyright © ');
$p->id = 'copyright';
$p->a('Adam Piper')->href = 'mailto:adam@ahri.net';
$p->addText(' 2010');

function google_analytics(Node $parent, $code)
{
        $ga = $parent->script(<<<GOOGLE
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
GOOGLE
        , Node::SCRIPT_EMBEDDED);
        $ga->type = 'text/javascript';

        $ga = $parent->script(<<<GOOGLE
try {
    var pageTracker = _gat._getTracker("$code");
    pageTracker._trackPageview();
} catch(err) {}
GOOGLE
        , Node::SCRIPT_EMBEDDED);
        $ga->type = 'text/javascript';
}

# javascript
foreach (array('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
               'http://static.ahri.net/js/jquery.form.js',
               'http://static.ahri.net/js/jquery.sha1.js',
               'http://static.ahri.net/js/jquery.google.js',
               'http://static.ahri.net/js/ckeditor/ckeditor.js',
               'http://static.ahri.net/js/ckeditor/adapters/jquery.js',
               'http://www.google.com/jsapi?key=ABQIAAAAunVZweGKq62BHJIOSFFdHBTFfuc3jwrDUWSqfMC4R4HHVNtxyBSuwv2pD6hSc22xyMK82Ahu9scOeg',
               'http://static.ahri.net/js/ahri.net.js') as $js_file) {
        $script = $body->script(NULL, Node::SCRIPT_INCLUDE);
        $script->type = 'text/javascript';
        $script->src = $js_file;
}

google_analytics($body, 'UA-15801531-1');

NodeTpl::content('html_title', 'ahri.net - {title}');
NodeTpl::content('page_title', '> {title}');

NodeTpl::postProcess(function () {
        if (isset($_SESSION['login']))
                NodeTpl::hook('links')->a('[Post]')->href = '/post';
});

NodeTpl::postProcess(function () {
        NodeTpl::hook('stats')->addText(sprintf(' Peak Memory Usage: %.2fMB', memory_get_peak_usage() / pow(1024, 2)));
});

?>
