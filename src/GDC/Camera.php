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

        $imageStream = $response->getBody(true);

        $image = imagecreatefromstring($imageStream);
        $resampledImage = imagecreatetruecolor(320, 240);
        imagecopyresampled($resampledImage, $image, 0, 0, 0, 0, 320, 240, 640, 480);

        ob_start();
        imagejpeg($resampledImage);

        return ob_get_clean();
    }
}
