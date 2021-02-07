<?php
namespace wapmorgan\UnifiedArchive\Formats\OneFile;

use wapmorgan\UnifiedArchive\Formats;

class Bzip extends OneFileDriver
{
    const FORMAT_SUFFIX =  'bz2';

    /**
     * @return array
     */
    public static function getSupportedFormats()
    {
        return [
            Formats::BZIP,
        ];
    }

    /**
     * @param $format
     * @return bool
     */
    public static function checkFormatSupport($format)
    {
        switch ($format) {
            case Formats::BZIP:
                return extension_loaded('bz2');
        }
    }

    /**
     * @inheritDoc
     */
    public static function getDescription()
    {
        return 'adapter for ext-bzip2';
    }

    /**
     * @inheritDoc
     */
    public static function getInstallationInstruction()
    {
        return 'install `bzip2` extension';
    }

    /**
     * @inheritDoc
     */
    public function __construct($archiveFileName, $format, $password = null)
    {
        parent::__construct($archiveFileName, $password);
        $this->modificationTime = filemtime($this->fileName);
    }

    /**
     * @param string $fileName
     *
     * @return string|false
     */
    public function getFileContent($fileName = null)
    {
        return bzdecompress(file_get_contents($this->fileName));
    }

    /**
     * @param string $fileName
     *
     * @return bool|resource|string
     */
    public function getFileResource($fileName = null)
    {
        return bzopen($this->fileName, 'r');
    }

    /**
     * @param string $data
     * @param int $compressionLevel
     * @return mixed|string
     */
    protected static function compressData($data, $compressionLevel)
    {
        static $compressionLevelMap = [
            self::COMPRESSION_NONE => 1,
            self::COMPRESSION_WEAK => 2,
            self::COMPRESSION_AVERAGE => 4,
            self::COMPRESSION_STRONG => 7,
            self::COMPRESSION_MAXIMUM => 9,
        ];
        // it seems not working at all
        $work_factor = ($compressionLevelMap[$compressionLevel] * 28);
        return bzcompress($data, $compressionLevelMap[$compressionLevel], $work_factor);
    }
}