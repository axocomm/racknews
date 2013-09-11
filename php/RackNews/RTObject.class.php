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
}
