<?php

namespace SingleTableInheritance;

use RuntimeException;

class ChildClassNotFoundException extends RuntimeException
{
    /**
     * ChildClassNotFoundException constructor.
     *
     * @param string $childClass
     */
    public function __construct($childClass)
    {
        parent::__construct("The class {$childClass} does not exist");
    }

}