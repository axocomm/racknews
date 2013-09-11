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

    public function build_report($params = null) {
        if (!$params) {
            $params = array(
                'fields' => $this->fields,
                'types'  => $this->types,
                'format' => $this->format
            );
        }

        var_dump($params);
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
}
