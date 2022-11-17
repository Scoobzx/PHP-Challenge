<?php

class WallysWidgetsCalculator {

    public function getPacks(int $widgetsRequired, array $packSizes): array
    {
        # check if theres an exact match
        if (in_array($widgetsRequired, $packSizes))
            return [$widgetsRequired => 1];

        # Create out output array
        $output = [];

        # Sort the array into descending order
        rsort($packSizes);

        # Loop then divide until we have run out of packs
        foreach ($packSizes as $packSize)
        {
            $division = (int) floor($widgetsRequired/$packSize);

            # Only if divisible apply the pack size to the output
            if ($division > 0) {
                $widgetsRequired = $widgetsRequired - ($packSize * $division);
                $output[$packSize] = $division;
            }
        }

        # If we got no matches then return the lowest pack size we have
        if (count($output) === 0)
            return [min($packSizes) => 1];

        # If we're still above 0, apply the lowest pack size we have
        if ($widgetsRequired > 0)
        {
            #Null coalesce - if returns null - +1
            $output[min($packSizes)] = ($output[min($packSizes)] ?? 0) + 1;
            $widgetsRequired = $widgetsRequired - $output[min($packSizes)];
        }

        # Sort the array by pack sizes
        krsort($output);

        # Loop to see if we can use fewer quantities
        foreach ($output as $packSize => $quantity)
        {
            # Loop the pack sizes to check better matches
            foreach ($packSizes as $packSizeToCheck)
            {
                # Multiply the pack size by the quantity and divide by the pack size to check for better match
                $equivalentValue = floor(($packSize * $quantity) / $packSizeToCheck);

                # If the "better value" is bigger than 0, less than the current "best value" and not the current packSize
                if ($equivalentValue > 0) {

                    # Remove the current value we're using
                    $output[$packSize] = null;

                    # Find quantity that is stored and increase it
                    $packSizeQty = 0;

                    if (!empty($output[$packSizeToCheck])) {
                        $packSizeQty += $output[$packSizeToCheck];
                    }

                    $output[$packSizeToCheck] = $packSizeQty + $equivalentValue;

                    # Break from trying anything smaller
                    break;
                }

            }

        }

        #fixes 1 more additionally but breaks old passes & packs dont add up properly

        $totalValue = 0;

        foreach ($output as $packSize => $qty)
            $totalValue += $packSize * $qty;

        foreach ($packSizes as $packSize)
            if ($packSize % $totalValue === 0)
                return [$packSize => $packSize/$totalValue];



        # Return array with removed null values
        return array_filter($output, function($qty) {
            return !is_null($qty);
        });


        }
    }

