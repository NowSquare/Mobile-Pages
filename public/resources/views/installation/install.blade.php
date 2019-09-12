<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Install Mobile Pages</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body class="bg-light">
  <div id="pageLoader" class="d-none" style="position: fixed; left:0; top: 0; right: 0; bottom: 0; background-color: rgba(255,255,255,0.95); z-index: 999999;">
    <div style="margin: 2.5rem 0 0 0; position: absolute; top: 40%; text-align: center; width: 100%" class="text-muted">
      <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
      </div>
      <br><br>
      Please be patient, installation may take a few minutes.<br>
      You will be redirected when finished.
    </div>
  </div>
  <div class="container">
    <div class="pt-5 pb-4 text-center"> <img class="d-block mx-auto mb-4" src="/assets/statics/icons/mstile-310x310.png" alt="Mobile Pages" width="72" height="72">
      <h2>Mobile Pages</h2>
      <p class="lead">You can change any setting after installation by modifying the <code>.env</code> file in the webroot.<br>
        If you want to reinstall the script, delete the <code>.env</code> file from the webroot.</p>
    </div>
    <div class="row">
      <div class="col-md-8 offset-md-2 order-md-1">
        <form action="{{ url('install') }}" method="post" id="frmInstall">

          <div class="card mb-4 rounded-0 shadow-sm">
            <h5 class="card-header">Application</h5>
            <div class="card-body">
              <div class="row">
                <div class="col-md-8 mb-3">
                  <label for="APP_NAME">Name</label>
                  <input type="text" class="form-control rounded-0" id="APP_NAME" name="APP_NAME" placeholder="" value="Mobile Pages" required>
                </div>
              </div>
              <div class="row">
                <div class="col-md-8 mb-3">
                  <label for="APP_URL">Url</label>
                  <input type="text" class="form-control rounded-0" id="APP_URL" name="APP_URL" placeholder="" value="{{ \Request::getSchemeAndHttpHost() }}" required>
                  <small class="form-text text-muted">Enter the full url including scheme (http or https), but without a trailing slash.</small>
                </div>
              </div>
              <div class="row">
                <div class="col-md-8">
                  <label for="APP_TIMEZONE">Timezone</label>
                  <select class="custom-select d-block rounded-0 w-100" id="APP_TIMEZONE" name="APP_TIMEZONE" required>
<?php
foreach ($tzList as $val => $text) {
  $selected = ($val == 'UTC') ? ' selected' : '';
  echo '<option value="' . $val . '"' . $selected . '>' . $text . '</option>';
}
?>
                  </select>
                  <small class="form-text text-muted">Can be changed later.</small>
                </div>
              </div>
            </div>
          </div>

          <div class="card mb-4 rounded-0 shadow-sm">
            <h5 class="card-header">Admin</h5>
            <div class="card-body">
              <p class="card-text">These are the credentials of the admin user to log in with after installation.</p>
              <div class="row">
                <div class="col-md-8 mb-3">
                  <label for="name">Name</label>
                  <input type="text" class="form-control rounded-0" id="name" name="name" placeholder="" value="Admin" required>
                </div>
              </div>
              <div class="row">
                <div class="col-md-8 mb-3">
                  <label for="email" class="form-label required">E-mail</label>
                  <input id="email" name="email" type="email" placeholder="" value="" class="form-control rounded-0" required>
                </div>
              </div>
              <div class="row">
                <div class="col-md-8 mb-3">
                  <label for="pass">Password</label>
                  <input type="password" class="form-control rounded-0" id="pass" name="pass" placeholder="" minlength="8" value="" required>
                  <small class="form-text text-muted">Password must have at least 8 characters.</small>
                </div>
              </div>
            </div>
          </div>

          <div class="card mb-4 rounded-0 shadow-sm">
            <h5 class="card-header"><a role="button" style="color: inherit" data-toggle="collapse" href="#collapseDb" aria-expanded="false">Database (optional)</a></h5>
            <div class="card-body collapse" id="collapseDb">
              <div class="row">
                <div class="col-md-8 mb-3">
                  <label for="DB_CONNECTION" class="form-label required">Database</label>
                  <select id="DB_CONNECTION" name="DB_CONNECTION" class="form-control rounded-0" required>
                    <option value="sqlite">SQLite</option>
                    <option value="mysql">MySQL</option>
                  </select>
                  <small class="form-text text-muted">SQLite is recommended, in most cases it is the best choice.</small>
                </div>
              </div>

              <div id="settingsMySQL" class="d-none">
                <p class="card-text">MySQL version >= 5.7.8 or MariaDB >= 10.2.7 is required.</p>
                <div class="row">
                  <div class="col-sm-8">
                    <div class="form-group">
                      <label for="DB_HOST" class="form-label">Host</label>
                      <input id="DB_HOST" name="DB_HOST" type="text" placeholder="127.0.0.1" value="127.0.0.1" maxlength="32" class="form-control rounded-0">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label for="DB_PORT" class="form-label">Port</label>
                      <input id="DB_PORT" name="DB_PORT" type="text" placeholder="3306" value="3306" maxlength="10" class="form-control rounded-0">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-8 mb-3">
                    <label for="DB_DATABASE" class="form-label required">Database name</label>
                    <input id="DB_DATABASE" name="DB_DATABASE" type="text" placeholder="" value="" class="form-control rounded-0">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-8 mb-3">
                    <label for="DB_USERNAME" class="form-label required">Username</label>
                    <input id="DB_USERNAME" name="DB_USERNAME" type="text" placeholder="" value="" class="form-control rounded-0">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-8">
                    <label for="DB_PASSWORD" class="form-label">Password</label>
                    <input id="DB_PASSWORD" name="DB_PASSWORD" type="text" placeholder="" class="form-control rounded-0">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card mb-4 rounded-0 shadow-sm">
            <h5 class="card-header"><a role="button" style="color: inherit" data-toggle="collapse" href="#collapseMail" aria-expanded="false">E-mail (optional)</a></h5>
            <div class="card-body collapse" id="collapseMail">
              <div class="row">
                <div class="col-md-8 mb-3">
                  <label for="MAIL_DRIVER" class="form-label required">Driver</label>
                  <select id="MAIL_DRIVER" name="MAIL_DRIVER" class="form-control rounded-0" required>
                    <option value="mail">mail</option>
                    <option value="mailgun">Mailgun</option>
                  </select>
                  <small class="form-text text-muted">The "mail" driver works on <strong>most</strong> servers by default, however it usually gets a high spam rating.</small>
                </div>
              </div>

              <p class="card-text">Mail from name and e-mail address for sending automated e-mails.</p>

              <div class="row">
                <div class="col-md-8 mb-3">
                  <label for="MAIL_FROM_NAME" class="form-label required">Mail from name</label>
                  <input id="MAIL_FROM_NAME" name="MAIL_FROM_NAME" type="text" placeholder="" value="Mobile Pages" class="form-control rounded-0" required>
                </div>
              </div>

              <div class="row">
                <div class="col-md-8">
                  <label for="MAIL_FROM_ADDRESS" class="form-label required">Mail from e-mail address</label>
                  <input id="MAIL_FROM_ADDRESS" name="MAIL_FROM_ADDRESS" type="email" placeholder="noreply@example.com" value="{{ 'noreply@' . \Request::getHttpHost() }}" class="form-control rounded-0" required>
                </div>
              </div>

              <div id="settingsMailgun" class="d-none">
                <h5 class="card-title pt-4 pb-2">Mailgun</h5>

                <div class="row">
                  <div class="col-md-8 mb-3">
                    <label for="MAILGUN_DOMAIN" class="form-label required">Mailgun domain</label>
                    <input id="MAILGUN_DOMAIN" name="MAILGUN_DOMAIN" type="text" placeholder="{{ 'mg.' . \Request::getHttpHost() }}" class="form-control rounded-0">
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-8 mb-3">
                    <label for="MAILGUN_SECRET" class="form-label required">Mailgun private API key</label>
                    <input id="MAILGUN_SECRET" name="MAILGUN_SECRET" type="text" placeholder="" value="" class="form-control rounded-0">
                    <small class="form-text text-muted">You can find the Private API key by logging in to your Mailgun dashboard and navigate to <strong>Settings > API Security</strong>.</small>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-8 mb-3">
                    <label for="MAIL_USERNAME" class="form-label required">Mailgun SMTP login</label>
                    <input id="MAIL_USERNAME" name="MAIL_USERNAME" type="text" placeholder="postmaster@mg.example.com" value="" class="form-control rounded-0">
                    <small class="form-text text-muted">You can find the SMTP credentials by logging in to your Mailgun dashboard and navigate to <strong>Sending > Domains > [select domain] > Domain Settings</strong>.</small>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-8">
                    <label for="MAIL_PASSWORD" class="form-label required">Mailgun SMTP password</label>
                    <input id="MAIL_PASSWORD" name="MAIL_PASSWORD" type="text" placeholder="" value="" class="form-control rounded-0">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card mb-4 rounded-0 shadow-sm">
            <h5 class="card-header"><a role="button" style="color: inherit" data-toggle="collapse" href="#collapsePusher" aria-expanded="false">Pusher (optional)</a></h5>
            <div class="card-body collapse" id="collapsePusher">
              <p class="card-text">If you haven't set up a Pusher app, you can leave these settings empty for now and change them later in the <code>.env</code> file in the webroot.</p>
              <p class="card-text">Create a new app in your <a href="https://pusher.com/" target="_blank">pusher.com</a> dashboard, you only have to enter a name and select a cluster location. Other options are not required.</p>
              <div class="row">
                <div class="col-sm-8">
                  <div class="form-group">
                    <label for="PUSHER_APP_ID" class="form-label required">App ID</label>
                    <input id="PUSHER_APP_ID" name="PUSHER_APP_ID" type="text" placeholder="" value="" maxlength="64" class="form-control rounded-0">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="PUSHER_APP_CLUSTER" class="form-label required">Cluster</label>
                    <input id="PUSHER_APP_CLUSTER" name="PUSHER_APP_CLUSTER" type="text" placeholder="eu" value="" maxlength="5" class="form-control rounded-0">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-8 mb-3">
                  <label for="PUSHER_APP_KEY" class="form-label required">Key</label>
                  <input id="PUSHER_APP_KEY" name="PUSHER_APP_KEY" type="text" placeholder="" value="" class="form-control rounded-0">
                </div>
              </div>
              <div class="row">
                <div class="col-md-8">
                  <label for="PUSHER_APP_SECRET" class="form-label">Secret</label>
                  <input id="PUSHER_APP_SECRET" name="PUSHER_APP_SECRET" type="text" placeholder="" class="form-control rounded-0">
                </div>
              </div>
            </div>
          </div>

          <hr class="mb-4">
          <button class="btn btn-primary btn-lg btn-block rounded-0" type="submit">Start installation</button>
        </form>
      </div>
    </div>
    <footer class="my-5 pt-5 text-muted text-center text-small">
      <p class="mb-1">&copy; 2019 Mobile Pages</p>
      <ul class="list-inline">
        <li class="list-inline-item"><a href="https://nowsquare.com/">Website</a></li>
      </ul>
    </footer>
  </div>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> 
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script> 
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> 

  <script>
  $(function() {
    $('#frmInstall').on('submit', function() {
      $('#pageLoader').removeClass('d-none');
    });

    $('#DB_CONNECTION').on('change', function() {
      if($(this).val() == 'mysql') {
        $('#settingsMySQL').removeClass('d-none');
      } else {
        $('#settingsMySQL').addClass('d-none');
      }
    });

    $('#MAIL_DRIVER').on('change', function() {
      if($(this).val() == 'mailgun') {
        $('#settingsMailgun').removeClass('d-none');
      } else {
        $('#settingsMailgun').addClass('d-none');
      }
    });
  });
  </script>

</body>
</html>
