<?php

require_once('Config.inc.php');

# TODO: configurable actions via the database
try {
        $file = null;
        switch(preg_replace(array('#^/#', '#\?.*#'), '', $_SERVER['REQUEST_URI'])) {
        case '':
        case 'blog':
                $file = 'Pages/Blog.php';
                break;
        case 'regex':
                $file = 'Pages/Regex.php';
                break;
        case 'apostrophes':
                $file = 'Pages/Apostrophes.php';
                break;
        case 'login':
                $file = 'Pages/Login.php';
                break;
        case 'post':
                $file = 'Pages/Post.php';
                break;
        case 'music':
                $file = 'Pages/Music.php';
                break;
        case 'test':
                $file = 'Pages/Test.php';
                break;
        default:
                header('HTTP/1.0 404 Not Found');
                $file = 'Pages/404.php';
        }

        if ($file) {
                $result = NULL;
                ob_start();
                include($file);
                $result = ob_get_contents();
                ob_end_clean();
                if (!empty($result))
                        throw new SPFException('Failed including file %s', $file);
        }

} catch (Exception $e) {
        NodeTpl::variable('title', 'Exceptional!');
        $content = NodeTpl::hook('content');
        $content->removeChildren();
        $content->h3('Message:');
        $msg = $content->p();
        Node::stripper($msg, str_replace("href='", "href='http://php.net/", $e->getMessage()), array('a href'));
        $content->h3('Trace:');
        $content->pre($e->getTraceAsString(), Node::UNMANGLED);
}

NodeTpl::output();

?>
