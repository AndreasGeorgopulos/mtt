<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mockery\Exception;

class SumOfSubset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:sum-of-subset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tömb és string műveletek: részhalmaz összege';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            printf('%s', $this->description . chr(10));
            $numbers = $this->readLineNumbers();
            $sum = $this->readLineSum();
            $this->getSumOfSubset($numbers, $sum, function (array $res) use ($numbers, $sum) : void {
                printf('Részhalmaz: [%s]', $res['y'] > -1 ? ($res['x'] . '-' . $res['y']) : 'n/a');
            });
        }
        catch (\Exception $ex) {
            printf('%s', $ex->getMessage());
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function readLineNumbers () : array {
        printf('Tömbelemek: ');
        $numbers = readline();
        if (!preg_match('/^[0-9\,]+$/', $numbers)) {
            throw new \Exception('Hibás adat, a tömbelemek csak számokat tartalmazhat vesszővel elválasztva.');
        }
        return explode(',', $numbers);
    }

    /**
     * @return int
     * @throws \Exception
     */
    private function readLineSum () : int {
        printf('Összeg: ');
        $sum = readline();
        if (!preg_match('/^[0-9]+$/', $sum)) {
            throw new \Exception('Hibás adat, az összeg csak pozitív egész szám lehet.');
        }
        return (int) $sum;
    }

    /**
     * @param array $numbers
     * @param int $sum
     * @param object|null $completeCallback
     * @return array
     */
    private function getSumOfSubset (array $numbers, int $sum, object $completeCallback = null) : array {
        $result = [
            'x' => -1,
            'y' => -1,
        ];

        $left_begin = 0;
        $left_end = 1;
        $right_begin = count($numbers) - 2;
        $right_end = count($numbers) - 1;

        while ($result['x'] == -1 && $result['y'] == -1 && $left_end <= $right_end) {
            $left_sum = array_sum(array_slice($numbers, $left_begin, (($left_end - $left_begin) + 1)));
            if ($left_sum == $sum) {
                $result['x'] = $left_begin;
                $result['y'] = $left_end;
                continue;
            }
            else if ($left_sum < $sum) {
                $left_end++;
            }
            else {
                $left_begin++;
                $left_end = $left_begin + 1;
            }

            $right_sum = array_sum(array_slice($numbers, $right_begin, (($right_end - $right_begin) + 1)));
            if ($right_sum == $sum) {
                $result['x'] = $right_begin;
                $result['y'] = $right_end;
                continue;
            }
            else if ($right_sum < $sum) {
                $right_begin--;
            }
            else {
                $right_end--;
                $right_begin = $right_end - 1;
            }
        }

        if (is_callable($completeCallback)) {
            $completeCallback($result);
        }

        return $result;
    }
}
