<?php

require_once '../../../config.php';

/**
 * get_best_students
 * @param int $courseid    Course ID
 * @param int $n_students  Number of studentes to return
 * @return stdClass
 * 
 * @author Iader E. Garcia G. <iadergg@gmail.com>
 */
function get_best_students($courseid, $nstudents) {

    global $DB;

    $sqlquery  =   "SELECT 
                        users.id AS userid, 
                        ggrades.finalgrade AS finalgrade, 
                        ROW_NUMBER() OVER (ORDER BY ggrades.finalgrade DESC) AS position
                    FROM 
                        {grade_grades} AS ggrades 
                        INNER JOIN {grade_items} AS gitems ON ggrades.itemid = gitems.id
                        INNER JOIN {course} AS courses ON gitems.courseid = courses.id
                        INNER JOIN {user} AS users ON ggrades.userid = users.id
                    WHERE
                        ggrades.finalgrade IS NOT NULL
                        AND gitems.itemtype = 'course'
                        AND courses.id = $courseid
                        AND users.deleted = 0
                    ORDER BY 
                        ggrades.finalgrade DESC
                    LIMIT $nstudents";

    $beststudentarray = $DB->get_records_sql($sqlquery);

    return $beststudentarray;
}

/**
 * get_best_students_nosql
 * @param int $courseid    Course ID
 * @param int $n_students  Number of studentes to return
 * @return Array
 * 
 * @author Iader E. Garcia G. <iadergg@gmail.com>
 */
function get_best_students_nosql($courseid, $nstudents) {

    global $DB, $CFG;

    require_once $CFG->libdir . '/gradelib.php';
    require_once($CFG->dirroot . '/grade/querylib.php');

    $coursecontext = context_course::instance($courseid);

    $usersenrolled = get_enrolled_users($coursecontext, '', 0, 'u.id');

    $usersarray = array();

    foreach ($usersenrolled as $user) {
        array_push($usersarray, $user->id);
    }

    $gradesinfo = grade_get_course_grades($courseid, $usersarray)->grades;

    $grades = array();

    // Order 
    foreach(array_keys($gradesinfo) as $key) {
        $grades[$key] = $gradesinfo[$key]->grade;
        $gradesinfo[$key]->userid = $key;
    }

    array_multisort($grades, SORT_DESC, $gradesinfo);

    $position = 0;
    $beststudents = array();

    foreach(array_keys($gradesinfo) as $key) {
        $position++;

        if($gradesinfo[$key]->grade == NULL){
            $gradesinfo[$key]->grade = "-";
        }

        $temp = array(
            'userid' => $gradesinfo[$key]->userid,
            'finalgrade' => $gradesinfo[$key]->grade,
            'position' => $position
        );

        array_push($beststudents, $temp);

        if($position == $nstudents) {
            break;
        }
    }

    return $beststudents;
}

/**
 * get_info_course_sections
 * @param int $courseid    Course ID
 * @param int $userid      User ID
 * @return stdClass
 * 
 * @author Iader E. Garcia G. <iadergg@gmail.com>
 */

function get_info_course_sections_by_user($courseid, $userid) { 

    global $DB;

    $sqlquery =   "SELECT
                        sections.id,
                        sections.section AS section_position,
                        sections.name AS section_name,
                        COUNT(DISTINCT modules_completion.coursemoduleid) AS modules,
                        SUM(modules_completion.completionstate)/COUNT(DISTINCT modules_completion.coursemoduleid)*100 AS percent
                    FROM
                        {course_sections} AS sections
                        INNER JOIN {course_modules} AS modules ON modules.section = sections.id
                        INNER JOIN {course_modules_completion} AS modules_completion ON modules_completion.coursemoduleid = modules.id
                    WHERE
                        sections.course = $courseid
                        AND modules_completion.userid = $userid
                    GROUP BY
                        sections.id,
                        section_position,
                        section_name";

    $infosections = $DB->get_records_sql($sqlquery);

    return $infosections;
}