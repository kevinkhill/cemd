<html>
  <head>
    <title>CEMD | Confide & Entrust Management Dashboard</title>
    {{ HTML::style('css/cemd/bootstrap-3.2.0.min.css') }}
    {{ HTML::style('css/cemd/bootstrap-checkbox.css') }}
    {{ HTML::style('css/cemd/dashboard.css') }}
  </head>

  <body>
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-1 hidden-xs">
          <h1>CEMD</h1>
        </div>
        <div class="col-sm-offset-1 col-sm-8" style="text-align: center;">
          <h2>Confide & Entrust Management Dashboard</h2>
          <h3>For Users, Roles, & Permissions</h3>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-3">
          <ul class="nav nav-pills nav-stacked" role="tablist">
            <li class="active">
              <a href="#users-tab" data-toggle="pill">
                <span data-bind="text: users().length" class="badge pull-right"></span>Users
              </a>
            </li>
            <li>
              <a href="#roles-tab" data-toggle="pill">
                <span data-bind="text: roles().length" class="badge pull-right"></span>Roles
              </a>
            </li>
            <li>
              <a href="#permissions-tab" data-toggle="pill">
                <span data-bind="text: permissions().length" class="badge pull-right"></span>Permissions
              </a>
            </li>
            <li><a href="#settings-tab" data-toggle="pill">Settings</a></li>
            <li data-bind="visible: settings.debug()">
              <a href="#debug-tab" data-toggle="pill">Debug</a>
            </li>
          </ul>
        </div>

        <div class="col-sm-9">
          <div class="tab-content">


            <div class="tab-pane in active" id="users-tab">
              <h2>Users</h2>
              <table id="users" class="table">
                <thead>
                  <tr>
                    <th>Name</th>
                  </tr>
                </thead>
                <tbody data-bind="foreach: users">
                  <tr>
                    <td>
                      <div class="input-group">
                        <input type="text" data-bind="value: email(), enable: editing()" class="form-control">
                        <span class="input-group-btn">
                          <button class="btn btn-success attachBtn" type="button" data-bind="visible: $root.roles().length > 0">
                            <span class="glyphicon glyphicon-paperclip"></span>&nbsp;&nbsp;Roles
                          </button>
                          <button type="button" class="btn btn-primary editBtn">
                            <span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;Edit
                          </button>
                          <button type="button" class="btn btn-danger deleteBtn">
                            <span class="glyphicon glyphicon-trash"></span>&nbsp;&nbsp;Delete
                          </button>
                        </span>
                      </div>
                      <div class="collapse" data-bind="attr: { id: 'userRoles_'+id }">
                        <br />
                        <div class="row">
                          <div class="col-lg-8 col-sm-10 col-sm-12">
                            <div class="panel panel-success">
                              <div class="panel-heading">
                                <h3 data-bind="text: (username()+' - '+email() + '\'s Roles')" class="panel-title"></h3>
                              </div>
                              <div class="panel-body">
                                <ul data-bind="foreach: $root.roles()">
                                  <li>
                                    <input type="checkbox" data-bind="attr: { value: id }, checked: $parent.roles()">
                                    <label data-bind="text: name()"></label>
                                  </li>
                                </ul>
                              </div>
                              <div class="panel-footer">
                                <button data-bind="click: $root.updateUserRoles" type="button" class="btn btn-success rolesSaveBtn">
                                  <span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>


            <div class="tab-pane" id="roles-tab">
              <h2>Roles</h2>
              <table id="roles" class="table">
                <thead>
                  <tr>
                    <th>Name</th>
                  </tr>
                </thead>
                <tbody data-bind="foreach: roles">
                  <tr>
                    <td>
                      <div class="input-group">
                        <input type="text" data-bind="value: name, enable: editing" class="form-control">
                        <span class="input-group-btn">
                          <button class="btn btn-success attachBtn" type="button" data-bind="visible: $root.permissions().length > 0">
                            <span class="glyphicon glyphicon-paperclip"></span>&nbsp;&nbsp;Permissions
                          </button>
                          <button type="button" class="btn btn-primary editBtn">
                            <span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;Edit
                          </button>
                          <button type="button" class="btn btn-danger deleteBtn">
                            <span class="glyphicon glyphicon-trash"></span>&nbsp;&nbsp;Delete
                          </button>
                        </span>
                      </div>
                      <div class="collapse" data-bind="attr: { id: 'rolePermissions_'+id }">
                        <br />
                        <div class="row">
                          <div class="col-lg-8 col-sm-10 col-sm-12">
                            <div class="panel panel-success">
                              <div class="panel-heading">
                                <h3 data-bind="text: (name() + ' Permissions')" class="panel-title"></h3>
                              </div>
                              <div class="panel-body">
                                <ul data-bind="foreach: $root.permissions()"><!-- class="permission-bindings"-->
                                  <li>
                                    <input type="checkbox" data-bind="attr: { value: id }, checked: $parent.permissions()">
                                    <label data-bind="text: display_name()"></label>
                                  </li>
                                </ul>
                              </div>
                              <div class="panel-footer right">
                                <button data-bind="click: $root.updateRolePermissions" type="button" class="btn btn-success permsSaveBtn">
                                  <span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>

              <form data-bind="submit: addRole">
                <div class="input-group">
                  <input type="text" data-bind="value: newRoleName, valueUpdate:'afterkeyup'" class="form-control">
                  <span class="input-group-btn">
                    <button data-bind="disable: newRoleNameCheck()" class="btn btn-success" type="submit">
                      <span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add Role
                    </button>
                  </span>
                </div>
              </form>
            </div>


            <div class="tab-pane" id="permissions-tab">
              <h2>Permissions</h2>
              <table id="permissions" class="table">
                <thead>
                  <tr>
                    <th>Name</th>
                  </tr>
                </thead>
                <tbody data-bind="foreach: permissions">
                  <tr data-bind="attr: { title: 'name: '+name() }">
                    <td>
                      <div class="input-group">
                        <input type="text" data-bind="value: display_name, enable: editing" class="form-control">
                        <span class="input-group-btn">
                          <button type="button" class="btn btn-primary editBtn">
                            <span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;Edit
                          </button>
                          <button type="button" class="btn btn-danger deleteBtn">
                            <span class="glyphicon glyphicon-trash"></span>&nbsp;&nbsp;Delete
                          </button>
                        </span>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>

              <form data-bind="submit: addPermission">
                <div class="input-group">
                  <input type="text" data-bind="value: newPermissionName, valueUpdate:'afterkeyup'" class="form-control">
                  <span class="input-group-btn">
                    <button data-bind="disable: newPermissionNameCheck()" class="btn btn-success" type="submit">
                      <span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add Permission
                    </button>
                  </span>
                </div>
              </form>
            </div>


            <div class="tab-pane" id="settings-tab">
              <h2>Settings</h2>
              <table class="table">
                <tbody>
                  <tr>
                    <td>
                      <button type="button" class="btn btn-default" data-bind="toggleBtn: settings.debug">Debugging</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>


            <div class="tab-pane" id="debug-tab">
              <h2>Debug</h2>
              <pre data-bind="text: ko.toJSON($root, null, 2)"></pre>
            </div>

          </div><!-- /tab-container -->
        </div><!-- /col-sm-9 -->
      </div><!-- /row -->
    </div> <!-- /container-fluid -->

    {{ HTML::script('js/cemd/jquery-1.11.1.min.js') }}
    {{ HTML::script('js/cemd/bootstrap-3.2.0.min.js') }}
    {{ HTML::script('js/cemd/knockout-3.1.0.min.js') }}
    {{ HTML::script('js/cemd/bootstrap-checkbox.min.js') }}
    {{ HTML::script('js/cemd/bootstrap-growl.min.js') }}
    {{ HTML::script('js/cemd/dashboard.js') }}

    <script type="text/javascript">
      CEMD.loadJson('perms' , '{{ $pJSON }}');
      CEMD.loadJson('roles' , '{{ $rJSON }}');
      CEMD.loadJson('users' , '{{ $uJSON }}');
      CEMD.init();
    </script>
  </body>
</html>
