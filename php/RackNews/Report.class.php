<?php
/**
 * RackNews - A reports tool for RackTables.
 *
 * Report.class.php
 * A representation of a RackNews report and related functions.
 */
namespace RackNews;

/**
 * The class Report.
 */
class Report {

    /**
     * The array of all objects.
     */
    private $objects;

    /**
     * The objects generated by the report.
     */
    private $report_objects;

    /**
     * The report parameters.
     */
    private $params;

    /**
     * Construct a new Report.
     *
     * @param array $objects the array of objects to use
     */
    public function __construct($objects) {
        $this->objects = ($objects) ? $objects : array();
        $this->report_objects = array();
        $this->params = array();
    }

    /**
     * Get the parameters used to create this report.
     *
     * @return array the parameters
     */
    public function get_params() {
        return $this->params;
    }

    /**
     * Set the parameters to use for this report.
     *
     * @param array $params the desired parameters
     */
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

    /**
     * Get the objects that will be used in this report.
     *
     * @return array the objects
     */
    public function get_objects() {
        return $this->objects;
    }

    /**
     * Get the objects generated by this report.
     *
     * @return array the generated objects
     */
    public function get_report_objects() {
        return $this->report_objects;
    }

    /**
     * Build this report.
     *
     * Uses $this->params to filter the $objects array and sets
     * $this->report_objects when done
     */
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

        if (!empty($this->params['and'])) {
            $found = $objects;
            foreach ($this->params['and'] as $match_string) {
                list($k, $v) = explode(':', $match_string);
                $found = ObjectUtils::find_by_attr($found, $k, $v);
            }

            $objects = $found;
        }

        if (!empty($this->params['or'])) {
            $found = array();
            foreach ($this->params['or'] as $or_string) {
                list($k, $v) = explode(':', $or_string);
                if (($attr_matches = ObjectUtils::find_by_attr($objects, $k, $v)) !== FALSE) {
                    foreach ($attr_matches as $attr_match) {
                        if (!ObjectUtils::find_by_name($found, $attr_match['name'])) {
                            $found[] = $attr_match;
                        }
                    }
                }
            }

            $objects = $found;
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
            $fields = $this->params['fields'];
            if ($this->params['format'][0] === 'html' && !in_array('id', $this->params['fields'])) {
                $fields[] = 'id';
            }

            $tmp_objects = array();
            foreach ($objects as $object) {
                $tmp_objects[] = self::pick_fields($object, $fields);
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

    /**
     * Display this report.
     *
     * Calls the appropriate display function to output the report objects
     */
    public function display() {
        switch (strtolower($this->params['format'][0])) {
        case 'html':
        case 'csv':
        case 'xml':
        case 'json':
            $display_function = 'as_' . strtolower($this->params['format'][0]);
            $this->$display_function();
            break;
        default:
            var_dump($this->report_objects);
            break;
        }
    }

    /**
     * Display this report as a CSV.
     */
    public function as_csv() {
        echo implode(',', $this->params['fields']) . "\n";
        $buffer = fopen('php://output', 'w');
        $csv_objects = array_merge(array(), $this->report_objects);
        foreach ($csv_objects as $object) {
            foreach ($object as &$attr) {
                if (is_array($attr)) {
                    $attr = Util::multi_implode($attr, ',');
                }
            }

            fputcsv($buffer, $object);
        }

        fclose($buffer);
    }

    /**
     * Display this report as JSON.
     */
    public function as_json() {
        echo json_encode($this->report_objects);
    }

    /**
     * Display this report as HTML.
     *
     * Uses template-parts in resources/ for styles
     */
    public function as_html() {
        include('resources/template-parts/header.php');
?>
        <link rel="stylesheet" href="resources/css/bootstrap-sortable.css">
        <h3>RackNews Report</h3>
        <table class="table table-striped table-condensed table-bordered table-hover sortable" id="report-table">
            <thead>
                <tr>
                    <?php foreach ($this->params['fields'] as $field): ?>
                    <th><?php echo $field; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->report_objects as $object): ?>
                <tr>
                    <?php $this->do_row($object); ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <script src="resources/js/bootstrap-sortable.js"></script>
<?php
        include('resources/template-parts/footer.php');
    }

    /**
     * Format this object's row.
     *
     * Creates a link to the object in the first column and implodes any arrays with commas
     *
     * @param object $object the object to display
     */
    private function do_row($object) {
        foreach ($this->params['fields'] as $field) {
            $a = $field === reset($this->params['fields']);
            $field = $object[$field];
            $cell = (is_array($field)) ? Util::multi_implode($field, ',') : $field;
            if ($a) {
                $cell = '<a href="' . '../' . makeHref(array(
                    'page'      => 'object',
                    'object_id' => $object['id'])
                ) . '">' . $cell . '</a>';
            }

            echo "<td>$cell</td>";
        }
    }

    /**
     * Display this report as XML.
     *
     * @see \RackNews\Util::array_to_xml
     */
    public function as_xml() {
        $xml = new \SimpleXMLElement('<?xml version="1.0" ?><report></report>');
        Util::array_to_xml($this->report_objects, $xml);
        print $xml->asXML();
    }

    /**
     * Perform a few reports-specific operations if needed.
     *
     * In this case,
     *  The 'fields' report just returns ObjectUtils::get_fields
     *  The 'unused_ip' report looks for not-in-use or empty objects
     *
     * @param array  $objects the objects
     * @param string $report the report type
     */
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

    /**
     * Filter the object to the given fields.
     *
     * Pays attention (or tries to) to special cases like tags and ports
     *
     * @param object $object the object to filter
     * @param array  $fields the fields to limit to
     *
     * @return object a filtered object containing $fields fields only
     */
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
                } elseif ($field == 'ipv4') {
                    $allocs = array();
                    foreach ($object['ipv4'] as $alloc) {
                        $allocs[] = array(
                            'osif' => $alloc['osif'],
                            'ip'   => $alloc['addrinfo']['ip']
                        );
                    }

                    $value = $allocs;
                } elseif ($field == 'HW type') {
                    $value = str_replace('%GPASS%', ' ', $object[$field]);
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
