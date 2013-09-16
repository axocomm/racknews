<?php
namespace RackNews;

if (!function_exists('loadIPv4AddrList')) {
    function loadIPv4AddrList(&$info) {
        loadIPAddrList($info);
    }
}

class Util {
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

    public static function array_to_xml($arr, &$xml) {
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                if (!is_numeric($k)) {
                    $subnode = $xml->addChild($k);
                    self::array_to_xml($v, $subnode);
                } else {
                    $subnode = $xml->addChild('object');
                    self::array_to_xml($v, $subnode);
                }
            } else {
                $xml->addChild($k, $v);
            }
        }
    }
}
