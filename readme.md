### 介绍
这段时间在写一个考试系统[ChemLab](https://github.com/netcan/HFUT_ChemLab)，期间用到了`AJAX`实现增删查改（`CRUD`）页面，现在写一个`Laravel`入门教程吧，一步步实现最基本的`CRUD`页面。

这个教程需要安装`homestead`环境，关于`homestead`可以参考我的另一篇博文：[windows下安装Homestead开发环境](http://www.netcan666.com/2016/06/24/windows%E4%B8%8B%E5%AE%89%E8%A3%85Homestead%E5%BC%80%E5%8F%91%E7%8E%AF%E5%A2%83/)

### 创建项目
```bash
vagrant@homestead:~/Code$ laravel new AJAX_CRUD
vagrant@homestead:~/Code$ cd AJAX_CRUD/
```

<!-- more -->
### 修改配置
#### 修改数据库
打开`.env`，主要是修改数据库方面的，我们选择`task`数据库，如下：
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task
DB_USERNAME=homestead
DB_PASSWORD=secret
```
然后在数据库中创建`task`数据库。

#### 修改网址映射
注意这部分是在**主机**下进行的，不是在虚拟机`homestead`中进行的，后面我提到**主机**应该注意。
修改`homestead`的配置文件：`~/.homestead/Homestead.yaml`，增加如下内容：
```bash
sites:
	- map: crud.app
	  to: /home/vagrant/Code/AJAX_CRUD/public
```
修改`C:\Windows\System32\drivers\etc\hosts`，增加一条记录：
```bash
192.168.10.10  crud.app
```

重启`homestead`虚拟机：
```bash
homestead reload --provision
```

### 访问项目
浏览器访问`crud.app`，应该能见到如下页面：
![http://7xibui.com1.z0.glb.clouddn.com/QQ%E6%88%AA%E5%9B%BE20160817111329.png](http://7xibui.com1.z0.glb.clouddn.com/QQ%E6%88%AA%E5%9B%BE20160817111329.png)

### 引入Bootstrap、JQuery、Toastr
刚创建的项目什么都没有，在`resources/welcome.blade.php`中加入`bootstrap`、`JQuery`、`Toastr`：
```html
<title>Task Manager</title>
        
<link href="http://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
<link href="http://cdn.bootcss.com/toastr.js/2.1.3/toastr.min.css" rel="stylesheet">
<script src="http://cdn.bootcss.com/jquery/3.1.0/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="http://cdn.bootcss.com/toastr.js/2.1.3/toastr.min.js"></script>
```

将如下内容删除：
```css
<style>
    html, body {
        height: 100%;
    }

    body {
        margin: 0;
        padding: 0;
        width: 100%;
        display: table;
        font-weight: 100;
        font-family: 'Lato', sans-serif;
    }

    .container {
        text-align: center;
        display: table-cell;
        vertical-align: middle;
    }

    .content {
        text-align: center;
        display: inline-block;
    }

    .title {
        font-size: 96px;
    }
</style>
```
```html
...
<div class="content">
    <div class="title">Laravel 5</div>
</div>
```

### 创建Eloquent模型、数据表
```bash
vagrant@homestead:~/Code/AJAX_CRUD$ php artisan make:model Task -m
Model created successfully.
Created Migration: 2016_08_17_033029_create_tasks_table
```

编辑模型：`app/Task.php`，如下：
```php
class Task extends Model
{
    protected $fillable = ['name', 'content'];
}
```



编辑`database/migrations/2016_08_17_033029_create_tasks_table.php`，如下
```php
...
public function up()
{
    Schema::create('tasks', function (Blueprint $table) {
        $table->increments('id');
        $table->text('name');
        $table->text('content');
        $table->timestamps();
    });

    App\Task::create([
        'name' => '任务1',
        'content' => '完成crud'
    ]);
    App\Task::create([
        'name' => '任务1',
        'content' => '完成教程'
    ]);
}
...
```

创建数据表：
```bash
vagrant@homestead:~/Code/AJAX_CRUD$ php artisan migrate
Migration table created successfully.
Migrated: 2014_10_12_000000_create_users_table
Migrated: 2014_10_12_100000_create_password_resets_table
Migrated: 2016_08_17_033029_create_tasks_table
```

到数据库中查看，你会发现`task`数据库中多了一些表和记录。

### 构建页面
#### 创建控制器
```bash
vagrant@homestead:~/Code/AJAX_CRUD$ php artisan make:controller TaskController
Controller created successfully.
```

#### 修改路由
编辑`app/Http/routes.php`，
```php
Route::get('/', function () {
    return redirect('/task');
});

Route::resource('/task', 'TaskController');
```


这样所有动作都由我们刚刚创建的控制器来执行了。
#### 修改控制器
编辑`app/Http/Controllers/TaskController.php`，在类中增加如下方法：
```php
use App\Task;
...
public function index() {
    return view('welcome', [
        'tasks' => Task::all()
    ]);
}
```

#### 编辑页面
编辑`resources/welcome.blade.php`，如下
```html
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
```
在`<head></head>`中插入：
```html
<script src="{{ asset('js/app.js') }}"></script>
```

创建`public/js/app.js`

好了目前页面如图：
![http://7xibui.com1.z0.glb.clouddn.com//2016/08/QQ%E6%88%AA%E5%9B%BE20160817151321.png](http://7xibui.com1.z0.glb.clouddn.com//2016/08/QQ%E6%88%AA%E5%9B%BE20160817151321.png)

### 增删查改CRUD
下面我们来实现核心功能：增删查改。

在实现之前，我们记得添加`CSRF_TOKEN`，不然`ajax`的时候会出现`500`错误。

编辑`resources/welcome.blade.php`，在`<form></form>`中添加如下代码：
```html
...
    </div>
    {!! csrf_field() !!}
</form>
...
```

#### 增加
在`Task`控制器中添加函数：
```php
public function store(Request $request) {
    $this->validate($request, [
        'name' => 'required',
        'content' => 'required'
    ]);
    $task = new Task();
    $task->name = $request->get('name');
    $task->content = $request->get('content');
    $task->save();
    return response()->json($task);
}
```

#### 删除
在`Task`控制器中增加函数：
```php
public function destroy($id) {
    $task = Task::find($id);
    $task->delete();
    return response()->json(['success']);
}
```

#### 查、改
在`Task`控制器中增加函数：
```php
public function show($id) {
    $task = Task::find($id);
    return response()->json($task);
}
public function update(Request $request, $id) {
    $task = Task::find($id);
    $task->name = $request->get('name');
    $task->content = $request->get('content');
    $task->save();
    return response()->json($task);
}
```

### app.js
最后的`public/js/app.js`如下：
```javascript
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
```


### 问题记录
写这个教程遇到的问题也蛮大的，记录下来。
#### 路由问题
之前路由是这样的：
```php
Route::resource('/', 'TaskController');
```

但是会出现`404`问题，于是只好重定向到`/task`中，问题解决。
```php
Route::get('/', function () {
    return redirect('/task');
});

Route::resource('/task', 'TaskController');
```
#### 新元素事件绑定问题
之前是这样绑定事件的：
```javascript
$('.delete').click(function () {
	...
}
```

但是会出现增加记录，无法点击触发事件的问题，将事件绑定到`body`解决：
```javascript
$('body').on('click', 'button.delete', function() {
	...
}
```

参考链接：[Event binding on dynamically created elements?](http://stackoverflow.com/questions/203198/event-binding-on-dynamically-created-elements)