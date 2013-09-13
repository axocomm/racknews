<?php include 'init.php'; ?>
<?php include 'resources/template-parts/header.php'; ?>
            <form id="report-form" action="report.php" method="post">
                <fieldset>
                    <legend>Report Generation</legend>
                    <div class="control-group">
                        <label class="control-label" for="fields">Fields</label>
                        <?php
                        $report = new \RackNews\Report(\RackNews\ObjectUtils::get_objects());
                        $params = array(
                            'report' => 'fields'
                        );

                        $report->set_params($params);
                        try {
                            $report->build();
                            $fields = $report->get_report_objects();
                        } catch (Exception $e) {
                            echo '<div class="well">' . $e->getMessage() . '</div>';
                        }
                        ?>
                        <?php if ($fields && count($fields)): ?>
                        <table class="table table-striped table-condensed table-bordered table-hover" id="fields-table">
                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th>Selected</th>
                                    <th>Required</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fields as $field): ?>
                                <tr>
                                    <td><?php echo $field['field']; ?></td>
                                    <td class="fields-check"><input type="checkbox" name="fields[]" value="<?php echo $field['field']; ?>"></td>
                                    <td class="has-check"><input type="checkbox" name="has[]" value="<?php echo $field['field']; ?>"></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td></td>
                                    <td class="fields-check"><button type="button" class="btn" onclick="return clearCheckboxes('fields');">Clear</button></td>
                                    <td class="has-check"><button type="button" class="btn" onclick="return clearCheckboxes('has');">Clear</button></td>
                                </tr>
                            </tbody>
                        </table>
                        <?php endif; ?> 
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="names">Names</label>
                        <div class="controls">
                            <input type="text" name="names" placeholder="Names">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="id">ID</label>
                        <div class="controls">
                            <input type="text" name="id" placeholder="ID">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="log">Log Matches</label>
                        <div class="controls">
                            <input type="text" name="log" placeholder="Log Matches">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="comment">Comment Matches</label>
                        <div class="controls">
                            <input type="text" name="comment" placeholder="Comment Matches">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="types">Types</label>
                        <div class="controls">
                            <input type="text" name="types" placeholder="Types">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="format">Format</label>
                        <div class="controls">
                            <input type="text" name="format" placeholder="Format">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <input class="btn btn-primary" type="submit" value="Test">
                        </div>
                    </div>
                </fieldset>
            </form>
            <button onclick="getFormData();" class="btn">Query String</button>
            <div class="modal hide fade" id="query-string-modal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3>Query String</h3>
                </div>
                <div class="modal-body">
                    <p>Generated query string:</p>
                    <code id="query-string"></code>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn" data-dismiss="modal">Close</a>
                </div>
            </div>
            <script>
                function getFormData() {
                    var query = [];
                    var formData = $('#report-form').serializeArray();
                    for (var i in formData) {
                        if (formData[i].value.length) {
                            query.push(formData[i].name + '=' + formData[i].value);
                        }
                    } 

                    var queryString = query.join('&');
                    $('#query-string').text(queryString);
                    $('#query-string-modal').modal();
                }

                function clearCheckboxes(group) {
                    $('#fields-table td.' + group + '-check input:checkbox').each(function() {
                        $(this).attr('checked', false);
                    });
                }
            </script>
<?php include 'resources/template-parts/footer.php'; ?>
