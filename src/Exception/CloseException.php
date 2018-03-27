<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-03-27
 * Time: 9:14
 */

namespace MyLib\WebSocket\Util\Exception;

use MyLib\WebSocket\Util\Protocol\Protocol;

/**
 * Class CloseException
 * @package MyLib\WebSocket\Util\Exception
 */
class CloseException extends \Exception
{
    /**
     * @param string    $message
     * @param int       $code
     * @param \Exception $previous
     */
    public function __construct($message = null, $code = null, $previous = null)
    {
        if ($code === null) {
            $code = Protocol::CLOSE_UNEXPECTED;
        }
        parent::__construct($message, $code, $previous);
    }
}
