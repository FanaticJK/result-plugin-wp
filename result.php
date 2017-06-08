<?php
/**
 * @package Ultrabyte
 */
/*
Plugin Name: Result
Description: It is a plugin that inputs results of students and displays them.
Version: 1
Author: Sujan Karki
Text Domain: Ultrabyte
*/

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define('RESULT_SYSTEM_VERSION', '1');
define('RESULT_SYSTEM__MINIMUM_WP_VERSION', '4');
define('RESULT_SYSTEM__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('RESULT_SYSTEM_DELETE_LIMIT', 100000);

register_activation_hook(__FILE__, 'plugin_activation');
register_deactivation_hook(__FILE__, array('Student_Result', 'plugin_deactivation'));

function plugin_activation()
{
    global $wpdb;
    global $result_system_student;
    global $result_system_student_result;
    $result_system_student = $wpdb->prefix . 'student';
    $result_system_student_result = $wpdb->prefix . 'student_meta';
    if (version_compare($GLOBALS['wp_version'], RESULT_SYSTEM__MINIMUM_WP_VERSION, '<')) {
        load_plugin_textdomain('result_system');

        $message = '<strong>' . sprintf(esc_html__('Result System %s requires WordPress %s or higher.', 'Result System'), RESULT_SYSTEM_VERSION, RESULT_SYSTEM__MINIMUM_WP_VERSION) . '</strong> ' . sprintf(__('Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 2.4 of the Result System plugin</a>.', 'Result System'), 'https://codex.wordpress.org/Upgrading_WordPress', 'https://wordpress.org/extend/plugins/Result System/download/');

        //Akismet::bail_on_activation( $message );
    } else {
        $charset_collate = $wpdb->get_charset_collate();
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        // create the ECPT metabox database table
        if ($wpdb->get_var("show tables like '$result_system_student'") != $result_system_student) {
            $sql = "CREATE TABLE " . $result_system_student . " (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`name` varchar(20) NOT NULL,
		`roll_no` varchar(10) NOT NULL,
		`year` varchar(10) NOT NULL,
		`symbol_no` varchar(10) NULL,
		`grade` varchar(10) NOT NULL,
		`section` varchar(10) NOT NULL,
		`term` varchar(50) NOT NULL,
		UNIQUE KEY id (id)
		)$charset_collate;";
            dbDelta($sql);
        }

        if ($wpdb->get_var("show tables like '$result_system_student_result'") != $result_system_student_result) {
            $sql1 = "CREATE TABLE " . $result_system_student_result . " (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`student_id` mediumint(9) NOT NULL,
		`term` varchar(50) NOT NULL,
		`year` varchar(10) NOT NULL,
		`marks_obtined` integer NOT NULL,
		`percentage` integer NOT NULL,
		`rank` integer NOT NULL,
		UNIQUE KEY id (id)
		)$charset_collate;";
            dbDelta($sql1);
        }
    }
}

function result_system_admin_actions()
{
    add_menu_page("Result System", "Result System", 1, "result-system", "result_system_admin");
    add_submenu_page('result-system', 'Display Results', 'Display Results', 2, 'display-results', 'result_system_admin_dashboard');
}

add_action('admin_menu', 'result_system_admin_actions');

function result_system_admin()
{
    include('admin/result_system_admin.php');
}

function result_system_admin_dashboard()
{
    include('admin/result_system_admin_dashboard.php');
}

function form_creation_for_result()
{
    ?>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"
            integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
            crossorigin="anonymous"></script>
    <form name="result_form" method="post" id="result_form" class="resultSubmit form-inline"
          onsubmit="preventDefault()">
        <input type="hidden" name="result_system_hidden" value="Y">
        <?php echo "<h4>" . __('Search Student Result', 'result_system') . "</h4>"; ?>

        <div class="form-group">
            <?php _e("Year: "); ?><input type="text" class="form-control" name="year">
        </div>
        <div class="form-group"><?php _e("Grade: "); ?>
            <select name="grade" id="grade" class="form-control">
                <option value="Nursery">Nursery</option>
                <option value="JKG">JKG</option>
                <option value="SKG">SKG</option>
                <option value="Class I">Class I</option>
                <option value="Class II">Class II</option>
                <option value="Class lll">Class lll</option>
                <option value="Class IV">Class IV</option>
                <option value="Class V">Class V</option>
                <option value="Class VI">Class VI</option>
                <option value="Class VII">Class VII</option>
                <option value="Class VIII">Class VIII</option>
                <option value="Class IX">Class IX</option>
                <option value="Class X">Class X</option>
            </select>
        </div>
        <div class="form-group"><?php _e("Terminal: "); ?>
            <select name="terminal" id="terminal" class="form-control">
                <option value="Ist Terminal" selected="">Ist Terminal</option>
                <option value="2nd Terminal">2nd Terminal</option>
                <option value="3rd Terminal">3rd Terminal</option>
                <option value="Final">Final</option>
            </select>
        </div>
        <input type="hidden" name="action" value="result_display"/>
        <div class="form-group">
            <input type="submit" name="submit" class="form-control" value="<?php _e('Search', 'result_system') ?>"/>
        </div>
    </form>
    <div class="dislplayResult"></div>
    <script type="text/javascript">
        jQuery('#result_form').bind('submit', function () {

            var form = jQuery('#result_form');
            var data = form.serialize();
            var getUrl = window.location;
            var baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
            $.post(baseUrl + '/wp-admin/admin-ajax.php', data, function (response) {
                jQuery('.dislplayResult').html('');
                jQuery('.dislplayResult').html(response);
            });
            return false;
        });
    </script>
    <?php
}

add_shortcode('display_form', 'form_creation_for_result');

add_action("wp_ajax_result_display", "result_display");
add_action("wp_ajax_nopriv_result_display", "result_display");

function result_display()
{
    if ($_POST['result_system_hidden'] == 'Y') {
        global $wpdb;
        $student_meta = $wpdb->prefix . 'student_meta';
        $student = $wpdb->prefix . 'student';
        $year = $_POST['year'];
        $grade = $_POST['grade'];
        $term = $_POST['terminal'];
        $results = $wpdb->get_results('SELECT ' . $student . '.*, ' . $student_meta . '.marks_obtined, ' . $student_meta . '.percentage, ' . $student_meta . '.rank FROM ' . $student . ' JOIN ' . $student_meta . ' ON ' . $student . '.id = ' . $student_meta . '.student_id WHERE ' . $student . '.year = 2074 and ' . $student . '.grade = "' . $grade . '" and ' . $student . '.term = "' . $term . '" ', OBJECT);
        $ij = 0;
        $rowcount = $wpdb->get_var('SELECT ' . $student . '.*, ' . $student_meta . '.marks_obtined, ' . $student_meta . '.percentage, ' . $student_meta . '.rank FROM ' . $student . ' JOIN ' . $student_meta . ' ON ' . $student . '.id = ' . $student_meta . '.student_id WHERE ' . $student . '.year = 2074 and ' . $student . '.grade = "' . $grade . '" and ' . $student . '.term = "' . $term . '" ');
        if ($rowcount > 0) {
            foreach ($results as $key) {
                $year = $key->year;
                $grade = $key->grade;
                $terminal = $key->term;
                if ($ij == 0) {
                    break;
                }
            }
            ob_start();
            ?>
            <h2><?php echo $terminal ?> Examination Result for the Year of <?php echo $year; ?>
                , <?php echo $grade ?></h2>
            <table class="table table-bordered">
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Roll</th>
                    <th>Year</th>
                    <th>Grade</th>
                    <th>Section</th>
                    <th>Term</th>
                    <th>Marks Obtined</th>
                    <th>Percentage</th>
                    <th>Rank</th>
                </tr>
                <?php
                $i = 1;
                foreach ($results as $key) {
                    ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $key->name ?></td>
                        <td><?php echo $key->roll_no ?></td>
                        <td><?php echo $key->year ?></td>
                        <td><?php echo $key->grade ?></td>
                        <td><?php echo $key->section ?></td>
                        <td><?php echo $key->term ?></td>
                        <td><?php echo $key->marks_obtined ?></td>
                        <td><?php echo $key->percentage ?>%</td>
                        <td><?php echo $key->rank ?></td>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
            </table>
            <?php
            $out1 = ob_get_clean();
            print_r($out1);
            die();
        } else {
            print_r("Sorry! No Result Found.");
            die();
        }
    }
}