<?php
namespace RackNews;

class Report {
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
        foreach ($params as $key => &$param) {
            if (!(is_numeric($param) or is_array($param)) or $key == 'id') {
                $param = explode(',', $param);
            }
        }

        $this->params = $params;
    }
    
    public function get_objects() {
        return $this->objects;
    }

    public function get_report_objects() {
        return $this->report_objects;
    }

    public function build() {
        $objects = $this->objects;

        if (count($this->params['has'])) {
            $tmp_objects = array();
            foreach ($objects as $object) {
                if (self::check_fields($object, $this->params['has'])) {
                    $tmp_objects[] = $object;
                }
            }

            $objects = $tmp_objects;
        }

        if (count($this->params['types'])) {
            $tmp_objects = array();
            foreach ($this->params['types'] as $type) {
                $tmp_objects = array_merge($tmp_objects, RTObject::find_by_type($objects, $type));
            }

            $objects = $tmp_objects;
        }

        if (count($this->params['names'])) {
            $tmp_objects = array();
            foreach ($this->params['names'] as $name) {
                if (($found = RTObject::find_by_attr($objects, 'name', $name)) !== FALSE) {
                    $tmp_objects = array_merge($tmp_objects, $found);
                }
            }

            $objects = $tmp_objects;
        } elseif (count($this->params['id'])) {
            $tmp_objects = array();
            foreach ($this->params['id'] as $id) {
                if (($found = RTObject::find_by_attr($objects, 'id', $id)) !== FALSE) {
                    $tmp_objects = array_merge($tmp_objects, $found);
                }
            }
            
            $objects = $tmp_objects;
        }

        if (count($this->params['fields'])) {
            $tmp_objects = array();
            foreach ($objects as $object) {
                $tmp_objects[] = self::pick_fields($object, $this->params['fields']);
            }

            $objects = $tmp_objects;
        }

        if (count($this->params['matching'])) {
            $tmp_objects = array();
            foreach ($this->params['matching'] as $match_string) {
                list($k, $v) = explode(':', $match_string);
                if (($found = RTObject::find_by_attr($objects, $k, $v)) !== FALSE) {
                    $tmp_objects = array_merge($tmp_objects, $found);
                }
            }

            $objects = $tmp_objects;
        }

        if (count($objects)) {
            $this->report_objects = $objects;
        } else {
            $this->report_objects = array(
                'error' => 'No objects matched your criteria.'
            );
        }
    }

    public function display() {
        switch (strtolower($this->params['format'][0])) {
        case 'html':
        case 'csv':
        case 'xml':
            throw new \Exception('Not yet implemented');
            break;
        case 'json':
            echo json_encode($this->report_objects);
            break;
        default:
            var_dump($this->report_objects);
            break;
        }
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
