<?php

class FormLogin extends Form
{
        public static function formNode($action = '')
        {
                return self::fieldSet(__CLASS__, 'post', $action, array(
                        'i_login_type' => array('type' => 'hidden', 'value' => 'html'),
                        'i_username' => array('type' => 'text'),
                        'i_password' => array('type' => 'password')
                ), 'Login');
        }

        private static function process()
        {
                unset($_SESSION['login']);

                if (!isset($_POST['login_type']))
                        return;

                switch ($_POST['login_type']) {
                case 'html':
                        if (isset($_POST['username']) && $_POST['username'] == USER && isset($_POST['password']) && sha1($_POST['username'].$_POST['password']) == SALTED_PASS)
                                $_SESSION['login'] = USER;
                        break;
                case 'js':
                        if (isset($_POST['username']) && $_POST['username'] == USER && isset($_SESSION['salt']) && isset($_POST['password']) &&
                            sha1($_SESSION['salt'].SALTED_PASS) == $_POST['password'])
                                $_SESSION['login'] = USER;
                        break;
                default:
                        throw new FormException('Unknown login_type: %s', $_POST['login_type']);
                }
        }

        public static function resultNode()
        {
                self::process();

                return new Node('p', isset($_SESSION['login'])? 'Logged in as '.$_SESSION['login'] : 'Login failed');
        }

        public static function resultJson()
        {
                self::process();

                # salt the session to ensure that replay attacks fail
                Session::salt();

                self::json(isset($_SESSION['login'])? array('status' => true, 'username' => $_SESSION['login']) : array());
        }
}

class FormPost extends Form
{
        public static function formNode($action = '')
        {
                $b = NULL;
                if (isset($_GET['blog']) && !empty($_GET['blog']))
                        $b = TLO::getObject(SSql::instance(), 'Blog', array($_GET['blog']));

                return self::fieldSet(__CLASS__, 'post', $action, array(
                        'i_id'        => array('type'  => 'hidden',
                                               'value' => $b? $b->getId() : ''),
                        'i_title'     => array('value' => $b? $b->title : '',
                                               'size'  => 40),
                        't_content'   => array('value' => $b? $b->getContent() : NULL,
                                               'cols'  => 55,
                                               'rows'  => 10),
                        'c_published' => $b && $b->published? array('checked' => 'checked') : array()
                ), 'Post');
        }

        private static function process()
        {
                if (!self::keysExist($_SESSION, array('login')) || !self::keysExist($_POST, array('id', 'title', 'content')))
                        return false;

                if (empty($_POST['id'])) {
                        if (empty($_POST['title']) || empty($_POST['content']))
                                return false;

                        $b = TLO::newObject(SSql::instance(), 'Blog', array('Blog' => array('title' => $_POST['title'])));
                } else {
                        $b = TLO::getObject(SSql::instance(), 'Blog', array($_POST['id']));
                }

                $b->title = stripslashes($_POST['title']);
                $b->setContent(stripslashes($_POST['content']));

                $old_published = $b->published;
                $b->published = isset($_POST['published'])? 1 : 0;

                if ($old_published == 0 && $b->published == 1)
                        $b->setPublishedDatetime();

                $b->write(SSql::instance());

                $_GET['blog'] = $b->getId(); # set the blog id in the global _GET for use by formNode()

                $dummy = Node::dummy(NULL, Node::INVISIBLE);
                Publish::blog($dummy, $b);

                return $dummy;
        }

        public static function resultNode()
        {
                if (!($dummy = self::process()))
                        return Node::p('Processing failed');

                $dummy->addChild(NodeTpl::hook('spacer'));

                $dummy->addChild(self::formNode());
                return $dummy;
        }

        public static function resultJson()
        {
                self::process();
                self::json(array());
        }
}

class FormEditPost extends Form
{
        public static function formNode($action = '', $class = __CLASS__, $p_blog = NULL)
        {
                if (!$p_blog)
                        return;

                $blogs = array();
                while ($blog = $p_blog->fetch()) {
                        $o = new stdClass();
                        $o->val = $blog->getId();
                        $o->name = sprintf("%s %s", $blog->created, $blog->title);
                        $blogs[] = $o;
                }

                return self::fieldSet($class, 'get', $action, array(
                        's_blog:'.json_encode($blogs) => array()
                ), 'Edit');
        }

        private static function process()
        {
        }

        public static function resultNode()
        {
        }

        public static function resultJson()
        {
        }
}

class FormEditPublishedBlogs extends FormEditPost
{
        public static function formNode($action = '')
        {
                return parent::formNode($action, __CLASS__, Blog::published(SSql::instance()));
        }

        public static function resultNode()
        {
                return parent::formNode('', __CLASS__, Blog::published(SSql::instance()));
        }
}

class FormEditUnpublishedBlogs extends FormEditPost
{
        public static function formNode($action = '')
        {
                return parent::formNode($action, __CLASS__, Blog::unpublished(SSql::instance()));
        }

        public static function resultNode()
        {
                return parent::formNode('', __CLASS__, Blog::unpublished(SSql::instance()));
        }
}

?>
