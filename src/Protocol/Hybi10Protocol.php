<?php

namespace MyLib\WebSocket\Util\Protocol;

/**
 * @see http://tools.ietf.org/html/draft-ietf-hybi-thewebsocketprotocol-10
 */
class Hybi10Protocol extends HybiProtocol
{
    const VERSION = 10;

    /**
     * @see Protocol::getVersion()
     */
    public function getVersion(): int
    {
        return self::VERSION;
    }

    /**
     * @see Protocol::acceptsVersion()
     * @param $version
     * @return bool
     */
    public function acceptsVersion($version): bool
    {
        $version = (int)$version;

        if ($version <= 10 && $version >= 10) {
            return true;
        }

        return false;
    }
}
