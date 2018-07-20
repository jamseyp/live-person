<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 18/07/2018
 * Time: 12:34
 */

namespace CwsOps\LivePerson\Rest;

/**
 * Class BuilderLockedException
 * A BuilderLockedException occurs when the URLBuilder
 * has already been locked, but you are trying to pass another URL part.
 *
 * @package CwsOps\LivePerson\Rest
 */
class BuilderLockedException extends \Exception
{
    public function __construct()
    {
        $message = "The URLBuilder is currently locked";
        parent::__construct($message, $code = 500);
    }
}