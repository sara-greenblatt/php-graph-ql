<?php

function binary_search(array $sortedArray, $value): int
{
    error_log('binary search is performed');
    $low = 0;
    $high = count($sortedArray) - 1;
    for (
        $mid = intval($low + ($high - $low) / 2 - 1);
        $sortedArray[$mid] !== $value && $high > $low && $low > -1 && $high < count($sortedArray);
        $mid = $low + ($high - $low) / 2
    ) {
        if ($value > $sortedArray[$mid]) {
            $low = $mid + 1;
        } else {
            $high = $mid - 1;
        }
    }
    return $sortedArray[$mid] == $value ? $mid : -1;
}

function int_to_letter(array $arr): string
{
    $greeting = 'We are going to learn numbers from 1 to 10';
    $dict = ['one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten'];
    $numbers_str = implode(',', array_map(fn ($x, $index) => (string)$x . ' ' . $dict[$index], $arr));
    return $greeting . ': ' . $numbers_str;
}
