<?php include 'resources/template-parts/header.php'; ?>
            <form id="report-form" action="report.php" method="get">
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
<?php include 'resources/template-parts/footer.php'; ?>
