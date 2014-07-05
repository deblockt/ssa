<?php

namespace ssa\runner\resolver;

/**
 * A chain of responsabilty (COR) for convert object into correct type
 *  PrimitiveResolverCOR
 *
 * @author thomas
 */
abstract class PrimitiveResolverCOR implements PrimitiveResolver {
    
    /**
     *
     * @var ChainOfResponsability
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
     * @param array $type the parameter type
     * 
     * @return true if the type can be resolve by this resolver
     */
    protected abstract function canResolve(array &$type);
    
    /**
     * convert the primitive type into a typed value
     * 
     * @param mixed $value the primitive value
     * @param array $type the excpected type, exctract with the method comment
     * 
     * @return the typed value
     */
    protected abstract function resolve($value,array &$type);
    
    
    /**
     * add a resolver just after the resolver
     * 
     * @param \ssa\runner\resolver\PrimitiveResolverCOR a primitive resolver
     */
    public function addResolver(PrimitiveResolverCOR $resolver) {
        if ($this->nextResolver != null) {
            $resolver->addResolver($this->nextResolver);
        }
        $this->nextResolver = $resolver;
    }
    
    /**
     * {@inheritdoc}
     */
    public function resolvePrimitive($value,array &$type) {
        if ($this->canResolve($type)) {
            return $this->resolve($value, $type);
        } else if ($this->nextResolver != null) {
            return $this->nextResolver->resolvePrimitive($value, $type);
        }
        throw new TypeNotSupportedException($value);
    }

}
