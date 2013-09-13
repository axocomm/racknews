<?php include 'resources/template-parts/header.php'; ?>
            <form id="report-form" action="report.php" method="post">
                <fieldset>
                    <legend>Report Generation</legend>
                    <div class="control-group">
                        <label class="control-label" for="fields">Fields</label>
                        <div class="controls">
                            <input type="text" name="fields" placeholder="name,id,FQDN,objtype_id,etags">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="types">Types</label>
                        <div class="controls">
                            <input type="text" name="types" placeholder="server">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="format">Format</label>
                        <div class="controls">
                            <input type="text" name="format" placeholder="json">
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
            </script>
<?php include 'resources/template-parts/footer.php'; ?>
