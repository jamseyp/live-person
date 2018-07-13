<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 13/07/2018
 * Time: 12:39
 */

namespace CwsOps\LivePerson\DataModels;

/**
 * Class Agent
 *
 * @package CwsOps\LivePerson\DataModels
 */
class Agent
{
    private $id;
    private $deleted;
    private $loginName;
    private $fullName;
    private $nickname;
    private $passwordSh;
    private $isEnabled;
    private $maxChats;
    private $email;
    private $disabledManually;
    private $skillIds = [];
    private $description;
    private $employeeId;
    private $backgndImgUri;

    public function __construct($agent)
    {

    }
}