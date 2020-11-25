var containerElement = $('.container');
var folderUrl = $('#folder-edit').data('url');

function refreshFolder(url) {
    $.ajax({
        url: url,
        type: 'GET',
        success: function (folder) {
            if(folder.status === 'creating') {
                $('#folder-status').text('En cours de création');
                $('#user-email').attr('disabled', false);
                $('#owner-email').attr('disabled', false);
            } else if(folder.status === 'in_progress') {
                $('#folder-status').text("En cours d'élaboration");
                $('#user-email').attr('disabled', true);
                $('#owner-email').attr('disabled', true);
            }

            $('#folder-name').val(folder.name);
            $('#folder-updated-at').text(folder.updatedAt);
            if ($('#user-email').val() === '...') {
                $('#user-email').val(folder.userEmail);
            }
            if ($('#owner-email').val() === '...') {
                $('#owner-email').val(folder.ownerEmail);
            }

            if (folder.canRename) {
                $('#folder-name').attr('disabled', false);
            } else {
                $('#folder-name').attr('disabled', true);
            }

            $('#folder-name').data('folder-id', folder.id);

            if (folder.canCreateDocument) {
                $('#create-document-button').show();
            } else {
                $('#create-document-button').hide();
            }

            if (folder.canRemove) {
                $('#remove-folder-button').show();
            } else {
                $('#remove-folder-button').hide();
            }

            if (folder.canSendFolderToUser) {
                $('#send-folder-button').show();
            } else {
                $('#send-folder-button').hide();
            }

            $('#folder-collection').empty();
            folder.documents.forEach(function (document) {
                var hasContent = document.status === 'uploaded';

                var addContentButtonColor = 'red';
                var removeDocumentButtonAttribute = 'disabled';
                if (hasContent) {
                    removeDocumentButtonAttribute = '';
                    addContentButtonColor = 'green';
                }

                var folderCollectionContent = '<li class="collection-item">'
                    +'<div class="row mb-3">'
                    + '    <div class="input-field col s4">'
                    + '        <input id="document-'+document.id+'-input" data-document-id="'+document.id+'"  value="'+document.name+'" type="text" class="validate document-name-input valid">'
                    + '        <label class="active" for="document-'+document.id+'-input">Nom du document</label>'

                if (document.uploader) {
                    folderCollectionContent += '<span class="helper-text">Mis en ligne par: ' + document.uploader + '</span>';
                    folderCollectionContent += '<span class="helper-text">' + document.updatedAt + '</span>';
                }
                folderCollectionContent += '    </div>';

                folderCollectionContent += '    <div class="input-field col s8">'
                    + '    <form action="" method="post" enctype="multipart/form-data">';

                if (document.canRemove) {
                    folderCollectionContent += '<a class="remove-document-from-folder-button red btn btn-small waves-effect waves-light" data-document-id="'+document.id+'">Supprimer<i class="material-icons right">delete</i></a>';
                }

                if (document.canUpload) {
                    folderCollectionContent += '<input type="file" style="display:none;" name="fichier" class="uploadFile" id="uploadFile-'+document.id+'" data-folder-id="'+folder.id+'" data-document-id="'+document.id+'"/>';
                    folderCollectionContent += '<button class="submit-file-button '+addContentButtonColor+' btn btn-small waves-effect waves-light btn-small" data-target-id="uploadFile-'+document.id+'" type="button">Joindre<i class="material-icons right">send</i></button>';
                }

                if (document.canDownload) {
                    folderCollectionContent += '<button class="download-file-button btn btn-small waves-effect waves-light btn-small" data-document-id="' + document.id + '" type="button">Télécharger<i class="material-icons right">download</i></button>';
                }

                if (document.canClear) {
                    folderCollectionContent += '<a class="remove-document-button ' + removeDocumentButtonAttribute + ' btn btn-small waves-effect waves-light" data-folder-id="' + folder.id + '" data-document-id="' + document.id + '">Supprimer PJ<i class="material-icons right">delete</i></a>';
                }

                folderCollectionContent += '</form>'
                    + '    </div>'
                    + '</div>'
                    + '</li>'
                ;

                $('#folder-collection').append(folderCollectionContent);


                if (document.canRename) {
                    $('#document-'+document.id+'-input').attr('disabled', false);
                } else {
                    $('#document-'+document.id+'-input').attr('disabled', true);
                }
            });
        }
    });
}
containerElement.on('change', '.folder-name-input', function() {
    var that = $(this);
    that.attr('disabled', true);
    $.ajax({
        // Your server script to process the upload
        url: that.data('url'),
        data: {'folder-name': that.val(), 'folder-id': that.data('folder-id')},
        type: 'POST',
        success: function (data) {
            that.attr('disabled', false);
            that.removeClass('invalid');
            refreshFolder(folderUrl);
        }
    });
});
refreshFolder(folderUrl);
containerElement.on('change', '.document-name-input', function() {
    $(this).attr('disabled', true);
    $.ajax({
        // Your server script to process the upload
        url: $('#folder-collection').data('rename-document-url'),
        data: {'document-name': $(this).val(), 'document-id': $(this).data('document-id')},
        type: 'POST',
        success: function (data) {
            $(this).attr('disabled', false);
            refreshFolder(folderUrl);
        }
    });
});

$('#remove-folder-button').on('click', function() {
    if (confirm("Etes vous sure ?")) {
        return true;
    } else {
        return false;
    }
});

$('#create-document-button').on('click', function() {
    var that = $(this);

    $.ajax({
        // Your server script to process the upload
        url: that.data('url'),
        data: {'folder-id': that.data('folder-id')},
        type: 'POST',
        success: function (data) {
            refreshFolder(folderUrl);
        }
    });
});

$('#send-folder-button').on('click', function() {
    var that = $(this);

    $.ajax({
        // Your server script to process the upload
        url: that.data('url'),
        data: {'folder-id': that.data('folder-id'), 'user-email': $('#user-email').val(), 'owner-email': $('#owner-email').val() },
        type: 'POST',
        success: function (data) {
            location.reload();
        }
    });
});

containerElement.on('click', '.remove-document-from-folder-button', function() {
    var that = $(this);
    $.ajax({
        url: $('#folder-collection').data('remove-document-url'),
        data: {'document-id': that.data('document-id') },
        type: 'POST',
        success: function (data) {
            refreshFolder(folderUrl);
        }
    });
});

containerElement.on('click', '.remove-document-button', function() {
    var that = $(this);
    $.ajax({
        url: $('#folder-collection').data('clear-document-url')+"?folder-id=" + that.data('folder-id') + "&document-id=" + that.data('document-id'),
        type: 'POST',
        success: function () {
            $(that.parents('form')[0]).find('.submit-file-button').removeClass('green');
            $(that.parents('form')[0]).find('.submit-file-button').addClass('red');
            that.addClass('disabled');
            refreshFolder(folderUrl);
        }
    });
});

containerElement.on('click', '.submit-file-button', function() {
    $("#"+$(this).data('target-id')).click();
});

containerElement.on('click', '.download-file-button', function() {
    var that = $(this);
    window.location=$('#folder-collection').data('download-document-url')+"?document-id=" + that.data('document-id');
});

containerElement.on('change', '.uploadFile', function() {
    var that = $(this);
    $.ajax({
        // Your server script to process the upload
        url: $('#folder-collection').data('upload-document-url')+"?folder-id="+that.data('folder-id')+"&document-id="+that.data('document-id'),
        type: 'POST',

        // Form data
        data: new FormData(that.parents('form:first')[0]),

        // Tell jQuery not to process data or worry about content-type
        // You *must* include these options!
        cache: false,
        contentType: false,
        processData: false,

        // Custom XMLHttpRequest
        xhr: function () {
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                // For handling the progress of the upload
                myXhr.upload.addEventListener('progress', function (e) {
                    if (e.lengthComputable) {
                        $('progress').attr({
                            value: e.loaded,
                            max: e.total,
                        });
                    }
                }, false);
            }
            return myXhr;
        },
        success: function () {
            $(that.parents('form')[0]).find('.submit-file-button').removeClass('red');
            $(that.parents('form')[0]).find('.submit-file-button').addClass('green');
            $(that.parents('form')[0]).find('.remove-document-button').removeClass('disabled');
            refreshFolder(folderUrl);
        },
        error: function(xhr, status, error) {
            var errorMessage = xhr.status + ': ' + xhr.statusText
            alert('Error - ' + errorMessage);
        }
    });
});