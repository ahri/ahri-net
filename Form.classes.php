<?php

class FormException extends SPFException {}

interface IForm
{
        /** Return a Node tree **/
        public static function formNode($action = '');

        /** Process POST/GET and return a Node tree */
        public static function resultNode();

        /** Process POST/GET and output JSON directly **/
        public static function resultJson();
}

abstract class Form implements IForm
{
        protected static function base($class, $type, $action)
        {
                $f = new Node('form');
                $f->id = $class;
                $f->name = $class;
                $f->method = $type;
                $f->action = $action;
                $f->{'accept-charset'} = 'UTF-8';

                return $f;
        }

        protected static function json($attrs = array())
        {
                $o = new stdClass();
                $o->status = false;

                foreach ($attrs as $k => $v)
                        $o->$k = $v;

                echo json_encode($o);
        }

        protected static function fieldSet($class, $type, $action, $lines = array(), $submit_name = 'Submit')
        {
                $f = self::base($class, $type, $action);

                $d = $f->fieldset();

                $pretty = '';
                foreach (str_split(str_ireplace('form', '', $class)) as $idx => $letter) {
                        if ($idx != 0 && preg_match('#[A-Z]#', $letter))
                                $pretty .= ' ';

                        $pretty .= $letter;
                }
                $d->legend($pretty);

                foreach ($lines as $title => $attrs) {
                        # parse the tile
                        preg_match('#(?<name>[^:]+)(:(?<args>.*))?$#', $title, $m);
                        $title = $m['name'];
                        $args = array();
                        if (isset($m['args']))
                                $args = json_decode($m['args']);
                        $prefix = substr($title, 0, 2);
                        $title = substr($title, 2);

                        switch ($prefix) {
                        case 'i_':
                                $input = new Node('input');
                                $input->type = 'text';
                                break;
                        case 't_':
                                $content = NULL;
                                if (isset($attrs['value']) && !empty($attrs['value'])) {
                                        $content = $attrs['value'];
                                        unset($attrs['value']);
                                }
                                $input = new Node('textarea', $content, Node::NOT_SELF_CLOSING | Node::RESET_INDENT | Node::UNSTRIPPED);
                                break;
                        case 'c_':
                                $input = new Node('input');
                                $input->type = 'checkbox';
                                break;
                        case 'r_':
                                $input = new Node('input');
                                $input->type = 'radio';
                                break;
                        case 's_':
                                $input = new Node('select');
                                $selected = NULL;
                                foreach ($args as $opt) {
                                        $optnode = $input->option();
                                        if (isset($opt->selected) && $opt->selected) {
                                                if ($selected)
                                                        throw new FormException('Trying to set "%s" to SELECTED in form "%s", item "%s". This item already has a default selection set to "%s"', $name, $pretty, $title, $selected->name);

                                                $optnode->selected = 'selected';
                                                $name = $opt->name;
                                                $selected = $opt;
                                        }
                                        $optnode->addText($opt->name);
                                        $optnode->value = $opt->val;
                                }
                                break;
                        default:
                                throw new FormException('Prefix of "%s" is invalid', $prefix);
                        }
                        $input->name = $title;

                        foreach ($attrs as $k => $v)
                                $input->$k = $v;

                        if (isset($attrs['type']) && $attrs['type'] == 'hidden') {
                                $f->addChild($input);
                        } else {
                                $d->label(sprintf('%s:', ucwords($title)), Node::INLINE)->for = $title;
                                $d->br();
                                $d->addChild($input);
                                $d->br();
                        }
                }

                $submit = $f->input();
                $submit->type = 'submit';
                $submit->value = $submit_name;

                return $f;
        }

        public static function postage(Node $node, $class)
        {
                $node->addChild(sizeof($_POST) > 0? call_user_func(array($class, 'resultNode')) : call_user_func(array($class, 'formNode')));
        }

        protected static function keysExist(&$arr, $keys = array())
        {
                foreach ($keys as $key)
                        if (!isset($arr[$key]))
                                return false;

                return true;
        }
}


?>
