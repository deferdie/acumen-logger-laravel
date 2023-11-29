<?php

namespace AcumenLogger;

use COM;
use Exception;

class AcumenPerformance
{
    /**
     * Returns the current server load time
     *
     * @return int
     */
    public static function getServerLoad()
    {
        try {
            if (stristr(PHP_OS, 'win')) {

                $wmi = new COM("Winmgmts://");
                $server = $wmi->execquery("SELECT LoadPercentage FROM Win32_Processor");

                $cpu_num = 0;
                $load_total = 0;

                foreach ($server as $cpu) {
                    $cpu_num++;
                    $load_total += $cpu->loadpercentage;
                }

                $load = round($load_total / $cpu_num);
            } else {

                $sys_load = sys_getloadavg();
                $load = $sys_load[0];
            }

            return (int) $load;
        } catch (Exception $e) {
            // dd($e);
        }
    }
}
