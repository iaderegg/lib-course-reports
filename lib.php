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
function get_best_students($courseid, $n_students) {

    global $DB;

    $sql_query  =   "SELECT 
                        users.id AS userid, 
                        ggrades.finalgrade AS finalgrade, 
                        ROW_NUMBER() OVER (ORDER BY ggrades.finalgrade DESC) AS list_position
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
                    LIMIT $n_students";

    $best_students_array = $DB->get_records_sql($sql_query);

    return $best_students_array;
}

/**
 * get_best_students_nosql
 * @param int $courseid    Course ID
 * @param int $n_students  Number of studentes to return
 * @return Array
 * 
 * @author Iader E. Garcia G. <iadergg@gmail.com>
 */
function get_best_students_nosql($courseid, $n_students) {

    global $DB, $CFG;

    require_once $CFG->libdir . '/gradelib.php';
    require_once($CFG->dirroot . '/grade/querylib.php');

    $coursecontext = context_course::instance($courseid);

    $users_objects = get_enrolled_users($coursecontext, '', 0, 'u.id');

    $users_array = array();

    foreach ($users_objects as $user) {
        array_push($users_array, $user->id);
    }

    $grades_info = grade_get_course_grades($courseid, $users_array)->grades;

    $grades = array();

    // Order 
    foreach(array_keys($grades_info) as $key) {
        $grades[$key] = $grades_info[$key]->grade;
        $grades_info[$key]->userid = $key;
    }

    array_multisort($grades, SORT_DESC, $grades_info);

    $position = 0;
    $best_students_array = array();

    foreach(array_keys($grades_info) as $key) {
        $position++;

        if($grades_info[$key]->grade == NULL){
            $grades_info[$key]->grade = "No registra";
        }

        $temp = array(
            'userid' => $grades_info[$key]->userid,
            'finalgrade' => $grades_info[$key]->grade,
            'position' => $position
        );

        array_push($best_students_array, $temp);

        if($position == $n_students) {
            break;
        }
    }

    return $best_students_array;
}

/**
 * get_info_course_sections
 * @param int $courseid    Course ID
 * @return stdClass
 * 
 * @author Iader E. Garcia G. <iadergg@gmail.com>
 */

function get_info_course_sections($courseid, $userid) {

    global $DB;

    $sql_query =   "SELECT
                        sections.id,
                        sections.section AS position,
                        sections.name AS section_name,
                        COUNT(DISTINCT modules_completion.coursemoduleid) AS modules
                    FROM
                        {course_sections} AS sections
                        INNER JOIN {course_modules} AS modules ON modules.section = sections.id
                        LEFT JOIN {course_modules_completion} AS modules_completion ON modules_completion.coursemoduleid = modules.id
                    WHERE
                        sections.course = $courseid
                    GROUP BY
                        sections.id,
                        position,
                        section_name";

    $info_sections_array = $DB->get_records_sql($sql_query);

    return $info_sections_array;
}

//print_r(get_best_students_nosql(32542, 10));
print_r(get_best_students(32542, 10));