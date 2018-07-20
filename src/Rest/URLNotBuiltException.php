<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 18/07/2018
 * Time: 12:44
 */

namespace CwsOps\LivePerson\Rest;

/**
 * Class UrlNotBuildException
 * URLNotBuilt will be thrown, when the URL Builder is not locked.
 * @package CwsOps\LivePerson\Rest
 */
class URLNotBuiltException extends \Exception
{
    public function __construct()
    {
        $message = "the URL has not been built, you need to call UrlBuilder::build() before getting the URL";

        parent::__construct($message, 500);
    }
}