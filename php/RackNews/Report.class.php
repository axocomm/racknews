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
    private $params;

    public function __construct($objects) {
        $this->objects = ($objects) ? $objects : array();
        $this->report_objects = array();
        $this->params = array();
    }

    public function get_params() {
        return $this->params;
    }

    public function set_params($params) {
        $this->params = $params;
    }
    
    public function get_objects() {
        return $this->objects;
    }

    public function get_report_objects() {
        return $this->report_objects;
    }

    public function build() {
        $report_objects = $this->objects;

        if (count($this->params['has'])) {
            $tmp_objects = array();
            foreach ($report_objects as $object) {
                if (self::check_fields($object, $this->params['has'])) {
                    $tmp_objects[] = $object;
                }
            }

            $report_objects = $tmp_objects;
        }

        if (count($this->params['fields'])) {
            $tmp_objects = array();
            foreach ($report_objects as $object) {
                $tmp_objects[] = self::pick_fields($object, $this->params['fields']);
            }

            $report_objects = $tmp_objects;
        }

        $this->report_objects = $report_objects;
    }

    public function display() {
        var_dump($this->report_objects);
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

    public static function check_fields($object, $fields) {
        foreach ($fields as $field) {
            if (!self::has_field($object, $field)) {
                return 0;
            }
        }

        return 1;
    }

    public static function has_field($object, $field) {
        if (isset($object[$field]) && ($v = $object[$field]) !== FALSE) {
            if (is_array($v)) {
                return count($v);
            } else {
                return strlen($v);
            }
        }

        return 0;
    }
}
