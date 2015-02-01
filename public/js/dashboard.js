(function() {
  'use strict';

  window.CEMD = {
    route: 'cemd/',
    json: {
      perms : null,
      roles : null,
      users : null,
    },
    floppyIcon: {
      save   : '<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save',
      saving : '<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Saving...',
      saved  : '<span class="glyphicon glyphicon-floppy-saved"></span>&nbsp;&nbsp;Saved!',
      error  : '<span class="glyphicon glyphicon-floppy-remove"></span>&nbsp;&nbsp;Error'
    },
    makeCheckButtons         : null,
    addBindingHandler        : null,
    infoGrowl                : null,
    successGrowl             : null,
    errorGrowl               : null,
    user                     : null,
    role                     : null,
    permission               : null,
    model                    : null,
    delegateUserCollapse     : null,
    delegatePermissionDelete : null,
    delegateRoleCollapse     : null,
    delegateRoleDelete       : null,
    loadJson                 : null,
    loadResource             : null,
    init: function() {
      ko.applyBindings(new this.model);

      this.addBindingHandler();
      this.makeCheckButtons();
      this.delegatePermissionDelete('#permissions', '.deleteBtn');
      this.delegateRoleCollapse('#roles', '.attachBtn');
      this.delegateRoleDelete('#roles', '.deleteBtn');
      this.delegateUserCollapse('#users', '.attachBtn');
    }
  };



/******************************************************************************
 * CEMD
 *****************************************************************************/
  window.CEMD.makeCheckButtons = function() {
    $(':checkbox').checkbox({
      buttonStyle: 'btn-default',
      buttonStyleChecked: 'btn-info',
      checkedClass: 'glyphicon glyphicon-check',
      uncheckedClass: 'glyphicon glyphicon-unchecked'
    });
  };

  window.CEMD.addBindingHandler = function() {
    ko.bindingHandlers.toggleBtn = {
      init: function(element, valueAccessor) {
        var value = valueAccessor();

        if (ko.unwrap(value) == true) {
          $(element).toggleClass('btn-default btn-info');
        }

        $(element).click(function() {
          $(this).toggleClass('btn-default btn-info');

          if (ko.unwrap(value) == true) {
            value(false);
          } else {
            value(true);
          }
        });
      }
    };
  };

  window.CEMD.infoGrowl = function (msg) {
    $.bootstrapGrowl(msg, {type: 'info', width: 'auto'});
  };

  window.CEMD.successGrowl = function (msg) {
    $.bootstrapGrowl(msg, {type: 'success', width: 'auto'});
  };

  window.CEMD.errorGrowl = function (msg) {
    $.bootstrapGrowl(msg, {type: 'danger', width: 'auto'});
  };

  window.CEMD.user = function (id, username, email, roles) {
    this.id = id;
    this.username = ko.observable(username);
    this.email = ko.observable(email);
    this.roles = ko.observableArray(roles || []);
    this.editing = ko.observable(false);
  };

  window.CEMD.role = function (id, name, perms) {
    this.id = id;
    this.name = ko.observable(name);/*
    this.elemId = ko.computed(function() {
      return this.name().replace(' ', '_').replace(/[^a-z0-9]/gi, '').toLowerCase();
    }, this);*/
    this.permissions = ko.observableArray(perms || []);
    this.editing = ko.observable(false);
  };

  window.CEMD.permission = function (id, name) {
    this.id = id;
    this.display_name = ko.observable(name);
    this.name = ko.computed(function() {
      return this.display_name().replace(' ', '_').replace(/[^a-z0-9_]/gi, '').toLowerCase()
    }, this);
    this.editing = ko.observable(false);
  };

  window.CEMD.model = function() {
    self = this;

    self.settings = {
      debug: ko.observable(false)
    }

    /**
     * Permissions
     */
    self.permissions = ko.observableArray(
      CEMD.json.perms.map(function (perm) {
        return new CEMD.permission(perm.id, perm.display_name);
      })
    );

    self.newPermissionName = ko.observable('');
    self.newPermissionNameCheck = ko.computed(function() {
      return self.newPermissionName() == '';
    });

    self.addPermission = function() {
      $.ajax({
        url: CEMD.route + 'permission-create',
        method: 'post',
        data: {
          name: self.newPermissionName().replace(' ', '_').replace(/[^a-z0-9_]/gi, '').toLowerCase(),
          display_name: self.newPermissionName()
        },
        dataType: 'json',
        success: function (response) {
          if( response.status == 'success') {
            var data = ko.utils.parseJson(response.data);

            self.permissions.push(new CEMD.permission(data.id, data.display_name));

            CEMD.makeCheckButtons();
            //makeTooltips();

            CEMD.successGrowl(response.message);
          } else {
            CEMD.errorGrowl(response.message);
          }
        }
      });
    }


    /**
     * Roles
     */
    self.roles = ko.observableArray(
      CEMD.json.roles.map(function (role) {
        var rolePermissions = [];

        if (role.perms.length > 0) {
          rolePermissions = role.perms.map(function (perm) {
            return perm.id.toString();
          });
        }

        return new CEMD.role(
          role.id,
          role.name,
          rolePermissions
        );
      })
    );

    self.newRoleName = ko.observable('');
    self.newRoleNameCheck = ko.computed(function() {
      return (self.newRoleName() == '');
    });

    self.addRole = function() {
      $.ajax({
        url: CEMD.route + 'role-create',
        method: 'post',
        data: { name: self.newRoleName() },
        success: function (response) {
          if(response.status == 'success') {
            var data = ko.utils.parseJson(response.data);

            self.roles.push(new CEMD.role(data.id, data.name));

            CEMD.makeCheckButtons();

            CEMD.successGrowl(response.message);
          } else {
            CEMD.errorGrowl(response.message);
          }
        },
        error: function (response) {
          CEMD.errorGrowl(response.message);
        }
      });
    };

    self.updateRolePermissions = function (role) {
      var permsSaveBtn = $('#rolePermissions_'+role.id+' .permsSaveBtn');

      permsSaveBtn.html(CEMD.floppyIcon.saving);

      $.ajax({
        url: CEMD.route + 'role-update-permissions',
        method: 'post',
        data: {
          roleId: role.id,
          permissions: role.permissions()
        },
        success: function (response) {
          if(response.status == 'success') {
            CEMD.successGrowl(response.message);
            permsSaveBtn.html(CEMD.floppyIcon.saved);
          } else {
            CEMD.errorGrowl(response.message);
            permsSaveBtn.html(CEMD.floppyIcon.error);
          }
        },
        error: function (response) {
          permsSaveBtn.html(CEMD.floppyIcon.error);
          CEMD.errorGrowl('danger', response.message);
        }
      }).always(function() {
        setTimeout(function() {
          permsSaveBtn.html(CEMD.floppyIcon.save);
          $('#rolePermissions_'+role.id).collapse('hide');
        }, 1000);
      });
    };


    /**
     * Users
     */
    self.users = ko.observableArray(
      CEMD.json.users.map(function (user) {
        var userRoles = [];

        if (user.roles.length > 0) {
          userRoles = user.roles.map(function (role) {
            return role.id.toString();
          });
        }

        return new CEMD.user(
          user.id,
          user.username,
          user.email,
          userRoles
        );
      })
    );

    self.updateUserRoles = function (user) {
      var rolesSaveBtn = $('#userRoles_'+user.id+' .rolesSaveBtn');

      rolesSaveBtn.html(CEMD.floppyIcon.saving);

      $.ajax({
        url: CEMD.route + 'user-update-roles',
        method: 'post',
        data: {
          userId: user.id,
          roles: user.roles()
        },
        success: function (response) {
          if(response.status == 'success') {
            rolesSaveBtn.html(CEMD.floppyIcon.saved);
            CEMD.successGrowl(response.message);
          } else {
            rolesSaveBtn.html(CEMD.floppyIcon.error);
            CEMD.errorGrowl(response.message);
          }
        },
        error: function (response) {
          rolesSaveBtn.html(CEMD.floppyIcon.error);
          CEMD.errorGrowl('danger', response.message);
        }
      }).always(function() {
        setTimeout(function() {
          rolesSaveBtn.html(CEMD.floppyIcon.save);
          $('#userRoles_'+user.id).collapse('hide');
        }, 1000);
      });
    };

  } /* </CEMD.model> */

  window.CEMD.delegateUserCollapse = function (eleId, clickTarg) {
    $(eleId).delegate(clickTarg, 'click', function () {
      $(this).parents('td').children('.collapse').collapse('toggle');
    });
  };

  window.CEMD.delegatePermissionDelete = function (eleId, clickTarg, url) {
    $(eleId).delegate(clickTarg, 'click', function () {
      var element = this;

      $.ajax({
        url: CEMD.route + 'permission-delete',
        method: 'post',
        data: {
          permissionId: ko.dataFor(element).id
        },
        dataType: 'json',
        success: function (response) {
          if(response.status == 'success') {
            var context = ko.contextFor(element);

            context.$parent.permissions.remove(context.$data);

            CEMD.infoGrowl(response.message);
          } else {
            CEMD.errorGrowl(response.message);
          }
        }
      });
    });
  };

  window.CEMD.delegateRoleCollapse = function (eleId, clickTarg) {
    $(eleId).delegate(clickTarg, 'click', function () {
      $(this).parents('td').children('.collapse').collapse('toggle');
    });
  };

  window.CEMD.delegateRoleDelete = function (eleId, clickTarg) {
    $(eleId).delegate(clickTarg, 'click', function () {
      var element = this;

      $.ajax({
        url: CEMD.route + 'role-delete',
        method: 'post',
        data: {
          roleId: ko.dataFor(element).id
        },
        dataType: 'json',
        success: function (response) {
          if(response.status == 'success') {
            var context = ko.contextFor(element);

            context.$parent.roles.remove(context.$data);

            CEMD.infoGrowl(response.message);
          } else {
            CEMD.errorGrowl(response.message);
          }
        }
      });
    });
  };

  window.CEMD.loadJson = function (target, json) {
    this.json[target] = ko.utils.parseJson(json);
  };
})(window);
