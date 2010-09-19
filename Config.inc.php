<?php

header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');
session_start();
error_reporting(E_ALL);

function __errorHandler($errno, $errstr, $errfile, $errline)
{
        $errors = array(1 => 'E_ERROR', 2 => 'E_WARNING', 4 => 'E_PARSE', 8 => 'E_NOTICE', 16 => 'E_CORE_ERROR', 32 => 'E_CORE_WARNING', 64 => 'E_COMPILE_ERROR', 128 => 'E_COMPILE_WARNING', 256 => 'E_USER_ERROR', 512 => 'E_USER_WARNING', 1024 => 'E_USER_NOTICE', 2048 => 'E_STRICT', 4096 => 'E_RECOVERABLE_ERROR');
        throw new Exception(sprintf('Handling %s: %s in %s:%s.', $errors[$errno], $errstr, $errfile, $errline));
}
set_error_handler('__errorHandler');

date_default_timezone_set('UTC');

define('USER', 'adam');
define('SALTED_PASS', 'e06706382c3b8163d5946eed9480a1f04e4de683');
define('SALT', 'ahri.net');

define('DATE_FORMAT', 'l \t\h\e jS \o\f F, o');
define('DATETIME_FORMAT', 'H:i \o\n l \t\h\e jS \o\f F, o');

define('RSS_DATE_FORMAT', 'D, d M o H:i:s \U\T');
define('W3C_DATE_FORMAT', 'Y-m-d\TH:i:sP'); # http://www.w3.org/TR/NOTE-datetime
define('MYSQL_DATETIME_FORMAT', 'Y-m-d H:i:s');
define('ATOM_TAG_DATE_FORMAT', 'Y-m-d');
define('ATOM_TAG_URI', 'flockingmonkeys.net');

require_once('lib/Exception.classes.php');
require_once('lib/TLO.classes.php');
require_once('lib/SSql.classes.php');
require_once('lib/Node.classes.php');
require_once('lib/NodeTpl.classes.php');

require_once('Form.classes.php');
require_once('Forms.inc.php');
require_once('Publish.inc.php');

/*if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('#MSIE ([1234567])\.#', $_SERVER['HTTP_USER_AGENT']))
        die('You seem to be using an old version of Internet Explorer. <a href="http://www.microsoft.com/windows/internet-explorer/default.aspx">Upgrade</a> to at least version 8 to view this site. Or don\'t. I don\'t care.');*/

require_once('Template.inc.php');

SSql::setup('sqlite:/var/www/ahri.net/www/db.sq3');
#SSql::exec("SET NAMES 'utf8'"); # MySQL UTF-8 setting
SSql::exec('PRAGMA encoding = "UTF-8"'); # SQLite UTF-8 setting

require_once('Schema.inc.php');
Blog::$allowed_tags = 'strong, b, em, i, p, p style, address, pre, h1, h2, h3, h4, h5, h6, ul, li, ol, sub, sup, img, img style, img src, img alt, img width, img height, a, a href';

class Session
{
        public static function salt()
        {
                list($usec, $sec) = explode(' ', microtime());
                mt_srand((float) $sec + ((float) $usec * 100000));

                $_SESSION['salt'] = sha1(mt_rand());
        }

        public static function json_salt()
        {
                $o = new stdClass();
                $o->session_salt = $_SESSION['salt'];

                echo json_encode($o);
        }

        public static function check_login()
        {
                if (!isset($_SESSION['login']))
                        throw new Exception('Not logged in');
        }
}

NodeTpl::hook('spacer', Node::div(NULL, Node::NOT_SELF_CLOSING));
NodeTpl::hook('spacer')->class = 'spacer';
NodeTpl::hook('spacer')->hr();

?>
