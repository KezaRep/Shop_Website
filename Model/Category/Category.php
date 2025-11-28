<?php
class Category
{
    public $id;
    public $name;
    public $description;

    public function __construct($id, $name, $description = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }
}
