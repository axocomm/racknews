<?php
namespace RackNews;

class Report {
    const FORMAT_HTML = 1;
    const FORMAT_CSV = 2;
    const FORMAT_JSON = 3;
    const FORMAT_XML = 4;
    const FORMAT_RAW = 5;

    private $objects;
    private $report_objects;
    private $fields;
    private $types;
    private $format;

    public function __construct($objects) {
        $this->objects = ($objects) ? $objects : array();
        $this->report_objects = array();
        $this->fields = array();
        $this->types = array();
        $this->format = self::FORMAT_RAW;
    }

    public function get_params() {
        return array(
            'fields' => $this->fields,
            'types' => $this->types,
            'format' => $this->format
        );
    }

    public function set_params($params) {
        $this->fields = isset($params['fields']) ? $params['fields'] : $this->fields;
        $this->types = isset($params['types']) ? $params['types'] : $this->types;
        $this->format = isset($params['format']) ? $params['format'] : $this->format;
    }

    public function build() {
        if (count($this->fields)) {
            foreach ($this->objects as $object) {
                $this->report_objects[] = self::pick_fields($object, $this->fields);
            }
        }
    }

    public function display() {
        var_dump($this->report_objects);
    }
    
    public function get_objects() {
        return $this->objects;
    }

    public function get_report_objects() {
        return $this->report_objects;
    }

    public function get_fields() {
        return $this->fields;
    }

    public function get_types() {
        return $this->types;
    }

    public function get_format() {
        return $this->format;
    }

    public function set_fields($fields) {
        $this->fields = $fields;
    }

    public function set_types($types) {
        $this->types = $types;
    }

    public function set_format($format) {
        $this->format = $format;
    }

    private static function pick_fields($object, $fields) {
        $out = array();

        foreach ($fields as $field) {
            // Special treatment for tags and ports.
            if (isset($object[$field])) {
                if ($field == 'etags' or $field == 'atags') {
                    $tags = array();
                    foreach ($object[$field] as $tag) {
                        $tags[] = $tag['tag'];
                    }

                    $value = $tags;
                } elseif ($field == 'ports') {
                    $ports = array();
                    if (has_mac($object)) {
                        foreach ($object['ports'] as $port) {
                            $ports[] = array(
                                'interface' => $port['name'],
                                'l2address' => $port['l2address']
                            );
                        }
                    }

                    $value = multi_implode($ports, ',');
                } else {
                    $value = $object[$field];
                }
            } else {
                $value = NULL;
            }

            $out[$field] = $value;
        }

        return $out;
    }
}
