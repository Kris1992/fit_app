Dropzone.autoDiscover = false;

$(document).ready(function() {
    $('#importActivity').on('shown.bs.modal',(event) => {
        initializeDropzone();
    });
});

function initializeDropzone() {
    var formElement = document.querySelector('.js-dropzone');
    if (!formElement) {
        return;
    }

    var dropzone = new Dropzone(formElement, {
        paramName: 'activityCSVFile',
        maxFiles: 1, 
        maxFilesize: 2,
        dictDefaultMessage: 
        `
            <strong>Drop files here to upload</strong>
            <p>Input here .csv file with activities data</p>
            <p class="text-danger">Server does not keep this file so if you wanna reuse it later keep it on disc</p>
        `,
        init: function() {
            this.on('success', (data) => {
                const result = JSON.parse(data.xhr.response);
                if (result) {
                    showReport(result);
                }
            });
        }
    });
}

function showReport(result)
{
    const $tableWrapper = $('#table-result-js');
    const tplText = $('#result-template-js').html();
    const tpl = _.template(tplText);
    const html = tpl(result);
    $tableWrapper.append($.parseHTML(html));
}