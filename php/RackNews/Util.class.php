<?php
namespace RackNews;

class Util {
    public static function multi_implode($pieces, $glue) {
        $out = '';

        foreach ($pieces as $piece) {
            if (is_array($piece)) {
                $out .= multi_implode($piece, $glue) . $glue;
            } else {
                $out .= $piece . $glue;
            }
        }

        $out = substr($out, 0, 0 - strlen($glue));

        return $out;
    }
}
