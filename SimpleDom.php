<?php

class SimpleDom {
    private $doc; 
    private $xpath; 
    private $nodes; 

   
    public function __construct($html, $isFile = false) {
        $this->doc = new DOMDocument();
        @$this->doc->loadHTML($isFile ? file_get_contents($html) : $html, LIBXML_NOWARNING | LIBXML_NOERROR);
        $this->xpath = new DOMXPath($this->doc);
        $this->nodes = [$this->doc]; 
    }

    public static function str_get_html($html) {
        return new self($html);
    }

    public static function file_get_html($filename) {
        return new self($filename, true);
    }

    public function find($selector, $index = null) {
        $xpathQuery = $this->convertCssToXPath($selector);
        $nodes = $this->xpath->query($xpathQuery, $this->nodes[0] ?? $this->doc);

        if ($nodes->length === 0) {
            return null;
        }

        $nodeArray = iterator_to_array($nodes);
        if ($index !== null) {
            $nodeArray = isset($nodeArray[$index]) ? [$nodeArray[$index]] : [];
        }

        $newDom = new self($this->doc->saveHTML());
        $newDom->nodes = $nodeArray;
        return $newDom;
    }

   
    private function convertCssToXPath($selector) {
        
        $selector = trim($selector);
        if (preg_match('/^([a-zA-Z0-9]+)?(\.[a-zA-Z0-9-]+)?(#[a-zA-Z0-9-]+)?(\[[^\]]+\])?$/', $selector, $matches)) {
            $tag = $matches[1] ?? '*';
            $class = isset($matches[2]) ? substr($matches[2], 1) : '';
            $id = isset($matches[3]) ? substr($matches[3], 1) : '';
            $attr = isset($matches[4]) ? $matches[4] : '';
            $div = isset($matches[5]) ? substr($matches[5], 1) : '';

            
            $xpath = '//' . $tag;
            if ($id) {
                $xpath .= "[@id='$id']";
            }
            if ($class) {
                $xpath .= "[contains(@class, '$class')]";
            }
            if ($div) {
                $xpath .= "[@body = '$div')]";
            }
            if ($attr) {
                
                if (preg_match('/\[([a-zA-Z0-9-]+)(?:="([^"]*)")?\]/', $attr, $attrMatches)) {
                    $attrName = $attrMatches[1];
                    $attrValue = $attrMatches[2] ?? '';
                    if ($attrValue) {
                        $xpath .= "[@$attrName='$attrValue']";
                    } else {
                        $xpath .= "[@$attrName]";
                    }
                }
            }
            return $xpath;
        }
        return '//*'; 
    }

    public function text() {
        return $this->nodes[0]->textContent ?? '';
    }

    public function html($outer = false) {
        if (empty($this->nodes)) {
            return '';
        }
        if ($outer) {
            return $this->doc->saveHTML($this->nodes[0]);
        }
        $inner = '';
        foreach ($this->nodes[0]->childNodes as $child) {
            $inner .= $this->doc->saveHTML($child);
        }
        return $inner;
    }

    public function getAttribute($name) {
        return $this->nodes[0]->getAttribute($name) ?? null;
    }

    public function setAttribute($name, $value) {
        if (!empty($this->nodes)) {
            $this->nodes[0]->setAttribute($name, $value);
        }
        return $this;
    }


    public function getAll() {
        $result = [];
        foreach ($this->nodes as $node) {
            $newDom = new self($this->doc->saveHTML());
            $newDom->nodes = [$node];
            $result[] = $newDom;
        }
        return $result;
    }

    public function count() {
        return count($this->nodes);
    }

    public function clear() {
        $this->nodes = [];
        $this->doc = null;
        $this->xpath = null;
    }

    public function __toString() {
        return $this->html(true);
    }
}

function str_get_html($html) {
    return SimpleDom::str_get_html($html);
}

function file_get_html($filename) {
    return SimpleDom::file_get_html($filename);
}
?>