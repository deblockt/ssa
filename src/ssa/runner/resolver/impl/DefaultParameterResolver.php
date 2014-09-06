<?php

namespace ssa\runner\resolver\impl;

use ssa\runner\resolver\ParameterResolver;
use ssa\runner\resolver\PrimitiveResolverCOR;
use ssa\runner\resolver\ObjectResolverCOR;
use ssa\runner\resolver\impl\FilePrimitiveResolver;
use ssa\runner\resolver\impl\ArrayFilePrimitiveResolver;

/**
 * Description of DefaultParameterResolver
 * this resolver use two chain of responsabilty for resolve object
 * 
 * {@see ObjectResolverCOR} {@see PrimitiveResolverCOR}
 * @author thomas
 */
class DefaultParameterResolver implements ParameterResolver {
    
    /**
     *
     * @var PrimitiveResolverCOR
     */
    private $primitiveResolver;
    
    /**
     *
     * @var ObjectResolverCOR 
     */
    private $objectResolver;
    
    /**
     * The default paramter resolver
     * @var DefaultParameterResolver 
     */
    private static $defaultParameterResolver;
    
    /**
     * create a parameter resolver it can convert
     * - primitive type
     * - DateTime object {@see \saa\runner\resolver\DateTimeObjectResolver}
     * - array and typed array as array(int) array(string) or array(MyClass)
     * - Custom oject all classe 
     * 
     * @return type
     */
    public static function createDefaultParameterResolver() {
        if (DefaultParameterResolver::$defaultParameterResolver == null) {
            $return = new DefaultParameterResolver();
            $return->addPrimitiveResolver(new DefaultPrimitiveResolver());
            $return->addPrimitiveResolver(new ArrayPrimitiveResolver());
            $return->addPrimitiveResolver(new FilePrimitiveResolver());
            $return->addPrimitiveResolver(new ArrayFilePrimitiveResolver());
            
            $return->addObjectResolver(new DefaultObjectResolver());
            $return->addObjectResolver(new DateTimeObjectResolver());
            DefaultParameterResolver::$defaultParameterResolver = $return;
        }
        return DefaultParameterResolver::$defaultParameterResolver ;
    }
    
    /**
     * add a primitive resolver , this resolver will be the first used resolver
     * 
     * @param \ssa\runner\resolver\PrimitiveResolverCOR $primitiveResolver
     */
    public function addPrimitiveResolver(PrimitiveResolverCOR $primitiveResolver) {
        if ($this->primitiveResolver != null) {
            $primitiveResolver->addResolver($this->primitiveResolver);
        }
        $primitiveResolver->setParameterResolver($this);
        $this->primitiveResolver = $primitiveResolver;
    }
    
    /**
     * add an object resolver , this resolver will be the first used resolver
     * 
     * @param \ssa\runner\resolver\ObjectResolverCOR $objectResolver
     */
    public function addObjectResolver(ObjectResolverCOR $objectResolver) {
        if ($this->objectResolver != null) {
            $objectResolver->addResolver($this->objectResolver);
        }
        $objectResolver->setParameterResolver($this);
        $this->objectResolver = $objectResolver;
    }
   
    /**
     * {@inheritdoc}
     */
    public function resolveObject(\ReflectionClass $class, $parameters,array &$commentType) {
        return $this->objectResolver->resolveObject($class, $parameters, $commentType);
    }

    /**
     * {@inheritdoc}
     */
    public function resolvePrimitive($value, array &$type) {
        return $this->primitiveResolver->resolvePrimitive($value, $type);
    }
}
