<?php

require __DIR__ . '/PossibleAnswer.php';

class WallysWidgetsCalculator
{
    /**
     * @var array
     */
    private $packSizes = [];

    /**
     * Your solution should return an array with the pack sizes as the key
     * and the number of packs in that size as the value.
     *
     * Pack sizes that are not required should not be included.
     *
     * Example:
     *
     * getPacks(251, [
     *  250,
     *  500,
     *  1000
     * ])
     *
     * should return:
     *
     * [500 => 1]
     */
    public function getPacks(int $widgetsRequired, array $packSizes): array
    {
        $this->widgetsRequired = $widgetsRequired;
        $this->remaining = $widgetsRequired;

        arsort($packSizes);
        $this->packSizes = array_values($packSizes);


        if ($this->exactPackSizeExists()) {
            return [$this->widgetsRequired => 1];
        }

        $possibles = [];

        $windowA = range(0, count($this->packSizes)-1);
        $windowAPosition = 0;
        $windowB = range(0, count($this->packSizes)-1);
        $windowBPosition = 0;

        foreach ($windowA as $position) {
            $windowBPosition = $position;
            $possible = [
                'a' => [],
                'b' => [],
            ];

            $size = $this->packSizes[$position];
            if ($this->sizeCanContainRequiredWidgets($size)) {
                $possible['a'][$size] = 1;

                $possibles[] = new PossibleAnswer(
                    $possible['a'] + $possible['b'],
                    $this->widgetsRequired
                );

                // $test['a'] = [100 => 1, 200 => 2];
                // $test['b'] = [300 => 3, 400 => 4];
                // var_dump($test, $test['a'] + $test['b']); die;

                // Already cover widgets required.
                // next($windowB);
                continue;
            }

            $timesPackFitsIntoQuantity = intval(floor($this->remaining / $size), 0);

            $possible['a'][$size] = $timesPackFitsIntoQuantity;
            $this->remaining = $this->remaining % $size;



            foreach ($windowB as $positionB) {
                $sizeB = $this->packSizes[$positionB];
                if ($sizeB >= $size) {
                    continue;
                }

                if ($this->sizeCanContainRequiredWidgets($sizeB)) {

                }

                var_dump($this->widgetsRequired, $sizeB); die;
            }

        }

        $bestOption = null;

        // var_dump($possibles, 'possibles'); die;
        foreach ($possibles as $possible) {
            // var_dump($possible, 'a possible'); die;
            if (empty($bestOption)) {
                $bestOption = $possible;
                continue;
            }

            if ($this->isABetterOption($possible, $bestOption)) {
                $bestOption = $possible;
                continue;
            }
        }
        // var_dump($bestOption->result(), 'here'); die;

        return $bestOption ? $bestOption->result() : [];
    }

    private function isABetterOption($possible, $bestOption)
    {
        // $possible = [200 => 1, 300 => 2];
        $numberOfPacksForPossible = $possible->numPacks();
        $numberOfStockForPossible = $possible->widgetCount();
        $wasteProducedByPossible  = $possible->wastedWidgets();

        $numberOfPacksForBest = $bestOption->numPacks();
        $numberOfStockForBest = $bestOption->widgetCount();
        $wasteProducedByBest  = $bestOption->wastedWidgets();

        if ($wasteProducedByPossible < $wasteProducedByBest) {
            return true;
        }

        if ($numberOfStockForPossible < $numberOfStockForBest) {
            return true;
        }

        if ($numberOfPacksForPossible < $numberOfPacksForBest) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a given pack size exists in the available pack sizes.
     * @param  int  $size
     * @return bool
     */
    private function exactPackSizeExists(): bool
    {
        return in_array($this->widgetsRequired, $this->packSizes);
    }

    public function sizeCanContainRequiredWidgets(int $size)
    {
        return $size >= $this->remaining;
    }
}
