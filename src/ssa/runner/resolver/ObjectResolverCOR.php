<?php

namespace ssa\runner\resolver;

/**
 * A chain of responsabilty (COR) for convert object into correct type
 *  PrimitiveResolverCOR
 *
 * @author thomas
 */
abstract class ObjectResolverCOR implements ObjectResolver {
    
    /**
     *
     * @var ObjectResolverCOR
     */
    private $nextResolver = null;
   
    /**
     *
     * @var ParameterResolver 
     */
    protected $parameterResolver;
    
    /**
     * set the parameter resolver for the PrimitiveResolver
     * 
     * @param ParameterResolver $parameterResolver the parameterResolver
     */
    public function setParameterResolver(ParameterResolver $parameterResolver) {
        $this->parameterResolver = $parameterResolver;
    }    
        
    /**
     * @param ReflectionClass $class the class to resolve
     * 
     * @return true if the class can be resolve by this resolver
     */
    protected abstract function canResolve(\ReflectionClass $class);
    
    /**
     * convert the primitive type into a typed value
     * 
     * @param \ReflectionClass $value the primitive value
     * @param array $parameters the object parameters
     * @param array $commentType the object type write in the documentation
     * 
     * @return the typed value
     */
    protected abstract function resolve(\ReflectionClass $class, $parameters, array &$commentType);
    
    
    /**
     * add a resolver just after the resolver
     * 
     * @param \ssa\runner\resolver\PrimitiveResolverCOR a primitive resolver
     */
    public function addResolver(ObjectResolverCOR $resolver) {
        if ($this->nextResolver != null) {
            $resolver->addResolver($this->nextResolver);
        }
        $this->nextResolver = $resolver;
    }
    
    /**
     * {@inheritdoc}
     */
    public function resolveObject(\ReflectionClass $class, $parameters,array &$commentType) {        
        if ($this->canResolve($class)) {
            return $this->resolve($class, $parameters, $commentType);
        } else if ($this->nextResolver != null) {
            return $this->nextResolver->resolveObject($class, $parameters, $commentType);
        }  
        
        throw new TypeNotSupportedException($class->getName());
    }

}
