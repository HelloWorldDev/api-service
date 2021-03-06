<?php

namespace Being\Api\Service\Device;

use Being\Api\Service\BaseClient;
use Being\Api\Service\Code;
use Being\Api\Service\HttpClient;

class DeviceClient extends BaseClient implements DeviceInterface
{
    public function save(Device $device)
    {
        $bodyArr = $device->toArray();
        $header = $this->getSecretHeader();
        $req = HttpClient::getRequest(HttpClient::POST, 'v1/device', [], $header, $bodyArr);
        list($code, $body, $header) = $this->httpClient->send($req);
        list($code) = $this->parseResponseBody($body);

        return $code == Code::SUCCESS;
    }

    public function find($uid)
    {
        $bodyArr = ['uid' => $uid];
        $header = $this->getSecretHeader();
        $req = HttpClient::getRequest(HttpClient::GET, 'v1/devices', $bodyArr, $header, null);
        list($code, $body, $header) = $this->httpClient->send($req);
        list($code, $devicesArr) = $this->parseResponseBody($body);

        $devices = null;
        if ($code == Code::SUCCESS && count($devicesArr) > 0) {
            $devices = [];
            foreach ($devicesArr as $deviceArr) {
                $devices[] = Device::create($deviceArr);
            }
        }

        return $devices;
    }

    public function remove($uid)
    {
        $bodyArr = ['uid' => $uid];
        $header = $this->getSecretHeader();
        $req = HttpClient::getRequest(HttpClient::DELETE, 'v1/device', $bodyArr, $header, null);
        list($code, $body, $header) = $this->httpClient->send($req);
        list($code) = $this->parseResponseBody($body);

        return $code == Code::SUCCESS;
    }

    public function pushTokens($uidList)
    {
        $uidList = array_slice($uidList, 0, 100);
        $bodyArr = ['uid_list' => implode(',', $uidList)];
        $header = $this->getSecretHeader();
        $req = HttpClient::getRequest(HttpClient::GET, 'v1/push/tokens', $bodyArr, $header, null);
        list($code, $body, $header) = $this->httpClient->send($req);
        list($code, $devicesArr) = $this->parseResponseBody($body);

        $devices = null;
        if ($code == Code::SUCCESS && count($devicesArr) > 0) {
            $devices = [];
            foreach ($devicesArr as $deviceArr) {
                $devices[] = Device::create($deviceArr);
            }
        }

        return $devices;
    }
}
