<?php include 'php/view/header.php'; ?>
            <form action="report.php" method="get">
                <input type="hidden" name="fields" value="name,id,FQDN,objtype_id,etags">
                <input type="hidden" name="types" value="server">
                <input type="hidden" name="format" value="json">
                <input type="submit" value="Test">
            </form>
<?php include 'php/view/footer.php'; ?>
