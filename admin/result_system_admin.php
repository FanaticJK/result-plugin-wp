<?php
$message = "";
if ($_POST['result_system_hidden'] == 'Y') {
    //Form data insert into database
    global $wpdb;
    $i = 0;
    foreach ($_POST['studentName'] as $key) {
        if ("" != $_POST['studentName'][$i]) {
            $wpdb->insert(
                $wpdb->prefix . 'student',
                array(
                    'name' => $_POST['studentName'][$i],
                    'roll_no' => $_POST['roll'][$i],
                    'grade' => $_POST['grade'],
                    'section' => $_POST['section'],
                    'term' => $_POST['terminal'],
                    'year' => $_POST['year'],
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                )
            );
            $lastid = $wpdb->insert_id;
            $wpdb->insert(
                $wpdb->prefix . 'student_meta',
                array(
                    'student_id' => $lastid,
                    'term' => $_POST['terminal'],
                    'year' => $_POST['year'],
                    'marks_obtined' => $_POST['marks_obtined'][$i],
                    'percentage' => $_POST['percentage'][$i],
                    'rank' => $_POST['rank'][$i],
                ),
                array(
                    '%d',
                    '%s',
                    '%s',
                    '%d',
                    '%d',
                    '%d'
                )
            );
        }
        $i++;
    }
    $message = "Data Inserted Successfully.";
}

if ($_GET['action'] == 'create') {
    ?>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"
            integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
            crossorigin="anonymous"></script>
    <div class="wrap">
        <?php echo "<h2>" . __('Student Result Options', 'result_system') . "</h2>"; ?>

        <form name="oscimp_form" method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
            <input type="hidden" name="result_system_hidden" value="Y">
            <?php echo "<h4>" . __('Insert Student Result', 'result_system') . "</h4>"; ?>
            <?php _e($message); ?>
            <p><?php _e("Grade "); ?>
                <select name="grade" id="grade">
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
                <?php _e("Section: "); ?><input type="text" name="section" required="required">
                <?php _e("Year: "); ?><input type="text" name="year" required="required">
                <?php _e("Terminal "); ?>
                <select name="terminal" id="terminal">
                    <option value="Ist Terminal" selected="">Ist Terminal</option>
                    <option value="2nd Terminal">2nd Terminal</option>
                    <option value="3rd Terminal">3rd Terminal</option>
                    <option value="Final">Final</option>
                </select>
            </p>
            <a href="javascript:void(0)" id="addRowBtn">Add Row</a>
            <table>
                <tr>
                    <th><?php _e("Student Name "); ?></th>
                    <th><?php _e("Roll No "); ?></th>
                    <th><?php _e("Total Marks Obtained "); ?></th>
                    <th><?php _e("Percentage "); ?></th>
                    <th><?php _e("Rank "); ?></th>
                    <th><?php _e("Action "); ?></th>
                </tr>
                <tbody id="tableBody">

                </tbody>
            </table>

            <p class="submit">
                <input type="submit" name="submit" value="<?php _e('Insert Result', 'result_system') ?>"/>
            </p>
        </form>
    </div>
    <script type="text/javascript">
        var tbl = jQuery("#tableBody");

        jQuery(window).load(function () {
            jQuery('<tr><td><input type="text" name="studentName[]" ></td>' +
                '<td><input type="text" name="roll[]" ></td>' +
                '<td><input type="text" name="marks_obtined[]"></td>' +
                '<td><input type="text" name="percentage[]"></td>' +
                '<td><input type="text" name="rank[]"></td>' +
                '<td></td></tr>').appendTo(tbl);
        });

        jQuery("#addRowBtn").click(function () {
            jQuery('<tr><td><input type="text" name="studentName[]" ></td>' +
                '<td><input type="text" name="roll[]" ></td>' +
                '<td><input type="text" name="marks_obtined[]"></td>' +
                '<td><input type="text" name="percentage[]"></td>' +
                '<td><input type="text" name="rank[]"></td>' +
                '<td><a href="javascript:void(0)" onclick="removeElement(this)">Remove</a></td></tr>').appendTo(tbl);
        });
        function removeElement(rem) {
            jQuery(rem).closest('tr').remove();
        }
    </script>
    <?php
} else {
    ?>
    <h2>Student Result System</h2>
    <a href="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>&action=create">Create Result</a>
    <?php
}