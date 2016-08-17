<!DOCTYPE html>
<html>
    <head>
        <title>Task Manager</title>

        <link href="http://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
        <link href="http://cdn.bootcss.com/toastr.js/2.1.3/toastr.min.css" rel="stylesheet">
        <script src="http://cdn.bootcss.com/jquery/3.1.0/jquery.min.js"></script>
        <script src="http://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="http://cdn.bootcss.com/toastr.js/2.1.3/toastr.min.js"></script>
        <script src="{{ asset('js/app.js') }}"></script>
    </head>
    <body>
        <div class="container">
            {{--task list--}}
            <div class="panel panel-default">
                <div class="panel-heading">
                    任务管理器
                </div>
                <div class="panel-body">
                    <button class="btn btn-primary" id="add">添加任务</button>
                </div>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>Name</th>
                        <th>Content</th>
                        <th>Created_at</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="task-list">
                    @foreach($tasks as $task)
                        <tr id="task{{ $task->id }}">
                            <td>{{ $task->id }}</td>
                            <td>{{ $task->name }}</td>
                            <td>{{ $task->content }}</td>
                            <td>{{ $task->created_at }}</td>
                            <td>
                                <button  class="btn btn-info edit" value="{{ $task->id }}">编辑</button>
                                <button class="btn btn-warning delete" value="{{ $task->id }}">删除</button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{--Modal--}}
            <div class="modal fade" id="taskModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span >&times;</span></button>
                            <h4 class="modal-title" id="task-title">编辑任务</h4>
                        </div>
                        <div class="modal-body">
                            <form id="task">
                                <div class="form-group">
                                    <label for="tname" class="control-label">Name:</label>
                                    <input id="tname" class="form-control" type="text">
                                </div>
                                <div class="form-group">
                                    <label for="tcontent" class="control-label">Content:</label>
                                    <textarea class="form-control" id="tcontent"></textarea>
                                </div>
                                {!! csrf_field() !!}
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="tsave" class="btn btn-primary" value="update">提交</button>
                            <input type="hidden" id="tid" name="tid" value="-1">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </body>
</html>
