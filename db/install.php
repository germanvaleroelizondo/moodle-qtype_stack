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
 * Stack question type installation code.
 *
 * @package    qtype_stack
 * @copyright  2012 Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../stack/utils.class.php');
require_once(__DIR__ . '/../stack/cas/installhelper.class.php');
require_once(__DIR__ . '/../stack/cas/connectorhelper.class.php');


function xmldb_qtype_stack_install() {
    global $CFG;

    // Define stackmaximaversion config parameter.
    if (!preg_match('~stackmaximaversion:(\d{10})~',
            file_get_contents($CFG->dirroot . '/question/type/stack/stack/maxima/stackmaxima.mac'), $matches)) {
        throw new coding_exception('Maxima libraries version number not found in stackmaxima.mac.');
    }
    set_config('stackmaximaversion', $matches[1], 'qtype_stack');

    // Note we set these next two settings here, rather than rely on a default
    // in settings.php, so that the guessed values are not overwritten in the
    // PHPunit/Behat case.

    // Make an reasonable guess at the OS.
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // See http://stackoverflow.com/questions/1482260/how-to-get-the-os-on-which-php-is-running
        // and http://stackoverflow.com/questions/738823/possible-values-for-php-os.
        set_config('platform', 'win', 'qtype_stack');
    } else {
        set_config('platform', 'unix', 'qtype_stack');
    }

    // Set this to blank, so it is guessed at runtime.
    set_config('maximacommand', '', 'qtype_stack');

    // If we are installing for a test, get maxima set up.
    if (defined('PHPUNIT_UTIL') || defined('BEHAT_UTIL')) {
        require_once($CFG->dirroot . '/question/type/stack/tests/fixtures/test_maxima_configuration.php');
        if (qtype_stack_test_config::is_test_config_available()) {
            qtype_stack_test_config::setup_test_maxima_connection();
        }
    }
    print_object(get_config('qtype_stack')); // DONOTOCOMMIT
    die;
}
