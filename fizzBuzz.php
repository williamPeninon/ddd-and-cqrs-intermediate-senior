<?php

declare(strict_types=1);

/**
 * 
 * Afficher les nombres de 1 à N selon les règles suivantes :
 * si le nombre est divisible par 3 : afficher Fizz
 * si le nombre est divisible par 5 : afficher Buzz
 * si le nombre est divisible par 3 ET 5 : afficher FizzBuzz 
 * sinon : afficher le nombre.
 * 
 * @param int $n
 * @return void
 */
function fizzBuzz(int $n): void
{
    $rules = [
        3  => 'Fizz',
        5  => 'Buzz',
    ];

    for ($i = 1; $i <= $n; $i++) {
        $output = '';

        foreach ($rules as $divisor => $word) {
            if ($i % $divisor === 0) {
                $output .= $word;
            }
        }

        echo ($output ?: $i) . PHP_EOL;
    }
}

fizzBuzz(15);