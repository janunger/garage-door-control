<?php

namespace GDC;

use Guzzle\Http\Client;

class Camera
{
    /**
     * @return string
     */
    public function getSnapshot()
    {
        $client = new Client('http://192.168.73.229');
        $request = $client->get('/snapshot.cgi')->setAuth('visitor', 'lvp3k4XiPcnV');
        $response = $request->send();

        return $response->getBody(true);
    }
}
