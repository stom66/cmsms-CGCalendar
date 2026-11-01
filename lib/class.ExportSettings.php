<?php
namespace CGCalendar;

// yup, this is a dumb class.
class ExportSettings
{
    public $datesel;
    public $fromdate;
    public $category;
    public $delimiter;

    public function __construct()
    {
        $this->datesel = 'start';
        $this->delimiter = ',';
    }
}