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

/*
 * Version details
 *
 * @package    block_moodletest
 * @copyright  Mohan Lal Sharma<mohan202145@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || exit;

class block_moodletest extends block_list {
    public function init() {
        $this->title = get_string('moodletest', 'block_moodletest');
    }

    public function applicable_formats() {
        return ['site' => false, 'course' => true];
    }

    public function get_content() {
        global $COURSE, $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = [];
        $this->content->footer = '';

        $courseid = $COURSE->id;
        if ($courseid <= 0) {
            $courseid = SITEID;
        }

        $this->content = new stdClass();
        $sql = "SELECT cm.id, module, added,m.name FROM {course_modules} as cm
                INNER JOIN {modules} as m on m.id =cm.module
                where cm.course = $courseid AND cm.completion = 1";

        $records = $DB->get_records_sql($sql);
        foreach ($records as $record) {
            $moduleinfosql = 'Select * from {' . $record->name . "} where course=$courseid";
            $modulerecord = $DB->get_record_sql($moduleinfosql);
            $module = $record->name;
            $activityname = $modulerecord->name;
            $moduleid = $record->id;
            $creationdate = $record->added;
            $url = new moodle_url("/mod/$module/view.php");

            $url->params(['id' => $moduleid]);
            $this->content->items[] = '<a href="' . $url->out() . '">'
                . $moduleid . ' ' . $activityname . ' ' . userdate($creationdate, '%d-%m-%Y') . '</a>';
        }

        return $this->content;
    }
}
