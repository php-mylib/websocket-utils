<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/4/10 0010
 * Time: 22:15
 */

namespace MyLib\WebSocket\Util;

/**
 * Interface WSInterface
 * @package MyLib\WebSocket\Util
 *
 * @property array $opCodes The ws protocal opCode list. @see `OPCODE_*`
 */
interface WebSocketInterface
{
    /**
     * version
     */
    const VERSION = '0.6.1';

    /**
     * the driver name
     * @var string
     */
    const DRIVER_NAME = '';

    const UPDATE_TIME = '2017-05-14';

    const HEADER_END = "\r\n\r\n";

    const SIGN_KEY = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
    // const TOKEN_CHARS = ' abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"§$%&/()=[]{}';

    const TOKEN_CHARS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"$&/()=[]{}0123456789';

    const DEFAULT_HOST = '0.0.0.0';
    const DEFAULT_PORT = 8080;

    /**
     * Websocket version
     */
    const WS_VERSION = '13';

    // internal, see rfc
    const WS_FIN = 128;
    const WS_MASK = 128;

    const OPCODE_CONT = 0; // CONTINUATION
    const OPCODE_TEXT = 1;
    const OPCODE_BINARY = 2;
    const OPCODE_CLOSE = 8;
    const OPCODE_PING = 9;
    const OPCODE_PONG = 10;

    // websocket 在任何时候都会处于下面4种状态中的其中一种
    const STATE_WAITING = 0; // 表示还没建立连接
    const STATE_CONNECTED = 1; // 已经建立连接，可以进行通讯// STATE_OPEN
    const STATE_CLOSING = 2; // 通过关闭握手，正在关闭连接
    const STATE_CLOSED = 3; // 连接已经关闭或无法打开

//CONNECTING	0	The connection is not yet open.
//OPEN	1	The connection is open and ready to communicate.
//CLOSING	2	The connection is in the process of closing.
//CLOSED	3	The connection is closed or couldn't be opened.


    const STATUS_NORMAL_CLOSE = 1000;
    const STATUS_GONE_AWAY = 1001;
    const STATUS_PROTOCOL_ERROR = 1002;
    const STATUS_UNSUPPORTED_MESSAGE_TYPE = 1003;
    const STATUS_MESSAGE_TOO_BIG = 1004;
    const STATUS_TIMEOUT = 1100;

    // custom ws handler position
    const CONNECT_HANDLER = 0;
    const HANDSHAKE_HANDLER = 1;
    const OPEN_HANDLER = 2;
    const MESSAGE_HANDLER = 3;
    const CLOSE_HANDLER = 4;
    const ERROR_HANDLER = 5;

    // 事件的回调函数名
    const ON_CONNECT = 'connect';
    const ON_HANDSHAKE = 'handshake';
    // websocket api都至少要提供下面三个事件的api接口
    const ON_OPEN = 'open';
    const ON_MESSAGE = 'message';
    const ON_CLOSE = 'close';

    const ON_ERROR = 'error';
    const ON_TICK = 'tick';

    /**
     * Websocket blob type.
     */
    const BINARY_TYPE_BLOB = "\x81";

    /**
     * Websocket array buffer type.
     */
    const BINARY_TYPE_ARRAY_BUFFER = "\x82";

    /**
     * @return string
     */
    public function getDriverName(): string;
}
