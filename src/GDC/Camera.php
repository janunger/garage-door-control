<?php

namespace GDC;

use DateTime;
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
        $this->addTimestamp($resampledImage);

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
        $targetWidth = 320;
        $targetHeight = 240;

        $resampledImage = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresampled($resampledImage, $imageResource, 0, 0, 0, 0, $targetWidth, $targetHeight, 640, 480);

        return $resampledImage;
    }

    /**
     * @param $imageResource
     * @return resource
     */
    private function addTimestamp($imageResource)
    {
        $now = new DateTime();
        $timestamp = $now->format('d.m.Y H:i:s');

        $black = imagecolorallocate($imageResource, 0, 0, 0);
        imagestring($imageResource, 5, 12, 12, $timestamp, $black);

        $white = imagecolorallocate($imageResource, 255, 255, 255);
        imagestring($imageResource, 5, 10, 10, $timestamp, $white);

        return $imageResource;
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
