<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Internal library of functions for module tincanlaunch
 *
 * All the tincanlaunch specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package mod_tincanlaunch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();



/*
 * tincanlaunch_get_launch_url
 *
 * Returns a launch link based on various data from Moodle
 *
 * @param none
 * @return string - the launch link to be used. 
 */
function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}
 
function tincanlaunch_get_launch_url($registrationuuid) {
	global $tincanlaunch, $USER, $CFG;
	
	//calculate basic authentication 
	$basicauth = base64_encode($tincanlaunch->tincanlaunchlrslogin.":".$tincanlaunch->tincanlaunchlrspass);
	
	//build the actor object
	$launchActor = array(
		"name" => fullname($USER),
		"account" => array(
			"homePage" => $CFG->wwwroot,
			"name" => $USER->id
		),
		"objectType" => "Agent"
	);
	
	//build the URL to be returned
	$rtnString = $tincanlaunch->tincanlaunchurl."?".http_build_query(array(
	        "endpoint" => $tincanlaunch->tincanlaunchlrsendpoint,
	        "auth" => "Basic ".$basicauth,
	        "actor" => json_encode($launchActor),
	        "registration" => $registrationuuid
	    ), 
	    '', 
	    '&'
	);
	
	//TODO: QUESTION: should we be using $USER->id, $USER->idnumber or even $USER->username ?
	
	return $rtnString;
}
