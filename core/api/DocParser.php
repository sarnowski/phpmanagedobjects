<?php

/**
 * A utility to parse PHP comments for annotations.
 *
 * @package pinjector
 * @author Tobias Sarnowski
 * @since 1.0
 */
class DocParser {

    /**
     * Searches for $key in $comment with the following scheme:
     *
     *   /**
     *    * @$key
     *    *
     *
     *  Returns the trailing strings or null if not found.
     *
     * @param string $comment
     * @param string $key
     * @return array
     */
    public static function parseAnnotations($comment, $key) {
        if (empty($comment)) {
            return array();
        }

        $defs = array();

        $lines = explode("\n", $comment);
        foreach ($lines as $line) {
            $line = trim($line)." ";
            $match = "* @$key ";
            if (substr($line, 0, strlen($match)) == $match) {
                $defs[] = trim(substr($line, strlen($match)));
            }
        }

        return $defs;
    }

    /**
     * Same as parseSettings just returning one line or null
     * if no keyline was found.
     *
     * @param string $comment
     * @param string $key
     * @return string
     */
    public static function parseAnnotation($comment, $key) {
        $list = self::parseAnnotations($comment, $key);
        if (empty($list)) {
            return null;
        } else {
            return $list[0];
        }
    }

    public static function hasAnnotation($comment, $key) {
        $list = self::parseAnnotations($comment, $key);
        return count($list) > 0;
    }
}