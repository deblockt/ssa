<?php

namespace ssa\annotation;

/**
 * Description of AnnotationUtil
 *
 * @author thomas
 */
class AnnotationUtil {
    /**
     * fonction pour retourner les types des arguments
     * récupération des types grâce aux commentaires
     * 
     * @param string $doc le commentaire de la fonction
     * @return array la liste des types pour chaque paramétre
     */
    public static function getMethodParameters($doc) {
        // récupération des type et des noms des variables
        preg_match_all('#@param\s+(.+)\s+\$([^\s]+).*[\n|\*]#i', $doc, $annotations);
        
        $return = array();
        $count = count($annotations[2]);
        for ($i = 0; $i < $count; $i++) {
            $return[trim($annotations[2][$i])] = AnnotationUtil::splitParameter($annotations[1][$i]);
        }
        return $return;
    }
    
    /**
     * split a parameter type 
     * @param type $parameter
     * @return type
     */
    public static function splitParameter($parameter) {
        $typepos = strpos($parameter, '(');
        $endtypepos = strpos($parameter, ')', $typepos);
        $return = array();
        if ($typepos > 0 || $endtypepos > 0) {
            $return[] = trim(\substr($parameter, 0, $typepos));
            $return[] = trim(\substr($parameter, $typepos + 1, $endtypepos - $typepos - 1));
        } else {
            $return[] = trim($parameter);
        }
        return $return;
    }
}
