<?php 
/*
*	This script was writed by Setec Astronomy - setec@freemail.it
*
*	This class allows to check the online status of an Yahoo! account.	
*	It connects directly to the Yahoo! status server.
*
*	This script is distributed  under the GPL License
*
*	This program is distributed in the hope that it will be useful,
*	but WITHOUT ANY WARRANTY; without even the implied warranty of
*	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* 	GNU General Public License for more details.
*
*	http://www.gnu.org/licenses/gpl.txt
*
*/
define ("YAHOO_ONLINE", 1);
define ("YAHOO_OFFLINE", 2);
define ("YAHOO_UNKNOWN", 3);

class CYahooStatus
{
	function execute ($yahoo = "", &$errno, &$errstr)
	{
		$errno = 0;
		$errstr = "";
		$lines = @file ("http://opi.yahoo.com/online?u=" . $yahoo . "&m=t"); 
		if ($lines !== false) {
			$response = implode ("", $lines);
			if (strpos ($response, "NOT ONLINE") !== false) {
				return YAHOO_OFFLINE;
			} elseif (strpos ($response, "ONLINE") !== false) {
				return YAHOO_ONLINE;
			} else {
				return YAHOO_UNKNOWN;
			}
		} else {
			$errno = 1;
			$errstr = "Unable to connect to http://opi.yahoo.com";
			return false;
		}
	}
}
?>