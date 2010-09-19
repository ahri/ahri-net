<?php

define('DIR_SEP', '/');
define('MUSIC_DIR', '/share/Music/Collection/CDs');

NodeTpl::variable('title', 'My music collection');

$music = dir(MUSIC_DIR);

$content = NodeTpl::hook('content');

$tmp = $content->p("These are CDs I've bought and ripped to ");
$tmp->a("flac")->href = 'http://en.wikipedia.org/wiki/Flac';
$tmp->addText(" on my home server, and this seems an excellent place to add a bit of an essay on music piracy.");
$content->p("My position on piracy is that it's completely fine; a massive amount of the music I bought was first downloaded illegally and enjoyed prior to being purchased. This included single tracks sent to me by friends as tips, whole albums \"stolen\" even before actual release, and even entire discographies downloaded that swayed me into buying select albums.");
$content->p("It's important that we note a couple of things here;");
$list = $content->ol();
$item = $list->li("I downloaded (\"stole\") music that I later ");
$item->strong("didn't");
$item->addText(" buy");
$item = $list->li("I wouldn't have bought most of the music I now own without having heard it first");
$content->p("Needless to say: I don't think I ever really stole anything. I never took anything away from an artist or record company that stopped them selling that thing to others. The whole concept of \"stealing\" music is completely ludicrous in my opinion. Did I deprive anyone of any sales by downloading that music? No. However I did download Radiohead's \"Hail to the Thief\" album a month before its release... and promptly pre-ordered it on Amazon. It's still sat on my shelf now.");
$content->p("It goes without saying that I think the litigation against downloaders is a misguided attempt to save a dying system (the model that the recording industry clings to, and will watch slip through their fingers). Power to the artists, I say: sell your stuff direct to me -- I and millions of others will buy.");
$content->p("Having said all that, and without further ado, here's the list!");
$content->p("(Note that I replace weird characters with underscores in my directory names, the result of which is passed on to this list.)");

$content->addchild(NodeTpl::hook('spacer'));

$dl = $content->dl();

while ($artist = $music->read()) {
        if ($artist == '.' || $artist == '..')
                continue;

        $artist_dir = implode(DIR_SEP, array(MUSIC_DIR, $artist));

        if (!is_dir($artist_dir))
                continue;

        $dl->dt($artist);

        $albums = dir($artist_dir);
        while ($album = $albums->read()) {
                if ($album == '.' || $album == '..')
                        continue;

                $album_dir = implode(DIR_SEP, array($artist_dir, $album));

                if (!is_dir($album_dir))
                        continue;

                $dl->dd($album);
        }
}

?>
