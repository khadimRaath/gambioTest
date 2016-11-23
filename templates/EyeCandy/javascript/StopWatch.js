/* StopWatch.js <?php
#   --------------------------------------------------------------
#   StopWatch.js 2011-03-07 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2011 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

/*
 * class to measure javascripts
 */
function StopWatch(p_warning_time_limit)
{
    /*
     * Total time
     */
    var v_total_time = 0;
    /*
     * Start time
     */
    var v_start_time = 0;
    /*
     * Stop time
     */
    var v_stop_time = 0;
    /*
     * Warning time limit
     */
    var v_warning_time_limit = p_warning_time_limit;

    /*
     * Start the stopwatch and set the start time to now and stop time to 0
     * @return bool true
     */
    this.start = function()
    {
        var coo_date = new Date();
        var t_microtime = coo_date.getTime();

        v_start_time = t_microtime;
        v_stop_time = 0;
        return true;
    }

    /*
     * Set the stop time to now and added the different to total_time.
     * @return bool true
     */
    this.stop = function()
    {
        var coo_date = new Date();
        var t_microtime = coo_date.getTime();

        v_stop_time = t_microtime;
        v_total_time += v_stop_time - v_start_time;
        return true;
    }

    /*
     * If stop time not set, returns the time between now and starttime.
     * Else the time between stop- and starttime
     * @return float Current time
     */
    this.get_current_time = function()
    {
        var coo_date = new Date();
        var t_microtime = coo_date.getTime();
        var t_output_value = 0;
        t_output_value = v_stop_time - v_start_time;
        if(v_stop_time == 0) {
            t_output_value = t_microtime - v_start_time;
        }

	// Hier vielleicht noch mal die Ausgabe formatieren
        return t_output_value;
    }

    /*
     * Logs the current time
     * @param string $p_debug_notice  Debug notice
     * @return bool true
     */
    this.log_current_time = function(p_debug_notice)
    {
        // Get the current time
        var t_exec_time = this.get_current_time();
        // Log the time to FireBug console
        if(fb) {
            console.log('execution time (secs): ' + (t_exec_time / 1000) + ((t_exec_time >= v_warning_time_limit) ? ' !!!' : '') + ' ' + p_debug_notice);
        }
        return true;
    }

    /*
     * Get the current time
     * @return float Total time
     */
    this.get_total_time = function()
    {
	var t_output = v_total_time;
	// Hier vielleicht noch mal die Ausgabe formatieren
        return t_output;
    }

    /*
     * Log total time
     * @param string $p_debug_notice Debug notice
     * @return bool true;
     */
    this.log_total_time = function(p_debug_notice)
    {
        // Get total time
        var t_exec_time = this.get_total_time();
        // Log the time to FireBug console
        if(fb) {
            console.log('execution time (secs): ' + (t_exec_time / 1000) + ((t_exec_time >= v_warning_time_limit) ? ' !!!' : '') + ' ' + p_debug_notice);
        }
        return true;
    }
}