<?php

class Student_Result
{
    /**
     * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
     * @static
     */
    public static function plugin_activation()
    {
        global $wpdb;
        $result_system_student = $wpdb->prefix . 'student';
        $result_system_student_result = $wpdb->prefix . 'student_meta';
        if (version_compare($GLOBALS['wp_version'], RESULT_SYSTEM__MINIMUM_WP_VERSION, '<')) {
            load_plugin_textdomain('result_system');

            $message = '<strong>' . sprintf(esc_html__('Result System %s requires WordPress %s or higher.', 'Result System'), RESULT_SYSTEM_VERSION, RESULT_SYSTEM__MINIMUM_WP_VERSION) . '</strong> ' . sprintf(__('Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 2.4 of the Result System plugin</a>.', 'Result System'), '', '');

            //Akismet::bail_on_activation($message);
        } else {
            global $wpdb;
            global $result_system_student;
            global $result_system_student_result;
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            // create the ECPT metabox database table
            if ($wpdb->get_var("show tables like '$result_system_student'") != $result_system_student) {
                $sql = "CREATE TABLE " . $result_system_student . " (
                        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                        `name` varchar(20) NOT NULL,
                        `roll_no` varchar(10) NOT NULL,
                        `symbol_no` varchar(10) NOT NULL,
                        `grade` varchar(10) NOT NULL,
                        `section` varchar(10) NOT NULL,
                        `term` varchar(50) NOT NULL,
                        UNIQUE KEY id (id)
                        );";

                dbDelta($sql);
            }

            if ($wpdb->get_var("show tables like '$result_system_student_result'") != $result_system_student_result) {
                $sql1 = "CREATE TABLE " . $result_system_student_result . " (
                        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                        `student_id` mediumint(9) NOT NULL,
                        `term` varchar(50) NOT NULL,
                        `student_meta` varchar(50) NOT NULL,
                        UNIQUE KEY id (id)
                        );";
                dbDelta($sql1);
            }
        }
    }
}