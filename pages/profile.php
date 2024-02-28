<?php include_once "inc/main.php"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<?php include "inc/head.php"; ?>

<body class="sessions" id="new">
  <div id="container">
    <?php include "inc/header.php"; ?>
    <div id="content">
      <?php if(isset($err)) { ?>
      <p><?php print_r($err); ?></p>
      <?php } ?>
      <div class="wrapper">
        <h2 class="thumb">
          <a href="/account/profile_image/<?=$_PROFILE["username"]?>.jpg"><img alt="Loom-icon" border="0"
              src="/account/profile_image/<?=$_PROFILE["username"]?>.jpg" alt="<?=$_PROFILE["fullname"]?>" valign="middle" /></a>
          <?php if(!$_SINGLE) { ?>
          <?=$_PROFILE["username"]?>
          <?php } else { ?>
          <a href="/<?=$_PROFILE["username"]?>"><?=$_PROFILE["username"]?></a>
          <?php } ?><?=isset($_PROFILE["badge"]) ? " <i class=\"badge $_PROFILE[badge]\"></i>" : "" ?>
          <?php if(isset($_USER) && $_PROFILE["id"] == $_USER["id"] && !$_EDIT) { ?>
          <span style="font-size: 0.5em; float: right;"><a href="/<?=$_PROFILE["username"]?>/edit">Edit Profile</a></span>
          <?php } ?>
        </h2>

        <div class="h-feed">
          <?php if(!$_EDIT) { ?>
          <div class="desc h-entry">
            <div class="entry-title entry-content"><?=parse_bweet($_STATUS["content"])?></div>
            <p class="meta entry-meta">
              <a href="/<?=$_PROFILE["username"]?>/statuses/<?=$_STATUS["id"]?>" class="entry-date" rel="bookmark"><abbr
                  class="published"><?=time_since($_STATUS["timestamp"])?></abbr></a> from
              web
              <span id="status_actions_<?=$_STATUS["timestamp"]?>"></span>
            </p>
          </div>
          <?php } else { ?>
          <div style="margin-top: 6px;padding-top: 11px;background: url(/images/arr2.gif) no-repeat 14px 0px;">
            <div class="meta entry-meta" style="background-color: #fff;display: inline-block;width: 100%;">
              <form action="/edit" enctype="multipart/form-data" method="post" name="f">
                <fieldset>
                  <table cellspacing="0">
                    <tbody>
                      <tr>
                        <th><label for="user_name">Full Name:</label></th>
                        <td><input id="user_name" name="fullname" size="30" type="text" value="<?=$_USER["fullname"]?>">
                        </td>
                      </tr>
                      <tr>
                        <th><label for="user_bio">Bio:</label></th>
                        <td><input id="user_bio" name="bio" size="30" type="text" placeholder="My name is <?=$_USER["fullname"]?>..." value="<?=$_USER["bio"]?>">
                        </td>
                      </tr>
                      <tr>
                        <th><label for="user_location">Location:</label></th>
                        <td><input id="user_location" name="location" size="30" type="text" placeholder="Washington D.C." value="<?=$_USER["location"]?>">
                        </td>
                      </tr>
                      <tr>
                        <th><label for="user_web">Web:</label></th>
                        <td><input id="user_web" name="web" size="30" type="text" placeholder="https://bwitter.me/" value="<?=$_USER["web"]?>">
                        </td>
                      </tr>
                      <tr>
                        <th><label for="password">Password:</label></th>
                        <td><input id="password" name="password" type="password">
                          <small>Required to modify profile.</small></td>
                      </tr>
                      <tr>
                        <th><label for="new_password">New Password:</label></th>
                        <td><input id="new_password" name="new_password" type="password">
                          <small>Leave blank to keep current.</small></td>
                      </tr>
                      <tr>
                        <th><label for="password_confirmation">Retype Password:</label></th>
                        <td><input id="password_confirmation" name="password_confirmation" size="30"
                            type="password">
                        </td>
                      </tr>
                      <tr>
                        <th><label for="user_time_zone">Time Zone:</label></th>
                        <td><select id="user_time_zone" name="time_zone">
                            <?php foreach($_TIMEZONES as $_TIMEZONE) { ?>
                            <option value="<?=$_TIMEZONE["id"]?>"><?=$_TIMEZONE["name"]?></option>
                            <?php } ?>
                          </select></td>
                      </tr>
                      <tr>
                        <th>
                          <label for="user_profile_image">
                            Picture:
                          </label>
                        </th>
                        <td>
                          <input id="user_profile_image_temp" name="profile_image_temp" type="hidden"><input
                            id="user_profile_image" name="profile_image" size="30" type="file">
                          <p><small>(Optional) JPEGs only!<br> If you don’t include a picture, your current profile
                              picture will be kept.</small></p>
                        </td>
                      </tr>
                      <tr>
                        <th></th>
                        <td>
                          <input type="submit" id="submit" value="Update">
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </fieldset>
              </form>
              </form>
            </div>
          </div>
          <?php } ?>
          <?php if(!$_SINGLE && !$_EDIT && count($_TWEETS) > 0) { ?>
          <ul class="tabMenu">
          </ul>
          <div class="tab">
            <table class="doing" id="timeline" cellspacing="0">
              <?php foreach($_TWEETS as $_STATUS) { render_bweet($_STATUS); } ?>
            </table>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <hr />
    <?php include "inc/side.php"; ?>
    <hr />
    <hr />
    <?php include "inc/footer.php"; ?>
  </div>
</body>

</html>