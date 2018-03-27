<?php

namespace MyLib\WebSocket\Util\Payload;

use MyLib\WebSocket\Util\Frame\Frame;
use MyLib\WebSocket\Util\Exception\FrameException;
use MyLib\WebSocket\Util\Exception\PayloadException;
use MyLib\WebSocket\Util\Exception\SocketException;
use MyLib\WebSocket\Util\Protocol\Protocol;
use Wrench\Socket\Socket;

/**
 * Payload class
 * Represents a WebSocket protocol payload, which may be made up of multiple
 * frames.
 */
abstract class Payload
{
    /**
     * A payload may consist of one or more frames
     *
     * @var Frame[]
     */
    protected $frames = [];

    /**
     * String representation of the payload contents
     *
     * @var string Binary
     */
    protected $buffer;

    /**
     * Encodes a payload
     *
     * @param string  $data
     * @param int     $type
     * @param boolean $masked
     * @return Payload
     * @todo No splitting into multiple frames just yet
     */
    public function encode(string $data, int $type = Protocol::TYPE_TEXT, bool $masked = false): Payload
    {
        $this->frames = [];

        $frame = $this->getFrame();
        $this->frames[] = $frame;

        $frame->encode($data, $type, $masked);

        return $this;
    }

    /**
     * Get a frame object
     *
     * @return Frame
     */
    abstract protected function getFrame(): Frame;

    /**
     * Whether this payload is waiting for more data
     *
     * @return bool
     */
    public function isWaitingForData(): bool
    {
        return $this->getRemainingData() > 0;
    }

    /**
     * Gets the number of remaining bytes before this payload will be
     * complete
     * May return 0 (no more bytes required) or null (unknown number of bytes
     * required).
     *
     * @return int|null
     */
    public function getRemainingData()
    {
        if ($this->isComplete()) {
            return 0;
        }

        try {
            if ($this->getCurrentFrame()->isFinal()) {
                return $this->getCurrentFrame()->getRemainingData();
            }
        } catch (FrameException $e) {
            return null;
        }

        return null;
    }

    /**
     * Whether the payload is complete
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->getCurrentFrame()->isComplete() && $this->getCurrentFrame()->isFinal();
    }

    /**
     * Gets the current frame for the payload
     *
     * @return mixed
     */
    protected function getCurrentFrame()
    {
        if (empty($this->frames)) {
            $this->frames[] = $this->getFrame();
        }
        return end($this->frames);
    }

    /**
     * @param Socket $socket
     * @return bool
     * @throws FrameException
     * @throws SocketException
     */
    public function sendToSocket(Socket $socket): bool
    {
        $success = true;

        foreach ($this->frames as $frame) {
            $success = $success && (
                    $socket->send($frame->getFrameBuffer()) !== null
                );
        }

        return $success;
    }

    /**
     * Receive raw data into the payload
     *
     * @param string $data
     * @return void
     * @throws PayloadException
     */
    public function receiveData(string $data): void
    {
        $chunkSize = null;

        while ($data) {
            $frame = $this->getReceivingFrame();

            $remaining = $frame->getRemainingData();

            if ($remaining === null) {
                $chunkSize = 2;
            } elseif ($remaining > 0) {
                $chunkSize = $remaining;
            }

            $chunkSize = min(\strlen($data), $chunkSize);
            $chunk = substr($data, 0, $chunkSize);
            $data = substr($data, $chunkSize);

            $frame->receiveData($chunk);
        }
    }

    /**
     * Gets the frame into which data should be receieved
     *
     * @throws PayloadException
     * @return Frame
     */
    protected function getReceivingFrame(): Frame
    {
        $current = $this->getCurrentFrame();

        if ($current->isComplete()) {
            if ($current->isFinal()) {
                throw new PayloadException('Payload cannot receive data: it is already complete');
            }

            $this->frames[] = $current = $this->getFrame();
        }

        return $current;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->getPayload();
        } catch (\Exception $e) {
            // __toString must not throw an exception
            return '';
        }
    }

    /**
     * @return string
     * @throws FrameException
     */
    public function getPayload(): string
    {
        $this->buffer = '';

        foreach ($this->frames as $frame) {
            $this->buffer .= $frame->getFramePayload();
        }

        return $this->buffer;
    }

    /**
     * Gets the type of the payload
     * The type of a payload is taken from its first frame
     *
     * @throws PayloadException
     * @return int
     */
    public function getType(): int
    {
        if (!isset($this->frames[0])) {
            throw new PayloadException('Cannot tell payload type yet');
        }

        return $this->frames[0]->getType();
    }
}
