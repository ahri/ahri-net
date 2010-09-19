<?php

NodeTpl::variable('title', 'Testing, testing, 1, 2, 3...');
NodeTpl::hookConvert();
#NodeTpl::hook('content')->pre(print_r(NodeTpl::hook('footer'), true), Node::UNSTRIPPED);

class NodeStrip2
{
        private $all_allowed = false;
        private $allowed_tags = array();
        private $allowed_spec = array();
        private $replacements = array();

        public function __construct(Node $node, $input, $allowed, array $replacements)
        {
                if (empty($input))
                        return;

                $d = new DOMDocument();
                $d->strictErrorChecking = false;
                try {
                        $d->loadHTML($input);
                } catch (Exception $e) {
                        # not interested in any exceptions raised
                }
                $input = $d;
                unset($d);

                # hacky, but DOMDocument adds <html><body>blah</body></html> around the given HTML, which I want to skip
                $input = $input->childNodes->item(1);
                $input = $input->childNodes->item(0);

                if (is_string($allowed) && $allowed == '*')
                        $this->all_allowed = true;
                else {
                        # build up the allowed tags and specs
                        foreach ($allowed as $spec) {
                                $spec = explode(' ', $spec);
                                if (!isset($this->allowed_spec[$spec[0]])) {
                                        $this->allowed_tags[] = $spec[0];
                                        $this->allowed_spec[$spec[0]] = array();
                                }

                                if (isset($spec[1]) && !in_array($spec[1], $this->allowed_spec[$spec[0]], true))
                                        $this->allowed_spec[$spec[0]][] = $spec[1];
                        }
                }

                $this->replacements = $replacements;
                $this->stripper($node, $input);
                unset($this);
        }

        private function stripper(Node $node, $input)
        {
                if (!$input->hasChildNodes()) {
                        return;
                }

                foreach ($input->childNodes as $child) {
                        switch (get_class($child)) {
                                case 'DOMText':
                                        $node->addText($child->nodeValue);
                                        break;
                                case 'DOMElement':
                                        $tag = strtolower($child->tagName);

                                        # replace if required
                                        if (isset($this->replacements[$tag]))
                                                $tag = $this->replacements[$tag];

                                        if ($this->all_allowed || in_array($tag, $this->allowed_tags, true)) {
                                                $childnode = $node->$tag();

                                                # iterate over attrs
                                                if ($child->hasAttributes()) {
                                                        foreach ($child->attributes as $attr) {
                                                                $attr_name = strtolower($attr->nodeName);

                                                                if (!$this->all_allowed && !in_array($attr_name, $this->allowed_spec[$tag], true))
                                                                        continue;

                                                                $attr_val  = $attr->nodeValue;
                                                                $childnode->$attr_name = $attr_val;
                                                        }
                                                }
                                                self::stripper($childnode, $child);
                                        } else {
                                                self::stripper($node, $child);
                                        }
                                        break;
                        }
                }
        }
}

$dummy = Node::dummy(NULL, Node::INVISIBLE);
new NodeStrip2($dummy, file_get_contents('Template.html'), '*', array());
NodeTpl::hook('content')->pre(print_r($dummy, Node::UNSTRIPPED);

?>
