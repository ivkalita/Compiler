<?php
    namespace vendor\Utility;

    class TextPoint
    {
        public $x;
        public $y;
        public $idx;

        public function __construct()
        {
            $this->x = 0;
            $this->y = 1;
            $this->idx = -1;
        }

        public function __toString()
        {
            return "{$this->y}:{$this->x}";
        }

    }