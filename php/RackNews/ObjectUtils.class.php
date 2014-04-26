<?php
/**
 * RackNews - A reports tool for RackTables.
 *
 * ObjectUtils.class.php
 * A collection of object- and attribute-related functions.
 */
namespace RackNews;

/**
 * The class ObjectUtils.
 */
class ObjectUtils {

    /**
     * Get all objects stored in RackTables.
     *
     * @return array an associative array of objects
     */
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

    /**
     * Find an object by its name.
     *
     * @param  array  $objects the objects to search
     * @param  string $name    the name to find
     *
     * @return object the object that has the given name, FALSE otherwise
     */
    public static function find_by_name($objects, $name) {
        foreach ($objects as $object) {
            if ($object['name'] == $name) {
                return $object;
            }
        }

        return FALSE;
    }

    /**
     * Find an object by its ID.
     *
     * @param array $objects the objects to search
     * @param int   $id      the ID to find
     *
     * @return object the object that has the given name, FALSE otherwise
     */
    public static function find_by_id($objects, $id) {
        foreach ($objects as $object) {
            if ($object['id'] == $id) {
                return $object;
            }
        }

        return FALSE;
    }

    /**
     * Find an object by its fully qualified domain name.
     *
     * @param array  $objects the objects to search
     * @param string $fqdn    the FQDN to find
     *
     * @return object the object that has the given name, FALSE otherwise
     */
    public static function find_by_fqdn($objects, $fqdn) {
        foreach ($objects as $object) {
            if ($object['FQDN'] == $fqdn) {
                return $object;
            }
        }

        return FALSE;
    }

    /**
     * Find objects that match the given attribute.
     *
     * @param array  $objects the objects to search
     * @param string $k       the key to match
     * @param string $v       the value it should have
     *
     * @return array the matching objects, FALSE otherwise
     */
    public static function find_by_attr($objects, $k, $v) {
        $matches = array();
        foreach ($objects as $object) {
            if ($object[$k] == $v) {
                $matches[] = $object;
            }
        }

        return count($matches) ? $matches : FALSE;
    }

    /**
     * Find objects of the given type name.
     *
     * @param array  $objects   the objects to search
     * @param string $type_name the name of the type
     *
     * @return array the objects of the given type name
     */
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

        return count($out) ? $out : FALSE;
    }

    /**
     * Find objects by a list of tags.
     *
     * @param array $objects the objects to search
     * @param array $tags    the tags
     *
     * @return array the objects with the given atags/etags/itags, FALSE otherwise
     */
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
                            $found = 1;
                        }
                    }
                }

                if (!$found) {
                    foreach ($object['itags'] as $etag) {
                        if ($etag['tag'] == $tag) {
                            $out[] = $object;
                        }
                    }
                }
            }
        }

        return count($out) ? $out : FALSE;
    }

    /**
     * Find objects by log query.
     *
     * @param array  $objects the objects to search
     * @param array  $records the log records
     * @param string $query   the log search term
     *
     * @return array the objects that have the given log query, FALSE otherwise
     */
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

        return count($matches) ? $matches : FALSE;
    }

    /**
     * Find objects by comment.
     *
     * @param  array  $objects the objects to search
     * @param  string $query   the comment to find
     *
     * @return array the objects that have the given comment, FALSE otherwise
     */
    public static function find_by_comment($objects, $query) {
        $matches = array();
        foreach ($objects as $object) {
            if (stripos($object['comment'], $query) !== FALSE) {
                $matches[] = $object;
            }
        }

        return count($matches) ? $matches : FALSE;
    }

    /**
     * Determine if the given object has a MAC address registered.
     *
     * @param object $object the object to test
     *
     * @return bool if the object has a l2address entered for an interface
     */
    public static function has_mac($object) {
        if (count($object['ports'])) {
            foreach ($object['ports'] as $port) {
                if ((isset($port['l2address']) && strlen($port['l2address']))) {
                    return TRUE;
                }
            }
        }

        return FALSE;
    }

    /**
     * Determine if the given object has these fields set and populated.
     *
     * @param object $object the object to test
     * @param array  $fields the fields to check
     *
     * @return bool if each of the given fields has a value in the object
     */
    public static function check_fields($object, $fields) {
        foreach ($fields as $field) {
            if (!self::has_field($object, $field)) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * Determine if the given object has this field set and populated.
     *
     * @param object $object the object to test
     * @param string $field  the field to check
     *
     * @return bool if this field is set and has a value
     */
    public static function has_field($object, $field) {
        if (isset($object[$field]) && ($v = $object[$field]) !== FALSE) {
            if (is_array($v)) {
                return count($v) > 0;
            } else {
                return strlen($v) > 0;
            }
        }

        return FALSE;
    }

    /**
     * Get an array of fields present in all objects.
     *
     * @param array $objects the objects to search
     *
     * @return array the fields common to these objects
     */
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

    /**
     * Get the attribute ID that corresponds to this attribute name.
     *
     * @param string $attr_name the name of the attribute
     *
     * @return integer the ID of this attribute, FALSE if it does not exist.
     */
    public static function get_attr_id($attr_name) {
        $map = getAttrMap();
        $attributes = array();
        foreach ($map as $id => $attr) {
            if (!strcasecmp($attr['name'], $attr_name)) {
                return $id;
            }
        }

        return FALSE;
    }
}
