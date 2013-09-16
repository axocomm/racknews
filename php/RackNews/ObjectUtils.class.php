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
     * @return an associative array of objects
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
     * @param $objects the objects to search
     * @param $name the name to find
     * @return the object that has the given name, FALSE otherwise
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
     * @param $objects the objects to search
     * @param the ID to find
     * @return the object that has the given name, FALSE otherwise
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
     * @param $objects the objects to search
     * @param the FQDN to find
     * @return the object that has the given name, FALSE otherwise
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
     * @param $objects the objects to search
     * @param $k the key to match
     * @param $v the value it should have
     * @return an array of matching objects, FALSE otherwise
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
     * @param $objects the objects to search
     * @param $type_name the name of the type
     * @return an array of objects of the given type name
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

        return $out;
    }

    /**
     * Find objects by a list of tags.
     *
     * @param $objects the objects to search
     * @param $tags an array of tags
     * @return an array of objects with the given tags (atags/etags), FALSE otherwise
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

    /**
     * Find objects by log query.
     *
     * @param $objects the objects to search
     * @param $records the log records
     * @param $query the log search term
     * @return an array of objects that have the given log query, FALSE otherwise
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

        return $matches;
    }

    /**
     * Find objects by comment.
     *
     * @param $objects the objects to search
     * @param $query the comment to find
     * @return an array of objects that have the given comment, FALSE otherwise
     */
    public static function find_by_comment($objects, $query) {
        $matches = array();
        foreach ($objects as $object) {
            if (stripos($object['comment'], $query) !== FALSE) {
                $matches[] = $object;
            }
        }

        return $matches;
    }

    /**
     * Determine if the given object has a MAC address registered.
     *
     * @param $object the object to test
     * @return if the object has a MAC/l2address entered for an interface
     */
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

    /**
     * Determine if the given object has these fields set and populated.
     *
     * @param $object the object to test
     * @param $fields an array of fields to check
     * @return if each of the given fields has a value in the object
     */
    public static function check_fields($object, $fields) {
        foreach ($fields as $field) {
            if (!self::has_field($object, $field)) {
                return 0;
            }
        }

        return 1;
    }

    /**
     * Determine if the given object has this field set and populated.
     *
     * @param $object the object to test
     * @param $field the field to check
     * @return if this field is set and has a value
     */
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

    /**
     * Get an array of fields present in all objects.
     *
     * @param $objects the objects to search
     * @return an array of fields common to these objects
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
}
