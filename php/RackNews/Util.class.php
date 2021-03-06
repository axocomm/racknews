<?php
/**
 * RackNews - A reports tool for RackTables.
 *
 * Util.class.php
 * A collection of general and RackTables-centric functions
 */
namespace RackNews;

/**
 * The class Util.
 */
class Util {

    /**
     * Get the IPv4 addresses.
     *
     * @return array all IPv4 addresses and associated object names
     */
    public static function get_addrs() {
        $rt_nets = scanRealmByText('ipv4net');

        $addrs = array();
        foreach ($rt_nets as $i => $net) {
            $info = spotEntity('ipv4net', $i);
            loadIPv4AddrList($info);

            foreach ($info['addrlist'] as $addr) {
                $addrs[] = array(
                    'object_name' => $addr['name'],
                    'ip' => $addr['ip']
                );
            }
        }

        return $addrs;
    }

    /**
     * Get IP address allocations.
     *
     * @param array $objects the objects to use
     *
     * @return array IPs and their allocations
     */
    public static function get_allocs($objects) {
        $allocs = array();
        foreach ($objects as $object) {
            $ipv4 = $object['ipv4'];
            foreach ($ipv4 as $alloc) {
                $allocs[$alloc['addrinfo']['ip']] = array(
                    'object' => $object['name'],
                    'interface' => $alloc['osif']
                );
            }
        }

        return $allocs;
    }

    /**
     * Recursively implode an array.
     *
     * @param array  $pieces the pieces
     * @param string $glue   the glue
     *
     * @return string the recursively imploded array
     */
    public static function multi_implode($pieces, $glue) {
        $out = '';

        foreach ($pieces as $piece) {
            if (is_array($piece)) {
                $out .= self::multi_implode($piece, $glue) . $glue;
            } else {
                $out .= $piece . $glue;
            }
        }

        $out = substr($out, 0, 0 - strlen($glue));

        return $out;
    }

    /**
     * Convert (roughly) an associative array to XML and print.
     *
     * @param array             $arr the array to convert
     * @param SimpleXMLElement& $xml a reference to the current XML object
     */
    public static function array_to_xml($arr, &$xml) {
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                if (!is_numeric($k)) {
                    $subnode = $xml->addChild('field');
                    $subnode->addAttribute('name', $k);
                    self::array_to_xml($v, $subnode);
                } else {
                    $subnode = $xml->addChild('object');
                    self::array_to_xml($v, $subnode);
                }
            } else {
                $subnode = $xml->addChild('field', $v);
                $subnode->addAttribute('name', $k);
            }
        }
    }
}
