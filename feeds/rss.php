<?php

require_once('../Config.inc.php');
header('Content-Type: application/rss+xml');

echo '<?xml version="1.0"?>'."\n";

$rss = Node::rss();
$rss->version = '2.0';
$rss->{'xmlns:atom'} = 'http://www.w3.org/2005/Atom';
$chan = $rss->channel();
$chan->title('ahri.net');
$chan->link('http://ahri.net/');
$chan->description('A (mostly) tech-related blog');
$chan->language('en-gb', true);

$first = true;
foreach (Blog::published(SSql::instance()) as $blog) {
        if ($first) {
                $first = false;

                $chan->pubDate($blog->getDate()->format(RSS_DATE_FORMAT), true);
                $chan->lastBuildDate($blog->getDate()->format(RSS_DATE_FORMAT), true);

                $chan->docs('http://blogs.law.harvard.edu/tech/rss', true);
                $chan->generator('ahri.net feedgen', true);
                $chan->managingEditor('adam@ahri.net (Adam Piper)', true);
                $chan->webMaster('adam@ahri.net (Adam Piper)', true);

                $atom_link = $chan->{'atom:link'}();
                $atom_link->href = 'http://ahri.net/feeds/all.rss.php';
                $atom_link->rel = 'self';
                $atom_link->type = 'application/rss+xml';
        }
        $item = $chan->item();
        $item->title($blog->title, true);
        $item->link(sprintf('http://ahri.net/blog?id=%d', $blog->getId()), true);

        $content = Node::dummy(NULL, Node::INVISIBLE);
        $blog->appendContent($content);
        $item->description($content);

        $item->pubDate($blog->getDate()->format(RSS_DATE_FORMAT), true);
        $item->guid(sprintf('http://ahri.net/blog?id=%d', $blog->getId()), true);
}

echo $rss;

?>
