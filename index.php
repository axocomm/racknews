<?php include 'init.php'; ?>
<?php
use \RackNews\Report as Report;
use \RackNews\ObjectUtils as ObjectUtils;
?>
<?php include 'resources/template-parts/header.php'; ?>
            <form role="form" id="report-form" action="report.php" method="post">
                <fieldset>
                    <legend>Report Generation</legend>
                    <div class="form-group">
                        <label for="fields">Fields</label>
                        <?php
                        $report = new Report(ObjectUtils::get_objects());
                        $params = array(
                            'report' => 'fields'
                        );

                        $report->set_params($params);
                        try {
                            $report->build();
                            $fields = $report->get_report_objects();
                            sort($fields);
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
                                    <td class="fields-check"><button type="button" class="btn btn-danger" onclick="return clearCheckboxes('fields');">Clear</button></td>
                                    <td class="has-check"><button type="button" class="btn btn-danger" onclick="return clearCheckboxes('has');">Clear</button></td>
                                </tr>
                            </tbody>
                        </table>
                        <?php endif; ?> 
                    </div>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="report">Report</label>
                                <input type="text" class="form-control" name="report" placeholder="report">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="names">Names</label>
                                <input type="text" class="form-control" name="names" placeholder="Names">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="id">ID</label>
                                <input type="text" class="form-control" name="id" placeholder="ID">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="and">Match All</label>
                                <input type="text" class="form-control" name="and" placeholder="Match All">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="or">Match Any</label>
                                <input type="text" class="form-control" name="or" placeholder="Match Any">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="log">Log Matches</label>
                                <input type="text" class="form-control" name="log" placeholder="Log Matches">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="comment">Comment Matches</label>
                                <input type="text" class="form-control" name="comment" placeholder="Comment Matches">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="types">Types</label>
                                <input type="text" class="form-control" name="types" placeholder="Types">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="format">Format</label>
                                <select class="form-control" name="format">
                                    <option value="html">HTML</option>
                                    <option value="json">JSON</option>
                                    <option value="csv">CSV</option>
                                    <option value="xml">XML</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button id="submit" type="submit" class="btn btn-primary">Report</button>
                    </div>
                </fieldset>
            </form>
            <button onclick="getFormData();" class="btn">Query String</button>
            <div class="modal fade" id="query-string-modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Query String</h4>
                        </div>
                        <div class="modal-body">
                            <p>Generated query string:</p>
                            <code id="query-string"></code>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <script src="resources/js/create-form.js"></script>
<?php include 'resources/template-parts/footer.php'; ?>
