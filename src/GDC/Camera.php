<?php

namespace GDC;

use Guzzle\Http\Client;

class Camera
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    public function __construct(Client $client, $username, $password)
    {
        $this->client = $client;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getSnapshot()
    {
        $image = $this->loadImageFromCamera();
        $resampledImage = $this->resize($image);

        return $this->renderToJpeg($resampledImage);
    }

    /**
     * @return resource
     */
    private function loadImageFromCamera()
    {
        $request = $this->client->get('/snapshot.cgi')->setAuth($this->username, $this->password);
        $response = $request->send();

        $imageStream = $response->getBody(true);

        return imagecreatefromstring($imageStream);
    }

    /**
     * @param $imageResource
     * @return resource
     */
    private function resize($imageResource)
    {
        $resampledImage = imagecreatetruecolor(320, 240);
        imagecopyresampled($resampledImage, $imageResource, 0, 0, 0, 0, 320, 240, 640, 480);

        return $resampledImage;
    }

    /**
     * @param $imageResource
     * @return string
     */
    private function renderToJpeg($imageResource)
    {
        ob_start();
        imagejpeg($imageResource);

        return ob_get_clean();
    }
}
