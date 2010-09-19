<?php

class Blog extends TLO
{
        public static $allowed_tags = '';

        public $created;

        public $title;
        protected $content;

        public $published = 0;

        private $node = NULL;
        private $date = NULL;

        public function __setup()
        {
                parent::__setup();

                if (empty($this->created))
                        $this->setPublishedDatetime();

                $this->tags = explode(', ', self::$allowed_tags);
                $this->parseNodes();
                $this->date = DateTime::createFromFormat(MYSQL_DATETIME_FORMAT, $this->created);
        }

        public function setPublishedDatetime()
        {
                $this->created = date('Y-m-d H:i:s');
        }

        public static function published($db, $page = 1, $name = NULL)
        {
                $query = new TLOQuery();
                $query->where('published = 1 ORDER BY created DESC');
                return TLO::getObjects($db, __CLASS__, $query);
        }

        public static function unpublished($db, $page = 1, $name = NULL)
        {
                $query = new TLOQuery();
                $query->where('published = 0 ORDER BY created ASC');
                return TLO::getObjects($db, __CLASS__, $query);
        }

        private function parseNodes()
        {
                $this->node = new Node('dummy', NULL, Node::INVISIBLE);
                Node::stripper($this->node, $this->content, $this->tags);
                $this->content = (string) $this->node;
                return $this->node;
        }

        public function setContent($content)
        {
                $this->content = $content;
                $this->parseNodes();
        }

        public function getContent()
        {
                return $this->content;
        }

        public function appendContent(Node $parent)
        {
                foreach ($this->node as $n)
                        $parent->addChild($n);

                return $parent;
        }

        public function getDate()
        {
                return $this->date;
        }
}

?>
