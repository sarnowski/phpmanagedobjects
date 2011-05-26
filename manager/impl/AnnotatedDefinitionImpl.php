<?php

/**
 * Holds annotation informations about a definition.
 *
 * @author Tobias Sarnowski
 */
class AnnotatedDefinitionImpl implements AnnotatedDefinition {

    /**
     * @var AnnotationImpl[]
     */
    private $annotations;

    /**
     * @param AnnotationImpl[] $annotations
     */
    function __construct($annotations) {
        $this->annotations = $annotations;
    }

    /**
     * Returns the annotation with the given names. Throws an exception if
     * the annotation was not found or is present more than one time.
     *
     * @param string $name
     * @return Annotation
     */
    public function getAnnotation($name) {
        foreach ($this->annotations as $annotation) {
            if ($annotation->getName() == $name) {
                return $annotation;
            }
        }
        throw new MobException("Annotation $name not found.");
    }

    /**
     * Provides a list of all annotations.
     *
     * @return Annotation[]
     */
    public function getAnnotations() {
        return $this->annotations;
    }

    /**
     * Returns if the annotation is present.
     *
     * @param string $name
     * @return boolean
     */
    public function hasAnnotation($name) {
        foreach ($this->annotations as $annotation) {
            if ($annotation->getName() == $name) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns if the annotation is present one time.
     *
     * @param string $name
     * @return boolean
     */
    public function hasSingleAnnotation($name) {
        $found = false;
        foreach ($this->annotations as $annotation) {
            if ($annotation->getName() == $name) {
                if ($found == false) {
                    $found = true;
                } else {
                    return false;
                }
            }
        }
        return $found;
    }

    function __toString() {
        return 'AnnotatedDefinitionImpl';
    }
}
