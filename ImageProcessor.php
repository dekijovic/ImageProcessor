<?php

Class ImageProcessorService
{

    public function __construct($storagePath = __DIR__)
    {
        $this->storagePath = $storagePath;
    }

    /**
     * @param $base64
     * @param $fileName
     * @return string
     * @throws Exception
     */
    public function convertImageFromBase64($base64, $fileName)
    {
        $process = $this->convert($base64, $fileName);
        return $process['filename'];
    }

    /**
     * @param $base64
     * @param $fileName
     * @param $rWidth
     * @param $rHeight
     * @return string
     * @throws Exception
     */
    public function convertImageFromBase64AndResize($base64, $fileName, $rWidth, $rHeight)
    {
        $process = $this->convert($base64, $fileName);
        $pathName = $process["filename"];


        list($width, $height) = getimagesize($this->storagePath . $pathName);
        $thumb = imagecreatetruecolor($rWidth, $rHeight);
        switch ($process["type"]) {
            case "png":
                $this->png($thumb, $this->storagePath . $pathName, $rWidth, $rHeight, $width, $height);
                break;
            case "jpeg":
                $this->jpeg($thumb, $this->storagePath . $pathName, $rWidth, $rHeight, $width, $height);
                break;
            case "jpg":
                $this->jpeg($thumb, $this->storagePath . $pathName, $rWidth, $rHeight, $width, $height);
                break;
            case "gif":
                $this->gif($thumb, $this->storagePath . $pathName, $rWidth, $rHeight, $width, $height);
        }
        return $pathName;
    }

    /**
     * Converting base64 to File Image under given fileName
     *
     * @param $base64
     * @param $fileName
     * @return array
     * @throws Exception
     */
    private function convert($base64, $fileName)
    {
        $folder = explode("/", $fileName);
        $folderPath = $this->storagePath;
        for ($i = 0; $i < count($folder) - 1; $i++) {
            $folderPath .= $folder[$i] . "/";
            if (!is_dir($folderPath)) {
                mkdir($folderPath, 0700);
            }
        }
        $data = explode(',', $base64);

        if (preg_match("/png/", $data[0])) {
            $type = "png";
        } elseif (preg_match("/jpeg/", $data[0])) {
            $type = "jpg";
        } elseif (preg_match("/jpg/", $data[0])) {
            $type = "jpg";
        } elseif (preg_match("/gif/", $data[0])) {
            $type = "gif";
        } else {
            throw new Exception("Image format not supported");
        }

        $theFileName = $fileName . "." . $type;

        $ifp = fopen($this->storagePath . $theFileName, 'wb');

        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);
        return ["filename" => $theFileName, "type" => $type];
    }

    /**
     * Creating png file
     *
     * @param $thumb
     * @param $path
     * @param $rWidth
     * @param $rHeight
     * @param $width
     * @param $height
     */
    private function png($thumb, $path, $rWidth, $rHeight, $width, $height)
    {
        $source = imagecreatefrompng($path);
        imagecopyresized($thumb, $source, 0, 0, 0, 0, $rWidth, $rHeight, $width, $height);
        imagepng($thumb, $path, 9);
    }

    /**
     * Creating jpeg file
     *
     * @param $thumb
     * @param $path
     * @param $rWidth
     * @param $rHeight
     * @param $width
     * @param $height
     */
    private function jpeg($thumb, $path, $rWidth, $rHeight, $width, $height)
    {
        $source = imagecreatefromjpeg($path);
        imagecopyresized($thumb, $source, 0, 0, 0, 0, $rWidth, $rHeight, $width, $height);
        imagejpeg($thumb, $path, 100);
    }

    /**
     * Creating gif file
     *
     * @param $thumb
     * @param $path
     * @param $rWidth
     * @param $rHeight
     * @param $width
     * @param $height
     */
    private function gif($thumb, $path, $rWidth, $rHeight, $width, $height)
    {
        $source = imagecreatefromgif($path);
        imagecopyresized($thumb, $source, 0, 0, 0, 0, $rWidth, $rHeight, $width, $height);
        imagegif($thumb, $path);
    }
}