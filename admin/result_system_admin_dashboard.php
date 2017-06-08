<?php
if ($_POST['result_system_hidden'] == 'Y') {
    global $wpdb;
    $student_meta = $wpdb->prefix . 'student_meta';
    $student = $wpdb->prefix . 'student';
    $year = $_POST['year'];
    $grade = $_POST['grade'];
    $term = $_POST['terminal'];
    $results = $wpdb->get_results('SELECT ' . $student . '.*, ' . $student_meta . '.marks_obtined, ' . $student_meta . '.percentage, ' . $student_meta . '.rank FROM ' . $student . ' JOIN ' . $student_meta . ' ON ' . $student . '.id = ' . $student_meta . '.student_id WHERE ' . $student . '.year = '.$year.' and ' . $student . '.grade = "'.$grade.'" and ' . $student . '.term = "'.$term.'" ', OBJECT);
    $i = 0;
    $rowcount = $wpdb->get_var('SELECT ' . $student . '.*, ' . $student_meta . '.marks_obtined, ' . $student_meta . '.percentage, ' . $student_meta . '.rank FROM ' . $student . ' JOIN ' . $student_meta . ' ON ' . $student . '.id = ' . $student_meta . '.student_id WHERE ' . $student . '.year = '.$year.' and ' . $student . '.grade = "'.$grade.'" and ' . $student . '.term = "'.$term.'" ');
    if ($rowcount>0){
        foreach ($results as $key) {
            $year = $key->year;
            $grade = $key->grade;
            $terminal = $key->term;
            if ($i == 0) {
                break;
            }
        }
        ?>
        <h2><?php echo $terminal ?> Examination Result for the Year of <?php echo $year; ?> , <?php echo $grade ?></h2>
        <table>
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
    }else{
        print_r("<h4>Sorry! No Result Found.</h4>");
    }

} else {
    ?>
    <h2>Search For Exam Result</h2>
    <div class="wrap">
        <?php echo "<h2>" . __('Student Result Options', 'result_system') . "</h2>"; ?>
        <form name="oscimp_form" method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
            <input name="result_system_hidden" value="Y" type="hidden">
            <h4>Search Student Result</h4>
            <div class="form-group">
                Year: <input class="form-control" name="year" type="text">
            </div>
            <div class="form-group">Grade:
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
            <div class="form-group">Terminal:             <select name="terminal" id="terminal" class="form-control">
                    <option value="Ist Terminal" selected="">Ist Terminal</option>
                    <option value="2nd Terminal">2nd Terminal</option>
                    <option value="3rd Terminal">3rd Terminal</option>
                    <option value="Final">Final</option>
                </select>
            </div>
            <input name="action" value="result_display" type="hidden">
            <div class="form-group">
                <input name="submit" class="form-control" value="Search" type="submit">
            </div>
        </form>
    </div>
    <?php
}