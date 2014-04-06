$(document).ready(function() {
    $('#report-form').submit(function() {
        if (!checkFormInputs($('#report-form'))) {
            alert('Please select at least one field to display.');
            return false;
        }
    
        return true;
    });
});

function checkFormInputs(form) {
    return form.find('input[name="fields[]"]:checked').length > 0;
}

function getFormData() {
    var query = [];
    var formData = $('#report-form input:not([type="checkbox"])').serializeArray();
    for (var i in formData) {
        if (formData[i].value.length) {
            query.push(formData[i].name + '=' + formData[i].value);
        }
    } 

    var fields = [];
    $('#report-form').find('input[name="fields[]"]:checked').each(function() {
        fields.push(this.value);
    });

    if (fields.length) {
        query.push('fields=' + fields.join(','));
    }

    var has = [];
    $('#report-form').find('input[name="has[]"]:checked').each(function() {
        has.push(this.value);
    });

    if (has.length) {
        query.push('matching=' + has.join(','));
    }

    $('#report-form').find('select').each(function() {
        query.push(this.name + '=' + this.value);
    });

    var queryString = query.join('&');

    $('#query-string').text(queryString);
    $('#query-string-modal').modal();
}

function clearCheckboxes(group) {
    $('#fields-table td.' + group + '-check input:checkbox').each(function() {
        $(this).attr('checked', false);
    });
}
