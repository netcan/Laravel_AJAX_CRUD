$(document).ready(function () {
    url = '/task/';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('#task input[name="_token"]').val()
        }
    });

    $('#add').click(function () {
        $('#task-title').text('添加任务');
        $('#tsave').val('add');
        $('#taskModal').modal('show');
    });

    $('body').on('click', 'button.delete', function() {
        var tid = $(this).val();
        $.ajax({
            type: 'DELETE',
            url: url+tid,
            success: function (data) {
                console.log(data);
                $('#task'+tid).remove();
                toastr.success('删除成功！');
            },
            error: function (data, json, errorThrown) {
                console.log(data);
                var errors = data.responseJSON;
                var errorsHtml= '';
                $.each( errors, function( key, value ) {
                    errorsHtml += '<li>' + value[0] + '</li>';
                });
                toastr.error( errorsHtml , "Error " + data.status +': '+ errorThrown);
            }
        });
    });

    $('body').on('click', 'button.edit', function() {
        $('#task-title').text('编辑任务');
        $('#tsave').val('update');
        var tid = $(this).val();
        $('#tid').val(tid);
        $.get(url+tid, function (data) {
            console.log(url+tid);
            console.log(data);
            $('#tname').val(data.name);
            $('#tcontent').val(data.content);
        });

        $('#taskModal').modal('show');
    });

    $('#tsave').click(function () {
        if($('#tsave').val() == 'add') {
            turl = url;
            var type = "POST"; // add
        }
        else {
            turl = url + $('#tid').val();
            var type = "PUT"; // edit
        }
        var data = {
            name: $('#tname').val(),
            content: $('#tcontent').val()
        };

        $.ajax({
            type: type,
            url: turl,
            data: data,
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $('#taskModal').modal('hide');
                $('#task').trigger('reset');
                var task = '<tr id="task' + data.id + '">' +
                    '<td>'+ data.id +'</td>' +
                    '<td>'+ data.name +'</td>' +
                    '<td>'+ data.content +'</td>' +
                    '<td>'+ data.created_at +'</td>' +
                    '<td><button class="btn btn-info edit" value="'+ data.id +'">编辑</button> <button class="btn btn-warning delete" value="'+ data.id +'">删除</button>'+ '</td>' +
                    '<tr>';
                console.log(task);
                if(type == 'POST') {
                    $('#task-list').append(task);
                    toastr.success('添加成功！');
                }
                else { // edit
                    $('#task'+data.id).replaceWith(task);
                    toastr.success('编辑成功！');
                }
            },
            error: function (data, json, errorThrown) {
                console.log(data);
                var errors = data.responseJSON;
                var errorsHtml= '';
                $.each( errors, function( key, value ) {
                    errorsHtml += '<li>' + value[0] + '</li>';
                });
                toastr.error( errorsHtml , "Error " + data.status +': '+ errorThrown);
            }
        });

    });

});