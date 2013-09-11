<?php
namespace RackNews;

class RTObject {
    private $name;
    private $attrs;

    public function __construct($name, $attrs) {
        $this->name = $name;
        $this->attrs = $attrs;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_attrs() {
        return $this->attrs;
    }

    public function set_name($name) {
        $this->name = $name;
    }

    public function set_attrs($attrs) {
        $this->attrs = $attrs;
    }

    public static function get_objects() {
        $rt_objects = scanRealmByText('object');

        $objects = array();
        foreach ($rt_objects as $i => $rt_object) {
            $info = spotEntity('object', $i);
            amplifyCell($info);

            $attrs = array();
            foreach (getAttrValues($i) as $record) {
                if (!isset($record['name'])) {
                    throw new Exception("Record $i is broken");
                }

                $attrs[$record['name']] = $record['value'];
            }

            $info = array_merge($info, $attrs);
            $objects[$info['name']] = $info;
        }

        return $objects;
    }

    public static function find_by_type($objects, $type_name) {
        $out = array();
        $types = readChapter(CHAP_OBJTYPE);

        $types = array_flip(array_map('strtolower', $types));
        $type_name = strtolower($type_name);

        if (!array_key_exists($type_name, $types)) {
            throw new \Exception("Invalid type $type_name.");
        }

        $type_id = $types[$type_name];
        foreach ($objects as $object) {
            if ($object['objtype_id'] == $type_id) {
                $out[] = $object;
            }
        }

        return $out;
    }
}
