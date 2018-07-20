<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 20/07/2018
 * Time: 11:48
 */

namespace CwsOps\LivePerson\Services;

/**
 * Class RequestNotSentException
 * @package CwsOps\LivePerson\Services
 */
class RequestNotSentException extends \Exception
{
    public function __construct()
    {
        parent::__construct($message = 'No request has been sent, you need call a service first.', 500);
    }
}