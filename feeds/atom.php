<?php

require_once('../Config.inc.php');
header('Content-Type: application/atom+xml');

echo '<?xml version="1.0"?>'."\n";

$feed = Node::feed();
$feed->xmlns = 'http://www.w3.org/2005/Atom';
$feed->title('ahri.net', true);
$feed->subtitle('A (mostly) tech-related blog', true);
$link = $feed->link();
$link->href = 'http://ahri.net/feeds/all.atom.php';
$link->rel = 'self';
$link = $feed->link();
$link->href = 'http://ahri.net/';

$first = true;
foreach (Blog::published(SSql::instance()) as $blog) {
        if ($first) {
                $first = false;

                $feed->updated($blog->getDate()->format(W3C_DATE_FORMAT), true);

                $author = $feed->author();
                $author->name('Adam Piper', true);
                $author->email('adam@ahri.net', true);
                $feed->id(sprintf('tag:%s,%s:/feeds/all.atom.php', ATOM_TAG_URI, date(ATOM_TAG_DATE_FORMAT)), true);
        }
        $entry = $feed->entry();
        $entry->title($blog->title, true);
        $link = $entry->link();
        $link->href = sprintf('http://ahri.net/blog?id=%d', $blog->getId());
        $entry->id(sprintf('tag:%s,%s:%d', ATOM_TAG_URI, $blog->getDate()->format(ATOM_TAG_DATE_FORMAT), $blog->getId()), true);
        $entry->updated($blog->getDate()->format(W3C_DATE_FORMAT), true);
        $content = Node::dummy(NULL, Node::INVISIBLE);
        $blog->appendContent($content);
        $summary = $entry->summary($content);
        $summary->type = 'html';
}

echo $feed;

?>
