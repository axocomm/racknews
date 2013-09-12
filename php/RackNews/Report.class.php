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
        $excludes = array('id', 'report');
        foreach ($params as $key => &$param) {
            if (!empty($param) && 
               (!(is_numeric($param) or is_array($param))) &&
               (!in_array($key, $excludes))) {
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

        if (!empty($this->params['report'])) {
            $objects = $this->pre_build($objects, strtolower($this->params['report']));
        }

        if (!empty($this->params['has'])) {
            $tmp_objects = array();
            foreach ($objects as $object) {
                if (ObjectUtils::check_fields($object, $this->params['has'])) {
                    $tmp_objects[] = $object;
                }
            }

            $objects = $tmp_objects;
        }

        if (!empty($this->params['types'])) {
            $tmp_objects = array();
            foreach ($this->params['types'] as $type) {
                $tmp_objects = array_merge($tmp_objects, ObjectUtils::find_by_type($objects, $type));
            }

            $objects = $tmp_objects;
        }

        if (!empty($this->params['names'])) {
            $tmp_objects = array();
            foreach ($this->params['names'] as $name) {
                if (($found = ObjectUtils::find_by_attr($objects, 'name', $name)) !== FALSE) {
                    $tmp_objects = array_merge($tmp_objects, $found);
                }
            }

            $objects = $tmp_objects;
        } elseif (!empty($this->params['id'])) {
            $tmp_objects = array();
            foreach ($this->params['id'] as $id) {
                if (($found = ObjectUtils::find_by_attr($objects, 'id', $id)) !== FALSE) {
                    $tmp_objects = array_merge($tmp_objects, $found);
                }
            }
            
            $objects = $tmp_objects;
        }

        if (!empty($this->params['log'])) {
            $records = getLogRecords();
            $tmp_objects = array();
            foreach ($this->params['log'] as $query) {
                if (($found = ObjectUtils::find_by_log_query($objects, $records, $query)) !== FALSE) {
                    $tmp_objects = array_merge($tmp_objects, $found);
                }
            }

            $objects = $tmp_objects;
        }

        if (!empty($this->params['comment'])) {
            $tmp_objects = array();
            foreach ($this->params['comment'] as $query) {
                $tmp_objects = array_merge($tmp_objects, ObjectUtils::find_by_comment($objects, $query));
            }

            $objects = $tmp_objects;
        }

        if (!empty($this->params['fields'])) {
            $tmp_objects = array();
            foreach ($objects as $object) {
                $tmp_objects[] = self::pick_fields($object, $this->params['fields']);
            }

            $objects = $tmp_objects;
        }

        if (!empty($this->params['matching'])) {
            $tmp_objects = array();
            foreach ($this->params['matching'] as $match_string) {
                list($k, $v) = explode(':', $match_string);
                if (($found = ObjectUtils::find_by_attr($objects, $k, $v)) !== FALSE) {
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

    private function pre_build($objects, $report) {
        switch (strtolower($this->params['report'])) {
        case 'fields':
            $objects = ObjectUtils::get_fields($objects);
            break;
        case 'unused_ip':
            $addrs = Util::get_addrs();
            $allocs = Util::get_allocs($objects);
            $tmp_objects = array();

            foreach ($addrs as $addr) {
                $f_name = explode(',', $addr['object_name']);
                $f_name = $f_name[0];
                if ((ObjectUtils::find_by_name($objects, $addr['object_name']) === FALSE) &&
                    (ObjectUtils::find_by_name($objects, $f_name) === FALSE) &&
                    (ObjectUtils::find_by_fqdn($objects, $addr['object_name']) === FALSE) &&
                    (!array_key_exists($addr['ip'], $allocs))) {
                        $tmp_objects[] = array(
                            'FQDN' => $addr['object_name'],
                            'ip'   => $addr['ip']
                        );
                }
            }

            $objects = array_merge(ObjectUtils::find_by_tags($objects, array('not-in-use')), $tmp_objects);
            break;
        default:
            throw new \Exception('Invalid report');
            break;
        }

        return $objects;
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
                    if (ObjectUtils::has_mac($object)) {
                        foreach ($object['ports'] as $port) {
                            $ports[] = array(
                                'interface' => $port['name'],
                                'l2address' => $port['l2address']
                            );
                        }
                    }

                    $value = Util::multi_implode($ports, ',');
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
