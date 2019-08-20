<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Palindrome extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:palindrome';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tömb és string műveletek: palindrom';

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
            $this->checkString($string, function (bool $result) : void {
                printf('%s palindrom', (!$result ? 'nem' : ''));
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
     * @param object|null $completeCallback
     * @return bool
     */
    private function checkString (string $string, object $completeCallback = null) : bool {
        $string = $this->changeAccentedChars(mb_strtolower(preg_replace('/[^[:alnum:]]/u', '', $string)));
        $reversed_string = self::changeAccentedChars(self::mbStrRev($string));
        $result = strcmp($string, $reversed_string) == 0 ? true : false;

        if (is_callable($completeCallback)) {
            $completeCallback($result);
        }

        return $result;
    }

    /**
     * @param string $string
     * @return string
     */
    private function changeAccentedChars (string $string) : string {
        return str_replace(
            ['á', 'é', 'í', 'ó', 'ö', 'ő', 'ú', 'ü', 'ű'],
            ['a', 'e', 'i', 'o', 'o', 'o', 'u', 'u', 'u'],
            $string
        );
    }

    /**
     * @param string $string
     * @param null $encoding
     * @return string
     */
    private static function mbStrRev (string $string, $encoding = null) : string {
        if ($encoding === null) {
            $encoding = mb_detect_encoding($string);
        }

        $length = mb_strlen($string, $encoding);
        $reversed = '';
        while ($length-- > 0) {
            $triple_char = mb_substr($string, ($length - 2), 3, $encoding);
            if ($triple_char == 'dzs') {
                $reversed .= $triple_char;
                $length-=2;
                continue;
            }

            $double_char = mb_substr($string, ($length - 1), 2, $encoding);
            if (in_array($double_char, ['cs', 'dz', 'ly', 'ny', 'sz', 'zs'])) {
                $reversed .= $double_char;
                $length--;
                continue;
            }

            $reversed .= mb_substr($string, $length, 1, $encoding);
        }

        return $reversed;
    }
}
