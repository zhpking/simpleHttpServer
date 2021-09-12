<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Go\Micro\Srv\Test;

/**
 */
class TestClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \Go\Micro\Srv\Test\UserRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetUserInfo(\Go\Micro\Srv\Test\UserRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/go.micro.srv.test.Test/GetUserInfo',
        $argument,
        ['\Go\Micro\Srv\Test\UserResponse', 'decode'],
        $metadata, $options);
    }

}
