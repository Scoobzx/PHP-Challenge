<?php

class PossibleAnswer
{
    private $answer;
    private $widgetsRequired;

    public function __construct(array $answer, int $widgetsRequired)
    {
        $this->answer = $answer;
        $this->widgetsRequired = $widgetsRequired;
    }

    public function result()
    {
        return $this->answer;
    }

    public function numPacks()
    {
        return array_sum($this->answer);
    }

    public function widgetCount()
    {
        $count = 0;
        foreach ($this->answer as $packSize => $quantity) {
            $count += $packSize * $quantity;
        }
        return $count;
    }

    public function wastedWidgets()
    {
        // var_dump($this->widgetCount(), $this->widgetsRequired); die;
        return $this->widgetCount() - $this->widgetsRequired;
    }
}
