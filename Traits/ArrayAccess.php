<?php

namespace Oz\Traits;

trait ArrayAccess
{
    
    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    public function offsetSet($offset, $offsetValue)
    {
        $this->{$offset} = $offsetValue;
    }

    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }
    
    public function offsetUnset ($offset)
    {
        unset($this->{$offset});
    }
}

?>