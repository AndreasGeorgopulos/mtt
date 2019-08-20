<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BracketClosures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:bracket-closures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tömb és string műveletek: zárójelek';

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
            $string = $this->readLineString();
            $this->checkString($string, function (int $result) : void {
                printf('Kimenet: %s', ($result == -1 ? 'OK' : 'Hibás index: ' . $result));
            });
        }
        catch (\Exception $ex) {
            printf('%s', $ex->getMessage());
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function readLineString () : string {
        printf('Szöveg: ');
        if (!($string = readline())) {
            throw new \Exception('Hibás adat, a szöveget kötelező megadni.');
        }
        return $string;
    }

    /**
     * @param string $string
     * @param object $completeCallback
     * @return int
     */
    private function checkString (string $string, object $completeCallback) : int {
        $result = -1;
        $brackets = ['openers' => ['(', '[', '{'], 'closures' => [')', ']', '}'],];
        $openers = [];
        for ($i = 0; $i < strlen($string); $i++) {
            if (in_array($string[$i], $brackets['openers'])) {
                array_push($openers, $string[$i]);
                continue;
            }

            if (in_array($string[$i], $brackets['closures'])) {
                if (empty($openers)) {
                    $result = $i;
                    break;
                }

                $last_opener = array_pop($openers);
                if ($string[$i] != $brackets['closures'][array_search($last_opener, $brackets['openers'])]) {
                    $result = $i;
                    break;
                }
            }
        }

        if ($result == -1 && count($openers)) {
            $result = strlen($string);
        }

        if (is_callable($completeCallback)) {
            $completeCallback($result);
        }

        return $result;
    }
}
