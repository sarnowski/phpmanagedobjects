<?php

/**
 * A utility to parse annotations from PHP comments.
 *
 * @author Tobias Sarnowski
 */
class AnnotationParserImpl implements AnnotationParser {

    /**
     * Provides an informal object which holds informations about annotations.
     *
     * @param ReflectionClass $class
     * @return AnnotatedDefinition
     */
    public function getAnnotatedClass(ReflectionClass $class) {
        $annotations = $this->parseAnnotations($class->getDocComment());
        return new AnnotatedDefinitionImpl($annotations);
    }

    /**
     * Provides an informal object which holds informations about annotations.
     *
     * @param ReflectionMethod $method
     * @return AnnotatedDefinition
     */
    public function getAnnotatedMethod(ReflectionMethod $method) {
        $annotations = $this->parseAnnotations($method->getDocComment());
        return new AnnotatedDefinitionImpl($annotations);
    }

    /**
     * Provides an informal object which holds informations about annotations.
     *
     * @param ReflectionProperty $property
     * @return AnnotatedDefinition
     */
    public function getAnnotatedProperty(ReflectionProperty $property) {
        $annotations = $this->parseAnnotations($property->getDocComment());
        return new AnnotatedDefinitionImpl($annotations);
    }

    /**
     * @param string $comment
     * @return AnnotationImpl[]
     */
    private function parseAnnotations($comment) {
        $annotations = array();

        $lines = explode("\n", $comment);
        foreach ($lines as $line) {
            $line = trim($line);
            $pattern = '/^\* @([a-zA-Z0-9]+)(.*)$/';
            if (preg_match($pattern, $line, $matches)) {
                $name = $matches[1];
                if (count($matches) > 2) {
                    $payload = $matches[2];
                } else {
                    $payload = '';
                }
                $annotations[] = new AnnotationImpl($name, $payload);
            }
        }

        return $annotations;
    }

    function __toString() {
        return 'AnnotationParserImpl';
    }
}
