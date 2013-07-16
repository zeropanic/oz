<?php

/**
 * This trait allows you to easily implement the ArrayAccess class of the SPL.
 * @package  Traits
 * @author  zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz\Traits
 */

namespace Oz\Traits;

trait ArrayAccess
{
    /**
     * offsetGet is a getter for offsets.
     * @access  public
     * @param  [string] $offset the offset you want to get
     * @return [mixed]          the content of the offset
     */
    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    /**
     * offsetSet is a setter for offsets.
     * @access  public
     * @param  string $offset      the offset you want to set
     * @param  mixed  $offsetValue the value of the offset
     * @return void
     */
    public function offsetSet($offset, $offsetValue)
    {
        $this->{$offset} = $offsetValue;
    }

    /**
     * offsetExists check whether and offset exists or not.
     * @access  public
     * @param  string $offset the offset you want to check
     * @return boolean        true if the offset exists, false if not
     */
    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }
    
    /**
     * offsetUnset allows you to unset an offset.
     * @access  public
     * @param  string $offset the offset you want to unset
     * @return void
     */
    public function offsetUnset ($offset)
    {
        unset($this->{$offset});
    }
}

?>