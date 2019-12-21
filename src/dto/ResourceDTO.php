<?php

namespace kosuha606\Minidoc\dto;

/**
 * Class ResourceDTO
 * @package app\contexts\Application\Docs\package\dto
 * @category DTO
 */
class ResourceDTO
{
    /**
     *
     */
    const TYPE_FILE = 1;

    /**
     *
     */
    const TYPE_URL = 2;

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     * @return ResourceDTO
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return ResourceDTO
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @var
     */
    public $file;

    /**
     * @var
     */
    public $type;

    /**
     * ResourceDTO constructor.
     * @param $file
     * @param $type
     */
    public function __construct($file, $type)
    {
        $this->file = $file;
        $this->type = $type;
    }
}