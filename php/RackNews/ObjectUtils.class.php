<?php
namespace RackNews;

class ObjectUtils {
    public static function get_objects() {
        $rt_objects = scanRealmByText('object');

        $objects = array();
        foreach ($rt_objects as $i => $rt_object) {
            $info = spotEntity('object', $i);
            amplifyCell($info);

            $attrs = array();
            foreach (getAttrValues($i) as $record) {
                if (!isset($record['name'])) {
                    throw new \Exception("Record $i is broken");
                }

                $attrs[$record['name']] = $record['value'];
            }

            $info = array_merge($info, $attrs);
            $objects[$info['name']] = $info;
        }

        return $objects;
    }

    public static function find_by_name($objects, $name) {
        foreach ($objects as $object) {
            if ($object['name'] == $name) {
                return $object;
            }
        }

        return FALSE;
    }

    public static function find_by_id($objects, $id) {
        foreach ($objects as $object) {
            if ($object['id'] == $id) {
                return $object;
            }
        }

        return FALSE;
    }

    public static function find_by_fqdn($objects, $fqdn) {
        foreach ($objects as $object) {
            if ($object['FQDN'] == $fqdn) {
                return $object;
            }
        }

        return FALSE;
    }

    public static function find_by_attr($objects, $k, $v) {
        $matches = array();
        foreach ($objects as $object) {
            if ($object[$k] == $v) {
                $matches[] = $object;
            }
        }

        return count($matches) ? $matches : FALSE;
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

    public static function find_by_tags($objects, $tags) {
        $out = array();

        foreach ($objects as $object) {
            foreach ($tags as $tag) {
                $found = 0;
                foreach ($object['atags'] as $atag) {
                    if ($atag['tag'] == $tag) {
                        $out[] = $object;
                        $found = 1;
                    }
                }

                if (!$found) {
                    foreach ($object['etags'] as $etag) {
                        if ($etag['tag'] == $tag) {
                            $out[] = $object;
                        }
                    }
                }
            }
        }

        if (count($out)) {
            return $out;
        } else {
            return NULL;
        }
    }

    public static function find_by_log_query($objects, $records, $query) {
        $matches = array();
        $found_ids = array();
        foreach ($records as $record) {
            $id = $record['object_id'];
            if (!in_array($id, $found_ids)) {
                $messages = getLogRecordsForObject($id);
                foreach ($messages as $message) {
                    $content = $message['content'];
                    if (stripos($content, $query) !== FALSE) {
                        $match = self::find_by_id($objects, $id);
                        if (!in_array($match['id'], $found_ids)) {
                            $match['log_match'] = $content;
                            $matches[] = $match;
                            $found_ids[] = $id;
                        }
                    }
                }
            }
        }

        return $matches;
    }

    public static function find_by_comment($objects, $query) {
        $matches = array();
        foreach ($objects as $object) {
            if (stripos($object['comment'], $query) !== FALSE) {
                $matches[] = $object;
            }
        }

        return $matches;
    }

    public static function has_mac($object) {
        if (count($object['ports'])) {
            foreach ($object['ports'] as $port) {
                if ((isset($port['l2address']) && strlen($port['l2address']))) {
                    return 1;
                }
            }
        }

        return 0;
    }

    private static function check_fields($object, $fields) {
        foreach ($fields as $field) {
            if (!self::has_field($object, $field)) {
                return 0;
            }
        }

        return 1;
    }

    private static function has_field($object, $field) {
        if (isset($object[$field]) && ($v = $object[$field]) !== FALSE) {
            if (is_array($v)) {
                return count($v);
            } else {
                return strlen($v);
            }
        }

        return 0;
    }

    public static function get_fields($objects) {
        if (!count($objects)) {
            return FALSE;
        }

        $field_list = array();
        foreach ($objects as $object) {
            foreach (array_keys($object) as $field) {
                $field_list[] = $field;
            }
        }

        $fields = array();
        $field_list = array_unique($field_list);
        foreach ($field_list as $field) {
            $fields[]['field'] = $field;
        }

        return $fields;
    }
}
