<?php

class Publish
{
        public static function blog($root, Blog $o)
        {
                $post = $root->div();
                $post->class = 'post';
                $id = $o->getId();

                $title = $post->div(NULL, Node::INLINE);
                $title->class = 'title';
                $title->h4($o->title)->class = 'text';

                $post->div($o->getDate()->format(DATETIME_FORMAT), Node::INLINE)->class = 'date';

                $content = $post->div();
                $content->class = 'content';
                $o->appendContent($content);
        }
}

?>
